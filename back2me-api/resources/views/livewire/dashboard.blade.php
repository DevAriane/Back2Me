<div class="page">
    <div class="page-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <div class="page-title">Tableau de bord</div>
            <div class="page-sub">Bienvenue, {{ auth()->user()->name }} · {{ now()->translatedFormat('l d M Y') }}</div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <span class="screen-label">Vue Admin</span>
            <button class="btn btn-primary" type="button" onclick="openAddObjetModal()">+ Ajouter un objet</button>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">📦</div>
            <div class="stat-value">{{ $totalObjets }}</div>
            <div class="stat-label">Objets enregistrés</div>
            <div class="stat-change up">Suivi en temps réel</div>
        </div>
        <div class="stat-card teal">
            <div class="stat-icon">✅</div>
            <div class="stat-value">{{ $objetsRendus }}</div>
            <div class="stat-label">Objets rendus</div>
            <div class="stat-change up">Restitutions validées</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-icon">⏳</div>
            <div class="stat-value">{{ $objetsEnAttente }}</div>
            <div class="stat-label">En attente</div>
            <div class="stat-change down">À traiter</div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon">📢</div>
            <div class="stat-value">{{ $objetsNonReclames }}</div>
            <div class="stat-label">Non réclamés</div>
            <div class="stat-change up">Relances possibles</div>
        </div>
    </div>

    <div class="dash-grid">
        <div class="table-card">
            <div class="card-header">
                <span class="card-title">Derniers objets enregistrés</span>
                <a class="card-action" href="{{ route('objets.index') }}">Voir tout →</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Objet</th>
                        <th>Catégorie</th>
                        <th>Date trouvé</th>
                        <th>Lieu</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestObjets as $objet)
                    @php
                        $photoUrl = null;
                        if (!empty($objet->photo_url)) {
                            $photoUrl = str_starts_with($objet->photo_url, 'http')
                                ? $objet->photo_url
                                : asset(ltrim($objet->photo_url, '/'));
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="obj-name">
                                <div class="obj-emoji">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="Photo de {{ $objet->name }}" loading="lazy">
                                    @else
                                        📦
                                    @endif
                                </div>
                                {{ $objet->name }}
                            </div>
                        </td>
                        <td>{{ $objet->category?->name ?? '-' }}</td>
                        <td>{{ optional($objet->found_date)->format('d/m/Y') }}</td>
                        <td>{{ $objet->location }}</td>
                        <td>
                            @if($objet->status === 'returned')
                                <span class="badge returned"><span class="badge-dot"></span>Rendu</span>
                            @elseif($objet->status === 'unclaimed')
                                <span class="badge unclaimed"><span class="badge-dot"></span>Non réclamé</span>
                            @else
                                <span class="badge found"><span class="badge-dot"></span>Trouvé</span>
                            @endif
                        </td>
                        <td><a href="{{ route('objets.show', $objet) }}" class="claim-btn">Gérer</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">Aucun objet enregistré.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="right-col">
            <div class="chart-card">
                <div class="card-header">
                    <span class="card-title">Activité mensuelle</span>
                    <span style="font-size:12px;color:var(--slate);">{{ now()->year }}</span>
                </div>
                <div class="chart-bars">
                    @php
                        $bars = [35, 45, 38, 52, 60, 74];
                        $months = ['Sep', 'Oct', 'Nov', 'Déc', 'Jan', 'Fév'];
                    @endphp
                    @foreach($bars as $idx => $height)
                        <div class="bar-wrap">
                            <div class="bar" style="height:{{ $height }}%;background:{{ $idx === 5 ? 'linear-gradient(180deg,var(--teal) 0%,var(--teal-2) 100%)' : 'var(--navy)' }};"></div>
                            <div class="bar-label">{{ $months[$idx] }}</div>
                        </div>
                    @endforeach
                </div>
                <div style="padding:0 20px 16px;display:flex;gap:16px;">
                    <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--slate);"><span style="width:10px;height:10px;background:var(--navy);border-radius:3px;display:inline-block;"></span>Précédents</div>
                    <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--slate);"><span style="width:10px;height:10px;background:var(--teal);border-radius:3px;display:inline-block;"></span>Ce mois</div>
                </div>
            </div>

            <div class="activity-card">
                <div class="card-header"><span class="card-title">Activité récente</span></div>
                <div class="activity-list">
                    @forelse($latestObjets as $index => $objet)
                    <div class="activity-item">
                        <div class="activity-dot {{ $index % 3 === 0 ? 'teal' : ($index % 3 === 1 ? 'green' : 'amber') }}"></div>
                        <div>
                            <div class="activity-text">Nouvel objet ajouté : <strong>{{ $objet->name }}</strong></div>
                            <div class="activity-time">{{ $objet->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="activity-item">
                        <div class="activity-dot teal"></div>
                        <div>
                            <div class="activity-text">Aucune activité récente.</div>
                            <div class="activity-time">-</div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
