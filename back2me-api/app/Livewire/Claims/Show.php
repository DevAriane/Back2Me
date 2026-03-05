<?php

namespace App\Livewire\Claims;

use App\Models\Claim;
use App\Services\CommissionService;
use App\Services\FirebaseNotificationService;
use App\Services\ObjectFoundNotifier;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Show extends Component
{
    use AuthorizesRequests;

    public Claim $claim;

    public function mount(Claim $claim): void
    {
        $this->authorize('view', $claim);
        $this->claim = $claim->load(['objet.user', 'user', 'commission']);
    }

    public function approve(): void
    {
        $this->authorize('approve', $this->claim);

        if ($this->claim->status !== 'pending') {
            session()->flash('error', 'Cette réclamation a déjà été traitée.');
            return;
        }
        if (!$this->claim->proof_file_url && !$this->claim->proof_link) {
            session()->flash('error', 'Aucune preuve fournie.');
            return;
        }
        if (!$this->claim->object_price || (float) $this->claim->object_price <= 0) {
            session()->flash('error', 'Prix de l\'objet manquant.');
            return;
        }

        $this->claim->update(['status' => 'approved']);
        $commission = app(CommissionService::class)->recordFromApprovedClaim($this->claim, auth()->id());
        $this->claim->refresh()->load(['objet.user', 'user', 'commission']);

        if ($this->claim->user->fcm_token) {
            $firebase = new FirebaseNotificationService();
            $firebase->sendToUser(
                $this->claim->user->fcm_token,
                'Reclamation approuvee',
                "Votre reclamation pour l'objet {$this->claim->objet->name} a ete approuvee.",
                ['claim_id' => $this->claim->id]
            );
        }

        session()->flash(
            'success',
            'Réclamation approuvée. Commission trouveur: ' . number_format((float) $commission->finder_commission, 0, ',', ' ') . ' FCFA.'
        );
    }

    public function reject(): void
    {
        $this->authorize('reject', $this->claim);

        if ($this->claim->status !== 'pending') {
            session()->flash('error', 'Cette réclamation a déjà été traitée.');
            return;
        }

        $this->claim->update(['status' => 'rejected']);
        $this->claim->refresh()->load(['objet.user', 'user', 'commission']);

        if ($this->claim->user->fcm_token) {
            $firebase = new FirebaseNotificationService();
            $firebase->sendToUser(
                $this->claim->user->fcm_token,
                'Reclamation rejetee',
                "Votre reclamation pour l'objet {$this->claim->objet->name} a ete rejetee.",
                ['claim_id' => $this->claim->id]
            );
        }

        session()->flash('success', 'Réclamation rejetée.');
    }

    public function markReturned()
    {
        $this->authorize('update', $this->claim->objet);

        if ($this->claim->status !== 'approved') {
            session()->flash('error', 'Il faut d\'abord approuver la réclamation.');
            return;
        }
        if ($this->claim->objet->status === 'returned') {
            session()->flash('error', 'Objet déjà marqué comme rendu.');
            return;
        }

        $this->claim->objet->update(['status' => 'returned']);
        app(ObjectFoundNotifier::class)->notifyAllUsersObjectReturned(
            $this->claim->objet,
            $this->claim->user?->name
        );
        return redirect()->route('commissions.index')
            ->with('success', 'Objet rendu validé. Redirection vers les commissions.');
    }

    public function render()
    {
        $proofFileUrl = null;
        if (!empty($this->claim->proof_file_url)) {
            $proofFileUrl = str_starts_with($this->claim->proof_file_url, 'http')
                ? $this->claim->proof_file_url
                : asset(ltrim($this->claim->proof_file_url, '/'));
        }

        $expected = null;
        if ($this->claim->object_price) {
            $expected = app(CommissionService::class)->calculate((float) $this->claim->object_price);
        }

        return view('livewire.claims.show', [
            'proofFileUrl' => $proofFileUrl,
            'expectedCommission' => $expected,
        ]);
    }
}
