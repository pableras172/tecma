<?php

namespace App\Filament\Resources\PlantaResource\Pages;

use App\Filament\Resources\PlantaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanta extends EditRecord
{
    protected static string $resource = PlantaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
