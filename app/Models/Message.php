<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['user_id','topic_id','brand_id','message','reply'];
    protected $hidden = ['created_at','deleted_at'];
    protected $casts = ['message' => 'array','reply' => 'array'];

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }

      /**
     * Get the user that owns the comment.
     */
    public function user()
    {        
        return $this->belongsTo('App\User');
    }

     /**
     * Get the topic that owns the message.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
