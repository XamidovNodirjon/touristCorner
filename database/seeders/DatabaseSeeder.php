<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LibraryCategorySeeder::class,
            EventCategorySeeder::class,
        ]);
    }
}
