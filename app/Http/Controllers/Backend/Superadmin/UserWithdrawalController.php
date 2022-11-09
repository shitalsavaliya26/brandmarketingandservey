<?php

namespace App\Http\Controllers\Backend\Superadmin;

use Auth;
use App\User;
use App\Models\Admin;
use App\UserWithdrawHistory;
use Illuminate\Http\Request;
use App\Models\WithdrawHistory;
use App\Http\Controllers\Controller;

class UserWithdrawalController extends Controller
{
    public function __construct(Request $request)
    {
        $this->limit = $request->limit ? $request->limit : 10;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $withdraws = UserWithdrawHistory::with(['currencyname','user']);

        if ($request->has('status') && ($request->status != '' && $request->status != 'null')) {
            $default = '';
            if($request->status == 'all'){
                $withdraws = $withdraws;
            }else{
                $withdraws = $withdraws->where('status', $request->status);
            }
        }else{
            $withdraws = $withdraws->where('status', 'pending');
        }
        $withdraws = $withdraws->orderBy('id', 'desc')->paginate($this->limit);
        return view('backend.superadmin.userwithdraw.index', compact('withdraws'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
    }

    public function changeWithdrawalStatus(Request $request)
    {

        if ($request->type == 'approve') {
            $withdrawHistory = UserWithdrawHistory::find($request->id);
            if (!$withdrawHistory) {
                return response()->json(['error' => 'Record not found.']);
            }
            $withdrawHistory->status = 'approved';
            $withdrawHistory->save();
        }
        if ($request->type == 'refuse') {
            $withdrawHistory = UserWithdrawHistory::find($request->id);
            if (!$withdrawHistory) {
                return response()->json(['error' => 'Record not found.']);
            }
            $withdrawHistory->status = 'rejected';
            $withdrawHistory->reason = $request->reason;
            $withdrawHistory->save();
            User::where('id', $withdrawHistory->user_id)->increment('wallet_amount', $withdrawHistory->amount);
            User::where('id', $withdrawHistory->user_id)->increment('wallet_amount_usd', $withdrawHistory->amount_usd);
        }
        return response()->json(['success' => 'The withdrawal request was successfully processed.']);
    }
}
