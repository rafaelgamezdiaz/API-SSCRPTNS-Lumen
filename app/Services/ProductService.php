<?php


namespace App\Services;


use App\Models\Product;
use App\Models\Subscription;
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

    public function store($subscription_id, $product_id)
    {
        if (is_array($product_id)) {
            foreach ($product_id as $product)
            {
                $this->saveProduct($subscription_id, $product);
            }
            return $this->successResponse('Subscriptions were saved');
        }
        $this->saveProduct($subscription_id, $product_id);
        return $this->successResponse('Product was saved', $subscription);
    }

    public function saveProduct($subscription_id, $product_id)
    {
        return SubscriptionDetail::create([
            'subscription_id' => $subscription_id,
            'product_id' => $product_id
        ]);
    }

    public function getProduct($id)
    {
        $endpoint = '/products/'.$id;
        $url = $this->getURL().$endpoint;
        $product = $this->performRequest('GET',$url,null,[]);
        return collect($product)->first();
    }
}
