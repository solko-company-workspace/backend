<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->is_admin) {
            return response()->json([
                'success' => false,
                'status'  => 403,
                'message' => '접근 권한이 없습니다.',
                'data'    => null,
            ], 403);
        }

        return $next($request);
    }
}
