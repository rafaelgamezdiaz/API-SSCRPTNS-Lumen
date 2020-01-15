<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionDetail extends Model
{
    protected $fillable = ['subscription_id','product_id'];

    public function contract()
    {
        return $this->belongsTo(Subscription::class);
    }

}
