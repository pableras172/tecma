<?php

namespace App\Filament\Resources\SecuenciaParteResource\Pages;

use App\Filament\Resources\SecuenciaParteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSecuenciaParte extends EditRecord
{
    protected static string $resource = SecuenciaParteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar'),
            Actions\Action::make('resetear')
                ->label('Resetear a 0')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Resetear secuencia')
                ->modalDescription('¿Está seguro que desea resetear esta secuencia a 0? El próximo parte comenzará desde 1.')
                ->modalSubmitActionLabel('Sí, resetear')
                ->action(function () {
                    $this->record->secuencia = 0;
                    $this->record->save();
                    
                    Notification::make()
                        ->success()
                        ->title('Secuencia reseteada')
                        ->body('La secuencia ha sido reseteada a 0.')
                        ->send();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Secuencia actualizada')
            ->body('La secuencia ha sido actualizada correctamente.');
    }
}
