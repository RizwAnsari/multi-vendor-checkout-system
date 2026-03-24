<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Models\Product;
use App\Models\Customer\Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Exceptions\InsufficientStockException;

class CartService
{
    /**
     * Add a product to the cart (User or Session).
     */
    public function addItem(?User $user, int $productId, int $quantity): void
    {
        $product = Product::active()->findOrFail($productId);
        $this->validateStock($product, $quantity);

        $user
            ? $this->addItemToUserCart($user, $product, $quantity)
            : $this->addItemToSessionCart($product, $quantity);
    }

    /**
     * Update item quantity (User or Session).
     */
    public function updateQuantity(?User $user, int $productId, int $quantity): void
    {
        // If quantity is 0 or less, remove the item entirely
        if ($quantity <= 0) {
            $this->removeItem($user, $productId);
            return;
        }

        $product = Product::active()->findOrFail($productId);
        $this->validateStock($product, $quantity);

        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $cart->items()->updateOrCreate(['product_id' => $productId], ['quantity' => $quantity]);
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $quantity;
                session()->put('cart', $cart);
            }
        }
    }

    /**
     * Remove item (User or Session).
     */
    public function removeItem(?User $user, int $productId): void
    {
        if ($user?->cart) {
            $user->cart->items()->where('product_id', $productId)->delete();
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }
    }

    /**
     * Get grouped items (Merged or separate).
     */
    public function getGroupedItems(?User $user): Collection
    {
        if ($user) {
            $items = $user->cart ? $user->cart->items()->with('product.vendor')->get() : collect();
        } else {
            $sessionCart = session()->get('cart', []);
            $productIds = array_keys($sessionCart);

            // Fetch all products with vendors in ONE query
            $products = Product::active()->with('vendor')->whereIn('id', $productIds)->get()->keyBy('id');

            $items = collect($sessionCart)->map(function ($details, $id) use ($products) {
                $product = $products[$id] ?? null;
                if (!$product) return null;

                return (object)[
                    'product_id' => $id,
                    'quantity' => $details['quantity'],
                    'product' => $product,
                ];
            })->filter();
        }

        // Group by vendor_id to avoid name collisions
        return $items->groupBy(fn($item) => $item->product->vendor_id);
    }

    /**
     * Get a simplified map of product IDs and their quantities.
     */
    public function getCartQuantities(?User $user): array
    {
        if ($user) {
            return $user->cart
                ? $user->cart->items()->pluck('quantity', 'product_id')->toArray()
                : [];
        }

        $sessionCart = session()->get('cart', []);
        return collect($sessionCart)->mapWithKeys(fn($details, $id) => [$id => $details['quantity']])->toArray();
    }

    /**
     * Merge session cart into database cart upon login.
     */
    public function mergeGuestCartIntoUser(User $user): void
    {
        $sessionCart = session()->get('cart', []);

        if (empty($sessionCart)) return;

        $productIds = array_keys($sessionCart);
        $products = Product::active()->whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($sessionCart as $productId => $details) {
            $product = $products[$productId] ?? null;

            if (!$product) continue; // skip missing or inactive products

            $this->addItemToUserCart($user, $product, $details['quantity']);
        }

        session()->forget('cart');
    }

    /**
     * Internal helper for user cart addition.
     */
    protected function addItemToUserCart(User $user, Product $product, int $quantity): void
    {
        DB::transaction(function () use ($user, $product, $quantity) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $item = $cart->items()->where('product_id', $product->id)->first();

            if ($item) {
                $item->increment('quantity', $quantity);
            } else {
                $cart->items()->create(['product_id' => $product->id, 'quantity' => $quantity]);
            }
        });
    }

    /**
     * Internal helper for session cart addition.
     */
    protected function addItemToSessionCart(Product $product, int $quantity): void
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'quantity' => $quantity,
                'price' => $product->price,
            ];
        }

        session()->put('cart', $cart);
    }

    /**
     * Stock validation.
     */
    protected function validateStock(Product $product, int $quantity): void
    {
        if ($quantity > $product->stock) {
            throw new InsufficientStockException("Insufficient stock for {$product->name}. Only {$product->stock} available.");
        }
    }
}
