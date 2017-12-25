<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketRead extends Model
{
    /**
     * Return the ticket which is read
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function ticket() {
        return $this->belongsTo('App\Ticket', 'ticket_id');
    }

    /**
     * Return the employee who reads ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function employee() {
        return $this->belongsTo('App\Employee', 'employee_id');
    }
}
