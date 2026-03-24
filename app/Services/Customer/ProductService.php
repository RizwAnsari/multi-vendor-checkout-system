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
            ->inRandomOrder()
            ->paginate($perPage);
    }
}
