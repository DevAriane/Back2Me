<div class="page">
    @if(session()->has('success'))
        <div class="table-card" style="padding:14px 16px; margin-bottom:16px; color:#059669;">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="table-card" style="padding:14px 16px; margin-bottom:16px; color:#dc2626;">{{ session('error') }}</div>
    @endif

    <div class="page-header" style="display:flex;align-items:center;gap:14px;">
        <a class="btn btn-outline" style="padding:8px 14px;" href="{{ route('claims.pending') }}">← Retour objets réclamés</a>
        <div>
            <div class="page-title">Dossier réclamation #{{ $claim->id }}</div>
            <div class="page-sub">{{ $claim->user?->name }} · {{ $claim->created_at?->format('d/m/Y H:i') }}</div>
        </div>
        <span class="badge {{ $claim->status === 'approved' ? 'returned' : ($claim->status === 'rejected' ? 'unclaimed' : 'found') }}" style="margin-left:auto;padding:8px 16px;font-size:13px;">
            <span class="badge-dot"></span>{{ strtoupper($claim->status) }}
        </span>
    </div>

    <div class="detail-grid">
        <div class="detail-main">
            <div class="info-card">
                <h3>Objet concerné</h3>
                <div class="info-row"><div class="info-icon">📦</div><div><div class="info-label">Nom</div><div class="info-value">{{ $claim->objet?->name }}</div></div></div>
                <div class="info-row"><div class="info-icon">📍</div><div><div class="info-label">Lieu trouvé</div><div class="info-value">{{ $claim->objet?->location }}</div></div></div>
                <div class="info-row"><div class="info-icon">👤</div><div><div class="info-label">Trouvé par</div><div class="info-value">{{ $claim->objet?->user?->name ?? '-' }}</div></div></div>
                <div class="info-row"><div class="info-icon">📞</div><div><div class="info-label">Téléphone trouveur</div><div class="info-value">{{ $claim->objet?->user?->phone ?? '-' }}</div></div></div>
            </div>

            <div class="info-card">
                <h3>Réclamant & preuve</h3>
                <div class="info-row"><div class="info-icon">🙋</div><div><div class="info-label">Réclamant</div><div class="info-value">{{ $claim->user?->name }} ({{ $claim->user?->email }})</div></div></div>
                <div class="info-row"><div class="info-icon">📞</div><div><div class="info-label">Téléphone</div><div class="info-value">{{ $claim->user?->phone ?? '-' }}</div></div></div>
                <div class="info-row"><div class="info-icon">🎓</div><div><div class="info-label">Niveau</div><div class="info-value">{{ $claim->user?->niveau ?? '-' }}</div></div></div>
                <div class="info-row"><div class="info-icon">🏫</div><div><div class="info-label">Filière</div><div class="info-value">{{ $claim->user?->filiere ?? '-' }}</div></div></div>
                <div class="info-row"><div class="info-icon">📝</div><div><div class="info-label">Message</div><div class="info-value">{{ $claim->message ?: '—' }}</div></div></div>
                <div class="info-row"><div class="info-icon">💵</div><div><div class="info-label">Prix déclaré</div><div class="info-value">{{ $claim->object_price ? number_format((float)$claim->object_price, 0, ',', ' ') . ' FCFA' : '—' }}</div></div></div>
                <div class="info-row">
                    <div class="info-icon">📄</div>
                    <div>
                        <div class="info-label">Facture / document</div>
                        <div class="info-value">
                            @if($proofFileUrl)
                                <a class="card-action" style="padding:0;" href="{{ $proofFileUrl }}" target="_blank" rel="noopener">Ouvrir fichier</a>
                            @endif
                            @if($proofFileUrl && $claim->proof_link)
                                <span> · </span>
                            @endif
                            @if($claim->proof_link)
                                <a class="card-action" style="padding:0;" href="{{ $claim->proof_link }}" target="_blank" rel="noopener">Ouvrir lien</a>
                            @endif
                            @if(!$proofFileUrl && !$claim->proof_link)
                                —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="action-card">
                <h4>Validation surveillant</h4>
                <div class="action-btn-group">
                    @if($claim->status === 'pending')
                        <button class="action-btn-item success" wire:click="approve">✅ Approuver propriété</button>
                        <button class="action-btn-item danger" wire:click="reject">❌ Rejeter</button>
                    @else
                        <button class="btn btn-outline" style="width:100%;justify-content:center;">Réclamation déjà traitée</button>
                    @endif
                </div>
            </div>

            <div class="action-card">
                <h4>Restitution objet</h4>
                @if($claim->status === 'approved' && $claim->objet?->status !== 'returned')
                    <button class="action-btn-item primary" wire:click="markReturned">🔐 Valider objet rendu</button>
                @elseif($claim->objet?->status === 'returned')
                    <button class="btn btn-outline" style="width:100%;justify-content:center;">Objet déjà rendu</button>
                @else
                    <button class="btn btn-outline" style="width:100%;justify-content:center;">Approuve d'abord la propriété</button>
                @endif
            </div>

            <div class="action-card">
                <h4>Simulation commission (25%)</h4>
                @if($expectedCommission)
                    <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;color:var(--navy);">
                        <div>Total commission: <strong>{{ number_format($expectedCommission['commission_total'], 0, ',', ' ') }} FCFA</strong></div>
                        <div>Part trouveur: <strong>{{ number_format($expectedCommission['finder_commission'], 0, ',', ' ') }} FCFA</strong></div>
                        <div>Part surveillant: <strong>{{ number_format($expectedCommission['supervisor_commission'], 0, ',', ' ') }} FCFA</strong></div>
                        <div>Part application: <strong>{{ number_format($expectedCommission['app_commission'], 0, ',', ' ') }} FCFA</strong></div>
                    </div>
                @else
                    <div style="font-size:13px;color:var(--slate);">Prix non renseigné.</div>
                @endif
            </div>
        </div>
    </div>
</div>
