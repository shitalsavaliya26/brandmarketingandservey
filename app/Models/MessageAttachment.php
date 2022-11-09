<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageAttachment extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['attachment'];

    public function getAttachmentAttribute($value){
         if(file_exists(public_path('uploads/message_attachments/'.$value)) && $value){
            return asset('uploads/message_attachments/'.$value); 
        }
        return '';
    }
}
