<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TrainerActivity;
use App\Traits\ApiResponse;
use App\User;
use App\ReviewOption;
use App\FavouriteTrainer;
use App\Day;
use App\TrainerAvailabilty;
use Illuminate\Support\Str;
use App\Card;
use App\TemporaryCard;
use App\TrainerBooking;
use DB;
use Carbon\Carbon;
use Stripe;
use App\CategoryLink;
use App\Notification;

class Notifications extends Controller
{
	use ApiResponse;

	public function getNotifications(Request $request){

		$validator = \Validator::make($request->all(), [
			'user_id' => 'required',
		]);
		if ($validator->fails()) {
			$this->setResponse($validator->errors()->first(), 0, 422, []);
			return response()->json($this->response, $this->status);
		}

		$data = Notification::where('user_id',$request->user_id)->orderBy('id', 'DESC')->paginate(20);
		$this->setResponse('All Notifications for current user.', 1, 200, $data);
		return response()->json($this->response, $this->status);

	}
	public function readNotifocations(Request $request){

		$validator = \Validator::make($request->all(), [
			'user_id' => 'required',
		]);
		if ($validator->fails()) {
			$this->setResponse($validator->errors()->first(), 0, 422, []);
			return response()->json($this->response, $this->status);
		}

		$data = Notification::where('user_id',$request->user_id)->update(['is_read' => 1]);

		$this->setResponse('Notification marked read.', 1, 200, []);
		return response()->json($this->response, $this->status);
	}
	
	public function getNotificationsCount(Request $request){

		$validator = \Validator::make($request->all(), [
			'user_id' => 'required',
		]);
		if ($validator->fails()) {
			$this->setResponse($validator->errors()->first(), 0, 422, []);
			return response()->json($this->response, $this->status);
		}

		$data = Notification::where('user_id',$request->user_id)->where('is_read',0)->orderBy('id', 'DESC')->get();
		$data = $data->count();
		$this->setResponse('All Notifications for current user.', 1, 200, $data);
		return response()->json($this->response, $this->status);

	}
}
