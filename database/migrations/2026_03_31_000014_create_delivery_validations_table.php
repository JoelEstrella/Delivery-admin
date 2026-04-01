<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryValidationsTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete()->unique();
            $table->integer('received_quantity')->default(0);
            $table->text('observations')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['delivery_id', 'status', 'validated_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_validations');
    }
}
