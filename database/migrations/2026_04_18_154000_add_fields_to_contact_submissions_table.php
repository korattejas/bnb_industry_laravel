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
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->string('company_name', 150)->nullable()->after('last_name');
            $table->string('country_name', 100)->nullable()->after('city_name');
            $table->string('country_code', 20)->nullable()->after('country_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'country_name', 'country_code']);
        });
    }
};
