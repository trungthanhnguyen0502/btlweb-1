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
     * @return array
     */

    public function post_comment(Request $request)
    {
        if ($request->has('ticket_id') && $request->has('content')) {
            $ticket_id = $request->input('ticket_id');
            $content = $request->input('content');

            // If ticket does not exist
            $ticket = Ticket::where('id', $ticket_id)->get();

            if ($ticket->count() == 0) {
                return [
                    'status' => 0,
                    'phrase' => 'Không tìm thấy ticket.'
                ];
            }

            $ticket = $ticket->first();

            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::find($employee_id);
            // If employee does not have permission to perform this action
            if ($ticket->created_by != $employee_id && $ticket->assigned_to != $employee_id) {
                if ($employee->role < 3) {
                    if ($employee->role < 2 && $employee->team_id != $ticket_id) {
                        return [
                            'status' => 0,
                            'phrase' => 'Không có quyền thực hiện.'
                        ];
                    }
                }
            }


            // Create comment
            $comment = new TicketThread();
            $comment->content = $content;
            $comment->ticket_id = $ticket_id;
            $comment->employee_id = $employee_id;
            $comment->type = 0;
            $comment->note = '';
            $comment->save();

            // Return status
            return [
                'status' => 1,
            ];
        }

        return [
            'status' => 0,
            'phrase' => 'Không tìm thấy ticket.'
        ];
    }

    /**
     * Get comments in Ticket Thread
     *
     * @param Request $request
     * @param $ticket_id
     * @return array|int
     */

    public function get_comments(Request $request, $ticket_id)
    {
        $ticket = Ticket::where('id', $ticket_id);

        if ($ticket->count() == 0) {
            return [];
        }

        $ticket = $ticket->get()->first();
        $ticket->relaters()->get();

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
        if (!$has_permission) {
            return [];
        }

        $comments = TicketThread::where('ticket_id', $ticket_id)->get();

        if ($comments->count() == 0) {
            return [];
        }

        foreach ($comments as $comment) {
            $comment->commented_by;
        }

        return $comments;
    }
}
