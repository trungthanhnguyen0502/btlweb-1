<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function ticket_relaters() {
        return $this->hasMany('App\TicketRelater')
    }
}
