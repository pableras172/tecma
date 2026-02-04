<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\ParteTrabajo;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Actions\Action;

class ParteTrabajoCreado extends Notification
{
    use Queueable;

    protected $parte;

    /**
     * Create a new notification instance.
     */
    public function __construct(ParteTrabajo $parte)
    {
        $this->parte = $parte;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for Filament.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $creador = $this->parte->creador->name ?? 'Usuario desconocido';
        $cliente = $this->parte->cliente->nombre ?? 'Cliente desconocido';
        
        return FilamentNotification::make()
            ->title('Nuevo parte de trabajo creado')
            ->body("**{$creador}** ha creado el parte **{$this->parte->numero}** para el cliente **{$cliente}**")
            ->icon('heroicon-o-document-plus')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label('Ver parte')
                    ->url(route('filament.dashboard.resources.parte-trabajos.edit', ['record' => $this->parte->id]))
                    ->button(),
            ])
            ->getDatabaseMessage();
    }
}
