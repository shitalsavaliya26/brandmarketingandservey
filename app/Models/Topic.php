<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{    
    use SoftDeletes;

    protected $hidden = ['created_at','updated_at','deleted_at'];

    /**
     * Get the messages for the topic.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
