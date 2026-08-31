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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('admin_creator_id')
                ->nullable()
                ->after('customer_type')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->string('insta_account')
                ->nullable()
                ->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_creator_id');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('insta_account');
        });
    }
};
