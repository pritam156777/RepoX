<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('admin_id', auth()->id())->latest()->get();

        $categories = Category::all(); // ✅ ADD THIS

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all(); // ✅ FETCH CATEGORIES

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ✅ Attach logged-in admin
        $data['admin_id'] = auth()->id();

        // ✅ Handle checkbox safely
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // ✅ Image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'products');
        }

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully!');
    }


    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity'   => 'required|integer'
        ]);

        $product = Product::findOrFail($request->product_id);

        $product->stock = max(0, $product->stock + $request->quantity);
        $product->save();

        return response()->json([
            'success' => true,
            'stock'   => $product->stock
        ]);
    }

  public function destroyStock(string $uuid)
    {
        $product = Product::where('uuid', $uuid)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true
        ]);
    }




    public function edit(Product $product)
    {
        $this->authorize('update', $product); // Make sure admin owns this product
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'is_active' => 'required|boolean',
            'image' => 'nullable|image'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'products');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated!');
    }

    public function destroy(Product $product)
    {
       $product = Product::where('uuid', $uuid)->firstOrFail();

        $productName = $product->name; // save name for message
        $product->delete();

        // Store flash message in session for next page reload
        session()->flash('delete_success', "✅ Product '{$productName}' deleted successfully.");

        return response()->json([
            'success' => true,
            'message' => "✅ Product '{$productName}' deleted successfully"
        ]);
    }
}
