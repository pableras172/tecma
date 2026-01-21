<?php

use Illuminate\Support\Facades\Route;

Route::get('/manifest.json', function () {
    return response()->json([
        'name' => setting('site_name', 'TECMA - Sistema de Gestión'),
        'short_name' => 'TECMA',
        'description' => 'Sistema de gestión de partes de trabajo',
        'start_url' => url('/dashboard'),
        'scope' => url('/'),
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => '#4f46e5',
        'orientation' => 'portrait-primary',
        'icons' => [
            [
                'src' => url('/images/icons/web-app-manifest-192x192.png'),
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => url('/images/icons/web-app-manifest-512x512.png'),
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any'
            ],
            [
                'src' => url('/images/icons/apple-touch-icon.png'),
                'sizes' => '180x180',
                'type' => 'image/png',
                'purpose' => 'any'
            ]
        ],
        'screenshots' => [
            [
                'src' => url('/images/screenshots/desktop-screenshot.png'),
                'sizes' => '1280x720',
                'type' => 'image/png',
                'form_factor' => 'wide',
                'label' => 'Vista de escritorio de TECMA'
            ],
            [
                'src' => url('/images/screenshots/mobile-screenshot.png'),
                'sizes' => '540x720',
                'type' => 'image/png',
                'label' => 'Vista móvil de TECMA'
            ]
        ],
        'related_applications' => [],
        'prefer_related_applications' => false,
        'app_version' => config('app.version') ?? '1.0.0',
    ])->header('Content-Type', 'application/manifest+json');
})->name('pwa.manifest');
