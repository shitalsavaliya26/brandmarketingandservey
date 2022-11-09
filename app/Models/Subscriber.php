<?php

namespace App\Models;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    
    use SoftDeletes;

    use Notifiable;

    protected $table = 'admins';

    
    /**
     * Get the country that owns the subscriber.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

      
    public function currency() {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
