<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role !== 'admin' && !auth()->user()->is_approved) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'حسابك في انتظار الموافقة من قبل الإدارة');
        }

        return $next($request);
    }
}
