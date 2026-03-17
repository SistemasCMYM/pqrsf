<?php

namespace App\Notifications;

use App\Models\Pqrsf;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PqrsfCreatedNotification extends Notification
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
            'title' => 'Nueva PQRSF radicada',
            'radicado' => $this->pqrsf->radicado,
            'asunto' => $this->pqrsf->asunto,
        ];
    }
}
