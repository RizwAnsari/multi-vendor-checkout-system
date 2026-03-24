<?php

namespace App\Http\Controllers\Customer;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Services\Customer\CartService;
use App\Http\Requests\AddToCartRequest;
use App\Exceptions\InsufficientStockException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Display the user's cart grouped by vendor.
     */
    public function index(Request $request): View
    {
        $groupedItems = $this->cartService->getGroupedItems($request->user());

        $grandTotal = $groupedItems->flatten()->sum(fn($item) => $item->product->price * $item->quantity);

        return view('cart.index', compact('groupedItems', 'grandTotal'));
    }

    /**
     * Add a product to the cart.
     */
    public function store(AddToCartRequest $request): RedirectResponse
    {
        try {
            $this->cartService->addItem(
                $request->user(),
                $request->product_id,
                $request->quantity
            );

            return redirect()->back()->with('success', 'Product added to cart successfully!');
        } catch (InsufficientStockException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Product not found.');
        } catch (\Throwable $e) {
            _log_exception('Cart Add Item Error', $e, ['product_id' => $request->product_id]);
            return redirect()->back()->with('error', config('shop.error_message'));
        }
    }

    /**
     * Update item quantity.
     */
    public function update(AddToCartRequest $request, int $productId): RedirectResponse
    {
        try {
            $this->cartService->updateQuantity(
                $request->user(),
                $productId,
                $request->quantity
            );

            return redirect()->route('cart.index')->with('success', 'Cart updated successfully!');
        } catch (InsufficientStockException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Product not found or inactive.');
        } catch (\Throwable $e) {
            _log_exception('Cart Update Quantity Error', $e, ['product_id' => $productId]);
            return redirect()->back()->with('error', config('shop.error_message'));
        }
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, int $productId): RedirectResponse
    {
        try {
            $this->cartService->removeItem($request->user(), $productId);
            return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
        } catch (\Throwable $e) {
            _log_exception('Cart Remove Item Error', $e, ['product_id' => $productId]);
            return redirect()->route('cart.index')->with('error', config('shop.error_message'));
        }
    }
}
