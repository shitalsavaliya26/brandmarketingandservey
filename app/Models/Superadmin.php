<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Model;

class Superadmin extends Model
{

    protected $guard = 'superadmins';

    protected $guarded = ['id'];

    public function getAuthPassword(){
        return $this->password;

    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }
    
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }
    
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
