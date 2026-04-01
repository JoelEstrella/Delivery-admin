<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryItemsTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('plant_id')->constrained('plants')->restrictOnDelete();
            $table->integer('quantity');
            $table->timestamps();
            $table->index(['delivery_id', 'plant_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_items');
    }
}
