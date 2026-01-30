<?php

namespace App\Filament\Resources\TipoTrabajoResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\TipoTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoTrabajos extends ListRecords
{
    protected static string $resource = TipoTrabajoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Nuevo tipo de trabajo')
            ->icon('heroicon-o-plus-circle')
            ->color('success'),
        ];
    }
}
