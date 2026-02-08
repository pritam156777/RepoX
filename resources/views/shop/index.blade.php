@extends('layouts.app')
@section('content')



    <div class="views-shop-index-wrapper max-w-7xl mx-auto px-4">

        @auth
            @if(auth()->user()->role === 'super_admin')

                <link rel="stylesheet" href="{{ asset('css/modern-category-card.css') }}">

                <div class="modern-category-wrapper" style="margin-top: -7pc">

                    <div class="modern-category-card">

                        {{-- HEADER --}}
                        <div class="modern-card-header">
                            <h1>{{ $category ? $category->name : 'Shop' }}</h1>
                        </div>

                        {{-- IMAGE --}}
                        <div class="modern-image-box">
                            @if($category && $category->photo)
                                <img id="imagePreview"
                                     src="{{ asset('storage/' . $category->photo) }}"
                                     alt="Category Image">
                            @else
                                <div class="modern-image-placeholder">
                                    No Image Available
                                </div>
                            @endif
                        </div>

                        {{-- UPDATE FORM --}}
                        <form id="updatePhotoForm"
                              action="{{ route('categories.photo.update', $category->uuid) }}"
                              method="POST"
                              enctype="multipart/form-data">

                            @csrf
                            @method('PUT')

                            <label class="modern-upload-btn">
                                Select New Image
                                <input type="file"
                                       name="photo"
                                       id="photoInput"
                                       accept="image/*"
                                       hidden
                                       required>
                            </label>

                            {{-- ACTION BUTTONS --}}
                            <div class="modern-action-row">

                                <button type="submit" class="btn-update">
                                    ✨ Update Photo
                                </button>
                                <button type="button"
                                        class="btn-delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmModal"
                                        data-category="{{ $category->name }}">
                                    🗑 Delete
                                </button>


                            </div>

                        </form>




                    </div>

                </div>



                <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content delete-modal">

                            <div class="modal-header border-0">
                                <h5 class="modal-title text-danger">⚠ Confirm Deletion</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body text-center">
                                <p class="delete-text">
                                    Are you sure you want to delete
                                    <strong id="deleteCategoryName"></strong>?
                                </p>
                                <small class="text-muted">
                                    This action is reversible (soft delete).
                                </small>
                            </div>

                            <div class="modal-footer border-0 justify-content-center gap-3">

                                <button type="button"
                                        class="btn btn-warning px-4"
                                        data-bs-dismiss="modal">
                                    ❌ No
                                </button>

                                <form id="deleteCategoryForm"
                                      action="{{ route('super-admin.categories.destroy', $category) }}"
                                      method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger px-4">
                                        ✅ Yes, Delete
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>


            @endif
        @endauth


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
