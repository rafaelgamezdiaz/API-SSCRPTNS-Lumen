<?php

namespace App\Models;

use App\Services\ClientService;
use App\Traits\ConsumesExternalService;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Subscription extends BaseModel
{
    use ConsumesExternalService;
    use SoftDeletes;

    const SUBSCRIPTION_ACTIVE = 'Activa';
    const SUBSCRIPTION_INACTIVE = 'Inactiva';

    protected $fillable = [
            'account',
            'code',
            'client_id',
            'date_start',
            'date_end',
            'billing_cycle'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $client_name;

    public function rules()
    {
        return [
            'account'         => 'required|numeric',
            'code'            => 'required',
            'client_id'       => 'required|numeric',
            'date_start'      => 'required',
            'date_end'        => 'required',
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
    public function checkCode($request)
    {
        $subcription = Subscription::where('code', strtolower($request->code))
                                   ->where('account', $request->account)
                                   ->get();
        return count($subcription) == 0;
    }

    public function isActive()
    {
        return $this->active == self::SUBSCRIPTION_ACTIVE;
    }

    /**
     * Accessor to return the Billing Cycle with first letter uppercase
     */
    public function getCodeAttribute($value)
    {
        return strtoupper($value);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtolower($value);
    }

}
