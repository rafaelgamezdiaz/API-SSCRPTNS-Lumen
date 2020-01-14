<?php

namespace App\Models;

use App\Services\ClientService;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
            'client_id',
            'product_id',
            'date_start',
            'date_end',
            'payment_periodicity'
    ];
    protected $client_name;

    public function rules()
    {
        return [
            'client_id'             => 'required|numeric',
            'product_id'            => 'required|numeric',
            'date_start'            => 'required',
            'date_end'              => 'required',
            'payment_periodicity'   => 'required'
        ];
    }

    public function scopeClient($id, ClientService $clientService)
    {
        return $this->client_name = $clientService->getClient($id);
    }

    public function subscriptions()
    {
        return $this->hasMany(SubscriptionDetail::class);
    }

}
