<?php

// app/Http/Controllers/SuperAdmin/CategoryController.php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
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


    public function destroy(Category $category)
    {
        // Extra safety check
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized');
        }

        $deletedName = $category->name; // Store the name for flash message

        // Delete image from storage if exists
        if ($category->photo && Storage::disk('products')->exists($category->photo)) {
            Storage::disk('products')->delete($category->photo);
        }

        // Delete category record
        $category->delete();

        // Redirect to home with success message
        return redirect()->route('home')->with('success', "Category '{$deletedName}' deleted successfully!");

    }

        public function updatePhoto(Request $request, Category $category)
        {
            $request->validate([
                'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($request->hasFile('photo')) {

                // 🔥 Delete old photo (storage + public)
                if ($category->photo) {
                    $oldStoragePath = storage_path('app/products/' . $category->photo);
                    $oldPublicPath  = public_path('images/storage/' . $category->photo);

                    if (file_exists($oldStoragePath)) {
                        unlink($oldStoragePath);
                    }

                    if (file_exists($oldPublicPath)) {
                        unlink($oldPublicPath);
                    }
                }

                $file = $request->file('photo');

                // Same filename logic as store()
                $filename = now()->format('l_Y-m-d_H-i-s')
                    . '--'
                    . rand(1000, 9999)
                    . '.'
                    . $file->getClientOriginalExtension();

                // Store in storage/app/products/products
                $path = $file->storeAs('products', $filename, 'products');

                // Ensure public directory exists
                $publicDir = public_path('images/storage/products');
                if (!file_exists($publicDir)) {
                    mkdir($publicDir, 0755, true);
                }

                // Copy to public folder
                copy(
                    storage_path('app/products/' . $path),
                    public_path('images/storage/' . $path)
                );

                // Update DB
                $category->update([
                    'photo' => $path, // products/filename.jpg
                ]);
            }

            return back()->with('success', 'Category photo updated successfully');
        }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|unique:categories,name',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $path = null;

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');

            // Unique filename
            $filename = now()->format('l_Y-m-d_H-i-s')
                . '--'
                . rand(1000, 9999)
                . '.'
                . $image->getClientOriginalExtension();

            // Store file in storage/app/products
            $path = $image->storeAs('products', $filename, 'products');

            // Ensure public directory exists
            $publicDir = public_path('images/storage/products');
            if (!file_exists($publicDir)) {
                mkdir($publicDir, 0755, true);
            }

            // Copy file to public directory
            copy(
                storage_path('app/products/' . $path),
                public_path('images/storage/products/' . $filename)
            );
        }

        Category::create([
            'name'  => $request->name,
            'photo' => $path, // stores only filename
        ]);

        return redirect()->back()->with('success', 'Category created successfully!');
    }

}
