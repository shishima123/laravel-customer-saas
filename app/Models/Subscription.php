<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    use SoftDeletes;

    protected $table = 'subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['customer_id', 'name', 'stripe_id', 'stripe_status', 'stripe_price',
        'quantity', 'trial_ends_at', 'ends_at'];

    protected $hidden = [
        'items', 'stripe_id', 'stripe_price'
    ];

    /**
     * Relationship with model Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
