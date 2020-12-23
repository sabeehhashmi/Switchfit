<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'card_name', 'card_number', 'expire_month', 'expire_year', 'cvc','stripe_customer_id'
        ,'stripe_card_id','type'];

    /**
     * @Description get all user card
     * @param $userId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getAllByUserId($userId)
    {
        return self::where('user_id', (int)$userId)->get();
    }
}
