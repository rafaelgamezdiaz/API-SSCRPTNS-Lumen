<?php


namespace App\Services;


use App\Traits\ApiResponser;
use App\Traits\ConsumesExternalService;

class BaseService
{
    use ConsumesExternalService, ApiResponser;

    public function doRequest($method, $endpoint)
    {
        $url = $this->getURL().$endpoint;
        $response = $this->performRequest($method,$url,null,[]);
        return collect($response);
    }

}
