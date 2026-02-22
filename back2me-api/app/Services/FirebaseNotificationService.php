<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS')));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Envoyer une notification à tous les utilisateurs via un topic.
     *
     * @param string $title
     * @param string $body
     * @param array $data Données supplémentaires (ex: objet_id)
     * @return array|null
     */
    public function sendToAll(string $title, string $body, array $data = [])
    {
        $message = CloudMessage::withTarget('topic', 'all_users')
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        try {
            return $this->messaging->send($message);
        } catch (MessagingException $e) {
            \Log::error('Firebase sendToAll error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Envoyer une notification à un utilisateur spécifique via son token FCM.
     *
     * @param string $token
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array|null
     */
    public function sendToUser(string $token, string $title, string $body, array $data = [])
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        try {
            return $this->messaging->send($message);
        } catch (MessagingException $e) {
            \Log::error('Firebase sendToUser error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Envoyer une notification à plusieurs utilisateurs (multicast, jusqu'à 500 tokens)
     *
     * @param array $tokens
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array|null
     */
    public function sendToMultiple(array $tokens, string $title, string $body, array $data = [])
    {
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        try {
            return $this->messaging->sendMulticast($message, $tokens);
        } catch (MessagingException $e) {
            \Log::error('Firebase sendToMultiple error: ' . $e->getMessage());
            return null;
        }
    }
}