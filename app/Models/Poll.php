<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use SoftDeletes;

    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

}
