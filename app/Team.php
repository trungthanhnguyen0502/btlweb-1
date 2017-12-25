<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /**
     * Return a set of employees who work in this team
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function members() {
        return $this->hasMany('App\Employee', 'team_id');
    }

    /**
     * Return a set of tickets which is assigned to this team
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function tickets() {
        return $this->hasMany('App\Ticket', 'team_id');
    }
}
