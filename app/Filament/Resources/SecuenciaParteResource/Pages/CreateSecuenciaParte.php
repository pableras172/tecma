<?php

namespace App\Filament\Resources\SecuenciaParteResource\Pages;

use App\Filament\Resources\SecuenciaParteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSecuenciaParte extends CreateRecord
{
    protected static string $resource = SecuenciaParteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->success()
            ->title('Secuencia creada')
            ->body('La secuencia ha sido creada correctamente.')
            ->send();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Verificar si ya existe una secuencia para este código y año
        $existe = \App\Models\SecuenciaParte::where('codigo', $data['codigo'])
            ->where('anio', $data['anio'])
            ->exists();

        if ($existe) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Ya existe una secuencia para este código y año.')
                ->persistent()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
