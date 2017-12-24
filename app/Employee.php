<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function tickets() {
        return $this->belongsToMany('App\Ticket', 'ticket_relaters');
    }
}
