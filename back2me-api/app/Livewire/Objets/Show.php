<?php

namespace App\Livewire\Objets;

use Livewire\Component;
use App\Models\Objet;
use App\Models\Claim;
use App\Services\ObjectFoundNotifier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithFileUploads;

class Show extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public Objet $objet;
    public $claimMessage = '';
    public $claimProofLink = '';
    public $claimProofFile = null;
    public $claimObjectPrice = null;
    public $userClaim = null;

    public function mount(Objet $objet)
    {
        $this->objet = $objet;
        $this->userClaim = Claim::where('objet_id', $objet->id)
                                 ->where('user_id', Auth::id())
                                 ->first();
    }

    public function claim()
    {
        $this->validate([
            'claimMessage' => 'nullable|string|max:500',
            'claimProofLink' => 'nullable|url|required_without:claimProofFile|max:2048',
            'claimProofFile' => 'nullable|file|required_without:claimProofLink|mimes:pdf,jpg,jpeg,png,svg|max:5120',
            'claimObjectPrice' => 'required|numeric|min:0.01|max:999999999.99',
        ]);

        if ($this->userClaim) {
            session()->flash('error', 'Vous avez déjà signalé cet objet.');
            return;
        }

        $proofFileUrl = null;
        if ($this->claimProofFile) {
            $path = $this->claimProofFile->store('claims/proofs', 'public');
            $proofFileUrl = Storage::url($path);
        }

        $this->userClaim = Claim::create([
            'objet_id' => $this->objet->id,
            'user_id' => Auth::id(),
            'message' => $this->claimMessage,
            'proof_file_url' => $proofFileUrl,
            'proof_link' => $this->claimProofLink ?: null,
            'object_price' => $this->claimObjectPrice,
            'status' => 'pending',
        ]);

        $this->reset(['claimMessage', 'claimProofLink', 'claimProofFile', 'claimObjectPrice']);
        session()->flash('success', 'Votre signalement a été envoyé.');
    }

    public function markReturned()
    {
        $this->authorize('update', $this->objet); // suppose que vous avez une policy

        if ($this->objet->status === 'returned') {
            session()->flash('error', 'Cet objet est déjà marqué comme rendu.');
            return;
        }

        $this->objet->update(['status' => 'returned']);
        app(ObjectFoundNotifier::class)->notifyAllUsersObjectReturned($this->objet);
        session()->flash('success', 'Objet marqué comme rendu.');
    }

    public function delete()
    {
        $this->authorize('delete', $this->objet);
        $this->objet->delete();
        return redirect()->route('objets.index')->with('success', 'Objet supprimé.');
    }

    public function render()
    {
        return view('livewire.objets.show');
    }
}
