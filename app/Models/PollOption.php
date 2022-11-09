<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PollOption extends Model
{
    use SoftDeletes;


    protected $dates = [ 'deleted_at' ];

      /**
     * Get the poll that owns the option.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    protected $appends = ['percent','vote'];

    public function getPercentAttribute(){
        $answers = PollAnswer::where('poll_id',$this->poll_id)->count();
        $useranswers = PollAnswer::where('poll_option_id',$this->id)->count();

        return ($answers > 0) ? round(($useranswers * 100) / $answers,2) : 0;
    }

    public function getVoteAttribute(){
        return PollAnswer::where('poll_option_id',$this->id)->count();
    }
}
