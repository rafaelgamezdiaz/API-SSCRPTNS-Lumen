<?php


namespace App\Services;


use App\Traits\ApiResponser;
use App\Traits\ConsumesExternalService;

class ClientService
{
    use ConsumesExternalService, ApiResponser;

    public function __construct()
    {
        $this->baseUri  = config('services.clients.base_url');
        $this->port     = config('services.clients.port');
        $this->secret   = config('services.clients.secret');
        $this->prefix   = config('services.clients.prefix');
    }

    /**
     * Returns the List of Clients from API-Clients, corresponding to the actual account
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $endpoint = '/clients';
        if(isset($_GET['account'])){
            $endpoint.='?account='.$_GET['account'];
        }
        $url = $this->getURL().$endpoint;
        $clients = $this->performRequest('GET',$url,null,[]);
        $clients = collect($clients)->first();
        return $this->successResponse('List of clients',$clients);
    }

    /**
     * Returns a Client from API-Clients, by id
     * @param $id
     * @return mixed
     */
    public function getClient($id)
    {
        $endpoint = '/clients/'.$id;
        $url = $this->getURL().$endpoint;
        $client = $this->performRequest('GET',$url,null,[]);
        $client = collect($client)->recursive(); //only(['name']);//->only(['name','last_name']);//->only(['name', 'last_name']);

        // Returns Some Clients Info
        $client = $client->first()->only(['name','last_name']);//->only(['name','last_name']);//->only(['name', 'last_name']);

        // Return Full Client Info uncomment the next line
        //$client = $client->first();

        return $client;
    }


}
