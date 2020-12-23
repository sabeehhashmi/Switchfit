<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;


class TrainerActivity extends Model
{

    use SearchableTrait;
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'name', 'price', 'about', 'image', 'duration', 'type', 'address', 'lat', 'lng'];

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'trainer_activities.name' => 10,
            'trainer_activities.about' => 10,
            'trainer_activities.type' => 10,
            'trainer_activities.address' => 10,
            
        ],
    ];

    /**
     * @Description get By user id
     * @param $userId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public function user()
    {
        
        return $this->belongsTo(User::class,'user_id','id' );
        
    }   
    public static function getByUserId($userId)
    {
        return self::whereUserId($userId)->orderByDesc('id')->get();
    }

    /**
     * @Description Delete trainer activity data
     * @param $trainerId
     * @Author Khuram Qadeer.
     */
    public static function deleteById($trainerId)
    {
        $activities = self::where('user_id', $trainerId)->get();
        if ($activities) {
            foreach ($activities as $activity) {
                if ($activity->image) {
                    deleteFile($activity->image);
                }
                $activity->forceDelete();
            }
        }
    }
}
