<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollAnswer extends Model
{

    protected $fillable = ['user_id','poll_id','poll_option_id'];

    protected $hidden = ['created_at','updated_at','deleted_at'];

}
