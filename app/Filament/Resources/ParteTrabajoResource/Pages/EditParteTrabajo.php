<?php

namespace App\Filament\Resources\ParteTrabajoResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ParteTrabajoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Torgodly\Html2Media\Actions\Html2MediaAction;


class EditParteTrabajo extends EditRecord
{
    protected static string $resource = ParteTrabajoResource::class;

    protected $listeners = ['reloadTotales' => 'reloadFormData'];

    public function reloadFormData(): void
    {
        // Recargar el registro desde la base de datos
        $this->record = $this->record->fresh();
        
        // Rellenar el formulario con los nuevos datos
        $this->fillForm();
    }

    protected function getHeaderActions(): array
    {
        return [
            //añade un boton de ver el parte. Abrira la vista personalizada del parte

            Html2MediaAction::make('print')
                ->scale(1)
                ->label('Imprimir parte')
                ->icon('heroicon-o-printer')
                ->print() // Enable print option                
                ->preview() // Enable preview option
                ->filename('Parte de trabajo ') // Custom file name
                ->savePdf() // Enable save as PDF option
                ->requiresConfirmation() // Show confirmation modal
                ->pagebreak('section', ['css', 'legacy'])
                ->orientation('landscape') // Landscape orientation
                ->format('a4', 'mm') // A4 format with mm units
                ->enableLinks() // Enable links in PDF
                ->margin([5, 10, 5, 10]) // Set custom margins
                ->content(fn($record) => view('filament.resources.parte.parte', ['parte' => $record])),
            DeleteAction::make()->icon('heroicon-o-trash')
                ->label('Eliminar parte de trabajo'),

        ];
    }


}
