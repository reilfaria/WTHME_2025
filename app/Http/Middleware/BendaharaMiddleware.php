<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BendaharaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isBendahara()) {
            return redirect()->route('panitia.index')
                ->with('error', 'Akses ditolak. Halaman ini khusus untuk Bendahara.');
        }
        return $next($request);
    }
}
