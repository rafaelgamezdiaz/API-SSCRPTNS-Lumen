<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Services\ProductService;
use App\Services\SubscriptionService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ClientSubscriptionController extends Controller
{

    use ApiResponser;

    /**
     * The service to consume the client service
     */
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(Request $request, $client_id, SubscriptionService $subscriptionService, ClientService $clientService, ProductService $productService)
    {
        return $subscriptionService->subscriptionsByClient($request, $client_id, $clientService, $productService);
    }

}
