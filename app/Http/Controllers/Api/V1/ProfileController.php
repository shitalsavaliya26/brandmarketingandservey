<?php

namespace App\Http\Controllers\Api\V1;

use App\Otp;
use App\User;
use Validator,Auth;
use App\Models\Message;
use App\UserPollHistory;
use App\Models\PollAnswer;
use App\UserWithdrawHistory;
use Illuminate\Http\Request;
use App\Models\TransactionHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\UserNotificationTopic;

class ProfileController extends Controller
{
    /* get user profile */
    public function getProfile()
    {

        $loggedUser = User::where('id', Auth::user()->id)->with('country:id,name,calling_code,iso','city:id,name')->first();
        return response()->json([
            'success' => true,
            'data' => $loggedUser,
            'message' => 'Data retrived.',
            "code" => 200,
        ],200);
    }

    /* update user profile */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'contact_number' => 'required|unique:users,contact_number,'.$user->id,
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            "dialing_code" => "required",
            "avatar" => 'mimes:jpg,jpeg,png,JPG,JPEG,gif'
        ]);

        //Send failed response if request is not valid

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->email     = $request->email;
        $user->contact_number = $request->contact_number;
        $user->country_id = $request->country_id;
        $user->city_id    = $request->city_id;
        $user->dialing_code    = $request->dialing_code;

        if ($request->hasFile('avatar')) {

            $path = ('uploads/avatar');
            if (!\File::isDirectory(public_path('uploads/avatar'))) {
                \File::makeDirectory(public_path('uploads/avatar'), $mode = 0755, $recursive = true);
            }
            $file_name = time() . '_avatar.' . $request->avatar->getClientOriginalExtension();
            $image = $request->avatar->move(public_path('uploads/avatar'), $file_name);
            $user->avatar = $file_name;
        } 

        $user->save();

        $user = User::where('id',auth()->user()->id)->with('country:id,name,calling_code','city:id,name')->first();

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Profile updated successfully.',
            "code" => 200,
        ],200);
    }


    /* delete account */
    public function deleteaccount()
    {
        $deletedcount = User::onlyTrashed()->count();
        $user = User::find(auth()->user()->id);
        if($user){
            UserWithdrawHistory::where('user_id',auth()->user()->id)->delete();
            // UserPollHistory::where('user_id',auth()->user()->id)->delete();
            UserNotificationTopic::where('user_id',auth()->user()->id)->delete();
            // TransactionHistory::where('user_id',auth()->user()->id)->delete();
            // PollAnswer::where('user_id',auth()->user()->id)->delete();
            Message::where('user_id',auth()->user()->id)->delete();
            Otp::where('contact_number',$user->dialing_code."".$user->contact_number)->delete();
        }
        // $user->email = "delete".rand(999,10000).$deletedcount."@gmail.com";
        $user->contact_number = null;
        $user->save();
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully.',
            "code" => 200,
        ],200);
    }


     /* get user profile */
     public function removeProfileimage()
     {
         $user = User::find(Auth::user()->id);
         $user->avatar = null;
         $user->save();

         $loggedUser = User::where('id', Auth::user()->id)->with('country:id,name,calling_code,iso','city:id,name')->first();
         return response()->json([
             'success' => true,
             'data' => $loggedUser,
             'message' => 'Profile photo removed.',
             "code" => 200,
         ],200);
     }


      /* delete account */
    public function changepassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|max:255',
            'new_password' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $hashedPassword = Auth::user()->password;
        if (Hash::check($request->old_password , $hashedPassword)) {
            $users = User::find(Auth::user()->id);
            $users->password = Hash::make($request->new_password);
            $users->update();
            return response()->json([
                'success' => true,
                'message' => 'Password change successfully.',
                "code" => 200,
            ],200);
        }
        else{
            return response()->json(['success' => false, 'message' => "Old password does not matched.", "code" => 400], 400);
        }
    }
}
