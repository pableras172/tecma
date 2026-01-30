<?php

namespace App\Filament\Personal\Resources\ParteTrabajoResource\Pages;

use Filament\Actions\Action;
use App\Filament\Personal\Resources\ParteTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Torgodly\Html2Media\Actions\Html2MediaAction;

class ViewParteTrabajo extends ViewRecord
{
    protected static string $resource = ParteTrabajoResource::class;
    protected $listeners = ['reloadTotales' => 'reloadFormData'];

    protected function getHeaderActions(): array
    {
        return [
            Html2MediaAction::make('print')
                //->scale(1)
                ->label('Imprimir parte')
                ->icon('heroicon-o-printer')
                ->print() // Enable print option                
                ->preview() // Enable preview option
                ->filename('Parte de trabajo ') // Custom file name
                ->savePdf() // Enable save as PDF option
                ->requiresConfirmation() // Show confirmation modal
                //->pagebreak('section', ['css', 'legacy'])
                ->orientation('landscape') // Landscape orientation
                ->format('a4', 'mm') // A4 format with mm units
                ->enableLinks() // Enable links in PDF
                //->margin([5, 10, 5, 10]) // Set custom margins
                ->content(fn($record) => view('filament.resources.parte.parte', ['parte' => $record])),
            

            Action::make('volver')
                ->label('Volver al listado')
                ->url(route('filament.personal.resources.parte-trabajos.index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
