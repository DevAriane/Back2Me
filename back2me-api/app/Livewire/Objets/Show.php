<?php

namespace App\Livewire\Objets;

use Livewire\Component;
use App\Models\Objet;
use App\Models\Claim;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests;

    public Objet $objet;
    public $claimMessage = '';
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
        $this->validate(['claimMessage' => 'nullable|string|max:500']);

        if ($this->userClaim) {
            session()->flash('error', 'Vous avez déjà signalé cet objet.');
            return;
        }

        Claim::create([
            'objet_id' => $this->objet->id,
            'user_id' => Auth::id(),
            'message' => $this->claimMessage,
            'status' => 'pending',
        ]);

        $this->userClaim = true;
        session()->flash('success', 'Votre signalement a été envoyé.');
    }

    public function markReturned()
    {
        $this->authorize('update', $this->objet); // suppose que vous avez une policy
        $this->objet->update(['status' => 'returned']);
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
