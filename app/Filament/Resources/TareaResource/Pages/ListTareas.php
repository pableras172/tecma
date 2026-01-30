<?php

namespace App\Filament\Resources\TareaResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\TareaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTareas extends ListRecords
{
    protected static string $resource = TareaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
