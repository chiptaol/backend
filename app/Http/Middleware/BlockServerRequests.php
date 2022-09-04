<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockServerRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->server('HTTP_ORIGIN'), config('cors.allowed_origins'))) {
            return new JsonResponse([
                'message' => 'Not allowed to accept server requests.'
            ], 401);
        }

        return $next($request);
    }
}
