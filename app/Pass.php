<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PassOrderItems;

class Pass extends Model
{

    /**
     * @var array
     * @Author Khuram Qadeer.
     */
    protected $fillable = [
        'user_id', 'gym_id', 'title', 'description', 'image', 'price',
        'use_limit', 'valid_days', 'active',
    ];

    /**
     * @Description Eloquent Mutator for change price value into 2 decimal whenever i'll get
     * @param $value
     * @return string
     * @Author Khuram Qadeer.
     */
    public function getPriceAttribute($value)
    {
        return number_format($value, 2);
    }

    /**
     * @Description Generate Default Passes against Gym , by user id and gym id
     * @param $userId
     * @param $gymId
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function generateDefaultPasses($userId, $gymId)
    {
        $res = [];
        $defaultPasses = [
            [
                'user_id' => $userId,
                'gym_id' => $gymId,
                'title' => 'Fit Daily',
                'description' => 'This Pass will allow you 1 visit and is valid days for the next 30 days',
                'image' => 'assets/images/pass-img.png',
                'price' => 0.00,
                'use_limit' => 1,
                'valid_days' => 30,
                'active' => 0,
            ],
            [
                'user_id' => $userId,
                'gym_id' => $gymId,
                'title' => 'Fit Multi',
                'description' => 'This Pass will allow you 5 visits and is valid days for the next 30 days',
                'image' => 'assets/images/pass-img.png',
                'price' => 0.00,
                'use_limit' => 5,
                'valid_days' => 30,
                'active' => 0,
            ],
            [
                'user_id' => $userId,
                'gym_id' => $gymId,
                'title' => 'Fit Multi+',
                'description' => 'This Pass will allow you 10 visit and is valid days for the next 60 days',
                'image' => 'assets/images/pass-img.png',
                'price' => 0.00,
                'use_limit' => 10,
                'valid_days' => 60,
                'active' => 0,
            ],
            [
                'user_id' => $userId,
                'gym_id' => $gymId,
                'title' => 'Fit Monthly',
                'description' => 'This Pass will allow you unlimited visits and is valid days for the next 30 days',
                'image' => 'assets/images/pass-img.png',
                'price' => 0.00,
                'use_limit' => 0,
                'valid_days' => 30,
                'active' => 0,
            ],
            [
                'user_id' => $userId,
                'gym_id' => $gymId,
                'title' => 'Fit Monthly+',
                'description' => 'This Pass will allow you unlimited visits and is valid days for the next 90 days',
                'image' => 'assets/images/pass-img.png',
                'price' => 0.00,
                'use_limit' => 0,
                'valid_days' => 90,
                'active' => 0,
            ],
        ];
        if ($defaultPasses) {
            foreach ($defaultPasses as $defaultPass) {
                self::create($defaultPass);
                array_push($res, $defaultPass);
            }
        }
        return $res;
    }

    /**
     * @Description Get Passes by gym id
     * @param $gymId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getByGymId($gymId)
    {
        return self::where('gym_id', $gymId)->get();
    }

    /**
     * @Description Get Active passes by gym id
     * @param $gymId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getActivePasses($gymId)
    {
        return self::where([['gym_id', $gymId], ['active', 1]])->get();
    }

    /**
     * @Description get lowest price of passes via gym id
     * @param $gymId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getLowestPriceByGymId($gymId)
    {
        return self::where('gym_id', $gymId)->where('price', '>', 0)->min('price');
    }

    /**
     * @Description get user active and deactivated passes list
     * @param $userId
     * @return array
     * @Author Khuram Qadeer
     */
    public static function getUserPassesDetail($userId)
    {
        $res = [];
        $activePasses = PassOrderItems::getUserActivePasses($userId);
        $deactivatedPasses = PassOrderItems::getUserDeactivatedPasses($userId);
        $resActive = [];
        if ($activePasses) {
            foreach ($activePasses as $activePass) {
                $data = [];
                if (Gym::whereId((int)$activePass->gym_id)->exists()) {
                    $data = Gym::getGymBasicInfo($activePass->gym_id);
                    $data['pass_detail'] = Pass::find($activePass->pass_id);
                    $data['user_pass_detail'] = $activePass;
                    array_push($resActive, $data);
                }
            }
            $res['active'] = $resActive;
        }

        $resDeactivated = [];
        if ($deactivatedPasses) {
            foreach ($deactivatedPasses as $deactivatedPass) {
                $data = [];
                if (Gym::whereId((int)$deactivatedPass->gym_id)->exists()){
                    $data = Gym::getGymBasicInfo($deactivatedPass->gym_id);
                    $data['is_reviewed'] = Review::isReviewed((int)$deactivatedPass->id) ? 1 : 0;
                    $data['pass_detail'] = Pass::find($deactivatedPass->pass_id);
                    $data['user_pass_detail'] = $deactivatedPass;
                    array_push($resDeactivated, $data);
                }
            }
            $res['deactivated'] = $resDeactivated;
        }
        return $res;
    }

    /**
     * @Description Get user pass detail by pass token
     * @param $passToken
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function getUserPassDetailByToken($passToken)
    {
        $res = [];
        $passToken = strtolower($passToken);
        $pass = \App\PassOrderItems::where('pass_token', $passToken)->first();
        if ($pass) {
            $res = $pass;
            $res['visits_detail'] = \App\ManageVisit::where([['order_item_id', $pass->id], ['pass_token', $passToken]])->orderByDesc('current_visit_no')->get();
        }
        return $res;
    }

}
