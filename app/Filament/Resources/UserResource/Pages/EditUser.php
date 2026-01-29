<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('volver')
                ->label('Volver al listado')
                ->url(route('filament.dashboard.resources.users.index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
