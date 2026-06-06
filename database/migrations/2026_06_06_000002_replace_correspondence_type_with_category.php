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
        if (Schema::hasColumn('correspondences', 'type')) {
            Schema::table('correspondences', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (! Schema::hasColumn('correspondences', 'category')) {
            Schema::table('correspondences', function (Blueprint $table) {
                $table->enum('category', ['request', 'decision', 'circular', 'summons'])
                    ->after('serial_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('correspondences', 'category')) {
            Schema::table('correspondences', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }

        if (! Schema::hasColumn('correspondences', 'type')) {
            Schema::table('correspondences', function (Blueprint $table) {
                $table->enum('type', ['internal', 'outbound', 'inbound'])
                    ->after('serial_number');
            });
        }
    }
};
