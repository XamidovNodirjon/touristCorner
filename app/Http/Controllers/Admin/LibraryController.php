<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\LibraryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    public function index()
    {
        $libraries = Library::latest()->get();
        $categories = LibraryCategory::all();
        return view('admin.libraries.index', [
            'libraries' => $libraries,
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_uz' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_uz' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'image' => 'nullable|image|max:10240', // 10 MB
            'file_path_ru' => 'nullable|url|max:255',
            'file_path_en' => 'nullable|url|max:255',
            'file_path_uz' => 'nullable|url|max:255',
            'category_id' => 'nullable|integer|exists:library_categories,id',
        ]);

        $library = new Library();
        $library->title_uz = $request->title_uz;
        $library->title_en = $request->title_en;
        $library->title_ru = $request->title_ru;
        $library->description_uz = $request->description_uz;
        $library->description_en = $request->description_en;
        $library->description_ru = $request->description_ru;
        $library->category_id = $request->category_id;

        if ($request->hasFile('image')) {
            $library->image = $request->file('image')->store('libraries/images', 'public');
        }

        // Fayl o‘rniga URL saqlanadi
        $library->file_path_ru = $request->file_path_ru;
        $library->file_path_en = $request->file_path_en;
        $library->file_path_uz = $request->file_path_uz;

        $library->save();

        return redirect()->back()->with('success', 'Yangi ma’lumot muvaffaqiyatli qo‘shildi!');
    }


    public function update(Request $request, $id)
    {
        $library = Library::findOrFail($id);

        $validated = $request->validate([
            'title_uz' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'description_uz' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'image' => 'nullable|image|max:10240', // 10 MB
            'file_path_ru' => 'nullable|url|max:255',
            'file_path_en' => 'nullable|url|max:255',
            'file_path_uz' => 'nullable|url|max:255',
            'category_id' => 'nullable|integer|exists:library_categories,id',
        ]);

        // 🔹 Faqat requestda kelgan maydonlarni yangilaymiz
        foreach ($validated as $key => $value) {
            if ($key === 'image' && $request->hasFile('image')) {
                if ($library->image && Storage::disk('public')->exists($library->image)) {
                    Storage::disk('public')->delete($library->image);
                }
                $library->image = $request->file('image')->store('libraries/images', 'public');
            } elseif ($key !== 'image' && $value !== null) {
                $library->$key = $value;
            }
        }

        $library->save();

        return redirect()->back()->with('success', 'Ma’lumot muvaffaqiyatli yangilandi!');
    }



    public function destroy($id)
    {
        $library = Library::findOrFail($id);

        if ($library->image && Storage::disk('public')->exists($library->image)) {
            Storage::disk('public')->delete($library->image);
        }
        $library->delete();

        return redirect()->back()->with('success', 'Ma’lumot muvaffaqiyatli o‘chirildi!');
    }
}
