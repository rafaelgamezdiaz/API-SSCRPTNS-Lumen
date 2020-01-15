<?php

namespace App\Models;

use App\Services\ClientService;
use App\Traits\ConsumesExternalService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Subscription extends Model
{
    use ConsumesExternalService;
    use SoftDeletes;

    const SUBSCRIPTION_ACTIVE = 'active';
    const SUBSCRIPTION_INACTIVE = 'inactive';

    protected $fillable = [
            'code',
            'client_id',
            'date_start',
            'date_end',
            'billing_cycle'
    ];

    protected $client_name;

    public function rules()
    {
        return [
            'client_id'             => 'required|numeric',
            'date_start'            => 'required',
            'date_end'              => 'required',
            'billing_cycle'   => 'required'
        ];
    }

    public function rules_status()
    {
        return [
            'status' => [
                'required',
                Rule::in([Subscription::SUBSCRIPTION_ACTIVE, Subscription::SUBSCRIPTION_INACTIVE])

            ]
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


    /**
     * Returns true if the Subscription code is availabe
     * New Subscriptions can not use an existing subscription with the same code
     * @param $code
     * @return bool
     */
    public function checkCode($code)
    {
        return count(Subscription::where('code', $code)->get()) == 0;
    }

    public function isActive()
    {
        return $this->active == self::SUBSCRIPTION_ACTIVE;
    }



}
