<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WinImage extends Model
{
    use SoftDeletes;

    protected $hidden = ['created_at','updated_at','deleted_at'];
    
    public function getImageAttribute($value){
        if(file_exists(public_path('uploads/win/'.$value)) && $value){
           return asset('uploads/win/'.$value); 
       }
       return '';
   }
}
