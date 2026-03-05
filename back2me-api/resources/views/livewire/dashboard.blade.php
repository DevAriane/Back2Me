<div class="page">
    <div class="page-header" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <div class="page-title">Tableau de bord</div>
            <div class="page-sub">Bienvenue, {{ auth()->user()->name }} · {{ now()->translatedFormat('l d M Y') }}</div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <span class="screen-label">{{ auth()->user()->role === 'admin' ? 'Vue Admin' : 'Vue Utilisateur' }}</span>
            <button class="btn btn-primary" type="button" onclick="openAddObjetModal()">+ Ajouter un objet</button>
        </div>
    </div>

    @if(auth()->user()->role !== 'admin')
        <div class="table-card" style="padding:18px 20px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div class="card-title">Ma commission à retirer</div>
                <div style="font-size:13px;color:var(--slate);margin-top:4px;">Retrait auprès du surveillant général</div>
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:800;color:var(--teal);">
                {{ number_format((float) $myPendingCommission, 0, ',', ' ') }} FCFA
            </div>
        </div>
    @endif

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
                    <span class="card-title">Courbe réelle des objets trouvés / mois</span>
                    <span style="font-size:12px;color:var(--slate);">{{ now()->year }}</span>
                </div>
                <div style="padding:16px 16px 8px;">
                    @php
                        $count = max(1, count($monthlyActivity));
                        $points = collect($monthlyActivity)->map(function ($row, $i) use ($count, $monthlyMax) {
                            $x = $count === 1 ? 190 : (10 + (($i * 360) / ($count - 1)));
                            $ratio = $monthlyMax > 0 ? ($row['value'] / $monthlyMax) : 0;
                            $y = 150 - ($ratio * 130);
                            return ['x' => round($x, 2), 'y' => round($y, 2), 'label' => $row['label'], 'value' => $row['value']];
                        })->values();
                        $polyline = $points->map(fn($p) => $p['x'].','.$p['y'])->implode(' ');
                    @endphp
                    <svg viewBox="0 0 380 170" width="100%" height="170" aria-label="Courbe activité mensuelle">
                        <line x1="10" y1="150" x2="370" y2="150" stroke="#CBD5E1" stroke-width="1" />
                        <polyline points="{{ $polyline }}" fill="none" stroke="#0FC6C2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        @foreach($points as $p)
                            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4.5" fill="#132053"></circle>
                        @endforeach
                    </svg>
                </div>
                <div style="padding:0 20px 16px;display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:6px;">
                    @foreach($monthlyActivity as $row)
                        <div style="text-align:center;">
                            <div style="font-size:12px;font-weight:700;color:var(--navy);">{{ $row['value'] }}</div>
                            <div style="font-size:11px;color:var(--slate);">{{ $row['label'] }}</div>
                        </div>
                    @endforeach
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
