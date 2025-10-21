<?php

namespace Database\Seeders;

use App\Models\Library;
use App\Models\LibraryCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LibraryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {
        $regions = [
            ['uz' => 'Toshkent shahri', 'en' => 'Tashkent City', 'ru' => 'Город Ташкент'],
            ['uz' => 'Andijon viloyati', 'en' => 'Andijan Region', 'ru' => 'Андижанская область'],
            ['uz' => 'Buxoro viloyati', 'en' => 'Bukhara Region', 'ru' => 'Бухарская область'],
            ['uz' => 'Farg‘ona viloyati', 'en' => 'Fergana Region', 'ru' => 'Ферганская область'],
            ['uz' => 'Jizzax viloyati', 'en' => 'Jizzakh Region', 'ru' => 'Джизакская область'],
            ['uz' => 'Xorazm viloyati', 'en' => 'Khorezm Region', 'ru' => 'Хорезмская область'],
            ['uz' => 'Qashqadaryo viloyati', 'en' => 'Kashkadarya Region', 'ru' => 'Кашкадарьинская область'],
            ['uz' => 'Navoiy viloyati', 'en' => 'Navoi Region', 'ru' => 'Навоийская область'],
            ['uz' => 'Namangan viloyati', 'en' => 'Namangan Region', 'ru' => 'Наманганская область'],
            ['uz' => 'Samarqand viloyati', 'en' => 'Samarkand Region', 'ru' => 'Самаркандская область'],
            ['uz' => 'Sirdaryo viloyati', 'en' => 'Syrdarya Region', 'ru' => 'Сырдарьинская область'],
            ['uz' => 'Surxondaryo viloyati', 'en' => 'Surkhandarya Region', 'ru' => 'Сурхандарьинская область'],
            ['uz' => 'Qoraqalpog‘iston Respublikasi', 'en' => 'Republic of Karakalpakstan', 'ru' => 'Республика Каракалпакстан'],
            ['uz' => 'Toshkent viloyati', 'en' => 'Tashkent Region', 'ru' => 'Ташкентская область'],
        ];

        foreach ($regions as $region) {
            LibraryCategory::create([
                'name_uz' => $region['uz'],
                'name_en' => $region['en'],
                'name_ru' => $region['ru'],
            ]);
        }
    }
}
