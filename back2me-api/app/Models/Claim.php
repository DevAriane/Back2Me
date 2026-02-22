<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = ['objet_id', 'user_id', 'message', 'status', 'rejection_reason'];

    public function objet()
    {
        return $this->belongsTo(Objet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
