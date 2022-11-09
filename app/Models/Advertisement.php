<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisement extends Model
{
    //
    use SoftDeletes;

    /**
     * Get the advertisement images for the advertisement.
     */
    public function advertisementImages()
    {
        return $this->hasMany(AdvertisementImage::class);
    }

    /**
     * Get the brand that owns the advertisement.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


}
