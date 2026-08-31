<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $cities = [
            // القاهرة والجيزة - Price 0, No discussion
            [
                'has_discussion_for_delivery' => true,
                'price' => 85,
                'translations' => [
                    'ar' => ['name' => 'القاهرة'],
                    'en' => ['name' => 'Cairo']
                ]
            ],
            [
                'has_discussion_for_delivery' => true,
                'price' => 85,
                'translations' => [
                    'ar' => ['name' => 'الجيزة'],
                    'en' => ['name' => 'Giza']
                ]
            ],
            
            // الأسكندرية والبحيرة - Price 0, No discussion
            [
                'has_discussion_for_delivery' => false,
                'price' => 90,
                'translations' => [
                    'ar' => ['name' => 'الأسكندرية'],
                    'en' => ['name' => 'Alexandria']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 90,
                'translations' => [
                    'ar' => ['name' => 'البحيرة'],
                    'en' => ['name' => 'Beheira']
                ]
            ],
            
            // الدلتا والقناة - Price 50, Has discussion
            [
                'has_discussion_for_delivery' => false,
                'price' => 96,
                'translations' => [
                    'ar' => ['name' => 'الدقهلية'],
                    'en' => ['name' => 'Dakahlia']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 96,
                'translations' => [
                    'ar' => ['name' => 'القليوبية'],
                    'en' => ['name' => 'Qalyubia']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 96,
                'translations' => [
                    'ar' => ['name' => 'الغربية'],
                    'en' => ['name' => 'Gharbia']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 96,
                'translations' => [
                    'ar' => ['name' => 'كفر الشيخ'],
                    'en' => ['name' => 'Kafr El Sheikh']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 96,
                'translations' => [
                    'ar' => ['name' => 'المنوفية'],
                    'en' => ['name' => 'Monufia']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 96,
                'translations' => [
                    'ar' => ['name' => 'الشرقية'],
                    'en' => ['name' => 'Sharqia']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 96,
                'translations' => [
                    'ar' => ['name' => 'دمياط'],
                    'en' => ['name' => 'Damietta']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 96,
                'translations' => [
                    'ar' => ['name' => 'الإسماعيلية'],
                    'en' => ['name' => 'Ismailia']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 96,
                'translations' => [
                    'ar' => ['name' => 'بورسعيد'],
                    'en' => ['name' => 'Port Said']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 96,
                'translations' => [
                    'ar' => ['name' => 'السويس'],
                    'en' => ['name' => 'Suez']
                ]
            ],
            
            // شمال الصعيد - Price 75, Has discussion
            [
                'has_discussion_for_delivery' => false,
                'price' => 108,
                'translations' => [
                    'ar' => ['name' => 'الفيوم'],
                    'en' => ['name' => 'Fayoum']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 108,
                'translations' => [
                    'ar' => ['name' => 'بني سويف'],
                    'en' => ['name' => 'Beni Suef']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 108,
                'translations' => [
                    'ar' => ['name' => 'المنيا'],
                    'en' => ['name' => 'Minya']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 108,
                'translations' => [
                    'ar' => ['name' => 'أسيوط'],
                    'en' => ['name' => 'Asyut']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 108,
                'translations' => [
                    'ar' => ['name' => 'سوهاج'],
                    'en' => ['name' => 'Sohag']
                ]
            ],
            
            // جنوب الصعيد - Price 100, Has discussion
            [
                'has_discussion_for_delivery' => false,
                'price' => 122,
                'translations' => [
                    'ar' => ['name' => 'قنا'],
                    'en' => ['name' => 'Qena']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 122,
                'translations' => [
                    'ar' => ['name' => 'الأقصر'],
                    'en' => ['name' => 'Luxor']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 122,
                'translations' => [
                    'ar' => ['name' => 'أسوان'],
                    'en' => ['name' => 'Aswan']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 122,
                'translations' => [
                    'ar' => ['name' => 'البحر الأحمر'],
                    'en' => ['name' => 'Red Sea']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 122,
                'translations' => [
                    'ar' => ['name' => 'مطروح'],
                    'en' => ['name' => 'Matrouh']
                ]
            ],
            
            // الساحل الشمالي - Price 0, No discussion
            [
                'has_discussion_for_delivery' => false,
                'price' => 125,
                'translations' => [
                    'ar' => ['name' => 'الساحل الشمالي'],
                    'en' => ['name' => 'North Coast']
                ]
            ],
            
            // سيناء والوادي الجديد - Price 100, Has discussion
            [
                'has_discussion_for_delivery' => false,
                'price' => 140,
                'translations' => [
                    'ar' => ['name' => 'شمال سيناء'],
                    'en' => ['name' => 'North Sinai']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 140,
                'translations' => [
                    'ar' => ['name' => 'جنوب سيناء'],
                    'en' => ['name' => 'South Sinai']
                ]
            ],
            [
                'has_discussion_for_delivery' => false,
                'price' => 140,
                'translations' => [
                    'ar' => ['name' => 'الوادي الجديد'],
                    'en' => ['name' => 'New Valley']
                ]
            ],
        ];

         foreach ($cities as $cityData) {
            $translations = $cityData['translations'];
            unset($cityData['translations']);

            $city = City::create($cityData);

            foreach ($translations as $locale => $translation) {
                $city->translateOrNew($locale)->fill(['locale' => $locale , 'name' => $translation['name']])->save();
            }

            //$city->save();
        }

        Cache::forget('cities');
    }
}
