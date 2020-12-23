<?php

namespace App\Http\Controllers;

use App\Pass;
use App\PassOrderItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Notification;

class ManageVisitController extends Controller
{

    /**
     * @Description Manage gym visits listing page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function list($token = null)
    {
        if(isset($_GET['notification_id'])){
            $notification = Notification::find($_GET['notification_id']);
            if($notification){

                $notification->delete();
            }
        }

        $msg = 'Please, Find Your Pass ID';
        if ($token) {
            $pass = null;
            $passToken = strtolower($token);
            $msg = "No Pass Available ".'"'.strtoupper($token).'"';
            if (PassOrderItems::where([['gym_owner_id', Auth::id()], ['pass_token', $passToken]])->exists()) {
                $pass = Pass::getUserPassDetailByToken($passToken);
            }
            return view('gyms.manage_visits', compact('pass','msg'));
        }
        return view('gyms.manage_visits',compact('msg'));
    }

    /**
     * @Description Add visit against pass
     * @param $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function addVisit($token)
    {
        $passToken = strtolower($token);
        $pass = \App\PassOrderItems::where('pass_token', $passToken)->first();
        if ($pass) {
            if ($pass->is_used == 0 && $pass->is_expire == 0) {
                $currentVisit = ((int)\App\ManageVisit::where([['order_item_id', $pass->id], ['pass_token', $passToken]])->count()) + 1;
                \App\ManageVisit::create([
                    'order_item_id' => $pass->id,
                    'pass_token' => $passToken,
                    'time' => date('H:i'),
                    'current_visit_no' => $currentVisit
                ]);
                $pass->update([
                    'user_visits' => $currentVisit
                ]);

                // if visits completed
                if ($pass->allow_visits == $pass->user_visits) {
                    $pass->update([
                        'is_used' => 1
                    ]);
                }
                Session::flash('alert-success', 'Visit has been added.');
            }
        }
        return redirect('/manage/visits/list/'.$token);
    }

}
