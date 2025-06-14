<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'category',
        'price',
        'discount',
        'rating',
        'stock',
        'category_id',
    ];
    public function images()
    {
    return $this->hasMany(ProductImage::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
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
        $users = \App\Models\User::whereHas('roles.permissions', function($query) {
            $query->where('name', 'edit_products');
        })->get();
        
        foreach ($users as $user) {
            $user->notify(new \App\Notifications\LowStockNotification($this));
        }
    }
}

