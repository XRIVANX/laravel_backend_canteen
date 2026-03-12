<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'description', 'price', 
        'image', 'stock_quantity', 'low_stock_threshold', 'is_available'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isOutOfStock()
    {
        return $this->stock_quantity <= 0;
    }

    public function updateStock($quantity, $reason, $referenceType = null, $referenceId = null, $userId = null)
    {
        $previousStock = $this->stock_quantity;
        $newStock = $previousStock + $quantity;
        
        $this->stock_quantity = $newStock;
        $this->is_available = $newStock > 0;
        $this->save();

        // Log inventory change
        InventoryLog::create([
            'menu_item_id' => $this->id,
            'quantity_change' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_by' => $userId ?? auth()->id(),
        ]);

        return $this;
    }
}