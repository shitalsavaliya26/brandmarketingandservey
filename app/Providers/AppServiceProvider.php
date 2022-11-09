<?php

namespace App\Providers;

use App\User;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Topup;
use App\CurrencyUpdateTime;
use App\Models\PriceSetting;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            if (Auth::check()) {
            $totalSubscribers = Admin::count();
            $totalUsers = User::count();
            $totalAmount = Admin::sum('wallet_amount_usd');
            $closeSubscriptions = Admin::where('pause_account', '!=', NULL)->count();
            // $totalGrossRevenue = Topup::where('status', '1')->sum('amount') - Topup::sum('fees');
            $totalGrossRevenue = TransactionHistory::where('deducted_from','wallet')->sum('admin_total_gross_revenue');

            $apicalltoday = CurrencyUpdateTime::whereDate('created_at', Carbon::today())->count();

        
            $symbol = isset(auth()->user()->currency->symbol) ? auth()->user()->currency->symbol : "";
            $currencyrate = isset(auth()->user()->currency->rate) ?  auth()->user()->currency->rate : "";
          
            $totalAmountuser = User::sum('wallet_amount_usd');

            $totalquestionusdprice = PriceSetting::where('title', "Poll Question")->first();
            /* forsubscriber */
            $subscribertopuptotal = Topup::where('subscriber_id', Auth::user()->id)->count();
            $subscriberamounttotal = Topup::where('subscriber_id', Auth::user()->id)->sum('amount');
            $view->with('totalSubscribers', $totalSubscribers)
                ->with('totalUsers', $totalUsers)
                ->with('totalAmount', $totalAmount)
                ->with('totalGrossRevenue', $totalGrossRevenue)
                ->with('closeSubscriptions', $closeSubscriptions)
                ->with('symbol', $symbol)
                ->with('apicalltoday', $apicalltoday)
                ->with('currencyrate', $currencyrate)
                ->with('subscribertopuptotal', $subscribertopuptotal)
                ->with('totalAmountuser', $totalAmountuser)
                ->with('subscriberamounttotal', $subscriberamounttotal)
                ->with('totalquestionusdprice', $totalquestionusdprice);
            }
        });
        Schema::defaultStringLength(191);
    }
}
