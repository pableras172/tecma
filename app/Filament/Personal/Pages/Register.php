<?php

namespace App\Filament\Personal\Pages;

use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    /**
     * Evita el auto-login tras el registro
     */
    protected function shouldLogin(): bool
    {
        return false;
    }

    /**
     * Redirección tras registrarse
     */
    protected function getRedirectUrl(): string
    {
        return route('filament.personal.pages.pending-activation');
    }
}