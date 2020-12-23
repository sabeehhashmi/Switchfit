<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @Description Delete By Id
     * @param $id
     * @Author Khuram Qadeer.
     */
    public static function deleteById($id)
    {
        $category = self::find((int)$id);
        if ($category->icon) {
            deleteFile($category->icon);
        }
        $category->delete();
    }
}
