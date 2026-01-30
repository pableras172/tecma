<?php

namespace App\Filament\Resources\ParteTrabajoResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ParteTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParteTrabajos extends ListRecords
{
    protected static string $resource = ParteTrabajoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Nuevo parte de trabajo')
            ->icon('heroicon-o-plus-circle')
            ->color('success'),
        ];
    }
}
