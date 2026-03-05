<?php

namespace App\Livewire\Claims;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Claim;
use App\Services\CommissionService;
use App\Services\FirebaseNotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Pending extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public $objet_id = null;

    protected $queryString = [
        'objet_id' => ['except' => null],
    ];

    public function approve(Claim $claim)
    {
        $this->authorize('approve', $claim);

        if (!$claim->proof_file_url && !$claim->proof_link) {
            session()->flash('error', 'Impossible d\'approuver: aucune preuve fournie.');
            return;
        }
        if (!$claim->object_price || (float) $claim->object_price <= 0) {
            session()->flash('error', 'Impossible d\'approuver: prix de l\'objet manquant.');
            return;
        }

        $claim->update(['status' => 'approved']);
        $commission = app(CommissionService::class)->recordFromApprovedClaim($claim, auth()->id());

        // Notifier l'utilisateur (si token FCM)
        if ($claim->user->fcm_token) {
            $firebase = new FirebaseNotificationService();
            $firebase->sendToUser(
                $claim->user->fcm_token,
                'Signalement approuvé',
                "Votre réclamation pour l'objet {$claim->objet->name} a été approuvée.",
                ['claim_id' => $claim->id]
            );
        }

        session()->flash(
            'success',
            'Signalement approuvé. Commission trouveur: ' . number_format((float) $commission->finder_commission, 0, ',', ' ') . ' FCFA.'
        );
    }

    public function reject(Claim $claim)
    {
        $this->authorize('reject', $claim);
        $claim->update(['status' => 'rejected']);

        // Notifier l'utilisateur
        if ($claim->user->fcm_token) {
            $firebase = new FirebaseNotificationService();
            $firebase->sendToUser(
                $claim->user->fcm_token,
                'Signalement rejeté',
                "Votre réclamation pour l'objet {$claim->objet->name} a été rejetée.",
                ['claim_id' => $claim->id]
            );
        }

        session()->flash('success', 'Signalement rejeté.');
    }

    public function render()
    {
        $claims = Claim::with(['objet', 'user'])
                       ->whereIn('status', ['pending', 'approved'])
                       ->whereHas('objet', function ($query) {
                           $query->where('status', '!=', 'returned');
                       })
                       ->when($this->objet_id, function ($query) {
                           $query->where('objet_id', $this->objet_id);
                       })
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('livewire.claims.pending', compact('claims'));
    }
}
