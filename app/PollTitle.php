<?php

namespace App;
use Auth;
use App\Models\Poll;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Country;
use App\Models\Currency;
use App\Models\PriceSetting;
use Illuminate\Database\Eloquent\Model;

class PollTitle extends Model
{
    //
     /**
     * Get the win record associated with the brand.
     */
    public function question()
    {
        return $this->hasMany(Poll::class);
    }

    public function brand() {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function subscriber() {
        return $this->belongsTo(Admin::class, 'subscriber_id', 'id');
    }

    protected $appends = ['wallet_amount','currency_symbol','total_winning_amount'];

    public function getWalletAmountAttribute() {
        if(!Auth::check()) {
            return 0;
        }else{
            return Auth::user()->wallet_amount;
        }
    }

    public function getCurrencySymbolAttribute() {
        if(!Auth::check()) {
            return  "";
        }else{
            return  isset(Auth::user()->currency->symbol)? Auth::user()->currency->symbol : "$";
        }
    }

    public function getTotalWinningAmountAttribute() {
        if(!Auth::check()) {
            return  "";
        }else{  
            $price = PriceSetting::where('title', "Poll Question")->first();
            $pollprice = PriceSetting::where('title', "User Poll Winning Price")->first();
            $winningamount = (($price->price*$this->total_questions)*$pollprice->price)/100;
            $rate = isset(Auth::user()->currency->rate)? Auth::user()->currency->rate : 1;
            $winningamountcurrency = ($winningamount*$rate);
            return number_format($winningamountcurrency,2);
        }
    }

}
