<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DefaultController extends Controller
{
    /**
     * Default profile picture
     *
     * @return mixed
     */

    public function profile_picture() {
        return response(view()->file('default/profile-picture.png'))->header('Content-Type', 'image/x-png');
    }
}
