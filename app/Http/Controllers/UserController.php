<?php

namespace App\Http\Controllers;

use App\User;
use App\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Notification;
use App\Http\Controllers\Hash;

class UserController extends Controller
{
    /**
     * @Description Get user list by user role type
     * @param null $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function list($type = null)
    {
        if ($type == 'trainers') {
            $users = User::where('role_id', 3)->orderByDesc('id')->get();
        } else if ($type == 'normal_users') {
            $users = User::where('role_id', 0)->orderByDesc('id')->get();
        } else {
            $users = User::where('role_id', '!=', 1)->where('role_id', '!=', 2)->orderByDesc('id')->get();
        }

        return view('users.list', compact('users'));
    }

    /**
     * @Description Show Profile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function showProfile()
    {
        $gymOwner = User::find(Auth::id());
        return view('users.profiles.gym_owner',compact('gymOwner'));
    }
    /**
     * @Description Show Profile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function showSettings()
    {
        $user = User::find(Auth::id());
        $page = Page::where('slug','terms-and-conditions')->first();
        return view('users.profiles.admin-settings',compact('user','page'));
    }
     /**
     * @Description Show Profile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
     public function updateSettings(Request $request)
     {
        $validator = Validator::make($request->all(), [

            'terms_and_conditions'=>'required',

        ]
    );

        if($request->password){
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'password' => 'required|confirmed|min:6',

            ]
        );

        }
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        if($request->password){
            $user = User::find(Auth::id());

            $check = \Hash::check($request->old_password, $user->password);
            if($check != true){
             Session::flash('alert-danger','Wrong current password.');
             return redirect()->back();
         }

         $user->password = bcrypt($request->password);
         $user->save();
     }

     $page = Page::where('slug','terms-and-conditions')->first();
     $page->content = $request->terms_and_conditions;
     $page->save();
     Session::flash('alert-success','Settings Updated.');
     return redirect()->back();
 }

    /**
     * @Description update gym owner
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @Author Khuram Qadeer.
     */
    public function updateProfile(Request $request)
    {
        $id = (int)$request->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users,id,' . $id,
            'address' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'manager_name' => 'required',
            'phone' => 'required',
        ], [
            'lat.required' => 'Please, Select Location From Map',
            'lng.required' => 'Please, Select Location From Map',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        $fileUrl = User::find($id)['logo'];
        $file = $request->file('logo');

        if ($file) {
            deleteFile($fileUrl);
            $filename = str_replace(' ', '_', Str::random(10) . $file->getClientOriginalName());
            //Move Uploaded File
            $dirPath = 'assets/uploads/logos/';
            $fileUrl = $dirPath . $filename;
            $file->move($dirPath, $filename);
        }

        User::find($id)->update([
            'role_id' => 2,
            'first_name' => $request->name,
            'address' => $request->address,
            'lat' => (double)$request->lat ?? null,
            'lng' => (double)$request->lng ?? null,
            'manager_name' => $request->manager_name,
            'phone' => $request->phone,
            'logo' => $fileUrl ?? null,
//            'terms_conditions' => $request->terms_conditions == 'on' ? 1 : 0,
        ]);

        Session::flash('alert-success', 'Profile has been updated.');
        return redirect()->back();
    }
    public function clearNotifocations(Request $request){
        if(isSuperAdmin()){
            $user_id=0;
        }else{
            $user_id=Auth::id();
        }


        $data = Notification::where('user_id',$user_id)->update(['is_read' => 1]);

        Session::flash('alert-success', 'all notifications cleared.');
        return redirect()->back();

    }

    public function deleteNotifocations($id){

        $data = Notification::find($id);

        if($data){
          $data->delete();
      }

      Session::flash('alert-success', 'notifications deleted.');
      return redirect()->back();

  }
  public function enableDisableUser($id){

    $user = User::find($id);
    if($user){
        if($user->is_disabled == 1){
            $user->is_disabled = 0;

            Session::flash('alert-success', 'User Enabled.');
        }
        else{
         $user->is_disabled = 1;
         Session::flash('alert-danger', 'User Disabled.');
     }
     $user->save();
 }
 else{
    Session::flash('alert-danger', 'user does\'t exist.');
}
return redirect()->back();

}
public function termsAndConditions(){

    $page = Page::where('slug','terms-and-conditions')->first();

    return view('terms-conditions',compact('page'));

}
}
