<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return redirect(\route('login'));
});

Auth::routes();
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/terms-and-conditions', 'UserController@termsAndConditions')->name('terms-and-conditions');


/**
 * @Description Login,Forgot process routes
 * @Author Khuram Qadeer.
 */
Route::prefix('admin')->group(function () {
    Route::post('login', 'LoginController@login')->name('admin.login');
    Route::get('send/email/reset/password', 'LoginController@showResetPassword')->name('admin.reset.password.page');
    Route::post('send/email/reset/password', 'LoginController@sendEmailResetPassword')->name('admin.reset.password');
    Route::get('confirm/password/{token}', 'LoginController@confirmPassword')->name('admin.confirm.password');
    Route::post('update/confirm/password/{token}', 'LoginController@updatePassword')->name('admin.update.confirm.password');
});

Route::prefix('gym_owner')->group(function () {
    Route::get('create', 'GymOwnerController@create')->name('gym_owner.create');
    Route::post('store', 'GymOwnerController@store')->name('gym_owner.store');
    Route::get('edit/{id}', 'GymOwnerController@edit')->name('gym_owner.edit');
    Route::post('update', 'GymOwnerController@update')->name('gym_owner.update');
    Route::get('delete/{id}', 'GymOwnerController@delete')->name('gym_owner.delete');
    Route::get('list', 'GymOwnerController@list')->name('gym_owner.list');
    Route::get('show/{id}', 'GymOwnerController@show')->name('gym_owner.show');
    Route::get('list/gyms/{ownerId}', 'GymOwnerController@listGyms')->name('gym_owner.list.gyms');
    Route::get('list/gyms/payout/{id}', 'GymController@getGymPayouts')->name('gym_owner.list.gyms.payouts');
    Route::get('list/payout/gyms/{id}', 'GymController@getPayoutsByGym')->name('gym_owner.list.payouts.gyms');
    Route::get('date/vise/booking/{gym_id}/{date}', 'GymController@getDateViseBookings')->name('gym_owner.list.gyms.payouts');
    Route::post('list/gyms/payout/search', 'GymController@getPayoutDetailDateSearch')->name('gym_owner.list.gyms.payouts');
    Route::post('list/gyms/payout/download', 'GymController@getPayoutDetailSheet')->name('gym_owner.list.gyms.download');
    Route::get('list/gyms/payout/download', 'GymController@getPayoutDetailSheet')->name('gym_owner.list.gyms.download');
    Route::post('list/gyms/payout/search/by/gym', 'GymController@getPayoutDetailByGymDateSearch')->name('gym_owner.list.gyms.payouts');
    Route::post('payout/detail/by/gym/export', 'GymController@PayoutDetailByGymExport')->name('payout.detail.by.gym.export');
});

Route::prefix('facility')->group(function () {
    Route::post('store', 'FacilityController@store')->name('facility.store');
    Route::get('delete/{id}', 'FacilityController@delete')->name('facility.delete');
    Route::get('list', 'FacilityController@list')->name('facility.list');
});

Route::prefix('amenity')->group(function () {
    Route::post('store', 'AmenityController@store')->name('amenity.store');
    Route::get('delete/{id}', 'AmenityController@delete')->name('amenity.delete');
    Route::get('list', 'AmenityController@list')->name('amenity.list');
});

/**
 * @Description DropZone uploading and remove file routes
 * @Author Khuram Qadeer.
 */
Route::post('upload/images', 'GymController@uploadImages')->name('upload.images');
Route::get('remove/image/{filename}/{path}', 'GymController@removeImage')->name('remove.image');

/**
 * @Description Gym CRUD, Passes CRUD, Reviews,Members
 * @Author Khuram Qadeer.
 */
Route::prefix('gym')->group(function () {
    Route::get('create', 'GymController@create')->name('gym.create');
    Route::post('store', 'GymController@store')->name('gym.store');
    Route::get('edit/{id}', 'GymController@edit')->name('gym.edit');
    Route::post('update/{id}', 'GymController@update')->name('gym.update');
    Route::get('delete/{id}', 'GymController@delete')->name('gym.delete');
    Route::get('list', 'GymController@list')->name('gym.list');
    Route::get('show/{id}', 'GymController@show')->name('gym.show');
    Route::get('remove/image/{indexImage}/{gymId}', 'GymController@deleteImage')->name('gym.delete.image');
    Route::post('search/gym/', 'GymController@searchGym')->name('gym.search');
    Route::post('search/gym_owner/gym', 'GymController@searchGymOwnerGyms')->name('gym_owner.gym.search');

    // Passes Routes
    Route::get('passes/list/{gymId}', 'GymController@passesList')->name('passes.list');
    Route::post('pass/update/price', 'GymController@updatePassPrice')->name('passes.price.update');
    Route::post('update/gym/percentage', 'GymController@updateGymPercentage')->name('update.gym.percentage');
    Route::post('pass/update/active', 'GymController@updatePassActive')->name('passes.active.update');

    // show gym reviews
    Route::get('show/reviews/{gymId}', 'GymController@showGymReviews')->name('gym.show.reviews');
    Route::get('delete/review/{reviewId}', 'GymController@deleteReview')->name('gym.delete.review')->middleware('is_role:super_admin');
    // members
    Route::get('members/{gymId}', 'GymController@showGymMembersList')->name('gym.show.members');
    Route::get('members/owner/all', 'GymController@getAllActiveMembers')->name('gym.all.members');

    Route::get('members_detail/{gymId}/{memberId}', 'GymController@showGymMembersDetail')->name('gym.show.members.detail');

});
//Activities

 Route::get('activities/{user_id}', 'Activities@userActivities')->name('activities')->middleware('is_role:super_admin');
 Route::get('split-activities/{user_id}', 'Activities@userSplitActivities')->name('split-activities')->middleware('is_role:super_admin');
 Route::post('search/activities/{user_id}', 'Activities@searchActivities')->name('search.activities')->middleware('is_role:super_admin');
 Route::get('get/activity/{id}', 'Activities@getActivity')->name('get.activity')->middleware('is_role:super_admin');
 Route::get('get/activity/{id}/{show_only}', 'Activities@getActivity')->name('get.activity')->middleware('is_role:super_admin');
 Route::get('delete/activity/{id}', 'Activities@delete')->name('delete.activity')->middleware('is_role:super_admin');
 Route::post('save/activity/', 'Activities@saveActivity')->name('save.activity')->middleware('is_role:super_admin');
// Faq's
Route::prefix('faqs')->group(function () {
    Route::get('create', 'FaqsController@create')->name('faqs.create');
    Route::post('store', 'FaqsController@store')->name('faqs.store');
    Route::get('edit/{id}', 'FaqsController@edit')->name('faqs.edit');
    Route::post('update/{id}', 'FaqsController@update')->name('faqs.update');
    Route::get('delete/{id}', 'FaqsController@delete')->name('faqs.delete');
    Route::get('list', 'FaqsController@list')->name('faqs.list');
});

// Users
Route::prefix('user')->group(function () {
    Route::get('list/{type?}', 'UserController@list')->name('user.list');
    Route::get('enabledisable/{id}', 'UserController@enableDisableUser')->name('user.enabledisable');
    Route::get('clear-notifications', 'UserController@clearNotifocations')->name('clear.notifications');
    Route::get('delete-notifications/{id}', 'UserController@deleteNotifocations')->name('delete.notifications');
    Route::get('gym_owner/show/profile', 'UserController@showProfile')->name('user.gym_owner.profile');
    Route::get('admin/show/settings', 'UserController@showSettings')->name('user.admin.settings');
    Route::post('admin/update/settings', 'UserController@updateSettings')->name('admin.update.settings');
    Route::post('gym_owner/update/profile', 'UserController@updateProfile')->name('user.gym_owner.update.profile');

});

// Bank Account
Route::prefix('bank')->group(function () {
    Route::get('account/create', 'BankAccountController@create')->name('bank_account.create');
    Route::post('account/store/update', 'BankAccountController@storeOrUpdate')->name('bank_account.store_update');
});

// @Description CategoryController
Route::prefix('category')->group(function () {
    Route::post('store', 'CategoryController@store')->name('category.store');
    Route::get('delete/{id}', 'CategoryController@delete')->name('category.delete');
    Route::get('list', 'CategoryController@list')->name('category.list');
});

// @Description Manage Visits of gym for user
Route::prefix('manage/visits/')->group(function () {
    Route::get('list/{token?}', 'ManageVisitController@list')->name('manage_visits.list');
    Route::get('add/visit/{passToken}', 'ManageVisitController@addVisit')->name('manage_visits.add.visit');
});

// @Description TrainerProfileController CRUD
Route::resource('trainer', 'TrainerProfileController')->middleware(['auth', 'is_role:super_admin']);
Route::post('update/trainer/{id}', 'TrainerProfileController@updateTrainer')->name('update.trainer')->middleware(['auth', 'is_role:super_admin']);
Route::post('/search/filter/', 'TrainerProfileController@searchTrainer')->name('search.filter');
Route::post('update/trainer/is/verified', 'TrainerProfileController@updateIsVerified')->name('update.trainer.verified')->middleware(['auth', 'is_role:super_admin']);
Route::get('trainer/delete/{id}', 'TrainerProfileController@delete')->name('trainer.delete')->middleware(['auth', 'is_role:super_admin']);
Route::get('trainer/reviews/{id}', 'TrainerProfileController@getTrainerReviews')->name('trainer.reviews')->middleware(['auth', 'is_role:super_admin']);
Route::get('trainer/payout/detail/{trainer_id}', 'TrainerProfileController@getPayoutDetail')->name('trainer.payout.detail')->middleware(['auth', 'is_role:super_admin']);
Route::post('trainer/payout/detail', 'TrainerProfileController@getPayoutDetailDateSearch')->name('trainer.payout.detail')->middleware(['auth', 'is_role:super_admin']);
Route::post('get/payouts/download', 'TrainerProfileController@getPayoutsDownload')->name('get.payouts.download')->middleware(['auth', 'is_role:super_admin']);
Route::get('get/date/vise/bookings/{date}/{trainer_id}', 'TrainerProfileController@getDateViseBookings')->name('date.vise.bookings')->middleware(['auth', 'is_role:super_admin']);

Route::get('test', function () {
//    return Cache::has('user-is-online-' . 1);
//    return Cache::forget('user-is-online-' . 1);
//    $expiresAt = Carbon::now()->addMinutes(1);
//    return Cache::put('user-is-online-' . 1, true, $expiresAt);
  return  $trainers = User::getUserData(19);
    return stripeBalance();

    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    $payout = \Stripe\Transfer::create([
        "amount" => round(10 * 100),
        "currency" => "gbp",
        "destination" => 'acct_1HIUX5Bpb67ozpTG',
        "transfer_group" => '9',
    ]);
    return $payout;

    $gymOwnerId = 21;
    $accountId = 'acct_1HI9jXKAgbaZQIfP';
    $pendings = \App\PassOrderItems::where([['gym_owner_id', $gymOwnerId], ['payout_status', 'pending']])->get();

    // if pending amount
    if ($pendings) {
        // get all pending amount
        $amount = (double)collect($pendings)->sum('gym_owner_amount');
        // payout amount save into db
        $payoutModel = \App\Payout::forceCreate([
            'user_id' => $gymOwnerId,
            'amount' => (double)$amount
        ]);
        // transfer amount to bank account with our payout id
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $transaction = \Stripe\Transfer::create([
            "amount" => round(($amount) * 100),
            "currency" => "gbp",
            "destination" => $accountId,
            "transfer_group" => $payoutModel->id,
        ]);
        // transaction id save
        $payoutModel->transaction_id = $transaction->id;
        $payoutModel->update();

        // payout id update for each amount entries
        foreach ($pendings as $pending) {
            $pending->update([
                'payout_id' => $payoutModel->id,
                'payout_status' => 'paid',
            ]);
        }
    }

    return stripeBalance();

//    postman token
//    Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzIxYzNmMTQ2MTBhOGFkNWFiMDA3NDY5NDlhODdiNDM1MTM4N2E1MzZkYTMxZmJhMGU4YWE0M2YzMTQzZjJlODRkZDkzNGUyMWRhMGJkMDEiLCJpYXQiOjE1OTU4MzQ3NTUsIm5iZiI6MTU5NTgzNDc1NSwiZXhwIjoxNjI3MzcwNzU1LCJzdWIiOiIxOSIsInNjb3BlcyI6W119.VGRMyHWv49TVD0muamQVOrCuxFVfbtNyA4nVtKf-txCrcYpii5fViSH_v16op_tXAye_6eTYvAJ63m8d0l3JAhXJHYkqVeiY_4P3lUZfkq5gY5_I8TpiUDHeoVInv_O5wtb_zKxjrlAEyCEWrdwzOgrAxnYeMHrFHZn34W9P9hdorVQbKKYIRNgXBJvQfmsD4eOqVUQRpnnUPNKCDyOvPTAPCsW1c3k76EyYCWigW7G461R_9eZSntIi_75WUZfUQIY0Tmnqa8fwWi_Nt7XtX7UvM4offjSFJzeJukGtepYeUBpDXDiTgunUI_5vn7bQjwbBRxrw2N_nM98vpd3NySIFNALUtzBbBQfQ5rmirWrlUqpY-uaFbtr5zukRK0a93aF0QaOkVrciE-VxbSQTUZm-imHTGOB75-ZA_YqoB_io2sss2j-3hkdY5XdTnqVEBStJlneWkSw9UAMFCKm5idgOud8V3vJHi2vXNrSaUiEcD1E_RhptXkdhIDrhko5COC1lA6GOuOhKXWcQ0jTtK5u6OEH5yDdIg0OvTopogVtEqczPoOzguKtylfAl9R-6Lt5AmYT8PWBo4Axmv6STjYfjBQh7DlWsOAhNRwPDiYLso9cy0WSCJiFD93S_tCu16IPfQM7elk3j8hKRC8kqifiqqF3j8K4nSw-rHfUr45k
    //    shujabad
//    $lat = 29.8717;
//    $lng = 71.3231;
    //    multan
//        $lat = 30.1575;
//        $lng = 71.5249;
    //    lahore
//    $lat = 31.5204;
//    $lng = 74.3587;
//    $data = getNearByData('users', 'lat', 'lng', $lat, $lng);
//    $res = makePaginate(collect($data), 1);
//    return $res;
//    $data_res = [];
//    $d = [];
//    $data_res['total'] = $res->total();
//    $data_res['next_page_url'] = $res->nextPageUrl();
//    $data_res['prev_page_url'] = $res->previousPageUrl();
//    $data_res['per_page'] = $res->perPage();
//    $data_res['first_page_url'] = $res->toArray()['first_page_url'];
//    $data_res['last_page_url'] = $res->toArray()['last_page_url'];
//    if ($res) {
//        foreach ($res as $re) {
//            array_push($d, $re);
//        }
//        $data_res['data'] = $d;
//    }
//    $res = $data_res;
//    return $res;

    //    return \App\Pass::generateDefaultPasses(21,2);
//    $toEmail = 'Khuramchef60@gmail.com';
//    $toName='SwitchFit';
//    $data=['user'=>\App\User::find(1),'token'=>'sdkfsdjlkfjs'];
//    sendEmail('emails.credentials_send','Reset Password',$toEmail,$toName,$data);

    $user = \App\User::find(1);
    $token = 'jdf35sdflk';
    $password = 'sdfjlskdf';
    return view('emails.credentials_send', compact('user', 'password'));
//return view('emails.reset_password',compact('token','user'));
//    deleteFile('assets/uploads/gym_owners/h2.png');
//   dd(isSuperAdmin());
//    return view('emails.credentials_send');

})->name('test.route');
