<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    protected $fillable = ['name', 'description', 'image'];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }   
}
