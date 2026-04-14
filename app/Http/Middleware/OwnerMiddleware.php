<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OwnerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user login DAN rolenya adalah 'owner'
        if (Auth::check() && Auth::user()->role === 'owner') {
            return $next($request);
        }

        // Jika bukan owner, arahkan ke dashboard customer (atau admin jika dia admin)
        if (Auth::check() && Auth::user()->role === 'admin') {
             return redirect('/admin/dashboard');
        }

        return redirect('/dashboard');
    }
}