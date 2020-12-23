<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * @Description Get Role Name by id
     * @param $roleId
     * @return mixed
     * @Author Khuram Qadeer.
     */
    public static function getRoleNameByRoleId($roleId)
    {
        $res = '';
        if ($roleId == 0) {
            $res = 'normal user';
        } else {
            $res = self::find($roleId)['name'];
        }
        return $res;
    }
}
