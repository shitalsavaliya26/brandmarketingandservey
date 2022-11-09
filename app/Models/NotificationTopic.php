<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTopic extends Model
{    
    use SoftDeletes;

    protected $hidden = ['created_at','deleted_at'];
    protected $appends = ['share_link','zip_link','pdf_link'];

    public function images()
    {
        return $this->hasMany(NotificationTopicImage::class);
    }

    public function subscribers()
    {
        return $this->hasMany(UserNotificationTopic::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getImageAttribute($value){
        if(file_exists(public_path('uploads/notificationimage/'.$value)) && $value){
            return asset('uploads/notificationimage/'.$value); 
        }
        return '';
    }

    public function getShareLinkAttribute(){
        if($this->attachment_type == 'pdf'){
            return $this->pdf_link;
        }elseif($this->attachment_type == 'image'){
            return $this->pdf_link;
        }elseif($this->attachment_type == 'link'){
            $image = NotificationTopicImage::where('notification_topic_id',$this->id)->first();
            return $image->image;
        }
        return url('/').'/'.$this->slug;
    }

    public function getZipLinkAttribute(){
        if(file_exists(public_path('uploads/notificationimage/'.$this->slug.'.zip'))){
            return asset('uploads/notificationimage/'.$this->slug.'.zip'); 
        }
        return '';
    }

    public function getPdfLinkAttribute(){
        if(file_exists(public_path('uploads/notificationimage/'.$this->slug.'.pdf'))){
            return asset('uploads/notificationimage/'.$this->slug.'.pdf'); 
        }
        return '';
    }
}
