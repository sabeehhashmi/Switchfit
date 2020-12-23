<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PassPayment extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['buyer_id', 'order_id', 'card_id', 'stripe_card_id', 'stripe_customer_id',
        'stripe_transaction_id', 'total', 'status'];
}
