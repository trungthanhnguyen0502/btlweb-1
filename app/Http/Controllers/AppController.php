<?php

namespace App\Http\Controllers;

class AppController extends Controller
{
    /**
     * App main page
     *
     * @return mixed
     */

    public function index()
    {
        return response()->file('../app.html');
    }

    /**
     * Home page redirecting
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */

    public function redirect()
    {
        return redirect('/index.html');
    }
}
