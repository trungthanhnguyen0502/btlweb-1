<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Ticket;
use App\TicketThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketApiController extends Controller
{

    /**
     * @param Request $request
     * @return int
     */

    public function create_ticket(Request $request)
    {
        if ($request->session()->has('login_key')) {

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
            $ticket->deadline = strtotime($request->input('deadline'));
            $ticket->team_id = $request->input('team_id');
            $ticket->content = $request->input('content');

            $ticket->save();
            return 1;

//            if ($request->input('attachment')) {
//                $attachment = $request->input('attachment');
//
//            }
        }

        return 0;
    }

    /**
     * @param Request $request
     * @return array
     */

    public function get_tickets(Request $request)
    {
        if ($request->session()->has('login_key')) {

            $table = DB::table('tickets')->join('ticket_relaters', 'tickets.id', '=', 'ticket_relaters.ticket_id');
//            $result = $table->select('tickets.*', 'ticket_relaters.employee_id');
//
            $employee_id = $request->session()->get('employee_id');

//            $table = DB::table('tickets');
            $result = $table
//                ->where('tickets.created_by', $employee_id)
//                ->orWhere('tickets.assigned_to', $employee_id)
//                ->orWhere('ticket_relaters.employee_id', $employee_id)
                ->get();

//            if ($request->input('id')) {
//                $result->where('id', $request->input('id'));
//            }
//
//            if ($request->input('subject')) {
//                $result->where('subject', 'LIKE', $request->input('subject') . '%');
//            }
//
//            if ($request->input('status')) {
//                $result->where('status', $request->input('status'));
//            }
//
//            if ($request->input('priority')) {
//                $result->where('priority', $request->input('prioriy'));
//            }
//
//            if ($request->input('deadline')) {
//                $deadline = strtotime($request->input('deadline'));
//                $result->where('deadline', $deadline);
//            }
//
//            if ($request->input('team_id')) {
//                $result->where('team_id', $request->input('team_id'));
//            }
//
//            if ($request->input('content')) {
//                $result->where('content', 'LIKE', $request->input('content'));
//            }
//
//            if ($request->input('created_by')) {
//                $result->where('created_by', $request->input('created_by'));
//            }
//
//            if ($request->input('assigned_to')) {
//                $result->where('assigned_to', $request->input('assigned_to'));
//            }
//
//            if ($request->input('related_employee_id')) {
//                $result->where('ticket_relaters.employee_id', $request->input('related_employee_id'));
//            }
//
//            if ($request->input('count')) {
//                return $result->count();
//            }
//
//            $tickets = $result->get();
//            $num_of_tickets = $tickets->count();
//
//            if ($request->input('per_page')) {
//                $per_page = intval($request->input($per_page));
//                if ($per_page < 0) {
//                    return $tickets;
//                }
//
//                $page = inval($request->input('page'));
//
//                if ($page < 0) {
//                    $page = 0;
//                }
//
//                $pages = $num_of_tickets / $per_page;
//
//                if ($num_of_tickets % $per_page != 0) {
//                    $pages++;
//                }
//
//                $page %= $pages;
//
//                return array_slice($tickets, $page * $per_page, $per_page);
//            }
//
//            return $tickets;
            return $result;
        }

        return 0;
    }

    /**
     * @param Request $request
     * @return int
     */

    public function comment(Request $request) {
        if ($request->session()->has('login_key')) {

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
        } else {
            return 0;
        }
    }
}
