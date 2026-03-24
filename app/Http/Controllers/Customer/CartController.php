<?php

namespace App\Http\Controllers\Customer;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            Log::error('Cart Add Item Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'product_id' => $request->product_id,
                'exception' => $e
            ]);
            return redirect()->back()->with('error', 'Something went wrong, please try again later.');
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
            Log::error('Cart Update Quantity Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'product_id' => $productId,
                'exception' => $e
            ]);
            return redirect()->back()->with('error', 'Something went wrong, please try again later.');
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
            Log::error('Cart Remove Item Error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'product_id' => $productId,
                'exception' => $e
            ]);
            return redirect()->route('cart.index')->with('error', 'Something went wrong, please try again later.');
        }
    }
}
