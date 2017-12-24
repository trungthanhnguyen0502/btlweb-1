<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function is_related_to($ticket_id) {
        $rela = TicketRelater::where('employee_id', $this->id)->where('ticket_id', $ticket_id)->count();

        return ($rela != 0);
    }
}
