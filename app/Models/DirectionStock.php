<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectionStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'direction_id',
        'plant_id',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
