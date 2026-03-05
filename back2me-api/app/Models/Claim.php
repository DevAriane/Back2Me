<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = ['objet_id', 'user_id', 'message', 'proof_file_url', 'proof_link', 'object_price', 'status', 'rejection_reason'];

    protected $casts = [
        'object_price' => 'decimal:2',
    ];

    public function objet()
    {
        return $this->belongsTo(Objet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commission()
    {
        return $this->hasOne(Commission::class);
    }
}
