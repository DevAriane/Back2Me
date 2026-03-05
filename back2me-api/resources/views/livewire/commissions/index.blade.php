<div class="page">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div class="page-title">Commissions & retraits</div>
            <div class="page-sub">Validation des retraits des trouveurs (fin de mois)</div>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="table-card" style="padding:14px 16px; margin-bottom:16px; color:#059669;">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="table-card" style="padding:14px 16px; margin-bottom:16px; color:#dc2626;">{{ session('error') }}</div>
    @endif

    <div class="stats-grid" style="grid-template-columns:repeat(3,minmax(0,1fr));">
        <div class="stat-card teal">
            <div class="stat-label">Part trouveurs à payer</div>
            <div class="stat-value" style="font-size:24px;">{{ number_format((float) $summary->total_finder_pending, 0, ',', ' ') }}</div>
            <div class="stat-change up">FCFA</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-label">Part surveillant (cumul)</div>
            <div class="stat-value" style="font-size:24px;">{{ number_format((float) $summary->total_supervisor, 0, ',', ' ') }}</div>
            <div class="stat-change up">FCFA</div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">Part application (cumul)</div>
            <div class="stat-value" style="font-size:24px;">{{ number_format((float) $summary->total_app, 0, ',', ' ') }}</div>
            <div class="stat-change up">FCFA</div>
        </div>
    </div>

    <div class="table-card">
        <div class="card-header">
            <span class="card-title">Trouveurs avec solde en attente</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Commission à retirer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($finderRows as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['email'] }}</td>
                        <td>{{ $row['phone'] }}</td>
                        <td style="font-weight:700;color:var(--navy);">{{ number_format($row['amount'], 0, ',', ' ') }} FCFA</td>
                        <td>
                            <button class="btn btn-teal" style="padding:7px 12px;font-size:12px;" wire:click="approvePayout({{ $row['finder_user_id'] }})" wire:confirm="Confirmer le retrait pour ce trouveur ?">
                                Valider retrait
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--slate);">Aucun retrait en attente.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
