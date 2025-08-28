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

    // إضافة العلاقة مع حركة المخزون
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // إضافة دالة لتحديث المخزون
    public function updateStock($quantity, $type, $referenceType = null, $referenceId = null, $notes = null)
    {
        // تسجيل حركة المخزون
        $this->inventoryMovements()->create([
            'quantity' => abs($quantity),
            'type' => $type,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'user_id' => auth()->id()
        ]);

        // تحديث كمية المخزون
        if ($type === 'in') {
            $this->increment('stock', abs($quantity));
        } else {
            $this->decrement('stock', abs($quantity));
        }

        // إذا انخفض المخزون عن الحد الأدنى، إرسال إشعار
        if ($this->stock <= $this->min_stock && $this->min_stock > 0) {
            $this->sendLowStockNotification();
        }

        return $this;
    }

    // إضافة دالة لإرسال إشعار انخفاض المخزون
    protected function sendLowStockNotification()
    {
        // إرسال إشعار للمستخدمين الذين لديهم صلاحية إدارة المخزون
        $users = \App\Models\User::whereHas('roles.permissions', function ($query) {
            $query->where('name', 'edit_products');
        })->get();

        foreach ($users as $user) {
            $user->notify(new \App\Notifications\LowStockNotification($this));
        }
    }

}