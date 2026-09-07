<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wholesale_requests', function (Blueprint $table) {
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('os')->nullable()->after('user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('wholesale_requests', function (Blueprint $table) {
            $table->dropColumn(['user_agent', 'os']);
        });
    }
};