<?php

namespace App\Http\Controllers\APIs;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Mail\NewTicket;
use App\Team;
use App\Ticket;
use App\TicketAttachment;
use App\TicketRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{

    /**
     * Create ticket by current employee
     *
     * @param Request $request
     * @return int
     */

    public function create_ticket(Request $request)
    {

        if (empty($request->input('subject'))) {
            return 0;
        }

        $subject = $request->input('subject');
        if (empty($subject)) {
            return 0;
        }

        $priority = intval($request->input('priority'));
        if ($priority < 0) {
            return 0;
        }

        $employee_id = $request->session()->get('employee_id');

        $ticket = new Ticket();
        $ticket->created_by = $employee_id;
        $ticket->subject = $subject;
        $ticket->status = 1;
        $ticket->priority = $priority;
        $ticket->deadline = date('Y-m-d H:i:s', strtotime($request->input('deadline')));
        $ticket->team_id = $request->input('team_id');
        $ticket->content = $request->input('content');

        // Assigned to leader of team
        $leader = Employee::where('team_id', $ticket->team_id)
            ->where('role', 2)
            ->where('is_leader', 1)
            ->get();

        if ($leader->count() != 1) {
            return 0;
        }

        $leader = $leader[0];

        $ticket->assigned_to = $leader->id;

        // If ticket has attachment
        if ($request->input('image')) {
            $image = (object)$request->input('image');
            $ticket_attachment = new TicketAttachment();
            $ticket_attachment->employee_id = $request->session()->get('employee_id');
            $ticket_attachment->mime_type = $image->filetype;
            $ticket_attachment->file_name = $image->filename;

            $ticket_attachment->data = base64_decode($image->base64);
            $ticket_attachment->size = $image->filesize;
            $ticket_attachment->uploaded_at = $request->server('REQUEST_TIME');
            $ticket_attachment->save();
            $ticket->attachment = $ticket_attachment->id;
        }
        // Save ticket
        $ticket->save();

        $who_created = Employee::where('id', $employee_id)->get()[0];
        $team = Team::where('id', $who_created->team_id)->get()[0];
        $leaders = Employee::where('team_id', $who_created->team_id)->whereIn('role', [2, 3])->get();

        // Create mail
        $notification = new NewTicket();
        $notification->who_created = $who_created->display_name;
        $notification->team_name = $team->title;
        $notification->title = $subject;
        $notification->deadline = $ticket->deadline;

        $num_of_leaders = $leaders->count();
        for ($i = 0; $i < $num_of_leaders; $i++) {
            Mail::to($leaders[$i]->email)->send($notification);
        }

        return 1;
    }

    public function get_ticket($ticket_id = 0)
    {
        if ($ticket_id != 0) {

            $ticket = Ticket::find($ticket_id);

            if ($ticket->count() == 0) {
                return response('{}')->header('Content-Type', 'application/json');
            }

            $ticket->created_by_employee;
            $ticket->assigned_to_employee;

            if ($ticket->attachment) {
                $ticket_attachment = TicketAttachment::where('id', $ticket->attachment)->get()[0];

                $ticket_attachment->url = url('/api/attachment/');
                $ticket_attachment->url .= "/{$ticket->attachment}/{$ticket_attachment->file_name}";

                $ticket->attachment_type = $ticket_attachment->mime_type;
                $ticket->attachment_name = $ticket_attachment->file_name;
                $ticket->attachment_url = $ticket_attachment->url;
            }

            return $ticket;
        }

        return response('{}')->header('Content-Type', 'application/json');
    }

    /**
     * Get ticket by selector and optional params
     *
     * @param Request $request
     * @return array
     */

    public function get_tickets(Request $request)
    {
        $employee_id = $request->session()->get('employee_id');

        if ($request->has('subject')) {
            $subject = $request->input('subject');
            $tickets = Ticket::where('subject', 'like', "%{$subject}%")->get();
        } else {
            $tickets = Ticket::all();
        }

        $selector = $request->has('selector') ? $request->input('selector') : 'assigned_to';

        switch ($selector) {
            case 'created_by':
                $tickets = $tickets->where('created_by', $employee_id);
                break;
            case 'related_to':
                $query = DB::table('tickets')
                    ->join('ticket_relaters', 'tickets.id', '=', 'ticket_relaters.ticket_id')
                    ->select('tickets.*')
                    ->where('ticket_relaters.employee_id', $employee_id);

                if ($request->has('subject')) {
                    $subject = $request->input('subject');
                    $tickets = $query->where('subject', 'like', "%{$subject}%")->get();
                } else {
                    $tickets = $query->get();
                }
                break;

            case 'team_id':
                if ($request->has('team_id')) {
                    $team_id = $request->input('team_id');
                    $leader = Employee::where('employee_id', $employee_id)
                        ->where('team_id', $team_id)
                        ->get();
                    if ($leader->count() == 0) {
                        return [];
                    }

                    $leader = $leader[0];

                    if ($leader->role < 2 || $team_id != $leader->team_id) {
                        return [];
                    }

                    $tickets = $tickets->where('team_id', $team_id);
                }
                break;

            case 'assigned_to':
            default:
                $tickets = $tickets->where('assigned_to', $employee_id);
                break;
        }

        if ($request->has('assigned_to')) {
            $assigned_to = $request->input('assigned_to');
            $tickets = $tickets->where('assigned_to', $assigned_to);
        }

        if ($request->has('created_by')) {
            $created_by = $request->input('created_by');
            $tickets = $tickets->where('created_by', $created_by);
        }

        if ($request->has('related_to')) {
            $related_to = $request->input('related_to');
            $tickets = $tickets->where('ticket_relaters.employee_id', $related_to);
        }

        if ($request->has('status')) {
            $status = $request->input('status');

            if ($status == '7') {
                $tickets = $tickets->where('out_of_date', 1);
            } else {
                $tickets = $tickets->where('status', $status);
            }
        }

        if ($request->has('priority')) {
            $priority = $request->input('priority');
            $tickets = $tickets->where('priority', $priority);
        }

        if ($request->has('deadline')) {
            $deadline = $request->input('deadline');
            $deadline = date('Y-m-d', strtotime($deadline));
            $first_second = "{$deadline} 00:00:00";
            $last_second = "{$deadline} 23:59:59";
            $tickets = $tickets->where('deadline', '>=', $first_second)
                ->where('deadline', '<=', $last_second);
        }

        if ($request->has('content')) {
            $content = $request->input('content');
            $tickets = $tickets->where('content', 'LIKE', "{$content}%");
        }

        if ($request->has('count')) {
            return $tickets->count();
        }

        if ($request->has('per_page') && $request->has('page')) {
            $per_page = intval($request->input('per_page'));
            $page = intval($request->input('page'));
            $tickets = $tickets->forPage($page, $per_page);
        }

        foreach ($tickets as $ticket) {
            $ticket->created_by_employee;
            $ticket->assigned_to_employee;
        }

        return $tickets;
    }

    /**
     * Search ticket by subject
     *
     * @param Request $request
     * @return array
     */

    public function search_ticket(Request $request)
    {
        if ($request->has('subject')) {

            $subject = $request->input('subject');

            $tickets = Ticket::where('subject', 'LIKE', "%{$subject}%")->get();

            return $tickets;
        }

        return [];
    }

    /**
     * Mark a ticket as read
     *
     * @param Request $request
     * @return int
     */

    public function read(Request $request)
    {
        if ($request->has('ticket_id')) {

            $ticket_id = $request->input('ticket_id');
            $employee_id = $request->session()->get('employee_id');

            $read = TicketRead::where('ticket_id', $ticket_id)->where('employee_id', $employee_id)->count();

            if ($read == 0) {

                DB::table('ticket_reads')->insert([
                    'ticket_id' => $ticket_id,
                    'employee_id' => $employee_id,
                    'read' => 1
                ]);

            } else {
                $status = 0;

                if ($request->has('read')) {
                    $status = intval($request->input('read')) % 2;
                }

                $read->update([
                    'read' => $status
                ]);
            }

            return 1;
        }

        return 0;
    }

    /**
     * Unread a ticket
     *
     * @param Request $request
     * @return int
     */

    public function unread(Request $request)
    {
        if ($request->has('ticket_id')) {
            $ticket_id = $request->input('ticket_id');
            $employee_id = $request->session()->get('employee_id');

            TicketRead::where('ticket_id', $ticket_id)
                ->where('employee_id', $employee_id)
                ->delete();

            return 1;
        }

        return 0;
    }

    /**
     * Add relaters to ticket
     *
     * @param Request $request
     * @return int
     */

    public function add_relaters(Request $request)
    {
        if ($request->has('ticket_id') && $request->has('relaters')) {
            $ticket_id = $request->input('ticket_id');
            $ticket = Ticket::where('id', $ticket_id)->get();
            if ($ticket->count() == 0) {
                return 0;
            }
            $ticket = $ticket[0];
            $employee_id = $request->session()->get('employee_id');
            if ($ticket->created_by != $employee_id && $ticket->assigned_to != $employee_id) {
                return 0;
            }
            $relaters = \GuzzleHttp\json_decode($request->input('relaters'));
            $records = [];
            foreach ($relaters as $key => $value) {
                $records[$key] = [
                    'ticket_id' => $ticket_id,
                    'employee_id' => $value,
                ];
            }
            DB::table('ticket_relaters')->insert($records);
            return 1;
        }

        return 0;
    }



    public function edit_ticket(Request $request)
    {
        if ($request->has('ticket_id')) {
            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::where('id', $employee_id)->get()[0];


        }

        return 0;
    }

}