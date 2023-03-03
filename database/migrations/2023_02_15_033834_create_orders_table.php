<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->nullable()
                ->references('id')
                ->on('orders');
            $table->foreignId('price_range_id')
                ->references('id')
                ->on('price_ranges');
            $table->string('binance_id')->nullable()->unique();
            $table->string('side', 4);
            $table->string('status', 20)->nullable();
            $table->float('price', 16, 8);
            $table->float('quantity', 16, 8);
            $table->unsignedFloat('amount', 16, 8)->nullable();
            $table->unsignedFloat('commission_amount', 16, 8)->nullable();
            $table->foreignId('commission_coin')
                ->nullable()
                ->references('id')
                ->on('coins');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
