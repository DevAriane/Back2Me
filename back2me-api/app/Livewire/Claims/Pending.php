<?php

namespace App\Livewire\Claims;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Claim;
use App\Models\Objet;
use App\Services\FirebaseNotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Pending extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public function approve(Claim $claim)
    {
        $this->authorize('approve', $claim);
        $claim->update(['status' => 'approved']);

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

        session()->flash('success', 'Signalement approuvé.');
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
                       ->where('status', 'pending')
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('livewire.claims.pending', compact('claims'));
    }
}
