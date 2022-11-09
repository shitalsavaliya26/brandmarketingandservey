<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Win extends Model
{
    //
    use SoftDeletes;

    protected $fillable = ['brand_id','attachment','attachment_type'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
    protected $appends = ['pdf_link'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getPdfLinkAttribute(){
        if($this->attachment_type == 'link'){
            return $this->attachment;
        }
        if($this->attachment_type == 'pdf'){
            return asset('uploads/win/'.$this->attachment);
        }
        if(file_exists(public_path('uploads/win/'.$this->slug.'.pdf'))){
            return asset('uploads/win/'.$this->slug.'.pdf'); 
        }
        return '';
    }

    public function images()
    {
        return $this->hasMany(WinImage::class);
    }

    public function getImageAttribute($value){
        if(file_exists(public_path('uploads/win/'.$value)) && $value){
            return asset('uploads/win/'.$value); 
        }
        return '';
    }
}
