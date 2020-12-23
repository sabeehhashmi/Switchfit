<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notification;
use App\Http\Controllers\POSTrait;
use App\User;


class SendNotifications extends Command
{
    use POSTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send notifications to users';

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
        $notifications = Notification::where('sent',0)->get();
        if($notifications->first()){
            foreach($notifications as $notification){

                $user = User::find($notification->user_id);

                if($user){

                    $this->extraPayLoad['screen'] = $notification->screen;

                    $this->extraPayLoad['source_id'] = $notification->source_id;
                    $this->extraPayLoad['notificationTitle'] = $notification->title;

                    $this->extraPayLoad['description'] =  $notification->description;
                    $this->extraPayLoad['source_image'] = $notification->source_image;

                    $this->notificationTitle = $notification->title;
                    $this->notificationMessage = $notification->description;
                    $this->deviceType = $user->device_type;
                    $this->deviceTokens = [$user->device_id];

                    $this->sendNotification();
                    $notification->sent = 1;
                    $notification->save();
                }
                else{

                    $notification->sent = 1;
                    $notification->save();
                }

            }
        }
    }
}
