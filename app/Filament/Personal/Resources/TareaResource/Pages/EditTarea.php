<?php

namespace App\Filament\Personal\Resources\TareaResource\Pages;

use App\Filament\Personal\Resources\TareaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTarea extends EditRecord
{
    protected static string $resource = TareaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
