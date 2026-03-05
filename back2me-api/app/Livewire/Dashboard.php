<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Objet;
use App\Models\Category;
use App\Models\Commission;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $totalObjets;
    public $objetsRendus;
    public $objetsNonReclames;
    public $objetsEnAttente;
    public $statsParCategorie = [];
    public $evolutionHebdo = [];
    public $monthlyActivity = [];
    public $monthlyMax = 1;
    public $myPendingCommission = 0;
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

        $this->loadMonthlyActivity();
        $this->myPendingCommission = (float) Commission::query()
            ->where('finder_user_id', auth()->id())
            ->where('payout_status', 'accrued')
            ->sum('finder_commission');
    }

    private function loadMonthlyActivity(): void
    {
        $start = now()->startOfMonth()->subMonths(5);
        $raw = Objet::query()
            ->selectRaw("DATE_FORMAT(found_date, '%Y-%m') as ym, COUNT(*) as total")
            ->where('found_date', '>=', $start->toDateString())
            ->groupBy('ym')
            ->pluck('total', 'ym')
            ->toArray();

        $series = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->startOfMonth()->subMonths($i);
            $key = $date->format('Y-m');
            $series[] = [
                'label' => $date->translatedFormat('M'),
                'value' => (int) ($raw[$key] ?? 0),
            ];
        }

        $this->monthlyActivity = $series;
        $this->monthlyMax = max(1, collect($series)->max('value'));
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
