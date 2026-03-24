<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Customer\Order;
use App\Http\Controllers\Controller;
use App\Services\Admin\OrderService;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Display a listing of orders with filters.
     */
    public function index(Request $request)
    {
        $orders = $this->orderService->getFilteredOrders($request->all());
        $filterData = $this->orderService->getFilterData();

        return view('admin.orders.index', array_merge(
            ['orders' => $orders],
            $filterData
        ));
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order)
    {
        $order = $this->orderService->getOrderDetails($order);

        return view('admin.orders.show', compact('order'));
    }
}
