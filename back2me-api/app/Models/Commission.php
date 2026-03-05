<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'claim_id',
        'objet_id',
        'finder_user_id',
        'claimer_user_id',
        'approved_by_user_id',
        'object_price',
        'commission_total',
        'finder_commission',
        'supervisor_commission',
        'app_commission',
        'payout_status',
        'paid_out_at',
    ];

    protected $casts = [
        'object_price' => 'decimal:2',
        'commission_total' => 'decimal:2',
        'finder_commission' => 'decimal:2',
        'supervisor_commission' => 'decimal:2',
        'app_commission' => 'decimal:2',
        'paid_out_at' => 'datetime',
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }

    public function objet()
    {
        return $this->belongsTo(Objet::class);
    }

    public function finder()
    {
        return $this->belongsTo(User::class, 'finder_user_id');
    }

    public function claimer()
    {
        return $this->belongsTo(User::class, 'claimer_user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
