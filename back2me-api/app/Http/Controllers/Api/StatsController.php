<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Objet;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    /**
     * Statistiques globales pour le dashboard admin
     */
    public function index(Request $request)
    {
        $stats = [
            'total_objets' => Objet::count(),
            'objets_rendus' => Objet::where('status', 'returned')->count(),
            'objets_non_rendus' => Objet::whereIn('status', ['found', 'unclaimed'])->count(),
            'objets_non_reclames' => Objet::where('status', 'unclaimed')->count(),
            'objets_trouves_ce_mois' => Objet::whereMonth('found_date', now()->month)
                                              ->whereYear('found_date', now()->year)
                                              ->count(),
            'objets_rendus_ce_mois' => Objet::where('status', 'returned')
                                            ->whereMonth('updated_at', now()->month)
                                            ->whereYear('updated_at', now()->year)
                                            ->count(),
            'total_utilisateurs' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'par_categorie' => Category::withCount('objets')->get()->map(function ($cat) {
                return [
                    'categorie' => $cat->name,
                    'total' => $cat->objets_count,
                ];
            }),
            'evolution_mensuelle' => $this->getMonthlyEvolution(),
        ];

        return response()->json($stats);
    }

    /**
     * Évolution mensuelle des objets trouvés/rendus (12 derniers mois)
     */
    private function getMonthlyEvolution()
    {
        $months = collect(range(0, 11))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'mois' => $date->format('Y-m'),
                'trouves' => Objet::whereYear('found_date', $date->year)
                                  ->whereMonth('found_date', $date->month)
                                  ->count(),
                'rendus' => Objet::where('status', 'returned')
                                 ->whereYear('updated_at', $date->year)
                                 ->whereMonth('updated_at', $date->month)
                                 ->count(),
            ];
        })->reverse()->values();

        return $months;
    }
}