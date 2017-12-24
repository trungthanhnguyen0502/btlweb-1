<?php

namespace App\Http\Controllers\APIs;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Mail\ChangeTeam;
use App\Team;
use App\Ticket;
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

        if ($request->has('deadline') && $request->has('ticket_id')) {

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

        if ($request->has('ticket_id') && $request->has('priority')) {
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

            if ($priority < 1 || $priority > 5 || $priority == $ticket->priority) {
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


    public function assigned_to(Request $request)
    {
        if ($request->has('ticket_id') && $request->has('employee_id') && $request->has('note')) {

        }

        return 0;
    }
}
