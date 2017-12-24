<?php

namespace App\Http\Controllers\APIs;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Ticket;
use App\TicketThread;
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

    public function get_comments(Request $request, $ticket_id)
    {
        $ticket = Ticket::where('id', $ticket_id);

        if ($ticket->count() == 0) {
            return 0;
        }

        $ticket = $ticket->get()->first();
        $ticket->relaters;

        $employee_id = $request->session()->get('employee_id');
        $employee = Employee::where('id', $employee_id)->get()->first();

        $has_permission = false;
        // If employee is created_by or assigned_to of this ticket
        $has_permission = $has_permission || $ticket->created_by == $employee_id || $ticket->assigned_to == $employee_id;
        // If employee is relater
        foreach ($ticket->relaters as $relater) {
            $has_permission = $has_permission || ($employee_id == $relater->employee_id);
        }
        // If employee is team leader
        $has_permission = $has_permission || ($employee->role == 2 && $employee->team_id == $ticket->team_id);
        // If employee is manager
        $has_permission = $has_permission || $employee->role == 3;
        // If employee does not have enough permission
        if (! $has_permission) {
            return 0;
        }

        $comments = TicketThread::where('ticket_id', $ticket_id)->get();
        foreach ($comments as $comment) {
            $comment->commented_by;
        }

        return $comments;
    }
}
