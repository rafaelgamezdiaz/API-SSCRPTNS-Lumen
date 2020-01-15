<?php


namespace App\Services;


use App\Models\SubscriptionDetail;
use App\Traits\ApiResponser;
use App\Traits\ConsumesExternalService;

class ProductService
{
    use ConsumesExternalService, ApiResponser;

    public function __construct()
    {
        $this->baseUri  = config('services.sales.base_url');
        $this->port     = config('services.sales.port');
        $this->secret   = config('services.sales.secret');
        $this->prefix   = config('services.sales.prefix');
    }

    /**
     * Returns all Products or Services for the current account
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $endpoint = '/products';
        if(isset($_GET['where'])){
            $endpoint.='?where='.$_GET['where'];
        }
        $url = $this->getURL().$endpoint;
        $products = $this->performRequest('GET',$url,null,[]);
        $products = collect($products)->first();

        return $this->successResponse('List of products',$products);
    }

    /**
     * Store Products and Services to the Subscription
     * @param $subscription_id
     * @param $products_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($subscription_id, $products_id)
    {
        // Add Products or Services to the Subscription
        $products_id->each(function($products_id) use ($subscription_id) {
            $this->addProduct($subscription_id, $products_id);
        });
        return $this->successResponse('Subscriptions were saved');
    }


    /**
     * Update Products and Services in the Subscription
     * @param $subscription_id
     * @param $products_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($subscription_id, $products_id)
    {
        // Remove Products or Services not included in the update product_id array
        $this->rejectProducts($subscription_id, $products_id);

        foreach ($products_id as $product_id)
        {
            $this->updateProduct($subscription_id, $product_id);
        }
        return $this->successResponse('Subscriptions were updated');
    }

    /**
     * Update an specific Product or Service for the subscription.
     * It is saved if not exist
     * @param $subscription_id
     * @param $product_id
     * @return |null
     */
    public function updateProduct($subscription_id, $product_id)
    {
        $subscription_product = SubscriptionDetail::where('subscription_id', $subscription_id)
                                                  ->where('product_id', $product_id)
                                                  ->get();
        if (count($subscription_product) == 0) {
            $this->addProduct($subscription_id, $product_id);
        }
        return null;
    }

    /**
     * Add the Product or Service
     * @param $subscription_id
     * @param $product_id
     * @return mixed
     */
    public function addProduct($subscription_id, $product_id)
    {
        return SubscriptionDetail::create([
            'subscription_id' => $subscription_id,
            'product_id' => $product_id
        ]);
    }

    /**
     * Remove Products or Services not includes in the $product_id array, when updated subscription.
     * @param $subscription_id
     * @param $product_id
     * @return mixed
     */
    public function rejectProducts($subscription_id, $product_id)
    {
        $reject_id = SubscriptionDetail::where('subscription_id', $subscription_id)
                                       ->whereNotIn('product_id', $product_id);
        return $reject_id->delete();
    }

    /**
     * Return a Product or Service from API-Ventas
     * @param $id
     * @return mixed
     */
    public function getProduct($id)
    {
        $endpoint = '/products/'.$id;
        $url = $this->getURL().$endpoint;
        $product = $this->performRequest('GET',$url,null,[]);
        return collect($product)->first();
    }


}
