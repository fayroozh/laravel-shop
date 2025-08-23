<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // العلاقات
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // الدوال المساعدة
    public function hasPermission($permission)
    {
        // If user is admin, they have all permissions
        if ($this->is_admin) {
            return true;
        }
        
        // Check if user has the specific permission through roles
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->exists();
    }

    public function isAdmin()
    {
        // التحقق من خاصية is_admin أو وجود دور admin
        return $this->is_admin === true || $this->roles()->where('name', 'admin')->exists();
    }

    public function isEmployee()
    {
        // التحقق من خاصية is_employee_role أو وجود دور employee
        return $this->is_employee_role || $this->roles()->where('name', 'employee')->exists() || $this->employee()->exists();
    }

    public function getEmployeeData()
    {
        return $this->employee;
    }
}