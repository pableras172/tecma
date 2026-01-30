<?php

namespace App\Filament\Resources\PlantaResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PlantaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlantas extends ListRecords
{
    protected static string $resource = PlantaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
