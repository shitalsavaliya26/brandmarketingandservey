<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    //
    use SoftDeletes;
    
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getTellAttribute($value){
        return ['title' => 'Tell','isShowing' => $value, 'icon' => asset('public/icons/ic_tell.png'),'id' => 1];
    }

    public function getKnowAttribute($value){
        return ['title' => 'Know','isShowing' => $value, 'icon' => asset('public/icons/ic_info.png'),'id' => 2];
    }

    public function getThinkAttribute($value){
        return ['title' => 'Think','isShowing' => $value, 'icon' => asset('public/icons/ic_poll.png'),'id' => 3];
    }

    public function getBuyAttribute($value){
    	// if($value == 1 && $this->brand->buy == null){
    	//         return ['title' => 'Buy','isShowing' => 0, 'icon' => asset('public/icons/ic_buy.png'),'id' => 4];
    	// }
        return ['title' => 'Buy','isShowing' => $value, 'icon' => asset('public/icons/ic_buy.png'),'id' => 4];
    }

    public function getWinAttribute($value){
    //    if($value == 1 && $this->brand->win == null){
    // 	        return ['title' => 'Buy','isShowing' => 0, 'icon' => asset('public/icons/ic_win.png'),'id' => 4];
    // 	}
        return ['title' => 'Win','isShowing' => $value, 'icon' => asset('public/icons/ic_win.png'),'id' => 5];
    }

    public function getWebsiteAttribute($value){
        return ['title' => 'Website','isShowing' => $value, 'icon' => '','id' => 6];
    }



}
