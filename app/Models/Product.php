<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'stock',
        'category_id',
        'image_url', // Add this line
        'discount',
        'rating'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price');
    }

    // Add relationship with inventory movement
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // Add function to update stock
    public function updateStock($quantity, $type, $referenceType = null, $referenceId = null, $notes = null)
    {
        // Record inventory movement
        $this->inventoryMovements()->create([
            'quantity' => abs($quantity),
            'type' => $type,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'user_id' => auth()->id()
        ]);

        // Update stock quantity
        if ($type === 'in') {
            $this->increment('stock', abs($quantity));
        } else {
            $this->decrement('stock', abs($quantity));
        }

        // If stock drops below the minimum, send a notification
        if ($this->stock <= $this->min_stock && $this->min_stock > 0) {
            $this->sendLowStockNotification();
        }

        return $this;
    }

    // Add function to send low stock notification
    protected function sendLowStockNotification()
    {
        // Send notification to users with permission to manage inventory
        $users = \App\Models\User::whereHas('roles.permissions', function ($query) {
            $query->where('name', 'edit_products');
        })->get();

        foreach ($users as $user) {
            $user->notify(new \App\Notifications\LowStockNotification($this));
        }
    }

}