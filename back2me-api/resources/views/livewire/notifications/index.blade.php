<div class="page">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div class="page-title">Notifications</div>
            <div class="page-sub">{{ $unreadCount }} nouvelle(s) notification(s)</div>
        </div>
        <button class="btn btn-outline" wire:click="markAllAsRead" @disabled($unreadCount === 0)>Tout marquer comme lu</button>
    </div>
    <div class="notif-list">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data ?? [];
                $title = $data['title'] ?? 'Notification';
                $body = $data['body'] ?? 'Vous avez une nouvelle notification.';
                $actionUrl = $data['action_url'] ?? null;
                $isUnread = is_null($notification->read_at);
                $notifType = $data['type'] ?? '';
                $icon = $notifType === 'found_object' ? '📦' : ($notifType === 'returned_object' ? '✅' : '🔔');
                $iconClass = $notifType === 'found_object' ? 'info' : ($notifType === 'returned_object' ? 'match' : 'match');
            @endphp

            <div class="notif-item {{ $isUnread ? 'unread' : '' }}">
                <div class="notif-icon-wrap {{ $iconClass }}">{{ $icon }}</div>
                <div class="notif-content">
                    <div class="notif-title">{{ $title }}</div>
                    <div class="notif-desc">{{ $body }}</div>
                    <div class="notif-time">🕐 {{ $notification->created_at?->diffForHumans() }}</div>
                    @if($actionUrl)
                        <a href="{{ $actionUrl }}" class="card-action" style="display:inline-flex;margin-top:8px;padding-left:0;">Voir l'objet →</a>
                    @endif
                </div>
                @if($isUnread)
                    <div class="unread-dot"></div>
                @endif
            </div>
        @empty
            <div class="notif-item">
                <div class="notif-icon-wrap info">🔔</div>
                <div class="notif-content">
                    <div class="notif-title">Aucune notification</div>
                    <div class="notif-desc">Les nouvelles notifications apparaîtront ici.</div>
                </div>
            </div>
        @endforelse
    </div>
    <div style="margin-top:16px;">
        {{ $notifications->links() }}
    </div>
</div>
