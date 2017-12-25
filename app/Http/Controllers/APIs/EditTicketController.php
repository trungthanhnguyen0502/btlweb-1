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
     * @return array
     */

    public function change_deadline(Request $request)
    {

        if ($request->has('deadline') && $request->has('ticket_id') && $request->has('note')) {

            $ticket_id = $request->input('ticket_id');
            $ticket = Ticket::where('id', $ticket_id)->get();

            if ($ticket->count() == 0) {
                return [
                    'status' => 0,
                    'phrase' => 'Không tìm thấy ticket.'
                ];
            }

            $ticket = $ticket->first();

            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::where('id', $employee_id)->get()->first();

            if ($ticket->created_by != $employee_id || $employee->role < 2) {
                return [
                    'status' => 0,
                    'phrase' => 'Không có quyền truy cập.'
                ];
            }

            if ($ticket->status != 1 && $ticket->status != 2 && $ticket->status != 4) {
                return [
                    'status' => 0,
                    'phrase' => 'Thao tác với ticket này không thực hiện được.'
                ];
            }

            $time = strtotime($request->input('deadline'));
            $deadline = date('Y-m-d H:i:s', $time);

            if ($ticket->created_at >= $deadline || $ticket->deadline == $deadline) {
                return [
                    'status' => 0,
                    'phrase' => 'Deadline mới không hợp lệ.'
                ];
            }

            $note = $request->input('note');

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
            return [
                'status' => 1,
            ];
        }

        return [
            'status' => 0,
            'phrase' => 'Thao tác không hợp lệ.'
        ];
    }

    /**
     * Change priority of a ticket
     *
     * @param Request $request
     * @return array
     */

    public function change_priority(Request $request)
    {

        if ($request->has('ticket_id') && $request->has('priority') && $request->has('note')) {
            $ticket_id = $request->input('ticket_id');

            $ticket = Ticket::where('id', $ticket_id);

            if ($ticket->count() == 0) {
                return [
                    'status' => 0,
                    'phrase' => 'Không tìm thấy ticket.'
                ];
            }

            $ticket = $ticket->get()->first();

            if ($ticket->status != 1 && $ticket->status != 2 && $ticket->status != 4) {
                return [
                    'status' => 0,
                    'phrase' => 'Thao tác với ticket này không thực hiện được.'
                ];
            }

            $priority = $request->input('priority');

            if ($priority < 1 || $priority > 4 || $priority == $ticket->priority) {
                return [
                    'status' => 0,
                    'phrase' => 'Độ ưu tiên không hợp lệ.'
                ];
            }

            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::find($employee_id);

            if (($employee->role < 2)
                || ($ticket->created_by != $employee_id && $ticket->assigned_to != $employee_id)
            ) {
                return [
                    'status' => 0,
                    'phrase' => 'Không đủ quyền thực hiện.'
                ];
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

            return [
                'status' => 1,
            ];
        }

        return [
            'status' => 0,
            'phrase' => 'Thao tác không hợp lệ.'
        ];
    }

    /**
     * Change IT team for a ticket
     *
     * @param Request $request
     * @return array
     */

    public function change_team(Request $request)
    {

        if ($request->has('ticket_id') && $request->has('team_id')) {

            $ticket_id = $request->input('ticket_id');
            $ticket = Ticket::where('id', $ticket_id);

            if ($ticket->count() == 0) {
                return [
                    'status' => 0,
                    'phrase' => 'Không tìm thấy ticket.'
                ];
            }

            $ticket = $ticket->get()->first();

            if ($ticket->status != 1 && $ticket->status != 2 && $ticket->status != 4) {
                return [
                    'status' => 0,
                    'phrase' => 'Thao tác với ticket này không thực hiện được.'
                ];
            }

            $team_id = $request->input('team_id');

            if ($team_id == $ticket->team_id) {
                return [
                    'status' => 1,
                    'phrase' => 'Không thể di chuyển về team hiện tại.'
                ];
            }

            $team = Team::where('id', $team_id);

            if ($team->count() == 0) {
                return [
                    'status' => 0,
                    'phrase' => 'Không tìm thấy team mới.'
                ];
            }

            $team = $team->get()->first();

            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::find($employee_id);

            if ($employee->role < 2) {
                return [
                    'status' => 0,
                    'phrase' => 'Không có quyền thực hiện.'
                ];
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

            return [
                'status' => 1,
                'phrase' => ''
            ];
        }

        return [
            'status' => 0,
            'phrase' => 'Thao tác không hợp lệ.'
        ];
    }

    /**
     * Assign a ticket to a member
     *
     * @param Request $request
     * @return array
     */

    public function assigned_to(Request $request)
    {
        if ($request->has('ticket_id') && $request->has('employee_id')) {

            // Check ticket is exist

            $ticket_id = $request->input('ticket_id');
            $ticket = Ticket::where('id', $ticket_id);

            if ($ticket->count() == 0) {
                return [
                    'status' => 0,
                    'phrase' => 'Không tìm thấy ticket.'
                ];
            }

            $ticket = $ticket->get()->first();

            if ($ticket->status == 3 || $ticket->status == 5 || $ticket->status == 6) {
                return [
                    'status' => 0,
                    'phrase' => 'Thao tác với ticket này không thực hiện được.'
                ];
            }

            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::find($employee_id);
            // If employee is not a leader
            if ($employee->role < 2) {
                return [
                    'status' => 0,
                    'phrase' => 'Không có quyền thực hiện.'
                ];
            }
            // Sub-leader checking
            if ($employee->role == 2 && $employee->team_id != $ticket->team_id) {
                return [
                    'status' => 0,
                    'phrase' => 'Không được thay đổi ở team khác.'
                ];
            }
            $who_is_assigned_id = $request->input('employee_id');
            $who_is_assigned = Employee::where('id', $who_is_assigned_id);

            // If people who is assigned do not exist
            if ($who_is_assigned->count() == 0) {
                return [
                    'status' => 0,
                    'phrase' => 'Giao việc cho người không hợp lệ.'
                ];
            }

            $who_is_assigned = $who_is_assigned->get()->first();

            // If people who is assigned is not in this team
            if ($who_is_assigned->team_id != $ticket->team_id) {
                return [
                    'status' => 0,
                    'phrase' => 'Không giao việc cho người team khác.'
                ];
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

            return [
                'status' => 1,
                'phrase' => ''
            ];
        }

        return [
            'status' => 0,
            'phrase' => 'Thao tác không hợp lệ.'
        ];
    }

    public function change_status(Request $request)
    {

        if ($request->has('ticket_id') && $request->has('status')) {
            $status = $request->input('status');
            $ticket_id = $request->input('ticket_id');

            if ($ticket_id <= 0) {
                return [
                    'status' => 0,
                    'phrase' => 'Không tìm thấy ticket.'
                ];
            }

            $ticket = Ticket::where('id', $ticket_id);

            if ($ticket->count() == 0) {
                return [
                    'status' => 0,
                    'phrase' => 'Không tìm thấy ticket.'
                ];
            }

            $ticket = $ticket->get()->first();

            if ($ticket->status == 5 || $ticket->status == 6) {
                return [
                    'status' => 0,
                    'phrase' => 'Không được thay đổi trạng thái của ticket này.'
                ];
            }

            if ($ticket->status == $status) {
                return [
                    'status' => 0,
                    'phrase' => 'Trạng thái không thay đổi.'
                ];
            }

            $employee_id = $request->session()->get('employee_id');
            $employee = Employee::where('id', $employee_id)->get()->first();

            $available = [
                // Who created ticket
                [

                    [6],
                    [6],
                    [4, 5, 6],
                    [5, 6]
                ],
                // Who is assigned
                [
                    [2],
                    [3],
                    [],
                    [2]
                ],
                // Who is team leader
                [
                    [2],
                    [3],
                    [4],
                    [2]
                ],
                // Who is company leader
                [
                    [2, 6],
                    [3, 6],
                    [4, 5, 6],
                    [2, 5, 6]
                ]
            ];

            $available_status = [];

            $old_status = $ticket->status - 1;

            if (self::is_created_by($ticket, $employee)) {
                $available_status = array_merge($available_status, $available[0][$old_status]);
            }

            if (self::is_assigned_to($ticket, $employee)) {
                $available_status = array_merge($available_status, $available[1][$old_status]);
            }

            if (self::is_team_leader($ticket, $employee)) {
                $available_status = array_merge($available_status, $available[2][$old_status]);
            }

            if (self::is_company_leader($employee)) {
                $available_status = array_merge($available_status, $available[3][$old_status]);
            }

            $available_status = array_unique($available_status);

            if (in_array($status, $available_status)) {
                $ticket->status = $status;
                $ticket->save();

                return [
                    'status' => 1,
                ];
            }

            return [
                'status' => 0,
                'phrase' => 'Thay đổi trạng thái không hợp lệ.'
            ];
        }

        return [
            'status' => 0,
            'phrase' => 'Thao tác không hợp lệ.'
        ];
    }


    /**
     * If current employee is who created this
     *
     * @param $ticket
     * @param $employee
     * @return bool
     */

    protected function is_created_by($ticket, $employee)
    {
        return ($ticket->created_by == $employee->id);
    }

    /**
     * If current employee is assigned this
     *
     * @param $ticket
     * @param $employee
     * @return bool
     */

    protected function is_assigned_to($ticket, $employee)
    {
        return ($ticket->assigned_to == $employee->id);
    }

    /**
     * If current employee is team leader
     *
     * @param $ticket
     * @param $employee
     * @return bool
     */

    protected function is_team_leader($ticket, $employee)
    {
        return ($ticket->team_id == $employee->team_id && $employee->role == 2);
    }

    /**
     * If current employee is who has all permissions
     *
     * @param $employee
     * @return bool
     */

    protected function is_company_leader($employee)
    {
        return ($employee->role == 3);
    }

    /**
     * Update status in database
     *
     * @param $ticket_id
     * @param $status
     * @return mixed
     */

    protected function update_status($ticket_id, $status)
    {
        return DB::table('tickets')->where('id', $ticket_id)->update(['status' => $status]);
    }

    /**
     * If input state is available to change to
     *
     * @param $old_status
     * @param $new_status
     * @param $available
     * @return bool
     */

    protected function is_available($old_status, $new_status, $available)
    {
        return in_array($new_status, $available[$old_status]);
    }
}
