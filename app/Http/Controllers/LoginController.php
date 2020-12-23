<?php

namespace App\Http\Controllers;

use App\PasswordReset;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class LoginController extends Controller
{

    /**
     * @Description Super Admin and gym owner login
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author Khuram Qadeer.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::getByEmail($request->email);
        $password = $request->password;
        $validator->after(function ($validator) use ($password, $user) {
            if (!$user) {
//                Session::flash('alert-danger', 'Email Does not exists.');
                $validator->errors()->add('email', 'Email Does not exists.');
            } else if ($user) {
                if ($user->role_id != 1 && $user->role_id != 2) {
//                    Session::flash('alert-danger', 'You can only login on mobile application.');
                    $validator->errors()->add('email', 'Sorry, You can only login on mobile application.');
                }
            }
            if ($user && $password) {
                if (!Hash::check($password, $user->password)) {
//                    Session::flash('alert-danger', 'Incorrect Password.');
                    $validator->errors()->add('password', 'Incorrect Password.');
                }
            }
        });

        if ($validator->fails()) {
//            Session::flash('alert-danger',  $validator->errors()->first());
            return redirect('login')
                ->withErrors($validator)
                ->withInput();
        }

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')],$request->get('remember'))) {
            Auth::user();
            return redirect(route('dashboard'));
        }
        return redirect(route('login'));
    }

    /**
     * @Description Page for send email for reset password
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function showResetPassword()
    {
        return view('auth.passwords.send_email_forgot');
    }

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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($user->role_id == 1 || $user->role_id == 2) {
            $token = Str::random(40) . 'Ti_' . time();
            PasswordReset::whereEmail($user->email)->delete();
            PasswordReset::create([
                'email' => $user->email,
                'token' => $token
            ]);

            $data['token'] = $token;
            $data['user'] = $user;
            sendEmail('emails.reset_password', 'Reset Password', $user->email, $user->name, $data);
        }
        Session::flash('alert-success', 'A password recovery email has been sent to given email address.');
        return redirect('/login');
    }

    /**
     * @Description Confirm password and new password page
     * @param $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function confirmPassword($token)
    {
        return view('auth.passwords.new_confirm', compact('token'));
    }

    /**
     * @Description update Password
     * @param Request $request
     * @param $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author Khuram Qadeer.
     */
    public function updatePassword(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $email = PasswordReset::whereToken($token)->first()['email'];
        $user = User::whereEmail($email)->first();
        if ($user) {
            PasswordReset::where('email', $user->email)->delete();
            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        Session::flash('alert-success', 'Password has been updated.');
        return redirect(route('login'));
    }

}
