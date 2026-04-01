<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'plant_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
