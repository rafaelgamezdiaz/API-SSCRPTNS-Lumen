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

    /**
     * Returns the product List from API-Ventas
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->productService->index();
    }

    /**
     * Returns a Product or Service from API-Ventas
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product =  $this->productService->getProduct($id);
        return $this->successResponse('Produc or Service',$product);
    }
}
