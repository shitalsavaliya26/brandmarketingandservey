<?php

namespace App\Http\Controllers\Backend\Subscriber;

use Auth;
use Stripe;
use App\Models\Admin;
use App\Models\Topup;
use App\StripeSessionId;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TopupController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('is_information_filled');
        $this->limit = $request->limit ? $request->limit : 10;
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $topup = new Topup();

        $topups = $topup->orderBy('id', 'desc')->where('subscriber_id', Auth::user()->id)->paginate($this->limit);
        return view('backend.subscriber.topup.index', compact('topups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stripeId = Auth::user()->stripe_id;
        $cards = [];
        if ($stripeId != null && $stripeId != '') {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $cards = $stripe->customers->allSources(
                $stripeId,
                ['object' => 'card', 'limit' => 30]
            );
        }
        return view('backend.subscriber.topup.create', compact('cards'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $currencies = strtolower(auth()->user()->currency->code);
            // Set Stripe Key
            Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            if ($request->savecard == "1") {
                // Create user if not exist in stripe

                if (Auth::user()->stripe_id == null || Auth::user()->stripe_id == '') {
                    $customer = \Stripe\Customer::create([
                        'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname,
                        'email' => Auth::user()->email,
                    ]);

                    $source = \Stripe\Customer::createSource(
                        $customer->id,
                        ['source' => $request->stripeToken]
                    );

                    $currentUser = Auth::user();
                    $currentUser->stripe_id = $customer->id;
                    $currentUser->save();

                    $stripeId = $customer->id;

                    try {
                        $charge = Stripe\Charge::create([
                            "amount" => ($request->amount*100),
                            "currency" => $currencies,
                            "customer" => $stripeId,
                            "description" => "This payment is for subscription top-up",
                            "source" => $source->card_id,
                        ]);
                    } catch (\Stripe\Exception\RateLimitException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\InvalidRequestException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\AuthenticationException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\ApiConnectionException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (Exception $e) {
                        return redirect()->back()->with('error', "Something else happened, completely unrelated to Stripe");
                    }
                } else {
                    $stripeId = Auth::user()->stripe_id;
                    $customer = \Stripe\Customer::retrieve($stripeId);

                    $token = \Stripe\Token::retrieve($request->stripeToken);

                    $card = [];
                    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                    $cards = $stripe->customers->allSources(
                        $stripeId,
                        ['object' => 'card', 'limit' => 12]
                    );

                    foreach ($cards as $source) {
                        if ($source['fingerprint'] == $token['card']->fingerprint) {
                            $card = $source;
                        }
                    }
                    if (!is_null($card) && count($card) > 0) {
                        try {
                            $charge = Stripe\Charge::create(array(
                                "amount" => ($request->amount*100),
                                "currency" => $currencies,
                                "customer" => Auth::user()->stripe_id,
                                "description" => "This payment is for subscription top-up",
                                "source" => $card->id,
                            ));
                        } catch (\Stripe\Exception\RateLimitException $e) {
                            return redirect()->back()->with('error', $e->getMessage());
                        } catch (\Stripe\Exception\InvalidRequestException $e) {
                            return redirect()->back()->with('error', $e->getMessage());
                        } catch (\Stripe\Exception\AuthenticationException $e) {
                            return redirect()->back()->with('error', $e->getMessage());
                        } catch (\Stripe\Exception\ApiConnectionException $e) {
                            return redirect()->back()->with('error', $e->getMessage());
                        } catch (\Stripe\Exception\ApiErrorException $e) {
                            return redirect()->back()->with('error', $e->getMessage());
                        } catch (Exception $e) {
                            return redirect()->back()->with('error', "Something else happened, completely unrelated to Stripe");
                        }
                    } else {
                        $source = \Stripe\Customer::createSource(
                            $stripeId,
                            ['source' => $request->stripeToken]
                        );
                        try {
                            $charge = Stripe\Charge::create([
                                "amount" => ($request->amount*100),
                                "currency" => $currencies,
                                "customer" => $stripeId,
                                "description" => "This payment is for subscription top-up",
                                "source" => $source->card_id,
                            ]);
                        } catch (\Stripe\Exception\RateLimitException $e) {
                            return redirect()->back()->with('error', $e->getMessage());
                        } catch (\Stripe\Exception\InvalidRequestException $e) {
                            return redirect()->back()->with('error', $e->getMessage());
                        } catch (\Stripe\Exception\AuthenticationException $e) {
                            return redirect()->back()->with('error', $e->getMessage());
                        } catch (\Stripe\Exception\ApiConnectionException $e) {
                            return redirect()->back()->with('error', $e->getMessage());
                        } catch (\Stripe\Exception\ApiErrorException $e) {
                            return redirect()->back()->with('error', $e->getMessage());
                        } catch (Exception $e) {
                            return redirect()->back()->with('error', "Something else happened, completely unrelated to Stripe");
                        }
                    }
                }
            } else {

                if ($request->has('card_id') && $request->card_id != null && $request->card_id != '') {
                    try {
                        $charge = Stripe\Charge::create(array(
                            "amount" => ($request->amount*100),
                            "currency" => $currencies,
                            "customer" => Auth::user()->stripe_id,
                            "description" => "This payment is for subscription top-up",
                            "source" => $request->card_id,
                        ));
                    } catch (\Stripe\Exception\RateLimitException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\InvalidRequestException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\AuthenticationException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\ApiConnectionException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (Exception $e) {
                        return redirect()->back()->with('error', "Something else happened, completely unrelated to Stripe");
                    }

                } else {
                    try {
                        $charge = Stripe\Charge::create([
                            "amount" => ($request->amount*100),
                            "currency" => $currencies,
                            "description" => "This payment is for subscription top-up",
                            "source" => $request->stripeToken,
                        ]);
                    } catch (\Stripe\Exception\RateLimitException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\InvalidRequestException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\AuthenticationException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\ApiConnectionException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        return redirect()->back()->with('error', $e->getMessage());
                    } catch (Exception $e) {
                        return redirect()->back()->with('error', "Something else happened, completely unrelated to Stripe");
                    }
                }
            }
            $amount = ($request->amount / auth()->user()->currency->rate);

            Topup::create([
                "subscriber_id" => Auth::user()->id,
                "amount" => $request->amount,
                "amount_usd" => $amount,
                // "fees" => ($request->amount * 2.9)/100 + 0.30,
                "fees" => ($amount * 2.9) / 100 + 0.30,
                "currency" => isset(auth()->user()->currency->code) ? auth()->user()->currency->code : 0,
                "currency_id" => isset(auth()->user()->currency->id) ? auth()->user()->currency->id : 0,
                "charge_id" => $charge->id,
                "transaction_id" => $charge->balance_transaction,
                "status" => $charge->status == "succeeded" ? '1' : '0',
                "reason" => $charge->failure_message,
                "response_data" => $charge,
            ]);

            Admin::where('id', Auth::user()->id)->increment('wallet_amount', $request->amount);
            Admin::where('id', Auth::user()->id)->increment('wallet_amount_usd', $amount);

            return redirect()->route('topup.index')->with('success', 'Payment successful!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }





    /* generate url */
    public function generateurl(Request $request)
    {
        $request->validate([
            'amount' => 'required',
        ], [
            'amount.required' => 'Please enter amount.',
        ]);
        try {
            $stripe = new \Stripe\StripeClient(
                config('services.stripe.secret')
            );
            try {
                $data = $stripe->checkout->sessions->create([
                    'success_url' => route('newtopupcreate'),
                    'cancel_url' => route('topup.create'),
                    'line_items' => [
                    [
                        'currency' => auth()->user()->currency->code,
                        'amount' => ($request->amount * 100),
                        'name' =>  auth()->user()->firstname." ".auth()->user()->lastname,
                        'quantity' => 1,
                    ],
                    ],
                    'mode' => 'payment',
                ]);

               
                if(!empty($data) && isset($data->url)){
                    $stripesessionid = new StripeSessionId();
                    $stripesessionid->user_id = auth()->user()->id;
                    $stripesessionid->session_id = $data->id;
                    $stripesessionid->save();
                    return redirect()->away($data->url);
                }
                else{
                    return redirect()->back()->with('error', "Something else happened, completely unrelated to Stripe");
                }
            

            } catch (\Stripe\Exception\RateLimitException $e) {
                return redirect()->back()->with('error', $e->getMessage());
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                return redirect()->back()->with('error', $e->getMessage());
            } catch (\Stripe\Exception\AuthenticationException $e) {
                return redirect()->back()->with('error', $e->getMessage());
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                return redirect()->back()->with('error', $e->getMessage());
            } catch (\Stripe\Exception\ApiErrorException $e) {
                return redirect()->back()->with('error', $e->getMessage());
            } catch (Exception $e) {
                return redirect()->back()->with('error', "Something else happened, completely unrelated to Stripe");
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }      
    }


    /* create url */
    public function newtopupcreate(Request $request)
    {
        $getid = StripeSessionId::where('user_id', auth()->user()->id)->where('status',"generated")->orderBy('id', 'desc')->first();
        if($getid){
            \Stripe\Stripe::setApiKey(config('services.stripe.secret')); 

            try { 
                $checkout_session = \Stripe\Checkout\Session::retrieve($getid['session_id']); 
            } catch(Exception $e) {  
                $api_error = $e->getMessage();  
            } 

            if(empty($api_error) && $checkout_session){ 
                // Retrieve the details of a PaymentIntent 
                try { 
                    $paymentIntent = \Stripe\PaymentIntent::retrieve($checkout_session->payment_intent); 
                } catch (\Stripe\Exception\ApiErrorException $e) { 
                    $api_error = $e->getMessage(); 
                } 
                // Retrieves the details of customer 
                try { 
                    $customer = \Stripe\Customer::retrieve($checkout_session->customer); 
                } catch (\Stripe\Exception\ApiErrorException $e) { 
                    $api_error = $e->getMessage(); 
                } 
                 
                if(empty($api_error) && $paymentIntent){  
                    // Check whether the payment was successful 
                    if(!empty($paymentIntent) && $paymentIntent->status == 'succeeded'){ 
                        // Transaction details  

                      
                        $transactionID = $paymentIntent->id; 
                        $paidAmount = $paymentIntent->amount; 
                        $paidAmount = ($paidAmount/100); 
                        $paidCurrency = $paymentIntent->currency; 
                        $payment_status = $paymentIntent->status; 

                        $amount = ($paidAmount / auth()->user()->currency->rate);
                        Topup::create([
                            "subscriber_id" => Auth::user()->id,
                            "amount" => $paidAmount,
                            "amount_usd" => $amount,
                            "fees" => ($amount * 2.9) / 100 + 0.30,
                            "currency" => isset(auth()->user()->currency->code) ? auth()->user()->currency->code : "",
                            "currency_id" => isset(auth()->user()->currency->id) ? auth()->user()->currency->id : 0,
                            "charge_id" => $paymentIntent->charges->data[0]->id,
                            "transaction_id" => $transactionID,
                            "status" => $payment_status == "succeeded" ? '1' : '0',
                            "reason" => $paymentIntent->charges->data[0]->failure_message,
                            "response_data" => $paymentIntent->charges->data[0],
                            'session_id' => $getid['session_id'],
                        ]);


                        Admin::where('id', Auth::user()->id)->increment('wallet_amount', $paidAmount);
                        Admin::where('id', Auth::user()->id)->increment('wallet_amount_usd', $amount);

                        $getid->status = "completed";
                        $getid->save();
                        return redirect()->route('topup.index')->with('success', 'Payment successful!');

                    }else{ 
                        $getid->status = "rejected";
                        $getid->save();
                        return redirect()->route('topup.index')->with('error', 'Transaction has been failed!');
                    } 
                }else{ 
                    $getid->status = "pending";
                    $getid->save();
                    return redirect()->route('topup.index')->with('error', 'Unable to fetch the transaction details!');
                } 
            }else{ 
                return redirect()->route('topup.index')->with('error', 'Invalid Transaction!');
            } 
        }else{ 
            return redirect()->route('topup.index')->with('error', 'Invalid Transaction!');
        } 
    }

}
