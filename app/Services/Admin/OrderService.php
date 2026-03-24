<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Vendor;
use App\Enums\UserRole;
use App\Enums\OrderStatus;
use App\Models\Customer\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService
{
    /**
     * Get orders based on filters.
     */
    public function getFilteredOrders(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Order::with(['user', 'vendor', 'items'])
            ->when(!empty($filters['vendor_id']), fn($query) => $query->where('vendor_id', $filters['vendor_id']))
            ->when(!empty($filters['user_id']), fn($query) => $query->where('user_id', $filters['user_id']))
            ->when(!empty($filters['status']), fn($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get data needed for the order index filters.
     */
    public function getFilterData(): array
    {
        return [
            'vendors' => Vendor::orderBy('name')->get(),
            'customers' => User::where('role', UserRole::CUSTOMER)->orderBy('name')->get(),
            'statuses' => OrderStatus::cases(),
        ];
    }

    /**
     * Get detailed order info.
     */
    public function getOrderDetails(Order $order): Order
    {
        return $order->load(['user', 'vendor', 'items.product', 'payment']);
    }
}
