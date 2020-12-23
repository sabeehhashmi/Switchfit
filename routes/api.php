<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * @Description Api\AuthController
 * @Author Khuram Qadeer.
 */
Route::post('signup', 'Api\AuthController@signup');
Route::post('login', 'Api\AuthController@login');
Route::post('social/login', 'Api\AuthController@socialSignupOrLogin');
Route::post('send/email/reset/password', 'Api\AuthController@sendEmailResetPassword');
Route::post('logout', 'Api\AuthController@logout');

/**
 * @Description Api\HelperController
 * @Author Khuram Qadeer.
 */
Route::post('get/lists', 'Api\HelperController@getLists');
Route::post('get/faqs', 'Api\HelperController@getFaqs');

/**
 * @Description Api\GymController
 * @Author Khuram Qadeer.
 */
Route::post('search/gyms', 'Api\GymController@searchGyms');
Route::post('get/gym', 'Api\GymController@getGym');
/**
 * @Description Api\TrainerProfileController
 * @Author Khuram Qadeer.
 */
/*Trainers Data*/
Route::post('/search/trainers', 'Api\TrainerProfileController@searchTrainers');
Route::post('get/trainer/reviews', 'Api\TrainerProfileController@getTrainerReviews');

/**
 * @Description Api\ReviewController
 * @Author Khuram Qadeer.
 */
Route::post('get/gym/reviews', 'Api\ReviewController@getGymReviews');

/*Get Trainer Profile detail*/

Route::post('get/trainer/detail', 'Api\TrainerProfileController@getTrainerDetails');
Route::post('get/trainer/reviews/detail', 'Api\TrainerProfileController@getTrainerReviewsDetails');
Route::post('get/activity/individual/reviews', 'Api\TrainerProfileController@getActivityIndividualReviews');
Route::post('get/noification/activity/individual/reviews', 'Api\TrainerProfileController@getNoificationActivityIndividualReviews');
Route::post('get/notification/activity/individual/reviews', 'Api\TrainerProfileController@getNoificationActivityIndividualReviews');
Route::post('get/booking/detail', 'Api\TrainerProfileController@getBookingDetail');

/**
 * @Description authenticated user can access these Apis
 * @Author Khuram Qadeer.
 */
Route::group(['middleware' => ['auth:api','CheckStatus']], function () {

        /**
     * @Description Api\AuthController
     * @Author Khuram Qadeer.
     */
    Route::post('update/password', 'Api\AuthController@updatePassword');
    Route::post('update/notifications', 'Api\AuthController@updateNotifications');

    /**
     * @Description Api\HelperController
     * @Author Khuram Qadeer.
     */
    Route::post('get/user/data', 'Api\HelperController@getUserData');

    /**
     * @Description Api\ProfileController
     * @Author Khuram Qadeer.
     */
    Route::post('get/user', 'Api\ProfileController@getUser');
    Route::post('update/user/profile', 'Api\ProfileController@updateUserProfile');

    /**
     * @Description Api\GymController
     * @Author Khuram Qadeer.
     */
    Route::post('make/fav/gym', 'Api\GymController@makeFavGym');
    Route::post('get/fav/gyms', 'Api\GymController@getFavGym');
    /**
     * @Description Api\TrainerProfileController
     * @Author Khuram Qadeer.
     */
    Route::post('/trainer/profile/update/basic', 'Api\TrainerProfileController@updateBasic');
    Route::post('/trainer/profile/update/professional', 'Api\TrainerProfileController@updateProfessional');
    Route::post('/trainer/profile/update/availability', 'Api\TrainerProfileController@updateAvailability');
    Route::post('/trainer/profile/update/categories', 'Api\TrainerProfileController@updateCategories');
    Route::post('/trainer/create/activity', 'Api\TrainerProfileController@createActivity');
    Route::post('/trainer/activities', 'Api\TrainerProfileController@allactivities');
    Route::post('/trainer/update/activity', 'Api\TrainerProfileController@updateActivity');
    Route::post('/trainer/delete/activity', 'Api\TrainerProfileController@deleteActivity');
    Route::post('/trainer/get-availabalities', 'Api\TrainerProfileController@getUserAvailablities');
    Route::post('/trainer/get-available-slots', 'Api\TrainerProfileController@getAvailableSlots');
    Route::post('/book-activity', 'Api\TrainerProfileController@bookActivity');
    Route::post('/get-user-bookings', 'Api\TrainerProfileController@getUserBookings');
    Route::post('/get-trainer-bookings', 'Api\TrainerProfileController@getTrainerBookings');
    Route::post('/get-trainer-pending-bookings', 'Api\TrainerProfileController@getTrainerPendigBookings');
    Route::post('/get-user-bank-info', 'Api\CardController@getBankInfo');
    Route::post('/accept-reject-activity', 'Api\TrainerProfileController@AcceptRejectActivity');
    Route::post('/get-trainer-up-coming-bookings-dates', 'Api\TrainerProfileController@getTrainerUpComingBookingsDates');
    Route::post('/get-trainer-up-coming-bookings', 'Api\TrainerProfileController@getTrainerUpComingBookings');


    /**
     * @Description Api\CardController
     * @Author Khuram Qadeer.
     */
    Route::post('card/save', 'Api\CardController@saveCard');
    Route::post('card/delete', 'Api\CardController@deleteCard');
    Route::post('get/cards', 'Api\CardController@getCards');
    Route::post('store/bank', 'Api\CardController@storeOrUpdateBank');

    /**
     * @Description Api\PassOrderController
     * @Author Khuram Qadeer.
     */
    Route::post('buy/passes', 'Api\PassOrderController@buyPasses');
    Route::post('get/user/passes', 'Api\PassOrderController@getUserPasses');

    /**
     * @Description Api\ReviewController
     * @Author Khuram Qadeer.
     */
    Route::post('get/gym/review/options', 'Api\ReviewController@getReviewOptions');
    Route::post('make/gym/review', 'Api\ReviewController@makeReview');

    /*Trainer Favourit UnFavourit Apis*/
    Route::post('make/trainer/fvrt', 'Api\TrainerProfileController@makeFavTrainer');
    Route::post('get/fvrt/trainer', 'Api\TrainerProfileController@getFvrtTrainers');
    Route::post('get/payout/detail', 'Api\TrainerProfileController@getPayoutDetail');
    Route::post('get/date/vise/bookings', 'Api\TrainerProfileController@getDateViseBookings');
    Route::post('cancel/booking/user', 'Api\TrainerProfileController@userCancelBooking');
    Route::post('cancel/booking/trainer', 'Api\TrainerProfileController@trainerCancelBooking');

    /*Notification*/
    Route::post('get/notifications', 'Api\Notifications@getNotifications');
    Route::post('get-notifications-count', 'Api\Notifications@getNotificationsCount');
    Route::post('read/notifications', 'Api\Notifications@readNotifocations');




});
