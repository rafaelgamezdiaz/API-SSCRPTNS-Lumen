<?php


namespace App\Services;


use App\Models\Subscription;
use App\Models\SubscriptionDetail;
use App\Traits\ApiResponser;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

class SubscriptionService extends BaseService
{
    use ApiResponser, ProvidesConvenienceMethods;

    public function __construct()
    {
        $this->baseUri  = config('services.clients.base_url');
        $this->port     = config('services.clients.port');
        $this->secret   = config('services.clients.secret');
        $this->prefix   = config('services.clients.prefix');
    }

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
        $product_existens = $productService->productsExisting($request, $request->product_id);
        if ($product_existens->count()) {
            if ($subscription->checkCode($request)) {
                if ($subscription->save()) {
                    $productService->store($subscription->id, $product_existens);
                    return $this->successResponse('Suscripción guardada con éxito.', $subscription->id);
                }
                return $this->errorMessage('Error, no se ha podido guardar la suscripción, inténtelo nuevamente.', 409);
            }
            return $this->errorMessage('Error, el código ya ha sido utilizado para otra suscripción', 409);
        }
        return $this->errorMessage('Debe enviar productos o servicios disponibles para esta cuenta', 400);
    }

    /**
     * Return the Subscription Details (Producst ans services included in the subscription)
     */
    public function show($id, $productService)
    {
        $subscription = Subscription::findOrFail($id);
        $code = $subscription->code;
        $subscription_details = $subscription->subscriptionDetails;
        $subscription_details->each(function($subscription_details) use($productService, $code) {
            $subscription_details->product = $productService->getProduct($subscription_details->product_id, false);
            $subscription_details->code = $code;
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

        $product_existens = $productService->productsExisting($request, $request->product_id);
        if ($product_existens->count()) {
                if ($subscription->update()) {
                    $productService->update($subscription->id, $product_existens);
                    return $this->successResponse('Suscripción actualizada', $subscription->id);
                }
                return $this->errorMessage('Error, no se ha podido guardar la suscripción, inténtelo nuevamente.', 409);
        }
        return $this->errorMessage('Debe enviar productos o servicios disponibles para esta cuenta', 409);
    }

    /**
     * Remove the Subscription
     */
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        if ($subscription->delete())
        {
            return $this->successResponse('La suscripción ha sido eliminada');
        }
        return $this->errorMessage('Ha ocurrido un error al intentar eliminar la suscripción.');
    }

    /**
     * Return all the subscriptions o a Client (Advanced Client Filter)
     */
    public function subscriptionsByClient($request, $client_id, $clientService, $productService)
    {
        if (isset($_GET['where'])) {
            $subscriptions = Subscription::doWhere($request)
                                         ->where('client_id', $client_id)
                                         ->get();
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
    public function subscriptionsFilterClientDates($request, $clientService, $productService)
    {
        $endpoint = '/clients?where=[{"op":"ct","field":"clients.commerce_name","value":"'.$request->commerce_name.'"}]';
        $clients = collect($this->doRequest('GET',  $endpoint)->first())->pluck('id');

        if (isset($_GET['where'])) {
            $subscriptions = Subscription::doWhere($request)
                                         ->whereIn('client_id', $clients)
                                         ->get();
        }
        else
        {
            $subscriptions = Subscription::whereIn('client_id', $clients)->get();
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
            return $this->successResponse('El estado de la suscripción ha cambiado.') ;
        }
        return $this->errorMessage('Ocurrio un error al intentar cambiar el estatus.');
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

            // Getting the Subscription Details
            $subscription_details = $subscriptions->subscriptionDetails;

            // Get Services or Product Info
            $subscription_details->each(function($subscription_details) use ($productService, $extended){
                $subscription_details->product = $productService->getProduct($subscription_details->product_id, $extended);
            });
        });
    }
}
