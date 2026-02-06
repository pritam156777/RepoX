@extends('layouts.app')
@section('content')



    <div class="views-shop-index-wrapper max-w-7xl mx-auto px-4 py-24">

<<<<<<< HEAD
=======

>>>>>>> 9cab5cf9e96d80ee4f131c4c03a7227d3aeeb65b
        @auth
            @if(auth()->user()->role === 'super_admin')


                <h1 class="text-3xl font-bold mb-4">
                    {{ $category ? $category->name : 'Shop' }}
                </h1>

                {{-- CATEGORY IMAGE --}}
                @if($category && $category->photo)
                    <img src="{{ asset('storage/' . $category->photo) }}"
                         class="h-40 rounded-lg mb-4">
                @endif



                <form style="margin-bottom: 5pc" action="{{ route('categories.photo.update', $category->uuid) }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <input type="file" name="photo" required>

                    <button type="submit" class="btn btn-primary">
                        Update Category Photo
                    </button>

                </form>

            @endif
        @endauth

<<<<<<< HEAD
=======




>>>>>>> 9cab5cf9e96d80ee4f131c4c03a7227d3aeeb65b
    @if($products->count())

            <div class="views-shop-index-grid">

                @foreach($products as $product)

                    {{-- ================= PRODUCT CARD ================= --}}
                    <div class="views-shop-index-card group">

                        {{-- PRODUCT IMAGE --}}
                        <div class="relative overflow-hidden">
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="image-height">

                            @if($product->stock == 0)
                                <span class="views-shop-index-badge bg-red-500">
                                Out of Stock
                            </span>
                            @else
                                <span class="views-shop-index-badge new-all-trigger"
                                      data-bs-toggle="modal"
                                      data-bs-target="#productQuickViewModal-{{ $product->id }}">
                                New All
                            </span>
                            @endif
                        </div>

                        {{-- PRODUCT INFO --}}
                        <div class="views-shop-index-info flex flex-col justify-between">

                            <h3>{{ $product->name }}</h3>

                            <p class="category">
                                {{ $product->category->name }}
                            </p>

                            <p class="description text-gray-600 text-sm mt-2">
                                {{ Str::limit($product->description, 100, '...') }}
                            </p>

                            <p class="price mt-3">
                                ₹ {{ number_format($product->price) }}
                            </p>

                            @if($product->stock > 0)
                                <div class="related-actions">
                                    <a href="{{ route('cart.add', $product->uuid) }}"
                                       class="make-payment-btn">
                                        <i class="fas fa-shopping-bag buy-icon" style="margin:4px 6px;"></i>
                                        Buy →
                                    </a>

                                    <a href="{{ route('shop.show', $product->uuid) }}"
                                       class="show-product-details">
                                        <i class="fas fa-eye show-icon" style="margin:4px 6px;"></i>
                                        Show →
                                    </a>
                                </div>
                            @else
                                <button class="btn-view-product mt-4 bg-gray-400 cursor-not-allowed" disabled>
                                    Out of Stock
                                </button>
                            @endif

                        </div>
                    </div>
                    {{-- ================= END CARD ================= --}}

                    {{-- ================= QUICK VIEW MODAL ================= --}}
                        <div class="modal fade"
                             id="productQuickViewModal-{{ $product->id }}"
                             tabindex="-1"
                             aria-hidden="true"
                        >

                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="">

                                <div class="modal-body p-0">

                                    <section class="product-wrapper ">

                                        <div class="row align-items-center g-5" style="width: max-content">

                                            {{-- IMAGE --}}
                                            <div class="col-md-6 text-center">
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                     alt="{{ $product->name }}"
                                                     class="img-fluid rounded-4 shadow-lg">
                                            </div>

                                            {{-- DETAILS --}}
                                            <div class="col-md-6 product-details">

                                                <h1 class="product-title mb-3">
                                                    {{ $product->name }}
                                                </h1>

                                                <p class="product-category mb-2">
                                                    Category •
                                                    <strong>{{ $product->category->name }}</strong>
                                                </p>

                                                <p class="product-description mb-4 text-muted">
                                                    {{ $product->description ?? 'No description available.' }}
                                                </p>

                                                <div class="price-section mb-4">
                                                <span class="price-label text-muted d-block">
                                                    Price
                                                </span>
                                                    <span class="price-amount fs-3 fw-bold text-primary">
                                                    ₹ {{ number_format($product->price, 2) }}
                                                </span>
                                                </div>

                                                <div class="action-buttons d-flex gap-3">

                                                    <i class="btn btn-outline-secondary rounded-pill px-4"
                                                            >
                                                        Your complete information is showing here.
                                                    </i>
                                                </div>

                                            </div>

                                        </div>

                                    </section>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ================= END MODAL ================= --}}

                @endforeach

            </div>

            {{-- PAGINATION --}}
            <div class="mt-20">
                {{ $products->links() }}
            </div>

        @else
            <div class="views-shop-index-empty">
                <p>😔 No products available</p>
            </div>
        @endif

    </div>

@endsection

{{-- EXTRA ASSETS --}}
<link rel="stylesheet" href="{{ asset('css/views-shop-index.css') }}">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/views-shop-index.js') }}"></script>
