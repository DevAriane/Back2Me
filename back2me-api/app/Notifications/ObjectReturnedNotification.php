<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ObjectReturnedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $objetId,
        public string $name,
        public string $location,
        public ?string $claimerName = null,
        public ?string $photoUrl = null,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'returned_object',
            'title' => 'Objet rendu',
            'body' => $this->claimerName
                ? "L'objet {$this->name} a ete rendu au nom de {$this->claimerName}."
                : "L'objet {$this->name} a ete rendu.",
            'objet_id' => $this->objetId,
            'name' => $this->name,
            'location' => $this->location,
            'claimer_name' => $this->claimerName,
            'photo_url' => $this->photoUrl,
            'action_url' => route('objets.show', ['objet' => $this->objetId]),
        ];
    }
}
