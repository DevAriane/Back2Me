<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objet extends Model
{
    protected $fillable = ['user_id', 'category_id', 'name', 'description', 'location', 'found_date', 'status', 'photo_url'];

    protected $casts = [
        'found_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
}