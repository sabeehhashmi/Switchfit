<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Review;
use App\ReviewLink;
use App\ReviewOption;
use App\TrainerBooking;
use App\Gym;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Notification;
use App\User;
use App\Http\Controllers\POSTrait;


class ReviewController extends Controller
{
    use ApiResponse;
    use POSTrait;

    /**
     * @Description Get Review Options
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function getReviewOptions(Request $request)
    {
        $data = ReviewOption::getReviewOptions();
        $this->setResponse('success.', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description make review to gym
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function makeReview(Request $request)
    {
        $validator = \Validator::make($request->all(), [

            'given_by' => 'required',
            'given_to' => 'required',            
            'stars' => 'required',
            'options_stars.*.option_id' => 'required',
            'options_stars.*.stars' => 'required',
            'description' => 'required',
        ]);
        $optionsReviews = json_decode($request->options_stars);
        $validator->after(function ($validator) use ($optionsReviews) {
            if (collect($optionsReviews)->count() < 6) {
                $validator->errors()->add('options_stars', 'Please, Give stars to all rating options.');
            }
        });

        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        if ($request->review_type == 'activity' && !$validator->fails()) {
            $validator = \Validator::make($request->all(), [
                'activity_id' => 'required',
                'booking_id' => 'required',
            ]);
        }else{
            $validator = \Validator::make($request->all(), [
                'gym_id' => 'required',
                'order_item_id' => 'required',
            ]);
        }

        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        
        $totalStars = round((double)(collect($optionsReviews)->sum('stars') + (double)$request->stars) / 7, 1);
            //return $request->activity_id;
        // make review and save total stars average
        $review = new Review();
        $review->order_item_id = $request->order_item_id;
        $review->given_by = $request->given_by;
        $review->given_to = $request->given_to;
        $review->gym_id = (int)$request->gym_id??null;
        $review->activity_id = (int)$request->activity_id??null;
        $review->booking_id = (int)$request->booking_id??null;
        $review->stars = (double)$totalStars;
        $review->description = $request->description;
        //return  $review;
        $review->save();

        // gym main rating without option
        $review = new ReviewLink;
        $review->given_by = (int)$request->given_by;
        $review->given_to = (int)$request->given_to;
        $review->gym_id = (int)$request->gym_id??null;
        $review->activity_id = (int)$request->activity_id??null;
        $review->booking_id = (int)$request->booking_id??null;
        $review->stars = (double)$request->stars;
        $review->review_id = (int)$review->id;
        $review->save();

        // option options ratings
        if ($optionsReviews) {
            foreach ($optionsReviews as $optionsReview) {
                $review = new ReviewLink;
                $review->given_by = (int)$request->given_by;
                $review->given_to = (int)$request->given_to;
                $review->gym_id = (int)$request->gym_id??null;
                $review->activity_id = (int)$request->activity_id??null;
                $review->booking_id = (int)$request->booking_id??null;
                $review->stars = (double)$optionsReview->stars;
                $review->review_id = (int)$review->id;
                $review->review_option_id = (int)$optionsReview->option_id;
                $review->save();
                
            }
        }

        if($request->review_type == 'activity'){
            $booking = TrainerBooking::find($request->booking_id);
            $booking->reviewed = 1;
            $booking->save();
        }
        
        $user = User::find($request->given_by);
        $notification = new Notification();
        $notification->screen = ($request->review_type == 'activity')?'trainer_rating':'gym_rating';
        $notification->user_id = $request->given_to;
        $notification->source_id = ($request->review_type == 'activity')?$request->booking_id:$request->gym_id;
        $notification->description = $user->first_name.'  gave you reviews.';
        $notification->source_image =  ltrim($user->avatar, '/');
        
        $trainer = User::find($request->given_to);
        if($trainer->notifications == 1 && $request->review_type == 'activity'){

            /*$this->extraPayLoad['screen'] = ($request->review_type == 'activity')?'trainer_rating':'gym_rating';

            $this->extraPayLoad['source_id'] = ($request->review_type == 'activity')?$request->booking_id:$request->gym_id;
            $this->extraPayLoad['notificationTitle'] = 'Review Posted';

            $this->extraPayLoad['description'] =   $user->first_name.'  gave you reviews.';
            $this->extraPayLoad['source_image'] = ltrim($user->avatar, '/');

            $this->notificationTitle = 'Review Posted';
            $this->notificationMessage = $user->first_name.'  gave you reviews.';
            $this->deviceType = $trainer->device_type;
            $this->deviceTokens = [$trainer->device_id];

            $this->sendNotification();*/

            $notification->title = 'Review Posted';
            $notification->sent = 0;

        }

        $notification->save();

        $this->setResponse('Review has been completed.', 1, 200, []);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description Get Gym Reviews Details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function getGymReviews(Request $request)
    {

     $validator = \Validator::make($request->all(), [
        'gym_id' => 'required',
    ]);

     if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }

    $final_data= [];
    $review_options_array = []; 
    $gym_id= $request->gym_id;

    $reviews_options = ReviewOption::with(['reviews'=>function ($query)  use ($gym_id) {
        $query->wheregym_id($gym_id);
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

    $gym_reviews = Gym::with('reviews.user','ratings')->find($gym_id);

    $final_data['reviews_options'] = $review_options_array;
    $final_data['reviews_details'] = $gym_reviews->reviews;

    $ratings = $gym_reviews->reviews->sum('stars');
    $ratings = ($ratings>0)?$ratings/$gym_reviews->reviews->count():0;
    $gym_reviews['average_rating'] = $ratings;
    $gym_reviews['total_reviews'] = $gym_reviews->reviews->count();

    $final_data['gym_data'] = $gym_reviews;

    $this->setResponse('Gym Ratings Details.', 1, 200, $final_data);
    return response()->json($this->response, $this->status);
        /*$validator = \Validator::make($request->all(), [
            'gym_id' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $data = Review::getReviewByGymId((int)$request->gym_id);
        $this->setResponse('success.', 1, 200, $data);
        return response()->json($this->response, $this->status);*/
    }

}
