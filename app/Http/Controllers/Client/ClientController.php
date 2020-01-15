<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ClientController extends Controller
{

    use ApiResponser;

    /**
     * The service to consume the client service
     * @var
     */
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        return $this->clientService->index();
    }

}
