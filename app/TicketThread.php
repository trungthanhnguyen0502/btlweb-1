<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketThread extends Model
{
    public function ticket() {
        return $this->belongsTo('Ticket');
    }
}
