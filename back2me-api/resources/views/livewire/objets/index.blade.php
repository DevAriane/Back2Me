<div class="page" wire:poll.10s>
    <div class="page-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <div class="page-title">Objets trouvés</div>
            <div class="page-sub">{{ $objets->total() }} objets enregistrés · {{ $objets->where('status', 'found')->count() }} en attente de réclamation</div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <span class="screen-label">Vue Utilisateur</span>
            <button class="btn btn-primary" type="button" onclick="openAddObjetModal()">+ Signaler un objet</button>
        </div>
    </div>

    <div class="toolbar">
        <div class="search-box">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par nom, catégorie…">
        </div>
        <select class="filter-select" wire:model.live="category_id">
            <option value="">Toutes catégories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <select class="filter-select" wire:model.live="status">
            <option value="">Tous statuts</option>
            <option value="found">Trouvé</option>
            <option value="returned">Rendu</option>
            <option value="unclaimed">Non réclamé</option>
        </select>
        <input type="date" class="filter-select" wire:model.live="date_from">
        <input type="date" class="filter-select" wire:model.live="date_to">
    </div>

    <div class="items-grid">
        @php
            $emojis = ['📱', '🎒', '🔑', '👓', '📓', '💳'];
            $bgs = ['bg1', 'bg2', 'bg3', 'bg4', 'bg5', 'bg6'];
        @endphp

        @forelse($objets as $objet)
            @php
                $index = $loop->index % 6;
                $emoji = $emojis[$index];
                $bg = $bgs[$index];
                $photoUrl = null;
                if (!empty($objet->photo_url)) {
                    $photoUrl = str_starts_with($objet->photo_url, 'http')
                        ? $objet->photo_url
                        : asset(ltrim($objet->photo_url, '/'));
                }
            @endphp
            <a class="item-card" href="{{ route('objets.show', $objet) }}">
                <div class="item-img {{ $photoUrl ? 'has-photo' : $bg }}">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="Photo de {{ $objet->name }}" loading="lazy">
                    @else
                        {{ $emoji }}
                    @endif
                    <div class="item-badge-pos">
                        @if($objet->status === 'returned')
                            <span class="badge returned"><span class="badge-dot"></span>Rendu</span>
                        @elseif($objet->status === 'unclaimed')
                            <span class="badge unclaimed"><span class="badge-dot"></span>Non réclamé</span>
                        @else
                            <span class="badge found"><span class="badge-dot"></span>Trouvé</span>
                        @endif
                    </div>
                </div>
                <div class="item-body">
                    <div class="item-title">{{ $objet->name }}</div>
                    <div class="item-meta">
                        <span>📂 {{ $objet->category?->name ?? '-' }}</span>
                        <span>📍 {{ $objet->location }}</span>
                        <span>📅 {{ optional($objet->found_date)->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
                <div class="item-footer">
                    <span style="font-size:12px;color:var(--slate);">ID: #{{ str_pad($objet->id, 4, '0', STR_PAD_LEFT) }}</span>
                    @if($objet->status === 'returned')
                        <span class="claim-btn" style="opacity:.4;cursor:not-allowed;">Rendu</span>
                    @else
                        <span class="claim-btn">C'est le mien</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="table-card" style="padding:20px;grid-column:1/-1;">Aucun objet trouvé.</div>
        @endforelse
    </div>

    <div style="margin-top:20px;">
        {{ $objets->links() }}
    </div>
</div>
