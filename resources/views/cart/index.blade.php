<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shopping Cart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($groupedItems->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    Your cart is empty. <a href="{{ route('products.index') }}"
                        class="text-indigo-600 hover:text-indigo-900 font-semibold">Start shopping!</a>
                </div>
            @else
                @foreach ($groupedItems as $vendorName => $items)
                    <div class="mb-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-indigo-50 border-b border-indigo-100 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-indigo-900">Vendor: {{ $vendorName }}</h3>
                        </div>
                        <div class="p-6 bg-white space-y-4">
                            @foreach ($items as $item)
                                <div class="flex items-center justify-between border-b pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}"
                                            class="w-16 h-16 object-cover rounded shadow-sm">
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $item->product->name }}</h4>
                                            <p class="text-sm text-gray-500">
                                                ₹{{ number_format($item->product->price, 2) }} each</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-6">
                                        <form action="{{ route('cart.update', $item->product_id) }}" method="POST"
                                            class="flex items-center space-x-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}"
                                                min="1" max="{{ $item->product->stock }}"
                                                class="w-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                            <button type="submit"
                                                class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">Update</button>
                                        </form>

                                        <div class="text-right min-w-[100px]">
                                            <p class="font-bold text-gray-900">
                                                ₹{{ number_format($item->product->price * $item->quantity, 2) }}</p>
                                        </div>

                                        <form action="{{ route('cart.destroy', $item->product_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-4 bg-gray-50 flex justify-end items-center">
                            <span class="text-sm text-gray-500 mr-2">Vendor Total:</span>
                            <span
                                class="text-lg font-bold text-gray-900">₹{{ number_format($items->sum(fn($i) => $i->product->price * $i->quantity), 2) }}</span>
                        </div>
                    </div>
                @endforeach

                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div class="text-2xl font-bold text-gray-900">Grand Total:</div>
                        <div class="text-3xl font-extrabold text-indigo-600">₹{{ number_format($grandTotal, 2) }}</div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        @auth
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:-translate-y-1">
                                Proceed to Checkout
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition transform hover:-translate-y-1 inline-block text-center">
                                Login to Checkout
                            </a>
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
