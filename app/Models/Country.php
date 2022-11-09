<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
        /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];
    
    /**
     * Get the user that owns the country.
     */
    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function city()
    {
        return $this->hasMany('App\Models\City');
    }

    /**
     * Get the subscriber for the country.
     */
    public function subscriber()
    {
        return $this->hasMany(Subscriber::class);
    }
}
