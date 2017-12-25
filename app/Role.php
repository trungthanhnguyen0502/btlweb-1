<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * Return all employee has this role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function people() {
        return $this->hasMany('App\Employee', 'role');
    }
}
