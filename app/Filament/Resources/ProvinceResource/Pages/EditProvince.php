<?php

namespace App\Filament\Resources\ProvinceResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ProvinceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProvince extends EditRecord
{
    protected static string $resource = ProvinceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
