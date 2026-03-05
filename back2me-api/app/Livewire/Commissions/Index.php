<?php

namespace App\Livewire\Commissions;

use App\Models\Commission;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    public function approvePayout(int $finderUserId): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $updated = app(CommissionService::class)->approveFinderPayout($finderUserId);
        if ($updated > 0) {
            session()->flash('success', 'Retrait approuvé. Le solde du trouveur est remis à zéro.');
            return;
        }

        session()->flash('error', 'Aucun solde à payer pour cet utilisateur.');
    }

    public function render()
    {
        $accruedByFinder = Commission::query()
            ->where('payout_status', 'accrued')
            ->selectRaw('finder_user_id, SUM(finder_commission) as total_finder')
            ->groupBy('finder_user_id')
            ->get();

        $finderUsers = User::query()
            ->whereIn('id', $accruedByFinder->pluck('finder_user_id'))
            ->get()
            ->keyBy('id');

        $finderRows = $accruedByFinder->map(function ($row) use ($finderUsers) {
            $user = $finderUsers->get((int) $row->finder_user_id);

            return [
                'finder_user_id' => (int) $row->finder_user_id,
                'name' => $user?->name ?? 'Utilisateur inconnu',
                'email' => $user?->email ?? '-',
                'phone' => $user?->phone ?? '-',
                'amount' => (float) $row->total_finder,
            ];
        })->sortByDesc('amount')->values();

        $summary = Commission::query()->select([
            DB::raw('COALESCE(SUM(commission_total),0) as total_commissions'),
            DB::raw('COALESCE(SUM(finder_commission),0) as total_finder'),
            DB::raw('COALESCE(SUM(supervisor_commission),0) as total_supervisor'),
            DB::raw('COALESCE(SUM(app_commission),0) as total_app'),
            DB::raw("COALESCE(SUM(CASE WHEN payout_status = 'paid' THEN finder_commission ELSE 0 END),0) as total_finder_paid"),
            DB::raw("COALESCE(SUM(CASE WHEN payout_status = 'accrued' THEN finder_commission ELSE 0 END),0) as total_finder_pending"),
        ])->first();

        return view('livewire.commissions.index', [
            'finderRows' => $finderRows,
            'summary' => $summary,
        ]);
    }
}
