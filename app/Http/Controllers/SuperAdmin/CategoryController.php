<?php

// app/Http/Controllers/SuperAdmin/CategoryController.php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function create()
    {
        return view('super-admin.categories.create');
    }


    public function store(Request $request)
    {
       $request->validate([
        'name'  => 'required|string|max:255',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $photoPath = null;

    if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')
            ->store('categories', 'public');
    }

    Category::create([
        'name'  => $request->name,
        'photo' => $photoPath,
    ]);

    return redirect()
        ->route('super-admin.categories.create')
        ->with('success', 'Category created successfully');
    }


    public function destroy(Category $category)
    {
        // Extra safety check (optional but recommended)
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized');
        }

        // Delete image from storage if exists
        if ($category->photo && Storage::disk('products')->exists($category->photo)) {
            Storage::disk('products')->delete($category->photo);
        }

        // Delete category record
        $category->delete();

        return redirect()->back()->with('success', 'Category and image deleted successfully.');
    }

        public function updatePhoto(Request $request, Category $category)
    {
        // 🔐 Super admin check
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $path = $category->photo; // keep old photo

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');

            // 🗑 Delete old files
            if ($category->photo) {

                $oldStorage = storage_path('app/products/' . $category->photo);
                if (file_exists($oldStorage)) {
                    unlink($oldStorage);
                }

                $oldPublic = public_path('images/storage/' . $category->photo);
                if (file_exists($oldPublic)) {
                    unlink($oldPublic);
                }
            }

            // 📛 Filename (same format you use)
            $filename = now()->format('l_Y-m-d_H-i-s')
                . '--'
                . rand(1000, 9999)
                . '.'
                . $image->getClientOriginalExtension();

            // 📦 Store
            $path = $image->storeAs('products', $filename, 'products');

            // 🔁 Mirror
            copy(
                storage_path('app/products/' . $path),
                public_path('images/storage/' . $path)
            );
        }

        // 💾 Update ONLY photo
        $category->update([
            'photo' => $path,
        ]);

        return back()->with('success', 'Category photo updated successfully!');
    }



}
