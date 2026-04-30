<?php

namespace App\Filament\Resources\ParteTrabajoResource\Pages;

use App\Filament\Resources\ParteTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Notifications\ParteTrabajoCreado;
use App\Models\User;
use App\Models\SecuenciaParte;

class CreateParteTrabajo extends CreateRecord
{
    protected static string $resource = ParteTrabajoResource::class;

    protected function afterCreate(): void
    {
        // Incrementar la secuencia
        if ($this->record->codigo && $this->record->anio) {
            SecuenciaParte::incrementar($this->record->codigo, $this->record->anio);
        }
        
        // Obtener todos los usuarios con rol admin
        $admins = User::role('admin')->get();
        
        // Enviar notificación a cada admin
        foreach ($admins as $admin) {
            $admin->notify(new ParteTrabajoCreado($this->record));
        }
    }
}
