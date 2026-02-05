    @extends('layouts.app')

    @section('content')
        <div class="container">
            <h2 class="mb-4">🛒 My Products</h2>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Add Product Form --}}
            <div class="card mb-4">
                <div class="card-header">➕ Add New Product</div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="bg-white shadow rounded-lg p-6 space-y-4">

                        @csrf

                        <!-- PRODUCT NAME -->
                        <div>
                            <label class="block font-semibold mb-1">Product Name</label>
                            <input type="text" name="name"
                                   class="w-full border rounded px-3 py-2"
                                   required>
                        </div>

                        <!-- CATEGORY -->
                        <div>
                            <label class="block font-semibold mb-1">Category</label>
                            <select name="category_id"
                                    class="w-full border rounded px-3 py-2"
                                    required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- DESCRIPTION -->
                        <div>
                            <label class="block font-semibold mb-1">Description</label>
                            <textarea name="description"
                                      class="w-full border rounded px-3 py-2"
                                      rows="3"></textarea>
                        </div>

                        <!-- PRICE -->
                        <div>
                            <label class="block font-semibold mb-1">Price (₹)</label>
                            <input type="number" name="price"
                                   class="w-full border rounded px-3 py-2"
                                   required>
                        </div>

                        <!-- STATUS -->
                        <div>
                            <label class="block font-semibold mb-1">Active</label>
                            <select name="is_active"
                                    class="w-full border rounded px-3 py-2"
                                    required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <!-- IMAGE -->
                        <div>
                            <label class="block font-semibold mb-1">Product Image</label>
                            <input type="file" name="image"
                                   class="w-full border rounded px-3 py-2">
                        </div>

                        <!-- SUBMIT -->
                        <div class="pt-4">
                            <button type="submit"
                                    class="relative inline-block px-8 py-3 font-semibold text-white rounded-lg shadow-lg overflow-hidden group transition-all duration-300 ease-in-out"
                                    style="width: 25%;background: linear-gradient(90deg, rgba(42, 123, 155, 1) 0%, rgba(87, 199, 133, 1) 50%, rgba(237, 221, 83, 1) 100%); background-size: 300% 300%;">

                                <span class="relative z-10">Save Product</span>

                                <!-- Hover overlay animation -->
                                <span class="absolute inset-0 bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 opacity-0 group-hover:opacity-50 transition-opacity duration-500 rounded-lg"></span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            {{-- Products Table --}}
            <div class="card">
                <div class="card-header">📦 My Products List</div>
                <div class="card-body">
                    @if($products->count() === 0)
                        <p>No products yet.</p>
                    @else
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Manage Stock</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>₹ {{ number_format($product->price) }}</td>
    
                                    <td>
                                <span class="badge bg-primary fs-6"
                                      id="stock-{{ $product->id }}">
                                    {{ $product->stock }}
                                </span>
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-danger btn-sm"
                                                    onclick="changeStock({{ $product->id }}, -1)">−</button>

                                            <input type="number" id="qty-{{ $product->id }}"
                                                   class="form-control text-center"
                                                   style="width:70px" value="1" min="1">

                                            <button class="btn btn-success btn-sm"
                                                    onclick="changeStock({{ $product->id }}, 1)">+</button>

                                            <button class="btn btn-info btn-sm"
                                                    onclick="updateStock({{ $product->id }})">
                                                Update
                                            </button>
                                        </div>
                                    </td>

                                    <td>
                                        @if($product->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    @endsection
    <script>
        function changeStock(id, type) {
            let qty = parseInt($('#qty-'+id).val()) || 1;
            sendStock(id, type === -1 ? -qty : qty);
        }

        function updateStock(id) {
            let qty = parseInt($('#qty-'+id).val());
            if (!qty) return alert('Invalid quantity');
            sendStock(id, qty);
        }

        function sendStock(id, qty) {
            $.post("{{ route('products.updateStock') }}", {
                _token: "{{ csrf_token() }}",
                product_id: id,
                quantity: qty
            }, function(res){
                if(res.success){
                    $('#stock-'+id).text(res.stock);
                }
            });
        }
    </script>
