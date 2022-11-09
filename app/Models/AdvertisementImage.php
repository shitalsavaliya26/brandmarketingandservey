<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementImage extends Model
{
    protected $hidden = ['created_at','updated_at','deleted_at','advertisement_id','type'];

    /**
     * Get the post that owns the comment.
     */
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

     public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getImageAttribute($value){
        if(file_exists(public_path('uploads/advertisement/'.$value)) && $value){
            return asset('uploads/advertisement/'.$value); 
        }
        return '';
    }
}
