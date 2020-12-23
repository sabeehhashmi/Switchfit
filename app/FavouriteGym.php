<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavouriteGym extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'gym_id', 'fav'];

    /**
     * @Description Get Favourite gyms List in basicInfoMood
     * @param $userId
     * @param bool $basicInfoMood
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function getFavouriteGyms($userId, $basicInfoMood = false)
    {
        $res = [];
        $favGyms = self::where([['user_id', $userId], ['fav', 1]])->orderByDesc('id')->get();
        if ($favGyms) {
            foreach ($favGyms as $favGym) {
                $gym = Gym::find((int)$favGym->gym_id);
                if ($gym) {
                    array_push($res, $gym);
                }
            }
        }
        if ($basicInfoMood) {
            $res = convertGymBasicInfoArr($res);
        }
        return $res;
    }

    /**
     * @Description check user have this gym into favorite
     * @param $gymId
     * @param null $userId
     * @return int
     * @Author Khuram Qadeer.
     */
    public static function checkFavorite($gymId, $userId = null)
    {
        $res = 0;
        if (self::where([['user_id', $userId], ['gym_id', $gymId], ['fav', 1]])->exists())
            $res = 1;
        return $res;
    }

}
