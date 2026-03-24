<?php

namespace App\Http\Controllers\Customer;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Customer\CartService;
use App\Services\Customer\ProductService;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected CartService $cartService
    ) {}

    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $products = $this->productService->getActiveProducts();
        $cartQuantities = $this->cartService->getCartQuantities($request->user());

        return view('products.index', compact('products', 'cartQuantities'));
    }
}
