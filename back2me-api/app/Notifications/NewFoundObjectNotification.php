<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewFoundObjectNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $objetId,
        public string $name,
        public string $location,
        public ?string $foundDate = null,
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
            'type' => 'found_object',
            'title' => 'Nouvel objet trouve',
            'body' => "Un(e) {$this->name} a ete trouve(e) a {$this->location}.",
            'objet_id' => $this->objetId,
            'name' => $this->name,
            'location' => $this->location,
            'found_date' => $this->foundDate,
            'photo_url' => $this->photoUrl,
            'action_url' => route('objets.show', ['objet' => $this->objetId]),
        ];
    }
}
