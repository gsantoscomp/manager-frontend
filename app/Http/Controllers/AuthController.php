<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json'
            ])
            ->post(config('auth.api_url') . '/auth/login',[
                'email' => $credentials['email'],
                'password' => $credentials['password']
            ])
            ->throw();
            
        } catch (\Exception $exception) {
            return redirect()->route('admin.login');
        }

        if (!$response->successful()) {
            return redirect()->route('admin.login');
        }

        session()->put([
            'accessToken' => $response->object()->access_token,
            'user'=> $response->object()->user,
            'permissions' => $response->object()->permissions,
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        if (!session()->exists('accessToken')) {
            return redirect()->route('admin.login');
        }

        try {
            Http::withHeaders([
                'Accept' => 'application/json'
            ])
            ->withToken(session()->get('accessToken'))
            ->post(config('auth.api_url') . '/auth/logout')
            ->throw();

        } catch (\Exception $exception) {
            return redirect()->back();
        }

        session()->flush();

        return redirect()->route('admin.login');
    }
}
