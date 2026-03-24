<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shopping Cart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($groupedItems->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    Your cart is empty. <a href="{{ route('products.index') }}"
                        class="text-indigo-600 hover:text-indigo-900 font-semibold">Start shopping!</a>
                </div>
            @else
                <div class="space-y-8">
                    @foreach ($groupedItems as $vendorId => $items)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                            <div class="p-4 bg-indigo-50 border-b border-indigo-100 flex justify-between items-center">
                                <h3 class="text-lg font-bold text-indigo-900">
                                    Vendor: {{ $items->first()->product->vendor->name }}
                                </h3>
                            </div>
                            <div class="p-6 bg-white space-y-6">
                                @foreach ($items as $item)
                                    <div
                                        class="flex flex-col sm:flex-row items-center justify-between border-b pb-6 last:border-0 last:pb-0">
                                        <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                                            <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}"
                                                class="w-20 h-20 object-cover rounded-lg shadow-sm border border-gray-100">
                                            <div>
                                                <h4 class="text-lg font-bold text-gray-900">{{ $item->product->name }}
                                                </h4>
                                                <p class="text-sm text-gray-500">
                                                    ₹{{ number_format($item->product->price, 2) }} each</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-6">
                                            <form action="{{ route('cart.update', $item->product_id) }}" method="POST"
                                                class="flex flex-col items-end">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="product_id"
                                                    value="{{ $item->product_id }}">
                                                <div
                                                    class="flex items-center bg-gray-100 rounded-xl p-1 shadow-inner border border-gray-200">
                                                    <button type="button"
                                                        onclick="const input = this.parentNode.querySelector('input'); input.stepDown(); input.form.submit();"
                                                        class="w-10 h-10 flex items-center justify-center text-indigo-600 hover:text-indigo-800 transition transform active:scale-95">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M20 12H4" />
                                                        </svg>
                                                    </button>

                                                    <input type="number" name="quantity" value="{{ $item->quantity }}"
                                                        min="0" max="{{ $item->product->stock }}"
                                                        onchange="this.form.submit()"
                                                        class="w-12 bg-transparent border-none text-center font-bold text-gray-800 focus:ring-0 p-0">

                                                    <button type="button"
                                                        onclick="const quantityInput = this.parentNode.querySelector('input[name=quantity]'); quantityInput.stepUp(); quantityInput.form.submit();"
                                                        @disabled($item->quantity >= $item->product->stock)
                                                        class="w-10 h-10 flex items-center justify-center text-indigo-600 hover:text-indigo-800 transition transform active:scale-95 disabled:opacity-25 disabled:cursor-not-allowed">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </form>

                                            <div class="text-right min-w-[120px]">
                                                <p class="text-lg font-extrabold text-gray-900">
                                                    ₹{{ number_format($item->product->price * $item->quantity, 2) }}
                                                </p>
                                            </div>

                                            <form action="{{ route('cart.destroy', $item->product_id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
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
                            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end items-center">
                                <span class="text-sm text-gray-500 mr-2 font-medium">Vendor Subtotal:</span>
                                <span
                                    class="text-xl font-black text-gray-900">₹{{ number_format($items->sum(fn($i) => $i->product->price * $i->quantity), 2) }}</span>
                            </div>
                        </div>
                    @endforeach

                    <!-- Grand Total Card -->
                    <div class="bg-indigo-600 overflow-hidden shadow-xl sm:rounded-2xl p-8 text-white">
                        <div class="flex flex-col sm:flex-row justify-between items-center">
                            <div class="mb-4 sm:mb-0">
                                <h3 class="text-xl font-bold opacity-80 uppercase tracking-widest">Grand Total</h3>
                                <p class="text-sm opacity-60">Inclusive of all vendor subtotals</p>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl sm:text-5xl font-black">₹{{ number_format($grandTotal, 2) }}</div>
                                <div class="mt-6">
                                    @auth
                                        <a href="{{ route('checkout.index') }}"
                                            class="w-full sm:w-auto bg-white text-indigo-600 hover:bg-indigo-50 font-black py-4 px-10 rounded-xl shadow-lg transition transform hover:-translate-y-1 inline-block text-center uppercase tracking-widest text-sm">
                                            Proceed to Checkout
                                        </a>
                                    @else
                                        <a href="{{ route('checkout.index') }}"
                                            class="w-full sm:w-auto bg-white text-indigo-600 hover:bg-indigo-50 font-black py-4 px-10 rounded-xl shadow-lg transition transform hover:-translate-y-1 inline-block text-center uppercase tracking-widest text-sm">
                                            Login to Checkout
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
