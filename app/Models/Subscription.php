<?php

namespace App\Models;

use App\Services\ClientService;
use App\Traits\ConsumesExternalService;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use ConsumesExternalService;

    protected $fillable = [
            'code',
            'client_id',
            'date_start',
            'date_end',
            'payment_periodicity'
    ];
    protected $client_name;

    public function rules()
    {
        return [
            'client_id'             => 'required|numeric',
            'date_start'            => 'required',
            'date_end'              => 'required',
            'payment_periodicity'   => 'required'
        ];
    }

    public function scopeClient($id, ClientService $clientService)
    {
        return $this->client_name = $clientService->getClient($id);
    }

    public function subscriptionDetails()
    {
        return $this->hasMany(SubscriptionDetail::class);
    }

    public function getProduct($id)
    {
        $endpoint = '/products/'.$id;
        $url = $this->getURL().$endpoint;
        $product = $this->performRequest('GET',$url,null,[]);
        return collect($product)->first();
    }

    public function checkCode($code)
    {
        return count(Subscription::where('code', $code)->get()) == 0;
    }

}
