<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function relaters() {
        return $this->hasMany('TicketRelater');
    }
}
