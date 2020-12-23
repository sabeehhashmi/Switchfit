<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Gym;
use App\TrainerActivity;
use Illuminate\Support\Facades\Auth;
use App\PassOrderItems;
use App\Review;
use DB;
class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {


        if(isSuperAdmin()){
            $users = User::where('role_id',0)->get();
            $trainers = User::where('role_id',3)->where('is_deleted',0)->orderByDesc('id')->get();
            $gyms = Gym::all();
            $activities = TrainerActivity::all();
            $gymOwners = User::where('role_id', 2)->orderBy('id', 'DESC')->get();
            return view('dashboard',compact('users','trainers','gyms','activities','gymOwners'));
        }
        $id = Auth::id();
        $gyms = Gym::where('user_id',$id)->get();
        $upcoming = PassOrderItems::where('gym_owner_id',$id)->where('payout_status','pending')->get();
        $upcoming = $upcoming->sum('gym_owner_amount');
        $members = count(Gym::getActiveOwnerMembers($id));
        $bookings =   PassOrderItems::where('gym_owner_id',$id)->groupBy('gym_id', 'book_date')->select(DB::raw('book_date'),DB::raw('gym_id'), DB::raw('count(pass_id) as total_passes'),DB::raw('sum(gym_owner_amount) as sub_total'),DB::raw('sum(price) as price'),DB::raw('sum(switch_fit_fee) as switch_fit_fee'),DB::raw('payout_status'))
        ->orderby('book_date','DESC')
        ->with('gym')
        ->get();
        $reviews = Review::where('given_to',$id)->with('user','gym')->orderBy('id', 'DESC')->get();
        $total_price =  $bookings->sum('sub_total');
        return view('dashboard',compact('gyms','upcoming','members','bookings','total_price','id','reviews'));

    }
}
