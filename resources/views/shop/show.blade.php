@extends('layouts.app')

@section('title', $product->name ?? 'Awesome Shop')

<link rel="stylesheet" href="{{ asset('css/folder-slider.css') }}">


@section('content')
    {{-- =============================
         PRODUCT HERO SECTION
         ISSUE FIXED:
         ❌ Was showing categories
         ✅ Now shows selected product
    ============================= --}}
    <div class="max-w-7xl mx-auto px-6 py-16">

        <!-- Multi Shows working up   -->
        <div class="relative flex items-center gap-3 mb-12">
            <!-- Previous Button -->
            <button id="prevFolder" class="nav-btn">◀</button>
            <!-- Category Slider -->
            <div id="folderSlider" class="flex gap-4 overflow-hidden w-full">


                @forelse($categories as $category)

                    <a href="{{ route('shop.index', ['category' => $category->uuid]) }}"
                       class="folder-item min-w-[180px] rounded-xl text-center hover:scale-105 transition-all duration-300 mt-6">

                        {{-- Badge --}}
                       {{-- <div class="text-xs uppercase tracking-wider text-indigo-500 font-semibold mb-2">
                            Category
                        </div>--}}

                        <div class="w-48 h-48 mx-auto mb-3 rounded-full overflow-hidden flex items-center justify-center border border-gray-300 bg-gray-100">

                            @php
                                $imagePath = 'products/' . basename($category->photo);
                            @endphp

                            @if($category->photo && file_exists(storage_path('app/products/' . $imagePath)))
                                <img src="{{ asset('storage/' . $imagePath) }}"
                                     alt="{{ $category->name }}"
                                     class="rounded-full object-cover w-full h-full">
                            @else
                                {{-- NO IMAGE FALLBACK --}}
                                <div class="flex flex-col items-center justify-center text-gray-400 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-10 w-10 mb-2"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M8 7h8M8 11h8M8 15h5" />
                                    </svg>
                                    No Image
                                </div>
                            @endif

                        </div>

                        <h3 class="mt-3 font-bold text-lg">
                            {{ $category->name }}
                        </h3>

                    </a>

                @empty
                    <p class="text-gray-500">No categories found</p>
                @endforelse


            </div>

            <!-- Next Button -->
            <button id="nextFolder" class="nav-btn">▶</button>
        </div>
        <!-- Multi Shows working to her -->


        {{-- NEW: Products grouped by category --}}
        @foreach($productsByCategory as $categoryName => $products)
            <div class="category-section">
                <h2 class="related-title mb-6 mt-6" style="margin-top: 4pc;">{{ $categoryName }}</h2>
                <div class="related-grid">
                    @foreach($products as $product)
                        <div class="related-card">

                            <div class="related-image">
                                <img src="{{ $product->image && file_exists(storage_path('app/products/' . $product->image))
                            ? asset('storage/' . $product->image)
                            : 'https://picsum.photos/300?random=' . rand(1,1000) }}"
                                     alt="{{ $product->name }}">
                            </div>
                            <div class="related-content">
                                <h3 class="related-name">{{ $product->name }}</h3>
                                <p class="related-price">₹ {{ number_format($product->price) }}</p>


                                <div class="related-actions">
                                    <a href="{{ route('cart.add', $product->uuid) }}"
                                       class="make-payment-btn">
                                        <i class="fas fa-shopping-bag buy-icon"></i> Buy →
                                    </a>

                                    <a href="{{ route('shop.show', $product->uuid) }}"
                                       class="show-product-details">
                                        <i class="fas fa-eye show-icon"></i> Show →
                                    </a>
                                </div>



                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if($relatedProducts->count())
            <div class="related-wrapper">

                <h2 class="related-title">
                    Related Products
                </h2>

                <div class="related-grid">

                    @foreach($relatedProducts as $related)
                        <div class="related-card">

                            <div class="related-image">
                                <img src="{{ $related->image && file_exists(storage_path('app/products/' . $related->image))
                            ? asset('storage/' . $related->image)
                            : 'https://picsum.photos/300?random=' . rand(1,1000) }}"
                                     alt="{{ $related->name }}">
                            </div>
                            <div class="related-content">
                                <h3 class="related-name">{{ $related->name }}</h3>
                                <p class="related-price">₹ {{ number_format($related->price) }}</p>
                                <div class="related-actions">
                                    <a href="{{ route('cart.add', $product->uuid) }}"
                                       class="make-payment-btn">
                                        <i class="fas fa-shopping-bag buy-icon"></i> Buy
                                    </a>

                                    <a href="{{ route('shop.show', $product->uuid) }}"
                                       class="show-product-details">
                                        <i class="fas fa-eye show-icon"></i> Show
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>
        @endif




    @if($product)

            <div class="product-wrapper">

                <!-- PRODUCT IMAGE -->
                <div class="product-image">
                    <img src="{{ $product->image && file_exists(storage_path('app/products/' . $product->image))
                ? asset('storage/' . $product->image)
                : 'https://picsum.photos/600?random=' . rand(1,1000) }}"
                         alt="{{ $product->name }}">
                </div>

                <!-- PRODUCT DETAILS -->
                <div class="product-details">

                    <h1 class="product-title">{{ $product->name }}</h1>

                    <p class="product-category">
                        Category • <strong>{{ $product->category->name }}</strong>
                    </p>

                    <p class="product-description">
                        {{ $product->description ?? 'No description available.' }}
                    </p>

                    <div class="price-section">
                        <span class="price-label">Price</span>
                        <span class="price-amount">₹ {{ number_format($product->price, 2) }}</span>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('cart.add', $product->uuid) }}" class="btn-primary">
                            🛒 Add to Cart
                        </a>
                    </div>

                </div>
            </div>

        @endif

        {{-- =============================
             RELATED PRODUCTS
             ISSUE FIXED:
             ❌ No related products before
             ✅ Uses $relatedProducts from controller
        ============================= --}}

    </div>

@endsection

    <script src="{{ asset('js/folder-slider.js') }}"></script>



