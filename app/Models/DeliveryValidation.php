<?php

namespace App\Models;

use App\Models\Delivery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryValidation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'delivery_id',
        'received_quantity',
        'observations',
        'validated_by',
        'validated_at',
        'status',
    ];

    protected $casts = [
        'received_quantity' => 'integer',
        'validated_at' => 'datetime',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
