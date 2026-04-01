<?php

namespace App\Models;

use App\Models\DeliveryItem;
use App\Models\DirectionStock;
use App\Models\PlantImage;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description_html',
        'care_instructions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(PlantImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(PlantImage::class)->where('is_primary', true);
    }

    public function deliveryItems()
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function stocks()
    {
        return $this->hasMany(DirectionStock::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
