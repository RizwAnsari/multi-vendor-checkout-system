<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Confirmed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl p-12">
                <div
                    class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner overflow-hidden relative">
                    <div class="absolute inset-0 bg-green-500 opacity-10 animate-ping"></div>
                    <svg class="w-10 h-10 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter italic mb-4">Payment Successful!
                </h3>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Thank you for your purchase. We've split your cart and notified all vendors.
                    You will receive separate updates for each shipment.
                </p>

                <div class="bg-gray-50 rounded-2xl p-6 mb-10 border border-gray-100">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Your Reference IDs
                    </div>
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach (session('order_ids', []) as $id)
                            <span
                                class="bg-indigo-600 text-white font-bold px-3 py-1 rounded-lg text-xs shadow-md">ORD#{{ $id }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('products.index') }}"
                        class="bg-gray-900 hover:bg-black text-white font-black py-4 px-8 rounded-2xl transition-all shadow-lg hover:shadow-gray-200 uppercase tracking-widest italic text-sm">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
