<?php

namespace App\Livewire\Notifications;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function markAllAsRead(): void
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        $user->unreadNotifications->markAsRead();
    }

    public function render()
    {
        /** @var LengthAwarePaginator $notifications */
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(12);

        $unreadCount = auth()->user()->unreadNotifications()->count();

        return view('livewire.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
