<?php

namespace App\Http\Controllers\Backend\Subscriber;
use DB;
use Auth;
use Session;
use App\PollTitle;
use App\Models\Poll;
use App\Models\User;
use App\Models\Brand;
use App\UserPollHistory;
use App\Models\PollAnswer;
use App\Models\PollOption;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class PollController extends Controller
{
    public function __construct(Request $request)
    {
        $this->limit = $request->limit ? $request->limit : 10;
    }
    //

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* poll main title */
    public function polltitleindex(Request $request, $id)
    {
        $brandId = CustomHelper::getDecrypted($id);
        $brand = Brand::find($brandId);
        $poll = new PollTitle();
        $polls = $poll->orderBy('id', 'desc')->where('subscriber_id', Auth::user()->id)->where('brand_id', $brandId)->where('is_deleted','!=',"1")->paginate($this->limit);
        return view('backend.subscriber.poll.indextitle', compact('polls','brand'));
    }




    /* poll title create */
    public function polltitlecreate(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            $id = CustomHelper::getDecrypted($id);

         
            $request->validate([
                'title' => ['required', 'string', 'max:255'],
            ],[
                'title.required' => 'Please enter poll title.',
            ]);

            try {
                $totalpoll = PollTitle::where('brand_id',$id)->where('status', "1")->where('is_deleted','!=',"1")->pluck('id')->toArray();
                
                if(empty($totalpoll)){
                    $status = "1";
                }else{
                    $status = "0";
                }

                $brand = Brand::find($id);
                $polltitle = new PollTitle();
                $polltitle->title = $request->title;
                $polltitle->brand_id = $id;
                $polltitle->status = $status;
                $polltitle->subscriber_id =  Auth::user()->id;
                $polltitle->country_id = $brand['country_id'];
                $polltitle->save();

                $brandId = CustomHelper::getEncrypted($id);
                return redirect()->route('polltitle.index',$brandId)->with('success', 'Poll created successfully.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
        else {
            return view('backend.subscriber.poll.createtitle', compact('id'));
        }
        
    }



     /* poll title update */
     public function polltitleupdate(Request $request, $id)
     {
         $id = CustomHelper::getDecrypted($id);
         $mainpoll = PollTitle::find($id);
         if ($request->isMethod('POST')) {
             $request->validate([
                 'title' => ['required', 'string', 'max:255'],
             ],[
                 'title.required' => 'Please enter poll title.',
             ]);
 
             try {
                 $mainpoll->title = $request->title;
                 $mainpoll->save();
                 $brandId = CustomHelper::getEncrypted($mainpoll->brand_id);
                 return redirect()->route('polltitle.index',$brandId)->with('success', 'Poll updated successfully.');
             } catch (Exception $e) {
                 return redirect()->back()->with('error', $e->getMessage());
             }
         }
         else {
             return view('backend.subscriber.poll.edittitle', compact('mainpoll'));
         }
         
     }


    /* poll main title */
    public function polltitledelete(Request $request)
    {
        $id = CustomHelper::getDecrypted($request->id);
        $mainpoll = PollTitle::find($id);
        if(!empty($mainpoll)){
            $mainpoll->is_deleted = "1";
            $mainpoll->update();

            
            $questions = Poll::where('poll_title_id', $id)->get();
            foreach ($questions as $value) {
                $questions = PollOption::where('poll_id', $value->id)->delete();
            }
            Poll::where('poll_title_id', $id)->delete();


            Session::flash('success', 'Poll delete successfully.');
            return;
        }else{
            Session::flash('error', 'Poll not found!');
            return;
        }
    }

    /* delete main poll */
    public function changePollStatus(Request $request)
    {

        $poll = PollTitle::find($request->poll_id);
        if(!$poll)
        {
            return response()->json(['error'=>'Record not found.']);
        }


        $countpoll = PollTitle::where('subscriber_id', Auth::user()->id)->where('brand_id', $poll->brand_id)->where('is_deleted','!=',"1")->count();

        if($countpoll > 1){
            if($request->status == '0' ){
                PollTitle::where('brand_id', $poll->brand_id)->where('id', '!=', $request->poll_id)->update(['status' => '0']);
                $poll->status = '1';
                $poll->save();
                return response()->json(['success'=>'Poll status change successfully.']);
            }

            if($request->status == '1' ){
                return response()->json(['error'=>"You can't deatcive all poll."]);
            }
        }else{
            if($request->status == '1' ){
                return response()->json(['error'=>"You can't deatcive all poll."]);
            }else{
                $poll->status = '1';
                $poll->save();
                return response()->json(['success'=>'Poll status change successfully.']);
            }
        }
    }




     /* poll create */
     public function createNewPoll(Request $request, $id)
     {
      
       
        $id = CustomHelper::getDecrypted($id);
        $pollcount = PollTitle::where('subscriber_id', Auth::user()->id)->where('brand_id',$id)->where('is_deleted','!=',"1")->count();
        $totalpoll = ($pollcount + 1);
        $brand = Brand::find($id);
        $options = [];

        $countrycount = 0;
        if($brand){
            $countrycount = Brand::where("group_id",$brand->group_id)->count();
        }


        return view('backend.subscriber.poll.createnewpollindex', compact('options','brand','totalpoll','countrycount'));
     }


      /* poll main title */
    public function pollquantity(Request $request)
    {
       
        $id = CustomHelper::getDecrypted($request->poll_title_id);
        $polltitle = PollTitle::find($id);
        if($polltitle){

          
          
            if(empty($request->total_quantity) && empty($polltitle->total_used_quantity)){
                $polltitle->total_quantity =  $request->total_quantity;
                $polltitle->type = "1";
                $polltitle->save();
                return redirect()->back()->with('success', 'Set Target add successfully.');
            }

            if(empty($request->total_quantity) && !empty($polltitle->total_used_quantity)){
                $polltitle->total_quantity =  $request->total_quantity;
                $polltitle->type = "1";
                $polltitle->save();
                return redirect()->back()->with('success', 'Set Target add successfully.');
            }
            
           
            if(($request->total_quantity >= 1) && empty($polltitle->total_quantity) && !empty($polltitle->total_quantity)){
                $polltitle->total_quantity =  $request->total_quantity;
                $polltitle->type = "1";
                $polltitle->save();
                return redirect()->back()->with('success', 'Set Target add successfully.');
            }

            if(($request->total_quantity >= 1) && ($request->total_quantity > $polltitle->total_used_quantity)){
                $polltitle->total_quantity =  $request->total_quantity;
                $polltitle->type = "1";
                $polltitle->save();
                return redirect()->back()->with('success', 'Set Target add successfully.');
            }

            if($request->total_quantity >= 1 && ($request->total_quantity <= $polltitle->total_used_quantity)){
                return redirect()->back()->with('error', "You can't set this target because already your status is over or you can set target 0 to Unlimited.");
            }
        }
        else{
            return redirect()->back()->with('error', "Something went wrong");
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $mainid = CustomHelper::getDecrypted($id);
        $mainpoll = PollTitle::find($mainid);
        $poll = new Poll();
        $polls = $poll->orderBy('id', 'desc')->where('poll_title_id', $mainid)->paginate($this->limit);
        $userpollcount = UserPollHistory::where('poll_title_id', $mainid)->count();

        $brand = Brand::find($mainpoll->brand_id);
        return view('backend.subscriber.poll.index', compact('polls','mainpoll','userpollcount','brand'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($pollID)
    {
        $options = [];
        $mainid = CustomHelper::getDecrypted($pollID);
        $mainpoll = PollTitle::find($mainid);
        $brand = Brand::find($mainpoll->brand_id);
        return view('backend.subscriber.poll.create', compact('options','pollID','mainpoll','brand'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id=null)
    {
        
        $request->validate([
            'option' => 'required_if:answertype,[1,2]|array|min:2',
            'option.*' => 'required_if:answertype,[1,2]',
            'question' => 'required|string|max:255',
        ], [
            'option.*.required_if' => 'Minimum 2 options are required',
        ]);

        

        if($request->has("saveall") && $request->saveall == "saveall")
        {
         
                $brandid = CustomHelper::getDecrypted($request->brandid);    
                $brand = Brand::find($brandid);
                $allcountrybrand = Brand::where("group_id",$brand->group_id)->pluck('id')->toArray();
                $pollid = [];
                foreach($allcountrybrand as $value){

                    $newbrand = Brand::find($value);

                    $totalpoll = PollTitle::where('brand_id',$value)->where('status', "1")->where('is_deleted','!=',"1")->pluck('id')->toArray();
                    $pollcount = PollTitle::where('subscriber_id', Auth::user()->id)->where('brand_id',$value)->where('is_deleted','!=',"1")->count();
                    $total = ($pollcount + 1);
                    $title = "Poll ".$total;
    
                    if(empty($totalpoll)){
                        $status = "1";
                    }else{
                        $status = "0";
                    }
    
                    $polltitle = new PollTitle();
                    $polltitle->title = $title;
                    $polltitle->brand_id = $value;
                    $polltitle->status = $status;
                    $polltitle->subscriber_id =  Auth::user()->id;
                    $polltitle->total_quantity =  0;
                    $polltitle->total_used_quantity =  0;
                    $polltitle->country_id = $newbrand['country_id'];
                    $polltitle->status = $status;
                    $polltitle->total_questions = 1;
                    $polltitle->save();
    
                    array_push($pollid,$polltitle->id);

                    $poll = new Poll();
                    $poll->poll_title_id = $polltitle->id;
                    $poll->brand_id = $value;
                    $poll->question = $request->question;
                    $poll->status = '1';
                    $poll->save();   


                    if (!empty($request->option) && count(array_filter($request->option)) > 0) {
                        foreach($request->option as $options){
                            $polloptions = new PollOption();
                            $polloptions->poll_id = $poll->id;
                            $polloptions->option = $options;
                            $polloptions->save();  
                        }
                    }
                }
                $tfid = CustomHelper::getEncrypted($pollid[0]);
                return redirect()->route('poll.index', $tfid)->with('success', 'Poll created successfully.');
        }
        else{
            try {

                if (empty($id)) {
                    $brandid = CustomHelper::getDecrypted($request->brandid);    
                    $brand = Brand::find($brandid);
                    $totalpoll = PollTitle::where('brand_id',$brandid)->where('status', "1")->where('is_deleted','!=',"1")->pluck('id')->toArray();
                    $pollcount = PollTitle::where('subscriber_id', Auth::user()->id)->where('brand_id',$brandid)->where('is_deleted','!=',"1")->count();
                    $total = ($pollcount + 1);
                    $title = "Poll ".$total;
    
                    if(empty($totalpoll)){
                        $status = "1";
                    }else{
                        $status = "0";
                    }
    
                    $polltitle = new PollTitle();
                    $polltitle->title = $title;
                    $polltitle->brand_id = $brand['id'];
                    $polltitle->status = $status;
                    $polltitle->subscriber_id =  Auth::user()->id;
                    $polltitle->total_quantity =  0;
                    $polltitle->total_used_quantity =  0;
                    $polltitle->country_id = $brand['country_id'];
                    $polltitle->status = $status;
                    $polltitle->total_questions = 1;
                    $polltitle->save();
    
                    $poll = new Poll();
                    $poll->poll_title_id = $polltitle->id;
                    $poll->brand_id = $brandid;
                    $poll->question = $request->question;
                    $poll->status = '1';
                    $poll->save();                       
                }else{
    
                    $mainid = CustomHelper::getDecrypted($id);
             
                    $mainpoll = PollTitle::find($mainid);
                    if(empty($mainpoll)){
                        return redirect()->back()->with('error', "Poll not found!");
                    }
                    // Poll::where('brand_id', '=', $brandId)->update(['status' => '0']);
                    $poll = new Poll();
                    $poll->poll_title_id = $mainid;
                    $poll->brand_id = $mainpoll['brand_id'];
                    $poll->question = $request->question;
                    $poll->status = '1';
                    $poll->save();
                    PollTitle::where('id',$mainid)->increment('total_questions', 1);
    
    
                }
    
                if (!empty($request->option) && count(array_filter($request->option)) > 0) {
                    for ($i = 0; $i < count($request->option); $i++) {
                        if ($request->option[$i] != null) {
                            $options[] = [
                                'poll_id' => $poll->id,
                                'option' => $request->option[$i],
                            ];
                        }
                    }
                    PollOption::insert($options);
                }
                if (empty($id)) {
                    $tfid = CustomHelper::getEncrypted($polltitle->id);
                    return redirect()->route('poll.index', $tfid)->with('success', 'Poll created successfully.');
                }else{
                    return redirect()->route('poll.index', $id)->with('success', 'Question added successfully.');
                }
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = CustomHelper::getDecrypted($id);
        $poll = Poll::with('options')->find($id);
        $mainpoll = PollTitle::find($poll['poll_title_id']);

        $options = $poll->options;
        if (!$poll) {
            return redirect()->back()->with('error', 'No recored found.');
        }

        $brand = Brand::find($mainpoll->brand_id);
        return view('backend.subscriber.poll.edit', compact('poll', 'options','mainpoll','brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $option_string = implode(" ", $request->hidden_option);
        $option_array = explode(',', $option_string);
        $id = CustomHelper::getDecrypted($id);
        $poll = Poll::find($id);

        if (!$poll) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        
        $request->validate([
            'option' => 'required_if:answertype,[1,2]|array|min:2',
            'option.*' => 'required_if:answertype,[1,2]',
            'question' => 'required|string|max:255',
        ], [
            'option.*.required_if' => 'Minimum 2 options are required',
        ]);

        try {
            $poll->question = $request->question;
            $poll->save();

            if (!empty($option_array)) {
                PollOption::whereIn('id', $option_array)->where('poll_id', $id)->delete();
            }

            $poll_option_details = PollOption::where('poll_id', $id)->pluck('id');
            foreach ($request->option as $key => $value) {
                if (in_array($key, $poll_option_details->toArray())) {
                    $option = PollOption::find($key);
                    if ($option) {
                        $option->option = $value;
                        $option->save();
                    }
                } elseif ($value != null) {
                    PollOption::insert(
                        ['poll_id' => $id, 'option' => $value]
                    );
                }
            }
            return redirect()->route('poll.index', CustomHelper::getEncrypted($poll->poll_title_id))->with('success', 'Question updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pollHistory($id)
    {
        $pollId = CustomHelper::getDecrypted($id);
        $poll = Poll::find($pollId);
        $brand = Brand::find($poll->brand_id);
        $pollHistory = PollAnswer::leftjoin('users', 'users.id', '=', 'poll_answers.user_id')
            ->leftjoin('polls', 'polls.id', '=', 'poll_answers.poll_id')
            ->leftjoin('poll_options', 'poll_options.id', '=', 'poll_answers.poll_option_id')
            ->select(DB::raw("CONCAT(users.firstname, ' ', users.lastname) as fullname"),'polls.question', 'poll_options.option')
            ->orderBy('poll_answers.created_at', 'desc')->where('poll_answers.poll_id', $pollId)->paginate($this->limit);
        return view('backend.subscriber.poll.poll-history', compact('pollHistory','poll','brand'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pollResult($id)
    {
        $pollId = CustomHelper::getDecrypted($id);
        $pollResult = Poll::with('options')->find($pollId);
        $brand = Brand::find($pollResult->brand_id);
        $mainpoll = PollTitle::find($pollResult['poll_title_id']);
        return view('backend.subscriber.poll.poll-result', compact('pollResult','mainpoll','brand'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $poll = Poll::find($id);
        if (!$poll) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        PollOption::where('poll_id', $id)->delete();
        PollTitle::where('id',$poll['poll_title_id'])->decrement('total_questions', 1);
        $brandId = CustomHelper::getEncrypted($poll->poll_title_id);
        $poll->delete();
        return redirect()->route('poll.index', $brandId)->with('success', 'Question deleted successfully');
    }
}
