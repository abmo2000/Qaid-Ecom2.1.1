<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('order_id')->unique()->nullable()->after('id');
            $table->string('delivery_option')->nullable()->default(null);
            $table->unsignedInteger('delivery_price')->default(0);
        });

          if (DB::getDriverName() === 'sqlite') {
              DB::statement('UPDATE orders SET order_id = LOWER(hex(randomblob(16))) WHERE order_id IS NULL');
              DB::statement('CREATE UNIQUE INDEX orders_order_id_unique_idx ON orders (order_id)');
          } else {
              DB::statement('ALTER TABLE orders MODIFY order_id CHAR(36) UNIQUE DEFAULT (UUID())');
              DB::statement('UPDATE orders SET order_id = UUID() WHERE order_id IS NULL');
          }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_id' , 'delivery_option' , 'delivery_price']);
        });
    }
};
