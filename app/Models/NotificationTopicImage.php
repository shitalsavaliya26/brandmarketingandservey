<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTopicImage extends Model
{
    use SoftDeletes;

    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function notification_topic()
    {
        return $this->belongsTo(NotificationTopic::class);
    }

    public function getImageAttribute($value){
        if($this->notification_topic->attachment_type == 'link'){
            return $value;
        }elseif(file_exists(public_path('uploads/notificationimage/'.$value)) && $value){
            return asset('uploads/notificationimage/'.$value); 
        }
        return '';
    }
}
