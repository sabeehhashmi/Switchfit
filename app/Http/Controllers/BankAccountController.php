<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\Traits\ApiResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stripe\File;
use Stripe\Stripe;

class BankAccountController extends Controller
{
    use ApiResponse;

    /**
     * @Description Create Bank Account for payout
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @Author Khuram Qadeer.
     */
    public function create()
    {
        $account = BankAccount::where('user_id', Auth::id())->first();
        return view('banks.create_update', compact('account'));
    }

    /**
     * @Description Store and update bank account
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @Author Khuram Qadeer.
     */
    public function storeOrUpdate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'type' => 'required',
                'routing_number' => 'required',
                'social_security_number' => 'required',
                'account_number' => 'required',
                'retype_account_number' => 'required|same:account_number',
                'dob' => 'required',
                'address' => 'required',
            ]);

            if (!(int)$request->id) {
                $validator = Validator::make($request->all(), [
                    'front' => 'required|image|mimes:jpeg,png,jpg|max:10240',
                    'back' => 'required|image|mimes:jpeg,png,jpg|max:10240',
                ]);
            }

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
            $ssn = $request->social_security_number;
            $address = $request->address;
            $dobDay = explode('/', $request->dob)[1];
            $dobMonth = explode('/', $request->dob)[0];
            $dobYear = explode('/', $request->dob)[2];
            $user = Auth::user();
            $accountId = (int)$request->id ?? null;
            $myAccount = $accountId ? BankAccount::find($accountId) : null;
            $myStripeAccount = $myStripeBankToken = $myStripeBankAccount = null;

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
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
                    "email" => $user->email,
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
                            'line1' => $user->address,
                            'postal_code' => 'SW1W 0NY',
                        ],
                        'dob' => [
                            "day" => $dobDay,
                            "month" => $dobMonth,
                            "year" => $dobYear,
                        ],
                        "email" => $user->email,
                        "first_name" => $user->first_name ?? 'user',
                        "last_name" => $user->last_name ?? 'user',
                        "gender" => $user->gender,
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
            $bank_account = BankAccount::find($accountId);
            if(empty($bank_account)){

                $bank_account = new BankAccount;

            }

            $bank_account->user_id = Auth::id();

            $bank_account->account_name = $accountHolderName;
            $bank_account->account_type = $accountType;
            $bank_account->routing_number = $routingNumber;
            $bank_account->social_security_number = $ssn;
            $bank_account->account_number = $accountNumber;
            $bank_account->dob = $request->dob;
            $bank_account->address = $address;
            $bank_account->stripe_account = $myStripeAccount ?? null;
            $bank_account->stripe_bank_token = $myStripeBankToken ?? null;
            $bank_account->stripe_bank_account = $myStripeBankAccount ?? null;
            $bank_account->save();
            $msg = 'Account has been saved.';

            
            Session::flash('alert-success', $msg);
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
        $this->setResponse('success', 1, 200, []);
        return response()->json($this->response, $this->status);
//        return redirect(route('bank_account.create'));
    }
}
