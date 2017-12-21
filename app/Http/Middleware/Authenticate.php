<?php

namespace App\Http\Middleware;

use App\Session;
use Closure;

class Authenticate
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
            // If user has login_key in session
            return $next($request);
        } else {
            $request_time = $request->server('REQUEST_TIME');
            $login_key = $request->cookie('login_key', '');
            $employee_id = $request->cookie('employee_id', 0);

            if ($this->isValidSession($login_key, $employee_id, $request_time)) {
                $request->session()->put('login_key', $login_key);
                $request->session()->put('employee_id', $employee_id);

                return $next($request);
            }

            return $this->redirectToLogin();
        }
    }

    protected function isValidSession($login_key, $employee_id, $request_time)
    {
        $session = Session::where('login_key', $login_key)
            ->where('employee_id', $employee_id)
            ->where('expired_at', '>', $request_time)
            ->get();
        return ($session->count() == 1);
    }

    public function redirectToLogin()
    {
        return redirect(route('login'));
    }
}
