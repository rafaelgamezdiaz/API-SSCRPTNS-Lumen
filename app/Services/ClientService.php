<?php


namespace App\Services;

use App\Traits\ApiResponser;
use phpDocumentor\Reflection\Types\Boolean;

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
     * Returns the List of Clients from API-Customers, corresponding to the actual account
     */
    public function index()
    {
        $endpoint = isset($_GET['account']) ? '/clients?account='.$_GET['account'] : '/clients';
        $clients = $this->doRequest('GET',  $endpoint)
                        ->first();
        return $this->successResponse('List of clients',$clients);
    }

    /**
     * Returns a Client from API-Customers, by id
     */
    public function getClient($id, $extended)
    {
        $endpoint = '/clients/'.$id;
        $client = $this->doRequest('GET',  $endpoint)
            ->recursive()
            ->first();

        if ( $client == false) {
            return "Error! There is nor connection with API-Customers";
        }

        // Returns Client data. $extended == true --> full info, else returns specific fields.
        $client_fields = $client->only(['name','last_name']);
        return ($extended == true) ? $client : $client_fields;
    }
}
