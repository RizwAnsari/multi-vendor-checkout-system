<?php

namespace App\Http\Controllers\Customer;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Exceptions\EmptyCartException;
use App\Services\Customer\CartService;
use App\Services\Customer\OrderService;
use App\Exceptions\InsufficientStockException;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService
    ) {}

    /**
     * Display the checkout summary.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if ($this->cartService->isEmpty($request->user())) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $groupedItems = $this->cartService->getGroupedItems($request->user());
        $total = $groupedItems->flatten(1)->sum(fn($item) => $item->product->price * $item->quantity);

        return view('customer.checkout.index', compact('groupedItems', 'total'));
    }

    /**
     * Process the checkout.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $orders = $this->orderService->processCheckout($request->user());

            $orderIds = $orders->pluck('id')->implode(', ');

            return redirect()->route('checkout.success')->with([
                'success' => "Order placed successfully! Order ID(s): {$orderIds}",
                'order_ids' => $orders->pluck('id')->toArray(),
            ]);
        } catch (EmptyCartException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        } catch (InsufficientStockException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            _log_exception('Checkout Process', $e);
            return redirect()->route('cart.index')->with('error', config('shop.error_message'));
        }
    }

    /**
     * Display success page.
     */
    public function success(): View|RedirectResponse
    {
        if (!session('order_ids')) {
            return redirect()->route('products.index');
        }

        return view('customer.checkout.success');
    }
}
