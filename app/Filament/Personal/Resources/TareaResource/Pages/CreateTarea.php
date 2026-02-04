<?php

namespace App\Filament\Personal\Resources\TareaResource\Pages;

use Filament\Actions\Action;
use App\Filament\Personal\Resources\TareaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Notifications\TareaAsignada;

class CreateTarea extends CreateRecord
{
    protected static string $resource = TareaResource::class;

    protected function afterCreate(): void
    {
        Log::debug('Entrando en afterCreate() de CreateTarea (Panel Personal)');
        Log::debug('Tarea creada:', ['id' => $this->record->id, 'titulo' => $this->record->titulo]);

        $usuarios = $this->record->usuarios;

        Log::debug('Usuarios asignados:', ['count' => $usuarios->count()]);

        foreach ($usuarios as $usuario) {
            Log::debug('Notificando a usuario:', ['id' => $usuario->id, 'email' => $usuario->email]);

            try {
                // Usar notificación de Laravel
                $usuario->notify(new TareaAsignada($this->record));
                
                Log::debug('Notificación enviada correctamente a usuario: ' . $usuario->id);
                
                // Verificar si se guardó
                $count = $usuario->notifications()->count();
                Log::debug('Total notificaciones del usuario: ' . $count);
            } catch (\Exception $e) {
                Log::error('Error al enviar notificación:', [
                    'usuario_id' => $usuario->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        Log::debug('Fin de notificaciones');
    }
}
