<?php

namespace App;
use Auth;
use App\PollTitle;
use App\Models\Brand;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;

class UserPollHistory extends Model
{
    //
    protected $fillable = ['user_id','brand_id','poll_title_id','amount','amount_usd','currency_id','currency','created_at','updated_at','total_cost_usd'];

    public function brand() {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function poll() {
        return $this->belongsTo(PollTitle::class, 'poll_title_id', 'id');
    }

    protected $appends = ['currency_symbol'];

    public function getCurrencySymbolAttribute() {
        if(!Auth::check()) {
            return  "";
        }else{
            return  isset(Auth::user()->currency->symbol)? Auth::user()->currency->symbol : "$";
        }
    }

}


