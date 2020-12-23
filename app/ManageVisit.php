<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManageVisit extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['order_item_id', 'pass_token', 'time', 'current_visit_no'];
}
