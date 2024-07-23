<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receive_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->date('date');
            $table->string('reference')->nullable();
            $table->foreignId('created_uid')->nullable();
            $table->timestamps();
        });

        Schema::create('receive_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receive_order_id')->constrained()->on('receive_orders')->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('pallet');

            $table->foreignId('product_id')->constrained()->on('products')->references('id')
                ->restrictOnUpdate()
                ->restrictOnDelete();

            $table->decimal('amount');
            $table->jsonb('mounts')->nullable();

            $table->foreignId('received_uid')->nullable();
            $table->foreignId('mounted_uid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receive_order_items');
        Schema::dropIfExists('receive_orders');
    }
};
