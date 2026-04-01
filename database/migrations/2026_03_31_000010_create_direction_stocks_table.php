<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectionStocksTable extends Migration
{
    public function up()
    {
        Schema::create('direction_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direction_id')->constrained('directions')->cascadeOnDelete();
            $table->foreignId('plant_id')->constrained('plants')->cascadeOnDelete();
            $table->integer('stock')->default(0);
            $table->timestamps();
            $table->unique(['direction_id', 'plant_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('direction_stocks');
    }
}
