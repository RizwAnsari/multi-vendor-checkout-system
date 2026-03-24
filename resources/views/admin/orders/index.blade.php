<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin - Order Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- Filters Section -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <form action="{{ route('admin.orders.index') }}" method="GET"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                            <select name="vendor_id" id="vendor_id"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Vendors</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}"
                                        {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                            <select name="user_id" id="user_id"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Customers</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ request('user_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Statuses</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}"
                                        {{ request('status') == $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Filter') }}
                            </button>
                            <a href="{{ route('admin.orders.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Orders Table -->
                <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th scope="col" class="py-3 px-6">ID</th>
                                <th scope="col" class="py-3 px-6">Customer</th>
                                <th scope="col" class="py-3 px-6">Vendor</th>
                                <th scope="col" class="py-3 px-6">Subtotal</th>
                                <th scope="col" class="py-3 px-6">Status</th>
                                <th scope="col" class="py-3 px-6">Date</th>
                                <th scope="col" class="py-3 px-6">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                                        ORD#{{ $order->id }}</td>
                                    <td class="py-4 px-6">
                                        {{ $order->user->name }}<br>
                                        <span class="text-xs italic">{{ $order->user->email }}</span>
                                    </td>
                                    <td class="py-4 px-6">{{ $order->vendor->name }}</td>
                                    <td class="py-4 px-6 font-semibold">₹{{ number_format($order->subtotal, 2) }}</td>
                                    <td class="py-4 px-6">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full 
                                            @if ($order->status === \App\Enums\OrderStatus::PENDING) bg-yellow-100 text-yellow-800 
                                            @elseif($order->status === \App\Enums\OrderStatus::CONFIRMED) bg-green-100 text-green-800
                                            @elseif($order->status === \App\Enums\OrderStatus::CANCELLED) bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-xs text-gray-400">
                                        {{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td class="py-4 px-6">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="font-medium text-indigo-600 hover:text-indigo-900 hover:underline">View
                                            Details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-500">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
