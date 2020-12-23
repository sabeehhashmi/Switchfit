<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReviewOption extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'active'];

    /**
     * @Description get All active review options
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getReviewOptions()
    {
        return self::where('active', 1)->get();
    }

    public function reviews()
    {
        return $this->hasMany(ReviewLink::class, 'review_option_id', 'id');
    }
}
