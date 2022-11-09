<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\Brand;
use Illuminate\Database\Eloquent\SoftDeletes;

class BrandGroup extends Model
{
    //
    use SoftDeletes;

    public function subscriber()
    {
        return $this->belongsTo(Admin::class,'subscriber_id','id');
    }

    public function brands()
    {
        return $this->hasMany(Brand::class,'group_id', 'id');
    }

    public function getLogoAttribute($value){
        if(file_exists(public_path('uploads/brand/'.$value)) && $value){
           return asset('uploads/brand/'.$value); 
       }
       return '';
   }
}
