<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainerAvailabilty extends Model
{
    protected $table='trainer_availabilties';

    public function user()
	{

		return $this->belongsTo(User::class, 'user_id', 'id');
	}
}
