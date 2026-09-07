<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wholesale_requests', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('phone', 30);
            $table->text('message')->nullable();
            $table->string('status')->default('new')->index();
            $table->ipAddress('ip_address')->nullable();
            $table->string('locale', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wholesale_requests');
    }
};