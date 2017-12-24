<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    public function people() {
        return $this->belongsTo('Employee');
    }
}
