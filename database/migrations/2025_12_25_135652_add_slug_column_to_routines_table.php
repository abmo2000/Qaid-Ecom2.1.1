<?php

use App\Models\Routine;
use Illuminate\Support\Str;
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
        Schema::table('routines', function (Blueprint $table) {
            $table->string('slug')->nullable()->default(null);
        });

       $routines = DB::table('routines')
        ->select(['routine_translations.title' , 'routines.id'])
        ->join('routine_translations' , 'routine_translations.routine_id' , '=' , 'routines.id')
        ->get()
        ->each(function ($routine) {
             Routine::query()->update([
                'slug' => Str::slug($routine->title . '-' .$routine->id),
             ]);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->dropColumn(['slug']);
        });
    }
};
