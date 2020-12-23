<?php

use App\Pass;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

function calculateMinutesHourse($start_time,$end_time){
    $start_time = explode(':',$start_time);
    $end_time = explode(':',$end_time);

    $start_time_hours = (int)$start_time[0];
    $start_minutes =  (int)$start_time[1];
    $start_range = ($start_time_hours * 60) + $start_minutes;
    $end_time_hours =  (int)$end_time[0];
    $end_time_minutes = (int)$end_time[1];
    $end_range = ($end_time_hours * 60) + $end_time_minutes;
    if($start_minutes > $end_time_minutes){
        $start_minutes = $start_minutes - $end_time_minutes;
        $end_time_hours = ( $end_time_hours > $start_time_hours)? $end_time_hours - 1:$end_time_hours;
        $end_time_minutes = 60 -  $start_minutes;
        $start_minutes = 0;

    }
    $total_hourse = ($end_time_hours - $start_time_hours) * 60;
    $total_minutes = $end_time_minutes - $start_minutes;
    $total_minutes = $total_minutes + $total_hourse;
    return array('total_minutes'=>$total_minutes,'end_range'=>$end_range,'start_range'=>$start_range);
}

/**
 * @Description Send Email
 * @param $emailTemplate
 * @param $subject
 * @param $toEmail
 * @param $toName
 * @param array $data
 * @Author Khuram Qadeer.
 */
function sendEmail($emailTemplate, $subject, $toEmail, $toName, $data = [])
{
    Mail::send($emailTemplate, $data, function ($message) use ($toName, $toEmail, $subject) {
        $message->to($toEmail, $toName)
        ->subject($subject)
        ->from('Khuramchef60@gmail.com', 'SwitchFit');
    });
}

function getLinkFromNotification($screen,$id){
 $url='';
 $sreens=array('gym_payouts'=>'gym_owner/list/gyms/payout/'.$id,'trainer_detail'=>'trainer/'.$id,'gym_order'=>'gym/members/'.$id,'gym_rating'=>'gym/members/'.$id);
 
 $url = url('/').'/'.$sreens[$screen];

 return $url;
}

/**
 * @Description Get Current Route name
 * @return string|null
 * @Author Khuram Qadeer.
 */
function getCurrentRouteName()
{
    return Route::currentRouteName();
}

/**
 * @Description Delete File by path
 * @param $path
 * @Author Khuram Qadeer.
 */
function deleteFile($path)
{
    $path = public_path($path);
    if (File::exists($path)) {
        File::delete($path);
//        unlink($path);
    }
}

/**
 * @Description Not Allowed Route for show side bar top bar footer text
 * @return bool
 * @Author Khuram Qadeer.
 */
function notAllowedSideBarTopBarAndFooter()
{
    $res = false;
    if (getCurrentRouteName() != 'login' && getCurrentRouteName() != 'admin.reset.password.page'
        && getCurrentRouteName() != 'admin.confirm.password') {
        $res = true;
}
return $res;
}

/**
 * @Description check current user is supper admin
 * @return bool
 * @Author Khuram Qadeer.
 */
function isSuperAdmin()
{
    $res = false;
    if (\Illuminate\Support\Facades\Auth::check()) {
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 1) {
            $res = true;
        }
    }
    return $res;
}
function get_notifications(){
    $id = \Illuminate\Support\Facades\Auth::user()->id;
    if(isSuperAdmin()){
        $notifications =  \App\Notification::where('user_id',0)->orderBy('created_at', 'desc')->get();
    }else{
        $notifications =  \App\Notification::where('user_id',$id)->orderBy('created_at', 'desc')->get();
    }
    return $notifications;

}
function get_notifications_count(){
    $id = \Illuminate\Support\Facades\Auth::user()->id;
    if(isSuperAdmin()){
        $notifications =  \App\Notification::where('is_read',0)->where('user_id',0)->orderBy('created_at', 'desc')->get();
    }else{
        $notifications =  \App\Notification::where('is_read',0)->where('user_id',$id)->orderBy('created_at', 'desc')->get();
    }
    return $notifications;

}

/**
 * @Description check current user Gym Owner
 * @return bool
 * @Author Khuram Qadeer.
 */
function isGymOwner()
{
    $res = false;
    if (\Illuminate\Support\Facades\Auth::check()) {
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 2) {
            $res = true;
        }
    }
    return $res;
}

/**
 * @Description check current user is trainer
 * @return bool
 * @Author Khuram Qadeer.
 */
function isTrainer()
{
    $res = false;
    if (\Illuminate\Support\Facades\Auth::check()) {
        if (\Illuminate\Support\Facades\Auth::user()->role_id == 3) {
            $res = true;
        }
    }
    return $res;
}

/**
 * @Description check current user is normal user
 * @return bool
 * @Author Khuram Qadeer.
 */
function isNormalUser()
{
    $res = false;
    if (\Illuminate\Support\Facades\Auth::check()) {
        if (!\Illuminate\Support\Facades\Auth::user()->role_id
            || \Illuminate\Support\Facades\Auth::user()->role_id == null
            || \Illuminate\Support\Facades\Auth::user()->role_id == 0) {
            $res = true;
    }
}
return $res;
}

/**
 * @Description Get Hours list in Dropdown option tags
 * @param string $default
 * @param string $interval
 * @return string
 * @Author Khuram Qadeer.
 */
function getHoursInHTMLDropDownOptions($defaultSelected = '00:00', $interval = '+15 minutes')
{
    $output = '';
    $current = strtotime('00:00');
    $end = strtotime('23:59');
    while ($current <= $end) {
        $time = date('H:i', $current);
        $sel = ($time == $defaultSelected) ? ' selected' : '';
        $output .= "<option value=\"{$time}\"{$sel}>" . date('h:i A', $current) . '</option>';
        $current = strtotime($interval, $current);
    }
    return $output;
}



function getStartHoursDropDownOptions($user_id,$day_id, $interval = '+15 minutes')
{
    $output = '';
    $current = strtotime('00:00');
    $end = strtotime('23:59');
    $availablity = \App\TrainerAvailabilty::where('user_id',$user_id)->where('day_id',$day_id)->first();
    while ($current <= $end) {
        $time = date('H:i', $current);
        $sel = ($availablity && $time == $availablity->time_start) ? ' selected' : '';
        $output .= "<option value=\"{$time}\"{$sel}>" . date('h:i A', $current) . '</option>';
        $current = strtotime($interval, $current);
    }
    return $output;
}
function getEndHoursDropDownOptions($user_id,$day_id, $interval = '+15 minutes')
{
    $output = '';
    $current = strtotime('00:00');
    $end = strtotime('23:59');
    $availablity = \App\TrainerAvailabilty::where('user_id',$user_id)->where('day_id',$day_id)->first();
    while ($current <= $end) {
        $time = date('H:i', $current);
        $sel = ($availablity && $time == $availablity->time_end) ? ' selected' : '';
        $output .= "<option value=\"{$time}\"{$sel}>" . date('h:i A', $current) . '</option>';
        $current = strtotime($interval, $current);
    }
    return $output;
}

/**
 * @Description Custom Pagination for any collect
 * @param Collection $results
 * @param $perPage
 * @return mixed
 * @Author Khuram Qadeer.
 */
function makePaginate(Collection $results, $perPage)
{
    $page = Paginator::resolveCurrentPage('page');
    $total = $results->count();
    $res = myPaginator($results->forPage($page, $perPage), $total, $perPage, $page, [
        'path' => Paginator::resolveCurrentPath(),
        'pageName' => 'page',
    ]);
    return $res;
}

/**
 * @Description use for pagination in make pagination
 * @param $items
 * @param $total
 * @param $perPage
 * @param $currentPage
 * @param $options
 * @return mixed
 * @throws \Illuminate\Contracts\Container\BindingResolutionException
 * @Author Khuram Qadeer.
 */
function myPaginator($items, $total, $perPage, $currentPage, $options)
{
    return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
        'items', 'total', 'perPage', 'currentPage', 'options'
    ));
}

/**
 * @Description Checking Array via key and value if matched it's response would be true
 *
 * @param $dataArr
 * @param $key
 * @param $checkValue
 * @return bool
 * @Author Khuram Qadeer.
 */
function inArrayByKeyAndValue($dataArr, $key, $checkValue)
{
    $res = false;
    if ($dataArr) {
        foreach ($dataArr as $item) {
            if(isset($item->$key)){
                if ($item->$key == $checkValue) {
                    $res = true;
                    break;
                }
            }
        }
    }
    return $res;
}
/*Check if day is selected*/
function checkday($user_id,$day_id){
    $available = 0;
    $availablity = \App\TrainerAvailabilty::where('user_id',$user_id)->where('day_id',$day_id)->first();
    if(!empty($availablity) && $availablity->available == 1){
        $available = 1;
    }
    return $available;
}

/**
 * @Description Check End Time great than start time or not
 * @param $startTime
 * @param $EndTime
 * @return bool
 * @Author Khuram Qadeer.
 */
function checkTimeEndTimeGreatThanStartTime($startTime, $EndTime)
{
    $res = false;
    $start = strtotime($startTime);
    $end = strtotime($EndTime);
    if ($end > $start) {
        $res = true;
    }
    return $res;
}

/**
 * @Description Get Current Url Segments by enter Segment Number
 *
 * @param $segmentNo
 * @return string|null
 *
 * @Author Khuram Qadeer.
 */
function getUrlSegment($segmentNo)
{
    return Request::segment($segmentNo);
}

/**
 * @Description Get data form any table from near by location via latitude,longitude,
 *
 * @param $tableName
 * @param $columnNameLatitude
 * @param $columnNameLongitude
 * @param $startLatitude
 * @param $startLongitude
 * @param int $radius
 * @param int $limit
 * @return array
 *
 * @Author Khuram Qadeer.
 */
function getNearByData($tableName, $columnNameLatitude, $columnNameLongitude, $startLatitude, $startLongitude, $radius = 5000, $limit = 500)
{
    return \Illuminate\Support\Facades\DB::select("SELECT * FROM (
        SELECT *,
        (
        (
        (
        acos(
        sin(( $startLatitude * pi() / 180))
                                * sin(( $columnNameLatitude * pi() / 180)) 
        + cos(( $startLatitude * pi() /180 ))
                                * cos(( $columnNameLatitude * pi() / 180))
                                * cos((($startLongitude - $columnNameLongitude) * pi()/180))
        )
        ) * 180/pi()
        ) * 60 * 1.1515 * 1.609344
        )
        as distance FROM $tableName
        ) $tableName
        WHERE distance <= $radius
        ORDER By distance
        LIMIT $limit");
}

/*Caculate Radius betweeen two points*/

function getCircleDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
    /*Note that you get the distance back in the same unit as you pass in with the parameter $earthRadius. The default value is 6371000 meters so the result will be in [m] too. To get the result in miles, you could e.g. pass 3959 miles as $earthRadius and the result would be in [mi]. In my opinion it is a good habit to stick with the SI units, if there is no particular reason to do otherwise.*/


  // convert from degrees to radians
    $earthRadius = 3959;
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
}

/**
 * @Description convert gyms array to basic info array
 * @param $gyms
 * @return array
 * @Author Khuram Qadeer.
 */
function convertGymBasicInfoArr($gyms)
{
    $res = [];
    if ($gyms) {
        foreach ($gyms as $gym) {
            $requireData = [];
            $requireData['id'] = $gym->id;
            $requireData['name'] = $gym->name;
            $requireData['image'] = $gym->images ? json_decode($gym->images)[0] : null;
            $requireData['lat'] = floatval($gym->lat);
            $requireData['lng'] = floatval($gym->lng);
            $requireData['reviews'] = [
                'total' => \App\Review::getReviewByGymId($gym->id)['total_reviews'],
                'star' => \App\Review::getReviewByGymId($gym->id)['total_stars'],
            ];
            $requireData['pass_lowest_price'] = number_format(Pass::getLowestPriceByGymId($gym->id) ?? 0, 2);
            $requireData['distance'] = $gym->distance;
            array_push($res, $requireData);
        }
    }
    return $res;
}

/**
 * @Description Convert Trainer array to basic info trainers
 * @param $trainers
 * @return array
 * @Author Khuram Qadeer.
 */
function convertTrainerBasicInfoArr($trainers)
{
    $res = [];
    if ($trainers) {
        foreach ($trainers as $trainer) {
            $requireData = [];
            $requireData['id'] = $trainer->id;
            $requireData['first_name'] = $trainer->first_name;
            $requireData['last_name'] = $trainer->last_name;
            $requireData['name'] = $trainer->first_name .' '.$trainer->last_name;
            $requireData['about'] = $trainer->about ?? null;
            $requireData['avatar'] = $trainer->avatar ?? null;
            $requireData['lat'] = floatval($trainer->lat);
            $requireData['lng'] = floatval($trainer->lng);
            $requireData['activity_lowest_price'] = $trainer->activity_lowest_price;
            $user = \App\User::with('reviews.user','ratings')->find($trainer->id);
            $ratings = $user->reviews->sum('stars');
            $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
            
            $requireData['reviews'] = [
                'star' => $ratings,
                'total' => $user->reviews->count()
            ];
            $requireData['distance'] = $trainer->distance;
            array_push($res, $requireData);
        }
    }
    return $res;
}

/**
 * @Description Get Strip Token
 * @param $cardName
 * @param $expireMonth
 * @param $expireYear
 * @param $cvc
 * @return array
 * @throws \Stripe\Exception\ApiErrorException
 * @Author Khuram Qadeer.
 */
function getStripeToken($cardName, $expireMonth, $expireYear, $cvc)
{
    $res = [];
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    /*\Stripe\Stripe::setApiKey('sk_test_51HHsgtD5CIQ5HTo6gOnMmNRaua3D5cTneHgazslX62fYd3tadsUzStW2nDri73U9Sq9uJQVWAezcU2ehKp1uOnc600BFPe50ke');*/
    try {
        $res['token'] = \Stripe\Token::create([
            'card' => [
                'number' => $cardName,
                'exp_month' => $expireMonth,
                'exp_year' => $expireYear,
                'cvc' => $cvc
            ]
        ]);
    } catch (\Stripe\Exception\CardException $e) {
        $res['error'] = $e->getMessage();
    }
    return $res;
}

/**
 * @Description Create Customer on Stripe
 * @param $clientId
 * @param $clientName
 * @param $clientEmail
 * @param $stripeToken
 * @return array
 * @Author Khuram Qadeer.
 */
function createStripeCustomer($clientId, $clientName, $clientEmail, $stripeToken)
{
    $res = [];
    try {
        $res['customer'] = \Stripe\Customer::create([
            "name" => $clientName,
            "email" => $clientEmail,
            "description" => "ClientID: " . $clientId . " Email: " . $clientEmail,
            "source" => $stripeToken // obtained with Stripe.js
        ]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        $res['error'] = $e->getMessage();
    }
    return $res;
}

/**
 * @Description Save Card on Stripe
 * @param $stripeCustomerId
 * @param $stripeToken
 * @return array
 * @Author Khuram Qadeer.
 */
function saveCardOnStripe($stripeCustomerId, $stripeToken)
{
    $res = [];
    try {
        $res['card'] = \Stripe\Customer::createSource(
            $stripeCustomerId,
            [
                'source' => $stripeToken,
            ]
        );
    } catch (\Stripe\Exception\ApiErrorException $e) {
        $res['error'] = $e->getMessage();
    }
    return $res;
}

/**
 * @Description Stripe Charge Amount
 * @param $customerId
 * @param $cardId
 * @param $amount
 * @return array
 * @Author Khuram Qadeer.
 */
function stripeCharge($customerId, $cardId, $amount)
{
    $res = [];
    try {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        /* \Stripe\Stripe::setApiKey('sk_test_51HHsgtD5CIQ5HTo6gOnMmNRaua3D5cTneHgazslX62fYd3tadsUzStW2nDri73U9Sq9uJQVWAezcU2ehKp1uOnc600BFPe50ke');*/
        $intent = \Stripe\PaymentIntent::create([
            'amount' => round($amount * 100),
            'currency' => 'gbp',
            'customer' => $customerId,
            'payment_method' => $cardId,
        ]);
        $intent = \Stripe\PaymentIntent::retrieve($intent->id);
        $confirm = $intent->confirm([
            'payment_method' => $cardId,
        ]);
        $res['charge'] = $confirm->charges->data[0]->id;
    } catch (\Stripe\Exception\ApiErrorException $e) {
        $res['error'] = $e->getMessage();
    }
    return $res;
}

/**
 * @Description get stripe available balance
 * @return float|int
 * @throws \Stripe\Exception\ApiErrorException
 * @Author Khuram Qadeer.
 */
function stripeBalance()
{
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    /*\Stripe\Stripe::setApiKey('sk_test_51HHsgtD5CIQ5HTo6gOnMmNRaua3D5cTneHgazslX62fYd3tadsUzStW2nDri73U9Sq9uJQVWAezcU2ehKp1uOnc600BFPe50ke');*/
    return \Stripe\Balance::retrieve()->available[0]->amount / 100;
}

/**
 * @Description Delete Stripe Account
 * @param $accountId
 * @return \Stripe\Account
 * @throws \Stripe\Exception\ApiErrorException
 * @Author Khuram Qadeer.
 */
function deleteStripeAccount($accountId)
{
    $stripe = new \Stripe\StripeClient('sk_test_51HHsgtD5CIQ5HTo6gOnMmNRaua3D5cTneHgazslX62fYd3tadsUzStW2nDri73U9Sq9uJQVWAezcU2ehKp1uOnc600BFPe50ke');
    return $stripe->accounts->delete(
        $accountId,
        []
    );
}

/**
 * @Description Get Credit card type like visa,mastercard etc
 * @param $cardNumber
 * @return string
 * @Author Khuram Qadeer.
 */
function getCreditCardType($cardNumber)
{
    $cardNumber = preg_replace('/\D/', '', $cardNumber);
    switch ($cardNumber) {
        case(preg_match('/^4/', $cardNumber) >= 1):
        return 'Visa';
        case(preg_match('/^5[1-5]/', $cardNumber) >= 1):
        return 'Mastercard';
        case(preg_match('/^3[47]/', $cardNumber) >= 1):
        return 'Amex';
        case(preg_match('/^3(?:0[0-5]|[68])/', $cardNumber) >= 1):
        return 'Diners Club';
        case(preg_match('/^6(?:011|5)/', $cardNumber) >= 1):
        return 'Discover';
        case(preg_match('/^(?:2131|1800|35\d{3})/', $cardNumber) >= 1):
        return 'JCB';
        default:
        return '';
        break;
    }
}
