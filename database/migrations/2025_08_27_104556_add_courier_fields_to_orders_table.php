<?php
/**
 * @Author: Anwarul
 * @Date: 2025-08-27 16:45:56
 * @LastEditors: Anwarul
 * @LastEditTime: 2025-08-27 16:48:10
 * @Description: Innova IT
 */

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
       Schema::table('orders', function (Blueprint $table) {
            $table->string('courier_status')->nullable()->after('payment_status')->comment('Pathao courier status');
            $table->string('courier_tracking_id')->nullable()->after('courier_status')->comment('Pathao tracking ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier_status', 'courier_tracking_id']);
        });
    }
};
