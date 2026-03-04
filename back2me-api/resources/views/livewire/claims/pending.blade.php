<div>
    <h1 class="text-2xl font-bold mb-6">Signalements en attente</h1>

    @if(session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Objet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Réclamant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($claims as $claim)
                <tr>
                    <td class="px-6 py-4">
                        <a href="{{ route('objets.show', $claim->objet) }}" class="text-indigo-600 hover:underline">
                            {{ $claim->objet->name }}
                        </a>
                    </td>
                    <td class="px-6 py-4">{{ $claim->user->name }}<br><span class="text-sm text-gray-500">{{ $claim->user->email }}</span></td>
                    <td class="px-6 py-4">{{ $claim->message ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $claim->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <button wire:click="approve({{ $claim->id }})" class="text-green-600 hover:text-green-900 mr-3">Approuver</button>
                        <button wire:click="reject({{ $claim->id }})" wire:confirm="Confirmer le rejet ?" class="text-red-600 hover:text-red-900">Rejeter</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun signalement en attente.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $claims->links() }}
    </div>
</div>