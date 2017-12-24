<?php

namespace App\Http\Controllers\APIs;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Mail\ChangeTeam;
use App\Team;
use App\Ticket;
use App\TicketThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EditTicketController extends Controller
{
    /**
     * Change deadline of a ticket
     *
     * @param Request $request
     * @return int
     */

    public function change_deadline(Request $request)
    {

        if ($request->has('deadline') && $request->has('ticket_id') && $request->has('note')) {

            $ticket_id = $request->input('ticket_id');
            $ticket = Ticket::where('id', $ticket_id)->get();

            if ($ticket->count() == 0) {
                return 0;
            }

            $ticket = $ticket->first();

            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::where('id', $employee_id)->get()->first();

            if ($ticket->created_by != $employee_id || $employee->role < 2) {
                return 0;
            }

            if ($ticket->status != 1 && $ticket->status != 2 && $ticket->status != 4) {
                return 0;
            }

            $time = strtotime($request->input('deadline'));
            $deadline = date('Y-m-d H:i:s', $time);

            if ($ticket->created_at >= $deadline || $ticket->deadline == $deadline) {
                return 0;
            }

            // Create comment in thread
            $comment = new TicketThread();
            $comment->ticket_id = $ticket_id;
            $comment->employee_id = $employee_id;
            $comment->type = 3;
            $comment->content = view('comment.change_deadline')
                ->with('employee_name', $employee->display_name)
                ->with('deadline', $deadline)
                ->with('note', $note);
            $comment->note = $request->input('note');

            $comment->save();

            DB::table('tickets')->where('id', $ticket_id)
                ->update(['deadline' => $deadline]);
            return 1;
        }

        return 0;
    }

    /**
     * Change priority of a ticket
     *
     * @param Request $request
     * @return int
     */

    public function change_priority(Request $request)
    {

        if ($request->has('ticket_id') && $request->has('priority') && $request->has('note')) {
            $ticket_id = $request->input('ticket_id');

            $ticket = Ticket::where('id', $ticket_id);

            if ($ticket->count() == 0) {
                return 0;
            }

            $ticket = $ticket->get()->first();

            if ($ticket->status != 1 && $ticket->status != 2 && $ticket->status != 4) {
                return 0;
            }

            $priority = $request->input('priority');

            if ($priority < 1 || $priority > 4 || $priority == $ticket->priority) {
                return 0;
            }

            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::find($employee_id);

            if (($employee->role < 2)
                || ($ticket->created_by != $employee_id && $ticket->assigned_to != $employee_id)
            ) {
                return 0;
            }

            $ticket->priority = $priority;
            $ticket->save();

            $note = $request->input('note');

            // Create comment in thread
            $comment = new TicketThread();
            $comment->ticket_id = $ticket_id;
            $comment->employee_id = $employee_id;
            $comment->type = 2;
            $comment->content = view('comment.change_priority')
                ->with('employee_name', $employee->display_name)
                ->with('priority', $priority)
                ->with('note', $note);
            $comment->note = $request->input('note');

            $comment->save();

            return 1;
        }

        return 0;
    }

    /**
     * Change IT team for a ticket
     *
     * @param Request $request
     * @return int
     */

    public function change_team(Request $request)
    {

        if ($request->has('ticket_id') && $request->has('team_id')) {

            $ticket_id = $request->input('ticket_id');
            $ticket = Ticket::where('id', $ticket_id);

            if ($ticket->count() == 0) {
                return 0;
            }

            $ticket = $ticket->get()->first();

            if ($ticket->status != 1 && $ticket->status != 2 && $ticket->status != 4) {
                return 0;
            }

            $team_id = $request->input('team_id');

            if ($team_id == $ticket->team_id) {
                return 0;
            }

            $team = Team::where('id', $team_id);

            if ($team->count() == 0) {
                return 0;
            }

            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::find($employee_id);

            if ($employee->role < 2) {
                return 0;
            }

            $ticket->team_id = $team_id;
            $leader = Employee::where('team_id', $team_id)->where('is_leader', 1)->get()[0];
            $ticket->assigned_to = $leader->id;

            $ticket->save();

            $note = $request->input('note');

            // Create comment in thread
            $comment = new TicketThread();
            $comment->ticket_id = $ticket_id;
            $comment->employee_id = $employee_id;
            $comment->type = 2;
            $comment->content = view('comment.change_team')
                ->with('employee_name', $employee->display_name)
                ->with('team_name', $team->title)
                ->with('note', $note);
            $comment->note = $request->input('note');

            $comment->save();

            // Create notification
            $notification = new ChangeTeam();
            $notification->who_assigned = $employee_id;
            $notification->title = $ticket->subject;
            $team = $team->get()->first();
            $notification->team_name = $team->title;
            $notification->deadline = $ticket->deadline;
            Mail::to($leader->email)->send($notification);

            return 1;
        }

        return 0;
    }

    /**
     * Assign a ticket to a member
     *
     * @param Request $request
     * @return int
     */

    public function assigned_to(Request $request)
    {
        if ($request->has('ticket_id') && $request->has('employee_id')) {

            // Check ticket is exist

            $ticket_id = $request->input('ticket_id');
            $ticket = Ticket::where('id', $ticket_id);

            if ($ticket->count() == 0) {
                return 0;
            }

            $ticket = $ticket->get()->first();

            if ($ticket->status == 3 || $ticket->status == 5 || $ticket->status == 6) {
                return 0;
            }

            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::find($employee_id);
            // If employee is not a leader
            if ($employee->role < 2) {
                return 0;
            }
            // Sub-leader checking
            if ($employee->role == 2 && $employee->team_id != $ticket->team_id) {
                return 0;
            }
            $who_is_assigned_id = $request->input('employee_id');
            $who_is_assigned = Employee::where('id', $who_is_assigned_id);

            // If people who is assigned do not exist
            if ($who_is_assigned->count() == 0) {
                return 0;
            }

            $who_is_assigned = $who_is_assigned->get()->first();

            // If people who is assigned is not in this team
            if ($who_is_assigned->team_id != $ticket->team_id) {
                return 0;
            }

            $ticket->assigned_to = $who_is_assigned_id;
            $ticket->save();

            // Create comment in thread
            $comment = new TicketThread();
            $comment->ticket_id = $ticket_id;
            $comment->employee_id = $employee_id;
            $comment->type = 4;
            $comment->content = view('comment.assigned_to')
                ->with('employee_name', $employee->display_name)
                ->with('assigned_to', $who_is_assigned->display_name);
            $comment->note = '';

            $comment->save();

            return 1;
        }

        return 0;
    }

    public function change_status(Request $request) {

    }

}
