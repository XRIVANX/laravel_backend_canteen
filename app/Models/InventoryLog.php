<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id', 'quantity_change', 'previous_stock', 'new_stock',
        'reason', 'reference_type', 'reference_id', 'created_by'
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}