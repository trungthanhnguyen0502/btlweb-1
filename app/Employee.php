<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /**
     * Return a set of tickets which is related to this employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function tickets() {
        return $this->belongsToMany('App\Ticket', 'ticket_relaters');
    }

    /**
     * Return a set of sessions which is created by this employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function sessions() {
        return $this->hasMany('App\Session', 'employee_id');
    }

    /**
     * Return a set of keys which is created by this employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function security_keys() {
        return $this->hasMany('App\PasswordResetKey', 'employee_id');
    }
}
