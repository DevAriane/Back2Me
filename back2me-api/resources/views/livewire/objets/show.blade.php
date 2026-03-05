<div class="page">
    @php
        $photoUrl = null;
        if (!empty($objet->photo_url)) {
            $photoUrl = str_starts_with($objet->photo_url, 'http')
                ? $objet->photo_url
                : asset(ltrim($objet->photo_url, '/'));
        }

        $claimProofFileUrl = null;
        if (!empty($userClaim?->proof_file_url)) {
            $claimProofFileUrl = str_starts_with($userClaim->proof_file_url, 'http')
                ? $userClaim->proof_file_url
                : asset(ltrim($userClaim->proof_file_url, '/'));
        }
    @endphp

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
            <div class="detail-hero {{ $photoUrl ? 'has-photo' : '' }}">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="Photo de {{ $objet->name }}" loading="lazy">
                @else
                    📦
                @endif
            </div>
            @if($photoUrl)
                <div style="display:flex;justify-content:flex-end;margin-top:-10px;">
                    <a class="btn btn-outline" href="{{ $photoUrl }}" target="_blank" rel="noopener" style="padding:8px 12px;">
                        Voir en pleine resolution
                    </a>
                </div>
            @endif
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
                    <div><div class="info-label">Trouvé par</div><div class="info-value">{{ $objet->user?->name ?? '-' }}</div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📞</div>
                    <div><div class="info-label">Téléphone du déposant</div><div class="info-value">{{ $objet->user?->phone ?: '-' }}</div></div>
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
                    <p style="font-size:13px;color:var(--slate);margin-bottom:14px;line-height:1.6;">Si cet objet vous appartient, joignez une preuve (facture/document ou lien). Le surveillant général validera votre demande.</p>
                    @if($userClaim)
                        <button class="btn btn-outline" style="width:100%;justify-content:center;padding:13px;">
                            Demande déjà envoyée ({{ strtoupper($userClaim->status) }})
                        </button>
                        @if($claimProofFileUrl || $userClaim->proof_link)
                            <div style="margin-top:10px;font-size:12.5px;color:var(--slate);">
                                Preuve fournie :
                                @if($claimProofFileUrl)
                                    <a href="{{ $claimProofFileUrl }}" target="_blank" rel="noopener" class="card-action" style="padding:0 6px 0 0;">Fichier</a>
                                @endif
                                @if($userClaim->proof_link)
                                    <a href="{{ $userClaim->proof_link }}" target="_blank" rel="noopener" class="card-action" style="padding:0;">Lien</a>
                                @endif
                            </div>
                        @endif
                    @else
                        <form wire:submit="claim" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="form-label">Message (optionnel)</label>
                                <textarea class="form-textarea" wire:model="claimMessage" placeholder="Ajoutez une précision utile..."></textarea>
                                @error('claimMessage')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Prix de l'objet (FCFA)</label>
                                <input class="form-input" type="number" wire:model="claimObjectPrice" min="0" step="0.01" placeholder="Ex: 45000">
                                @error('claimObjectPrice')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Facture / document (PDF, JPG, PNG, SVG)</label>
                                <input class="form-input" type="file" wire:model="claimProofFile" accept=".pdf,.jpg,.jpeg,.png,.svg">
                                @error('claimProofFile')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ou lien de preuve</label>
                                <input class="form-input" type="url" wire:model="claimProofLink" placeholder="https://...">
                                @error('claimProofLink')<span class="text-danger">{{ $message }}</span>@enderror
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
