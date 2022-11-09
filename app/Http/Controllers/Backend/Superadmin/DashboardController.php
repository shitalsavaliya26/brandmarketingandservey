<?php

namespace App\Http\Controllers\Backend\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Country;
use App\User;
use App\Models\PriceSetting;
use App\Models\TransactionHistory;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $subscriberCount = Admin::count();
        $userCount = User::count();

        $countries = Country::get();
        $priceSetting = PriceSetting::get();

        if ($request->startDate == null || $request->startDate == '' || $request->startDate == 'null' || $request->endDate == null || $request->endDate == '' || $request->endDate == 'null') {
            $messageHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'message_sent')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('deducted_from','wallet')->get();
            $infoHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'notification_topic_add')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('deducted_from','wallet')->get();
            $buyHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'buy')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('deducted_from','wallet')->get();
            $winHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'win')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('deducted_from','wallet')->get();
            $wesiteLinkHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'wesite_link')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('deducted_from','wallet')->get();
            $pollHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'poll')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('deducted_from','wallet')->get();
            $distributionHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'notification_distribution')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('deducted_from','wallet')->get();

            $totalRevenue = TransactionHistory::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('deducted_from','wallet')->sum('amount_usd');
        } else {
            $messageHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'message_sent');
            $infoHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'notification_topic_add');
            $buyHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'buy');
            $winHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'win');
            $wesiteLinkHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'wesite_link');
            $pollHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'poll');
            $distributionHistories = TransactionHistory::with('user:id,country_id,city_id')->where('pay_for', 'notification_distribution');

            if ($request->country_id != null && $request->country_id != '' && $request->country_id != 'null') {
                $messageHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('country_id', $request->country_id);
                });
                $infoHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('country_id', $request->country_id);
                });
                $buyHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('country_id', $request->country_id);
                });
                $winHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('country_id', $request->country_id);
                });
                $wesiteLinkHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('country_id', $request->country_id);
                });
                $pollHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('country_id', $request->country_id);
                });
                $distributionHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('country_id', $request->country_id);
                });
                $subscriberCount = Admin::where('country_id', $request->country_id)->count();
                $userCount = User::where('country_id', $request->country_id)->count();
            }


            if ($request->city_id != null && $request->city_id != '' && $request->city_id != 'null') {
                $messageHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('city_id', $request->city_id);
                });
                $infoHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('city_id', $request->city_id);
                });
                $buyHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('city_id', $request->city_id);
                });
                $winHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('city_id', $request->city_id);
                });
                $wesiteLinkHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('city_id', $request->city_id);
                });
                $pollHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('city_id', $request->city_id);
                });
                $distributionHistories->whereHas('user', function ($query) use ($request) {
                    $query->where('city_id', $request->city_id);
                });
            }

            if ($request->startDate != null && $request->startDate != '' && $request->startDate != 'null' && $request->endDate != null && $request->endDate != '' && $request->endDate != 'null') {
                $dateS = date('Y-m-d 00:00:00', strtotime($request->startDate));
                $dateE = date('Y-m-d 00:00:00', strtotime($request->endDate));

                $messageHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
                $infoHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
                $buyHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
                $winHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
                $wesiteLinkHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
                $pollHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
                $distributionHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            }
            $messageHistories = $messageHistories->where('deducted_from','wallet')->get();
            $infoHistories = $infoHistories->where('deducted_from','wallet')->get();
            $buyHistories = $buyHistories->where('deducted_from','wallet')->get();
            $winHistories = $winHistories->where('deducted_from','wallet')->get();
            $wesiteLinkHistories = $wesiteLinkHistories->where('deducted_from','wallet')->get();
            $pollHistories = $pollHistories->where('deducted_from','wallet')->get();
            $distributionHistories = $distributionHistories->where('deducted_from','wallet')->get();

            $totalRevenue = TransactionHistory::whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE)->sum('amount_usd');
        }

        $count = [];
        $count['messageHistoriesCount'] = $messageHistoriesCount = count($messageHistories);
        $count['infoHistoriesCount'] = $infoHistoriesCount = count($infoHistories);
        $count['buyHistoriesCount'] = $buyHistoriesCount = count($buyHistories);
        $count['winHistoriesCount'] = $winHistoriesCount = count($winHistories);
        $count['wesiteLinkHistoriesCount'] = $wesiteLinkHistoriesCount = count($wesiteLinkHistories);
        $count['pollHistoriesCount'] = $pollHistoriesCount = count($pollHistories);
        $count['distributionHistoriesCount'] = $distributionHistoriesCount = count($distributionHistories);
        $countHistory = array_sum($count);

        $sum = [];
        $sum['messageHistoriesSum'] = $messageHistoriesSum = $messageHistories->sum('amount_usd');
        $sum['infoHistoriesSum'] = $infoHistoriesSum = $infoHistories->sum('amount_usd');
        $sum['buyHistoriesSum'] = $buyHistoriesSum = $buyHistories->sum('amount_usd');
        $sum['winHistoriesSum'] = $winHistoriesSum = $winHistories->sum('amount_usd');
        $sum['wesiteLinkHistoriesSum'] = $wesiteLinkHistoriesSum = $wesiteLinkHistories->sum('amount_usd');
        $sum['pollHistoriesSum'] = $pollHistoriesSum = $pollHistories->sum('amount_usd');
        $sum['distributionHistoriesSum'] = $distributionHistoriesSum = $distributionHistories->sum('amount_usd');
        $totalHistory = array_sum($sum);

        $totalAmount = Admin::sum('wallet_amount_usd');

        
        
        $closeSubscriptions = Admin::where('pause_account', '!=', null)->count();
        return view('backend.superadmin.dashboard.dashboard', compact('countries', 'priceSetting', 'totalHistory', 'countHistory', 'sum', 'count', 'totalAmount', 'totalRevenue', 'subscriberCount', 'userCount', 'closeSubscriptions'));
    }

    public function getCity(Request $request)
    {
        $data['cities'] = DB::table('cities')->where("country_id", $request->country_id)
            ->get(["name", "id"]);
        return response()->json($data);
    }

}
