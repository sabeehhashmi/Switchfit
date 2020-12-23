<?php

namespace App\Http\Controllers;


use Edujugon\PushNotification\PushNotification;

trait POSTrait
{
    protected $success = false;
    protected $message = '';
    protected $arrCompanies = [];
    protected $arrCommunities = [];
    protected $data = [];
    protected $params = [];
    protected $requestData = [];
    protected $batchId = '';
    protected $prefixMessage = '';
    protected $viewOnly = false;
    protected $isCompanyRole = false;
    protected $isCompanyUserRole = false;
    protected $isCompanyOrUserRole = false;
    protected $isApi = false;
    private $userId = 0;
    private $isAdminRole = false;
    protected $deviceTokens = [];
    protected $deviceType = '';
    protected $notificationTitle = '';
    protected $notificationMessage = '';
    protected $extraPayLoad = [];
    protected $logMessage = '';
    protected $badge = 0;

    /**
     * This is used to send push notification message
     */
    public function sendNotification()
    {

        if (!\App::isLocal()) {

            if (!empty(array_filter($this->deviceTokens))) {

                if ($this->deviceType == 'ios') {

                    $push = new PushNotification('apn');
                    $response = $push->setMessage([
                        'aps' => [
                            'alert' => [
                                'title' => $this->notificationTitle,
                                'body' => $this->notificationMessage
                            ],
                            'sound' => 'default',
                            'badge' => (int) $this->badge
                        ],
                        'data' => $this->extraPayLoad
                    ])
                    ->setDevicesToken($this->deviceTokens)
                    ->send();
                   
                    if(isset($push->feedback->error)){
                      return $push->feedback->error;
                  }

                   //dd('push notification sent with out error');
              } else {
                //dd($this->notificationTitle);
                $push = new PushNotification('fcm');
                $response = $push->setMessage([
                    'notification' => [
                        'title' => $this->notificationTitle,
                        'body' => $this->notificationMessage,
                        'sound' => 'default'
                    ],
                    'data' => $this->extraPayLoad
                ])
                ->setApiKey('AAAAdU0AncI:APA91bH5H6ZEsoKVAoe2a9xEwdZVzse-sypNuOHX0tCzMUjYTl06QUSkQP_SyrHgeQz7FOnbll4IkkBJv1LQ1QPKjftNtRtJMVGOHcnhxU_2p-gJKQNra7EguzPG30xmQ4-1sgYmyEQN')
                ->setConfig(['dry_run' => false,'priority' => 'high'])
                ->setDevicesToken($this->deviceTokens)
                ->send();
                //dd();
                if(isset($push->feedback->results[0]->error)){
                     return $push->feedback->error;
                      
                  }
            }
            
        }
    }
}

    /**
     * This is used to get user chat messages count
     *
     * @param $userId
     */
    public function getUserChatMessagesCount($data=array())
    {
        $count = 0;
        
        if(isset($data['chat_id'])){

            $sql = \DB::table('chat')->where('id', $data['chat_id'])->count();

            $count = $sql;
        }



        return $count;
    }

    

}