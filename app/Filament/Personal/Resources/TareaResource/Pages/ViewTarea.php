<?php

namespace App\Filament\Personal\Resources\TareaResource\Pages;

use App\Filament\Personal\Resources\TareaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTarea extends ViewRecord
{
    protected static string $resource = TareaResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\Action::make('volver')
                ->label('Volver al listado')
                ->url(route('filament.personal.resources.tareas.index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];

        // Solo mostrar editar y eliminar a los admins
        if (auth()->user()->hasRole('admin')) {
            $actions[] = Actions\EditAction::make();
            $actions[] = Actions\DeleteAction::make();
        }

        return $actions;
    }
}
