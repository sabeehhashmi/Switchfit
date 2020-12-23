<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AmenityLink extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['gym_id', 'amenity_id'];

    /**
     * @Description Get All gym amenities by gym id
     * @param $gymId
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function getByGymId($gymId)
    {
        $res = [];
        $amenities = self::where('gym_id', $gymId)->get();
        if ($amenities) {
            foreach ($amenities as $amenity) {
                $data = [];
                $data = $amenity;
                $data['detail'] = Amenity::findOrFail($amenity->amenity_id);
                array_push($res, $data);
            }
        }
        return $res;
    }

    /**
     * @Description Delete by Gym id
     * @param $gymId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function deleteByGymId($gymId)
    {
        return self::where('gym_id', $gymId)->forceDelete();
    }

    /**
     * @Description check gym have amenity or not
     * @param $amenityId
     * @param $gymId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function checkGymHaveAmenity($amenityId, $gymId)
    {
        return self::where([['amenity_id', $amenityId], ['gym_id', $gymId]])->exists();
    }
}
