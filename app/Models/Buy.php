<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buy extends Model
{
    //
    use SoftDeletes;

    protected $fillable = ['brand_id','link'];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
