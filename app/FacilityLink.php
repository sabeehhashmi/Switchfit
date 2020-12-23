<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacilityLink extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['gym_id', 'facility_id'];

    /**
     * @Description Get all facilities data of gym
     * @param $gymId
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function getByGymId($gymId)
    {
        $res = [];
        $facilities = self::where('gym_id', $gymId)->get();
        if ($facilities) {
            foreach ($facilities as $facility) {
                $data = [];
                $data = $facility;
                $data['detail'] = Facility::find($facility->facility_id) ?? null;
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
     * @Description check gym have facility or not
     * @param $facilityId
     * @param $gymId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function checkGymHaveFacility($facilityId, $gymId)
    {
        return self::where([['facility_id', $facilityId], ['gym_id', $gymId]])->exists();
    }

    public static function checkGymHaveFacilities($facilityIds, $gymId)
    {
        $exist = false;
        $counter = 1;
        if(!empty($facilityIds)){
            foreach ($facilityIds as $facilityId) {
                if(self::where([['facility_id', $facilityId], ['gym_id', $gymId]])->exists()){
                 $exist = true;
             }
             else{
               $exist = false;
           }
           $counter ++;
           if($counter > 1 && !$exist){
            return false;
        }
    }

}
return true;

}

}
