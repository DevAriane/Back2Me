<div>
    <h1 class="text-2xl font-bold mb-6">Objets réclamés (dossiers à consulter)</h1>
    @if($objet_id)
        <div class="text-sm text-gray-600 mb-4">Filtre objet ID: #{{ $objet_id }}</div>
    @endif

    @if(session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Objet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Réclamant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix déclaré</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preuve</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($claims as $claim)
                @php
                    $proofFileUrl = null;
                    if (!empty($claim->proof_file_url)) {
                        $proofFileUrl = str_starts_with($claim->proof_file_url, 'http')
                            ? $claim->proof_file_url
                            : asset(ltrim($claim->proof_file_url, '/'));
                    }
                @endphp
                <tr>
                    <td class="px-6 py-4">
                        <a href="{{ route('objets.show', $claim->objet) }}" class="text-indigo-600 hover:underline">
                            {{ $claim->objet->name }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        {{ $claim->user->name }}<br>
                        <span class="text-sm text-gray-500">{{ $claim->user->email }}</span><br>
                        <span class="text-sm text-gray-500">{{ $claim->user->phone ?? '-' }}</span><br>
                        <span class="text-sm text-gray-500">{{ ($claim->user->niveau ?? '-') . ' · ' . ($claim->user->filiere ?? '-') }}</span>
                    </td>
                    <td class="px-6 py-4">{{ $claim->message ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $claim->object_price ? number_format((float) $claim->object_price, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                    <td class="px-6 py-4">
                        @if($proofFileUrl)
                            <a href="{{ $proofFileUrl }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Fichier</a>
                        @endif
                        @if($proofFileUrl && $claim->proof_link)
                            <span class="text-gray-400"> · </span>
                        @endif
                        @if($claim->proof_link)
                            <a href="{{ $claim->proof_link }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Lien</a>
                        @endif
                        @if(!$proofFileUrl && !$claim->proof_link)
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($claim->status === 'approved')
                            <span class="text-green-700 font-semibold">Approuvé</span>
                        @else
                            <span class="text-amber-700 font-semibold">En attente</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $claim->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('claims.show', $claim) }}" class="text-indigo-600 hover:underline mr-3">Consulter</a>
                        @if($claim->status === 'pending')
                            <button wire:click="approve({{ $claim->id }})" class="text-green-600 hover:text-green-900 mr-3">Approuver rapide</button>
                            <button wire:click="reject({{ $claim->id }})" wire:confirm="Confirmer le rejet ?" class="text-red-600 hover:text-red-900">Rejeter</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Aucun signalement en attente.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $claims->links() }}
    </div>
</div>
