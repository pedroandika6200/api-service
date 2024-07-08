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
        Schema::create('product_converts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('base_id');
            $table->foreignId('point_id');
            $table->decimal('rate');

            $table->unique(['base_id', 'point_id'], 'base_unique');
            $table->foreign('base_id')->on('products')->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('point_id')->on('products')->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_converts');
    }
};
