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
            'body' => "L'objet {$this->name} a ete rendu.",
            'objet_id' => $this->objetId,
            'name' => $this->name,
            'location' => $this->location,
            'photo_url' => $this->photoUrl,
            'action_url' => route('objets.show', ['objet' => $this->objetId]),
        ];
    }
}
