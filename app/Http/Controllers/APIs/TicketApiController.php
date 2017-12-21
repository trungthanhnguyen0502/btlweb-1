<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Ticket;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    public function create_ticket(Request $request)
    {
        if ($request->session()->has('login_key')) {

            $ticket = new Ticket();
            $ticket->create_by = session('employee_id');
            $ticket->subject = $request->input('subject');
            $ticket->status = $request->input('status');
            $ticket->priority = $request->input('priority');
//            $ticket->assigned_to = $request->input('assigned_to');
            $ticket->deadline = strtotime($request->input('deadline'));
            $ticket->team_id = $request->input('team_id');
            $ticket->content = $request->input('content');

            $ticket->save();
            return 1;

//            if ($request->input('attachment')) {
//                $attachment = $request->input('attachment');
//
//            }
        } else {
            return 0;
        }
    }

    public function get_ticket(Request $request) {
        if ($request->session()->has('login_key')) {
            $result = Ticket::all();
            if ($request->input('id')) {
                $result->where('id', $request->input('id'));
            }
            if ($request->input('subject')) {
                $result->where('subject', 'LIKE', $request->input('subject') . '%');
            }
            if ($request->input('created_by')) {
                $result->where('created_by', $request->input('created_by'));
            }
            if ($request->input('status')) {
                $result->where('status', $request->input('status'));
            }
            if ($request->input('priority')) {
                $result->where('priority', $request->input('prioriy'));
            }
            if ($request->input('assigned_to')) {
                $result->where('assigned_to', $request->input('assigned_to'));
            }
            if ($request->input('deadline')) {
                $deadline = strtotime($request->input('deadline'));
                $result->where('deadline', $deadline);
            }
//            if ($request->input('related_user_id')) {
//                $relaters = $request->user
//            }
            if ($request->input('team_id')) {
                $result->where('team_id', $request->input('team_id'));
            }
            if ($request->input('content')) {
                $result->where('content', 'LIKE', $request->input('content'));
            }
            $tickets = $result->get();
            if ($request->input('per_page')) {
                $per_page = $request->input('per_page');
                $page = intval($request->input('page'));

                return array_slice($tickets, $page * $per_page, $per_page);
            }

            return $tickets;
        } else {
            return 0;
        }
    }
}
