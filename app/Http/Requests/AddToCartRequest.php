<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $productId = $this->product_id ?: $this->route('product');
                    $product = Product::find($productId);

                    if (!$product) return;

                    $existingQuantity = 0;
                    $user = $this->user();

                    if ($user) {
                        // Check database cart for authenticated users
                        $cartItem = $user->cart?->items()->where('product_id', $productId)->first();
                        $existingQuantity = $cartItem?->quantity ?? 0;
                    } else {
                        // Check session cart for guests
                        $sessionCart = session()->get('cart', []);
                        $existingQuantity = $sessionCart[$productId]['quantity'] ?? 0;
                    }

                    if (($existingQuantity + $value) > $product->stock) {
                        $remaining = max(0, $product->stock - $existingQuantity);
                        $message = "You already have {$existingQuantity} in your cart. ";
                        $message .= $remaining > 0
                            ? "You can only add up to {$remaining} more (Total stock: {$product->stock})."
                            : "No more stock available (Total stock: {$product->stock}).";

                        $fail($message);
                    }
                },
            ],
        ];
    }
}
