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
        $names = [
            'Toshkent shahari',
            'Andijon viloyati',
            'Buxoro viloyati',
            'Farg\'ona viloyati',
            'Jizzax viloyati',
            'Xorazm viloyati',
            'Qashqadaryo viloyati',
            'Navoiy viloyati',
            'Namangan viloyati',
            'Samarqand viloyati',
            'Sirdaryo viloyati',
            'Surxondaryo viloyati',
            'Qoraqalpog\'iston Respublikasi',
            'Toshkent viloyati'
        ];

        foreach ($names as $name) {
            LibraryCategory::create(['name' => $name]);
        }
    }
}
