<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // Home page
    public function home()
    {

        $categories = Category::all();

        $product = Product::with('category')->latest()->first();

        $allProducts = Product::all();
        $relatedProducts = collect(); // empty collection
        if ($product) {
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();
        }
        
        
productsByCategory = Product::with('category')
            ->get()
            ->groupBy(function($product) {
                return $product->category->name;
            });
        dd($product);

        return view('shop.show', [
            'categories'        => $categories,
            'product'           => $product,          // may be null
            'allProducts'       => $allProducts,
            'relatedProducts'   => $relatedProducts,
            'productsAvailable' => $product !== null,
            'productsByCategory'  => $productsByCategory, // NEW

        ]);
    }


    public function show($uuid)
    {

        $product = Product::with('category')
            ->where('uuid', $uuid)
            ->first();

        $allProducts = Product::all();
        $relatedProducts = collect();

        if ($product) {
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();
        }



        $productsByCategory = Product::with('category')
            ->get()
            ->groupBy(function ($product) {
                return $product->category->name;
            });
        return view('shop.show-page', compact(
            'product',
            'allProducts',
            'relatedProducts',
            'productsByCategory'
        ));
    }



    // Shop page
   /* products function index(Request $request)
    {
        $purchase = $request->query('purchase');

        $products = Product::where('is_active', 1)
            ->when($purchase, fn($q) => $q->where('folder', $purchase))
            ->orderBy('id', 'desc')
            ->paginate(10);
    //this s like this page http://127.0.0.1:8000/shop?purchase=beauty
        return view('shop.index', compact('products', 'purchase'));
    }*/





    public function index(Request $request)
    {

        // Start query for products
        $products = Product::query();
        $category = null;
        // -----------------------------
        // ISSUE: Check if category exists
        // -----------------------------
        if ($request->has('category')) {
            // Use firstOrFail to ensure we get a valid category
            $category = Category::where('uuid', $request->category)->firstOrFail();

            // Filter products by category_id
            $products->where('category_id', $category->id);
        }

        // -----------------------------
        // ISSUE: Paginate results
        // -----------------------------
        // Make sure you import use Illuminate\Pagination\Paginator;
        $products = $products->paginate(12);
        // Return view with products and category
        return view('shop.index', compact('products', 'category'));
    }



    public function category(Category $category)
    {
        // $category is already loaded by UUID
        $products = $category->products()->get();

        $categories = Category::all();

        return view('shop.index', [
            'categories' => $categories,
            'electronicsProducts' => $products,
        ]);
    }
}
