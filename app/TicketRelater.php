<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketRelater extends Model
{
    /**
     * Return the ticket whichs is related to a employee

     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function ticket() {
        return $this->belongsTo('App\Ticket', 'ticket_id');
    }

    /**
     * Return the employee who relates to a ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function employee() {
        return $this->belongsTo('App\Employee', 'employee_id');
    }
}
