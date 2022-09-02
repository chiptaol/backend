<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class AuthenticateRequests
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->server('HTTP_ORIGIN'), config('cors.allowed_origins'))) {
            return new JsonResponse([
                'message' => 'Not allowed origin'
            ], 401);
        }

        return $next($request);
    }
}
