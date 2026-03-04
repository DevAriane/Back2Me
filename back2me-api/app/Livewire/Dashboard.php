<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Objet;
use App\Models\Category;
use App\Models\Claim;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $totalObjets;
    public $objetsRendus;
    public $objetsNonReclames;
    public $objetsEnAttente;
    public $statsParCategorie = [];
    public $evolutionHebdo = [];
    public $latestObjets = [];

    public function mount()
    {
        $this->totalObjets = Objet::count();
        $this->objetsRendus = Objet::where('status', 'returned')->count();
        $this->objetsNonReclames = Objet::where('status', 'unclaimed')->count();
        $this->objetsEnAttente = Objet::where('status', 'found')->count();

        $this->statsParCategorie = Category::withCount('objets')->get();

        // Évolution des 7 derniers jours
        $this->evolutionHebdo = Objet::select(DB::raw('DATE(found_date) as date'), DB::raw('count(*) as total'))
            ->where('found_date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->latestObjets = Objet::with('category')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
