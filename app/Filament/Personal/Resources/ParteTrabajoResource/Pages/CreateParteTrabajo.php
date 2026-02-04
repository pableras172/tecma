<?php

namespace App\Filament\Personal\Resources\ParteTrabajoResource\Pages;

use App\Filament\Personal\Resources\ParteTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Notifications\ParteTrabajoCreado;
use App\Models\User;

class CreateParteTrabajo extends CreateRecord
{
    protected static string $resource = ParteTrabajoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function afterCreate(): void
    {
        // Obtener todos los usuarios con rol admin
        $admins = User::role('admin')->get();
        
        // Enviar notificación a cada admin
        foreach ($admins as $admin) {
            $admin->notify(new ParteTrabajoCreado($this->record));
        }
    }
}
