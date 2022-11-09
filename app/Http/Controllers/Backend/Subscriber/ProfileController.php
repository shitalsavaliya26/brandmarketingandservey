<?php

namespace App\Http\Controllers\Backend\Subscriber;
use Session;
use Auth;
use App\Models\Brand;
use App\Models\Admin;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * Display a details of the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $profile = Auth::user();
        $countries = Country::get();
        $currencies = Currency::get();

        $callingcode = Country::orderBy('calling_code', 'asc')->pluck('calling_code','id')->toArray();
        $callingcode = array_unique($callingcode);
        sort($callingcode);
        return view('backend.subscriber.profile.profile', compact('profile', 'countries','currencies','callingcode'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, $id)
    {
      
        $id = CustomHelper::getDecrypted($id);
        $user = Admin::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'No user found.');
        }
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'logo' => 'mimes:jpeg,jpg,png,gif',
            'desktop_logo' => 'mimes:jpeg,jpg,png,gif',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'contact_number' => 'required|unique:admins,contact_number,' . $id . ',id',
            // 'email' => 'required|email|max:50|unique:users,email,' . $id . ',id',
            'country_id' => 'required',
            'calling_code' => 'required',
            // 'currency_id' => 'required',
        ], [
            // 'logo.dimensions' => 'Please upload an image with 1:1 aspect ratio',
            // 'desktop_logo.dimensions' => 'Please upload an image with 3:1 aspect ratio',
            'firstname.required' => 'Please enter name',
            'lastname.required' => 'Please enter surname',
        ]);
        try {
            // if ($request->logo) {
            //     if (!\File::isDirectory(public_path('uploads/subscriber/mobile_logo/'))) {
            //         \File::makeDirectory(public_path('uploads/subscriber/mobile_logo'), $mode = 0755, $recursive = true);
            //     }
            //     $file_name = time() . '_mobile_logo.' . $request->logo->getClientOriginalExtension();
            //     $logo = $request->logo->move(public_path('uploads/subscriber/mobile_logo'), $file_name);
            //     $user->logo = $file_name;
            // }

            if ($request->desktop_logo) {
                if (!\File::isDirectory(public_path('uploads/subscriber/desktop_logo'))) {
                    \File::makeDirectory(public_path('uploads/subscriber/desktop_logo'), $mode = 0755, $recursive = true);
                }
                $desktop_file_name = time() . '_desktop_logo.' . $request->desktop_logo->getClientOriginalExtension();
                $desktop_logo = $request->desktop_logo->move(public_path('uploads/subscriber/desktop_logo'), $desktop_file_name);
                $user->desktop_logo = $desktop_file_name;
            }
            $user->organization_name = $request->organization_name;
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->contact_number = $request->contact_number;
            $user->calling_code = $request->calling_code;
            // $user->email = $request->email;
            $user->country_id = $request->country_id;
            if(empty($user->currency_id )){
                $user->currency_id = $request->currency_id;
            }
            $user->save();

            if($user->is_bonus_added != '1'){
                $currency = Currency::find($user->currency_id);
                $bonus = (10*$currency['rate']);
                $user->bonus_amount = $bonus;
                $user->bonus_amount_usd = 10;
                $user->wallet_amount = 0;
                $user->wallet_amount_usd = 0;
                $user->is_bonus_added = '1';
                $user->update();

                return redirect()->route('subscriber.dashboard')->with('success', 'Profile save successfully');
            }
            return redirect()->back()->with('success', 'Profile updated successfully');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pauseAccount($id)
    {
        $id = CustomHelper::getDecrypted($id);

        $user = Admin::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'No user found.');
        }

        try {
            $user->pause_account = '1';
            $user->save();
            return redirect()->route('subscriber.profile')->with('success', 'Your account is pasued!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unpauseAccount($id)
    {
        $id = CustomHelper::getDecrypted($id);

        $user = Admin::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'No user found.');
        }

        try {
            $user->pause_account = null;
            $user->save();
            return redirect()->route('subscriber.profile')->with('success', 'Your account is unpasued!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete Subscriber
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAccount($id)
    {
        $id = CustomHelper::getDecrypted($id);
        $subscriber = Admin::find($id);
        if (!$subscriber) {
            Session::flash('error', 'No recored found.');
            return 0;
        }

        Brand::where('subscriber_id',auth()->user()->id)->delete();

        $subscriber->email = "delete".rand(999,10000).time()."@gmail.com";
        $subscriber->contact_number = null;
        $subscriber->save();
        $subscriber->delete();
        auth()->logout();

        Session::flash('success', 'Subscriber delete successfully.');
        return 1;
    }


    public function currencyget(Request $request, $id)
    {
        $country = Country::find($id);
        $currencyData = Currency::where('code', $country['currency'])->first();
        if (!empty($currencyData)) {
            return response()->json(['success' => true, 'data' => $currencyData['id'],'callingcode' => $country['calling_code']]);
        } else {
            return response()->json(['success' => false, 'data' => "",'callingcode' => $country['calling_code']]);
        }
    }
}
