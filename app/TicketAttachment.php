<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    /**
     * Return employee who uploaded this attachment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function employee() {
        return $this->belongsTo('App\Employee', 'employee_id');
    }
}
