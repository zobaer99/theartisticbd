<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'is_facebook_capi')) {
                $table->tinyInteger('is_facebook_capi')->default(0)->after('is_facebook_messenger');
            }
            if (!Schema::hasColumn('settings', 'facebook_capi_pixel_id')) {
                $table->string('facebook_capi_pixel_id')->nullable()->after('is_facebook_capi');
            }
            if (!Schema::hasColumn('settings', 'facebook_capi_access_token')) {
                $table->text('facebook_capi_access_token')->nullable()->after('facebook_capi_pixel_id');
            }
            if (!Schema::hasColumn('settings', 'facebook_capi_test_event_code')) {
                $table->string('facebook_capi_test_event_code')->nullable()->after('facebook_capi_access_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'facebook_capi_test_event_code')) {
                $table->dropColumn('facebook_capi_test_event_code');
            }
            if (Schema::hasColumn('settings', 'facebook_capi_access_token')) {
                $table->dropColumn('facebook_capi_access_token');
            }
            if (Schema::hasColumn('settings', 'facebook_capi_pixel_id')) {
                $table->dropColumn('facebook_capi_pixel_id');
            }
            if (Schema::hasColumn('settings', 'is_facebook_capi')) {
                $table->dropColumn('is_facebook_capi');
            }
        });
    }
};
