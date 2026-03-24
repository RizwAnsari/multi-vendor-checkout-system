<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Details') }} ORD#{{ $order->id }}
            </h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                &larr; Back to Orders
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Left Column: Items and Totals -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Order Items</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3 px-4">Product</th>
                                        <th scope="col" class="py-3 px-4">Quantity</th>
                                        <th scope="col" class="py-3 px-4">Unit Price</th>
                                        <th scope="col" class="py-3 px-4 text-right">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4 font-medium text-gray-900">
                                                {{ $item->product_name }}<br>
                                                <span class="text-xs text-gray-400">ID: {{ $item->product_id }}</span>
                                            </td>
                                            <td class="py-4 px-4">{{ $item->quantity }}</td>
                                            <td class="py-4 px-4">₹{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="py-4 px-4 text-right font-semibold">
                                                ₹{{ number_format($item->line_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50 font-bold text-gray-900 border-t-2 border-gray-100">
                                        <td colspan="3" class="py-4 px-4 text-right">SUBTOTAL</td>
                                        <td class="py-4 px-4 text-right text-indigo-600 text-lg">
                                            ₹{{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Payment Details</h3>
                        @if ($order->payment)
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-400">Transaction ID</p>
                                    <p class="font-mono">{{ $order->payment->transaction_ref }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Status</p>
                                    <p class="font-semibold text-green-600 uppercase">
                                        {{ $order->payment->status->label() }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Amount Paid</p>
                                    <p class="font-semibold">₹{{ number_format($order->payment->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Paid At</p>
                                    <p>{{ $order->payment->paid_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-red-500 italic">No payment record found for this order.</p>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Info Cards -->
                <div class="space-y-6">
                    <!-- Order Status -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Status</h3>
                        <div class="flex items-center gap-2 mb-4">
                            <span
                                class="px-3 py-1 text-sm font-semibold rounded-full 
                                @if ($order->status === \App\Enums\OrderStatus::PENDING) bg-yellow-100 text-yellow-800 
                                @elseif($order->status === \App\Enums\OrderStatus::CONFIRMED) bg-green-100 text-green-800
                                @elseif($order->status === \App\Enums\OrderStatus::CANCELLED) bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ strtoupper($order->status->value) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 italic">Ordered at:
                            {{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    <!-- Customer Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Customer</h3>
                        <p class="font-medium">{{ $order->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                        <hr class="my-4 border-gray-100">
                        <p class="text-xs text-gray-400 uppercase">Customer ID: {{ $order->user->id }}</p>
                    </div>

                    <!-- Vendor Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Vendor</h3>
                        <p class="font-medium">{{ $order->vendor->name }}</p>
                        <p class="text-sm text-gray-500 italic">{{ $order->vendor->slug }}</p>
                        <hr class="my-4 border-gray-100">
                        <p class="text-xs text-gray-400 uppercase">Vendor ID: {{ $order->vendor->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
