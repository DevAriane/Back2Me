<?php

namespace App\Services;

use App\Models\Objet;
use App\Models\User;
use App\Notifications\NewFoundObjectNotification;
use App\Notifications\ObjectReturnedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ObjectFoundNotifier
{
    public function notifyAllUsers(Objet $objet): void
    {
        $payload = new NewFoundObjectNotification(
            objetId: $objet->id,
            name: $objet->name,
            location: $objet->location,
            foundDate: optional($objet->found_date)->toDateString(),
            photoUrl: $objet->photo_url,
        );

        User::query()->select(['id'])->chunkById(200, function ($users) use ($payload): void {
            Notification::send($users, $payload);
        });

        try {
            $firebase = new FirebaseNotificationService();
            $firebase->sendToAll(
                'Nouvel objet trouve',
                "Un(e) {$objet->name} a ete trouve(e) a {$objet->location}.",
                ['objet_id' => (string) $objet->id, 'type' => 'new_object']
            );
        } catch (\Throwable $e) {
            Log::error('Erreur notification objet trouve: ' . $e->getMessage());
        }
    }

    public function notifyAllUsersObjectReturned(Objet $objet): void
    {
        $payload = new ObjectReturnedNotification(
            objetId: $objet->id,
            name: $objet->name,
            location: $objet->location,
            photoUrl: $objet->photo_url,
        );

        User::query()->select(['id'])->chunkById(200, function ($users) use ($payload): void {
            Notification::send($users, $payload);
        });

        try {
            $firebase = new FirebaseNotificationService();
            $firebase->sendToAll(
                'Objet rendu',
                "L'objet {$objet->name} a ete rendu.",
                ['objet_id' => (string) $objet->id, 'type' => 'object_returned']
            );
        } catch (\Throwable $e) {
            Log::error('Erreur notification objet rendu: ' . $e->getMessage());
        }
    }
}
