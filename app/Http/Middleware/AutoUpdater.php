<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class AutoUpdater
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $deadline = date('Y-m-d H:i:s', $request->server('REQUEST_TIME'));
        DB::table('tickets')->where('deadline', '<=', $deadline)->update(['out_of_date' => 1]);
        return $next($request);
    }
}
