<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'amount', 'transaction_id'];
}
