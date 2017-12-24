<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketThread extends Model
{
    public function commented_by() {
        return $this->belongsTo('App\Employee', 'employee_id');
    }
}
