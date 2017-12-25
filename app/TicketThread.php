<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketThread extends Model
{
    /**
     * Return the employee who commented this
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function commented_by() {
        return $this->belongsTo('App\Employee', 'employee_id');
    }

    /**
     * Return the ticket who this comment is commented on
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function commented_on() {
        return $this->belongsTo('App\Ticket', 'ticket_id');
    }
}
