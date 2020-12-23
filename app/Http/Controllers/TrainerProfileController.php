<?php

namespace App\Http\Controllers;

use App\CategoryLink;
use App\TrainerActivity;
use App\Traits\ApiResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Day;
use App\TrainerAvailabilty;
use App\Notification;
use App\ReviewOption;
use DB;
use App\TrainerBooking;
use App\Http\Controllers\POSTrait;
use Excel;
use App\Exports\InvoicesExport;

class TrainerProfileController extends Controller
{
    use ApiResponse;
    use POSTrait;

    /**
     * @Description Display a listing of the trainers.
     *
     * @return \Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    public function index(Request $request)
    {

        $users = [];
        if($request->searc_param){
            $trainers = User::search($request->searc_param)->with('reviews')->where('role_id', 3)->where('is_deleted',0)->orderByDesc('id')->paginate(15);
        }
        else{
            $trainers = User::where('role_id', 3)->where('is_deleted',0)->orderByDesc('id')->paginate(15);
        }
        if(!empty($trainers)){
            foreach ($trainers as $user) {
             $ratings = $user->reviews->sum('stars');
             $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
             $user['average_rating'] = $ratings;
             $user['total_reviews'] = $user->reviews->count();
             $users[] = $user;

         }
     }

     return view('trainers.list', compact('trainers','users'));
 }
 public function searchTrainer(Request $request)
 {
    if($request->key){
        $trainers = User::search($request->key)->where('role_id', 3)->where('is_deleted',0)->orderByDesc('id')->paginate(15);
    }
    else{
        $trainers = User::where('role_id', 3)->where('is_deleted',0)->orderByDesc('id')->paginate(15);
    }
    ob_start();
    if($trainers->first()){

       foreach($trainers as $trainer):

        ?>
        <div class="col-sm-4 row_trainers">
            <div class="card-box trainer-box">
                <div class="row mlr-10">
                    <div class="col-5">
                        <div class="profile-img-holder">
                            <img src="<?php echo \App\User::getUserAvatar($trainer->id)?>" alt="img"/>
                        </div>
                    </div>
                    <div class="col-7 p-0">
                        <div class="trainer-content-holder">
                            <strong><?php echo\App\User::getFullName($trainer->id) ?></strong>
                            <small><a href="<?php echo url('/activities') .'/'.$trainer->id ?>">Total
                                Activities: <?php echo collect(\App\TrainerActivity::getByUserId($trainer->id))->count()?></a>  </small>
                                <?php 
                                if($trainer->is_verified==1):
                                    ?>
                                    <a href="#" style="pointer-events:none;"
                                    class="btn btn-verifed"><span><img
                                        src="/assets/images/verify-icon.png" alt="icon"/></span>Verified</a>
                                        <?php  else: ?>
                                            <a href="#" style="pointer-events:none;"
                                            class="btn btn-verifed gold-bg"><span><img
                                                src="/assets/images/unverify-icon.png" alt="icon"/></span>Unverified</a>
                                            <?php endif ?>
                                            <p>Joined on <?php echo $trainer->created_at->format('d M Y') ?></p>
                                            <div class="btn-group dot-btn">
                                                <a href="javascript:void(0);" class="dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <button class="dropdown-item" type="button"
                                                onclick="window.location.href='<?php echo route('trainer.show',$trainer->id) ?>';">
                                                View
                                            </button>
                                            <button class="dropdown-item" type="button"
                                            onclick="window.location.href='<?php echo route('trainer.edit',$trainer->id)?>'">
                                            Edit
                                        </button>
                                        <button class="dropdown-item" type="button"
                                        onclick="window.location.href='<?php echo url('/'); ?>/split-activities/<?php echo $trainer->id; ?>'">
                                        Acitivity Schedule
                                    </button>
                                    <button class="dropdown-item" type="button"
                                    onclick="window.location.href='<?php echo url('/'); ?>/trainer/payout/detail/<?php echo $trainer->id; ?>'">
                                    Payouts
                                </button>
                                <button class="dropdown-item" type="button"
                                onclick="questionNotification('Confirmation','Are You Sure? You want to delete trainer?','<?php echo route('trainer.delete',$trainer->id) ?>')">
                                Delete
                            </button>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
endforeach;
}

return ob_get_clean();

    //return view('trainers.list', compact('trainers'));
}

    /**
     * @Description Show the form for creating a new trainer.
     *
     * @return \Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    public function create()
    {
        return view('trainers.create');
    }

    /**
     * @Description Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|max:50|regex:/^[a-zA-Z ]+$/u',
            'last_name' => 'required|max:50|regex:/^[a-zA-Z ]+$/u',
            'email' => 'required|email|unique:users',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|max:13',
            'address' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'country' => 'required|max:50|regex:/^[a-zA-Z]+(\s[a-zA-Z]+)?$',
            'state' => 'required|max:50|regex:/^[a-zA-Z]+(\s[a-zA-Z]+)?$',
            'city' => 'required|max:50|regex:/^[a-zA-Z]+(\s[a-zA-Z]+)?$',
            'postal_code' => 'required|max:50|regex:/^[a-zA-Z]+(\s[a-zA-Z]+)?$',
            'about' => 'required|max:1000',
            'qualification_1' => 'required',
            'document' => 'required',
            'document_type' => 'required',
            'document_expire_date' => 'required',
            'availability' => 'required',
            'categories' => 'required',
        ], [
            'lat.required' => 'Please, Select Address From Map.',
            'lng.required' => 'Please, Select Address From Map.',
            'qualification_1.required' => 'Please, Add your qualifications.',
            'document.required' => 'Please, Add your verification document.',
            'email.email' => 'Please, Enter the valid email address'
        ]);
        $available = json_decode($request->availability);
        //dd($available);
        $categories = json_decode($request->categories);

        $validator->after(function ($validator) use ($available, $categories) {
            if (!$categories) {
                $validator->errors()->add('categories', ' Please, Select categories.');
            }
            // Time Schedule validation checking end time greater must be greater then start time
            if ($available) {
                foreach ($available as $day) {
                    if (!checkTimeEndTimeGreatThanStartTime($day->time_start, $day->time_end)) {
                       $current_day = Day::find($day->day_id);
                       $validator->errors()->add($current_day->name, ucfirst($current_day->name) . ' end time must be greater than start time.');
                   }
               }
           }
       });

        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $email_array = explode("@",$request->email);

        $email_array =  explode(".",$email_array['1']);
        $check_string = $email_array['1'];
        if ((strpos($check_string, '+') !== false)  || (strpos($check_string, '!') !== false) || (strpos($check_string, '@') !== false) || (strpos($check_string, '#') !== false) || (strpos($check_string, '$') !== false) || (strpos($check_string, '%') !== false) || (strpos($check_string, '6') !== false) || (strpos($check_string, '7') !== false) || (strpos($check_string, '*') !== false) || (strpos($check_string, ')') !== false) || (strpos($check_string, '_') !== false) || (strpos($check_string, '-') !== false) || (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $check_string))) {

            $validator->errors()->add('email',  ' Please, Enter Valid Email Address.');
        }
        if(isset($email_array['2'])){
           $check_string = $email_array['2'];

           if ((strpos($check_string, '+') !== false)  || (strpos($check_string, '!') !== false) || (strpos($check_string, '@') !== false) || (strpos($check_string, '#') !== false) || (strpos($check_string, '$') !== false) || (strpos($check_string, '%') !== false) || (strpos($check_string, '6') !== false) || (strpos($check_string, '7') !== false) || (strpos($check_string, '*') !== false) || (strpos($check_string, ')') !== false) || (strpos($check_string, '_') !== false) || (strpos($check_string, '-') !== false) || (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $check_string))) {

            $validator->errors()->add('email',  ' Please, Enter Valid Email Address.');
        }
    }
    if(empty($categories)){
       $validator->errors()->add('categories',  ' Please, Select atleast one category.'); 
    }
    if(empty(json_decode($request->availability))){
       $validator->errors()->add('categories',  ' Please, Select atleast one available day.'); 
    }
    
    

    if ($validator->errors()->first()) {

        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);

    }



        // uploading avatar
    $fileUrl = '';
    $image = $request->avatar;
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
    

        // uploading verification document
    $docFileUrl = '';
    $image = $request->document;
    if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
        $image = substr($image, strpos($image, ',') + 1);
        $type = strtolower($type[1]); 

        $image = base64_decode($image);
        $destinationPath    = 'assets/uploads/trainers/';

        if (!file_exists(public_path().'/'.$destinationPath)) {
                    //dd(public_path().$destinationPath);
            mkdir(public_path().'/'.$destinationPath, 0777, true);

        }
        $fileName = 'image_'.time().'doc.'.$type;

        $tempFile = $destinationPath . $fileName;

        $docFileUrl = $destinationPath . $fileName;

        file_put_contents(public_path().'/'.$tempFile, $image);

    } 
    
    $date_of_birth =  ($request->date_of_birth)?explode('/', $request->date_of_birth):'';
    $date_of_birth = ($date_of_birth)?$date_of_birth[2].'-'.$date_of_birth[1].'-'.$date_of_birth[0]:'';
    $document_expire_date =  ($request->document_expire_date)?explode('/', $request->document_expire_date):'';
    $document_expire_date = ($document_expire_date)?$document_expire_date[2].'-'.$document_expire_date[1].'-'.$document_expire_date[0]:'';
    $user = User::create([
            // basic info
        'email' => $request->email,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'date_of_birth' => $date_of_birth,
        'gender' => $request->gender,
        'phone' => $request->phone,
        'address' => $request->address,
        'lat' => (double)$request->lat ?? null,
        'lng' => (double)$request->lng ?? null,
        'country' => $request->country,
        'state' => $request->state,
        'city' => $request->city,
        'postal_code' => $request->postal_code,
        'avatar' => $fileUrl ?? null,
        'role_id' => 3,
            // professional info
        'about' => $request->about,
        'qualification_1' => $request->qualification_1,
        'document_type' => $request->document_type ?? null,
        'document' => $docFileUrl ?? null,
        'document_expire_date' => $document_expire_date ?? null,
            // availabilities
        'is_profile_completed' => 1,
    ]);

    $existing_availablities = TrainerAvailabilty::where('user_id',$user->id);
    if(!empty(TrainerAvailabilty::where('user_id',$user->id)->first())){
        $existing_availablities->delete();
    }
    $availibities = json_decode($request->availability);
    if (!empty($availibities)) {
        foreach ($availibities as $day) {
            $availibilty = new TrainerAvailabilty();

            $time_calculator = calculateMinutesHourse($day->time_start,$day->time_end);

            $availibilty->day_id = $day->day_id;
            $availibilty->available = $day->available;
            $availibilty->available = $day->available;
            $availibilty->time_start = $day->time_start;
            $availibilty->time_end = $day->time_end;
            $availibilty->user_id = $user->id;
            $availibilty->total_available_minutes = $time_calculator['total_minutes'];
            $availibilty->start_available_minutes_range = $time_calculator['start_range'];
            $availibilty->end_available_minutes_range = $time_calculator['end_range'];
            $availibilty->save();
        }
    }
    if ($categories) {
        foreach ($categories as $category) {
            CategoryLink::create([
                'user_id' => $user->id,
                'category_id' => (int)$category->id,
            ]);
        }
    }
    Session::flash('alert-success', 'Trainer profile has been created.');
    $this->setResponse('success', 1, 200, []);
    return response()->json($this->response, $this->status);
}

    /**
     * @Description Display the specified trainer.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    public function show($id)
    {
        $trainer = User::getUserData($id)['user'];
        return view('trainers.show', compact('trainer'));
    }

    /**
     * @Description Show the form for editing the specified trainer.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    public function edit($id)
    {

        if(isset($_GET['notification_id'])){
            $notification = Notification::find($_GET['notification_id']);
            if($notification){

                $notification->is_read = 1;
                $notification->save();
            }
        }
        $trainer = User::getUserData($id)['user'];
        return view('trainers.edit', compact('trainer'));
    }

    /**
     * @Description Update the specified trainer in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    public function updateTrainer(Request $request, $id)
    {

        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|regex:/^[\pL\s\-]+$/u|max:50',
            'last_name' => 'required|regex:/^[\pL\s\-]+$/u|max:50',
            'email' => 'required|email|unique:users,id,'. $id,
            'date_of_birth' => 'required',
            'gender' => 'required',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|max:13',
            'address' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'country' => 'required|max:50|regex:/^[\pL\s\-]+$/u',
            'state' => 'required|max:50|regex:/^[\pL\s\-]+$/u',
            'city' => 'required|max:50|regex:/^[\pL\s\-]+$/u',
            'postal_code' => 'required|alpha_num',
            'about' => 'required|max:1000',
            'qualification_1' => 'required',
            'document_type' => 'required',
            'document_expire_date' => 'required',
            'availability' => 'required',
            'categories' => 'required',
        ], [
            'lat.required' => 'Please, Select Address From Map.',
            'lng.required' => 'Please, Select Address From Map.',
            'qualification_1.required' => 'Please, Add your qualifications.',
            'document.required' => 'Please, Add your verification document.',
            'email.email' => 'Please, Enter a valid email address'
        ]);

        $available = json_decode($request->availability);

        $categories = json_decode($request->categories);


        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $email_array = explode("@",$request->email);

        $email_array =  explode(".",$email_array['1']);
        $check_string = $email_array['1'];
        if ((strpos($check_string, '+') !== false)  || (strpos($check_string, '!') !== false) || (strpos($check_string, '@') !== false) || (strpos($check_string, '#') !== false) || (strpos($check_string, '$') !== false) || (strpos($check_string, '%') !== false) || (strpos($check_string, '6') !== false) || (strpos($check_string, '7') !== false) || (strpos($check_string, '*') !== false) || (strpos($check_string, ')') !== false) || (strpos($check_string, '_') !== false) || (strpos($check_string, '-') !== false) || (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $check_string))) {

            $validator->errors()->add('email',  ' Please, Enter Valid Email Address.');
        }
        if(isset($email_array['2'])){
           $check_string = $email_array['2'];

           if ((strpos($check_string, '+') !== false)  || (strpos($check_string, '!') !== false) || (strpos($check_string, '@') !== false) || (strpos($check_string, '#') !== false) || (strpos($check_string, '$') !== false) || (strpos($check_string, '%') !== false) || (strpos($check_string, '6') !== false) || (strpos($check_string, '7') !== false) || (strpos($check_string, '*') !== false) || (strpos($check_string, ')') !== false) || (strpos($check_string, '_') !== false) || (strpos($check_string, '-') !== false) || (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $check_string))) {

            $validator->errors()->add('email',  ' Please, Enter Valid Email Address.');
        }
    }
    

    if ($validator->errors()->first()) {

        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);

    }

    $validator->after(function ($validator) use ($available, $categories) {
        if (!$categories) {
            $validator->errors()->add('categories', ' Please, Select categories.');
        }
            // Time Schedule validation checking end time greater must be greater then start time
        if ($available) {
            foreach ($available as $day) {
                if (!checkTimeEndTimeGreatThanStartTime($day->time_start, $day->time_end)) {
                    $validator->errors()->add($day->day, ucfirst($day->day) . ' end time must be greater than start time.');
                }
            }
        }
    });



    $user = User::find((int)$id);
        // uploading avatar
    $fileUrl = '';
    $image = $request->avatar;

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

    

        // uploading verification document
    $docFileUrl = '';
    $image = $request->document;
    if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
        $image = substr($image, strpos($image, ',') + 1);
        $type = strtolower($type[1]); 

        $image = base64_decode($image);
        $destinationPath    = 'assets/uploads/trainers/';

        if (!file_exists(public_path().'/'.$destinationPath)) {
                    //dd(public_path().$destinationPath);
            mkdir(public_path().'/'.$destinationPath, 0777, true);

        }
        $fileName = 'image_'.time().'doc.'.$type;

        $tempFile = $destinationPath . $fileName;

        $docFileUrl = $destinationPath . $fileName;

        file_put_contents(public_path().'/'.$tempFile, $image);

    }
    $date_of_birth =  ($request->date_of_birth)?explode('/', $request->date_of_birth):'';
    $date_of_birth = ($date_of_birth)?$date_of_birth[2].'-'.$date_of_birth[1].'-'.$date_of_birth[0]:'';
    $document_expire_date =  ($request->document_expire_date)?explode('/', $request->document_expire_date):'';
    $document_expire_date = ($document_expire_date)?$document_expire_date[2].'-'.$document_expire_date[1].'-'.$document_expire_date[0]:'';

    $user->update([
            // basic info
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'date_of_birth' => $date_of_birth,
        'gender' => $request->gender,
        'phone' => $request->phone,
        'address' => $request->address,
        'email' => $request->email,
        'lat' => (double)$request->lat ?? null,
        'lng' => (double)$request->lng ?? null,
        'country' => $request->country,
        'state' => $request->state,
        'city' => $request->city,
        'postal_code' => $request->postal_code,
        'avatar' => $fileUrl ? $fileUrl : $user->avatar,
        'role_id' => 3,
            // professional info
        'about' => $request->about,
        'qualification_1' => $request->qualification_1,
        'document_type' => $request->document_type ?? null,
        'document' => $docFileUrl ? $docFileUrl : $user->document,
        'document_expire_date' => $document_expire_date ?? null,
            // availabilities
    ]);
    $existing_availablities = TrainerAvailabilty::where('user_id',$user->id);
    if(!empty(TrainerAvailabilty::where('user_id',$user->id)->first())){
        $existing_availablities->delete();
    }
    $availibities = json_decode($request->availability);
    if (!empty($availibities)) {
        foreach ($availibities as $day) {
            $availibilty = new TrainerAvailabilty();

            $time_calculator = calculateMinutesHourse($day->time_start,$day->time_end);

            $availibilty->day_id = $day->day_id;
            $availibilty->available = $day->available;
            $availibilty->available = $day->available;
            $availibilty->time_start = $day->time_start;
            $availibilty->time_end = $day->time_end;
            $availibilty->user_id = $user->id;
            $availibilty->total_available_minutes = $time_calculator['total_minutes'];
            $availibilty->start_available_minutes_range = $time_calculator['start_range'];
            $availibilty->end_available_minutes_range = $time_calculator['end_range'];
            $availibilty->save();
        }
    }
    CategoryLink::deleteByUserId($id);
    if ($categories) {
        foreach ($categories as $category) {
            CategoryLink::create([
                'user_id' => $user->id,
                'category_id' => (int)$category->id,
            ]);
        }
    }
    Session::flash('alert-success', 'Trainer profile has been updated.');
    $this->setResponse('success', 1, 200, []);
    return response()->json($this->response, $this->status);
}

    /**
     * @Description Remove the specified trainer from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    public function delete($id)
    {
        $trainer = User::find($id);
        if ($trainer) {
            $trainer->is_deleted = 1;
            $trainer->save();
        }
        Session::flash('alert-success', 'Trainer profile has been deleted.');
        return redirect()->route('trainer.index');
    }

    /**
     * @Description Update Verified Status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function updateIsVerified(Request $request)
    {
        User::find((int)$request->id)->update([
            'is_verified' => (int)$request->status
        ]);

        $status = ($request->status == 1)?'Approved':'Rejected';

        $user = User::find((int)$request->id);
        $notification = new Notification();
        $notification->screen = 'profile_approved';
        $notification->user_id = $user->id;
        $notification->source_id = $user->id;            
        $notification->description = 'Your Profile has been '.$status.' by SwitchFit';
        $notification->source_image = ltrim($user->avatar, '/');
        $notification->save();

        if($user->notifications == 1){

            $notification->title = 'Profile '.$status;
            $notification->sent = 0;
        }
        $notification->save();

        $this->setResponse('success', 1, 200, []);
        return response()->json($this->response, $this->status);
    }
    public function getTrainerReviews($id){



        $final_data= [];
        $review_options_array = []; 
        $trainer_id= $id;

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
        $final_data['actual_reviews'] = $reviews_options;

        //return $final_data;

        return view('activities.show_reviews', compact('final_data'));


    }
    public function getPayoutDetail($trainer_id){

       /* $validator = \Validator::make($request->all(), [
            'trainer_id' => 'required'
        ]);

        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }*/
        $all_bookings = [];

        $bookings =  DB::table('trainer_bookings')
        ->select(DB::raw('payout'),DB::raw('booking_date'), DB::raw('sum(accepted) as total_activites'),DB::raw('sum(price) as total_amount'),DB::raw('sum(receiveable) as total_receiveable'))
        ->groupBy(DB::raw('booking_date') )
        ->where('owner_id',$trainer_id)
        ->where('accepted',1);

        $payable = TrainerBooking::where('payout',0)->where('accepted',1)->where('owner_id',$trainer_id);

        /*if($request->from_date && $request->to_date){
            $bookings =  $bookings
            ->whereDate('booking_date','<=',  $request->to_date)
            ->whereDate('booking_date', '>=', $request->from_date)
            ->get();

            $payable =  $payable
            ->whereDate('booking_date','<=',  $request->to_date)
            ->whereDate('booking_date', '>=', $request->from_date)
            ->get();

        }elseif($request->from_date){
            $bookings =  $bookings->whereDate('booking_date', '>=', $request->from_date)->get();

            $payable =  $payable->whereDate('booking_date', '>=', $request->from_date)->get();
        }
        elseif($request->to_date){
            $bookings =  $bookings->whereDate('booking_date','<=',  $request->to_date)->get();
            $payable =  $payable->whereDate('booking_date','<=',  $request->to_date)->get();
        }*/
        /*else{
            $bookings =  $bookings->get();
            $payable =  $payable->get();
        }*/

        $bookings =  $bookings->orderby('booking_date','DESC')->get();
        $payable =  $payable->get();
        $all_bookings['bookings'] = $bookings;
        $payable= ($payable->first())?$payable->sum('receiveable'):0;

        $total_payment =  $bookings->sum('total_receiveable');
        $all_bookings['payable'] = $payable;
        $all_bookings['total_payment'] = $total_payment;
       // return $all_bookings;
        //payouts-details
        return view('activities.payouts', compact('all_bookings'));
    }
    /*Get Booking Detail Single*/
    public function getDateViseBookings($date,$trainer_id){


        $booking = TrainerBooking::with('activity','trainer','buyer')->whereDate('booking_date',$date)->where('accepted',1)->where('owner_id',$trainer_id)->get();
        //return $booking;

        return view('activities.payouts-details', compact('booking'));

    }
    public function getPayoutsDownload(Request $request){

        $validator = \Validator::make($request->all(), [
            'trainer_id' => 'required'
        ]);

        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $all_bookings = [];

        $bookings =  DB::table('trainer_bookings')
        ->select(DB::raw('payout'),DB::raw('sum(price) as total_amount'),DB::raw('booking_date'), DB::raw('sum(accepted) as total_activites'),DB::raw('sum(receiveable) as total_receiveable'))
        ->groupBy(DB::raw('booking_date') )
        ->where('owner_id',$request->trainer_id)
        ->where('accepted',1);

        $payable = TrainerBooking::where('payout',0)->where('accepted',1)->where('owner_id',$request->trainer_id);

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
            ->whereDate('booking_date','<=',  $to_date)
            ->whereDate('booking_date', '>=', $from_date)
            ->orderby('booking_date','DESC')
            ->get();

            $payable =  $payable
            ->whereDate('booking_date','<=',  $to_date)
            ->whereDate('booking_date', '>=', $from_date)
            ->orderby('booking_date','DESC')
            ->get();

        }elseif($request->from_date){
            $bookings =  $bookings->whereDate('booking_date', '>=', $from_date)->orderby('booking_date','DESC')->get();

            $payable =  $payable->whereDate('booking_date', '>=', $from_date)->orderby('booking_date','DESC')->get();
        }
        elseif($request->to_date){
            $bookings =  $bookings->whereDate('booking_date','<=',  $to_date)->get();
            $payable =  $payable->whereDate('booking_date','<=',  $to_date)->orderby('booking_date','DESC')->get();
        }
        else{
            $bookings =  $bookings->orderby('booking_date','DESC')->get();
            $payable =  $payable->get();
        }

        
        $all_bookings['bookings'] = $bookings;
        $payable= ($payable->first())?$payable->sum('receiveable'):0;

        $total_payment =  $bookings->sum('total_receiveable');
        $all_bookings['payable'] = $payable;
        $all_bookings['total_payment'] = $total_payment;

        $data_array[] = array('UpComing Earning', 'Total Earning');

        $data_array[] = array(
           'UpComing Earning'  => '£ '.number_format((float)$all_bookings['payable'], 2, '.', ''),
           'Total Earning'   => '£ '.number_format((float)$all_bookings['total_payment'], 2, '.', '')

       );

        $data_array[] = array('Date', 'Total Activities', 'Total Payment','Total Receiveable','Status');
        if(!empty($all_bookings['bookings'])){

          $bookings = $all_bookings['bookings'];

          foreach($bookings as $booking)
          {

            $total_amount = '£ '.number_format((float)$booking->total_amount, 2, '.', '');
            $total_receiveable = '£ '.number_format((float)$booking->total_receiveable, 2, '.', '');

            $data_array[] = array(
               'Date'  => date("d M Y", strtotime($booking->booking_date)),
               'Total Activities'   => $booking->total_activites,
               'Total Payment'    => $total_amount,
               'Total Receiveable'  => $total_receiveable,
               'Status'   =>  ($booking->payout == 1)?'Completed':'Pending',
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

    $validator = \Validator::make($request->all(), [
        'trainer_id' => 'required'
    ]);

    if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }
    $all_bookings = [];

    $bookings =  DB::table('trainer_bookings')
    ->select(DB::raw('payout'),DB::raw('sum(price) as total_amount'),DB::raw('booking_date'), DB::raw('sum(accepted) as total_activites'),DB::raw('sum(receiveable) as total_receiveable'))
    ->groupBy(DB::raw('booking_date') )
    ->where('owner_id',$request->trainer_id)
    ->where('accepted',1);

    $payable = TrainerBooking::where('payout',0)->where('accepted',1)->where('owner_id',$request->trainer_id);

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
        ->whereDate('booking_date','<=',  $to_date)
        ->whereDate('booking_date', '>=', $from_date)
        ->orderby('booking_date','DESC')
        ->get();

        $payable =  $payable
        ->whereDate('booking_date','<=',  $to_date)
        ->whereDate('booking_date', '>=', $from_date)
        ->orderby('booking_date','DESC')
        ->get();

    }elseif($request->from_date){
        $bookings =  $bookings->whereDate('booking_date', '>=', $from_date)->orderby('booking_date','DESC')->get();

        $payable =  $payable->whereDate('booking_date', '>=', $from_date)->orderby('booking_date','DESC')->get();
    }
    elseif($request->to_date){
        $bookings =  $bookings->whereDate('booking_date','<=',  $to_date)->get();
        $payable =  $payable->whereDate('booking_date','<=',  $to_date)->orderby('booking_date','DESC')->get();
    }
    else{
        $bookings =  $bookings->orderby('booking_date','DESC')->get();
        $payable =  $payable->get();
    }


    $all_bookings['bookings'] = $bookings;
    $payable= ($payable->first())?$payable->sum('receiveable'):0;

    $total_payment =  $bookings->sum('total_receiveable');
    $all_bookings['payable'] = $payable;
    $all_bookings['total_payment'] = $total_payment;
        //return $all_bookings;
    ob_start();

    ?>

    <div class="dropdown float-right">
      <h4 class="header-title">Total Earning: <i class="mdi mdi-currency-gbp"></i><?php echo number_format((float)$all_bookings['total_payment'], 2, '.', ''); ?></h4>
  </div>

  <h4 class="header-title mb-3">UpComing Earning: <i class="mdi mdi-currency-gbp"></i><?php echo number_format((float)$all_bookings['payable'], 2, '.', ''); ?></h4>

  <div class="table-responsive">
      <table class="table table-hover table-centered table-nowrap m-0">

        <thead>
          <tr>
            <th>Date</th>
            <th>Total Activities</th>
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
                    <?php echo date("d M Y", strtotime($booking->booking_date)); ?>

                </td>

                <td style="text-align: center;">
                  <?php echo $booking->total_activites; ?>
              </td>

              <td>
                  <i class="mdi mdi-currency-gbp"></i><?php echo number_format((float)$booking->total_amount, 2, '.', ''); ?>
              </td>

              <td>
                  <i class="mdi mdi-currency-gbp"></i><?php echo number_format((float)$booking->total_receiveable, 2, '.', ''); ?>
              </td>

              <td>

                  <?php
                  if($booking->payout == 1):
                    ?>

                    <strong class="text-success status-shown">Completed</strong>
                    <a href="<?php echo url('/')?>/get/date/vise/bookings/<?php echo $booking->booking_date .'/'.$request->trainer_id; ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
                    <?php else:?>

                        <strong class="text-warning status-shown payout-status-pending">
                        Pending</strong>
                        <a href="<?php echo url('/')?>/get/date/vise/bookings/<?php echo $booking->booking_date .'/'.$request->trainer_id; ?>" ><i class="fa fa-eye" aria-hidden="true"></i></a>
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

}
