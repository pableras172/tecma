<?php

namespace App\Filament\Personal\Pages;

class Register extends \Filament\Auth\Pages\Register
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