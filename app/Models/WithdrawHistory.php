<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawHistory extends Model
{
    //
      //currency
    public function currencyname()
    {
        return $this->belongsTo(Currency::class,'currency_id','id');
    }
}
