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

class SubscriptionController extends Controller
{
    use ApiResponser, ConsumesExternalService;

    public function index(ClientService $clientService, ProductService $productService, SubscriptionService $subscriptionService)
    {
        $subscriptions = $subscriptionService->index($clientService, $productService);
        return $this->successResponse('List of subscriptions', $subscriptions);
    }

    public function store(Request $request, Subscription $subscription, ProductService $product)
    {
        $this->validate($request, $subscription->rules());
        $subscription->fill($request->all());
        $subscription->last_billing = Carbon::now();

        if ($subscription->checkCode($request->code)) {
            if ($subscription->save()) {
                $product->store($subscription->id, $request->product_id);

                return $this->successResponse('Subscription saved!', $subscription->id);
            }
            return $this->errorMessage('Sorry. Something happends when trying to save the subscription!', 409);
        }
        return $this->errorMessage('This code is not available!', 409);
    }

    public function update(Request $request, $id, Subscription $subscription)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->fill($request->all());
        if ($subscription->isClean()) {
            return $this->errorMessage('Sorry, At least one field must be different!', 409);
        }
        if ($subscription->save()) {
            return $this->successResponse($subscription);
        }
        return $this->errorMessage('Sorry. Something happends when trying to update the subscription!', 409);
    }

    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        if ($subscription->delete())
        {
            return $this->successResponse('The contract was deleted');
        }
        return $this->errorMessage('Sorry! Something happends when trying to delete the subscription.');
    }
}
