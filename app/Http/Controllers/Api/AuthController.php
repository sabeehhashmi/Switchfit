<?php

namespace App\Http\Controllers\Api;

use App\Country;
use App\LoginLogs;
use App\PasswordReset;
use App\Traits\ApiResponse;
use App\User;
use App\Day;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Notification;
class AuthController extends Controller
{

    use ApiResponse;

    /**
     * @Description Registration
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function signup(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'device_id' => 'required',
            'device_type' => 'required',
            'is_trainer' => 'required',
        ]);

        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $file = $request->file;
        $dirPath = 'assets/uploads/trainers/';
        $fileUrl = null;
        if ($file) {
            $filename = Str::random(40) . '.png';
            $imageInfo = explode(";base64,", $file);
            $image = str_replace(' ', '+', $imageInfo[1]);
            file_put_contents(public_path($dirPath . $filename), base64_decode($image));
            $fileUrl = $dirPath . $filename;
        }

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'device_id' => $request->device_id,
            'device_type' => $request->device_type,
            'provider' => $request->provider ?? null,
            'provider_id' => $request->provider_id ?? null,
            'role_id' => (int)$request->is_trainer == 1 ? 3 : 0,
            'avatar' => $fileUrl ?? null,
        ]);

        $data = [];
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $data['token'] = $user->createToken('switchFit')->accessToken;
            $user->update([
                'api_token' => $data['token'],
            ]);
            $data['user'] = Auth::user();
            $this->setResponse('user has been logged in.', 1, 200, $data);
        }
        $user = User::where('email',$request->email)->first();
        if($request->is_trainer == 1){

            $notification = new Notification();
            $notification->screen = 'trainer_detail';
            $notification->user_id = 0;
            $notification->source_id = $user->id;            
            $notification->description = 'New Trainer Profile Request is recieved from '.$user->first_name.' '.$user->last_name;

            $notification->source_image = ltrim($user->avatar, '/');
            $notification->save();

        }

        return response()->json($this->response, $this->status);
    }

    /**
     * @Description Login
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_id' => 'required',
            'device_type' => 'required',
        ]);
        $user = User::getByEmail($request->email);
        $validator->after(function ($validator) use ($user) {
            if (!$user) {
                $validator->errors()->add('email', 'Email Does not exists.');
            } else if ($user) {
                if ($user->role_id == 1 || $user->role_id == 2) {
                    $validator->errors()->add('email', 'Sorry, You can only login on website.');
                }
            }
        });
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $data = [];
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            if($user->is_disabled == 1){

                $this->setResponse('Sorry! You are disabled from admin', 0, 419, []);
                return response()->json($this->response, $this->status);

            }
            if($user->is_deleted == 1){

                $this->setResponse('This user no longer exist', 0, 419, []);
                return response()->json($this->response, $this->status);

            }
            $data['token'] = $user->createToken('switchFit')->accessToken;
            $user->update([
                'device_id' => $request->device_id,
                'device_type' => $request->device_type,
                'api_token' => $data['token'],
            ]);


            $user = User::with('categories','reviews')->find((int)$user->id);
            $availibities = Day::with(['availability'=> function ($query)  use ($user) {
                $query->whereuser_id($user->id);

            }])->get();
            $ratings = $user->reviews->sum('stars');
            $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
            $data['user'] = $user;
            $data['user']['average_rating'] = $ratings;
            $data['user']['total_reviews'] = $user->reviews->count();
            $data['user']['availibities'] = $availibities;

            $this->setResponse('user has been logged in.', 1, 200, $data);
        } else {
            // Unauthorized
            $this->setResponse('invalid credentials.', 0, 401, $data);
        }
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description Social Login and registration
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function socialSignupOrLogin(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'device_id' => 'required',
            'device_type' => 'required',
            'provider' => 'required',
            'provider_id' => 'required',
            'is_trainer' => 'required',
        ]);

        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $email = $request->email;
        $avatar = $request->avatar;
        $device_id = $request->device_id;
        $device_type = $request->device_type;
        $provider = $request->provider;
        $provider_id = $request->provider_id;
        $role_id = 0;
        $dirPath = 'uploads/users';
        $filename = Str::random(40) . '.png';
//        if ($avatar) {
//            Image::make($avatar)->save(public_path($dirPath . $filename));
//        }
        if (User::whereEmail($email)->exists()) {
            User::whereEmail($email)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'device_id' => $device_id,
                'device_type' => $device_type,
                'provider' => $provider,
                'provider_id' => $provider_id,
//                'avatar' => $dirPath . $filename,
            ]);
            $user = User::whereEmail($email)->first();
        } else {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $email,
                'device_id' => $device_id,
                'device_type' => $device_type,
                'provider' => $provider,
                'provider_id' => $provider_id,
                'role_id' => (int)$request->is_trainer == 1 ? 3 : 0,
                //                'avatar' =>  $dirPath . $filename,
            ]);
        }
        if ($user) {
            Auth::login($user);
            $user = Auth::user();
            $data['token'] = $user->createToken('switchFit')->accessToken;
            $user->update([
                'api_token' => $data['token'],
                
                
            ]);
            $data['user'] = Auth::user();
            $this->setResponse('user has been logged in.', 1, 200, $data);
        }
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description Logout
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    public function logout(Request $request)
    {

       if($request->user('api')){
         
        $user =   $request->user('api');
        $user->api_token = '';
        $user->device_id = '';
        $user->save();
        $this->setResponse('user logout.', 1, 200, []);
        return response($this->response, $this->status);         
    }
    $this->setResponse('user not loged in.', 1, 422, []);
    return response($this->response, $this->status);

}

    /**
     * @Description Forgot password sending email with verification code
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @Author Khuram Qadeer.
     */
    /**
     * @Description Forgot password Reset email sending
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author Khuram Qadeer.
     */
    public function sendEmailResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        $user = User::getByEmail($request->email);
        $validator->after(function ($validator) use ($user) {
            if (!$user) {
                $validator->errors()->add('email', 'Email Does not exists.');
            }
        });

        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        $token = Str::random(40) . 'Ti_' . time();
        PasswordReset::whereEmail($user->email)->delete();
        PasswordReset::create([
            'email' => $user->email,
            'token' => $token
        ]);

        $data['token'] = $token;
        $data['user'] = $user;
        sendEmail('emails.reset_password', 'Reset Password', $user->email, $user->first_name, $data);

        $this->setResponse('A password recovery email has been sent to given email address.', 1, 200, []);

        return response()->json($this->response, $this->status);
    }

    /**
     * @Description update user password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);
        $user = User::find((int)$request->user_id);
        $password = $request->current_password;
        $validator->after(function ($validator) use ($user, $password) {
            if (!Hash::check($password, $user->password)) {
                $validator->errors()->add('password', 'current password is wrong.');
            }
        });
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        User::find((int)$request->user_id)->update([
            'password' => bcrypt($request->new_password)
        ]);
        $this->setResponse('password has been changed.', 1, 200, []);
        return response()->json($this->response, $this->status);
    }

    public function updateNotifications(Request $request){

       $validator = \Validator::make($request->all(), [
        'user_id' => 'required',
        'notification' => 'required'

    ]);

       if ($validator->fails()) {
        $this->setResponse($validator->errors()->first(), 0, 422, []);
        return response()->json($this->response, $this->status);
    }
    $user = User::with('categories','reviews')->find($request->user_id);
    $user->notifications = $request->notification;
    $user->save();
    $availibities = Day::with(['availability'=> function ($query)  use ($user) {
        $query->whereuser_id($user->id);

    }])->get();
    $ratings = $user->reviews->sum('stars');
    $ratings = ($ratings>0)?$ratings/$user->reviews->count():0;
    $data['user'] = $user;
    $data['user']['average_rating'] = $ratings;
    $data['user']['total_reviews'] = $user->reviews->count();
    $data['user']['availibities'] = $availibities;
    
    $this->setResponse('Notification settings are saved', 1, 200, $user);
    return response()->json($this->response, $this->status);
}

}
