<?php
/**
 * @Author: Anwarul
 * @Date: 2025-08-18 13:25:21
 * @LastEditors: Anwarul
 * @LastEditTime: 2025-08-26 10:12:13
 * @Description: Innova IT
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Items table indexes
        if (!DB::select("SHOW INDEXES FROM items WHERE Key_name = 'items_status_index'")) {
            DB::statement('CREATE INDEX items_status_index ON items (status)');
        }

        if (!DB::select("SHOW INDEXES FROM items WHERE Key_name = 'items_is_type_index'")) {
            DB::statement('CREATE INDEX items_is_type_index ON items (is_type)');
        }

        if (!DB::select("SHOW INDEXES FROM items WHERE Key_name = 'items_category_status_index'")) {
            DB::statement('CREATE INDEX items_category_status_index ON items (category_id, status)');
        }

        if (!DB::select("SHOW INDEXES FROM items WHERE Key_name = 'items_subcategory_status_index'")) {
            DB::statement('CREATE INDEX items_subcategory_status_index ON items (subcategory_id, status)');
        }

        if (!DB::select("SHOW INDEXES FROM items WHERE Key_name = 'items_childcategory_status_index'")) {
            DB::statement('CREATE INDEX items_childcategory_status_index ON items (childcategory_id, status)');
        }

        if (!DB::select("SHOW INDEXES FROM items WHERE Key_name = 'items_created_at_index'")) {
            DB::statement('CREATE INDEX items_created_at_index ON items (created_at)');
        }

        // Categories table
        if (!DB::select("SHOW INDEXES FROM categories WHERE Key_name = 'categories_status_index'")) {
            DB::statement('CREATE INDEX categories_status_index ON categories (status)');
        }

        // Brands table
        if (!DB::select("SHOW INDEXES FROM brands WHERE Key_name = 'brands_status_popular_index'")) {
            DB::statement('CREATE INDEX brands_status_popular_index ON brands (status, is_popular)');
        }

        // Campaign items table
        if (!DB::select("SHOW INDEXES FROM campaign_items WHERE Key_name = 'campaign_items_status_feature_index'")) {
            DB::statement('CREATE INDEX campaign_items_status_feature_index ON campaign_items (status, is_feature)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // MySQL DROP INDEX syntax is: DROP INDEX index_name ON table_name;
        DB::statement('DROP INDEX items_status_index ON items');
        DB::statement('DROP INDEX items_is_type_index ON items');
        DB::statement('DROP INDEX items_category_status_index ON items');
        DB::statement('DROP INDEX items_subcategory_status_index ON items');
        DB::statement('DROP INDEX items_childcategory_status_index ON items');
        DB::statement('DROP INDEX items_created_at_index ON items');

        DB::statement('DROP INDEX categories_status_index ON categories');
        DB::statement('DROP INDEX brands_status_popular_index ON brands');
        DB::statement('DROP INDEX campaign_items_status_feature_index ON campaign_items');
    }
};
