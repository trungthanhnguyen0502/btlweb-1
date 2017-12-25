<?php
namespace App\Http\Middleware;
use App\PasswordResetKey;
use App\Session;
use Closure;
class AutoCleaner
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
        $next_request = $next($request);
        $request_time = $request->server('REQUEST_TIME');
        // Clean expired sessions
        Session::where('expired_at', '<=', $request_time)->delete();
        // Clean expired password reset security keys
        PasswordResetKey::where('expired_at', '<=', $request_time)->delete();
        return $next_request;
    }
}
