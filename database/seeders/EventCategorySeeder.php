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

    public function run()
    {
        $categories = [
            ['uz' => 'Musiqa', 'en' => 'Music', 'ru' => 'Музыка'],
            ['uz' => 'San’at', 'en' => 'Art', 'ru' => 'Искусство'],
            ['uz' => 'Texnologiya', 'en' => 'Technology', 'ru' => 'Технологии'],
            ['uz' => 'Sport', 'en' => 'Sports', 'ru' => 'Спорт'],
            ['uz' => 'Ta’lim', 'en' => 'Education', 'ru' => 'Образование'],
            ['uz' => 'Sog‘lomlik', 'en' => 'Health & Wellness', 'ru' => 'Здоровье и благополучие'],
            ['uz' => 'Biznes va Networking', 'en' => 'Business & Networking', 'ru' => 'Бизнес и связи'],
            ['uz' => 'Oziq-ovqat va ichimliklar', 'en' => 'Food & Drink', 'ru' => 'Еда и напитки'],
            ['uz' => 'Sayohat va sarguzashtlar', 'en' => 'Travel & Adventure', 'ru' => 'Путешествия и приключения'],
            ['uz' => 'Jamiyat va ijtimoiy', 'en' => 'Community & Social', 'ru' => 'Сообщество и социальные мероприятия'],
            ['uz' => 'Film va media', 'en' => 'Film & Media', 'ru' => 'Кино и медиа'],
            ['uz' => 'Moda va go‘zallik', 'en' => 'Fashion & Beauty', 'ru' => 'Мода и красота'],
            ['uz' => 'Fan va innovatsiyalar', 'en' => 'Science & Innovation', 'ru' => 'Наука и инновации'],
            ['uz' => 'Adabiyot va yozuv', 'en' => 'Literature & Writing', 'ru' => 'Литература и писательство'],
            ['uz' => 'O‘yinlar va Esport', 'en' => 'Gaming & Esports', 'ru' => 'Игры и киберспорт'],
            ['uz' => 'Xayriya va mablag‘ yig‘ish', 'en' => 'Charity & Fundraising', 'ru' => 'Благотворительность'],
            ['uz' => 'Treninglar va mashg‘ulotlar', 'en' => 'Workshops & Classes', 'ru' => 'Семинары и занятия'],
            ['uz' => 'Festival va yarmarkalar', 'en' => 'Festivals & Fairs', 'ru' => 'Фестивали и ярмарки'],
            ['uz' => 'Diniy va ma’naviy', 'en' => 'Religious & Spiritual', 'ru' => 'Религиозные и духовные'],
            ['uz' => 'Oila va bolalar', 'en' => 'Family & Kids', 'ru' => 'Семья и дети'],
        ];

        foreach ($categories as $cat) {
            EventCategory::create([
                'name_uz' => $cat['uz'],
                'name_en' => $cat['en'],
                'name_ru' => $cat['ru'],
            ]);
        }
    }

}
