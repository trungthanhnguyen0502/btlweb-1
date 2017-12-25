<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordResetKey extends Model
{
    /**
     * Return the employee who created this key
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function employee() {
        return $this->belongsTo('App\Employee', 'employee_id');
    }
}
