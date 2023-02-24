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
            $table->foreignId('protection_symbol_id')
                ->after('symbol_id')
                ->nullable()
                ->references('id')
                ->on('symbols');
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
            $table->dropForeign('price_ranges_protection_symbol_id_foreign');
            $table->dropColumn('protection_symbol_id');
        });
    }
};
