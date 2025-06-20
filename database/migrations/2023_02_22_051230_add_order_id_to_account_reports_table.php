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
        Schema::table('account_reports', function (Blueprint $table) {
            $table->foreignId('order_id')
                ->nullable()
                ->after('id')
                ->references('id')
                ->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('account_reports', function (Blueprint $table) {
            $table->dropForeign('account_reports_order_id_foreign');
            $table->dropColumn('order_id');
        });
    }
};
