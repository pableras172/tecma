<?php

namespace App\Filament\Resources\TareaResource\Pages;

use Filament\Actions\Action;
use App\Filament\Resources\TareaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CreateTarea extends CreateRecord
{
    protected static string $resource = TareaResource::class;

    protected function afterCreate(): void
    {
        Log::debug('Entrando en afterCreate() de CreateTarea');
        Log::debug('Tarea creada:', ['id' => $this->record->id, 'titulo' => $this->record->titulo]);

        $usuarios = $this->record->usuarios;

        Log::debug('Usuarios asignados:', ['count' => $usuarios->count()]);

        foreach ($usuarios as $usuario) {
            Log::debug('Notificando a usuario:', ['id' => $usuario->id, 'email' => $usuario->email]);

            Notification::make()
                ->title('Nueva tarea asignada')
                ->body("Se te ha asignado la tarea: {$this->record->titulo}")
                ->actions([
                    Action::make('Ver')
                        ->url(route('filament.personal.resources.tareas.edit', ['record' => $this->record])),
                ])                
                ->sendToDatabase($usuario);
        }
        Log::debug('Fin de notificaciones');
    }
}
