<?php

namespace App\Notifications;

use App\Models\Pqrsf;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PqrsfStatusChangedNotification extends Notification
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
            'title' => 'Cambio de estado en PQRSF',
            'radicado' => $this->pqrsf->radicado,
            'estado' => $this->pqrsf->estado?->nombre,
        ];
    }
}
