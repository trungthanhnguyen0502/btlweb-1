<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordResetKey extends Model
{
    public function people() {
        return $this->belongsTo('Employee');
    }
}
