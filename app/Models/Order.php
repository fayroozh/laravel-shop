<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'email',
        'mobile',
        'address',
        'status',
        'total',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted()
    {
        static::created(function ($order) {
            // إرسال إشعار للمستخدمين الذين لديهم صلاحية إدارة الطلبات
            $users = \App\Models\User::whereHas('roles.permissions', function ($query) {
                $query->where('name', 'view_orders');
            })->get();

            foreach ($users as $user) {
                $user->notify(new \App\Notifications\NewOrderNotification($order));
            }
        });
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

}