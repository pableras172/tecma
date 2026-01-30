<?php

namespace App\Filament\Personal\Resources\TareaResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Personal\Resources\TareaResource;
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
