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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('GENERAL');
            $table->string('number');
            $table->date('date');
            $table->date('due')->nullable();
            $table->string('termcode')->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->on('customers')->references('id')
                ->restrictOnDelete()
                ->restrictOnUpdate();

            $table->text('description')->nullable();
            $table->string('state')->default('OPEN');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->nullable();
            $table->jsonb('option')->nullable();
            $table->timestamps();

        });

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->on('sales_orders')->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('product_id')->constrained()->on('products')->references('id')
                ->restrictOnDelete()
                ->restrictOnUpdate();

            $table->string('name');
            $table->string('unit', 10);
            $table->decimal('quantity');
            $table->decimal('price', 12, 2);
            $table->decimal('discprice', 12, 2)->nullable();
            $table->string('notes')->nullable();
            $table->jsonb('option')->nullable();
            $table->integer('seq');
            $table->integer('group_seq')->nullable();
            $table->timestamps();

            $table->unique(['sales_order_id', 'seq'], 'unique_seq');
            $table->unique(['sales_order_id', 'seq', 'group_seq'], 'unique_group_seq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
    }
};
