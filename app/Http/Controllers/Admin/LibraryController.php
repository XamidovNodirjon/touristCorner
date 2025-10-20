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
            'title_uz' => 'required|string|max:255',
            'title_ru' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_uz' => 'required|string',
            'description_en' => 'required|string',
            'description_ru' => 'required|string',
            'image' => 'nullable|image|max:10240', // 10 MB
            'file_path_ru' => 'required|file|max:102400', // 100 MB
            'file_path_en' => 'required|file|max:102400', // 100 MB
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

        if ($request->hasFile('file_path_ru')) {
            $library->file_path_ru = $request->file('file_path_ru')->store('libraries/files_ru', 'public');
        }

        if ($request->hasFile('file_path_en')) {
            $library->file_path_en = $request->file('file_path_en')->store('libraries/files_en', 'public');
        }

        $library->save();

        return redirect()->back()->with('success', 'Yangi ma’lumot muvaffaqiyatli qo‘shildi!');
    }

    public function update(Request $request, $id)
    {
        $library = Library::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description_uz' => 'required|string',
            'description_en' => 'required|string',
            'description_ru' => 'required|string',
            'image' => 'nullable|image|max:10240',
            'file_path_ru' => 'nullable|file|max:102400',
            'file_path_en' => 'nullable|file|max:102400',
        ]);

        $library->title = $request->title;
        $library->description_uz = $request->description_uz;
        $library->description_en = $request->description_en;
        $library->description_ru = $request->description_ru;

        if ($request->hasFile('image')) {
            if ($library->image && Storage::disk('public')->exists($library->image)) {
                Storage::disk('public')->delete($library->image);
            }
            $library->image = $request->file('image')->store('libraries/images', 'public');
        }

        if ($request->hasFile('file_path_ru')) {
            if ($library->file_path_ru && Storage::disk('public')->exists($library->file_path_ru)) {
                Storage::disk('public')->delete($library->file_path_ru);
            }
            $library->file_path_ru = $request->file('file_path_ru')->store('libraries/files_ru', 'public');
        }

        if ($request->hasFile('file_path_en')) {
            if ($library->file_path_en && Storage::disk('public')->exists($library->file_path_en)) {
                Storage::disk('public')->delete($library->file_path_en);
            }
            $library->file_path_en = $request->file('file_path_en')->store('libraries/files_en', 'public');
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

        if ($library->file_path_ru && Storage::disk('public')->exists($library->file_path_ru)) {
            Storage::disk('public')->delete($library->file_path_ru);
        }

        if ($library->file_path_en && Storage::disk('public')->exists($library->file_path_en)) {
            Storage::disk('public')->delete($library->file_path_en);
        }

        $library->delete();

        return redirect()->back()->with('success', 'Ma’lumot muvaffaqiyatli o‘chirildi!');
    }
}
