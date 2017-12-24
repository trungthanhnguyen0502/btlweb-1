<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function employees() {
        return $this->hasMany('Employee');
    }

    public function tickets() {
        return $this->hasMany('Ticket');
    }
}
