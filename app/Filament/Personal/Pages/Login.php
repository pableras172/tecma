<?php

namespace App\Filament\Personal\Pages;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getRedirectUrl(): ?string
    {
        $user = auth()->user();

        if($user) {
            \Log::info('User logged in', [           
                'user_id' =>  $user->id,
                'user_email' =>  $user->email,
            ]);

        }else{
            \Log::info('No user logged in');
        }
        
        if (! $user) {
            return null;
        }

        // Panel por rol
        if ($user->hasRole('admin')) {
            return route('filament.dashboard.pages.dashboard');
        }

        if ($user->hasRole('empleado')) {
            return route('filament.personal.pages.dashboard');
        }

        auth()->logout();
        abort(403);
    }
}
