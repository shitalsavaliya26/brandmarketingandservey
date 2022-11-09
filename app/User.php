<?php

namespace App;
use Auth;
use App\User;
use App\PollTitle;
use App\Models\Brand;
use App\Models\Country;
use App\Models\Setting;
use App\Models\Currency;
use App\Models\PriceSetting;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use App\Notifications\PasswordResetRequest;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verified_at', 'created_at', 'updated_at', 'deleted_at',

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['currency_symbol','total_submitted_poll','available_poll','minimum_withdrawal_amount'];

    public function getCurrencySymbolAttribute() {
        return  isset(Auth::user()->currency->symbol)? Auth::user()->currency->symbol : "$";
    }

    public function getTotalSubmittedPollAttribute() {
        return  UserPollHistory::where('user_id', $this->id)->count(); 
    }

    public function getAvailablePollAttribute() {

        $samedeletsuser = [];
        $deleteduserpollget = [];
  
        if(Auth::check()) {
         /* check previous sam user email alredy submited poll */
        $samedeletsuser = User::onlyTrashed()->where("email",auth()->user()->email)->pluck('id')->toArray();
        $deleteduserpollget = UserPollHistory::whereIn('user_id',$samedeletsuser)->pluck('poll_title_id')->toArray();

        }
       

        $availablepoll = PollTitle::where('type',"1")->where('total_questions','>','0')->where('status', "1")->where('is_deleted','!=',"1")->whereNotIn('id',$deleteduserpollget)->whereHas('brand', function ($query)
        {
             $query->where('country_id', $this->country->id)->whereHas('settings', function ($query)
             {
                  $query->where('think',1);
             });
        })->pluck('id')->toArray();


        $userpoll = UserPollHistory::where('user_id', $this->id)->pluck('poll_title_id')->toArray();
        $diff = array_diff(array_unique($availablepoll), array_unique($userpoll));
        if(!empty($diff)){
            return count($diff);
        }
        else{
            return 0;
        }        
    }

    

    public function getMinimumWithdrawalAmountAttribute() {
            if(Auth::guard('superadmins')->check() || Auth::guard('subscribers')->check()){
                return 0;
            }
            else{
                if(!Auth::check()) {
                    return 0;
                }else{
                    $setting = PriceSetting::where('title', "User Withdrawal Setting")->first();
                    $minimunamount = ($setting->price*auth()->user()->currency->rate);
                    return number_format($minimunamount,3);
                }
            }
    }


    public function getWalletAmountAttribute($value){
        return number_format($value,2);
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    /* Append Attribute*/
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetRequest($token));
    }

    public function getAvatarAttribute($value)
    {
        if (file_exists(public_path('uploads/avatar/' . $value)) && $value) {
            return asset('uploads/avatar/' . $value);
        }
        return '';
    }

    /**
     * Get the country record associated with the user.
     */

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    /**
     * Get the city record associated with the user.
     */

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function getFacebookIdAttribute($value)
    {
        if (!$value) {
            return '';
        }
        return $value;
    }

    public function getGoogleIdAttribute($value)
    {
        if (!$value) {
            return '';
        }
        return $value;
    }

    public function getAppleIdAttribute($value)
    {
        if (!$value) {
            return '';
        }
        return $value;
    }

    public function getLinkedinIdAttribute($value)
    {
        if (!$value) {
            return '';
        }
        return $value;
    }

    /**
     * Get the comments for the blog post.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
