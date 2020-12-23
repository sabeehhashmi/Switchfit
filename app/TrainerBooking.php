<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainerBooking extends Model
{
	protected $table='trainer_bookings';

	public function trainer()
	{

		return $this->belongsTo(User::class, 'owner_id', 'id');
	}
	public function buyer()
	{

		return $this->belongsTo(User::class, 'buyer_id', 'id');
	}
	public function activity()
	{

		return $this->belongsTo(TrainerActivity::class, 'activity_id', 'id');
	}
}
