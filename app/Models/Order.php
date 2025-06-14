<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'customer_name',
        'email',
        'mobile',
        'address',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($order) {
            // إرسال إشعار للمستخدمين الذين لديهم صلاحية إدارة الطلبات
            $users = \App\Models\User::whereHas('roles.permissions', function($query) {
                $query->where('name', 'view_orders');
            })->get();
            
            foreach ($users as $user) {
                $user->notify(new \App\Notifications\NewOrderNotification($order));
            }
        });
    }
}
