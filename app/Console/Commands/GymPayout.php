<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PassOrderItems;
use Carbon\Carbon;
use App\BankAccount;
use App\User;
use App\Notification;

class GymPayout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gym:payout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will make payouts for the gym';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $upcomings = PassOrderItems::where('book_date', '<', Carbon::now())->where('payout_status','pending')->get();

        $faciliated_user = [];

        if($upcomings->first()){
            foreach($upcomings as $upcoming){

                if(in_array($upcoming->gym_owner_id, $faciliated_user)){
                    continue;
                }else{

                    $owner_payments = PassOrderItems::where('gym_owner_id',$upcoming->gym_owner_id)->where('payout_status','pending')->get();

                    $total_amount = $owner_payments->sum('gym_owner_amount');

                    $bank_account = BankAccount::where('user_id',$upcoming->gym_owner_id)->first();
                    /*if($bank_account){
                        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                        $transaction = \Stripe\Transfer::create([
                            "amount" => round(($amount) * 100),
                            "currency" => "gbp",
                            "destination" => $accountId,
                            "transfer_group" => $upcoming->gym_owner_id,
                        ]);*/

                        $owner_payments = PassOrderItems::where('gym_owner_id',$upcoming->gym_owner_id)->where('payout_status','pending')->update(['payout_status' => 'payed']);
                        $user = User::find($upcoming->gym_owner_id);
                        if($user){
                            $data['user'] = $user;
                            $notification = new Notification();
                            $notification->screen = 'gym_payouts';
                            $notification->user_id = $user->id;
                            $notification->source_id = $user->id;            
                            $notification->description = 'New payment received';

                            $notification->source_image = ltrim($user->avatar, '/');
                            $notification->save();
                        }

                        sendEmail('emails.payout-send', 'Pass Purchase Notification', $user->email, $user->first_name, $data);
                        $faciliated_user[] = $upcoming->gym_owner_id;
                        /*}*/
                    }



                }

            }

            $this->info('Gym payouts are done successfully');
        }
    }
