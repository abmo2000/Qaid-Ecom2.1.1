<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_translations', function (Blueprint $table): void {
            $table->index(
                ['product_id', 'locale', 'name'],
                'product_translations_product_locale_name_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('product_translations', function (Blueprint $table): void {
            $table->dropIndex('product_translations_product_locale_name_index');
        });
    }
};
