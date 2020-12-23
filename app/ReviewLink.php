<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReviewLink extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['given_by', 'given_to', 'gym_id', 'stars', 'review_id', 'review_option_id'];

    /**
     * @Description get options Stars
     * @param $gymId
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function getOptionStars($gymId)
    {
        $res = [];
        $options = ReviewOption::getReviewOptions();
        if ($options) {
            foreach ($options as $option) {
                array_push($res, [
                    'id' => $option->id,
                    'name' => $option->name,
                    'stars' => self::getOptionStarsById($gymId, $option->id)
                ]);
            }
        }
        return $res;
    }

    /**
     * @Description get reviews option wise stars OR rating count
     * @param $gymId
     * @param $optionId
     * @return float|int
     * @Author Khuram Qadeer.
     */
    public static function getOptionStarsById($gymId, $optionId)
    {
        $res = 0;
        $reviews = self::where([['gym_id', $gymId], ['review_option_id', $optionId]])->get();
        if (collect($reviews)->count() > 0)
            $res = round((double)(collect($reviews)->sum('stars') / collect($reviews)->count()), 3);
        return $res;
    }
    public function activity()
    {
        
        return $this->belongsTo(TrainerActivity::class,'activity_id','id' );
    }
}
