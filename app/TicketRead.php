<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketRead extends Model
{
    public function ticket() {
        return $this->belongsTo('Ticket', 'id');
    }

    public function people() {
        return $this->belongsTo('Employee', 'id');
    }
}
