<?php

namespace App\Filament\Resources\ParteTrabajoResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ParteTrabajoResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

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
            Actions\Action::make('volver')
                ->label('Volver al listado')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => ParteTrabajoResource::getUrl('index')),

            Actions\Action::make('guardar_pdf')
                ->label('Guardar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    // Genera el HTML
                    $html = view('filament.resources.parte.parte', ['parte' => $this->record])->render();
                    
                    // Genera el PDF usando DomPDF
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                        ->setPaper('a4', 'landscape');
                    
                    // Define la ruta siguiendo la estructura: documentos/{nombrecliente}/{año}/{numeroparte}/parte_{numero}.pdf
                    $nombreCliente = \Str::slug($this->record->cliente?->nombre ?? 'sin-cliente');
                    $anio = date('Y');
                    $numeroParte = $this->record->numero;
                    $filename = "parte_{$numeroParte}.pdf";
                    $path = "documentos/{$nombreCliente}/{$anio}/{$numeroParte}/{$filename}";
                    
                    // Guarda en storage
                    \Storage::disk('public')->put($path, $pdf->output());
                    
                    // Crea un registro en la tabla docs
                    $doc = \App\Models\Doc::create([
                        'parte_trabajo_id' => $this->record->id,
                        'ruta' => $path,
                        'nombre_documento' => "Parte de trabajo {$numeroParte}",
                        'fecha' => now(),
                    ]);
                    
                    // Notificación con acción para ver el PDF
                    \Filament\Notifications\Notification::make()
                        ->title('PDF generado correctamente')
                        ->success()
                        ->body("El parte se ha guardado como documento")
                        ->actions([
                            Action::make('ver')
                                ->label('Ver PDF')
                                ->icon('heroicon-o-eye')
                                ->url(asset('storage/' . $path))
                                ->openUrlInNewTab(),
                        ])
                        ->send();
                    
                    // Recargar la tabla de documentos
                    $this->dispatch('refresh-docs');
                }),
            
            DeleteAction::make()->icon('heroicon-o-trash')
                ->label('Eliminar parte de trabajo'),

        ];
    }


}
