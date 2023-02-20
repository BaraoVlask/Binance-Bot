<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('price_ranges', function (Blueprint $table) {
            $table->float('sell_price', 8, 8)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('price_ranges', function (Blueprint $table) {
            $table->float('sell_price')
                ->change();
        });
    }
};
