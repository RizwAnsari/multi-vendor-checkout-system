<?php

namespace App\Http\Controllers\Customer;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Services\Customer\ProductService;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Display a listing of products.
     */
    public function index(): View
    {
        $products = $this->productService->getActiveProducts();

        return view('products.index', compact('products'));
    }
}
