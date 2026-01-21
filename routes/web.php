<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.dashboard.pages.dashboard');
});

// Rutas PWA
require __DIR__.'/pwa.php';
