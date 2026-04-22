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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'watt')) {
                $table->string('watt')->nullable()->after('name');
            }
            $table->string('meta_title')->nullable()->after('status');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keyword')->nullable()->after('meta_description');
            $table->string('product_brochure_photo')->nullable()->after('meta_keyword');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['watt', 'meta_title', 'meta_description', 'meta_keyword', 'product_brochure_photo']);
        });
    }
};
