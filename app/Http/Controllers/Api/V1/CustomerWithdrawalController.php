<?php

namespace App\Http\Controllers\Api\V1;

use Auth;
use App\User;
use Validator;
use App\Models\PriceSetting;
use App\UserWithdrawHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerWithdrawalController extends Controller
{

    /* Get withdrawal request */
    public function customerwithdrawalrequest()
    {
        $userwithdrawhistory = UserWithdrawHistory::where('user_id', Auth::user()->id)->get();
        return response()->json([
            'success' => true,
            'data' => $userwithdrawhistory,
            'message' => 'Data retrived.',
            "code" => 200,
        ], 200);
    }

    /* Add withdrawal request*/
    public function addwithdrawalrequest(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            "amount" => "required|numeric",
            'paypal_email' => 'required|email|max:255',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        } else {
            try {

               




                /* Check is any user pending request for verification */
                $pendingcount = UserWithdrawHistory::where('user_id', Auth::user()->id)->where('status',"pending")->count();
                if($pendingcount > 0){
                    return response()->json(['success' => false, 'message' => 'Your previous request is pending for verification.', "code" => 400], 400);
                }

          
                /* Check user wallet amount */
                if(isset($request->amount) && number_format($request->amount,3) > Auth::user()->wallet_amount){
                    return response()->json(['success' => false, 'message' => 'Please enter amount less than or equal to the available wallet balance.', "code" => 400], 400);
                }

                /* Conver USD to user currency */
                $setting = PriceSetting::where('title', "User Withdrawal Setting")->first();
                $minimunamount = ($setting->price*auth()->user()->currency->rate);

               
                /* Check minimim wallet amount */
                if(isset($request->amount) && $request->amount < number_format($minimunamount,3)){
                    $mamount = number_format($minimunamount,3);
                    return response()->json(['success' => false, 'message' => "Minimum Withdrawal limit is {$mamount}", "code" => 400], 400);
                }

                /* Convert user currency to USD */
                $amountusd = ($request->amount/auth()->user()->currency->rate);

                /* Create request */
                $withdraw = new UserWithdrawHistory();
                $withdraw->amount = $request->amount;
                $withdraw->user_id = Auth::user()->id;
                $withdraw->paypal_email = $request->paypal_email;
                $withdraw->amount_usd = $amountusd;
                $withdraw->currency = isset(Auth::user()->currency->code)? Auth::user()->currency->code : "";
                $withdraw->currency_id = isset(Auth::user()->currency->id)? Auth::user()->currency->id : 0;
                // $withdraw->bank_name = $request->bank_name;
                // $withdraw->branch_name = $request->branch_name;
                // $withdraw->acc_holder_name = $request->acc_holder_name;
                // $withdraw->acc_number = $request->acc_number;
                // $withdraw->ifsc = $request->ifsc;
                $withdraw->save();


                /* Amount deduction from user wallet */ 
                User::where('id', Auth::user()->id)->decrement('wallet_amount', $request->amount);
                User::where('id', Auth::user()->id)->decrement('wallet_amount_usd', $amountusd);

                /* Return data */
                return response()->json([
                    'success' => true,
                    'message' => 'You have successfully generated a withdrawn request.',
                    "code" => 200,
                ], 200);

            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->message(), "code" => 400], 400);
            }
        }
    }
}
