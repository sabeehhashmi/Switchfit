<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
	protected $table='days';

	public function availability()
	{

		return $this->hasOne(TrainerAvailabilty::class, 'day_id','id');
	}
}
