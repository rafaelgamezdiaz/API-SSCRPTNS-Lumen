<?php


namespace App\Services;


use App\Models\Subscription;
use App\Traits\ApiResponser;
use App\Traits\ConsumesExternalService;

class SubscriptionService
{
    use ConsumesExternalService, ApiResponser;

    public function index($clientService, $productService)
    {
        $subscriptions = Subscription::all()->sortBy('id')->flatten();
        $subscriptions->each(function($subscriptions) use($clientService, $productService){
            $subscriptions->client = $clientService->getClient($subscriptions->client_id);
            $temp = $subscriptions->subscriptionDetails;
            $temp->each(function($temp) use ($productService){
                $temp->product = $productService->getProduct($temp->product_id);
            });
        });
        return $subscriptions;
    }
}
