<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Library;
use App\Models\Map;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

            $fileUrl = match ($lang) {
                'uz' => $material->file_path_uz,
                'en' => $material->file_path_en,
                default => $material->file_path_ru,
            };

            if (empty($fileUrl)) {
                return response()->json(['error' => 'Tanlangan tildagi URL topilmadi'], 404);
            }

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

            $body = match ($lang) {
                'uz' => "Assalomu alaykum!\n\nSiz so‘ragan material: {$title}\n\n{$description}\n\nUshbu havola orqali ko‘rishingiz mumkin:\n{$fileUrl}",
                'en' => "Hello!\n\nYou requested the material: {$title}\n\n{$description}\n\nYou can view/download it here:\n{$fileUrl}",
                'ru' => "Здравствуйте!\n\nВы запросили материал: {$title}\n\n{$description}\n\nПосмотреть/скачать можно по ссылке:\n{$fileUrl}",
            };

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
        $filePath = storage_path('app/public/' . $event['image']);
        $hasFile = file_exists($filePath);

        $messageText = "
            Tadbir nomi: {$event['title_uz']}
            Sana: {$event['date']}
            Joylashuv: {$event['location']}
            Tavsif:
            {$event['description_uz']}
        ";

        \Mail::raw($messageText, function ($message) use ($data, $event, $filePath, $hasFile) {
            $message->to($data['email'])
                ->subject("Tadbir: {$event['title_uz']}");
            if ($hasFile) {
                $message->attach($filePath);
            }
        });

        return response()->json(['success' => true, 'message' => 'To‘liq tadbir ma’lumoti yuborildi']);
    }

    public function sendMap(Request $request)
    {
        try {
            $request->validate([
                'email'  => 'required|email',
                'map_id' => 'required|integer|exists:maps,id',
                'lang'   => 'nullable|in:uz,en,ru',
            ]);

            $lang = $request->input('lang', 'uz');
            $map  = Map::findOrFail($request->map_id);

            $title       = $map->name;
            $description = $map->description;

            $emailContent = $this->getEmailContent($lang, $title, $description);
            $emailSubject = $this->getEmailSubject($lang, $title);

            Mail::raw($emailContent, function ($message) use ($request, $emailSubject) {
                $message->to($request->email)
                        ->subject($emailSubject);
            });

            return response()->json([
                'success' => true,
                'message' => __('messages.Email sent successfully!')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);

        } catch (\Throwable $e) {
            \Log::error('Map email error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Server error. Please try again later.'
            ], 500);
        }
    }

    private function getEmailSubject(string $lang, string $title): string
    {
        return match ($lang) {
            'uz' => __("messages.Map Info: :title", ['title' => $title]),
            'en' => __("messages.Map Info: :title", ['title' => $title]),
            'ru' => __("messages.Map Info: :title", ['title' => $title]),
            default => "Map Info: {$title}",
        };
    }

    private function getEmailContent(string $lang, string $title, ?string $description): string
    {
        $description = $description ?: __('messages.No description available');

        return match ($lang) {
            'uz' => "Joy nomi: {$title}\n\nJoylashuv: O'zbekiston\n\nTavsif:\n{$description}\n\nBatafsil ma'lumot uchun veb-saytimizga tashrif buyuring! https://uzbekistan.travel/uz/ozbekiston-shaharlari/",
            'en' => "Location: {$title}\n\nLocation: Uzbekistan\n\nDescription:\n{$description}\n\nVisit our website for more details! https://uzbekistan.travel/en/i/uzbekistan-cities/",
            'ru' => "Название места: {$title}\n\nМестоположение: Узбекистан\n\nОписание:\n{$description}\n\nПосетите наш сайт для подробной информации! https://uzbekistan.travel/ru/i/goroda-uzbekistana/",
            default => "Location: {$title}\n\nDescription:\,{$description}",
        };
    }
}