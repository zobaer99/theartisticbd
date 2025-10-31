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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('footer_phone');
            $table->boolean('is_whatsapp_enabled')->default(true)->after('whatsapp_number');
            $table->text('whatsapp_message')->nullable()->after('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
            $table->dropColumn('is_whatsapp_enabled');
            $table->dropColumn('whatsapp_message');
        });
    }
};
