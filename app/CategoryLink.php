<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryLink extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'category_id'];

    /**
     * @Description delete categories by user id
     * @param $userId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function deleteByUserId($userId)
    {
        return self::where('user_id', (int)$userId)->delete();
    }

    /**
     * @Description get By user id for trainer categories
     * @param $userId
     * @return array
     * @Author Khuram Qadeer.
     */
    public static function getByUserId($userId)
    {
        $res = [];
        $categoriesLinks = self::where('user_id', (int)$userId)->get();
        if ($categoriesLinks) {
            foreach ($categoriesLinks as $categoriesLink) {
                array_push($res, Category::find($categoriesLink->category_id));
            }
        }
        return $res;
    }

    /**
     * @Description check user/trainer have this category
     * @param $categoryId
     * @param $userId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function checkUserHaveCategory($categoryId, $userId)
    {
        return self::where([['user_id', $userId], ['category_id', $categoryId]])->exists();
    }
}
