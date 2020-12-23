<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    /**
     * @var array
     * @Author Khuram Qadeer.
     */
    protected $fillable = ['name', 'icon'];

    /**
     * @Description Delete By Id
     * @param $id
     * @Author Khuram Qadeer.
     */
    public static function deleteById($id)
    {
        $amenity = self::find((int)$id);
        if ($amenity->icon) {
            deleteFile($amenity->icon);
        }
        $amenity->delete();
    }
}
