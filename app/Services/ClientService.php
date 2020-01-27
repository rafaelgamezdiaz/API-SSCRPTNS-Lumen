<?php


namespace App\Services;


use App\Models\Subscription;
use App\Traits\ApiResponser;
use App\Traits\ConsumesExternalService;

class ClientService extends BaseService
{
    use ApiResponser;

    public function __construct()
    {
        $this->baseUri  = config('services.clients.base_url');
        $this->port     = config('services.clients.port');
        $this->secret   = config('services.clients.secret');
        $this->prefix   = config('services.clients.prefix');
    }

    /**
     * Returns the List of Clients from API-Clients, corresponding to the actual account
     */
    public function index()
    {
        $endpoint = isset($_GET['account']) ? '/clients?account='.$_GET['account'] : '/clients';
        $clients = $this->doRequest('GET',  $endpoint)
                        ->first();
        return $this->successResponse('List of clients',$clients);
    }

    /**
     * Returns a Client from API-Clients, by id
     */
    public function getClient($id, $fullInfo = true)
    {
        $endpoint = '/clients/'.$id;
        $client = $this->doRequest('GET',  $endpoint)
            ->recursive();

        // Return Full Client Info uncomment the next line
        if ($fullInfo) {
            return $client->first();
        }
        // Returns Some Clients Info
        if ($client->first() !== true and $client->first() !== false ) {
            return $client->first()->only(['name','last_name']);
        }
        return $client;
    }

}
