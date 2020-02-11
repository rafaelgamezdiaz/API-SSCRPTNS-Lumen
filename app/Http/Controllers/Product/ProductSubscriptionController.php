<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Services\ProductService;
use App\Services\SubscriptionService;
use App\Traits\ApiResponser;

class ProductSubscriptionController extends Controller
{
    use ApiResponser;

    /**
     * The service to consume the client service
     */
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Return all Subscriptions for an specific product, include the client info (advanced filter)
     */
    public function index($product_id, SubscriptionService $subscriptionService, ClientService $clientService)
    {
        return $subscriptionService->subscriptionsByProduct($product_id, $clientService);
    }

}
