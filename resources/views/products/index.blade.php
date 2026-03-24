<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                        <div class="p-4 bg-gray-50 border-t border-gray-100 mt-auto">
                            @if ($product->stock > 0)
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <div class="flex items-center space-x-2">
                                        <input type="number" name="quantity" value="1" min="1"
                                            max="{{ $product->stock }}"
                                            class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                        <button type="submit"
                                            class="flex-grow bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition text-sm shadow-sm">
                                            Add to Cart
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div
                                    class="w-full bg-gray-200 text-gray-500 font-bold py-2 px-4 rounded text-sm text-center cursor-not-allowed">
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
</x-app-layout>
