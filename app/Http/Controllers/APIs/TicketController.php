<?php

namespace App\Http\Controllers\APIs;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Ticket;
use App\TicketAttachment;
use App\TicketThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{

    /**
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

        $ticket = new Ticket();
        $ticket->created_by = session('employee_id');
        $ticket->subject = $subject;
        $ticket->status = 1;
        $ticket->priority = $priority;
        $ticket->deadline = date('Y-m-d H:i:s', strtotime($request->input('deadline')));
        $ticket->team_id = $request->input('team_id');
        $ticket->content = $request->input('content');

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

        $ticket->save();
        return 1;
    }


    /**
     * @param Request $request
     * @return array
     */

    public function get_tickets(Request $request)
    {
        $employee_id = $request->session()->get('employee_id');

        $tickets = Ticket::all();

        $selector = $request->has('selector') ? $request->input('selector') : 'assigned_to';
        switch ($selector) {
            case 'created_by':
                $tickets = $tickets->where('created_by', $employee_id);
                break;
            case 'related_to':
                $tickets = DB::table('tickets')
                    ->join('ticket_relaters', 'tickets.id', '=', 'ticket_relaters.ticket_id')
                    ->select('tickets.*')
                    ->where('ticket_relaters.employee_id', $employee_id)
                    ->get();
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

        if ($request->has('subject')) {
            $subject = $request->input('subject');
            $tickets = $tickets->where('subject', 'LIKE', "%{$subject}%");
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
            $tickets = $tickets->whereBetween('deadline', [$first_second, $last_second]);
        }

        if ($request->has('content')) {
            $content = $request->input('content');
            $tickets = $tickets->where('content', 'LIKE', "{$content}%");
        }

//        $tickets = $tickets->get();

        if ($request->has('count')) {
            return $tickets->count();
        } else {
            return $tickets;
        }
    }

    /**
     * @param Request $request
     * @return int
     */

    public function comment(Request $request)
    {
        $ticket_id = $request->input('ticket_id');
        $content = $request->input('content');
        if (empty($content)) {
            return 0;
        }
        // If ticket does not exist
        $ticket = Ticket::find($ticket_id);
        if ($ticket->count() == 0) {
            return 0;
        }
        $ticket = $ticket->get();

        // If ticket has been closed before
        if ($ticket->closed_at > 0) {
            return 0;
        }

        $comment = new TicketThread();
        $comment->content = $content;
        $comment->ticket_id = $ticket_id;
        $comment->employee_id = $request->input('employee_id');
        $comment->type = 0;
        $comment->note = '';

        $comment->save();
        return 1;
    }

    public function search_ticket(Request $request)
    {
        if ($request->has('subject')) {

            $subject = $request->input('subject');

            $tickets = Ticket::where('subject', 'LIKE', "%{$subject}%")->get();

            return $tickets;
        }

        return [];
    }
}