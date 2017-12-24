<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function relaters() {
        return $this->hasMany('TicketRelater');
    }

    public function created_by() {
        return $this->belongsTo('Employee', 'id');
    }

    public function assigned_to() {
        return $this->belongsTo('Employee', 'id');
    }
}
