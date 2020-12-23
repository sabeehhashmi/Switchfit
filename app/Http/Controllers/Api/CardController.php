<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\File;
use Stripe\Stripe;
use App\User;
use App\BankAccount;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    use ApiResponse;

    /**
     * @Description save Card against user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function saveCard(Request $request)
    {
        $userId = $request->user()->id;
        /*|unique:cards*/
        $validator = \Validator::make($request->all(), [
            'card_name' => 'required',
            'card_number' => 'required',
            'expire_month' => 'required',
            'expire_year' => 'required',
            'cvc' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $stripeToken = getStripeToken($request->card_number, $request->expire_month, $request->expire_year, $request->cvc);
        if (isset($stripeToken['error'])) {
            $this->setResponse((string)$stripeToken['error'], 0, 422, []);
            return response()->json($this->response, $this->status);
        }

        Card::create([
            'user_id' => $userId,
            'card_name' => $request->card_name,
            'card_number' => $request->card_number,
            'expire_month' => $request->expire_month,
            'expire_year' => $request->expire_year,
            'cvc' => $request->cvc,
            'type' => getCreditCardType($request->card_number)
        ]);
        $data['cards'] = Card::getAllByUserId($userId);
        $this->setResponse('Card has been stored.', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description delete Card
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function deleteCard(Request $request)
    {
        $userId = $request->user()->id;
        $validator = \Validator::make($request->all(), [
            'card_id' => 'required',
        ]);
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        Card::find((int)$request->card_id)->delete();
        $data['cards'] = Card::getAllByUserId($userId);
        $this->setResponse('Card has been deleted.', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description Get All Cards
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function getCards(Request $request)
    {
        $userId = $request->user()->id;
        $data['cards'] = Card::getAllByUserId($userId);
        $this->setResponse('success.', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }

    public function storeOrUpdateBank(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'trainer_id' => 'required',
                'type' => 'required',
                'routing_number' => 'required',
                'account_number' => 'required',
                'name' => 'required'
            ],
            [
                'name.required' => 'Please, provide name on the card.',


            ]);

            if (!(int)$request->bank_id && !$validator->fails()) {
                $validator = Validator::make($request->all(), [
                    'front' => 'required|image|mimes:jpeg,png,jpg|max:10240',
                    'back' => 'required|image|mimes:jpeg,png,jpg|max:10240',
                ],
                [
                    'front.required' => 'Please, upload the front side of your id card.',
                    'back.required' => 'Please, upload the back side of your id card.'

                ]);
            }

            

            $trainer = User::find($request->trainer_id);

            if ($validator->fails()) {
                $this->setResponse($validator->errors()->first(), 0, 422, []);
                return response()->json($this->response, $this->status);
//                return redirect()->back()
//                    ->withErrors($validator)
//                    ->withInput();
            }
            $accountHolderName = $request->name;
            $accountType = $request->type;
            $routingNumber = $request->routing_number;
            $accountNumber = $request->account_number;
            $address = $trainer->address;
            $date_of_birth  = ($trainer->date_of_birth)?explode('-',$trainer->date_of_birth):'';
            $dobDay = ($date_of_birth)?$date_of_birth[2]:'';
            $dobMonth = ($date_of_birth)?$date_of_birth[1]:'';
            $dobYear = ($date_of_birth)?$date_of_birth[0]:'';
            $accountId = (int)$request->bank_id ?? null;
            $myAccount = $accountId ? BankAccount::find($accountId) : null;
            $myStripeAccount = $myStripeBankToken = $myStripeBankAccount = null;
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            /*\Stripe\Stripe::setApiKey('sk_test_8YZYr3uSfijns8qMAXwC5pN700kMHDnoQD');*/
            if (!isset($myAccount->stripe_account) || !$myAccount->stripe_account) {

                // create stripe account
                $stripeAccount = \Stripe\Account::create([
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
//                        'legacy_payments' => ['requested' => true],
                        'transfers' => ['requested' => true],
                    ],
                    "type" => "custom",
                    "country" => 'gb',
                    "email" => $trainer->email,
                    "business_type" => 'individual',
                    'business_profile' => [
                        "url" => "https://wantechsolutions.com",
                        'mcc' => '8050',
                    ],
                    "individual" => [
                        'address' => [
                            'city' => 'alabaster',
                            'state' => 'AL',
                            'country' => 'gb',
                            'line1' => $trainer->address,
                            'postal_code' => 'SW1W 0NY',
                        ],
                        'dob' => [
                            "day" => $dobDay,
                            "month" => $dobMonth,
                            "year" => $dobYear,
                        ],
                        "email" => $trainer->email,
                        "first_name" => $trainer->first_name ?? 'user',
                        "last_name" => $trainer->last_name ?? 'user',
                        "gender" => $trainer->gender,
                        "phone" => '+' . '14109607860',
//                        "ssn_last_4" => substr($ssn, -4),
                    ],
                ]);

                // create bank account token
                $bankToken = \Stripe\Token::create([
                    'bank_account' => [
                        'country' => 'gb',
                        'currency' => 'gbp',
                        'account_holder_name' => $accountHolderName,
                        'account_holder_type' => $accountType,
                        'routing_number' => $routingNumber,
                        'account_number' => $accountNumber,
                    ],
                ]);

                // third link the bank account with the stripe account
                $bankAccount = \Stripe\Account::createExternalAccount(
                    $stripeAccount->id, ['external_account' => $bankToken->id]
                );

                $frontFile = \Stripe\File::create([
                    'purpose' => 'identity_document',
                    'file' => fopen($request->front->getPathname(), 'r')
                ], [
                    'stripe_account' => $stripeAccount->id,
                ]);

                $backFile = \Stripe\File::create([
                    'purpose' => 'identity_document',
                    'file' => fopen($request->back->getPathname(), 'r')
                ], [
                    'stripe_account' => $stripeAccount->id,
                ]);
                $additionalFrontFile = \Stripe\File::create([
                    'purpose' => 'additional_verification',
                    'file' => fopen($request->front->getPathname(), 'r')
                ], [
                    'stripe_account' => $stripeAccount->id,
                ]);

                $additionalBackFile = \Stripe\File::create([
                    'purpose' => 'additional_verification',
                    'file' => fopen($request->back->getPathname(), 'r')
                ], [
                    'stripe_account' => $stripeAccount->id,
                ]);

                // Fourth stripe account update for tos acceptance
                \Stripe\Account::update(
                    $stripeAccount->id, [
                        'individual' => [
                            "verification" => [
                                'document' => [
                                    'back' => $backFile,
                                    'front' => $frontFile,
                                ],
                                'additional_document' => [
                                    'back' => $additionalBackFile,
                                    'front' => $additionalFrontFile,
                                ]
                            ]
                        ],
                        'tos_acceptance' => [
                            'date' => time(),
                            'ip' => $_SERVER['REMOTE_ADDR'], // Assumes you're not using a proxy
                        ],
                    ]
                );
                $myStripeAccount = $stripeAccount->id;
                $myStripeBankToken = $bankToken->id;
                $myStripeBankAccount = $bankAccount->id;

            } else {
                $stripeAccount = $myAccount->stripe_account;

                $bankToken = \Stripe\Token::create([
                    'bank_account' => [
                        'country' => 'gb',
                        'currency' => 'gbp',
                        'account_holder_name' => $accountHolderName,
                        'account_holder_type' => $accountType,
                        'routing_number' => $routingNumber,
                        'account_number' => $accountNumber,
                    ],
                ]);

                // third link the bank account with the stripe account
                $bankAccount = \Stripe\Account::createExternalAccount(
                    $stripeAccount, ['external_account' => $bankToken->id]
                );

                // Fourth stripe account update for tos acceptance
                \Stripe\Account::update(
                    $stripeAccount, [
                        'tos_acceptance' => [
                            'date' => time(),
                            'ip' => $_SERVER['REMOTE_ADDR'], // Assumes you're not using a proxy
                        ],
                    ]
                );

                $myStripeAccount = $stripeAccount;
                $myStripeBankToken = $bankToken->id;
                $myStripeBankAccount = $bankAccount->id;
            }

            $fileUrl = '';
            $file = $request->file('front');
            if ($file) {
                $filename = str_replace(' ', '_', Str::random(10) . $file->getClientOriginalName());
                $dirPath = 'assets/uploads/trainers/';
                $fileUrl = $dirPath . $filename;
                $file->move($dirPath, $filename);
            }

            $fileUrlback = '';
            $file = $request->file('back');
            if ($file) {
                $filename = str_replace(' ', '_', Str::random(10) . $file->getClientOriginalName());
                $dirPath = 'assets/uploads/trainers/';
                $fileUrlback = $dirPath . $filename;
                $file->move($dirPath, $filename);
            }
            $accountId = BankAccount::where('user_id',$request->trainer_id)->first();
            $accountId = ($accountId)?$accountId->id:'';
            if ($accountId) {
                $bank_account = BankAccount::find($accountId);

            } else {
                $bank_account = new BankAccount();
                
            }

            $bank_account->user_id = $request->trainer_id;
            $bank_account->account_name = $accountHolderName;
            $bank_account->account_type = $accountType;
            $bank_account->routing_number = $routingNumber;
            $bank_account->account_number = $request->account_number;
            $bank_account->dob = $trainer->date_of_birth;
            $bank_account->address = $address;

            if($myStripeAccount){
                $bank_account->stripe_account = $myStripeAccount;
            }
            if($myStripeBankToken){
                $bank_account->stripe_bank_token = $myStripeBankToken;
            }
            if($myStripeBankAccount){
                $bank_account->stripe_bank_account = $myStripeBankAccount;
            }
            if($fileUrl){
              $bank_account->front_file = $fileUrl;  
          }
          if($fileUrlback){
              $bank_account->back_file = $fileUrlback;  
          }

          $bank_account->save();
          $msg = 'Account information has been saved.';

      } catch (\Stripe\Error\Card $e) {

            // Since it's a decline, \Stripe\Error\Card will be caught
//            Session::flash('alert-danger', $e->getJsonBody()['error']['message']);
        $this->setResponse($e->getJsonBody()['error']['message'], 0, 422, []);
        return response()->json($this->response, $this->status);
    } catch (\Stripe\Error\RateLimit $e) {
//            Session::flash('alert-danger', "To many requests");
        $this->setResponse($e->getJsonBody()['error']['message'], 0, 422, []);
        return response()->json($this->response, $this->status);
    } catch (\Stripe\Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
//            Session::flash('alert-danger', $e->getJsonBody()['error']['message']);
        $this->setResponse($e->getJsonBody()['error']['message'], 0, 422, []);
        return response()->json($this->response, $this->status);
    } catch (\Stripe\Error\Authentication $e) {
            // Authentication with Stripe's API failed
        $this->setResponse($e->getJsonBody()['error']['message'], 0, 422, []);
        return response()->json($this->response, $this->status);
//            Session::flash('alert-danger', 'Invalid auth');
    } catch (\Stripe\Error\ApiConnection $e) {
            // Network communication with Stripe failed
        $this->setResponse($e->getJsonBody()['error']['message'], 0, 422, []);
        return response()->json($this->response, $this->status);
//            Session::flash('alert-danger', "Error in network communication");
    } catch (\Stripe\Error\Base $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
        $this->setResponse($e->getJsonBody()['error']['message'], 0, 422, []);
        return response()->json($this->response, $this->status);
//            Session::flash('alert-danger', $e->getJsonBody()['error']['message']);
    } catch (\Stripe\Exception\InvalidRequestException $e) {
        $this->setResponse($e->getJsonBody()['error']['message'], 0, 422, []);
        return response()->json($this->response, $this->status);
//            Session::flash('alert-danger', $e->getJsonBody()['error']['message']);
    } catch (Exception $e) {

        $this->setResponse($e->getJsonBody()['error']['message'], 0, 422, []);
        return response()->json($this->response, $this->status);
            // Something else happened, completely unrelated to Stripe
//            Session::flash('alert-danger', $e->getJsonBody()['error']['message']);
    }
    $this->setResponse('success', 1, 200, $bank_account);
    return response()->json($this->response, $this->status);
//        return redirect(route('bank_account.create'));
}

public function getBankInfo(Request $request){
    $validator = Validator::make($request->all(), [
        'trainer_id' => 'required',

    ]);
    $bank_data = BankAccount::where('user_id',$request->trainer_id)->first();
    $this->setResponse('success', 1, 200, $bank_data);
    return response()->json($this->response, $this->status);
}
}
