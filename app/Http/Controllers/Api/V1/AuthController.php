<?php

namespace App\Http\Controllers\Api\V1;

use DB;
use App\Otp;
use JWTAuth;
use App\User;
use Validator;
use Carbon\Carbon;
use App\Models\Country;
use Twilio\Rest\Client;
use App\Models\Currency;
use Tymon\JWTAuth\Token;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Twilio\Exceptions\TwilioException;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Notifications\PasswordResetRequest;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "contact_number" => 'required_if:login_type,normal',
                "password" => "required_if:login_type,normal",
                "login_type" => "required",
                "email" => "required_if:login_type,facebook|required_if:login_type,google|required_if:login_type,linkedin",
                'facebook_id' => 'required_if:login_type,facebook',
                'google_id' => 'required_if:login_type,google',
                'linkedin_id' => 'required_if:login_type,linkedin',
                'apple_id' => 'required_if:login_type,apple',
                "device_name" => "nullable",
                "device_token" => "nullable",
            ]
        );
        //print_r($request->all());die();

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        if ($request->login_type == "normal") {
            $credentials = $request->only('contact_number', 'password');
        } elseif ($request->login_type == "facebook") {
            $inputs = $request->all();
            $credentials['email'] = $request->only('email');
            $credentials['facebook_id'] = $request->only('facebook_id');
        } elseif ($request->login_type == "google") {
            $inputs = $request->all();
            $credentials['email'] = $request->only('email');
            $credentials['google_id'] = $request->only('google_id');
        } elseif ($request->login_type == "linkedin") {
            $inputs = $request->all();
            $credentials['email'] = $request->only('email');
            $credentials['linkedin_id'] = $request->only('linkedin_id');
        } elseif ($request->login_type == "apple") {
            $inputs = $request->all();
            $credentials['email'] = $request->only('email');
            $credentials['apple_id'] = $request->only('apple_id');
        }
        if($request->has('email')){
            $user = User::where('email',$request->email)->first();
        }
        if($request->login_type != 'normal'){
            $user = User::where('email', $request->email)->with('country:id,name,calling_code,iso','city:id,name')->first();
            if(!$user){
                return response(['success' => false, 'message' => "User not found.", "code" => 400], 400);
            }
            $user->login_type = $request->login_type;

            if ($request->login_type == 'google') {
               $user->google_id = $request->google_id;
            } elseif ($request->login_type == 'facebook') {
               $user->facebook_id = $request->facebook_id;
            }elseif ($request->login_type == 'linkedin') {
               $user->linkedin_id = $request->linkedin_id;
            }elseif ($request->login_type == 'apple') {
               $user->apple_id = $request->apple_id;
            }
            $user->update();

            $token = JWTAuth::fromUser($user);
            $user->device_name = $request->device_name;
            $user->device_token = $request->device_token;
            $user->save();

            return response()->json([
                'success' => true,
                'token' => $token,
                'data' => $user,
                'message' => 'Login successfully.',
                "code" => 200,
            ]);
        }elseif ($token = JWTAuth::attempt($credentials)) {
            $loggedUser = User::where('id', Auth::user()->id)->with('country:id,name,calling_code','city:id,name')->first();
            $loggedUser->device_name = $request->device_name;
            $loggedUser->device_token = $request->device_token;
            $loggedUser->save();
            

            return response()->json([
                'success' => true,
                'token' => $token,
                'data' => $loggedUser,
                'message' => 'Login successfully.',
                "code" => 200,
            ],200);
        }elseif ($request->login_type == 'normal') {
            return response(['success' => false, 'message' => "Username/Password is incorrect.", "code" => 400], 400);
        }else{
            return response(['success' => false, 'message' => "User not found.", "code" => 400], 400);
        }

        $users = User::where('contact_number', $request->contact_number)->where('login_type', '!=', 'normal')->first();
        if ($users) {
            return response(['success' => false, 'message' => "Your account has been registered with Google / Facebook / Apple. Please use the respective way to login.", "code" => 400], 400);
        }

        return response(['success' => false, 'message' => "User not found.", "code" => 400], 400);
    }

    public function register(Request $request)
    {
        if($request->has('contact_number')) {
            $request->merge(['contact_number' => CustomHelper::decrypt($request->contact_number)]);
        }

        $credentials = $request->all();

        $validator = Validator::make($credentials, [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email'=>'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'contact_number' => 'required|unique:users|max:255',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'password' => 'required_if:login_type,normal',
            'facebook_id' => 'required_if:login_type,facebook',
            'google_id' => 'required_if:login_type,google',
            'linkedin_id' => 'required_if:login_type,linkedin',
            'apple_id' => 'required_if:login_type,apple',
            "login_type" => "required",
            "dialing_code" => "required",
            "device_name" => "nullable",
            "device_token" => "nullable",
            "avatar" => 'mimes:jpg,jpeg,png,JPG,JPEG,gif'

        ]);

         //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }
        $count = User::where('email', $request->email)->first();
        if($request->login_type == "normal"){
            if ($count) {
                return response()->json([
                    "success" => false,
                    "message" => "Already have an account with this email.Please, choose different email.",
                    "code" => 400,
                ],400);
            }
        }else{
            if ($count) {
                return response()->json([
                    "success" => false,
                    "message" => "Already have an account with this email.Please, do forgot passord to sign in.",
                    "code" => 400,
                ],400);
            }
        }
       
        if($request->has('dialing_code') && $request->has('contact_number') && $request->login_type == "normal") {
            $number = $request->dialing_code."".$request->contact_number;

            $time = Carbon::now();
            $subtime = Carbon::now()->subSeconds(30);
            $record = Otp::where(['contact_number' => $number])->whereBetween('created_at', [$subtime, $time])->orderBy('id', 'desc')->first();

            if(!empty($record)){
                return response()->json([
                    "success" => false,
                    "message" => "Wait for 30 seconds.",
                    "code" => 400,
                ],200);
            } 
            
            $sid    = config('services.twilio.account_sid');
            $token  = config('services.twilio.auth_token');
            $client = new Client( $sid, $token );
            $otp = rand(100000, 999999);
            $message = "Example OTP - ".$otp;
         
            try {
                $client->messages->create(
                    $number,
                    [
                        'from' => config('services.twilio.from'),
                        'body' => $message,
                    ]
                );
            } catch (TwilioException $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    "code" => 400,
                ],400);
            }

            $saveotp = new Otp;
            $saveotp->contact_number = $number;
            $saveotp->otp = $otp ;
            $saveotp->save();
        }
       
        if ($request->login_type == 'facebook') {
            $password = bcrypt($request->facebook_id . '@example');
            $facebook_id = $request->facebook_id;
        } elseif ($request->login_type == 'linkedin') {
            $password = bcrypt($request->linkedin_id . '@example');
            $linkedin_id = $request->linkedin_id;
        } elseif ($request->login_type == 'google') {
            $password = bcrypt($request->google_id . '@example');
            $google_id = $request->google_id;
        } elseif ($request->login_type == 'apple') {
            $password = bcrypt($request->apple_id . '@example');
            $apple_id = $request->apple_id;
        }

        
        /* currency id base on country */
        if($request->country_id){
            $country = Country::find($request->country_id);
            $currencyData = Currency::where('code', $country['currency'])->first();
            if (!empty($currencyData)) {
                $currency = $currencyData['id'];
            } else {
                $currency = 1;
            }
        }else{
            $currency = 1;
        }

        $createUser = new User;

        $createUser->firstname = $request->firstname;
        $createUser->lastname = $request->lastname;
        $createUser->email = $request->email;
        $createUser->contact_number = $request->contact_number;
        $createUser->country_id = $request->country_id;
        $createUser->currency_id = $currency;
        $createUser->city_id = $request->city_id;
        $createUser->login_type = $request->login_type;
        $createUser->dialing_code = $request->dialing_code;
        $createUser->device_name = $request->device_name;
        $createUser->device_token = $request->device_token;
        $createUser->facebook_id = $request->has('facebook_id') ? $request->facebook_id : null;
        $createUser->linkedin_id = $request->has('linkedin_id') ? $request->linkedin_id : null;
        $createUser->google_id = $request->has('google_id') ? $request->google_id : null;
        $createUser->apple_id = $request->has('apple_id') ? $request->apple_id : null;
        $createUser->password = $request->has('password') ? bcrypt($request->password) : $password;

        if ($request->hasFile('avatar')) {
            $path = ('uploads/avatar');
            if (!\File::isDirectory(public_path('uploads/avatar'))) {
                \File::makeDirectory(public_path('uploads/avatar'), $mode = 0755, $recursive = true);
            }
            $file_name = time() . '_avatar.' . $request->avatar->getClientOriginalExtension();
            $image = $request->avatar->move(public_path('uploads/avatar'), $file_name);
            $createUser->avatar = $file_name;
        } elseif (is_string($request->avatar)) {
            if (!\File::isDirectory(public_path('uploads/avatar'))) {
                \File::makeDirectory(public_path('uploads/avatar'), $mode = 0755, $recursive = true);
            }
            $image = file_get_contents($request->avatar);
            $avatar = time() . '_avatar.png';
            file_put_contents(public_path('uploads/avatar') . '/' . $avatar, $image);
            $createUser->avatar = $avatar;
        } else {
            $createUser->avatar = '';
        }

        $createUser->save();

        // $userData = User::where('contact_number', $request->contact_number)->first();
        $token = JWTAuth::fromUser($createUser);

        JWTAuth::setToken($token);

        $createUser = User::where('id', $createUser->id)->with('country:id,name,calling_code','city:id,name')->first();
        // $createUser = $createUser->toArray();

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
            'data' => $createUser,
            'message' => 'You are successfully registered in Example.',
            "code" => 200,
        ],200);
    }
    

    public function resendOtp(Request $request)
    {
        if($request->has('contact_number')) {
            // $encryption = new \MrShan0\CryptoLib\CryptoLib();
            // $request->merge(['contact_number' => $encryption->decryptCipherTextWithRandomIV($request->contact_number, env('SECRET_KEY'))]);
            $request->merge(['contact_number' => CustomHelper::decrypt($request->contact_number)]);
        }
        $credentials = $request->all();    
        $validator = Validator::make($credentials, [
            'contact_number' => 'required|max:255',
            'dialing_code' => 'required',
        ]);
       
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $number = $request->dialing_code."".$request->contact_number;

        $time = Carbon::now();
        $subtime = Carbon::now()->subSeconds(30);
        $record = Otp::where(['contact_number' => $number])->whereBetween('created_at', [$subtime, $time])->orderBy('id', 'desc')->first();

        if(!empty($record)){
            return response()->json([
                "success" => false,
                "message" => "Wait for 30 seconds.",
                "code" => 400,
            ],400);
        } 
        
        $sid    = config('services.twilio.account_sid');
        $token  = config('services.twilio.auth_token');
        $client = new Client( $sid, $token );
        $otp = rand(100000, 999999);
        $message = "Example OTP - ".$otp;

        Otp::where(['contact_number' => $number])->delete();
        try {
            $client->messages->create(
                $number,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message,
                ]
            );
        } catch (TwilioException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                "code" => 400,
            ],400);
        }

        $saveotp = new Otp;
        $saveotp->contact_number = $number;
        $saveotp->otp = $otp ;
        $saveotp->save();


        return response()->json([
            'success' => true,
            'message' => 'Otp send successfully.',
            "code" => 200,
        ],200);
       
    }

    /**
     * @api {post} /forgot-password User Forgot Password
     * @apiName Forgot Password
     * @apiGroup User
     * @apiVersion 0.0.1
     *
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), array(
            "email" => "required",
        ));

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        } else {
            $user = User::where('email', $request->email)->first();
            if ($user && $user != null) {
                $token = Password::broker()->createToken($user);

                PasswordReset::updateOrCreate(['email' => $user->email], ['email' => $user->email, 'token' => $token]);

                $user->notify(new PasswordResetRequest($token));
                return response()->json(['success' => true, 'message' => 'An email is sent to your mailbox with Reset Password Details. Please check your inbox for more details.', "code" => 200], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Enter your registered email to reset your password', "code" => 400], 400);
            }
        }
    }

    public function showResetPasswordForm($token)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
        ]);
    }

    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $getEmail = DB::table('password_resets')->where(['token' => $request->token])->first();

        if (!$getEmail) {
            return back()
            ->withInput()
            ->with('error', 'Invalid token!');
        }

        $user = User::where('email', $getEmail->email)->update([
            'password' => bcrypt($request->password),
            'login_type' => 'normal'
        ]);

        DB::table('password_resets')
        ->where(['email' => $getEmail->email])
        ->delete();

        return redirect('/')->with(
            'success',
            'Your password has been changed!'
        );
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $token = $request->header("Authorization");
        // Invalidate the token
        try {
            $user = Auth::user();
            $user->device_token = '';
            $user->save();
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                "success" => true,
                "message" => "User successfully logged out.",
                "code" => 200,
            ],200);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                "success" => "error",
                "message" => "Failed to logout, please try again.",
                "code" => 500,
            ]);
        }
    }

    public function isEmailExists(Request $request)
    {
        $credentials = $request->all();

        $validator = Validator::make($credentials, [
            'email' => 'required|unique:users|max:255'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        return response()->json([
                "success" => true,
                "message" => "You can use this email.",
                "code" => 200,
            ],200);
    }

     public function isPhoneExists(Request $request)
    {
        $credentials = $request->all();    
        $validator = Validator::make($credentials, [
            'contact_number' => 'required|unique:users|max:255',
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        return response()->json([
                "success" => true,
                "message" => "You can use this contact number.",
                "code" => 200,
            ],200);
    }


    public function verifyOtp(Request $request)
    {
     
        if($request->has('contact_number')) {
            $request->merge(['contact_number' => CustomHelper::decrypt($request->contact_number)]);
            // $encryption = new \MrShan0\CryptoLib\CryptoLib();
            // $request->merge(['contact_number' => $encryption->decryptCipherTextWithRandomIV($request->contact_number, env('SECRET_KEY'))]);
        }
       
        $credentials = $request->all();    
        $validator = Validator::make($credentials, [
            'contact_number' => 'required|max:255',
            'dialing_code' => 'required',
            'otp' => 'required'
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $number = $request->dialing_code."".$request->contact_number;

        $verify = Otp::where(['contact_number' => $number,'status' => "0",'otp' => $request->otp])->orderBy('id', 'desc')->first();

        if(!empty($verify))
        {
            $verify->status = "1";
            $verify->save();
            $verify->delete();
            Otp::where(['contact_number' => $number])->delete();
            $user =  User::with('country:id,name,calling_code,iso','city:id,name')->where(['contact_number' => $request->contact_number,'otp_verified' => "0"])->first();

            if(!empty($user)){
                $user->otp_verified = "1";
                $user->save();
                
                $token = JWTAuth::fromUser($user);

                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'data' => $user,
                    'message' => 'Otp Verification Successfully.',
                    "code" => 200,
                ]);
            }
            else{
                return response(['success' => false, 'message' => "User not found.", "code" => 400], 400);
            }
        }
        else{
            return response()->json([
                "success" => false,
                "message" => "Invalid otp",
                "code" => 400,
            ],400);
        }
    }

    public function isAppleIdExists(Request $request)
    {
        $credentials = $request->all();

        $validator = Validator::make($credentials, [
            'apple_id' => 'required|unique:users|max:255'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        return response()->json([
                "success" => true,
                "message" => "You can use this apple account.",
                "code" => 200,
            ],200);
    }

    
}
