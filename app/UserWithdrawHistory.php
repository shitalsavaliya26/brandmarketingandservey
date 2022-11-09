<?php

namespace App;

use App\User;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;

class UserWithdrawHistory extends Model
{
    //
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function currencyname()
    {
        return $this->belongsTo(Currency::class,'currency_id','id');
    }
}
