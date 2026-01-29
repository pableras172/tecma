<?php

namespace App\Filament\Personal\Pages;

use Filament\Pages\Page;

class PendingActivation extends Page
{
    protected static string $view = 'filament.personal.pages.pending-activation';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * 🔑 CLAVE: permitir acceso sin login
     */
    public static function canAccess(): bool
    {
        return true;
    }

    /**
     * Usar layout simple sin sidebar
     */
    protected function getLayoutData(): array
    {
        return [
            'hasTopbar' => false,
        ];
    }

    /**
     * Contenido de ancho completo
     */
    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
