<?php

namespace App\Http\Controllers\Backend\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\WithdrawHistory;
use Auth;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct(Request $request)
    {
        $this->limit = $request->limit ? $request->limit : 10;
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $withdraws = WithdrawHistory::with(['currencyname']);

        if ($request->has('status') && ($request->status != '' && $request->status != 'null')) {
            $default = '';
            if($request->status == 'all'){
                $withdraws = $withdraws;
            }else{
                $withdraws = $withdraws->where('status', $request->status);
            }
        }else{
            $withdraws = $withdraws->where('status', '2');
        }
        $withdraws = $withdraws->orderBy('id', 'desc')->paginate($this->limit);

        return view('backend.superadmin.withdraw.index', compact('withdraws'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.superadmin.withdraw.create');
    }

    public function changeWithdrawalStatus(Request $request)
    {
        
        if ($request->type == 'approve') {
            $withdrawHistory = WithdrawHistory::find($request->id);
            if (!$withdrawHistory) {
                return response()->json(['error' => 'Record not found.']);
            }
            $withdrawHistory->status = '1';
            $withdrawHistory->save();
        }
        if ($request->type == 'refuse') {
            $withdrawHistory = WithdrawHistory::find($request->id);
            if (!$withdrawHistory) {
                return response()->json(['error' => 'Record not found.']);
            }
            $withdrawHistory->status = '0';
            $withdrawHistory->reason = $request->reason;
            $withdrawHistory->save();
            Admin::where('id', $withdrawHistory->subscriber_id)->increment('wallet_amount', $withdrawHistory->amount);
            Admin::where('id', $withdrawHistory->subscriber_id)->increment('wallet_amount_usd', $withdrawHistory->amount_usd);
        }
        return response()->json(['success' => 'The withdrawal request was successfully processed.']);
    }
}
