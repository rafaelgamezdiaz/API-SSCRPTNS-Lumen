<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionDetail extends BaseModel
{
    protected $fillable = [
        'subscription_id',
        'product_id'
    ];

    protected $hidden = [
        'subscription_id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

}
