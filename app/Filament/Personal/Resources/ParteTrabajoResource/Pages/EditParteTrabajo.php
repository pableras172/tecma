<?php

namespace App\Filament\Personal\Resources\ParteTrabajoResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Personal\Resources\ParteTrabajoResource;
use Filament\Actions;
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
            DeleteAction::make(),
        ];
    }
}
