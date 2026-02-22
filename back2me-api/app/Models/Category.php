<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','icon'];
       public function objets()
    {
        return $this->hasMany(Objet::class);
    }
}
