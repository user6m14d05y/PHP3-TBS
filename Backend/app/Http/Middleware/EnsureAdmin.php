<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Chi admin moi duoc thuc hien thao tac nay.');
        }

        return $next($request);
    }
}
