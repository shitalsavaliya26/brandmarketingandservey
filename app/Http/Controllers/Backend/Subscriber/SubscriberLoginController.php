<?php

namespace App\Http\Controllers\Backend\Subscriber;

use Laravel\Socialite\Facades\Socialite;
use App\Models\Admin;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Session;

class SubscriberLoginController extends Controller
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
     * Where to redirect users after login.
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
        $this->middleware('guest:subscribers')->except('logout', 'showLoginForm');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        Session::forget('url');
        return view('backend.subscriber.auth.login');
    }

  

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
        // return array_merge($request->only($this->username(), 'password'), ['status' => 1, 'pause_account' => NULL]);

    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
        ? new JsonResponse([], 204)
        : redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->pause_account == '0' || $user->status == '0') {
            $this->guard()->logout();
            return redirect()->route('subscriber.login')->with('error', 'Looks like your status is deactivate by admin');
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        // $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
        ? new JsonResponse([], 204)
        : redirect()->route('subscriber.login');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('subscribers');
    }

    /**
     * Redirect the user to the provider authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider(Request $request,$driver)
    {
        Session::put('url', $request->get('page'));
        return Socialite::driver($driver)->redirect();
    }

    /**
     * Obtain the user information from provider.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($driver)
    {
        if(Session::get('url') == "login"){
            Session::forget('url');
            try {
                $user = Socialite::driver($driver)->user();
            } catch (\Exception $e) {
                return redirect()->route('subscriber.login');
            }
    
            $existingUser = Admin::where('email', $user->getEmail())->where('provider_name', $driver)->first();
    
            if ($existingUser) {
                    $name = explode(" ", $user->getName());
                    $existingUser->update([
                        'provider_name' => $driver,
                        'provider_id' => $user->getId(),
                        'firstname' => $name[0],
                        'lastname' => $name[1],
                    ]);
                    auth()->guard('subscribers')->login($existingUser);
    
                    return redirect()->route('subscriber.dashboard');
            } else {
                return redirect()->route('subscriber.register')->with('error', 'Account not found! Kindly sign up first.');
            }
            return redirect($this->redirectPath());
        }else{
            Session::forget('url');
            try {
                $user = Socialite::driver($driver)->user();
            } catch (\Exception $e) {
                return redirect()->route('subscriber.login');
            }
    
            $existingUser = Admin::where('email', $user->getEmail())->where('provider_name', $driver)->first();
    
            if ($existingUser) {
                return redirect()->route('subscriber.login')->with('error', ' Account already created. Please, login here.');
            } else {
                $find = Admin::where('email', '=', $user->getEmail())->first();
                if(!$find){
    
                    $name = explode(" ", $user->getName());
                    $newUser = new Admin;
                    $newUser->provider_name = $driver;
                    $newUser->provider_id = $user->getId();
                    $newUser->firstname = $name[0];
                    $newUser->lastname = $name[1];
                    $newUser->email = $user->getEmail();
                    $newUser->email_verified_at = now();
                    $newUser->save();
    

                    auth()->guard('subscribers')->login($newUser);
                    return redirect()->route('subscriber.dashboard');
                }
                else{

                    return redirect()->route('subscriber.register')->with('error', 'Already have an account with this email.Please, choose different email.');
                }
            }
    
            return redirect($this->redirectPath());
        }
    }
}
