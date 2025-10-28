<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Library;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Material;

class EmailController extends Controller
{
    public function sendMaterial(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'material_id' => 'required|integer|exists:libraries,id',
                'lang' => 'nullable|in:uz,ru,en',
            ]);

            $lang = $request->input('lang', 'ru');

            $material = Library::findOrFail($request->material_id);

            // Faqat URL ni olayapmiz (hech qanday fayl yo‘q)
            $fileUrl = match ($lang) {
                'uz' => $material->file_path_uz,
                'en' => $material->file_path_en,
                default => $material->file_path_ru,
            };

            if (empty($fileUrl)) {
                return response()->json(['error' => 'Tanlangan tildagi URL topilmadi'], 404);
            }

            // Tilga qarab matn
            $title = match ($lang) {
                'uz' => $material->title_uz,
                'en' => $material->title_en,
                default => $material->title_ru,
            };

            $description = match ($lang) {
                'uz' => $material->description_uz,
                'en' => $material->description_en,
                default => $material->description_ru,
            };

            // Email matnini tayyorlaymiz
            $body = match ($lang) {
                'uz' => "Assalomu alaykum!\n\nSiz so‘ragan material: {$title}\n\n{$description}\n\nUshbu havola orqali ko‘rishingiz mumkin:\n{$fileUrl}",
                'en' => "Hello!\n\nYou requested the material: {$title}\n\n{$description}\n\nYou can view/download it here:\n{$fileUrl}",
                'ru' => "Здравствуйте!\n\nВы запросили материал: {$title}\n\n{$description}\n\nПосмотреть/скачать можно по ссылке:\n{$fileUrl}",
            };

            // Email yuborish
            Mail::raw($body, function ($message) use ($request, $title) {
                $message->to($request->email)
                    ->subject("Material: {$title}");
            });

            return response()->json(['success' => true, 'message' => 'Material havolasi email orqali yuborildi.']);

        } catch (\Throwable $e) {
            \Log::error('Email yuborishda xatolik: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }






    
    public function sendEvent(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'event' => 'required|array',
        ]);

        $event = $data['event'];

        // Fayl yo‘lini topish
        $filePath = storage_path('app/public/' . $event['image']);
        $hasFile = file_exists($filePath);

        // Email matni
        $messageText = "
            🗓 Tadbir nomi: {$event['title_uz']}
            📅 Sana: {$event['date']}
            📍 Joylashuv: {$event['location']}
            ℹ️ Tavsif:
            {$event['description_uz']}
        ";

        // Email yuborish
        \Mail::raw($messageText, function ($message) use ($data, $event, $filePath, $hasFile) {
            $message->to($data['email'])
                ->subject("Tadbir: {$event['title_uz']}");
            if ($hasFile) {
                $message->attach($filePath);
            }
        });

        return response()->json(['success' => true, 'message' => 'To‘liq tadbir ma’lumoti yuborildi ✅']);
    }

    public function sendMap(Request $request)
    {
        dd($request->all());
        try {
            $request->validate([
                'email' => 'required|email',
                'map_id' => 'required|integer|exists:maps,id',
                'lang' => 'nullable|in:uz,ru,en',
            ]);

            $lang = $request->input('lang', 'uz');
            $map = \App\Models\Map::findOrFail($request->map_id);

            // Tilga qarab ma'lumotlarni olish
            $title = match ($lang) {
                'uz' => $map->name_uz,
                'en' => $map->name_en,
                default => $map->name_ru,
            };

            $description = match ($lang) {
                'uz' => $map->description_uz,
                'en' => $map->description_en,
                default => $map->description_ru,
            };

            // Email matni
            $messageText = match ($lang) {
                'uz' => "
                🗺️ Joy nomi: {$title}
                📍 Joylashuv: O'zbekiston

                ℹ️ Tavsif:
                {$description}

                Bu joy haqida batafsil ma'lumot uchun veb-saytimizga tashrif buyuring!
                            ",
                            'en' => "
                🗺️ Location: {$title}
                📍 Location: Uzbekistan

                ℹ️ Description:
                {$description}

                Visit our website for more details about this location!
                            ",
                            default => "
                🗺️ Название места: {$title}
                📍 Местоположение: Узбекистан

                ℹ️ Описание:
                {$description}

                Посетите наш веб-сайт для получения подробной информации!
                ",
            };

            // Email yuborish
            \Mail::raw($messageText, function ($message) use ($request, $title) {
                $message->to($request->email)
                    ->subject("Joy haqida ma'lumot: {$title}");
            });

            return response()->json(['success' => true, 'message' => 'Ma\'lumotlar muvaffaqiyatli yuborildi!']);

        } catch (\Throwable $e) {
            \Log::error('Map email yuborishda xatolik: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
