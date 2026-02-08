@extends('layouts.app')

@section('content')
    <div class="">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-6 py-3 rounded-lg mb-6 shadow-lg">
                    <p class="font-semibold tracking-wide">
                        ✅ {{ session('success') }}
                    </p>
                </div>
            @endif

            <!-- Category Form Card -->
            <div class="bg-gradient-to-br  via-gray-900  rounded-3xl shadow-2xl p-10 md:p-16 hover:shadow-3xl transition-all" style="background: #15160e;">
                <h3 class="text-3xl font-extrabold text-white mb-3 text-center tracking-wide">
                    Create a New Category
                </h3>

                <p class="text-center text-gray-400 mb-8 text-sm">
                    Organize your products better by adding a clear and attractive category
                </p>

                <form action="{{ route('super-admin.categories.store') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="space-y-6">
                    @csrf

                    <!-- Category Name -->
                    <div>
                        <label class="block text-gray-300 font-semibold mb-2">
                            Category Name
                        </label>

                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border border-gray-600 bg-gray-900 text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                               placeholder="e.g. Electronics, Furniture, Accessories">

                        <p class="text-gray-500 text-xs mt-1">
                            Use a short, clear name customers can recognize instantly
                        </p>

                        @error('name')
                        <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-gray-300 font-semibold mb-2">
                            Category Photo
                        </label>

                        <input type="file" name="photo" id="category-photo"
                               accept="image/*"
                               required
                               class="hidden">

                        <div id="image-upload-box"
                             class="cursor-pointer flex flex-col items-center justify-center border-2 border-dashed border-gray-500 rounded-xl h-40 bg-gray-900 hover:bg-gray-800 transition relative">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 style="height: 2pc"
                                 class="h-12 w-12 text-gray-400 mb-2"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3v4M8 3v4m8 0H8" />
                            </svg>

                            <span class="text-gray-400 text-sm">
                                Click to upload category image
                            </span>

                            <span class="text-gray-500 text-xs mt-1">
                                JPG, PNG or WEBP (recommended)
                            </span>
                        </div>

                        <div class="mt-4">
                            <img id="image-preview"
                                 class="rounded-xl w-full max-h-60 object-cover hidden border border-gray-600 shadow-lg">
                        </div>

                        @error('photo')
                        <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between items-center pt-4">
                        <a href="{{ route('super-admin.dashboard') }}"
                           class="cancel-btn px-6 py-3 rounded-xl font-semibold shadow-md">
                            ← Cancel
                        </a>

                        <button type="submit"
                                style="width: 12pc;"
                                class="submit-btn px-10 py-3 rounded-xl font-bold shadow-lg">
                            + Create Category
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>


    @if(isset($categories) && $categories->count() > 0)
        <div class="mt-10 grid grid-cols-3 gap-6">
            @foreach($categories as $category)
                <div class="border rounded-xl overflow-hidden shadow-lg bg-gray-900 text-white">
                    <img src="{{ $category->photo ? asset('storage/' . $category->photo) : 'https://via.placeholder.com/150' }}"
                         alt="{{ $category->name }}"
                         class="w-full h-40 object-cover">
                    <div class="p-4 text-center">
                        <h3 class="font-bold text-lg">{{ $category->name }}</h3>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        $(document).ready(function () {

            $('#image-upload-box').click(function () {
                $('#category-photo').click();
            });

            $('#category-photo').change(function (e) {
                if (e.target.files.length === 0) return;

                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#image-preview')
                        .attr('src', e.target.result)
                        .removeClass('hidden');
                };
                reader.readAsDataURL(e.target.files[0]);
            });

        });
    </script>

    <style>
        #image-preview {
            width: 25pc;
        }

        .submit-btn {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: scale(1.05);
            background: linear-gradient(45deg, #2575fc, #6a11cb);
            box-shadow: 0 10px 20px rgba(0,0,0,0.4);
        }

        .cancel-btn {
            background: linear-gradient(45deg, #434343, #000000);
            color: #fff;
            transition: all 0.3s ease;
        }

        .cancel-btn:hover {
            transform: scale(1.05);
            background: linear-gradient(45deg, #000000, #434343);
            box-shadow: 0 10px 20px rgba(0,0,0,0.4);
        }
    </style>
@endsection
