<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketRelater extends Model
{
    public function ticket() {
        return $this->belongsTo('Ticket');
    }
}
