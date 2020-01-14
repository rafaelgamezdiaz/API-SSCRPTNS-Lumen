<?php


namespace App\Services;


use App\Models\Client;
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

    public function store($id)
    {
        $client = Client::create([
            'client_id' => $id
        ]);
        return $this->successResponse('Client was saved', $client);
    }

    public function getClient($id)
    {
        $endpoint = '/clients/'.$id;
        $url = $this->getURL().$endpoint;
        $client = $this->performRequest('GET',$url,null,[]);
        return collect($client)->first();
       // return $this->successResponse('Client',$client);
        //$clients = collect($clients)->first();
        //return $this->successResponse('List of clients',$clients);
    }
}
