<?php

namespace App\Filament\Resources\TipoTrabajoResource\Pages;

use App\Filament\Resources\TipoTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoTrabajo extends EditRecord
{
    protected static string $resource = TipoTrabajoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
