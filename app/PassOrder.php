<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PassOrder extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['buyer_id', 'book_date', 'total'];
}
