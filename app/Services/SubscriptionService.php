<?php


namespace App\Services;


use App\Models\Subscription;
use App\Models\SubscriptionDetail;
use App\Traits\ApiResponser;
use App\Traits\ConsumesExternalService;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

class SubscriptionService
{
    use ConsumesExternalService, ApiResponser, ProvidesConvenienceMethods;

    /**
     * Returns the List of Subscriptions including Clients and Products or Services
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
                                         ->orderBy('created_at', 'desc')
                                         ->get();
        }
        return $this->getClientsAndProducts($subscriptions, $clientService, $productService, false);
    }

    /**
     * Store the Subscription
     */
    public function store($request, $subscription, $productService)
    {
        // Validations
        $this->validate($request, $subscription->rules());
        $subscription->fill($request->all());

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
     * Return the Subscription Details (Producst ans services included in the subscription)
     */
    public function show($id, $productService)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription_details = $subscription->subscriptionDetails;
        $subscription_details->each(function($subscription_details) use($productService) {
            $subscription_details->product = $productService->getProduct($subscription_details->product_id, false);
        });
        return $subscription_details;
    }

    /**
     * Update the Subscription
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

    /**
     * Return all the subscriptions o a Client (Advanced Client Filter)
     */
    public function subscriptionsByClient($request, $client_id, $clientService, $productService)
    {
        if (isset($_GET['where'])) {
            $subscriptions = Subscription::doWhere($request)->where('client_id', $client_id)->get();
        }
        else
        {
            $subscriptions = Subscription::where('client_id', $client_id)->get();
        }
        $subscriptions = $this->getClientsAndProducts($subscriptions, $clientService, $productService, false);
        return $this->dataResponse($subscriptions);
    }

    /**
     * Return all the subscriptions o a Client (Advanced Client Filter)
     */
    public function subscriptionsByProduct($product_id, $clientService)
    {
        $subscription_details = SubscriptionDetail::where('product_id', $product_id)->get();
        $subscription_details->each(function($subscription_details) use($clientService){

            // Getting the Subscription Info
            $subscription_details = $subscription_details->subscription;

            // Getting the Client Info
            $subscription_details->client = $clientService->getClient($subscription_details->client_id, false);
        });

        return $this->dataResponse($subscription_details);
    }

    public function account()
    {
        if ( isset($_GET['account'])) {
            return $_GET['account'];
        }
        return null;
    }

    /**
     * Mannage the Status of a subscriptions
     */
    public function status($request, $id, $subscription)
    {
        // Validations
        $this->validate($request, $subscription->rules_status());

        $subscription = Subscription::findOrFail($id);
        $subscription->status = $this->changeStatus($request->status);
        if ($subscription->update())
        {
            return $this->successResponse('The Subscription status was updated') ;
        }
        return $this->errorMessage('Sorry!, something happends trying to change the Subscription status');
    }

    /**
     * Change the Status of a subscriptions (active or inactive)
     */
    public function changeStatus($status)
    {
        return ($status == Subscription::SUBSCRIPTION_ACTIVE) ? Subscription::SUBSCRIPTION_ACTIVE : Subscription::SUBSCRIPTION_INACTIVE;
    }

    /**
     * Make the query of the subscriptions (to use in the Report)
     */
    public function querySubscription($request, $clientService, $productService)
    {
        if ($request->has('where')){
            $subscriptions = Subscription::doWhere($request)
                                         ->whereIn('id', $request->ids);
        }else{
            $subscriptions = Subscription::whereIn('id', $request->ids);
        }
        $subscriptions = $subscriptions->orderByDesc('created_at')
                                        ->get();
        return $this->getClientsAndProducts($subscriptions, $clientService, $productService);
    }

    /**
     * Returns info of Clients and Products for a list of subscriptions (from Customers and Inventary APIs)
     * $extended = true  --> returns full info
     * $extended = false --> returns only specifics fields
     */
    public function getClientsAndProducts($subscriptions, $clientService, $productService, $extended = false)
    {
        return $subscriptions->each(function($subscriptions) use($clientService, $productService, $extended){
            // Getting the Client Info
            $subscriptions->client = $clientService->getClient($subscriptions->client_id, $extended);

            // Getting the Produc or Service Info
            $temp = $subscriptions->subscriptionDetails;
            $temp->each(function($temp) use ($productService, $extended){
                $temp->product = $productService->getProduct($temp->product_id, $extended);
            });
        });
    }
}
