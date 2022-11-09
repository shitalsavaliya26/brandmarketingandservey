<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNotificationTopic extends Model
{
    use SoftDeletes;
    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected $fillable = ['user_id','brand_id','notification_topic_id','status','reading_status','notift_at'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function notification_topic()
    {
        return $this->belongsTo(NotificationTopic::class);
    }
}
