<?php

namespace App\Http\Controllers;

use App\Gym;
use App\Traits\ApiResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GymOwnerController extends Controller
{
    use ApiResponse;

    /**
     * GymOwnerController constructor.
     * @Author Khuram Qadeer.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'is_role:super_admin']);
    }

    /**
     * @Description Create Gym Owner template
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function create()
    {
        return view('gym_owners.create');
    }

    /**
     * @Description Store Gym Owner
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'manager_name' => 'required',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'email' => 'required|email|unique:users',
            'logo' => 'required',
            'terms_conditions' => 'accepted',
        ], [
            'lat.required' => 'Please, Select Location From Map',
            'lng.required' => 'Please, Select Location From Map',
            'email.email'  =>  'Please, Enter Valid Email Address'
        ]);


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
    $password = Str::random(10, 15);

    $fileUrl = '';
    $image = $request->logo;
    if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
        $image = substr($image, strpos($image, ',') + 1);
        $type = strtolower($type[1]); 

        $image = base64_decode($image);
        $destinationPath    = 'assets/uploads/logos/';

        if (!file_exists(public_path().'/'.$destinationPath)) {
                    //dd(public_path().$destinationPath);
            mkdir(public_path().'/'.$destinationPath, 0777, true);

        }
        $fileName = 'image_'.time().'.'.$type;

        $tempFile = $destinationPath . $fileName;

        $fileUrl = $destinationPath . $fileName;

        file_put_contents(public_path().'/'.$tempFile, $image);

    }      


    $user = User::create([
        'role_id' => 2,
        'first_name' => $request->name,
        'address' => $request->address,
        'lat' => (double)$request->lat ?? null,
        'lng' => (double)$request->lng ?? null,
        'manager_name' => $request->manager_name,
        'phone' => $request->phone,
        'email' => $request->email,
        'password' => bcrypt($password),
        'logo' => $fileUrl ?? null,
        'terms_conditions' => $request->terms_conditions == 'on' ? 1 : 0
    ]);

    $data['user'] = $user;
    $data['password'] = $password;
    sendEmail('emails.credentials_send', 'Gym Owner Credentials', $user->email, $user->first_name, $data);

    Session::flash('alert-success', 'Gym Owner has been Created.');
    $this->setResponse('success', 1,200, []);
    return response()->json($this->response, $this->status);
//        return redirect(route('gym_owner.list'));
}

    /**
     * @Description Show view page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function show($id)
    {
        $gymOwner = User::findOrFail($id);
        return view('gym_owners.show', compact('gymOwner'));
    }

    /**
     * @Description Edit gym owner
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function edit($id)
    {
        $gymOwner = User::findOrFail($id);
        return view('gym_owners.edit', compact('gymOwner'));
    }

    /**
     * @Description update gym owner
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function update(Request $request)
    {
        $id = (int)$request->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users,id,' . $id,
            'address' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'manager_name' => 'required',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'email' => 'required|email|unique:users,id,' . $id,
            'terms_conditions' => 'accepted',
        ], [
            'lat.required' => 'Please, Select Location From Map',
            'lng.required' => 'Please, Select Location From Map',
        ]);

        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
//            return redirect()->back()
//                ->withErrors($validator)
//                ->withInput();
        }

        $email_array = explode("@",$request->email);

        //dd(strpos($email_array['1'], '+'));

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

       // dd($validator->fails());
    if ($validator->errors()->first()) {

        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
//            return redirect()->back()
//                ->withErrors($validator)
//                ->withInput();
    }
    $fileUrl = User::find($id)['logo'];
    $fileUrl = '';
    $image = $request->logo;
    if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
        $image = substr($image, strpos($image, ',') + 1);
        $type = strtolower($type[1]); 

        $image = base64_decode($image);
        $destinationPath    = 'assets/uploads/logos/';

        if (!file_exists(public_path().'/'.$destinationPath)) {
                    //dd(public_path().$destinationPath);
            mkdir(public_path().'/'.$destinationPath, 0777, true);

        }
        $fileName = 'image_'.time().'.'.$type;

        $tempFile = $destinationPath . $fileName;

        $fileUrl = $destinationPath . $fileName;

        file_put_contents(public_path().'/'.$tempFile, $image);

    } 

    User::find($id)->update([
        'role_id' => 2,
        'first_name' => $request->name,
        'address' => $request->address,
        'lat' => (double)$request->lat ?? null,
        'lng' => (double)$request->lng ?? null,
        'manager_name' => $request->manager_name,
        'phone' => $request->phone,
        'email' => $request->email,
        'logo' => $fileUrl ?? null,
        'terms_conditions' => $request->terms_conditions == 'on' ? 1 : 0,
    ]);

    Session::flash('alert-success', 'Gym Owner has been updated.');
    $this->setResponse('success', 1,200, []);
    return response()->json($this->response, $this->status);
//        return redirect(route('gym_owner.list'));
}

    /**
     * @Description Delete Gym Owner
     * @param $id
     * @Author Khuram Qadeer.
     */
    public function delete($id)
    {
        //User::deleteById($id);
        $user = User::find($id);
        $user->is_deleted = 1;
        $user->save();
        $delete_gym = Gym::where('user_id', $user->id)
        ->update(['is_deleted' => 1]);
        
        Session::flash('alert-danger', 'Gym Owner has been deleted.');
        return redirect(route('gym_owner.list'));
    }

    /**
     * @Description Listing of Gym owners
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function list()
    {
        $gymOwners = User::where('role_id', 2)->where('is_deleted',0)->orderBy('id', 'DESC')->get();
        
        return view('gym_owners.list', compact('gymOwners'));
    }

    /**
     * @Description List Gyms by gym owner id
     * @param $gymOwnerId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function listGyms($gymOwnerId)
    {
        $gyms = Gym::getAllByUserId((int)$gymOwnerId);
        $gyms = makePaginate(collect($gyms), 10);
        return view('gyms.gym_owner_gyms_list',compact('gyms'));
    }
}
