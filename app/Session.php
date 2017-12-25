<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    /**
     * Return the employee who created this session
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function employee() {
        return $this->belongsTo('App\Employee', 'employee_id');
    }
}
