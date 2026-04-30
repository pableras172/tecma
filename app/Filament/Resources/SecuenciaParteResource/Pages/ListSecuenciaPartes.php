<?php

namespace App\Filament\Resources\SecuenciaParteResource\Pages;

use App\Filament\Resources\SecuenciaParteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSecuenciaPartes extends ListRecords
{
    protected static string $resource = SecuenciaParteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Secuencia'),
        ];
    }
}
