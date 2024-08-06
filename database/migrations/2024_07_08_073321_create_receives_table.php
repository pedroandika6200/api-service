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

            $table->foreignId('received_uid')->nullable();
            $table->foreignId('mounted_uid')->nullable();
            $table->timestamps();
        });

        Schema::create('receive_order_mounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receive_order_item_id')->constrained()->on('receive_order_items')->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('locker_id')->constrained()->on('lockers')->references('id')
                ->restrictOnUpdate()
                ->restrictOnDelete();

            $table->decimal('amount');
            $table->timestamp('mounted_at')->nullable();
            $table->timestamps();
        });

        Schema::table('lockers', function (Blueprint $table) {
            $table->foreignId('receive_order_id')->nullable()->after('product_id')
                ->constrained()->on('receive_orders')->references('id')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lockers', function (Blueprint $table) {
            $table->dropColumn('receive_order_id');
        });
        Schema::dropIfExists('receive_order_items');
        Schema::dropIfExists('receive_orders');
    }
};
