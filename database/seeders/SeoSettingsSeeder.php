<?php

namespace Database\Seeders;

use App\Models\BuisnessSetting;
use Illuminate\Database\Seeder;

class SeoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Create default SEO Settings if it doesn't exist
        BuisnessSetting::firstOrCreate(
            ['key' => 'seo-settings'],
            ['key' => 'seo-settings']
        );

        // Create English translation
        $seoSetting = BuisnessSetting::where('key', 'seo-settings')->first();
        if ($seoSetting) {
            $enTranslation = $seoSetting->translations()->where('locale', 'en')->first();
            if (!$enTranslation) {
                $seoSetting->translations()->create([
                    'locale' => 'en',
                    'value' => json_encode([
                        'meta_title' => 'Welcome to Our E-commerce Store',
                        'meta_description' => 'Discover amazing products and great deals on our online store. Shop quality items at the best prices.',
                        'meta_keywords' => 'shop, store, products, online, ecommerce',
                    ]),
                ]);
            }

            // Create Arabic translation
            $arTranslation = $seoSetting->translations()->where('locale', 'ar')->first();
            if (!$arTranslation) {
                $seoSetting->translations()->create([
                    'locale' => 'ar',
                    'value' => json_encode([
                        'meta_title' => 'مرحبا بك في متجرنا الإلكتروني',
                        'meta_description' => 'اكتشف منتجات رائعة وعروض مميزة في متجرنا الإلكتروني. تسوق منتجات جودة بأفضل الأسعار.',
                        'meta_keywords' => 'تسوق, متجر, منتجات, اونلاين, تجارة إلكترونية',
                    ]),
                ]);
            }
        }
    }
}
