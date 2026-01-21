<?php

namespace App\Filament\Resources\ParteTrabajoResource\Pages;

use App\Filament\Resources\ParteTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParteTrabajos extends ListRecords
{
    protected static string $resource = ParteTrabajoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nuevo parte de trabajo')
            ->icon('heroicon-o-plus-circle')
            ->color('success'),
        ];
    }
}
