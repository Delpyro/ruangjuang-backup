<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Izinkan jika dia Admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }
        
        // (Opsional) Jika kamu mau Owner JUGA BISA mengakses halaman admin
        // if (Auth::check() && in_array(Auth::user()->role, ['admin', 'owner'])) {
        //     return $next($request);
        // }

        // Jika dia Owner yang nyasar ke link admin, kembalikan ke tempatnya
        if (Auth::check() && Auth::user()->role === 'owner') {
             return redirect('/owner/dashboard');
        }

        // Kalau bukan admin/owner → redirect ke dashboard user biasa
        return redirect('/dashboard');
    }
}