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
        Schema::create('racks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->json('position')->nullable();
            $table->timestamps();
        });

        Schema::create('lockers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('rack_id');
            $table->json('position')->nullable();
            $table->jsonb('dimension')->comment("Array dimension [d x d x d]");
            $table->integer('wmax')->comment("Maximum weight on [kg] unit");
            $table->foreignId('product_id')->nullable()
                ->constrained()->on('products')->references('id')->nullOnDelete()->cascadeOnUpdate();
            $table->integer('capacity')->default(0);
            $table->integer('amount')->default(0);
            $table->timestamps();
        });

        Schema::create('lockerables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locker_id');
            $table->foreignId('product_id');
            $table->integer('amount')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lockerables');
        Schema::dropIfExists('lockers');
        Schema::dropIfExists('racks');
    }
};
