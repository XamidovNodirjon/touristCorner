<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $n = [
            'Music',
            'Art',
            'Technology',
            'Sports',
            'Education',
            'Health & Wellness',
            'Business & Networking',
            'Food & Drink',
            'Travel & Adventure',
            'Community & Social',
            'Film & Media',
            'Fashion & Beauty',
            'Science & Innovation',
            'Literature & Writing',
            'Gaming & Esports',
            'Charity & Fundraising',
            'Workshops & Classes',
            'Festivals & Fairs',
            'Religious & Spiritual',
            'Family & Kids'
        ];
        
        foreach($n as $name){
            EventCategory::create(['name' => $name]);
        }
    }
}
