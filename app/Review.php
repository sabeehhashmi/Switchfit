<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['order_item_id', 'given_by', 'given_to', 'gym_id', 'stars', 'description'];

    /**
     * @Description get All Review details
     * @param $gymId
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function getReviewByGymId($gymId)
    {
        $res = $optionStars = $details = [];
        $totalReviews = $totalStars = 0;
        $reviews = self::whereGymId($gymId)->orderBy('id', 'DESC')->get();
        if (collect($reviews)->count() > 0) {
            $totalReviews = collect($reviews)->count();
            $totalStars = round((double)(collect($reviews)->sum('stars') / collect($reviews)->count()), 2);
            if ($reviews) {
                foreach ($reviews as $review) {
                    $data = $review;
                    $data['given_by_user_name'] = User::getFullName($review->given_by);
                    $data['given_by_user_avatar'] = User::find($review->given_by)['avatar'] ?? 'assets/images/default-dp.jpg';
                    array_push($details, $data);
                }
            }
        }
        $res['gym_name'] = Gym::find($gymId)['name'];
        $res['total_reviews'] = $totalReviews;
        $res['total_stars'] = $totalStars;
        $res['options_stars'] = ReviewLink::getOptionStars($gymId);
        $res['reviews'] = $details;
        return $res;
    }

    /**
     * @Description check is review by order item id
     * @param $orderItemId
     * @return bool
     * @Author Khuram Qadeer.
     */
    public static function isReviewed($orderItemId)
    {
        $res = false;
        if (self::where('order_item_id', $orderItemId)->exists()) {
            $res = true;
        }
        return $res;
    }

    /**
     * @Description Delete By gym id
     * @param $gymId
     * @Author Khuram Qadeer.
     */
    public static function deleteByGymId($gymId)
    {
        self::where('gym_id', $gymId)->forceDelete();
        ReviewLink::where('gym_id', $gymId)->forceDelete();
    }

    public function trainer()
    {
        
        return $this->belongsTo(User::class,'given_to','id' );
    }
    public function user()
    {
        
        return $this->belongsTo(User::class,'given_by','id' );
    }
    public function gym()
    {
        
        return $this->belongsTo(Gym::class,'gym_id','id' );
    }
    public function activity()
    {
        
        return $this->belongsTo(TrainerActivity::class,'activity_id','id' );
    }

}
