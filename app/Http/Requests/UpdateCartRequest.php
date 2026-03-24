<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    $productId = $this->route('product');
                    $product = Product::find($productId);

                    if (!$product) return;

                    if ($value > $product->stock) {
                        $fail("The requested quantity exceeds available stock ({$product->stock}).");
                    }
                },
            ],
        ];
    }
}
