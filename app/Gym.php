<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'name', 'address', 'lat', 'lng', 'country', 'state', 'city', 'postal_code',
    'about', 'time_schedule', 'images',];

    public function reviews()
    {

        return $this->hasMany(Review::class, 'gym_id', 'id');
    }

    public function ratings()
    {

        return $this->hasMany(ReviewLink::class, 'gym_id', 'id');
    }

    /**
     * @Description Get All user id
     * @param null $userId
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function getAllByUserId($userId)
    {
        $res = [];
        if (User::isSuperAdmin($userId)) {
            $gyms = self::orderBy('id', 'DESC')->get();
        } else {
            $gyms = self::where('user_id', $userId)->orderBy('id', 'DESC')->get();
        }
        if ($gyms) {
            foreach ($gyms as $gym) {
                $data = [];
                $data = $gym;
                $data['reviews'] = [
                    'total' => \App\Review::getReviewByGymId($gym->id)['total_reviews'],
                    'star' => \App\Review::getReviewByGymId($gym->id)['total_stars'],
                ];
                $data['facilities'] = FacilityLink::getByGymId($gym->id);
                $data['amenities'] = AmenityLink::getByGymId($gym->id);
                $data['passes'] = Pass::getByGymId($gym->id);
                array_push($res, $data);
            }
        }
        return $res;
    }

    /**
     * @Description Get By Id
     * @param $gymId
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function getByGymId($gymId)
    {
        $res = [];
        $gym = self::whereId($gymId)->first();
        if ($gym) {
            $res = $gym;
            $res['reviews'] = [
                'total' => \App\Review::getReviewByGymId($gym->id)['total_reviews'],
                'star' => \App\Review::getReviewByGymId($gym->id)['total_stars'],
            ];
            $res['facilities'] = FacilityLink::getByGymId($gym->id) ?? [];
            $res['amenities'] = AmenityLink::getByGymId($gym->id) ?? [];
            $res['passes'] = Pass::getByGymId($gym->id) ?? [];
        }
        return $res;
    }

    /**
     * @Description Delete all related data of gym
     * @param $gymId
     * @Author Khuram Qadeer.
     */
    public static function deleteById($gymId)
    {
        $gym = self::find($gymId);
        if ($gym) {
            if ($gym->images) {
                foreach (json_decode($gym->images) as $imgUrl) {
                    deleteFile($imgUrl);
                }
            }
            FacilityLink::deleteByGymId($gymId);
            AmenityLink::deleteByGymId($gymId);
            Pass::where('gym_id', $gymId)->forceDelete();
            FavouriteGym::where('gym_id', $gymId)->forceDelete();
            Review::deleteByGymId($gymId);
        }
        $gym->delete();
    }

    /**
     * @Description Get Gym basic info
     * @param $gymId
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function getGymBasicInfo($gymId)
    {
        $res = [];
        $gym = self::find($gymId);
        if ($gym) {
            $res['id'] = $gym->id;
            $res['name'] = $gym->name;
            $res['image'] = $gym->images ? json_decode($gym->images)[0] : null;
            $res['lat'] = $gym->lat;
            $res['lng'] = $gym->lng;
            $res['reviews'] = [
                'total' => \App\Review::getReviewByGymId($gym->id)['total_reviews'],
                'star' => \App\Review::getReviewByGymId($gym->id)['total_stars'],
            ];
            $res['pass_lowest_price'] = number_format(Pass::getLowestPriceByGymId($gym->id) ?? 0, 2);
            $res['distance'] = $gym->distance;
        }
        return $res;
    }

    /**
     * @Description Get Active gym members
     * @param $gymId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getActiveGymMembers($gymId)
    {
        $members = [];
        $passesOrderItems = PassOrderItems::where([['gym_id', $gymId], ['is_used', 0], ['is_expire', 0]])->orderBy('book_date','DESC')->get();
        $users = [];
        if ($passesOrderItems) {
            foreach ($passesOrderItems as $passesOrderItem) {
                $data = [];
                $data = $passesOrderItem;
                $user = User::find($passesOrderItem->buyer_id);
                
                if(in_array($user, $users)){
                    continue;
                }else{
                    $userpassesOrderItems = PassOrderItems::where([['gym_id', $gymId], ['is_used', 0], ['is_expire', 0],['buyer_id',$passesOrderItem->buyer_id]])->get();
                    $data['buyer_user'] = $user;
                    $data['total_passes'] = $userpassesOrderItems->count();

                    array_push($members, $data);
                }
                $users[] = $user;
                
            }
        }
        return $members;
    }
    public static function getActiveOwnerMembers($gym_owner_id)
    {
        $members = [];
        $passesOrderItems = PassOrderItems::where([['gym_owner_id', $gym_owner_id], ['is_used', 0]])->get();
        $users = [];
        if ($passesOrderItems) {
            foreach ($passesOrderItems as $passesOrderItem) {
                $data = [];
                $data = $passesOrderItem;
                $user = User::find($passesOrderItem->buyer_id);
                
                if(in_array($user, $users)){
                    continue;
                }else{
                    $userpassesOrderItems = PassOrderItems::where([['gym_owner_id', $gym_owner_id], ['is_used', 0],['buyer_id',$passesOrderItem->buyer_id]])->get();
                    $data['buyer_user'] = $user;
                    $data['total_passes'] = $userpassesOrderItems->count();

                    array_push($members, $data);
                }
                $users[] = $user;
                
            }
        }
        return $members;
    }

}
