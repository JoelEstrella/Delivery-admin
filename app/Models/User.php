<?php

namespace App\Models;

use App\Models\ActivityLog;
use App\Models\Delivery;
use App\Models\DeliveryValidation;
use App\Models\Role;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'username',
        'password',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }

    public function deliveriesCreated()
    {
        return $this->hasMany(Delivery::class, 'created_by');
    }

    public function deliveryValidations()
    {
        return $this->hasMany(DeliveryValidation::class, 'validated_by');
    }

    public function isSuperAdmin()
    {
        return $this->role && $this->role->slug === 'super-admin';
    }

    public function hasPermission($permission)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        if ($this->role->relationLoaded('permissions')) {
            return $this->role->permissions->contains('slug', $permission);
        }

        return $this->role->permissions()->where('slug', $permission)->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
