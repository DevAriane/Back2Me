<div class="page">
    @if(session()->has('success'))
        <div class="table-card" style="padding:14px 16px; margin-bottom:16px; color:#059669;">{{ session('success') }}</div>
    @endif

    @if(session()->has('error'))
        <div class="table-card" style="padding:14px 16px; margin-bottom:16px; color:#dc2626;">{{ session('error') }}</div>
    @endif

    <div class="page-header" style="display:flex;align-items:center;gap:14px;">
        <a class="btn btn-outline" style="padding:8px 14px;" href="{{ route('objets.index') }}">← Retour</a>
        <div>
            <div class="page-title">Détail de l'objet #{{ str_pad($objet->id, 4, '0', STR_PAD_LEFT) }}</div>
            <div class="page-sub">Enregistré le {{ optional($objet->found_date)->translatedFormat('d F Y') }} · {{ $objet->location }}</div>
        </div>
        @if($objet->status === 'returned')
            <span class="badge returned" style="margin-left:auto;padding:8px 16px;font-size:13px;"><span class="badge-dot"></span>Rendu</span>
        @elseif($objet->status === 'unclaimed')
            <span class="badge unclaimed" style="margin-left:auto;padding:8px 16px;font-size:13px;"><span class="badge-dot"></span>Non réclamé</span>
        @else
            <span class="badge found" style="margin-left:auto;padding:8px 16px;font-size:13px;"><span class="badge-dot"></span>Trouvé</span>
        @endif
    </div>

    <div class="detail-grid">
        <div class="detail-main">
            <div class="detail-hero">📦</div>
            <div class="info-card">
                <h3>{{ $objet->name }}</h3>
                <div class="info-row">
                    <div class="info-icon">📂</div>
                    <div><div class="info-label">Catégorie</div><div class="info-value">{{ $objet->category?->name ?? '-' }}</div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📍</div>
                    <div><div class="info-label">Lieu de découverte</div><div class="info-value">{{ $objet->location }}</div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📅</div>
                    <div><div class="info-label">Date de découverte</div><div class="info-value">{{ optional($objet->found_date)->translatedFormat('d F Y') }}</div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon">👤</div>
                    <div><div class="info-label">Déposé par</div><div class="info-value">{{ $objet->user?->name ?? '-' }}</div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📝</div>
                    <div><div class="info-label">Description</div><div class="info-value">{{ $objet->description ?: 'Aucune description disponible.' }}</div></div>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            @can('update', $objet)
            <div class="action-card">
                <h4>🛠 Actions administrateur</h4>
                <div class="action-btn-group">
                    @if($objet->status !== 'returned')
                        <button class="action-btn-item success" wire:click="markReturned">✅ Marquer comme rendu</button>
                    @endif
                    <button class="action-btn-item danger" wire:click="delete" wire:confirm="Êtes-vous sûr de vouloir supprimer cet objet ?">🗑 Supprimer l'objet</button>
                </div>
            </div>
            @endcan

            @if(!auth()->user()->can('update', $objet) && $objet->status !== 'returned')
                <div class="action-card">
                    <h4>👋 Vous reconnaissez cet objet ?</h4>
                    <p style="font-size:13px;color:var(--slate);margin-bottom:14px;line-height:1.6;">Si cet objet vous appartient, signalez-le. Le surveillant général vous contactera pour validation.</p>
                    @if($userClaim)
                        <button class="btn btn-outline" style="width:100%;justify-content:center;padding:13px;">Signalement déjà envoyé</button>
                    @else
                        <form wire:submit="claim">
                            <div class="form-group">
                                <label class="form-label">Message (optionnel)</label>
                                <textarea class="form-textarea" wire:model="claimMessage" placeholder="Ajoutez une précision utile..."></textarea>
                                @error('claimMessage')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <button class="btn btn-teal" style="width:100%;justify-content:center;padding:13px;">🙋 C'est mon objet !</button>
                        </form>
                    @endif
                </div>
            @endif

            <div class="action-card">
                <h4>📋 Historique</h4>
                <div style="display:flex;flex-direction:column;gap:12px;margin-top:4px;">
                    <div style="display:flex;gap:12px;align-items:flex-start;">
                        <div style="width:10px;height:10px;background:var(--teal);border-radius:50%;margin-top:4px;flex-shrink:0;"></div>
                        <div><div style="font-size:13px;font-weight:600;color:var(--navy);">Objet enregistré</div><div style="font-size:11.5px;color:var(--slate);">{{ $objet->created_at->format('d/m/Y · H\hi') }}</div></div>
                    </div>
                    <div style="display:flex;gap:12px;align-items:flex-start;opacity:{{ $objet->status === 'returned' ? '1' : '.4' }};">
                        <div style="width:10px;height:10px;background:var(--green);border-radius:50%;margin-top:4px;flex-shrink:0;{{ $objet->status !== 'returned' ? 'border:2px dashed var(--green);background:transparent;' : '' }}"></div>
                        <div><div style="font-size:13px;font-weight:600;color:var(--navy);">Restitution</div><div style="font-size:11.5px;color:var(--slate);">{{ $objet->status === 'returned' ? 'Effectuée' : 'En attente…' }}</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
