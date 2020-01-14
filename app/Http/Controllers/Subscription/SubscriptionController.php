<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Subscription;
use App\Services\ClientService;
use App\Services\ProductService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use ApiResponser;

    public function index(ClientService $clientService, ProductService $productService)
    {
        $contracts = Subscription::all()->sortBy('id')->flatten();

        $contracts->each(function($contracts) use($clientService, $productService){
            $contracts->client = $clientService->getClient($contracts->client_id);
            $contracts->product = $productService->getProduct($contracts->product_id);
        });
        return $this->successResponse('List of contracts', $contracts);
    }

    public function store(Request $request, Subscription $contract, ClientService $client, ProductService $product)
    {
        // Check if client and service already exist in database
        //
        //


        $client->store($request->client_id);
        $product->store($request->product_id);

        $this->validate($request, $contract->rules());
        $contract->fill($request->all());
        if ($contract->save()) {
            return $this->successResponse('Contract saved!', $contract);
        }
        return $this->errorMessage('Sorry. Something happends when trying to save the contract!', 409);
    }

    public function update(Request $request, $id, Subscription $contract)
    {
        $contract = Subscription::findOrFail($id);
        $contract->fill($request->all());
        if ($contract->isClean()) {
            return $this->errorMessage('Sorry, At least one field must be different!', 409);
        }
        if ($contract->save()) {
            return $this->successResponse($contract);
        }
        return $this->errorMessage('Sorry. Something happends when trying to update the contract!', 409);
    }

    public function destroy($id)
    {
        $contract = Subscription::findOrFail($id);
        if ($contract->delete())
        {
            return $this->successResponse('The contract was deleted');
        }
        return $this->errorMessage('Sorry! Something happends when trying to delete the contract.');
    }
}
