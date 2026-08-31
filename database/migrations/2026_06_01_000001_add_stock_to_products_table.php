<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'stock')) {
                $table->unsignedInteger('stock')->default(1)->after('price');
            }
        });

        if (Schema::hasColumn('products', 'in_stock')) {
            DB::table('products')
                ->where('in_stock', false)
                ->update(['stock' => 0]);

            DB::table('products')
                ->where('in_stock', true)
                ->update(['stock' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
