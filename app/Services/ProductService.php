<?php


namespace App\Services;


use App\Models\Product;
use App\Models\SubscriptionDetail;
use App\Traits\ApiResponser;
use App\Traits\ConsumesExternalService;
use function foo\func;
use phpDocumentor\Reflection\Types\Boolean;

class ProductService extends BaseService
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
        $endpoint = isset($_GET['where']) ? '/products?where='.$_GET['where'] : '/products';
        $products = $this->doRequest('GET',  $endpoint)
                         ->first();
        return $this->successResponse('List of products',$products);
    }

    /**
     * Store Products and Services to the Subscription
     * @param $subscription_id
     * @param $products_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($subscription_id, $products)
    {
        // Add Products or Services to the Subscription
        foreach ($products as $product)
        {
            $this->addProduct($subscription_id, $product);
        }
        return $this->successResponse('Asignación de productos realizada con éxito.');
    }


    /**
     * Update Products and Services in the Subscription
     */
    public function update($subscription, $products)
    {
        $products_id = $products->pluck('id');

        // Remove products or services not included in update array
        $this->rejectProducts($subscription->id, $products_id);

        foreach ($products as $product)
        {
            $this->proccessProduct($subscription->id, $product); //$product_id['id']
        }
        return $this->successResponse('La suscripciones han sido actualizadas.');
    }

    /**
     * Update an specific Product or Service for the subscription.
     * It is saved if not exist
     */
    public function proccessProduct($subscription_id, $product)
    {
        $subscription_product = SubscriptionDetail::where('subscription_id', $subscription_id)
                                                  ->where('product_id', $product['id'])
                                                  ->get();
        if (count($subscription_product) == 0) {
            $this->addProduct($subscription_id, $product);
        }else{
            $this->updateProduct($subscription_product->first(), $product);
        }
        return null;
    }

    /**
     * Add the Product or Service
     */
    public function addProduct($subscription_id, $product)
    {
        return SubscriptionDetail::create([
            'subscription_id' => $subscription_id,
            'product_id' => $product['id'],
            'quantity' => $product['quantity'],
            'unit_price' => $product['unit_price'],
            'tax' => $product['tax']
        ]);
    }

    /**
     * Update the Product or Service
     */
    public function updateProduct($subscription_product, $product)
    {
        $subscription_product->quantity = $product['quantity'];
        $subscription_product->unit_price = $product['unit_price'];
        $subscription_product->tax = $product['tax'];
        return $subscription_product->update();
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
    public function getProduct($id, $extended)
    {
        $endpoint = '/products/'.$id;
        $product = $this->doRequest('GET',  $endpoint)
                         ->recursive()
                         ->first();

        if ( $product == false) {
            return "Error! There is nor connection with API-Inventary";
        }

        // Returns Produc data. $extended == true --> full info, else returns specific fields.
        $product_fields = $product->first()->only(['name']);
        return ($extended == true) ? $product : $product_fields;
    }

    public function getProductByName($request, $name, $extended = true)
    {
        $endpoint = '/products?where=[{"op":"eq","field":"p.name", "value":"'.$name.'"},{"op":"eq", "field":"p.account", "value":'.$request->account.'}]';
        $product = $this->doRequest('GET',  $endpoint)
            ->recursive()
            ->first();

        if ( $product == false) {
            return "¡Error de conexión con API-Inventary!";
        }
        if (count($product) == 0) {
            return null;
        }

        // Returns Produc data. $extended == true --> full info, else returns specific fields.
        $product_fields = $product->first()->only(['id']);
        return ($extended == true) ? $product : $product_fields;
    }

    /**
     * Returns a collection of products existing for the current account corresponding to a list of names $products_names
     */
    public function productsExisting($request, $products_names)
    {
        $product_existing = collect();
        foreach ($products_names as $product_name)
        {
            $product = $this->getProductByName($request, $product_name, false);
            if ($product != null) {
                $product_existing->push($product);
            }
        }
        return $product_existing;
    }
}
