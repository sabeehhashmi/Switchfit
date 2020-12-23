<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
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
        $facility = self::find((int)$id);
        if ($facility){
            if ($facility->icon) {
                deleteFile($facility->icon);
            }
            $facility->delete();
        }
    }
}
