<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faqs extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['question', 'answer'];
}
