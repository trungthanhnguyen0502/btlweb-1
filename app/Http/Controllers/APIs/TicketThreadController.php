<?php

namespace App\Http\Controllers\APIs;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Ticket;
use Illuminate\Http\Request;

class TicketThreadController extends Controller
{
    /**
     * Post comment to ticket thread
     *
     * @param Request $request
     * @return int
     */

    public function post_comment(Request $request)
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

//    public function get_comments(Request $request, $ticket_id)
//    {
//        if ($ticket_id <= 0) {
//            return [];
//        }
//
//        $employee_id = $request->session()->get('employee_id');
//        $employee = Employee::where('id', $employee_id);
//
//        $ticket = Ticket::where('id', $ticket_id)->get()[0];
//
//        if ($ticket->created_by != $employee_id && $ticket->assigned_to != $employee_id && !$employee_id)
//    }
}
