<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Nicolaslopezj\Searchable\SearchableTrait;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable,SearchableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'api_token', 'device_id',
        'device_type', 'provider', 'provider_id', 'role_id',
        'manager_name', 'phone', 'address', 'logo', 'lat', 'lng', 'terms_conditions',
        'postal_code', 'date_of_birth', 'gender', 'emergency_name', 'emergency_phone',
        'country', 'state', 'city', 'avatar', 'about', 'qualification_1', 'qualification_2', 'document_type', 'document',
        'document_expire_date', 'availability', 'is_verified', 'is_profile_completed'
    ];

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'users.first_name' => 10,
            'users.email' => 10,
            'users.address' => 10,
            'users.last_name' => 10,
            'users.country' => 10,
            'users.state' => 10,
            'users.city' => 10,
            
        ],
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @Description Get user by email
     * @param $email
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getByEmail($email)
    {
        return self::whereEmail($email)->first();
    }

    /**
     * @Description Delete By user id
     * @param $userId
     * @Author Khuram Qadeer.
     */
    public static function deleteById($userId)
    {
        $user = self::find((int)$userId);
        if ($user->logo) {
            deleteFile($user->logo);
        }
        if ($user->document) {
            deleteFile($user->document);
        }

        if ($user->role_id == 3) {
            TrainerActivity::deleteById($user->id);
        }
        $user->delete();
    }

    /**
     * @Description is super admin check
     * @param $userId
     * @return bool
     * @Author Khuram Qadeer.
     */
    public static function isSuperAdmin($userId)
    {
        $res = false;
        $user = self::find($userId);
        if ($user) {
            if ($user->role_id == 1) {
                $res = true;
            }
        }
        return $res;
    }

    /**
     * @Description check user id supper admin
     * @param $userId
     * @return bool
     * @Author Khuram Qadeer.
     */
    public static function checkSuperAdmin($userId)
    {
        $res = false;
        $user = self::find($userId);
        if ($user) {
            if ($user->role_id == 1) {
                $res = true;
            }
        }
        return $res;
    }

    /**
     * @Description check user is gym owner
     * @param $userId
     * @return bool
     * @Author Khuram Qadeer.
     */
    public static function checkGymOwner($userId)
    {
        $res = false;
        $user = self::find($userId);
        if ($user) {
            if ($user->role_id == 2) {
                $res = true;
            }
        }
        return $res;
    }

    /**
     * @Description get user data
     * @param $userId
     * @return array
     * @Author Khuram Qadeer.
     */
    public function categories() {

     return $this->belongsToMany(Category::class, 'category_links', 'user_id','category_id');
 }
 public static function getUserData($userId)
 {
    $res = [];
    $user = self::with('reviews')->find((int)$userId);
    if ($user) {
        if ($user->role_id == 1) {
                // super admin
            $res['user'] = $user;
        } elseif ($user->role_id == 2) {
                // gym owner
            $res['user'] = $user;
            $res['gyms'] = Gym::getAllByUserId($user->id);
        } elseif ($user->role_id == 3) {
                // trainer
            $res['user'] = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'date_of_birth' => $user->date_of_birth,
                'gender' => $user->gender,
                'phone' => $user->phone,
                'address' => $user->address,
                'lat' => $user->lat,
                'lng' => $user->lng,
                'country' => $user->country,
                'state' => $user->state,
                'city' => $user->city,
                'postal_code' => $user->postal_code,
                'avatar' => $user->avatar,
                'about' => $user->about,
                'qualification_1' => $user->qualification_1,
                'qualification_2' => $user->qualification_2,
                'document_type' => $user->document_type,
                'document' => $user->document,
                'document_expire_date' => $user->document_expire_date,
                'is_profile_completed' => $user->is_profile_completed,
                'is_verified' => $user->is_verified,
                'is_disabled' => $user->is_disabled,
                'availability' => $user->availability ? json_decode($user->availability) : [],
                'categories' => CategoryLink::getByUserId($user->id),
            ];
            $res['activities'] = TrainerActivity::getByUserId($user->id);
            $ratings = $user->reviews->sum('stars');
            $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
            $res['user']['average_rating'] = $ratings;
            $res['user']['total_reviews'] = $user->reviews->count();

        } elseif ($user->role_id == 0) {
                // normal user
            $res['user'] = $user;
        }
    }
    return $res;
}

    /**
     * @Description get full name of user
     * @param $userId
     * @return string
     * @Author Khuram Qadeer.
     */
    public static function getFullName($userId)
    {
        $res = '';
        $user = self::find((int)$userId);
        if ($user)
            $res = $user->first_name . ' ' . $user->last_name;
        return $res;
    }

    /**
     * @Description get User avatar
     * @param $userId
     * @return string
     * @Author Khuram Qadeer.
     */
    public static function getUserAvatar($userId)
    {
        $res = '/assets/images/default-dp.jpg';
        $user = self::find($userId);
        if ($user)
            if ($user->avatar)
                $res = asset($user->avatar);
            return $res;
        }

    /**
     * @Description Get Stripe customer id for user
     * @param $userId
     * @return string
     * @Author Khuram Qadeer.
     */
    public static function getStripeCustomerId($userId)
    {
        $res = '';
        $cards = Card::getAllByUserId($userId);
        if ($cards) {
            foreach ($cards as $card) {
                if ($card->stripe_customer_id) {
                    $res = $card->stripe_customer_id;
                    break;
                }
            }
        }
        return $res;
    }

    /**
     * @Description Get User by role id
     * @param $roleId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getByRoleId($roleId)
    {
        return self::where('role_id', $roleId)->orderByDesc('id')->get();
    }

    public function ratings()
    {

        return $this->hasMany(ReviewLink::class, 'given_to', 'id');
    }
    public function reviews()
    {

        return $this->hasMany(Review::class, 'given_to', 'id')->orderBy('id', 'DESC');
    }
    public function activities()
    {

        return $this->hasMany(TrainerActivity::class, 'user_id', 'id');
    }

}
