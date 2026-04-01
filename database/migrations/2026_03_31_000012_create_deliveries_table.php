<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cct_id')->constrained('ccts')->restrictOnDelete();
            $table->foreignId('direction_id')->constrained('directions')->restrictOnDelete();
            $table->date('delivery_date');
            $table->string('status')->default('pending');
            $table->text('observations')->nullable();
            $table->string('delivered_by')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['cct_id', 'direction_id', 'delivery_date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
}
