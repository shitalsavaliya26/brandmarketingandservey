<?php

namespace App\Http\Controllers\Backend\Superadmin;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\PasswordReset;
use App\Models\Subscriber;
use App\Models\Admin;
use App\Models\TransactionHistory;
use App\Notifications\ChangePasswordMail;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Notification;
use Auth;

class SubscriberController extends Controller
{
    public function __construct(Request $request)
    {
      
        $this->limit = $request->limit ? $request->limit : 10;
    }
    //

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        $subscribers = Subscriber::with(['currency']);

        if ($request->has('name') && $request->name != '' && $request->name != null) {
            $subscribers = $subscribers->where('firstname', 'LIKE', "%{$request->name}%")
                ->orWhere('lastname', 'LIKE', "%{$request->name}%")
                ->orWhere('email', 'LIKE', "%{$request->name}%");
        }
        if ($request->has('status') && ($request->status != '' && $request->status != 'null')) {
            $subscribers = $subscribers->where('status', $request->status);
        }
        if ($request->has('country') && ($request->country != '' && $request->country != 'null')) {
            $subscribers = $subscribers->where('country_id', $request->country);
        }

        $countries = Country::get();

        $subscribers = $subscribers->with('country')->orderBy('id', 'desc')->paginate($this->limit);
        foreach ($subscribers as $subscriber) {
            $subscriber['spend'] = TransactionHistory::where('admin_id', $subscriber->id)->sum('amount_usd');
        }

        return view('backend.superadmin.subscriber.index', compact('subscribers', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::get();
        return view('backend.superadmin.subscriber.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'organization_name' => 'required|string|max:255',
            'logo' => 'required|mimes:jpeg,jpg,png,gif',
            'contact_number' => ['required', 'unique:admins'],
            'website_url' => ['required', 'regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'country_id' => 'required',
        ]);
        try {
            $subscriber = new Subscriber();
            $subscriber->firstname = $request->firstname;
            $subscriber->lastname = $request->lastname;
            $subscriber->organization_name = $request->organization_name;
            $subscriber->contact_number = $request->contact_number;
            $subscriber->website_url = $request->website_url;
            $subscriber->email = $request->email;
            $subscriber->country_id = $request->country_id;
            $subscriber->status = $request->status;

            if ($request->logo) {
                $path = ('uploads/subscriber/mobile_logo');
                if (!\File::isDirectory(public_path('uploads/subscriber/mobile_logo'))) {
                    \File::makeDirectory(public_path('uploads/subscriber/mobile_logo'), $mode = 0755, $recursive = true);
                }
                $file_name = time() . '_mobile_logo.' . $request->logo->getClientOriginalExtension();
                $logo = $request->logo->move(public_path('uploads/subscriber/mobile_logo'), $file_name);
                $subscriber->logo = $file_name;
            }

            if ($request->desktop_logo) {
                $path = ('uploads/subscriber/desktop_logo');
                if (!\File::isDirectory(public_path('uploads/subscriber/desktop_logo'))) {
                    \File::makeDirectory(public_path('uploads/subscriber/desktop_logo'), $mode = 0755, $recursive = true);
                }
                $desktop_file_name = time() . '_desktop_logo.' . $request->desktop_logo->getClientOriginalExtension();
                $desktop_logo = $request->desktop_logo->move(public_path('uploads/subscriber/desktop_logo'), $desktop_file_name);
                $subscriber->desktop_logo = $desktop_file_name;
            }

            $subscriber->save();

            $token = uniqid(base64_encode(str_random(60)));
            PasswordReset::updateOrCreate(['email' => $subscriber->email], ['email' => $subscriber->email, 'token' => $token]);

            $details = [];
            $details['token'] = $token;
            $details['email'] = $subscriber->email;

            Notification::send($subscriber, new ChangePasswordMail($details));

            return redirect()->route('subscriber.index')->with('success', 'Subscriber created successfully');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = CustomHelper::getDecrypted($id);

        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return redirect()->back()->with('error', 'No recored found.');
        }

        return view('backend.superadmin.subscriber.view', compact('subscriber'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = CustomHelper::getDecrypted($id);

        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $countries = Country::get();

        return view('backend.superadmin.subscriber.edit', compact('subscriber', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = CustomHelper::getDecrypted($id);

        $subscriber = Subscriber::find($id);
        if (!$subscriber) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'organization_name' => 'required|string|max:255',
            'logo' => 'mimes:jpeg,jpg,png,gif',
            'contact_number' => 'required',
            'website_url' => ['required', 'regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'country_id' => 'required',
        ]);
        try {

            if ($request->has('remove_img') && $request->remove_img > 0) {
                if ($subscriber->logo != null && file_exists(public_path('uploads/subscriber/mobile_logo') . '/' . $subscriber->logo)) {
                    unlink(public_path('uploads/subscriber/mobile_logo') . '/' . $subscriber->logo);
                }
                $subscriber->logo = null;
            }
            if ($request->logo) {
                $path = ('uploads/subscriber/mobile_logo');
                if (!\File::isDirectory(public_path('uploads/subscriber/mobile_logo'))) {
                    \File::makeDirectory(public_path('uploads/subscriber/mobile_logo'), $mode = 0755, $recursive = true);
                }
                $file_name = time() . '_mobile_logo.' . $request->logo->getClientOriginalExtension();
                $logo = $request->logo->move(public_path('uploads/subscriber/mobile_logo'), $file_name);
                $subscriber->logo = $file_name;
            }

            if ($request->desktop_logo) {
                $path = ('uploads/subscriber/desktop_logo');
                if (!\File::isDirectory(public_path('uploads/subscriber/desktop_logo'))) {
                    \File::makeDirectory(public_path('uploads/subscriber/desktop_logo'), $mode = 0755, $recursive = true);
                }
                $desktop_file_name = time() . '_desktop_logo.' . $request->logo->getClientOriginalExtension();
                $desktop_logo = $request->desktop_logo->move(public_path('uploads/subscriber/desktop_logo'), $desktop_file_name);
                $subscriber->desktop_logo = $desktop_file_name;
            }

            $subscriber->firstname = $request->firstname;
            $subscriber->lastname = $request->lastname;
            $subscriber->organization_name = $request->organization_name;
            $subscriber->contact_number = $request->contact_number;
            $subscriber->website_url = $request->website_url;
            $subscriber->email = $request->email;
            $subscriber->country_id = $request->country_id;
            $subscriber->save();
            return redirect()->route('subscriber.index')->with('success', 'Subscriber created successfully');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $subscriber->pause_account = '0';
        $subscriber->status = '0';
        $subscriber->save();
        $subscriber->delete();
        return redirect()->route('subscriber.index')->with('success', 'subscriber delete successfully');
    }

    public function showChangePasswordForm($token)
    {
        return view('auth.passwords.change-password', [
            'token' => $token,
        ]);
    }

    public function submitChangePasswordForm(Request $request)
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

        $user = Subscriber::where('email', $getEmail->email)->update([
            'password' => bcrypt($request->password),
        ]);

        DB::table('password_resets')
            ->where(['email' => $getEmail->email])
            ->delete();

        return redirect('/')->with(
            'success',
            'Your password has been changed!'
        );
    }

    public function changeSubscriberStatus(Request $request)
    {
        $subscriber = Subscriber::find($request->user_id);
        if (!$subscriber) {
            return response()->json(['error' => 'Record not found.']);
        }
        $subscriber->status = $request->status == '1' ? '0' : '1';
        $subscriber->save();

        return response()->json(['success' => 'Subscriber status changed successfully.']);
    }

    public function changePassword(Request $request)
    {
        try {
            $subscriber = Subscriber::find($request->id);

            if (!$subscriber) {
                return response()->json(['error' => 'Record not found.']);
            }

            $subscriber->password = bcrypt($request->password);
            $subscriber->save();
            return response()->json(['success' => "Subscriber's password has been changed."]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function loginUsingId($id)
    {
        try {
            $id = CustomHelper::getDecrypted($id);
            $subscriber = Admin::find($id);
            Auth::guard('subscribers')->login($subscriber);

            return redirect()->route('subscriber.dashboard');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
