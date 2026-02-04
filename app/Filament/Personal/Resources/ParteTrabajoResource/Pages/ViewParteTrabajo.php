<?php

namespace App\Filament\Personal\Resources\ParteTrabajoResource\Pages;

use Filament\Actions\Action;
use App\Filament\Personal\Resources\ParteTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewParteTrabajo extends ViewRecord
{
    protected static string $resource = ParteTrabajoResource::class;
    protected $listeners = ['reloadTotales' => 'reloadFormData'];

    protected function getHeaderActions(): array
    {
        return [        
            

            Action::make('volver')
                ->label('Volver al listado')
                ->url(route('filament.personal.resources.parte-trabajos.index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
