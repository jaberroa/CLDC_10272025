<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| These routes handle authentication for the application. They are loaded
| by the RouteServiceProvider and all of them will be assigned to the
| "auth" middleware group. Make something great!
|
*/

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
    
    Route::get('/register', function () { return view('auth.signup'); })->name('register');
    Route::post('/register', function () { 
        // Lógica de registro aquí
        return redirect()->route('login')->with('success', 'Cuenta creada exitosamente. Verifica tu email.');
    });
    
    Route::get('/forgot-password', function () { return view('auth.forgot-password'); })->name('password.request');
    Route::post('/forgot-password', function () { 
        // Lógica de envío de email aquí
        return back()->with('success', 'Se ha enviado un enlace de recuperación a tu email.');
    });
    
    Route::get('/reset-password/{token}', function ($token) { 
        return view('auth.reset-password', compact('token')); 
    })->name('password.reset');
    Route::post('/reset-password', function () { 
        // Lógica de restablecimiento aquí
        return redirect()->route('login')->with('success', 'Contraseña restablecida exitosamente.');
    });
});

// Email verification routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () { return view('auth.email-verify'); })->name('verification.notice');
    Route::post('/email/verification-notification', function () { 
        // Lógica de reenvío aquí
        return back()->with('success', 'Email de verificación reenviado.');
    })->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', function () { 
        // Lógica de verificación aquí
        return redirect()->route('dashboard')->with('success', 'Email verificado exitosamente.');
    })->name('verification.verify');
    
    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Static HTML reference routes (for development/demo)
Route::prefix('auth')->group(function () {
    Route::get('/signin-static', function () {
        return response()->file(public_path('auth/auth-signin.html'));
    })->name('auth.signin.static');
    
    Route::get('/signup-static', function () {
        return response()->file(public_path('auth/auth-signup.html'));
    })->name('auth.signup.static');
    
    Route::get('/forgot-password-static', function () {
        return response()->file(public_path('auth/auth-forgot-password.html'));
    })->name('auth.forgot-password.static');
    
    Route::get('/reset-password-static', function () {
        return response()->file(public_path('auth/auth-reset-password.html'));
    })->name('auth.reset-password.static');
    
    Route::get('/email-verify-static', function () {
        return response()->file(public_path('auth/auth-email-verify.html'));
    })->name('auth.email-verify.static');
    
    Route::get('/signout-static', function () {
        return response()->file(public_path('auth/auth-signout.html'));
    })->name('auth.signout.static');
});
