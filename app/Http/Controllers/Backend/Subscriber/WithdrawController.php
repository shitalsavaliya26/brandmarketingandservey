<?php

namespace App\Http\Controllers\Backend\Subscriber;

use Auth;
use App\Models\Admin;
use App\Models\Topup;
use Illuminate\Http\Request;
use App\Models\WithdrawHistory;
use App\Http\Controllers\Controller;

class WithdrawController extends Controller
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
            
        $topuptotal = Topup::where('subscriber_id', Auth::user()->id)->count();
        $amounttotal = Topup::where('subscriber_id', Auth::user()->id)->sum('amount');
        if($topuptotal == 0 && ($amounttotal < Auth::user()->bonus_amount)){
            return redirect()->back()->with('error', "You haven't topup any amount,please topup amount.");
        }
        $withdraw = new WithdrawHistory();
        $withdraws = $withdraw->orderBy('id', 'desc')->where('subscriber_id', Auth::user()->id)->paginate($this->limit);
        return view('backend.subscriber.withdraw.index', compact('withdraws'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $topuptotal = Topup::where('subscriber_id', Auth::user()->id)->count();
        $amounttotal = Topup::where('subscriber_id', Auth::user()->id)->sum('amount');
        if($topuptotal == 0 && ($amounttotal < Auth::user()->bonus_amount)){
            abort(404);
        }
        return view('backend.subscriber.withdraw.create');
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

            $pendingcount = WithdrawHistory::where('subscriber_id', Auth::user()->id)->where('status',"2")->count();
            if($pendingcount > 0){
                return redirect()->route('withdraw.index')->with('error', 'Your one request is pending.');
            }

            
            // $useramount = (Auth::user()->wallet_amount - Auth::user()->bonus_amount);

            $useramount = (Auth::user()->wallet_amount);
            if($request->amount > $useramount){
                return redirect()->back()->with('error', "You can withdraw less than or equal to ".auth()->user()->currency->symbol.number_format($useramount,3));
            }
            $regex = "/^[a-zA-Z\s]+$/";
            $amountRegex = "/^[1-9]\d*(\.\d+)?$/";
            $amount = Auth::user()->wallet_amount;

            $request->validate([
                'bank_name' => array("required", "string", "min:5", "max:100", "regex:" . $regex),
                'branch_name' => array("required", "string", "min:5", "max:100", "regex:" . $regex),
                'acc_holder_name' => array("required", "string", "min:5", "max:100", "regex:" . $regex),
                'acc_number' => array("required", "string", "digits_between:5,20"),
                'amount' => array("required", "regex:" . $amountRegex, "lte:" . $amount, "gt:0"),
                'ifsc' => array("required", "string", "min:5", "max:15"),
            ], [
                'acc_number.digits_between' => 'Length of the account number must be between 5 and 20 digits.',
            ]);

            $amountusd = ($request->amount/auth()->user()->currency->rate);

            $withdraw = new WithdrawHistory();
            $withdraw->subscriber_id = Auth::user()->id;
            $withdraw->bank_name = $request->bank_name;
            $withdraw->branch_name = $request->branch_name;
            $withdraw->acc_holder_name = $request->acc_holder_name;
            $withdraw->acc_number = $request->acc_number;
            $withdraw->amount = $request->amount;
            $withdraw->ifsc = $request->ifsc;
            $withdraw->amount_usd = $amountusd;
            $withdraw->currency = isset(auth()->user()->currency->code)?auth()->user()->currency->code:0;
            $withdraw->currency_id = isset(auth()->user()->currency->id)?auth()->user()->currency->id:0;
            $withdraw->save();

            Admin::where('id', Auth::user()->id)->decrement('wallet_amount', $request->amount);
            Admin::where('id', Auth::user()->id)->decrement('wallet_amount_usd', $amountusd);

            return redirect()->route('withdraw.index')->with('success', 'You have successfully generated a withdrawn request.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
