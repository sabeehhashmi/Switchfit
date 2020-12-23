<?php

namespace App\Http\Controllers\Api;

use App\CategoryLink;
use App\Http\Controllers\Controller;
use App\TrainerActivity;
use App\Traits\ApiResponse;
use App\User;
use App\ReviewOption;
use App\FavouriteTrainer;
use App\Day;
use App\TrainerAvailabilty;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Card;
use App\TemporaryCard;
use App\TrainerBooking;
use App\Notification;
use App\Review;
use DB;
use Carbon\Carbon;
use Stripe;
use App\Http\Controllers\POSTrait;

class TrainerProfileController extends Controller
{
    use ApiResponse;
    use POSTrait;

    /**
     * @Description Update Basic info of trainer profile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function updateBasic(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $tempFile = '';
        //$file = $request->file('avatar');
        //dd($request->avatar);
        $image = $request->avatar;
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {

            $image = substr($image, strpos($image, ',') + 1);

            $type = strtolower($type[1]);

            $image = base64_decode($image);

            $destinationPath    = '/assets/uploads/trainers/';
                //dd(public_path().$destinationPath);
            if (!file_exists(public_path().$destinationPath)) {
                    //dd(public_path().$destinationPath);
                mkdir(public_path().$destinationPath, 0777, true);

            }
            $fileName = 'image_'.time().'.'.$type;

            $tempFile = $destinationPath . $fileName;
                //dd($image);
            file_put_contents(public_path().$tempFile, $image);
        }
        //document
        //dd($request->file('avatar'));
        /*if ($file) {
            $filename = str_replace(' ', '_', Str::random(10) . $file->getClientOriginalName());
            //Move Uploaded File
            $dirPath = 'assets/uploads/trainers/';
            $fileUrl = $dirPath . $filename;
            $file->move($dirPath, $filename);
        }*/

        $date_of_birth = ($request->date_of_birth)?date("m/d/Y", strtotime($request->date_of_birth)):'';

        $user = User::find((int)$request->user_id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->date_of_birth = $request->date_of_birth;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->lat = (double)$request->lat ?? null;
        $user->lng = (double)$request->lng ?? null;
        $user->country = $request->country;
        $user->state = $request->state;
        $user->city = $request->city;
        $user->postal_code = $request->postal_code;
        if(!empty($tempFile)){
            $user->avatar = $tempFile;
        }

        $user->save();

        $data = User::getUserData($request->user_id);
        $this->setResponse('Trainer profile has been updated.', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description update professional info of trainer profile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function updateProfessional(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'about' => 'required',
            'document_type' => 'required',
            'document_expire_date' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $image = $request->document;
        $tempFile = '';
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {

            $image = substr($image, strpos($image, ',') + 1);

            $type = strtolower($type[1]);

            $image = base64_decode($image);

            $destinationPath    = '/assets/uploads/trainers/';

            if (!file_exists(public_path().$destinationPath)) {

                mkdir(public_path().$destinationPath, 0777, true);

            }
            $fileName = 'image_'.time().'.'.$type;

            $tempFile = $destinationPath . $fileName;

            file_put_contents(public_path().$tempFile, $image);
        }

        $user = User::find((int)$request->user_id);
        if($user){

            $user->about = $request->about;
            $user->qualification_1 = $request->qualification_1;
            $user->qualification_2 = $request->qualification_2;
            $user->document_type = $request->document_type;
            $user->document_expire_date = $request->document_expire_date;
            if($tempFile){
                $user->document = $tempFile;
            }

            $user->is_profile_completed = ($tempFile || $user->document)?1:0;

        }
        $user->save();
        $data = User::getUserData($request->user_id);
        $this->setResponse('Trainer profile has been updated.', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }
    /*Get Availabilities Of the User*/
    public function getUserAvailablities(Request $request){

        $user_id = $request->user_id;
        $availibities = Day::with(['availability'=> function ($query)  use ($user_id) {
            $query->whereuser_id($user_id);

        }])->get();

        $this->setResponse('Trainer Availabilies.', 1, 200, $availibities);
        return response()->json($this->response, $this->status);

    }
    /**
     * @Description Update Trainer availability dates and times
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function updateAvailability(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'availability' => 'required',
        ]);
        $available = $request->availability;
        $available_times = $available;
        $validator->after(function ($validator) use ($available) {
            // Time Schedule validation checking end time greater must be greater then start time

            $available = json_decode($available);
            if (!empty($available)) {
                foreach ($available as $day) {
                    if (!checkTimeEndTimeGreatThanStartTime($day->time_start, $day->time_end)) {
                        $current_day = Day::find($day->day_id);
                        $validator->errors()->add($current_day->name, ucfirst($current_day->name) . ' end time must be greater than start time.');
                    }
                }
            }
        });

        $availibities = json_decode($available);

        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $existing_availablities = TrainerAvailabilty::where('user_id',$request->user_id);
        if(!empty(TrainerAvailabilty::where('user_id',$request->user_id)->first())){
            $existing_availablities->delete();
        }
        if (!empty($availibities)) {
            foreach ($availibities as $day) {
                $availibilty = new TrainerAvailabilty();

                $time_calculator = calculateMinutesHourse($day->time_start,$day->time_end);

                $availibilty->day_id = $day->day_id;
                $availibilty->available = $day->available;
                $availibilty->available = $day->available;
                $availibilty->time_start = $day->time_start;
                $availibilty->time_end = $day->time_end;
                $availibilty->user_id = $request->user_id;
                $availibilty->total_available_minutes = $time_calculator['total_minutes'];
                $availibilty->start_available_minutes_range = $time_calculator['start_range'];
                $availibilty->end_available_minutes_range = $time_calculator['end_range'];
                $availibilty->save();
            }
        }

        $user_id = $request->user_id;
        $availibities = Day::with(['availability'=> function ($query)  use ($user_id) {
            $query->whereuser_id($user_id);

        }])->get();

        //return $availibities;
        $data = $availibities;
        $this->setResponse('Trainer profile has been updated.', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description update categories of trainer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function updateCategories(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'category_ids' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $category_ids = $request->category_ids ? explode(',', $request->category_ids) : null;
        CategoryLink::deleteByUserId($request->user_id);
        if ($category_ids) {
            foreach ($category_ids as $category_id) {
                CategoryLink::create([
                    'user_id' => $request->user_id,
                    'category_id' => (int)$category_id,
                ]);
            }
        }
        $data = User::getUserData($request->user_id);
        $this->setResponse('Trainer profile has been updated.', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description search Trainer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function searchTrainers(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $data = [];
        $lat = (double)$request->lat;
        $lng = (double)$request->lng;
        $radius = $request->radius ? (int)$request->radius : 5000;
        $categoryIds = $request->category_ids ? explode(',', $request->category_ids) : null;
        $searchKey = $request->search_key ?? null;
        $pagination = (int)$request->pagination ?? 0;


        $usersByLocation = getNearByData('users', 'lat', 'lng', $lat, $lng, $radius);


        $nearTrainers = [];
        $filtered_data = [];
        $gender =  $request->gender;
        if ($usersByLocation) {
            foreach ($usersByLocation as $user) {
                if ($user->role_id == 3 & $user->is_verified == 1 & $user->is_disabled != 1 & $user->is_deleted!= 1) {
                    if($gender){
                        if($user->gender == $gender)
                        {
                          $activity_lowest_price = TrainerActivity::
                          where('user_id', $user->id)
                          ->orderBy('price', 'desc')->first();
                          $user->activity_lowest_price = ($activity_lowest_price)?$activity_lowest_price->price:0;
                          array_push($nearTrainers, $user);
                      }

                  }
                  else{
                    $activity_lowest_price = TrainerActivity::
                    where('user_id', $user->id)
                    ->orderBy('price', 'ASC')->first();
                    $user->activity_lowest_price = ($activity_lowest_price)?$activity_lowest_price->price:0;
                    array_push($nearTrainers, $user);
                }
            }
        }
    }


    if ($searchKey && $nearTrainers) {
        foreach ($nearTrainers as $trainer) {
            if (Str::contains(strtolower($trainer->first_name), strtolower($searchKey))
                || Str::contains(strtolower($trainer->last_name), strtolower($searchKey))
                || Str::contains(strtolower($trainer->about), strtolower($searchKey))
                || Str::contains(strtolower($trainer->postal_code), strtolower($searchKey))
                || Str::contains(strtolower($trainer->address), strtolower($searchKey))
                || Str::contains(strtolower($trainer->country), strtolower($searchKey))
                || Str::contains(strtolower($trainer->state), strtolower($searchKey))
                || Str::contains(strtolower($trainer->city), strtolower($searchKey))
            ) {
                array_push($data, $trainer);
        }
    }
} elseif ($nearTrainers) {
    $data = $nearTrainers;
}

if ($categoryIds && $data) {
    foreach ($data as $trainer) {

        foreach ($categoryIds as $categoryId) {
            if (\App\CategoryLink::checkUserHaveCategory((int)$categoryId, (int)$trainer->id)) {
                if (!in_array($trainer, $filtered_data)) {
                 array_push($filtered_data, $trainer);
                                                     //array_push($data, $trainer);
             }
         }
     }
 }
}

if($filtered_data){
    $data = $filtered_data;
}
$res = [];
if ($data) {
    $res = convertTrainerBasicInfoArr($data);

                                       // $res = collect($res)->sortBy('distance');

}

if ($pagination) {
    $res = makePaginate(collect($res), 15);
    $data_res = [];
    $data_res['total'] = $res->total();
    $data_res['current_page'] = $res->toArray()['current_page'];
    $data_res['next_page_url'] = $res->nextPageUrl();
    $data_res['prev_page_url'] = $res->previousPageUrl();
    $data_res['per_page'] = $res->perPage();
    $data_res['first_page_url'] = $res->toArray()['first_page_url'];
    $data_res['last_page_url'] = $res->toArray()['last_page_url'];
    $data_res['to'] = $res->toArray()['to'];
    $data_res['from'] = $res->toArray()['from'];

    $d = [];
    if ($res) {
        foreach ($res as $re) {
            array_push($d, $re);
        }
        $data_res['data'] = $d;
    }
    $res = $data_res;
}
$this->setResponse('success', 1, 200, $res);
return response()->json($this->response, $this->status);
}

    /**
     * @Description Create Activity
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function createActivity(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
            'duration' => 'required',
            'type' => 'required',
            'about' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $userId = $request->user()->id;
        $fileUrl = null;

        $tempFile = '';
        $image = $request->image;
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {

            $image = substr($image, strpos($image, ',') + 1);

            $type = strtolower($type[1]);

            $image = base64_decode($image);

            $destinationPath    = '/assets/uploads/trainers/';
                //dd(public_path().$destinationPath);
            if (!file_exists(public_path().$destinationPath)) {
                    //dd(public_path().$destinationPath);
                mkdir(public_path().$destinationPath, 0777, true);

            }
            $fileName = 'image_'.time().'.'.$type;

            $tempFile = $destinationPath . $fileName;
                //dd($image);
            file_put_contents(public_path().$tempFile, $image);
        }

        $trainer_activity = new TrainerActivity();
        $trainer_activity->user_id =(int)$userId;
        $trainer_activity->name = $request->name;
        $trainer_activity->price = (double)$request->price;
        $trainer_activity->duration = (int)$request->duration;
        $trainer_activity->type = $request->type;
        $trainer_activity->address = $request->address;
        $trainer_activity->online_information = $request->online_information;
        $trainer_activity->lat = (double)$request->lat;
        $trainer_activity->lng = (double)$request->lng;
        $trainer_activity->about = $request->about;
        $trainer_activity->image = $tempFile ?? null;

        $trainer_activity->save();


        $res = TrainerActivity::getByUserId($userId);
        $this->setResponse('success', 1, 200, $res);
        return response()->json($this->response, $this->status);
    }
    public function allactivities(Request $request){

        $res = TrainerActivity::getByUserId($request->user_id);
        $this->setResponse('success', 1, 200, $res);
        return response()->json($this->response, $this->status);
    }
    /**
     * @Description update Activity
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function updateActivity(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'duration' => 'required',
            'type' => 'required',
            'about' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $activity = TrainerActivity::find((int)$request->id);
        $userId = $request->user()->id;
        $fileUrl = null;
        $tempFile = '';
        $image = $request->image;
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {

            $image = substr($image, strpos($image, ',') + 1);

            $type = strtolower($type[1]);

            $image = base64_decode($image);

            $destinationPath    = '/assets/uploads/trainers/';
                //dd(public_path().$destinationPath);
            if (!file_exists(public_path().$destinationPath)) {
                    //dd(public_path().$destinationPath);
                mkdir(public_path().$destinationPath, 0777, true);

            }
            $fileName = 'image_'.time().'.'.$type;

            $tempFile = $destinationPath . $fileName;
                //dd($image);
            file_put_contents(public_path().$tempFile, $image);
        }

        $activity->user_id = (int)$userId;
        $activity->name = $request->name;
        $activity->price = (double)$request->price;
        $activity->duration = (int)$request->duration;
        $activity->type = $request->type;
        $activity->address = $request->address;
        $activity->online_information = $request->online_information;
        $activity->lat = (double)$request->lat;
        $activity->lng = (double)$request->lng;
        $activity->about = $request->about;
        if($tempFile){
            $activity->image = $tempFile;
        }
        $activity->save();
        $res = TrainerActivity::getByUserId($userId);
        $this->setResponse('Activity has been updated', 1, 200, $res);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description Delete Activity
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function deleteActivity(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $booking = TrainerBooking::where('activity_id',$request->id)->where(function ($query) {
            $query->orwhere('accepted',0)->orwhere('accepted',1);
        })->where('booking_date', '<', Carbon::now())->first();
        if($booking){
            $this->setResponse('Activity can\'t be deleted,Activity either have active or peding bookings', 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $activity = TrainerActivity::find((int)$request->id);
        $userId = $request->user()->id;
        if ($activity->image) {
            deleteFile($activity->image);
        }
        $message = $activity->name.' has been deleted';
        $activity->forceDelete();
        $res = TrainerActivity::getByUserId($userId);
        $this->setResponse($message, 1, 200, $res);
        return response()->json($this->response, $this->status);
    }

    /*Mark Favourit and Unfavourit Trainer*/

    public function makeFavTrainer(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'trainer_id' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $fvrt_trainer = FavouriteTrainer::where('user_id',$request->user_id)->where('trainer_id',$request->trainer_id)->first();
        if($fvrt_trainer){
            $fvrt_trainer->delete();

            $this->setResponse('Trainer removed from favourites', 1, 200, []);
            return response()->json($this->response, $this->status);
        }
        $fvrt_trainer = new FavouriteTrainer();
        $fvrt_trainer->user_id = $request->user_id;
        $fvrt_trainer->trainer_id = $request->trainer_id;
        $fvrt_trainer->save();

        $this->setResponse('Trainer has been added favourites', 1, 200, []);
        return response()->json($this->response, $this->status);


    }

    /*Get Favourit Trainers*/

    public function getFvrtTrainers(Request $request){

        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $data = [];

        $user_id = $request->user_id;
        $customer = User::find($user_id);

        $users = User::with('categories','reviews')

        ->whereIn('id', function($query)use ($user_id){
            $query->select('trainer_id')
            ->from(with(new FavouriteTrainer)->getTable())
            ->where('user_id', $user_id)->get();})

        ->addSelect(['price_starts_from' => TrainerActivity::select('price')
            ->whereColumn('user_id', 'users.id')
            ->orderBy('price', 'ASC')
            ->limit(1)])
        ->get();

        foreach ($users as $user){

            $ratings = $user->reviews->sum('stars');
            $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
            $user['average_rating'] = $ratings;
            $user['total_reviews'] = $user->reviews->count();
            $user['favourite'] = 1;
            $user['distance']= getCircleDistance($customer->lat,$customer->lng,$user->lat,$user->lng);

            array_push($data, $user);
        }
        $this->setResponse('favourite trainers list', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    /*Get Trainer Details*/
    public function getTrainerDetails(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'trainer_id' => 'required',

        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $user_id = ($request->user_id)?$request->user_id:0;

        $user = User::with('categories','ratings','activities','reviews')

        ->addSelect(['price_starts_from' => TrainerActivity::select('price')
            ->whereColumn('user_id', 'users.id')
            ->orderBy('price', 'ASC')
            ->limit(1)])

        ->addSelect(['favourite' => FavouriteTrainer::select('favorit')
            ->whereColumn('trainer_id', 'users.id')
            ->where('user_id', $user_id)
            ->limit(1)])
        ->find($request->trainer_id);

        $user['favourite'] = ($user->favourite==1)?$user->favourite:0;
        $ratings = $user->reviews->sum('stars');
        $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
        $user['average_rating'] = $ratings;
        $user['total_reviews'] = $user->reviews->count();
        $user['availibities'] = Day::with(['availability'=> function ($query)  use ($user) {
            $query->whereuser_id($user->id);

        }])->get();

        $this->setResponse('trainer detail', 1, 200, $user);
        return response()->json($this->response, $this->status);

    }
    public function getTrainerReviews(Request $request){

       $validator = \Validator::make($request->all(), [
        'trainer_id' => 'required',
    ]);

       if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }

    $final_data= [];
    $review_options_array = [];
    $trainer_id= $request->trainer_id;

    $reviews_options = ReviewOption::with(['reviews'=>function ($query)  use ($trainer_id) {
        $query->wheregiven_to($trainer_id);
    }])->where('active',1)->get();

    if($reviews_options->first()){
        foreach($reviews_options as $reviews_option){
            $ratings = $reviews_option->reviews->sum('stars');
            $ratings = ($ratings>0)?$ratings/$reviews_option->reviews->count():0;
            $reviews_option['average_rating'] = $ratings;
            $reviews_option['total_reviews'] = $reviews_option->reviews->count();
            $review_options_array[] = $reviews_option;
        }
    }

    $user_reviews = User::with('reviews.user','ratings')->find($trainer_id);

    $final_data['reviews_options'] = $review_options_array;
    $final_data['reviews_details'] = $user_reviews->reviews;

    $ratings = $user_reviews->reviews->sum('stars');
    $ratings = ($ratings>0)?$ratings/$user_reviews->reviews->count():0;
    $user_reviews['average_rating'] = $ratings;
    $user_reviews['total_reviews'] = $user_reviews->reviews->count();

    $final_data['trainer_data'] = $user_reviews;

    $this->setResponse('Trainer Ratings Details.', 1, 200, $final_data);
    return response()->json($this->response, $this->status);


}
public function getAvailableDates(Request $request){
    $validator = \Validator::make($request->all(), [
        'date' => 'required',
        'trainer_id' => 'required',
        'duration' => 'required',
        'activity_id' => 'required',

    ]);
    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }

    $trainer_id = $request->trainer_id;
    $availibity = Day::with(['availability'=> function ($query)  use ($trainer_id) {
        $query->whereuser_id($trainer_id)->whereavailable(1);

    }])->get();
    $date = $request->date;
    $month = $date[1]; // Month ID, 1 through to 12.
    $year = $date[0]; // Year in 4 digit 2009 format.
    $day_count = cal_days_in_month($type, $month, $year);
    $workdays = array();

    for ($i = 1; $i <= $day_count; $i++) {

        $date = $year.'/'.$month.'/'.$i; //format date
        $get_name = date('l', strtotime($date)); //get week day
        $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars

        //if not a weekend add day to array
        if($day_name != 'Sun' && $day_name != 'Sat'){
            $workdays[] = $i;
        }

    }

}
public function getAvailableSlots(Request $request){

    $validator = \Validator::make($request->all(), [
        'date' => 'required',
        'activity_id' => 'required',

    ]);
    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }

    $day = ($request->date)?date("l", strtotime($request->date)):'';

    $activity = TrainerActivity::find($request->activity_id);

    $user_id = $activity->user_id;
    $availibity = Day::where('name',$day)->with(['availability'=> function ($query)  use ($user_id) {
        $query->whereuser_id($user_id)->whereavailable(1);

    }])->first();

    if(empty( $availibity->availability)){
        $this->setResponse($day.' is not available for this trainer please select another day.', 1, 422, []);
        return response()->json($this->response, $this->status);
    }

    $slots = [];

    $duration = $activity->duration;



    for ($i = $availibity->availability->start_available_minutes_range; $i <= ($availibity->availability->end_available_minutes_range-$duration); $i+=$duration) {
        $booking = TrainerBooking::where('owner_id',$user_id)->where('booking_date',$request->date)->where('start_time_range',$i)->first();


        if($booking){


           continue;

       }
       else{
        $mod = $i%60;
        if($mod == 0){
            $hours = $i/60;
            $time_zone = ($request->time_zone)?$request->time_zone:+5;
            $time_hours = $hours;
            if(10>$hours){
                $time_hours = '0'.$time_hours;
            }
            $timeup = $request->date .' '.$time_hours.':'.'00';

            if($timeup > Carbon::now($time_zone)){

                $slots []= $hours.':'.'00';
            }
        }
        else{
            $remaining_minutes = $mod;
            $total_minutes = $i-$remaining_minutes;
            $hours = $total_minutes/60;
            $remaining_minutes = ($remaining_minutes<10)?'0'.$remaining_minutes:$remaining_minutes;
            $time_zone = ($request->time_zone)?$request->time_zone:+5;
            $time_hours = $hours;
            if(10>$hours){
                $time_hours = '0'.$time_hours;
            }
            $time = $request->date .' '.$time_hours.':'.$remaining_minutes;
            if($time > Carbon::now($time_zone)){
                $slots []= $hours.':'.$remaining_minutes;
            }

        }
    }
}
$data = [];

$data['slots'] = $slots;
$data['activity'] =  $activity;
$this->setResponse('Trainer Available Slots.', 1, 200, $data);
return response()->json($this->response, $this->status);

}

public function bookActivity(Request $request){

    $validator = \Validator::make($request->all(), [
        'activity_id' => 'required',
        'is_saved' => 'required',
        'book_time' => 'required',
        'user_id' => 'required',
        'date' => 'required',
    ]);

    if ((!isset($request->card_id) || !$request->card_id) && !$validator->fails()) {
        $validator = \Validator::make($request->all(), [
            'card_name' => 'required',
            'card_number' => 'required',
            'expire_month' => 'required',
            'expire_year' => 'required',
            'cvc' => 'required',
        ]);

        $stripeToken = getStripeToken($request->card_number, $request->expire_month, $request->expire_year, $request->cvc);
        if (isset($stripeToken['error'])) {
            $this->setResponse((string)$stripeToken['error'], 0, 422, []);
            return response()->json($this->response, $this->status);
        }
    }

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }

    $transaction = DB::transaction(function () use ($request) {
        try {
            $is_temporary = ($request->is_saved == 1 || $request->card_id)?0:1;
            $card_id = $request->card_id;
            $data ='';

            if(empty($card_id)){

                if( $request->is_saved == 1 ){
                    $card = new Card();
                }
                else{
                    $card = new TemporaryCard();
                }

                $card->user_id = $request->user_id;
                $card->card_name = $request->card_name;
                $card->card_number = $request->card_number;
                $card->expire_month = $request->expire_month;
                $card->expire_year = $request->expire_year;
                $card->cvc = $request->cvc;
                $card->type = getCreditCardType($request->card_number);
                $card->save();

                $card_id = $card->id;
            }

            $day = ($request->date)?date("l", strtotime($request->date)):'';
            $activity = TrainerActivity::find($request->activity_id);
            $trainer_id = $activity->user_id;
            $availibity = Day::where('name',$day)->with(['availability'=> function ($query)  use ($trainer_id) {
                $query->whereuser_id($trainer_id)->whereavailable(1);
            }])->first();

            $availibity_id = $availibity->availability->id;
            $start_time = $request->book_time;
            $time_split = explode(':', $start_time);
            $duration = $activity->duration;
            $minutes = (int)$time_split[1]+$duration;
            $hours = $time_split[0];
            $end_time = $hours.':'.$minutes;

            if($minutes >= 60){

                while($minutes > 59){
                    $minutes = $minutes-60;
                    $hours = $hours+1;
                    if($hours > 23){
                        $this->setResponse('Date is changed you can not select service in two dates.', 1, 422, $data);
                        return response()->json($this->response, $this->status);
                        break;
                    }
                }
                $minutes = ($minutes < 10)?'0'.$minutes:$minutes;
                $end_time = $hours.':'.$minutes;

            }

            $time_calculator = calculateMinutesHourse($start_time,$end_time);
            $booking = TrainerBooking::where('start_time',$start_time)->where('booking_date',$request->date)->where('activity_id',$request->activity_id)->first();
            if($booking){
             $this->setResponse('Sorry this slot is book recently please try another one.', 0, 422, $data);
             return response()->json($this->response, $this->status);
         }
         $booking = new TrainerBooking;
         $booking->availability_id = $availibity_id;
         $booking->price = $activity->price;
         $fee = (20 / 100) * $activity->price;
         $tax = (5.00 / 100) * $activity->price;
         $deduction =  $tax + $fee;
         $booking->fee = $fee;
         $booking->tax = $tax;
         $booking->receiveable = $activity->price - $deduction;
         $booking->start_time = $start_time;
         $booking->end_time = $end_time;
         $booking->duration = $duration;
         $booking->start_time_range = $time_calculator['start_range'];
         $booking->end_time_range = $time_calculator['end_range'];
         $booking->activity_id = $request->activity_id;
         $booking->buyer_id = $request->user_id;
         $booking->owner_id = $trainer_id;
         $booking->booking_date = $request->date;
         $booking->card_id = $card_id;
         $booking->is_temporary = $is_temporary;
         $booking->save();
         $booking = TrainerBooking::where('start_time',$start_time)->where('activity_id',$request->activity_id)->where('booking_date',$request->date)->first();
         $buyer = User::find($request->user_id);
         $notification = new Notification();
         $notification->screen = 'activity_detail';
         $notification->user_id = $trainer_id;
         $notification->source_id = $booking->id;
         $notification->description = $buyer->first_name.' sent you a request for booking '.$activity->name.' Training.';

         $notification->source_image = ltrim($buyer->avatar, '/');
         $trainer = User::find($trainer_id);
         if($trainer->notifications == 1){

            $notification->title = 'New Booking';
            $notification->sent = 0;

                /*$this->extraPayLoad['screen'] = $notification->screen;

                $this->extraPayLoad['source_id'] = $notification->source_id;
                $this->extraPayLoad['notificationTitle'] = $notification->title;

                $this->extraPayLoad['description'] =  $notification->description;
                $this->extraPayLoad['source_image'] = $notification->source_image;

                $this->notificationTitle = $notification->title;
                $this->notificationMessage = $notification->description;
                $this->deviceType = $trainer->device_type;
                $this->deviceTokens = [$trainer->device_id];

                $this->sendNotification();*/

                $data = $booking;

            }
            $notification->save();

            $data = $booking;

            $this->setResponse('Booking for '.$activity->name.' has been completed, You will receive confirmation shortly.', 1, 200, $data);
            return response()->json($this->response, $this->status);

        }catch (Exception $ex) {
            DB::rollback();
        }
    });

return $transaction;
}
public function getUserBookings(Request $request){



    $validator = \Validator::make($request->all(), [
        'buyer_id' => 'required',
    ]);

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }
    $all_bookings = [];
    $all_active_bookings = [];
    $all_pending_bookings = [];
    $all_past_bookings = [];

    $active_bookings = TrainerBooking::where('buyer_id',$request->buyer_id)->with('activity','trainer.reviews','buyer')->where('accepted',1)->whereDate('booking_date', '>=', Carbon::now())->orderBy('id', 'DESC')->get();
    $time_zone = ($request->time_zone)?$request->time_zone:+5;
    if(!empty($active_bookings)){
        foreach ($active_bookings as $active_booking) {
            $time = $active_booking->booking_date .' '. $active_booking->end_time;

            if($time > Carbon::now($time_zone)){
                $user = $active_booking->trainer;
                $ratings = ((isset($user->reviews)))?$user->reviews->sum('stars'):0;
                $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
                if(isset($active_booking->trainer) ){
                   if($active_booking->trainer->is_deleted == 0){
                    $active_booking->trainer['average_rating'] = $ratings;
                    $active_booking->trainer['total_reviews'] = $user->reviews->count();
                    $all_active_bookings[] =$active_booking;
                }
            }
        }
    }
}
$all_bookings['active_bookings'] =  $all_active_bookings;

$pending_bookings = TrainerBooking::where('buyer_id',$request->buyer_id)->with('activity','trainer.reviews','buyer')->where('accepted',0)->whereDate('booking_date', '>=', Carbon::now())->orderBy('id', 'DESC')->get();

if(!empty($pending_bookings)){
    foreach ($pending_bookings as $pending_booking) {

        $user = $pending_booking->trainer;
        $ratings = (isset($user->reviews))?$user->reviews->sum('stars'):0;
        $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
        if(isset($pending_booking->trainer)){
            if($pending_booking->trainer->is_deleted == 0){
                $pending_booking->trainer['average_rating'] = $ratings;
                $pending_booking->trainer['total_reviews'] = $user->reviews->count();
                $all_pending_bookings[] = $pending_booking;

            }
        }

    }
}
$all_bookings['pending_bookings'] =  $all_pending_bookings;

$past_bookings = TrainerBooking::where('buyer_id',$request->buyer_id)->with('activity','trainer.reviews','buyer')->where(function ($query) {
    $query->orwhere('booking_date', '<', Carbon::now()->addDays(1))->orwhere('accepted',2);
})->orderBy('id', 'DESC')->get();

if(!empty($past_bookings->first())){
    foreach ($past_bookings as $past_booking) {
       $time = $past_booking->booking_date .' '. $past_booking->end_time;
       if($time < Carbon::now($time_zone) || $past_booking->accepted == 2) {
        $user = $past_booking->trainer;
        $ratings = (isset($user->reviews))?$user->reviews->sum('stars'):0;
        $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
        if(isset($past_booking->trainer)){
            $past_booking->trainer['average_rating'] = $ratings;
            $past_booking->trainer['total_reviews'] = $user->reviews->count();
            $all_past_bookings[] = $past_booking;
        }
    }

}
}

$all_bookings['past_bookings'] =  $all_past_bookings;
$this->setResponse('Booking data.', 1, 200, $all_bookings);
return response()->json($this->response, $this->status);

}

public function getTrainerBookings(Request $request){

    $validator = \Validator::make($request->all(), [
        'trainer_id' => 'required',
    ]);

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }
    $all_bookings = [];
    $all_active_bookings = [];
    $all_pending_bookings = [];
    $all_past_bookings = [];

    $active_bookings = TrainerBooking::where('owner_id',$request->trainer_id)->with('activity','trainer.reviews','buyer')->where('accepted',1)->whereDate('booking_date', '>=', Carbon::now())->orderBy('id', 'DESC')->get();
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


    $past_bookings = TrainerBooking::where('owner_id',$request->trainer_id)->whereDate('booking_date', '<', Carbon::now()->addDays(1))->where('accepted',1)->
    with('activity','trainer.reviews','buyer')->orderBy('id', 'DESC')->get();

    if(!empty($past_bookings)){
        foreach ($past_bookings as $past_booking) {
            $time = $past_booking->booking_date .' '. $past_booking->start_time;
            if($time < Carbon::now($time_zone)){
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
    $this->setResponse('Booking data.', 1, 200, $all_bookings);
    return response()->json($this->response, $this->status);

}
public function getTrainerPendigBookings(Request $request){

    $validator = \Validator::make($request->all(), [
        'trainer_id' => 'required',
    ]);

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }

    $all_bookings = [];
    $all_pending_bookings = [];

    $pending_bookings = TrainerBooking::where('owner_id',$request->trainer_id)->with('activity','trainer.reviews','buyer')->where('accepted',0)->whereDate('booking_date', '>=', Carbon::now())->orderBy('booking_date', 'ASC')->orderBy('start_time','ASC')->get();

    $time_zone = ($request->time_zone)?$request->time_zone:+5;

    if(!empty($pending_bookings)){


        foreach ($pending_bookings as $pending_booking) {

         $time_hours = explode(':', $pending_booking->start_time);
         $hours = $time_hours[0];
         if(10>$hours){
            $hours = '0'.$hours;
        }
        $timeup = $pending_booking->booking_date .' '.$hours.':'.$time_hours[1];

        if($timeup > Carbon::now($time_zone)){

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
$all_bookings['total_count'] =   $pending_bookings->count();



$this->setResponse('Booking data.', 1, 200, $all_bookings);
return response()->json($this->response, $this->status);

}
public function AcceptRejectActivity(Request $request){
    $validator = \Validator::make($request->all(), [
        'booking_id' => 'required',
        'accepted' => 'required'
    ]);

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }

    $transaction = DB::transaction(function () use ($request) {
        try {

            $booking = TrainerBooking::with('activity','trainer.reviews','buyer')->find($request->booking_id);

            $booking->accepted = $request->accepted;

            $booking->save();
            if($request->accepted ==  1){
                if($booking->is_temporary == 1){
                   $card = TemporaryCard::find($booking->card_id);


               }
               else{
                 $card = Card::find($booking->card_id);
             }
             if(empty($card)){
              $this->setResponse('Provided card was deleted from so can not be booked', 0, 422, []);
                return response()->json($this->response, $this->status);
             }
             $stripeToken = getStripeToken($card->card_number, $card->expire_month, $card->expire_year, $card->cvc);
             if (isset($stripeToken['error'])) {
                $this->setResponse((string)$stripeToken['error'], 0, 422, []);
                return response()->json($this->response, $this->status);
            }
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));



            $orderID = strtoupper(str_replace('.','',uniqid('', true)));
            try{
                $charge = Stripe\Charge::create ([

                    "amount" =>  round($booking->activity->price*100),

                    "currency" => "gbp",

                    "source" => $stripeToken['token']->id,

                    "description" => 'Amount Added to wallet through activity purchase' ,
                    "expand" => array("balance_transaction"),
                    'metadata' => array(
                       'order_id' => $orderID,
                   )

                ]);
            }catch (\Stripe\Exception\ApiErrorException $e) {
                $this->setResponse($e->getMessage(), 0, 422, []);
                return response()->json($this->response, $this->status);

            }
        }

        if($booking->is_temporary == 1){
           $card = TemporaryCard::where('id',$booking->card_id)->delete();
       }
       $notification = Notification::where('source_id',$request->booking_id)->first();
       if($notification){

        $notification->delete();
    }
    $status  = ($request->accepted==1)?'Approved':'Rejected';

    $buyer = User::find($booking->buyer_id);
    $trainer = User::find($booking->owner_id);

    $notification = new Notification();
    $notification->screen = 'activity_detail';
    $notification->user_id = $booking->buyer_id;
    $notification->source_id = $booking->id;
    $notification->description = $trainer->first_name.' '.$status.' your request. Tap to view!';

    $notification->source_image = ltrim($booking->activity->image, '/');


    if($buyer->notifications == 1){

        $notification->title = 'Booking '.$status;;
        $notification->sent = 0;
        /*$this->extraPayLoad['screen'] = $notification->screen;

        $this->extraPayLoad['source_id'] = $notification->source_id;
        $this->extraPayLoad['notificationTitle'] = $notification->title;

        $this->extraPayLoad['description'] =  $notification->description;
        $this->extraPayLoad['source_image'] = $notification->source_image;

        $this->notificationTitle = $notification->title;
        $this->notificationMessage = $notification->description;
        $this->deviceType = $buyer->device_type;
        $this->deviceTokens = [$buyer->device_id];

        $this->sendNotification();*/
    }
    $notification->save();
    $this->setResponse('Booking for '.$booking->activity->name.' has been '.$status, 1, 200, $booking);
    return response()->json($this->response, $this->status);

}catch (Exception $ex) {
    DB::rollback();
}
});

    return $transaction;


}

public function getTrainerUpComingBookingsDates(Request $request){

    $validator = \Validator::make($request->all(), [
        'trainer_id' => 'required',
    ]);

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }


    $upcoming_bookings = TrainerBooking::where('owner_id',$request->trainer_id)->where('accepted',1)->whereDate('booking_date', '>', Carbon::now())->orderBy('id', 'DESC')->groupBy('booking_date')->pluck('booking_date');


    $this->setResponse('Up Coming Booking dates.', 1, 200, $upcoming_bookings);
    return response()->json($this->response, $this->status);

}
public function getTrainerUpComingBookings(Request $request){

    $validator = \Validator::make($request->all(), [
        'trainer_id' => 'required',
        'date' => 'required',

    ]);

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }

    $all_bookings = [];
    $all_pending_bookings = [];

    $pending_bookings = TrainerBooking::where('owner_id',$request->trainer_id)->with('activity','trainer.reviews','buyer')->where('accepted',1)->whereDate('booking_date', '=', $request->date)->orderBy('start_time', 'ASC')->get();

    $time_zone = ($request->time_zone)?$request->time_zone:+5;

    if(!empty($pending_bookings)){
        foreach ($pending_bookings as $pending_booking) {

            $time = $pending_booking->booking_date .' '. $pending_booking->start_time;

            if($time > Carbon::now($time_zone)){
                $user = $pending_booking->trainer;
                $ratings = $user->reviews->sum('stars');
                $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
                $pending_booking->trainer['average_rating'] = $ratings;
                $pending_booking->trainer['total_reviews'] = $user->reviews->count();
                $all_pending_bookings[] =$pending_booking;
            }
        }
    }
    $all_bookings['upcoming_bookings'] =  $all_pending_bookings;

    $this->setResponse('Up Coming Bookings.', 1, 200, $all_bookings);
    return response()->json($this->response, $this->status);

}

/*Reviews Details*/
public function getTrainerReviewsDetails(Request $request){

   $validator = \Validator::make($request->all(), [
    'trainer_id' => 'required',
]);

   if ($validator->fails()) {
    $this->setResponse($validator->errors()->first(), 0, 422, []);
    return response()->json($this->response, $this->status);
}

$final_data= [];
$review_options_array = [];
$trainer_id= $request->trainer_id;


$user_reviews = User::with('reviews.user','reviews.activity')->find($trainer_id);


$final_data['reviews_details'] = $user_reviews->reviews;

$this->setResponse('Trainer Ratings Details.', 1, 200, $final_data);
return response()->json($this->response, $this->status);


}

/*Get Individual Rating Detail*/
public function getActivityIndividualReviews(Request $request){

   $validator = \Validator::make($request->all(), [
    'booking_id' => 'required',
]);

   if ($validator->fails()) {
    $this->setResponse($validator->errors()->first(), 0, 422, []);
    return response()->json($this->response, $this->status);
}

$final_data= [];
$review_options_array = [];
$booking_id= $request->booking_id;

$reviews_options = ReviewOption::with(['reviews'=>function ($query)  use ($booking_id) {
    $query->wherebooking_id($booking_id)->with('activity');
}])->where('active',1)->get();

$review = Review::where('booking_id',$booking_id)->with('activity')->first();

$final_data['avg_stars'] = ($review)?$review->stars:0;
$final_data['description'] = ($review)?$review->description:'';

$final_data['activity_name'] = ($review->activity)?$review->activity->name:'';
$final_data['options'] = $reviews_options;

$this->setResponse('Trainer Ratings Details.', 1, 200, $final_data);
return response()->json($this->response, $this->status);


}
/*Get Booking Detail Single*/
public function getBookingDetail(Request $request){

    $validator = \Validator::make($request->all(), [
        'booking_id' => 'required',
    ]);

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }


    $active_booking = TrainerBooking::with('activity','trainer.reviews','buyer')->find($request->booking_id);


    $user = $active_booking->trainer;
    $ratings = $user->reviews->sum('stars');
    $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
    $active_booking->trainer['average_rating'] = $ratings;
    $active_booking->trainer['total_reviews'] = $user->reviews->count();

    $this->setResponse('Booking data.', 1, 200, $active_booking);
    return response()->json($this->response, $this->status);

}

/*Get Individual Rating Detail*/
public function getNoificationActivityIndividualReviews(Request $request){

   $validator = \Validator::make($request->all(), [
    'booking_id' => 'required'
]);

   if ($validator->fails()) {
    $this->setResponse($validator->errors()->first(), 0, 422, []);
    return response()->json($this->response, $this->status);
}

$final_data= [];
$review_options_array = [];

$booking = TrainerBooking::find($request->booking_id );

$booking_id= $request->booking_id;
$reviews_options = ReviewOption::with(['reviews'=>function ($query)  use ($given_by,$activity_id) {
    $query->wherebooking_id($booking_id)->with('activity')->first();
}])->where('active',1)->get();



$this->setResponse('Trainer Ratings Details.', 1, 200, $reviews_options);
return response()->json($this->response, $this->status);


}
public function getPayoutDetail(Request $request){

    $validator = \Validator::make($request->all(), [
        'trainer_id' => 'required'
    ]);

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }
    $all_bookings = [];

    $bookings =  DB::table('trainer_bookings')
    ->select(DB::raw('payout'),DB::raw('booking_date'), DB::raw('sum(accepted) as total_activites'),DB::raw('CAST(sum(receiveable) AS DECIMAL(12,2)) total_receiveable'))
    ->groupBy(DB::raw('booking_date') )
    ->where('owner_id',$request->trainer_id)
    ->where('accepted',1);

    $payable = TrainerBooking::where('payout',0)->where('accepted',1)->where('owner_id',$request->trainer_id);

    if($request->from_date && $request->to_date){
        $bookings =  $bookings
        ->whereDate('booking_date','<=',  $request->to_date)
        ->whereDate('booking_date', '>=', $request->from_date)
        ->orderBy('booking_date','ASC')
        ->get();

        $payable =  $payable
        ->whereDate('booking_date','<=',  $request->to_date)
        ->whereDate('booking_date', '>=', $request->from_date)
        ->get();

    }elseif($request->from_date){
        $bookings =  $bookings->whereDate('booking_date', '>=', $request->from_date)->orderBy('booking_date','DESC')->get();

        $payable =  $payable->whereDate('booking_date', '>=', $request->from_date)->get();
    }
    elseif($request->to_date){
        $bookings =  $bookings->whereDate('booking_date','<=',  $request->to_date)->orderBy('booking_date','DESC')->get();
        $payable =  $payable->whereDate('booking_date','<=',  $request->to_date)->get();
    }
    else{
        $bookings =  $bookings->orderBy('booking_date','DESC')->get();
        $payable =  $payable->get();
    }


    $all_bookings['bookings'] = $bookings;
    $payable= ($payable->first())?$payable->sum('receiveable'):0;

    $total_payment =  $bookings->sum('total_receiveable');
    $all_bookings['payable'] = $payable;
    $all_bookings['total_payment'] = $total_payment;
    $this->setResponse('Trainer Bookings Detail.', 1, 200, $all_bookings);
    return response()->json($this->response, $this->status);
}

/*Get Booking Detail Single*/
public function getDateViseBookings(Request $request){

    $validator = \Validator::make($request->all(), [
        'booking_date' => 'required',
        'trainer_id' => 'required'
    ]);

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }


    $booking = TrainerBooking::with('activity','trainer','buyer')->whereDate('booking_date',$request->booking_date)->where('accepted',1)->where('owner_id',$request->trainer_id)->get();

    $this->setResponse('Booking data.', 1, 200, $booking);
    return response()->json($this->response, $this->status);

}
public function userCancelBooking(Request $request){

 $validator = \Validator::make($request->all(), [
    'booking_id' => 'required',
]);

 if ($validator->fails()) {
    $this->setResponse($validator->errors()->first(), 0, 422, []);
    return response()->json($this->response, $this->status);
}

$transaction = DB::transaction(function () use ($request) {
    try {

        $booking = TrainerBooking::with('activity','trainer.reviews','buyer')->find($request->booking_id);

        $booking->accepted = 2;

        $booking->save();

         $notification = Notification::where('source_id',$request->booking_id)->first();
       if($notification){

        $notification->delete();
    }

        $buyer = $booking->buyer;
        $trainer = $booking->trainer;

        $notification = new Notification();
        $notification->screen = 'trainer_cancelation';
        $notification->user_id = $booking->owner_id;
        $notification->source_id = $booking->id;
        $notification->description = $booking->activity->name.' has been canceled by '. $buyer->first_name.'. Tap to view!';

        $notification->source_image = ltrim($booking->activity->image, '/');


        if($buyer->notifications == 1){

            $notification->title = 'Booking Canceled';
            $notification->sent = 0;
        /*$this->extraPayLoad['screen'] = $notification->screen;

        $this->extraPayLoad['source_id'] = $notification->source_id;
        $this->extraPayLoad['notificationTitle'] = $notification->title;

        $this->extraPayLoad['description'] =  $notification->description;
        $this->extraPayLoad['source_image'] = $notification->source_image;

        $this->notificationTitle = $notification->title;
        $this->notificationMessage = $notification->description;
        $this->deviceType = $buyer->device_type;
        $this->deviceTokens = [$buyer->device_id];

        $this->sendNotification();*/
    }
    $notification->save();
    $this->setResponse($booking->activity->name.' has been canceled by '. $buyer->first_name.'. Tap to view!', 1, 200, $booking);
    return response()->json($this->response, $this->status);

}catch (Exception $ex) {
    DB::rollback();
}
});

return $transaction;



}
public function trainerCancelBooking(Request $request){

 $validator = \Validator::make($request->all(), [
    'booking_id' => 'required',
]);

 if ($validator->fails()) {
    $this->setResponse($validator->errors()->first(), 0, 422, []);
    return response()->json($this->response, $this->status);
}

$transaction = DB::transaction(function () use ($request) {
    try {

        $booking = TrainerBooking::with('activity','trainer.reviews','buyer')->find($request->booking_id);

        $booking->accepted = 2;

        $booking->save();

         $notification = Notification::where('source_id',$request->booking_id)->first();
       if($notification){

        $notification->delete();
    }

        $buyer = $booking->buyer;
        $trainer = $booking->trainer;

        $notification = new Notification();
        $notification->screen = 'user_cancelation';
        $notification->user_id = $booking->buyer_id;
        $notification->source_id = $booking->id;
        $notification->description = $booking->activity->name.' has been canceled by '. $trainer->first_name.'. Tap to view!';

        $notification->source_image = ltrim($booking->activity->image, '/');


        if($buyer->notifications == 1){

            $notification->title = 'Booking canceled';
            $notification->sent = 0;
        /*$this->extraPayLoad['screen'] = $notification->screen;

        $this->extraPayLoad['source_id'] = $notification->source_id;
        $this->extraPayLoad['notificationTitle'] = $notification->title;

        $this->extraPayLoad['description'] =  $notification->description;
        $this->extraPayLoad['source_image'] = $notification->source_image;

        $this->notificationTitle = $notification->title;
        $this->notificationMessage = $notification->description;
        $this->deviceType = $buyer->device_type;
        $this->deviceTokens = [$buyer->device_id];

        $this->sendNotification();*/
    }
    $notification->save();
    $this->setResponse($booking->activity->name.' has been canceled by '. $buyer->first_name.'. Tap to view!', 1, 200, $booking);
    return response()->json($this->response, $this->status);

}catch (Exception $ex) {
    DB::rollback();
}
});

return $transaction;



}
}
