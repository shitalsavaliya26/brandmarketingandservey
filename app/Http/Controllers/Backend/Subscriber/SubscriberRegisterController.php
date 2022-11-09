<?php

namespace App\Http\Controllers\Backend\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Subscriber;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Mail;
use Session;
use Validator;

class SubscriberRegisterController extends Controller
{
    use RedirectsUsers, ThrottlesLogins;
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    /**
     * Where to redirect users after register.
     *
     * @var string
     */
    protected $redirectTo = '/subscriber/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     */
    public function __construct()
    {
        $this->middleware('guest:subscribers')->except('register', 'showRegisterForm');
    }
    /**
     * Show the application's register form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        Session::forget('url');
        $countries = Country::get();
        $currencies = Currency::get();
        return view('backend.subscriber.auth.register', compact('countries', 'currencies'));
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $request->validate([
            // 'firstname' => 'required|string|max:255',
            // 'lastname' => 'required|string|max:255',
            // 'organization_name' => 'required|string|max:255',
            // 'contact_number' => ['required', 'unique:admins'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => 'required|string|max:30|min:6',
            // 'logo' => 'required|mimes:jpeg,jpg,png,gif',
            // 'desktop_logo' => 'required|mimes:jpeg,jpg,png,gif',
            // 'country_id' => 'required',
            // 'currency_id' => 'required',
        ], [
            // 'logo.dimensions' => 'Please upload an image with 1:1 aspect ratio',
            // 'desktop_logo.dimensions' => 'Please upload an image with 3:1 aspect ratio',
        ]);
        try {

            // if ($request->logo) {
            //     $path = ('uploads/subscriber/mobile_logo');
            //     if (!\File::isDirectory(public_path('uploads/subscriber/mobile_logo'))) {
            //         \File::makeDirectory(public_path('uploads/subscriber/mobile_logo'), $mode = 0755, $recursive = true);
            //     }
            //     $file_name = time() . '_mobile_logo.' . $request->logo->getClientOriginalExtension();
            //     $logo = $request->logo->move(public_path('uploads/subscriber/mobile_logo'), $file_name);
            //     // $subscriber->logo = $file_name;
            // }

            // if ($request->desktop_logo) {
            //     $path = ('uploads/subscriber/desktop_logo');
            //     if (!\File::isDirectory(public_path('uploads/subscriber/desktop_logo'))) {
            //         \File::makeDirectory(public_path('uploads/subscriber/desktop_logo'), $mode = 0755, $recursive = true);
            //     }
            //     $desktop_file_name = time() . '_desktop_logo.' . $request->desktop_logo->getClientOriginalExtension();
            //     $desktop_logo = $request->desktop_logo->move(public_path('uploads/subscriber/desktop_logo'), $desktop_file_name);
            // }

            // $currency = Currency::find($request->currency_id);
            // $bonus = (10*$currency['rate']);
            $subscriber = Admin::create([
                // 'firstname' => $request->firstname,
                // 'lastname' => $request->lastname,
                // 'organization_name' => $request->organization_name,
                // 'contact_number' => $request->contact_number,
                // 'calling_code' => $request->calling_code,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                // 'country_id' => $request->country_id,
                // 'currency_id' => $request->currency_id,
                // 'logo' => $request->logo ? $file_name : null,
                // 'desktop_logo' => $request->desktop_logo ? $desktop_file_name : null,
                // 'status' => "1",
                // 'bonus_amount' => $bonus,
                // 'bonus_amount_usd' => 10,
                // 'wallet_amount' => $bonus,
                // 'wallet_amount_usd' => 10,
            ]);

            // Auth::guard('subscribers')->login($subscriber);
            $subscriber = Admin::where('email', $request->email)->first();
            $token = Password::broker()->createToken($subscriber);

            $subscriber->token = $token;
            $subscriber->save();

            Mail::send('emails.email-verification', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Verify Email Address');
            });

            return redirect()->route('subscriber.login')->with('success', 'Account created successfully, Please activate it through an mail we sent.');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function verifyAccount(Request $request,$token)
    {

        if ($request->isMethod('POST')) {

            $request->validate([
                'terms_and_conditions' => 'required|array|min:2',
            ], [
                'terms_and_conditions.required' => 'Please select terms and conditions.',
                'terms_and_conditions.min' => 'Please select terms and conditions.',
            ]);
            try {

                $admin = Admin::where('token', $token)->first();
                $message = 'Sorry your email cannot be identified.';
                if (!is_null($admin)) {
                    if ($admin->email_verified_at == null || $admin->email_verified_at == '' || empty($admin->is_terms_and_conditions_accepted)) {
                        $admin->is_terms_and_conditions_accepted = '1';
                        $admin->email_verified_at = now();
                        $admin->save();
                        $message = "Your e-mail is verified. You can now login.";
                    } else {
                        $message = "Your e-mail is already verified. You can now login.";
                    }
                }
                return redirect()->route('subscriber.login')->with('success', $message);

            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
           
        } else {

            $admin = Admin::where('token', $token)->first();
            if($admin){
                 if(empty($admin->is_terms_and_conditions_accepted)){
                    return view('layouts.backend.subscriber.terms_and_conditions_register',compact('token'));
                 }
                 else{
                    return redirect()->route('subscriber.login')->with('success', 'Your e-mail is already verified. You can now login.');
                 }
            }
            
        }
    }

    public function currencyget(Request $request, $id)
    {
        $country = Country::find($id);
        $currencyData = Currency::where('code', $country['currency'])->first();
        if (!empty($currencyData)) {
            return response()->json(['success' => true, 'data' => $currencyData['id']]);
        } else {
            return response()->json(['success' => false, 'data' => ""]);
        }
    }

    // /**
    //  * Get the guard to be used during authentication.
    //  *
    //  * @return \Illuminate\Contracts\Auth\StatefulGuard
    //  */
    // protected function guard()
    // {
    //     return Auth::guard('subscribers');
    // }



    public function showResendverificationForm(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'exists:admins'],
            ]);

            $subscriber = Admin::where('email', $request->email)->first();

            if (!empty($subscriber['provider_name'])) {
                $validator = Validator::make([], []);
                $validator->getMessageBag()->add('email', "You can't send verification link to social account.");
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if (!empty($subscriber['email_verified_at'])) {
                $validator = Validator::make([], []);
                $validator->getMessageBag()->add('email', "Your e-mail is already verified. You can now login.");
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $token = Password::broker()->createToken($subscriber);
            $subscriber->token = $token;
            $subscriber->save();

            try {
                Mail::send('emails.email-verification', ['token' => $token], function ($message) use ($request) {
                    $message->to($request->email);
                    $message->subject('Verify Email Address');
                });
            }
            catch(\Exception $e) {
                return redirect()->back()->with('error', 'Something went wrong please try after sometime.');
            }
            return redirect()->back()->with('success', 'We have send verification link to your email address.');
        }
        else {
            return view('backend.subscriber.auth.resendverification');
        }
        
    }
}
