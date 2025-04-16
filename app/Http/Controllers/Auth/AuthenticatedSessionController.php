<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request  $request): RedirectResponse
    {
        $request->validate([
            'codigo' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['codigo' => $request->codigo, 'password' => $request->password], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'codigo' => __('Credenciales inválidas.'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
