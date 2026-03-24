<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout Summary') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <div class="mb-8 border-b border-gray-100 pb-4">
                    <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tighter italic">Review Your Order
                    </h3>
                    <p class="text-sm text-gray-500">Your items are grouped by vendor and will be processed as separate
                        orders.</p>
                </div>

                @foreach ($groupedItems as $vendorId => $items)
                    @php $vendor = $items->first()->product->vendor; @endphp
                    <div class="mb-10 last:mb-0">
                        <div class="flex items-center space-x-2 mb-4">
                            <div
                                class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xs shadow-md">
                                {{ substr($vendor->name, 0, 1) }}
                            </div>
                            <h4 class="font-bold text-gray-800 uppercase tracking-widest text-sm">{{ $vendor->name }}
                            </h4>
                        </div>

                        <div class="space-y-4 pl-10 border-l-2 border-indigo-50 ml-4">
                            @foreach ($items as $item)
                                <div class="flex justify-between items-center">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800">{{ $item->product->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $item->quantity }} x
                                            ₹{{ number_format($item->product->price, 2) }}</span>
                                    </div>
                                    <span
                                        class="font-black text-gray-900">₹{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                </div>
                            @endforeach
                            <div class="pt-2 flex justify-end">
                                <span
                                    class="text-xs font-bold text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2 py-1 rounded">
                                    Vendor Subtotal:
                                    ₹{{ number_format($items->sum(fn($i) => $i->product->price * $i->quantity), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-12 pt-8 border-t-2 border-dashed border-gray-100 flex flex-col items-end">
                    <div class="text-sm text-gray-500 uppercase tracking-widest font-bold mb-1">Grand Total</div>
                    <div class="text-4xl font-black text-indigo-600 italic tracking-tighter mb-8">
                        ₹{{ number_format($total, 2) }}</div>

                    <form action="{{ route('checkout.store') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-8 rounded-2xl transition-all shadow-xl hover:shadow-indigo-200 transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest italic text-lg">
                            Confirm & Pay Securely
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
