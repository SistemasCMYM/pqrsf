<?php

namespace App\Notifications;

use App\Models\Pqrsf;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PqrsfSlaAlertNotification extends Notification
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
            'title' => 'Alerta SLA PQRSF',
            'radicado' => $this->pqrsf->radicado,
            'dias_restantes' => $this->pqrsf->dias_restantes,
        ];
    }
}
