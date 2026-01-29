<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect(
            auth()->user()->hasRole('admin')
                ? route('filament.dashboard.pages.dashboard')
                : route('filament.personal.pages.dashboard')
        );
    }

    return redirect('/personal/login');
});

Route::redirect('/dashboard/login', '/personal/login');

// Rutas PWA
require __DIR__.'/pwa.php';
