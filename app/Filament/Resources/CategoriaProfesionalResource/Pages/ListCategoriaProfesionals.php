<?php

namespace App\Filament\Resources\CategoriaProfesionalResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\CategoriaProfesionalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriaProfesionals extends ListRecords
{
    protected static string $resource = CategoriaProfesionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
