<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionDetail extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'product_id',
        'quantity',
        'unit_price',
        'tax'
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
