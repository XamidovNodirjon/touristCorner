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
            
            $lang = $request->input('lang', 'ru');
            $request->validate([
                'email' => 'required|email',
                'material_id' => 'required|integer|exists:libraries,id',
                // bu yerda endi 'required' shart emas, faqat valid qiymat
                'lang' => 'nullable|in:ru,en'
            ]);

            $material = Library::find($request->material_id);

            // Fayl yo‘li tilga qarab tanlanadi
            $fileColumn = $lang === 'ru' ? 'file_path_ru' : 'file_path_en';
            $filePath = storage_path('app/public/' . $material->$fileColumn);

            if (!file_exists($filePath)) {
                return response()->json(['error' => 'Fayl topilmadi'], 404);
            }

            Mail::raw("Siz so‘ragan material: {$material->title_uz}", function ($message) use ($request, $material, $filePath) {
                $message->to($request->email)
                    ->subject("Material: {$material->title_uz}")
                    ->attach($filePath);
            });

            return response()->json(['success' => true, 'message' => 'Material email orqali yuborildi.']);

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
            Vaqt: {$event['time']}
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

}
