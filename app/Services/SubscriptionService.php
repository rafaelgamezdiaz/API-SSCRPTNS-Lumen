<?php


namespace App\Services;


use App\Models\Subscription;
use App\Traits\ApiResponser;
use App\Traits\ConsumesExternalService;
use http\Env\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

class SubscriptionService
{
    use ConsumesExternalService, ApiResponser, ProvidesConvenienceMethods;

    /**
     * Returns the List of Subscriptions including Clients and Products or Services
     * @param $clientService
     * @param $productService
     * @return \Illuminate\Support\Collection
     */
    public function index($request, $clientService, $productService)
    {
        if (isset($_GET['where'])) {
            $subscriptions = Subscription::doWhere($request)
                                         ->where('account', $this->account())
                                         ->orderBy('created_at', 'desc')
                                         ->get();
        }
        else{
            $subscriptions = Subscription::where('account', $this->account())
                                         ->orderBy('created_at', 'desc');
                                       //  ->get();
        }
        // Get Clients and Products (or Services) for the Subscription
        $subscriptions->each(function($subscriptions) use($clientService, $productService){
            $subscriptions->client = $clientService->getClient($subscriptions->client_id);
            $temp = $subscriptions->subscriptionDetails;
            $temp->each(function($temp) use ($productService){
                $temp->product = $productService->getProduct($temp->product_id);
            });
        });
        return $subscriptions;
    }

    public function account()
    {
        if ( isset($_GET['account'])) {
            return $_GET['account'];
        }
        return null;

        // Use this if token validatios is activated for this api
        // return $this->getAccount($request)
    }

    /**
     * Store the Subscription
     * @param $request
     * @param $subscription
     * @param $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store($request, $subscription, $productService)
    {
        // Validations
        $this->validate($request, $subscription->rules());
        $subscription->fill($request->all());
        //$subscription->account = $this->getAccount($request);

        if ($subscription->checkCode($request->code)) {
            if ($subscription->save()) {
                $productService->store($subscription->id, collect($request->product_id));

                return $this->successResponse('Subscription saved!', $subscription->id);
            }
            return $this->errorMessage('Sorry. Something happends when trying to save the subscription!', 409);
        }
        return $this->errorMessage('This code is not available!', 409);
    }

    /**
     * Update the Subscription
     * @param $request
     * @param $id
     * @param $productService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($request, $id, $productService)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->fill($request->all());

        if ($subscription->update()) {
            return $productService->update($subscription->id, $request->product_id);
        }
        return $this->errorMessage('Sorry. Something happends when trying to update the subscription!', 409);
    }

    /**
     * Remove the Subscription
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        if ($subscription->delete())
        {
            return $this->successResponse('The subscription was deleted');
        }
        return $this->errorMessage('Sorry! Something happends when trying to delete the subscription.');
    }

    public function status($request, $id, $subscription)
    {
        // Validations
        $this->validate($request, $subscription->rules_status());

        $subscription = Subscription::findOrFail($id);
        $subscription->status = $this->changeStatus($request->status);
        if ($subscription->save())
        {
            return $this->successResponse('The Subscription status was updated') ;
        }
        return $this->errorMessage('Sorry!, something happends trying to change the Subscription status');
    }

    public function changeStatus($status)
    {
        return ($status == Subscription::SUBSCRIPTION_ACTIVE) ? Subscription::SUBSCRIPTION_ACTIVE : Subscription::SUBSCRIPTION_INACTIVE;
    }
}
