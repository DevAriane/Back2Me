<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Commission;

class CommissionService
{
    public const TOTAL_RATE = 0.25; // 25% du prix
    public const PRICE_THRESHOLD = 20000.0; // seuil de plafond
    public const FIXED_COMMISSION_AT_THRESHOLD = 5000.0; // commission fixe si prix >= seuil
    public const FINDER_SHARE_OF_TOTAL = 0.50; // 50% des 25%
    public const SUPERVISOR_SHARE_OF_REMAINING = 0.50; // 50% de la moitié restante

    /**
     * @return array{commission_total: float,finder_commission: float,supervisor_commission: float,app_commission: float}
     */
    public function calculate(float $objectPrice): array
    {
        $commissionTotal = $objectPrice >= self::PRICE_THRESHOLD
            ? self::FIXED_COMMISSION_AT_THRESHOLD
            : round($objectPrice * self::TOTAL_RATE, 2);
        $finder = round($commissionTotal * self::FINDER_SHARE_OF_TOTAL, 2);
        $remaining = round($commissionTotal - $finder, 2);
        $supervisor = round($remaining * self::SUPERVISOR_SHARE_OF_REMAINING, 2);
        $app = round($remaining - $supervisor, 2);

        return [
            'commission_total' => $commissionTotal,
            'finder_commission' => $finder,
            'supervisor_commission' => $supervisor,
            'app_commission' => $app,
        ];
    }

    public function recordFromApprovedClaim(Claim $claim, int $approvedByUserId): Commission
    {
        $claim->loadMissing(['objet', 'user']);

        $price = (float) $claim->object_price;
        $amounts = $this->calculate($price);

        return Commission::updateOrCreate(
            ['claim_id' => $claim->id],
            [
                'objet_id' => $claim->objet_id,
                'finder_user_id' => $claim->objet->user_id,
                'claimer_user_id' => $claim->user_id,
                'approved_by_user_id' => $approvedByUserId,
                'object_price' => $price,
                'commission_total' => $amounts['commission_total'],
                'finder_commission' => $amounts['finder_commission'],
                'supervisor_commission' => $amounts['supervisor_commission'],
                'app_commission' => $amounts['app_commission'],
                'payout_status' => 'accrued',
                'paid_out_at' => null,
            ]
        );
    }

    public function approveFinderPayout(int $finderUserId): int
    {
        return Commission::query()
            ->where('finder_user_id', $finderUserId)
            ->where('payout_status', 'accrued')
            ->update([
                'payout_status' => 'paid',
                'paid_out_at' => now(),
            ]);
    }
}
