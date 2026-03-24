<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline font-medium text-sm">{{ session('success') }}</span>
                    <a href="{{ route('cart.index') }}"
                        class="ml-2 font-bold underline hover:text-green-900 transition-colors text-sm">View Cart
                        &rarr;</a>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        <div class="p-6 bg-white border-b border-gray-200 flex-grow flex flex-col">
                            <div class="text-sm text-gray-500 mb-1">{{ $product->vendor->name }}</div>
                            <h3 class="text-lg font-bold mb-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $product->description }}</p>

                            <div class="flex justify-between items-center mt-auto">
                                <div class="text-xl font-semibold text-indigo-600">
                                    ₹{{ number_format($product->price, 2) }}
                                </div>
                                <div class="text-xs font-medium">
                                    @if ($product->stock > 0)
                                        <span
                                            class="px-2 py-1 bg-green-100 text-green-700 rounded-full font-bold uppercase tracking-wide text-[10px]">
                                            In Stock: {{ $product->stock }}
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 bg-red-100 text-red-700 rounded-full font-bold uppercase tracking-wide text-[10px]">
                                            Out of Stock
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 border-t border-gray-100 mt-auto text-center">
                            @php
                                $cartQty = $cartQuantities[$product->id] ?? 0;
                                $isMaxed = $cartQty >= $product->stock;
                            @endphp

                            @if ($product->stock > 0)
                                @if ($isMaxed)
                                    <div
                                        class="w-full h-10 flex items-center justify-center bg-indigo-50 text-indigo-700 font-black text-xs uppercase tracking-widest rounded-lg border border-indigo-100 shadow-inner">
                                        Max Quantity in Cart
                                    </div>
                                @else
                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div class="flex items-center space-x-2">
                                            <div
                                                class="flex items-center bg-gray-100 rounded-lg p-1 border border-gray-200 shadow-sm h-10">
                                                <button type="button"
                                                    onclick="const quantityInput = this.parentNode.querySelector('input[type=number]'); quantityInput.stepDown();"
                                                    class="w-8 h-8 flex items-center justify-center text-indigo-600 hover:text-indigo-800 transition transform active:scale-95">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M20 12H4" />
                                                    </svg>
                                                </button>

                                                <input type="number" name="quantity" value="1" min="1"
                                                    max="{{ $product->stock - $cartQty }}"
                                                    class="w-10 bg-transparent border-none text-center font-bold text-gray-800 focus:ring-0 p-0 text-sm">

                                                <button type="button"
                                                    onclick="const quantityInput = this.parentNode.querySelector('input[type=number]'); quantityInput.stepUp();"
                                                    class="w-8 h-8 flex items-center justify-center text-indigo-600 hover:text-indigo-800 transition transform active:scale-95">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <button type="submit"
                                                class="flex-grow h-10 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 rounded-lg transition text-xs uppercase tracking-widest shadow-md hover:shadow-lg transform active:scale-95">
                                                Add
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            @else
                                <div
                                    class="w-full bg-gray-200 text-gray-500 font-bold py-2 px-4 rounded-lg text-xs uppercase tracking-widest text-center cursor-not-allowed border border-gray-300">
                                    Unavailable
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
    </div>
    </div>
</x-app-layout>
