<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function created_by_employee() {
        return $this->belongsTo('App\Employee', 'created_by');
    }

    public function assigned_to_employee() {
        return $this->belongsTo('App\Employee', 'assigned_to');
    }

    public function relaters() {
        return $this->hasMany('App\TicketRelater', 'ticket_id');
    }
}
