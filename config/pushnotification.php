<?php
/**
 * @see https://github.com/Edujugon/PushNotification
 */

return [
    'gcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'My_ApiKey',
    ],
    'fcm' => [
        'priority' => 'normal',
        'dry_run' => true,
        'apiKey' => 'AAAAdU0AncI:APA91bEQG9BtRAWzs5oNxsopywAm5K03pGM9hn4FdaQ8I4LnO-ETGbKXH3kfnu23Qz5iAFSd-nj_52Szc35uw2Lu3GsRXHRHGDX9K2iuR99cku6jZ56M1kq6_cyZkfhpBpQvI2wAUb8z',
    ],
    'apn' => [
    'certificate' => __DIR__ . '/iosCertificates/pushcert_live.pem',
//      'certificate' => __DIR__ . '/iosCertificates/pushcert_live.pem',
     //'passPhrase' => '', //Optional
//      'passFile' => __DIR__ . '/iosCertificates/pushcert_live.pem', //Optional
    'dry_run' => false,
  ]
  // 'apn' => [
  //     'certificate' => __DIR__ . '/iosCertificates/apns-dev-cert.pem',
  //     'passPhrase' => '1234', //Optional
  //     'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
  //     'dry_run' => true
  // ]
];
