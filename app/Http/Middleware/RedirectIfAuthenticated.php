<?php

namespace App\Http\Middleware;

use App\Session;
use Closure;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->session()->has('login_key')) {
            return redirect('/');
        } else {
            $login_key = $request->cookie('login_key');
            $employee_id = $request->cookie('employee_id');
            $request_time = $request->server('REQUEST_TIME');

            if ($this->isValidSession($login_key, $employee_id, $request_time)) {
                return redirect('/');
            }
        }
        return $next($request);
    }

    protected function isValidSession($login_key, $employee_id, $request_time)
    {
        $session = Session::where('login_key', $login_key)
            ->where('employee_id', $employee_id)
            ->where('expired_at', '>', $request_time)
            ->get();
        return ($session->count() == 1);
    }
}
