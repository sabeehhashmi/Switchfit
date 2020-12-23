<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'account_name', 'account_type', 'routing_number',
        'social_security_number', 'account_number', 'dob', 'address','stripe_account','stripe_bank_token','stripe_bank_account',];
}
