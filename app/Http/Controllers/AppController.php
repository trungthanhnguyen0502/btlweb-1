<?php

namespace App\Http\Controllers;

class AppController extends Controller
{
    public function index()
    {
        return response()->file('../app.html');
    }

    public function redirect()
    {
        return redirect('/index.html');
    }
}
