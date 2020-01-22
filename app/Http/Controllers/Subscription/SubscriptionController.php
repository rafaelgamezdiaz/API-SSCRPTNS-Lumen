<?php
namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\ClientService;
use App\Services\ProductService;
use App\Services\SubscriptionService;
use App\Traits\ApiResponser;
use App\Traits\ConsumesExternalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    use ApiResponser, ConsumesExternalService;

    /**
     * Returns all Subscriptions including Client info and Products or Services Info
     * @param ClientService $clientService
     * @param ProductService $productService
     * @param SubscriptionService $subscriptionService
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, ClientService $clientService, ProductService $productService, SubscriptionService $subscriptionService)
    {
        $subscriptions = $subscriptionService->index($request, $clientService, $productService);
        return $this->successResponse('List of subscriptions', $subscriptions);
    }

    /**
     * Store a Subscription
     * @param Request $request
     * @param Subscription $subscription
     * @param ProductService $productService
     * @param SubscriptionService $subscriptionService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Subscription $subscription, ProductService $productService, SubscriptionService $subscriptionService)
    {
        return $subscriptionService->store($request, $subscription, $productService);
    }

    /**
     * Update a Subscription
     * @param Request $request
     * @param $id
     * @param ProductService $productService
     * @param SubscriptionService $subscriptionService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id, ProductService $productService, SubscriptionService $subscriptionService)
    {
        return $subscriptionService->update($request, $id, $productService);
    }

    /**
     * Remove a Subscription
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, SubscriptionService $subscriptionService)
    {
        return $subscriptionService->destroy($id);
    }


    public function status(Request $request, $id, Subscription $subscription, SubscriptionService $subscriptionService){
        return $subscriptionService->status($request, $id, $subscription);
    }
}
