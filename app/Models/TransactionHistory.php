<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    protected $fillable = ['brand_id','admin_id','amount','pay_for','user_id','submitted_data','amount_usd','currency_id','currency','admin_total_gross_revenue','deducted_from','is_counting'];

    protected $casts = ['submitted_data' => 'array'];

    /**
     * Get the brand that owns the transaction history.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    
    /**
     * Get the user that owns the transaction history.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
