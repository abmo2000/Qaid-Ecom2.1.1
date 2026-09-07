<?php

namespace Database\Seeders;

use App\Models\BuisnessSetting;
use Illuminate\Database\Seeder;

class SeoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'home' => [
                'en' => ['Welcome to Our E-commerce Store', 'Discover amazing products and great deals on our online store. Shop quality items at the best prices.', 'shop, store, products, online, ecommerce'],
                'ar' => ['مرحبا بك في متجرنا الإلكتروني', 'اكتشف منتجات رائعة وعروض مميزة في متجرنا الإلكتروني. تسوق منتجات جودة بأفضل الأسعار.', 'تسوق, متجر, منتجات, اونلاين, تجارة إلكترونية'],
            ],
            'shop' => ['en' => ['Shop Our Products', 'Browse quality products and find the right choice for you.', 'shop, products, online store'], 'ar' => ['تسوق منتجاتنا', 'تصفح المنتجات عالية الجودة واعثر على الاختيار المناسب لك.', 'تسوق, منتجات, متجر إلكتروني']],
            'contact' => ['en' => ['Contact Us', 'Get in touch with our team and we will be happy to help.', 'contact, customer support'], 'ar' => ['تواصل معنا', 'تواصل مع فريقنا وسنسعد بمساعدتك.', 'تواصل, دعم العملاء']],
            'wholesale-sales' => ['en' => ['Wholesale Sales', 'Explore wholesale opportunities and request a business price quote.', 'wholesale, business sales'], 'ar' => ['مبيعات الجملة', 'اكتشف فرص البيع بالجملة واطلب عرض سعر لنشاطك التجاري.', 'جملة, مبيعات الشركات']],
            'terms' => ['en' => ['Terms and Conditions', 'Read the terms and conditions for using our store.', 'terms, conditions'], 'ar' => ['الشروط والأحكام', 'اقرأ الشروط والأحكام الخاصة باستخدام متجرنا.', 'الشروط, الأحكام']],
            'routines' => ['en' => ['Routines', 'Discover routines and recommendations for your needs.', 'routines, recommendations'], 'ar' => ['الروتينات', 'اكتشف الروتينات والتوصيات المناسبة لاحتياجاتك.', 'روتين, توصيات']],
            'packages' => ['en' => ['Packages', 'Explore our available packages and offers.', 'packages, offers'], 'ar' => ['الباقات', 'اكتشف الباقات والعروض المتاحة لدينا.', 'باقات, عروض']],
        ];

        foreach ($pages as $page => $locales) {
            $setting = BuisnessSetting::firstOrCreate(['key' => 'seo-page-' . $page]);

            foreach ($locales as $locale => [$title, $description, $keywords]) {
                $setting->translateOrNew($locale)->fill([
                    'value' => $setting->translate($locale)?->value ?? [],
                    'meta_title' => $setting->translate($locale)?->meta_title ?: $title,
                    'meta_description' => $setting->translate($locale)?->meta_description ?: $description,
                    'meta_keywords' => $setting->translate($locale)?->meta_keywords ?: $keywords,
                ])->save();
            }
        }
    }
}
