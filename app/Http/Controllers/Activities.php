<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TrainerActivity;
use App\TrainerBooking;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Traits\ApiResponse;
use Carbon\Carbon;

class Activities extends Controller
{
	use ApiResponse;

	public function userActivities(Request $request,$user_id){
		$activities = TrainerActivity::where('user_id',$user_id)->with('user')->paginate(10);
		//return $activities;
		return view('activities.list', compact('activities'));
		
	}

	public function userSplitActivities(Request $request,$user_id){

		$all_bookings = [];
		$all_active_bookings = [];
		$all_pending_bookings = [];
		$all_past_bookings = [];

		$active_bookings = TrainerBooking::where('owner_id',$user_id)->with('activity','trainer.reviews','buyer')->where('accepted',1)->whereDate('booking_date', '>=', Carbon::now())->orderBy('booking_date', 'DESC')->get();
		$time_zone = ($request->time_zone)?$request->time_zone:+5;
		if(!empty($active_bookings)){
			foreach ($active_bookings as $active_booking) {
				$time = $active_booking->booking_date .' '. $active_booking->start_time;

				if($time > Carbon::now($time_zone)){
					$user = $active_booking->trainer;
					$ratings = $user->reviews->sum('stars');
					$ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
					$active_booking->trainer['average_rating'] = $ratings;
					$active_booking->trainer['total_reviews'] = $user->reviews->count();
					$all_active_bookings[] =$active_booking; 
				}
			}
		}
		$all_bookings['active_bookings'] =  $all_active_bookings;

		$all_pending_bookings = [];

		$pending_bookings = TrainerBooking::where('owner_id',$user_id)->with('activity','trainer.reviews','buyer')->where('accepted',0)->whereDate('booking_date', '>=', Carbon::now())->orderBy('booking_date', 'DESC')->get();


		if(!empty($pending_bookings)){
			foreach ($pending_bookings as $pending_booking) {
				$time = $pending_booking->booking_date .' '. $pending_booking->start_time;
				if($time > Carbon::now($time_zone) ){
					$user = $pending_booking->trainer;
					$ratings = $user->reviews->sum('stars');
					$ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
					$pending_booking->trainer['average_rating'] = $ratings;
					$pending_booking->trainer['total_reviews'] = $user->reviews->count();
					$all_pending_bookings[] =$pending_booking; 
				}
			}
		}
		$all_bookings['pending_bookings'] =  $all_pending_bookings;


		$past_bookings = TrainerBooking::where('owner_id',$user_id)->
		where(function ($query) {
			$query->orwhereDate('booking_date', '<', Carbon::now()->addDays(1))->orwhere('accepted',2);
		})->
		with('activity','trainer.reviews','buyer')->orderBy('booking_date', 'DESC')->get();

		if(!empty($past_bookings)){
			foreach ($past_bookings as $past_booking) {
				$time = $past_booking->booking_date .' '. $past_booking->start_time;
				if($time < Carbon::now($time_zone) || $past_booking->accepted == 2){
					$user = $past_booking->trainer;
					$ratings = $user->reviews->sum('stars');
					$ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
					$past_booking->trainer['average_rating'] = $ratings;
					$past_booking->trainer['total_reviews'] = $user->reviews->count();
					$all_past_bookings[] =$past_booking; 
				}
			}
		}

		$all_bookings['past_bookings'] =  $all_past_bookings;

		$user = User::find($user_id);
		$all_bookings['user'] =  $user;

		//return $activities;
		return view('activities.split-list', compact('all_bookings'));

	}

	public function searchActivities(Request $request,$user_id){

		if($request->key){
			$activities = TrainerActivity::search($request->key)->where('user_id',$user_id)->with('user')->get();
		}
		else{
			$activities = TrainerActivity::where('user_id',$user_id)->with('user')->paginate(10);
		}
		ob_start();
		if(!empty($activities->first())):
			foreach($activities as $activity):
				?>
				<div class="gym-panel">
					<div class="row">
						<div class="col-sm-2">
							<div class="gym-image-holder">
								<img src="<?php echo url('/') .'/'.$activity->image; ?>" alt="Activity Image" />
							</div>
						</div>
						<div class="col-sm-10">
							<div class="gym-content-panel">
								<div class="row">
									<div class="col-sm-9">
										<div class="d-flex align-self-center">
											<h3><?php echo $activity->name; ?></h3>

										</div>
									</div>
									<div class="col-sm-3">
										<div class="d-flex action-btn text-right pull-right">
											<a href="<?php echo url('/').'/get/activity/'.$activity->id.'/1'; ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
											<a href="<?php echo url('/').'/get/activity/'.$activity->id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											<a href="<?php echo url('/').'/delete/activity/'.$activity->id; ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
										</div>
									</div>

								</div>
								<small><i class="fa fa-user" aria-hidden="true"></i> <?php echo $activity->user->first_name.' '.$activity->user->first_name; ?><br><i class="fa fa-money" aria-hidden="true"></i>
									£ <?php echo number_format((float)$activity->price, 2, '.', ''); ?><br><i class="fa fa-clock-o" aria-hidden="true"></i>
									<?php echo $activity->duration; ?> Minutes</small>

								</div>
							</div>
						</div>
					</div>
					<?php
				endforeach;
			endif;

			return ob_get_clean();

		}

		public function saveActivity(Request $request)
		{
			$validator = \Validator::make($request->all(), [
				'name' => 'required',
				'price' => 'required|numeric',
				'duration' => 'required|numeric',
				'type' => 'required',
				'about' => 'required',
			]);
			if ($validator->fails()) {
				$this->setResponse($validator->errors()->first(), 0, 422, []);
				return response()->json($this->response, $this->status);
			}
			$fileUrl = null;

			$image = $request->image;
			if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
				$image = substr($image, strpos($image, ',') + 1);
				$type = strtolower($type[1]); 

				$image = base64_decode($image);
				$destinationPath    = 'assets/uploads/trainers/';

				if (!file_exists(public_path().'/'.$destinationPath)) {
                    //dd(public_path().$destinationPath);
					mkdir(public_path().'/'.$destinationPath, 0777, true);

				}
				$fileName = 'image_'.time().'.'.$type;

				$tempFile = $destinationPath . $fileName;

				$fileUrl = $destinationPath . $fileName;

				file_put_contents(public_path().'/'.$tempFile, $image);

			}

			$activity = TrainerActivity::find($request->activity_id);

			if($activity){
				$activity->name = $request->name;
				$activity->duration = $request->duration;
				$activity->price = $request->price;
				$activity->type = $request->type;
				$activity->address = $request->address;
				$activity->online_information = $request->online_information;
				$activity->lat = $request->lat;
				$activity->lng = $request->lng;
				$activity->about = $request->about;
				if($fileUrl){
					$activity->image = $fileUrl;
				}
				$activity->save();

			}

			Session::flash('alert-success', 'Trainer Activity has been updated.');
			$this->setResponse('success', 1, 200, []);
			return response()->json($this->response, $this->status);
		}
		public function getActivity($id='',$show_only=0){

			$activity = TrainerActivity::find($id);

		//return $activity;

			return view('activities.detail', compact('activity','show_only'));

		}

		public function delete($id)
		{
			$trainer = TrainerActivity::find($id);
			$user_id = $trainer->user_id;
			if($trainer){
				$trainer->delete();
			}



			Session::flash('alert-success', 'Trainer Activity has been deleted.');
			return redirect('activities/'.$user_id);
		}
	}
