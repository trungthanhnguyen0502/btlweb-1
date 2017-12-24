<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function is_related_to($ticket_id)
    {
        return $this->hasMany('TicketRelater', 'employee_id');
    }

    public function have_created() {
        return $this->hasOne('Ticket', 'id');
    }

    public function have_been_assigned() {
        return $this->hasOne('Ticket', 'id');
    }

    public function sessions() {
        return $this->hasMany('Session');
    }

    public function security_keys() {
        return $this->hasMany('PasswordResetKey');
    }

    public function have_read() {
        return $this->hasMany('TicketRead');
    }

    public function have_commented_on() {
        return $this->hasMany('TicketThread');
    }
}
