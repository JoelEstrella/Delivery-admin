<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('guest')->only(['showLoginForm', 'login', 'forgotPassword']);
        $this->middleware('auth')->only(['logout']);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        list($success, $result) = $this->authService->login(
            $request->input('login'),
            $request->input('password'),
            $request->boolean('remember')
        );

        if (!$success) {
            return back()
                ->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => $result]);
        }

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Bienvenido al sistema.');
    }

    public function logout(Request $request)
    {
        $this->authService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Tu sesión ha sido cerrada.');
    }

    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }
}
