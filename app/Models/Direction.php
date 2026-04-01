<?php

namespace App\Models;

use App\Models\Delivery;
use App\Models\DirectionStock;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Direction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'responsible_name',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stocks()
    {
        return $this->hasMany(DirectionStock::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
