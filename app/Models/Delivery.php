<?php

namespace App\Models;

use App\Models\Cct;
use App\Models\DeliveryItem;
use App\Models\DeliveryValidation;
use App\Models\Direction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cct_id',
        'direction_id',
        'delivery_date',
        'status',
        'observations',
        'delivered_by',
        'created_by',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function cct()
    {
        return $this->belongsTo(Cct::class, 'cct_id');
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function validation()
    {
        return $this->hasOne(DeliveryValidation::class);
    }
}
