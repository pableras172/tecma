<?php

namespace App\Filament\Personal\Resources\ParteTrabajoResource\Pages;

use App\Filament\Personal\Resources\ParteTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParteTrabajos extends ListRecords
{
    protected static string $resource = ParteTrabajoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
