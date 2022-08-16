<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AppendHeadersToResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasCookie('Authorization')) {
            $request->headers->set('Authorization', $request->cookie('Authorization'));
        }

        return $next($request);
    }
}
