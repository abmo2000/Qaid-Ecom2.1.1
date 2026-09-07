<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $legacy = DB::table('buisness_settings')->where('key', 'seo-settings')->first();

        if (! $legacy) {
            return;
        }

        $homeId = DB::table('buisness_settings')->where('key', 'seo-page-home')->value('id');
        $homeId ??= DB::table('buisness_settings')->insertGetId([
            'key' => 'seo-page-home',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (DB::table('buisness_setting_translations')->where('buisness_setting_id', $legacy->id)->get() as $translation) {
            $value = is_string($translation->value) ? json_decode($translation->value, true) : [];
            $value = is_array($value) ? $value : [];

            DB::table('buisness_setting_translations')->updateOrInsert(
                ['buisness_setting_id' => $homeId, 'locale' => $translation->locale],
                [
                    'value' => json_encode([]),
                    'meta_title' => $value['meta_title'] ?? null,
                    'meta_description' => $value['meta_description'] ?? null,
                    'meta_keywords' => $value['meta_keywords'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('buisness_settings')->where('key', 'seo-page-home')->delete();
    }
};