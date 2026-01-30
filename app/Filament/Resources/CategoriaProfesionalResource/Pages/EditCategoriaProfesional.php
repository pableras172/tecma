<?php

namespace App\Filament\Resources\CategoriaProfesionalResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\CategoriaProfesionalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaProfesional extends EditRecord
{
    protected static string $resource = CategoriaProfesionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
