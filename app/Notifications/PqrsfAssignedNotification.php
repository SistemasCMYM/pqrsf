<?php

namespace App\Notifications;

use App\Models\Pqrsf;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PqrsfAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Pqrsf $pqrsf)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'PQRSF asignada',
            'radicado' => $this->pqrsf->radicado,
            'estado' => $this->pqrsf->estado?->nombre,
        ];
    }
}
