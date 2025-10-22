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
        return view('auth.signin');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Verificar si hay una redirección específica guardada
        $redirectModule = $request->input('redirect_module');
        
        // Log para debug
        \Log::info('Login attempt', [
            'redirect_module' => $redirectModule,
            'all_input' => $request->all()
        ]);
        
        if ($redirectModule) {
            \Log::info('Redirecting to module', ['module' => $redirectModule]);
            
            switch ($redirectModule) {
                case 'Dashboard':
                    return redirect()->route('dashboard');
                case 'Miembros':
                    return redirect()->route('miembros.index');
                case 'Directiva':
                    return redirect()->route('directiva.index');
                default:
                    return redirect()->intended(route('dashboard'));
            }
        }

        \Log::info('No redirect module, going to dashboard');
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
