<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\Http\Controllers\Controller;
use App\Pass;
use App\PassOrder;
use App\PassOrderItems;
use App\PassPayment;
use App\Traits\ApiResponse;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Gym;
use App\Notification;

class PassOrderController extends Controller
{
    use ApiResponse;

    /**
     * @Description Buy the passes with already saved card
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Stripe\Exception\ApiErrorException
     * @Author Khuram Qadeer.
     */
    public function buyPasses(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'passes' => 'required',
            'is_saved' => 'required',
        ]);

        if (!isset($request->card_id) || !$request->card_id) {
            $validator = \Validator::make($request->all(), [
                'card_name' => 'required',
                'card_number' => 'required',
                'expire_month' => 'required',
                'expire_year' => 'required',
                'cvc' => 'required',
            ]);
        }
        if ($validator->fails()) {
            $this->setResponse($validator->errors()->first(), 0, 422, []);
            return response()->json($this->response, $this->status);
        }
        $user = $request->user();
        $passes = json_decode($request->passes);
        $order = null;
        $total = 0;
        $msg = 'You have successfully purchased ';
        $pass_owner ='';
        $gymId = '';
        if ($passes) {
            // getting total amount for charge
            foreach ($passes as $pass) {
                $msg .= (string)Pass::whereId((int)$pass->pass_id)->value('title') . ',';
                $total += $pass->total;
                $pass_owner = $pass->gym_owner_id;
                $gymId = $pass->gym_id;
            }
            $msg = rtrim($msg, ', ') . '.';
            $card = [];
            $myCard = '';
            $newCard = false;
            if (!isset($request->card_id) || !$request->card_id) {
                if ((int)$request->is_saved == 1) {
                    if (!Card::where([['user_id', $user->id], ['card_number', $request->card_number]])->exists()) {
                        $newCard = true;
                        $myCard = Card::create([
                            'user_id' => $user->id,
                            'card_name' => $request->card_name,
                            'card_number' => $request->card_number,
                            'expire_month' => $request->expire_month,
                            'expire_year' => $request->expire_year,
                            'cvc' => $request->cvc,
                            'type' => getCreditCardType($request->card_number)
                        ]);
                    }
                }
                $card = [
                    'id' => $myCard ? $myCard->id : null,
                    'card_name' => $request->card_name,
                    'card_number' => $request->card_number,
                    'expire_month' => $request->expire_month,
                    'expire_year' => $request->expire_year,
                    'cvc' => $request->cvc,
                    'stripe_customer_id' => User::getStripeCustomerId($user->id),
                    'stripe_card_id' => null,
                ];
            } else {
                $myCard = Card::find((int)$request->card_id);
                $card = [
                    'id' => $myCard->id,
                    'card_name' => $myCard->card_name,
                    'card_number' => $myCard->card_number,
                    'expire_month' => $myCard->expire_month,
                    'expire_year' => $myCard->expire_year,
                    'cvc' => $myCard->cvc,
                    'stripe_customer_id' => $myCard->stripe_customer_id,
                    'stripe_card_id' => $myCard->stripe_card_id,
                ];
            }

            if ($card) {
                // if user have not customer id and card id against card OR not saved Card transaction
                if ($card['stripe_customer_id'] && $card['stripe_card_id']) {
                    $stripeCardId = $card['stripe_card_id'];
                    $stripeCustomerId = $card['stripe_customer_id'];
                } // if customer id exists and card id does not exists
                elseif ($card['stripe_customer_id'] && !$card['stripe_card_id']) {
                    // getting strip New Token for further use
                    $newStripeToken = getStripeToken($card['card_number'], $card['expire_month'], $card['expire_year'], $card['cvc']);
                    if (isset($newStripeToken['error'])) {
                        if ($newCard) {
                            Card::whereId((int)$card['id'])->delete();
                        }
                        $this->setResponse((string)$newStripeToken['error'], 0, 422, []);
                        return response()->json($this->response, $this->status);
                    }
                    // Saving Card on Stripe
                    $savedStripeCard = saveCardOnStripe($card['stripe_customer_id'], $newStripeToken['token']->id);
                    if (isset($savedStripeCard['error'])) {
                        if ($newCard) {
                            Card::whereId((int)$card['id'])->delete();
                        }
                        $this->setResponse((string)$savedStripeCard['error'], 0, 422, []);
                        return response()->json($this->response, $this->status);
                    }
                    // card id of save card on stripe
                    $stripeCardId = $savedStripeCard['card']->id;
                    // customer id on stripe
                    $stripeCustomerId = $card['stripe_customer_id'];
                } // if new customer with new card
                else {
                    // getting strip token
                    $stripeToken = getStripeToken($card['card_number'], $card['expire_month'], $card['expire_year'], $card['cvc']);
                    if (isset($stripeToken['error'])) {
                        if ($newCard) {
                            Card::whereId((int)$card['id'])->delete();
                        }
                        $this->setResponse((string)$stripeToken['error'], 0, 422, []);
                        return response()->json($this->response, $this->status);
                    }
                    // Create Customer on stripe
                    $stripeCustomer = createStripeCustomer($user->id, User::getFullName($user->id), $user->email, $stripeToken['token']->id);
                    if (isset($stripeCustomer['error'])) {
                        if ($newCard) {
                            Card::whereId((int)$card['id'])->delete();
                        }
                        $this->setResponse((string)$stripeCustomer['error'], 0, 422, []);
                        return response()->json($this->response, $this->status);
                    }
                    // getting strip New Token for further use
                    $newStripeToken = getStripeToken($card['card_number'], $card['expire_month'], $card['expire_year'], $card['cvc']);
                    if (isset($newStripeToken['error'])) {
                        if ($newCard) {
                            Card::whereId((int)$card['id'])->delete();
                        }
                        $this->setResponse((string)$newStripeToken['error'], 0, 422, []);
                        return response()->json($this->response, $this->status);
                    }
                    // Saving Card on Stripe
                    $savedStripeCard = saveCardOnStripe($stripeCustomer['customer']->id, $newStripeToken['token']->id);
                    if (isset($savedStripeCard['error'])) {
                        if ($newCard) {
                            Card::whereId((int)$card['id'])->delete();
                        }
                        $this->setResponse((string)$savedStripeCard['error'], 0, 422, []);
                        return response()->json($this->response, $this->status);
                    }
                    // card id of save card on stripe
                    $stripeCardId = $savedStripeCard['card']->id;
                    // customer id on stripe
                    $stripeCustomerId = $stripeCustomer['customer']->id;
                }

                // charge payment
                $paymentCharge = stripeCharge($stripeCustomerId, $stripeCardId, $total);
                if (isset($paymentCharge['error'])) {
                    if ($newCard) {
                        Card::whereId((int)$card['id'])->delete();
                    }
                    $this->setResponse((string)$paymentCharge['error'], 0, 422, []);
                    return response()->json($this->response, $this->status);
                }

                // saving card
                if ((int)$card['id']) {
                    Card::whereId((int)$card['id'])->update([
                        'stripe_card_id' => $stripeCardId,
                        'stripe_customer_id' => $stripeCustomerId,
                    ]);
                }

                // transaction id
                $transactionId = $paymentCharge['charge'];

                // today date
                $today = Carbon::now();
                // order
                $order = PassOrder::create([
                    'buyer_id' => (int)$user->id,
                    'book_date' => $today->format('Y-m-d'),
                    'total' => (double)$total
                ]);
                foreach ($passes as $pass) {
                    $gym = Gym::find($pass->gym_id);
                    // portal fee and gym owner amount calculating
                    $fee = ($gym)?$gym->percentage:20;
                    $switchFitFee = (double)(($fee / 100) * $pass->total);
                    $gymOwnerAmount = (double)($pass->total - $switchFitFee);
                    //order items/single pass detail
                    $pass_token = strtolower(Str::random(5, 6)) . $user->id;
                    $valid_days = array(30=>1,60=>2);
                    $valid_days = isset($valid_days[$pass->valid_days])?$valid_days[$pass->valid_days]:$pass->valid_days;
                    PassOrderItems::create([
                        'order_id' => $order->id,
                        'buyer_id' => (int)$user->id,
                        'gym_owner_id' => (int)$pass->gym_owner_id,
                        'gym_id' => (int)$pass->gym_id,
                        'pass_id' => (int)$pass->pass_id,
                        'pass_token' => $pass_token,
                        'price' => (double)$pass->price,
                        'qty' => (int)$pass->qty,
                        'valid_days' => (int)$valid_days,
                        'sub_total' => (double)$pass->total,
                        'allow_visits' => (int)$pass->allow_visits,
                        'user_visits' => 0,
                        'book_date' => date('Y-m-d'),
                        'last_valid_date' => $today->addDays((int)$valid_days)->format('Y-m-d'),
                        'is_expire' => 0,
                        'is_used' => 0,
                        'switch_fit_fee' => (double)$switchFitFee,
                        'gym_owner_amount' => (double)$gymOwnerAmount,
                        'payment_status' => 'paid',
                        'payout_status' => 'pending',
                    ]);
                    $gym = Gym::find($gymId);
                    $notification = new Notification();
                    $notification->screen = 'gym_order';
                    $notification->user_id = $pass_owner;
                    $notification->source_id = $gymId;
                    $notification->other_info = $pass_token;
                    $notification->description = 'New Pass is purchased on '.$gym->name;
                    $notification->source_image =  ltrim($gym->image, '/');
                    $notification->save();
                }
                // save payment detail
                PassPayment::create([
                    'buyer_id' => $user->id,
                    'order_id' => $order->id,
                    'card_id' => $card['id'] ?? null,
                    'stripe_card_id' => $stripeCardId,
                    'stripe_customer_id' => $stripeCustomerId,
                    'stripe_transaction_id' => $transactionId,
                    'total' => (double)$total,
                    'status' => 'paid'
                ]);


                /*$gym = Gym::find($gymId);
                $notification = new Notification();
                $notification->screen = 'gym_order';
                $notification->user_id = $pass_owner;
                $notification->source_id = $gymId;
                $notification->description = 'New Pass is purchased on '.$gym->name;
                $notification->source_image =  ltrim($gym->image, '/');
                $notification->save();*/
                
            }
        }
        $this->setResponse($msg, 1, 200, []);
        return response()->json($this->response, $this->status);
    }

    /**
     * @Description get user passes details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @Author Khuram Qadeer.
     */
    public function getUserPasses(Request $request)
    {
        $userId = $request->user()->id;
        $data = Pass::getUserPassesDetail($userId);
        $this->setResponse('success', 1, 200, $data);
        return response()->json($this->response, $this->status);
    }
}
