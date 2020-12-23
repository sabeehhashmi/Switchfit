<?php

namespace App\Http\Controllers;

use App\AmenityLink;
use App\FacilityLink;
use App\Gym;
use App\Pass;
use App\Review;
use App\ReviewLink;
use App\User;
use App\PassOrderItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use App\Notification;
use Excel;
use App\Exports\InvoicesExport;
use Carbon\Carbon;

class GymController extends Controller
{
    /**
     * GymController constructor.
     * @Author Khuram Qadeer.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * @Description Create Gym  template
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function create()
    {
        return view('gyms.create');
    }

    /**
     * @Description Store Gym
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|regex:/^[a-zA-Z]+$/u',
            'address' => 'required|max:80',
            'lat' => 'required',
            'lng' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'about' => 'required|max:1000',
            'facilities' => 'required',
            'amenities' => 'required',
            'days' => 'required',
            'image_urls' => 'required',
        ], [
            'days.required' => 'Please, Select Time Schedule.',
            'lat.required' => 'Please, Select Location From Map.',
            'lng.required' => 'Please, Select Location From Map.',
            'facilities.required' => 'Please, Select at least one facility.',
            'amenities.required' => 'Please, Select at least one amenity.',
            'image_urls.required' => 'Please, Upload at least one image.',
        ]);

        $validator->after(function ($validator) use ($request) {
            // Time Schedule validation checking end time greater must be greater then start time
            if ($request->days) {
                foreach ($request->days as $day) {
                    $time_start_key = ((string)$day) . '_start';
                    $time_end_key = ((string)$day) . '_end';
                    if (!checkTimeEndTimeGreatThanStartTime($request->$time_start_key, $request->$time_end_key)) {
                        $validator->errors()->add($day, ucfirst($day) . ' end time must be greater than start time.');
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        // All Files move into gyms folder and make an array for files names
//        $images = [];
//        for ($i = 1; $i <= 6; $i++) {
//            $file = $request->file('file' . $i);
//            if ($file) {
//                $filename = str_replace(' ', '_', Str::random(10) . $file->getClientOriginalName());
//                //Move Uploaded File
//                $dirPath = 'assets/uploads/gyms/';
//                $fileUrl = $dirPath . $filename;
//                $file->move($dirPath, $filename);
//                array_push($images, $fileUrl);
//            }
//        }

        $timeSchedule = [];
        // Time Schedule
        if ($request->days) {
            foreach ($request->days as $day) {
                $time_start_key = ((string)$day) . '_start';
                $time_end_key = ((string)$day) . '_end';
                array_push($timeSchedule, [
                    'day' => $day,
                    'time_start' => $request->$time_start_key,
                    'time_end' => $request->$time_end_key,
                ]);
            }
        }

        $gym = Gym::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'address' => $request->address,
            'lat' => (double)$request->lat ?? null,
            'lng' => (double)$request->lng ?? null,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'about' => $request->about,
            'time_schedule' => json_encode($timeSchedule) ?? null,
            'images' => json_encode(json_decode($request->image_urls)) ?? null,
        ]);

        // Gym Facilities
        if ($request->facilities) {
            foreach ($request->facilities as $facilityId) {
                FacilityLink::create([
                    'gym_id' => $gym->id,
                    'facility_id' => (int)$facilityId,
                ]);
            }
        }

        // Gym Amenities
        if ($request->amenities) {
            foreach ($request->amenities as $amenityId) {
                AmenityLink::create([
                    'gym_id' => $gym->id,
                    'amenity_id' => (int)$amenityId,
                ]);
            }
        }
        \App\Pass::generateDefaultPasses(Auth::id(), $gym->id);
        Session::flash('alert-success', 'Gym has been Created.');
        return redirect(route('gym.list'));
    }

    /**
     * @Description Show view page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function show($id)
    {
        $gym = Gym::getByGymId($id);
        return view('gyms.show', compact('gym'));
    }

    /**
     * @Description Edit gym
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function edit($id)
    {
        $gym = Gym::getByGymId($id);
        return view('gyms.edit', compact('gym'));
    }

    /**
     * @Description update gym
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|regex:/^[a-zA-Z]+$/u',
            'address' => 'required|max:80',
            'lat' => 'required',
            'lng' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'about' => 'required|max:1000',
            'facilities' => 'required',
            'amenities' => 'required',
            'days' => 'required',
            'image_urls' => 'required',
        ], [
            'days.required' => 'Please, Select Time Schedule.',
            'lat.required' => 'Please, Select Location From Map.',
            'lng.required' => 'Please, Select Location From Map.',
            'facilities.required' => 'Please, Select at least one facility.',
            'amenities.required' => 'Please, Select at least one amenity.',
            'image_urls.required' => 'Please, Upload at least one image.',

        ]);

        $validator->after(function ($validator) use ($request) {
            // Time Schedule validation checking end time greater must be greater then start time
            if ($request->days) {
                foreach ($request->days as $day) {
                    $time_start_key = ((string)$day) . '_start';
                    $time_end_key = ((string)$day) . '_end';
                    if (!checkTimeEndTimeGreatThanStartTime($request->$time_start_key, $request->$time_end_key)) {
                        $validator->errors()->add($day, ucfirst($day) . ' end time must be greater than start time.');
                    }

                }
            }

        });

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        // All Files move into gyms folder and make an array for files names
//        $images = [];
        $gym = Gym::find($id);
//        if ($gym->images) {
//            foreach (json_decode($gym->images) as $imgUrl) {
//                array_push($images, $imgUrl);
//            }
//        }
//        for ($i = 1; $i <= 6; $i++) {
//            $file = $request->file('file' . $i);
//            if ($file) {
//                $filename = str_replace(' ', '_', Str::random(10) . $file->getClientOriginalName());
//                //Move Uploaded File
//                $dirPath = 'assets/uploads/gyms/';
//                $fileUrl = $dirPath . $filename;
//                $file->move($dirPath, $filename);
//                array_push($images, $fileUrl);
//            }
//        }
        $timeSchedule = [];
        // Time Schedule
        if ($request->days) {
            foreach ($request->days as $day) {
                $time_start_key = ((string)$day) . '_start';
                $time_end_key = ((string)$day) . '_end';
                array_push($timeSchedule, [
                    'day' => $day,
                    'time_start' => $request->$time_start_key,
                    'time_end' => $request->$time_end_key,
                ]);
            }
        }

        $gym->update([
            'name' => $request->name,
            'address' => $request->address,
            'lat' => (double)$request->lat ?? null,
            'lng' => (double)$request->lng ?? null,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'about' => $request->about,
            'time_schedule' => json_encode($timeSchedule) ?? null,
            'images' => json_decode($request->image_urls) ? json_encode(json_decode($request->image_urls)) : null,
        ]);

        FacilityLink::deleteByGymId($gym->id);
        // Gym Facilities
        if ($request->facilities) {
            foreach ($request->facilities as $facilityId) {
                FacilityLink::create([
                    'gym_id' => $gym->id,
                    'facility_id' => (int)$facilityId,
                ]);
            }
        }

        AmenityLink::deleteByGymId($gym->id);
        // Gym Amenities
        if ($request->amenities) {
            foreach ($request->amenities as $amenityId) {
                AmenityLink::create([
                    'gym_id' => $gym->id,
                    'amenity_id' => (int)$amenityId,
                ]);
            }
        }
        Session::flash('alert-success', 'Gym has been updated.');
        return redirect(route('gym.list'));
    }

    /**
     * @Description Delete Gym
     * @param $id
     * @Author Khuram Qadeer.
     */
    public function delete($id)
    {
       $passesOrderItem = PassOrderItems::where([['gym_id', $id], ['is_expire', 0]])->orderBy('book_date','DESC')->first();
       if(!empty($passesOrderItem)){
        Session::flash('alert-danger', 'Gym can\'t be deleted as it has active passes.');
       return redirect(route('gym.list'));
       }
       Gym::deleteById($id);
       Session::flash('alert-danger', 'Gym has been deleted.');
       return redirect(route('gym.list'));
   }

    /**
     * @Description Listing of Gym owners
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function list()
    {
        $gyms = null;
        if (isSuperAdmin()) {
            $gyms = Gym::orderBy('id', 'DESC')->where('is_deleted',0)->get();
        } else if (isGymOwner()) {
            $gyms = Gym::getAllByUserId(Auth::id());
        }
        $gyms = makePaginate(collect($gyms), 10);
        return view('gyms.list', compact('gyms'));
    }

    /**
     * @Description search Gym
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    public function searchGym(Request $request)
    {
        $gyms = null;
        if ($request->ajax()) {
            $key = $request->key;
            if ($key) {
                if (isSuperAdmin()) {
                    $gyms = Gym::where('name', 'LIKE', "%{$key}%")->where('is_deleted',0)->paginate(10);
                } elseif (isGymOwner()) {
                    $gyms = Gym::where('user_id', Auth::id())->where('name', 'LIKE', "%{$key}%")->paginate(10);
//                    $gyms = makePaginate(collect($gyms), 10);
                }
            } else {
                if (isSuperAdmin()) {
                    $gyms = Gym::orderBy('id', 'DESC')->where('is_deleted',0)->paginate(10);
                } elseif (isGymOwner()) {
                    $gyms = Gym::getAllByUserId(Auth::id());
                    $gyms = makePaginate(collect($gyms), 10);
                }
            }
            return response()->view('gyms.ajax_search_gyms', compact('gyms'));
        }
    }

    /**
     * @Description Search Gym By Gym owner
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    public function searchGymOwnerGyms(Request $request)
    {
        if ($request->ajax()) {
            $key = $request->key;
            $gymOwnerId = $request->gym_owner_id;
            if ($key) {
                $gyms = Gym::where('user_id', (int)$gymOwnerId)->where('name', 'LIKE', "%{$key}%")->paginate(10);
            } else {
                $gyms = Gym::where('user_id', (int)$gymOwnerId)->orderBy('id', 'DESC')->get();
                $gyms = makePaginate(collect($gyms), 10);
            }
            return response()->view('gyms.ajax_search_gyms', compact('gyms'));
        }
    }

    /**
     * @Description Delete image by index of image and gym id
     * @param $index
     * @param $gymId
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function deleteImage($index, $gymId)
    {
        $gym = Gym::find((int)$gymId);
        if ($gym) {
            if ($gym->images) {
                $allImageUrls = [];
                foreach (json_decode($gym->images) as $i => $imgUrl) {
                    if ($i == (int)$index) {
                        deleteFile($imgUrl);
                    } else {
                        array_push($allImageUrls, $imgUrl);
                    }
                }
                $gym->update([
                    'images' => json_encode($allImageUrls) ?? null
                ]);
            }
            Session::flash('alert-danger', 'Image has been removed.');
        }
        return redirect()->back();
    }

    /**
     * @Description Get Passes List
     * @param $gymId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function passesList($gymId)
    {
        $passes = Pass::getByGymId((int)$gymId);
        return view('gyms.passes_list', compact('passes'));
    }

    /**
     * @Description Update Pass Price
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function updatePassPrice(Request $request)
    {
        if ((double)$request->price) {
            Pass::whereId((int)$request->pass_id)->update([
                'price' => (double)$request->price,
                'active' => 1
            ]);
            Session::flash('alert-success', 'Pass price has been updated.');
        }
        return redirect()->back();
    }

    public function updateGymPercentage(Request $request)
    {

        $gym = Gym::find($request->gym_id);
        if($gym){
            $gym->percentage = $request->gym_price;
            $gym->save();
            Session::flash('alert-success', 'SwitchFit Percentage updated for this gym.'); 
        }else{
            Session::flash('alert-danger', 'No Gym exist with this name.');
        }
        
        return redirect()->back();
    }

    /**
     * @Description update pass status is active or not
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function updatePassActive(Request $request)
    {
        Pass::whereId((int)$request->passId)->update([
            'active' => (int)$request->active
        ]);
        return response()->json(['msg' => 'Pass status has been updated.']);
    }

    /**
     * @Description Upload Image on mention path via Drop Zone
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function uploadImages(Request $request)
    {
        $fileUrl = '';
        $path = $request->path;
        $file = $request->file('file');
        if ($file) {
//            $filename = str_replace(' ', '_', Str::random(10) . $file->getClientOriginalName());
            $filename = $file->getClientOriginalName();
            $fileUrl = $path . $filename;
            $file->move($path, $filename);
        }
        return response()->json(['image_url' => $fileUrl], 200);
    }

    /**
     * @Description remove Image from path via Drop Zone
     * @param $filename
     * @param $path
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function removeImage($filename, $path)
    {
        $path = str_replace('*', '/', $path);
        deleteFile($path . $filename);
        return response()->json(['message' => 'Image has been removed.'], 200);
    }

    /**
     * @Description Show reviews of gym
     * @param $gymId
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function showGymReviews($gymId)
    {
        $reviews = Review::getReviewByGymId($gymId);
        return view('gyms.show_reviews', compact('reviews'));
    }

    /**
     * @Description Delete Review
     * @param $reviewId
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function deleteReview($reviewId)
    {
        Review::find($reviewId)->delete();
        ReviewLink::where('review_id', $reviewId)->delete();
        Session::flash('alert-danger', 'Review has been deleted');
        return redirect()->back();
    }

    /**
     * @Description Show list of gym members
     * @param $gymId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function showGymMembersList($gymId)
    {
        if(isset($_GET['notification_id'])){
            $notification = Notification::find($_GET['notification_id']);
            if($notification){

                $notification->is_read = 1;
                $notification->save();
            }
        }

        $members = Gym::getActiveGymMembers($gymId);
        return view('gyms.members_list', compact('members'));
    }
    /*All Members*/
    public  function getAllActiveMembers()
    {

        $members = [];
        $passesOrderItems = PassOrderItems::where([['gym_owner_id', Auth::id()],['is_used', 0], ['is_expire', 0]])->orderBy('book_date','DESC')->get();

        $users = [];
        if ($passesOrderItems) {
            foreach ($passesOrderItems as $passesOrderItem) {
                $data = [];
                $data = $passesOrderItem;
                $user = User::find($passesOrderItem->buyer_id);
                
                if(in_array($user, $users)){
                    continue;
                }else{
                    $userpassesOrderItems = PassOrderItems::where([['gym_owner_id', Auth::id()], ['is_used', 0], ['is_expire', 0],['buyer_id',$passesOrderItem->buyer_id]])->get();
                    $data['buyer_user'] = $user;
                    $data['total_passes'] = $userpassesOrderItems->count();

                    array_push($members, $data);
                }
                $users[] = $user;
                
            }
        }

        return view('gyms.members_list', compact('members'));
    }

    /**
     * @Description Show list of gym members
     * @param $gymId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function showGymMembersDetail($gymId,$memberId)
    {

     $members_details = PassOrderItems::where([['gym_id', $gymId], ['is_used', 0],['buyer_id',$memberId]])->get();
     //return $members_details;
     return view('gyms.members_passes_list', compact('members_details'));
 }
 public function getGymPayouts($id,Request $request){
    if(isset($_GET['notification_id'])){
        $notification = Notification::find($_GET['notification_id']);
        if($notification){

            $notification->is_read = 1;
            $notification->save();
        }
    }
    $all_bookings = [];
    if($request->coming == 1){


       $bookings =   PassOrderItems::where('gym_owner_id',$id)->where('payout_status','pending')->groupBy('gym_id', 'book_date')->select(DB::raw('book_date'),DB::raw('gym_id'), DB::raw('count(pass_id) as total_passes'),DB::raw('sum(gym_owner_amount) as sub_total'),DB::raw('sum(price) as price'),DB::raw('sum(switch_fit_fee) as switch_fit_fee'),DB::raw('payout_status'))
       ->orderby('book_date','DESC')
       ->with('gym')
       ->get();

   }else{
    $bookings =   PassOrderItems::where('gym_owner_id',$id)->groupBy('gym_id', 'book_date')->select(DB::raw('book_date'),DB::raw('gym_id'), DB::raw('count(pass_id) as total_passes'),DB::raw('sum(gym_owner_amount) as sub_total'),DB::raw('sum(price) as price'),DB::raw('sum(switch_fit_fee) as switch_fit_fee'),DB::raw('payout_status'))
    ->orderby('book_date','DESC')
    ->with('gym')
    ->get();
}

$all_bookings['bookings'] = $bookings;
$upcoming = PassOrderItems::where('gym_owner_id',$id)->where('payout_status','pending')->get();
$upcoming = $upcoming->sum('gym_owner_amount');
$all_bookings['upcoming'] = $upcoming;
$total_price =  $bookings->sum('sub_total');

$all_bookings['total_price'] = $total_price;
    //return $all_bookings;
return view('gym_owners.payouts', compact('all_bookings'));

}
public function getPayoutsByGym($id){
    if(isset($_GET['notification_id'])){
        $notification = Notification::find($_GET['notification_id']);
        if($notification){

            $notification->is_read = 1;
            $notification->save();
        }
    }
    $all_bookings = [];
    $bookings =   PassOrderItems::where('gym_id',$id)->groupBy('book_date')->select(DB::raw('book_date'),DB::raw('gym_owner_id'),DB::raw('gym_id'), DB::raw('count(pass_id) as total_passes'),DB::raw('sum(gym_owner_amount) as sub_total'),DB::raw('sum(price) as price'),DB::raw('sum(switch_fit_fee) as switch_fit_fee'),DB::raw('payout_status'))
    ->orderby('book_date','DESC')
    ->with('gym')
    ->get();
    $all_bookings['bookings'] = $bookings;
    $upcoming = PassOrderItems::where('gym_id',$id)->where('payout_status','pending')->get();
    $upcoming = $upcoming->sum('gym_owner_amount');
    $all_bookings['upcoming'] = $upcoming;
    $total_price =  $bookings->sum('sub_total');

    $all_bookings['total_price'] = $total_price;
    //return $all_bookings;
    return view('gym_owners.payouts-gym', compact('all_bookings'));

}
public function getPayoutDetailSheet(Request $request){

    $all_bookings = [];
    $gym_owner_id = $request->gym_owner_id;
    $bookings =   PassOrderItems::where('gym_owner_id',$gym_owner_id)
    ->groupBy('gym_id', 'book_date')->select(DB::raw('book_date'),DB::raw('gym_id'), DB::raw('count(pass_id) as total_passes'),DB::raw('sum(gym_owner_amount) as sub_total'),DB::raw('sum(price) as price'),DB::raw('sum(switch_fit_fee) as switch_fit_fee'),DB::raw('payout_status'));

    $upcoming = PassOrderItems::where('gym_owner_id',$gym_owner_id)->where('payout_status','pending');

    if($request->from_date){
        $from_date = explode('/', $request->from_date);
        $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
    }
    if($request->to_date){
        $to_date = explode('/', $request->to_date);
        $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
    }

    if($request->from_date && $request->to_date){
        $bookings =  $bookings
        ->whereDate('book_date','<=',  $to_date)
        ->whereDate('book_date', '>=', $from_date)
        ->orderby('book_date','DESC')
        ->with('gym')
        ->get();

        $upcoming =  $upcoming
        ->whereDate('book_date','<=',  $to_date)
        ->whereDate('book_date', '>=', $from_date)
        ->get();
    }
    elseif($request->from_date){
        $bookings =  $bookings->whereDate('book_date', '>=', $from_date)
        ->orderby('book_date','DESC')
        ->with('gym')->get();

        $upcoming =  $upcoming->whereDate('book_date', '>=', $from_date)->get();
    }
    elseif($request->to_date){
        $bookings =  $bookings->whereDate('book_date','<=',  $to_date)->orderby('book_date','DESC')
        ->with('gym')->get();
        $upcoming =  $upcoming->whereDate('book_date','<=',  $to_date)->get();
    }

    else{
        $bookings =  $bookings
        ->orderby('book_date','DESC')
        ->with('gym')
        ->get();
        $upcoming =  $upcoming->get();
    }
    $all_bookings['bookings'] = $bookings;
    $upcoming = $upcoming->sum('gym_owner_amount');
    $all_bookings['upcoming'] = $upcoming;
    $total_price =  $bookings->sum('sub_total');

    $all_bookings['total_price'] = $total_price;

    $data_array[] = array('UpComing Earning', 'Total Earning');

    $data_array[] = array(
     'UpComing Earning'  => '£ '.number_format((float)$all_bookings['upcoming'], 2, '.', ''),
     'Total Earning'   => '£ '.number_format((float)$all_bookings['total_price'], 2, '.', '')

 );

    $data_array[] = array('Date', 'Gym Name', 'Total Pass','Total Payment','Total Receiveable','Status');
    if(!empty($all_bookings['bookings'])){

      $bookings = $all_bookings['bookings'];

      foreach($bookings as $booking)
      {
        $data_array[] = array(
         'Date'  => date("d M Y", strtotime($booking->book_date)),
         'Gym Name'   => ($booking->gym)?$booking->gym->name:'',
         'Total Pass'    => $booking->total_passes,
         'Total Payment'  => '£ '.number_format((float)$booking->sub_total + $booking->switch_fit_fee, 2, '.', ''),
         'Total Receiveable'   => '£ '.number_format((float)$booking->sub_total, 2, '.', ''),
         'Status'   => ($booking->payout_status =='pending')?'pending':'completed'
     );

    }
    $export = new InvoicesExport([
        $data_array
    ]);
    $unique_id = uniqid();
    Excel::store($export, 'public/reports/'.$unique_id.'.xlsx');

    return response()->json(['file' => '/storage/reports/'.$unique_id.'.xlsx']);
}
}
public function getPayoutDetailDateSearch(Request $request){

    $all_bookings = [];
    $gym_owner_id = $request->gym_owner_id;
    $bookings =   PassOrderItems::where('gym_owner_id',$gym_owner_id)
    ->groupBy('gym_id', 'book_date')->select(DB::raw('book_date'),DB::raw('gym_id'), DB::raw('count(pass_id) as total_passes'),DB::raw('sum(gym_owner_amount) as sub_total'),DB::raw('sum(price) as price'),DB::raw('sum(switch_fit_fee) as switch_fit_fee'),DB::raw('payout_status'));

    $upcoming = PassOrderItems::where('gym_owner_id',$gym_owner_id)->where('payout_status','pending');

    if($request->from_date){
        $from_date = explode('/', $request->from_date);
        $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
    }
    if($request->to_date){
        $to_date = explode('/', $request->to_date);
        $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
    }

    if($request->from_date && $request->to_date){
        $bookings =  $bookings
        ->whereDate('book_date','<=',  $to_date)
        ->whereDate('book_date', '>=', $from_date)
        ->orderby('book_date','DESC')
        ->with('gym')
        ->get();

        $upcoming =  $upcoming
        ->whereDate('book_date','<=',  $to_date)
        ->whereDate('book_date', '>=', $from_date)
        ->get();
    }
    elseif($request->from_date){
        $bookings =  $bookings->whereDate('book_date', '>=', $from_date)
        ->orderby('book_date','DESC')
        ->with('gym')->get();

        $upcoming =  $upcoming->whereDate('book_date', '>=', $from_date)->get();
    }
    elseif($request->to_date){
        $bookings =  $bookings->whereDate('book_date','<=',  $to_date)->orderby('book_date','DESC')
        ->with('gym')->get();
        $upcoming =  $upcoming->whereDate('book_date','<=',  $to_date)->get();
    }

    else{
        $bookings =  $bookings
        ->orderby('book_date','DESC')
        ->with('gym')
        ->get();
        $upcoming =  $upcoming->get();
    }
    $all_bookings['bookings'] = $bookings;
    $upcoming = $upcoming->sum('gym_owner_amount');
    $all_bookings['upcoming'] = $upcoming;
    $total_price =  $bookings->sum('sub_total');

    $all_bookings['total_price'] = $total_price;

    ob_start();
    ?>
    <div class="dropdown float-right">
      <h4 class="header-title">Total Earning: <i class="mdi mdi-currency-gbp"></i>
        <?php echo number_format((float)$all_bookings['total_price'], 2, '.', ''); ?>
    </h4>
</div>

<h4 class="header-title mb-3">UpComing Earning: <i class="mdi mdi-currency-gbp"></i>
    <?php echo number_format((float)$all_bookings['upcoming'], 2, '.', ''); ?></h4>

    <div class="table-responsive">
      <table class="table table-hover table-centered table-nowrap m-0">

        <thead>
          <tr>
            <th>Date</th>
            <th>Gym Name</th>
            <th>Total Pass</th>
            <th>Total Payment</th>
            <th>Total Receiveable</th>
            <th>Status</th>

        </tr>
    </thead>
    <tbody>
      <?php

      if(!empty($all_bookings['bookings'])):

          $bookings = $all_bookings['bookings'];

          foreach($bookings as $booking):
            ?>

            <tr>
                <td>
                    <?php echo date("d M Y", strtotime($booking->book_date)); ?>

                </td>

                <td style="text-align: center;">
                  <?php echo ($booking->gym)?$booking->gym->name:''; ?>
              </td>
              <td style="text-align: center;">
                  <?php echo $booking->total_passes; ?>
              </td>

              <td>
                  <i class="mdi mdi-currency-gbp"></i><?php echo number_format((float)$booking->sub_total + $booking->switch_fit_fee, 2, '.', ''); ?>
              </td>


              <td>
                  <i class="mdi mdi-currency-gbp"></i><?php echo number_format((float)$booking->sub_total, 2, '.', ''); ?>
              </td>



              <td>

                  <?php if($booking->payout_status == 'pending'): ?>

                      <strong class="text-warning status-shown payout-status-pending">
                      Pending</strong>
                      <a href="<?php echo url('/').'/get/date/vise/gym/bookings/'.$booking->gym_id.'/'.$booking->booking_date; ?>" ><i class="fa fa-eye" aria-hidden="true"></i></a>
                      <?php else: ?>
                          <strong class="text-success status-shown">Completd</strong>
                          <a href="{{ url('/') }}/get/date/vise/bookings/{{$booking->booking_date}}/{{getUrlSegment(4)}}"><i class="fa fa-eye" aria-hidden="true"></i></a>

                      <?php endif; ?>
                  </td>
              </tr>
              <?php
          endforeach;
      endif;
      ?>



  </tbody>
</table>
</div>

<?php

return ob_get_clean();


}
public function PayoutDetailByGymExport(Request $request){
    $all_bookings = [];
    $gym_id = $request->gym_id;
    $bookings =   PassOrderItems::where('gym_id',$gym_id)
    ->groupBy('book_date')->select(DB::raw('book_date'),DB::raw('gym_id'), DB::raw('count(pass_id) as total_passes'),DB::raw('sum(gym_owner_amount) as sub_total'),DB::raw('sum(price) as price'),DB::raw('sum(switch_fit_fee) as switch_fit_fee'),DB::raw('payout_status'));

    $upcoming = PassOrderItems::where('gym_id',$gym_id)->where('payout_status','pending');

    if($request->from_date){
        $from_date = explode('/', $request->from_date);
        $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
    }
    if($request->to_date){
        $to_date = explode('/', $request->to_date);
        $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
    }

    if($request->from_date && $request->to_date){
        $bookings =  $bookings
        ->whereDate('book_date','<=',  $to_date)
        ->whereDate('book_date', '>=', $from_date)
        ->orderby('book_date','DESC')
        ->with('gym')
        ->get();

        $upcoming =  $upcoming
        ->whereDate('book_date','<=',  $to_date)
        ->whereDate('book_date', '>=', $from_date)
        ->get();
    }
    elseif($request->from_date){
        $bookings =  $bookings->whereDate('book_date', '>=', $from_date)
        ->orderby('book_date','DESC')
        ->with('gym')->get();

        $upcoming =  $upcoming->whereDate('book_date', '>=', $from_date)->get();
    }
    elseif($request->to_date){
        $bookings =  $bookings->whereDate('book_date','<=',  $to_date)->orderby('book_date','DESC')
        ->with('gym')->get();
        $upcoming =  $upcoming->whereDate('book_date','<=',  $to_date)->get();
    }

    else{
        $bookings =  $bookings
        ->orderby('book_date','DESC')
        ->with('gym')
        ->get();
        $upcoming =  $upcoming->get();
    }
    $all_bookings['bookings'] = $bookings;
    $upcoming = $upcoming->sum('gym_owner_amount');
    $all_bookings['upcoming'] = $upcoming;
    $total_price =  $bookings->sum('sub_total');

    $all_bookings['total_price'] = $total_price;

    $data_array[] = array('UpComing Earning', 'Total Earning');

    $data_array[] = array(
     'UpComing Earning'  => '£ '.number_format((float)$all_bookings['upcoming'], 2, '.', ''),
     'Total Earning'   => '£ '.number_format((float)$all_bookings['total_price'], 2, '.', '')

 );

    $data_array[] = array('Date', 'Gym Name', 'Total Pass','Total Payment','Total Receiveable','Status');
    if(!empty($all_bookings['bookings'])){

      $bookings = $all_bookings['bookings'];

      foreach($bookings as $booking)
      {
        $data_array[] = array(
         'Date'  => date("d M Y", strtotime($booking->book_date)),
         'Gym Name'   => ($booking->gym)?$booking->gym->name:'',
         'Total Pass'    => $booking->total_passes,
         'Total Payment'  => '£ '.number_format((float)$booking->sub_total + $booking->switch_fit_fee, 2, '.', ''),
         'Total Receiveable'   => '£ '.number_format((float)$booking->sub_total, 2, '.', ''),
         'Status'   => ($booking->payout_status =='pending')?'pending':'completed'
     );



    }
    $export = new InvoicesExport([
        $data_array
    ]);
    $unique_id = uniqid();
    Excel::store($export, 'public/reports/'.$unique_id.'.xlsx');

    return response()->json(['file' => '/storage/reports/'.$unique_id.'.xlsx']);
}

}
public function getPayoutDetailByGymDateSearch(Request $request){

    $all_bookings = [];
    $gym_id = $request->gym_id;
    $bookings =   PassOrderItems::where('gym_id',$gym_id)
    ->groupBy('book_date')->select(DB::raw('book_date'),DB::raw('gym_id'), DB::raw('count(pass_id) as total_passes'),DB::raw('sum(gym_owner_amount) as sub_total'),DB::raw('sum(price) as price'),DB::raw('sum(switch_fit_fee) as switch_fit_fee'),DB::raw('payout_status'));

    $upcoming = PassOrderItems::where('gym_id',$gym_id)->where('payout_status','pending');

    if($request->from_date){
        $from_date = explode('/', $request->from_date);
        $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
    }
    if($request->to_date){
        $to_date = explode('/', $request->to_date);
        $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
    }

    if($request->from_date && $request->to_date){
        $bookings =  $bookings
        ->whereDate('book_date','<=',  $to_date)
        ->whereDate('book_date', '>=', $from_date)
        ->orderby('book_date','DESC')
        ->with('gym')
        ->get();

        $upcoming =  $upcoming
        ->whereDate('book_date','<=',  $to_date)
        ->whereDate('book_date', '>=', $from_date)
        ->get();
    }
    elseif($request->from_date){
        $bookings =  $bookings->whereDate('book_date', '>=', $from_date)
        ->orderby('book_date','DESC')
        ->with('gym')->get();

        $upcoming =  $upcoming->whereDate('book_date', '>=', $from_date)->get();
    }
    elseif($request->to_date){
        $bookings =  $bookings->whereDate('book_date','<=',  $to_date)->orderby('book_date','DESC')
        ->with('gym')->get();
        $upcoming =  $upcoming->whereDate('book_date','<=',  $to_date)->get();
    }

    else{
        $bookings =  $bookings
        ->orderby('book_date','DESC')
        ->with('gym')
        ->get();
        $upcoming =  $upcoming->get();
    }
    $all_bookings['bookings'] = $bookings;
    $upcoming = $upcoming->sum('gym_owner_amount');
    $all_bookings['upcoming'] = $upcoming;
    $total_price =  $bookings->sum('sub_total');

    $all_bookings['total_price'] = $total_price;

    ob_start();
    ?>
    <div class="dropdown float-right">
      <h4 class="header-title">Total Earning: <i class="mdi mdi-currency-gbp"></i>
        <?php echo number_format((float)$all_bookings['total_price'], 2, '.', ''); ?>
    </h4>
</div>

<h4 class="header-title mb-3">UpComing Earning: <i class="mdi mdi-currency-gbp"></i>
    <?php echo number_format((float)$all_bookings['upcoming'], 2, '.', ''); ?></h4>

    <div class="table-responsive">
      <table class="table table-hover table-centered table-nowrap m-0">

        <thead>
          <tr>
            <th>Date</th>
            <th>Gym Name</th>
            <th>Total Pass</th>
            <th>Total Payment</th>
            <th>Total Receiveable</th>
            <th>Status</th>

        </tr>
    </thead>
    <tbody>
      <?php

      if(!empty($all_bookings['bookings'])):

          $bookings = $all_bookings['bookings'];

          foreach($bookings as $booking):
            ?>

            <tr>
                <td>
                    <?php echo date("d M Y", strtotime($booking->book_date)); ?>

                </td>

                <td style="text-align: center;">
                  <?php echo ($booking->gym)?$booking->gym->name:''; ?>
              </td>
              <td style="text-align: center;">
                  <?php echo $booking->total_passes; ?>
              </td>

              <td>
                  <i class="mdi mdi-currency-gbp"></i><?php echo number_format((float)$booking->sub_total + $booking->switch_fit_fee, 2, '.', ''); ?>
              </td>


              <td>
                  <i class="mdi mdi-currency-gbp"></i><?php echo number_format((float)$booking->sub_total, 2, '.', ''); ?>
              </td>

              <td>

                  <?php if($booking->payout_status == 'pending'): ?>

                      <strong class="text-warning status-shown payout-status-pending">
                      Pending</strong>
                      <a href="<?php echo url('/').'/get/date/vise/gym/bookings/'.$gym_id.'/'.$booking->booking_date; ?>" ><i class="fa fa-eye" aria-hidden="true"></i></a>
                      <?php else: ?>
                          <strong class="text-success status-shown">Completd</strong>
                          <a href="<?php echo url('/').'/get/date/vise/gym/bookings/'.$gym_id.'/'.$booking->booking_date; ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>

                      <?php endif; ?>
                  </td>
              </tr>
              <?php
          endforeach;
      endif;
      ?>



  </tbody>
</table>
</div>

<?php

return ob_get_clean();


}

/*Get Booking Detail Single*/
public function getDateViseBookings($gym_id,$date){


    $bookings =   PassOrderItems::where('gym_id',$gym_id)->where('book_date',$date)
    ->orderby('book_date','DESC')
    ->with('gym','pass','byer')
    ->get();
    //return $bookings;
    return view('gym_owners.payouts-details', compact('bookings'));

}


}
