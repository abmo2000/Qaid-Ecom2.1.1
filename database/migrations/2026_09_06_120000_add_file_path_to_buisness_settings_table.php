<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buisness_settings', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('key');
        });
    }

    public function down(): void
    {
        Schema::table('buisness_settings', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
};