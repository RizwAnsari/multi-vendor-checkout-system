<?php

namespace App\Services\Customer;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Get active products with vendor information, paginated.
     */
    public function getActiveProducts(int $perPage = 8): LengthAwarePaginator
    {
        return Product::active()
            ->with('vendor')
            ->paginate($perPage);
    }

    /**
     * Check if a product has sufficient stock.
     */
    public function isInStock(Product $product, ?int $requestedQuantity = null): bool
    {
        return $product->isInStock($requestedQuantity);
    }

    /**
     * Check if a product is out of stock.
     */
    public function isOutOfStock(Product $product, int $requestedQuantity): bool
    {
        return $product->isOutOfStock($requestedQuantity);
    }
}
