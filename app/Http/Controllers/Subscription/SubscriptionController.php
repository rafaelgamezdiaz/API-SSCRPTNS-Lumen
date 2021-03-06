<?php
namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\ClientService;
use App\Services\ProductService;
use App\Services\SubscriptionService;
use App\Traits\ApiResponser;
use App\Traits\ConsumesExternalService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use ApiResponser, ConsumesExternalService;

    /**
     * Returns all Subscriptions including Client info and Products or Services Info
     */
    public function index(Request $request, ClientService $clientService, ProductService $productService, SubscriptionService $subscriptionService)
    {
        $subscriptions = $subscriptionService->index($request, $clientService, $productService);
        return $this->dataResponse($subscriptions);
    }

    /**
     * Store a Subscription
     */
    public function store(Request $request, Subscription $subscription, ProductService $productService, SubscriptionService $subscriptionService)
    {
        return $subscriptionService->store($request, $subscription, $productService);
    }

    public function show($id, SubscriptionService $subscriptionService, ProductService $productService)
    {
        return $subscriptionService->show($id, $productService);
    }

    /**
     * Update a Subscription
     */
    public function update(Request $request, $id, ProductService $productService, SubscriptionService $subscriptionService)
    {
        return $subscriptionService->update($request, $id, $productService);
    }

    /**
     * Remove a Subscription
     */
    public function destroy($id, SubscriptionService $subscriptionService)
    {
        return $subscriptionService->destroy($id);
    }


    public function status(Request $request, $id, Subscription $subscription, SubscriptionService $subscriptionService){
        return $subscriptionService->status($request, $id, $subscription);
    }
}
