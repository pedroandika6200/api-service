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
        Schema::create('packings', function (Blueprint $table) {
            $table->id();
            $table->string('state')->default('OPEN');
            // State option: [OPEN, PICKED, CHECKED, PACKED, SHIPPED]
            $table->foreignId('sales_order_id')->constrained()->on('sales_orders')->references('id')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->integer('seq')->default(0);

            $table->foreignId('fetched_uid');
            $table->foreignId('packed_uid');
            $table->timestamps();
        });

        Schema::create('packing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packing_id')->constrained()->on('packings')->references('id')
                ->cascadeOnUpdate();

            $table->foreignId('sales_order_item_id')->constrained()->on('sales_order_items')->references('id')
                ->restrictOnDelete()
                ->restrictOnUpdate();

            $table->decimal('quantity');
            $table->string('unit');
            $table->timestamps();
        });


        Schema::create('delivery_batchs', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->jsonb('sales');
            $table->timestamps();
        });

        Schema::create('delivery_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_batch_id')->constrained()->on('delivery_batchs')->references('id')
                ->cascadeOnDelete();

            $table->foreignId('product_id')->constrained()->on('products')->references('id')
                ->restrictOnDelete()
                ->restrictOnUpdate();

            $table->decimal('quantity');
            $table->string('unit');

            $table->foreignId('fetched_uid');
            $table->timestamps();
        });

        Schema::create('packing_batchs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packing_id')->constrained()->on('packings')->references('id')
                ->cascadeOnDelete()
                ->restrictOnUpdate();

            $table->foreignId('delivery_batch_id')->constrained()->on('delivery_batchs')->references('id')
                ->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_batchings');
        Schema::dropIfExists('delivery_packings');
        Schema::dropIfExists('delivery_shipments');
    }
};
