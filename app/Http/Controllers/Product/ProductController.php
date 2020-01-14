<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Traits\ApiResponser;

class ProductController extends Controller
{
    use ApiResponser;

    /**
     * The service to consume the client service
     * @var
     */
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        return $this->productService->index();
    }
}
