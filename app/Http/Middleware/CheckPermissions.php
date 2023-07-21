<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $needle = ''): Response
    {
        $userPermissions = session('permissions');

        if (!$userPermissions || !in_array($needle, $userPermissions)) {
            return redirect()->back();
        }

        return $next($request);
    }
}
