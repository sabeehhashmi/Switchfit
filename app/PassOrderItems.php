<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PassOrderItems extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'order_id', 'buyer_id', 'gym_owner_id', 'gym_id', 'pass_id', 'pass_token', 'price', 'qty', 'valid_days',
        'sub_total', 'allow_visits', 'user_visits', 'book_date', 'last_valid_date', 'is_expire', 'is_used',
        'switch_fit_fee', 'gym_owner_amount','payout_id', 'payout_status', 'payment_status',
    ];

    /**
     * @Description Get Active passes of user
     * @param $buyerId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getUserActivePasses($buyerId)
    {
        return self::where([['buyer_id', $buyerId], ['is_used', 0], ['is_expire', 0]])->orderByDesc('id')->whereDate('last_valid_date', '>', Carbon::now())->get();
    }

    /**
     * @Description Get Deactivated Passes
     * @param $buyerId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getUserDeactivatedPasses($buyerId)
    {
        return self::where('buyer_id', $buyerId)->
        where(function ($query) {
            $query->orwhereDate('last_valid_date', '<', Carbon::now())->orwhere('is_used', 1);
        })->orderByDesc('id')->get();


/*        where(function ($query) use ($buyerId) {
            $query->where('buyer_id', $buyerId)->where('is_used', 1);
        })->where(function ($query) {
            $query->orwhereDate('last_valid_date', '<', Carbon::now());
        })->orWhere('is_expire', 1)->orderByDesc('id')->get();*/
    }
    public function gym() {
     return $this->belongsTo(Gym::class,'gym_id','id' );
 } 
 public function pass() {
     return $this->belongsTo(Pass::class,'pass_id','id' );
 }
 public function byer() {
     return $this->belongsTo(User::class,'buyer_id','id' );
 }
}
