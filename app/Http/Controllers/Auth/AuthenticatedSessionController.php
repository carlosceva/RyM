<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Validation\ValidationException;

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
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Normaliza el código a minúsculas
        $codigo = strtolower($request->input('codigo'));

        // Busca al usuario en minúsculas (asume que en DB también están guardados así)
        $user = User::whereRaw('LOWER(codigo) = ?', [$codigo])->first();

        if (!$user || $user->estado !== 'a') {
            throw ValidationException::withMessages([
                'codigo' => __('Estas credenciales no coinciden con nuestros registros o el usuario no está activo.'),
            ]);
        }

        // Intentar login usando el código en minúsculas
        if (!Auth::attempt(['codigo' => $codigo, 'password' => $request->input('password')], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => __('Credenciales inválidas.'),
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
