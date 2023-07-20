<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::exists('accessToken')) {
            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json'
                ])
                ->withToken(Session::get('accessToken'))
                ->get(config('auth.api_url') . '/auth/me')->throw();

            } catch (\Exception $exception) {
                return redirect()->route('admin.login');
            }

            return $next($request); 
        }

        return redirect()->route('admin.login');
    }
}
