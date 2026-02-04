<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Tarea;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Actions\Action;

class TareaAsignada extends Notification
{
    use Queueable;

    protected $tarea;

    /**
     * Create a new notification instance.
     */
    public function __construct(Tarea $tarea)
    {
        $this->tarea = $tarea;
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
        return FilamentNotification::make()
            ->title('Nueva tarea asignada')
            ->body("Se te ha asignado la tarea: {$this->tarea->titulo}")
            ->icon('heroicon-o-clipboard-document-check')
            ->iconColor('success')
            ->actions([
                Action::make('view')
                    ->label('Ver tarea')
                    ->url(route('filament.personal.resources.tareas.view', ['record' => $this->tarea->id]))
                    ->button(),
            ])
            ->getDatabaseMessage();
    }
}
