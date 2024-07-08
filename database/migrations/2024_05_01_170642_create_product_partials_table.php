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
        Schema::create('product_partials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('part_id');
            $table->decimal('count');

            $table->unique(['product_id', 'part_id'], 'base_unique');

            $table->foreign('product_id')->on('products')->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('part_id')->on('products')->references('id')
                ->restrictOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_partials');
    }
};
