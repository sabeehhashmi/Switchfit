<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\PassOrderItems;
use Carbon\Carbon;
use App\BankAccount;

class SendBeforeExpireNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:before:expire:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will the notification to user before expiring the pass';

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
        $passorders = PassOrderItems::where('last_valid_date', '<', Carbon::now()->addDays(6))->where('is_used', 0)->where('is_expire', 0)->where('notification_sent',0)->get();


        if(!empty($passorders->first())){
            foreach($passorders as $passorder){

                if($passorder->allow_visits > $passorder->user_visits){
                    $user = User::find($passorder->buyer_id);
                    $data['user'] = $user;
                    sendEmail('emails.expiry_send', 'Pass Expiry Notification', $user->email, $user->first_name, $data);
                    $passorder->notification_sent=1; 
                    $passorder->save(); 
                }
                
            }
            $this->info('Notification sent');

        }

        
    }
}
