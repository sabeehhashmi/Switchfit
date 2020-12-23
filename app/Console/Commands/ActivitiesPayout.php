<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\TrainerBooking;
use Carbon\Carbon;
use App\BankAccount;

class ActivitiesPayout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:payout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will be make payouts for activities';

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

        $active_bookings = TrainerBooking::where('payout',0)->where('accepted',1)->whereDate('booking_date', '<', Carbon::now())->get();
        $faciliated_user = [];
        if(!empty($active_bookings->first())){
            foreach($active_bookings as $active_booking){
                if(in_array($active_booking->owner_id, $faciliated_user)){
                    continue;
                }
                else{
                    $trainer_booking = TrainerBooking::where('owner_id',$active_booking->owner_id)->where('payout',0)->where('accepted',1)->whereDate('booking_date', '<', Carbon::now())->get();
                    $total_amount = $trainer_booking->sum('receiveable');

                /*$bank_account = BankAccount::where('user_id',$active_booking->owner_id)->first();
                if($bank_account){
                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $transaction = \Stripe\Transfer::create([
                        "amount" => round(($total_amount) * 100),
                        "currency" => "gbp",
                        "destination" => $bank_account->stripe_account,
                        "transfer_group" => $active_booking->owner_id,
                    ]);*/

                    $trainer_booking = TrainerBooking::where('owner_id',$active_booking->owner_id)->where('payout',0)->where('accepted',1)->whereDate('booking_date', '<', Carbon::now())->update(['payout' => 1]);
                    $faciliated_user[] = $active_booking->owner_id;
                    /*}*/
                }
            }
        }
        $this->info('Payouts are done successfully');
    }
}
