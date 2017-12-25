<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * Return the employee who this ticket is created_by
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function created_by_employee() {
        return $this->belongsTo('App\Employee', 'created_by');
    }

    /**
     * Return the employee who this ticket is assigned to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function assigned_to_employee() {
        return $this->belongsTo('App\Employee', 'assigned_to');
    }

    /**
     * Return relaters of this ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function relaters() {
        return $this->hasMany('App\TicketRelater', 'ticket_id');
    }

    /**
     * Return comments in this ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function comments() {
        return $this->hasMany('App\TicketThread', 'ticket_id');
    }
}
