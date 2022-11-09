<?php

namespace App\Models;
use App\BrandGroup;
use App\PollTitle;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    //
    use SoftDeletes;

    protected $hidden = ['created_at','updated_at','deleted_at'];
    protected $appends = ['website'];
    /**
     * Get the advertisement record associated with the user.
     */
    public function advert()
    {
        return $this->hasMany(AdvertisementImage::class);
    }

    public function settings()
    {
        return $this->hasOne(Setting::class);
    }

    public function getLogoAttribute($value){
         if(file_exists(public_path('uploads/brand/'.$value)) && $value){
            return asset('uploads/brand/'.$value); 
        }
        return '';
    }
    
    public function getWebsiteAttribute($value){
    	 $setting = Setting::where('brand_id',$this->id)->first();
        if($setting && $setting->website['isShowing']=="1"){
            return $this->website_url; 
        }
        return '';
    }

    public function subscriber()
    {
        return $this->belongsTo(Admin::class,'subscriber_id','id');
    }

    /**
     * Get the win record associated with the brand.
     */
    public function buy()
    {
        return $this->hasOne(Buy::class);
    }

    public function win()
    {
        return $this->hasOne(Win::class);
    }

    /**
     * Get the transaction for the brand.
     */
    public function transactionHistory()
    {
        return $this->hasMany(TransactionHistory::class);
    }

    /**
     * Get the transaction for the brand.
     */
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
 
    /**
     * Get the win record associated with the brand.
     */
    public function polls()
    {
        return $this->hasMany(Poll::class);
    }

     /**
     * Get the win record associated with the brand.
     */
    public function mainpolls()
    {
        return $this->hasMany(PollTitle::class)->where('is_deleted','!=',"1");
    }


     /**
     * Get the win record associated with the brand.
     */
    


    /**
     * Get the win record associated with the brand.
     */
    public function notificationTopics()
    {
        return $this->hasMany(NotificationTopic::class);
    }

    public function brandgroup()
    {
        return $this->belongsTo(BrandGroup::class,'group_id','id');
    }

    public function country() {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
