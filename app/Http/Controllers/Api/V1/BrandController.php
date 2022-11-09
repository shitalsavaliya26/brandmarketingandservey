<?php

namespace App\Http\Controllers\Api\V1;
use Auth;
use App\User;
use Validator;
use App\PollTitle;
use Carbon\Carbon;
use App\Models\Buy;
use App\Models\Win;
use App\Models\Poll;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Topic;
use App\Models\Message;
use App\Models\Setting;
use App\UserPollHistory;
use App\Models\PollAnswer;
use App\Models\PriceSetting;
use Illuminate\Http\Request;
use App\Models\NotificationTopic;
use App\Models\AdvertisementImage;
use App\Models\TransactionHistory;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\UserNotificationTopic;

class BrandController extends Controller
{
    /* search brands */
    public function seachBrand(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                "keyword" => 'nullable',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brands = Brand::with('settings:id,brand_id,tell,know,think,buy,win,website')->with('buy:brand_id,link')->with('win:brand_id,attachment,attachment_type,slug')->whereHas('subscriber', function ($q) {
            $q->where('wallet_amount', '>', 0)->orWhere('bonus_amount', '>', 0)->where('pause_account', null)->where('status', '1');
        })->where('status', '1')->where('country_id', auth()->user()->country_id)->with('advert');

        if ($request->has('keyword')) {
            $brands = $brands->where('name', 'LIKE', '%' . $request->keyword . '%');
        }
        $brands = $brands->get();

        return response()->json([
            'success' => true,
            'data' => $brands,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ], 200);
    }

    /* brand detail */
    public function brandDetail(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brand = Brand::whereHas('subscriber', function ($query) {
            $query->where('status', '1'); //->where('pause_account',0);
        })->where('id', $request->brand_id)->where('status', '1')->with('settings:id,brand_id,tell,know,think,buy,win,website')->first();

        return response()->json([
            'success' => true,
            'data' => $brand,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ], 200);
    }

    /* get brand message topics */
    public function messageTopics(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brand = Brand::find($request->brand_id);

        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }

        $totalbalance = ($brand->subscriber->wallet_amount + $brand->subscriber->bonus_amount);


        if ($totalbalance <= 0) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        $topics = Topic::where('brand_id', $request->brand_id)->where('status', '1')->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $topics,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ], 200);
    }

    /* send brand message */
    public function sendMessage(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
                "topic_id" => 'required|exists:topics,id',
                "description" => 'required|array',
                "attachments" => 'nullable|array',
                "reply" => 'required|array',

            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brand = Brand::find($request->brand_id);


        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }


        $totalbalance = ($brand->subscriber->wallet_amount + $brand->subscriber->bonus_amount);

        if ($totalbalance <= 0) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        $message = Message::create([
            'user_id' => auth()->user()->id,
            'brand_id' => $request->brand_id,
            'topic_id' => $request->topic_id,
            'message' => $request->description,
            'reply' => $request->reply,
        ]);

	    if(isset($request->attachments)){
        	for ($i = 0; $i < count($request->attachments); $i++) {
            	$image = $request->file('attachments')[$i];
            	$filename = time() . $i . '.' . $image->getClientOriginalExtension();
            	$image->storeAs('message_attachments', $filename);
            	$message->attachments()->create(['attachment' => $filename]);
        	}
        }

        $user = auth()->user();

        $message = Message::where('id', $message->id)->with('attachments:id,message_id,attachment')->first();
        $topic = Topic::find($request->topic_id);
        $description = '<ul><li>' . implode('</li><li>', $request->description) . '</li></ul>';
        $reply = implode('.', $request->reply);
        $email_list_to = explode(',', $topic->email_list_to);
        $email_list_cc = explode(',', $topic->email_list_cc);
        $files = $message->attachments;

        // dd($email_list_cc);
        if ($topic->email_list_to) {
            if (count($email_list_cc) > 0 && $topic->email_list_cc != '') {
                \Mail::send('backend.subscriber.emails.infoadded', ['topic' => $topic, 'description' => $description, 'reply' => $reply, 'user' => $user], function ($message) use ($email_list_to, $email_list_cc, $files, $reply, $user) {
                    $message->from($address = 'noreply@example.com', $name ='example User');
                    if($reply == 'email'){
                        $message->replyTo($user->email, $user->firstname);
                    }else{
                        $message->replyTo('noreply@example.com');
                    }
                    $message->to($email_list_to)->cc($email_list_cc)
                        ->subject("example user message");
                    foreach ($files as $file){
                        $message->attach($file->attachment);
                    }
                });
            } else {
                \Mail::send('backend.subscriber.emails.infoadded', ['topic' => $topic, 'description' => $description, 'reply' => $reply, 'user' => $user], function ($message) use ($email_list_to, $email_list_cc, $files, $reply, $user) {
                    if($reply == 'noreply' || $reply == 'phone'){
                        $message->from($address = 'noreply@example.com', $name ='example User');
                    }
                    if($reply == 'email'){
                        $message->from($address = 'timcrocompany@gmail.com', $name ='example User');
                        $message->replyTo($user->email, $user->firstname);
                    }
                    $message->to($email_list_to)
                        ->subject("example user message");
                    foreach ($files as $file){
                        $message->attach($file->attachment);
                    }
                });
            }
            // $message1 = 'test';
            // \Mail::send([], [], function($message) use($topic,$message1,$email_list_to)  {
            //         $data = [
            //             'Subject' =>'New info added ',
            //             'title' => 'New info added ',
            //             'description' => $message1
            //         ];
            //         $message->to('shital.savaliya@aipxperts.com')
            //         ->subject($data['Subject'])
            //         ->setBody($data['description'], 'text/html');
            //     });
        }
       
        $this->createTransactionHistory($request->brand_id, 'Messaging', 'message_sent', $message->toArray());

        return response()->json([
            'success' => true,
            'data' => $message,
            'message' => 'Message sent successfully.',
            "code" => 200,
        ], 200);
    }

    /* get advertisements */
    public function getAdvertisement(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brand = Brand::find($request->brand_id);


        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }


        $totalbalance = ($brand->subscriber->wallet_amount + $brand->subscriber->bonus_amount);

        if ($totalbalance <= 0) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        $adverts = AdvertisementImage::where('brand_id', $request->brand_id)->get();

        return response()->json([
            'success' => true,
            'data' => $adverts,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ]);
    }

    /* get notification topics */
    public function notificationTopics(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brand = Brand::find($request->brand_id);

        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }

        $totalbalance = ($brand->subscriber->wallet_amount + $brand->subscriber->bonus_amount);

        if ($totalbalance <= 0) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        $topics = NotificationTopic::where('brand_id', $request->brand_id)
            ->with('brand', 'images')
            ->whereDoesntHave('subscribers', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('notify_date', 'DESC')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topics,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ], 200);
    }

    /* add notification in list */
    public function addNotificationTopic(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
                "topic_id" => 'required|exists:notification_topics,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brand = Brand::find($request->brand_id);

        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }

        $totalbalance = ($brand->subscriber->wallet_amount + $brand->subscriber->bonus_amount);

        if ($totalbalance <= 0) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        $topics = UserNotificationTopic::updateOrCreate([
            'user_id' => auth()->user()->id,
            'brand_id' => $request->brand_id,
            'notification_topic_id' => $request->topic_id,
        ],
        [
            'user_id' => auth()->user()->id,
            'brand_id' => $request->brand_id,
            'notification_topic_id' => $request->topic_id,
            'status' => '1',
            'reading_status' => '0',
            'notift_at' => Carbon::now(),
        ]);

        $notificationTopic = NotificationTopic::with('brand')->find($request->topic_id);

        $userNotification = UserNotificationTopic::where('notification_topic_id', $request->topic_id)
            ->where('user_id', auth()->user()->id)->pluck('user_id')->toArray();

        if (count($userNotification) > 0) {
            $user = User::find(auth()->user()->id);
            $title = $notificationTopic->brand->name;
            $message = $notificationTopic->name;
            NotificationHelper::send_pushnotification($user, $title, $message, 0);
        }
        $this->createTransactionHistory($request->brand_id, 'Info', 'notification_topic_add', $topics->toArray());

        return response()->json([
            'success' => true,
            'data' => $topics,
            'message' => 'Notification added successfully.',
            "code" => 200,
        ], 200);
    }

    /* get user notification topics */
    public function getUserNotificationTopics(Request $request)
    {
        // $validator = Validator::make(
        //     $request->all(),
        //     [
        //         "brand_id" => 'required|exists:brands,id'
        //     ]
        // );

        // if ($validator->fails()) {
        //     return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        // }

        $topics = UserNotificationTopic::where('user_id', auth()->user()->id)
            ->with(['notification_topic' => function ($q) {
                $q->with('images');
            }])
            ->with('brand:id,name,logo')
            ->orderBy('notift_at', 'DESC')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topics,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ], 200);
    }

    /* read user notification topics */
    public function readUserNotificationTopics(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                "notification_topic_id" => 'required|exists:notification_topics,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        UserNotificationTopic::where('user_id', auth()->user()->id)
            ->where('notification_topic_id', $request->notification_topic_id)
            ->update(['reading_status' => '1']);

        return response()->json([
            'success' => true,
            'message' => 'Notification read successfully.',
            "code" => 200,
        ], 200);
    }


    
    /* Stop user notifications */
    public function stopNotification(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "notification_id" => 'required|exists:user_notification_topics,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $topics = UserNotificationTopic::where('id', $request->notification_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification removed successfully.',
            "code" => 200,
        ], 200);
    }




    /* Stop user notifications */
    public function getUserUnreadNotifications(Request $request)
    {
        $count = UserNotificationTopic::where('user_id', auth()->user()->id)
            ->where(['reading_status' => '0'])->count();

        return response()->json([
            'success' => true,
            'data' => ($count > 0) ? true : false,
            'message' => 'Notification retrieved successfully.',
            "code" => 200,
        ], 200);
    }




    /* Get buy links */
    public function getBuyLinks(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brand = Brand::find($request->brand_id);

        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }

        $totalbalance = ($brand->subscriber->wallet_amount + $brand->subscriber->bonus_amount);

        if ($totalbalance <= 0) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        $buylinks = Buy::where('brand_id', $request->brand_id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $this->createTransactionHistory($request->brand_id, 'Shop', 'buy', $buylinks->toArray());

        return response()->json([
            'success' => true,
            'data' => $buylinks,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ], 200);
    }




    /* Get win links */
    public function getWin(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brand = Brand::find($request->brand_id);

        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }

        $totalbalance = ($brand->subscriber->wallet_amount + $brand->subscriber->bonus_amount);

        if ($totalbalance <= 0) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        $win = Win::where('brand_id', $request->brand_id)
            ->orderBy('created_at', 'DESC')
            ->get();
        $this->createTransactionHistory($request->brand_id, 'Win', 'win', $win->toArray());

        return response()->json([
            'success' => true,
            'data' => $win,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ], 200);
    }



    /* Click websitelink */
    public function visitWebsite(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brand = Brand::find($request->brand_id);

        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }

        $totalbalance = ($brand->subscriber->wallet_amount + $brand->subscriber->bonus_amount);

        if ($totalbalance <= 0) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        $brand = Brand::where('id', $request->brand_id)
            ->orderBy('created_at', 'DESC')
            ->get();
        $this->createTransactionHistory($request->brand_id, 'Web Link', 'wesite_link', $brand->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ], 200);
    }





    /* Get polls */
    public function getPolls(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $brand = Brand::find($request->brand_id);


        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }

        $totalbalance = ($brand->subscriber->wallet_amount + $brand->subscriber->bonus_amount);

      
        if ($totalbalance <= 0) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        $samedeletsuser = User::onlyTrashed()->where("email",auth()->user()->email)->pluck('id')->toArray();
        $deleteduserpollget = UserPollHistory::whereIn('user_id',$samedeletsuser)->pluck('poll_title_id')->toArray();
        


        $polls = PollTitle::select('id', 'brand_id','title','total_questions','created_at')->where('brand_id', $request->brand_id)
            ->where('status', '1')->where('type',"1")->where('is_deleted','!=',"1")->where('total_questions','>','0')->whereNotIn('id',$deleteduserpollget)
            ->orderBy('created_at', 'DESC')
            ->with('brand:id,name,logo')->with(['question'=>function($q){
                $q->with('options:id,poll_id,option');
            }])->first();

          
        $alreadySubmitted = false;
        if (!$polls) {
            return response()->json([
                'success' => false,
                'isSubmitted' => $alreadySubmitted,
                'message' => 'Not found any poll.',
                "code" => 400,
            ], 400);
        }

        $poll = PollAnswer::where(['user_id' => $user->id, 'poll_title_id' => $polls->id])->first();
        if ($poll) {
            $alreadySubmitted = true;
            return response()->json([
                'success' => false,
                'data' => $polls,
                'isSubmitted' => true,
                'message' => 'Already submitted.',
                "code" => 200,
            ], 200);
        }

        // $this->createTransactionHistory($request->brand_id,'Web Link','wesite_link',$brand->toArray());

        return response()->json([
            'success' => true,
            'data' => $polls,
            'isSubmitted' => $alreadySubmitted,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ], 200);
    }




    /* Send poll answer */
    public function sendPollAnswer(Request $request)
    {
        if($request->has('poll')) {
            $request->merge(['poll' => json_decode($request->poll)]);
        }
        
        // dd(json_decode($request->poll));
        $user = auth()->user();
        $validator = Validator::make(
            $request->all(),
            [
                "poll_id" => 'required|exists:poll_titles,id',
                'poll' => 'array',
                // 'poll.*.question' => 'required|exists:polls,id',
                // 'poll.*.answer' => 'required|exists:poll_options,id',
                // "option_id" => 'required|exists:poll_options,id',
            ],[
                'poll_id.required' => 'The selected poll id is invalid.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }

        $poll = PollTitle::where('id', $request->poll_id)
            ->where('status', '1')->where('is_deleted','!=',"1")->where('type',"1")
            ->orderBy('created_at', 'DESC')
            ->first();

        $brand = Brand::find($poll->brand_id);

        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }

        $totalbalance = ($brand->subscriber->wallet_amount + $brand->subscriber->bonus_amount);

       
        if ($totalbalance <= 0) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        $subscriber = Admin::find($brand->subscriber_id);
        $pollprice = PriceSetting::where('title', "Poll Question")->first();
        $rate = isset($subscriber->currency->rate)? $subscriber->currency->rate : 1;
        $totalamount = ($pollprice->price*$poll->total_questions);

    
        /* Check amount available in subscriber wallet */
        if(($totalamount*$rate)  >  $totalbalance){
            return response()->json(['success' => false, 'message' => 'Something went wrong!', "code" => 400], 400);
        }

        /* Check poll exist */
        if (!$poll) {
            return response()->json([
                'success' => false,
                'message' => 'Poll not exist.',
                "code" => 400,
            ], 400);
        }

        /* Check poll alredy submitted by user */
        $pollanswer = PollAnswer::where(['user_id' => $user->id, 'poll_title_id' => $request->poll_id])->first();
        if ($pollanswer) {
            return response()->json([
                'success' => false,
                'message' => 'Not found any poll.',
                "code" => 400,
            ], 400);
        }


       
        foreach ($request->poll as $value) {
            $pollanswer = new Pollanswer();
            $pollanswer->poll_title_id = $request->poll_id;
            $pollanswer->user_id = $user->id;
            $pollanswer->poll_id = $value->question;
            $pollanswer->poll_option_id = $value->answer;
            $pollanswer->save();
        } 

        // $poll = PollTitle::select('id', 'brand_id','title','total_questions','created_at')->where('brand_id', $request->poll_id)
        // ->where('status', '1')->where('is_deleted','!=',"1")->where('total_questions','>','0')
        // ->orderBy('created_at', 'DESC')
        // ->with('brand:id,name,logo')->with(['question'=>function($q){
        //     $q->with('options:id,poll_id,option');
        // }])->first();

        $this->createTransactionHistorypoll($poll->brand_id, 'Poll Question', 'poll', $brand->toArray(),$poll->total_questions,$request->poll_id);

        return response()->json([
            'success' => true,
            // 'data' => $poll,
            'message' => 'Poll submitted successfully.',
            "code" => 200,
        ], 200);
    }

    /* Get poll result */
    public function getPollResult(Request $request)
    {
        $user = auth()->user();

        /* Start validation */
        $validator = Validator::make(
            $request->all(),
            [
                "brand_id" => 'required|exists:brands,id',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->first(), "code" => 400], 400);
        }
        /* End validation */

        $brand = Brand::find($request->brand_id);
        if(!$brand){
            return response()->json(['success' => false, 'message' => "Brand not found!", "code" => 400], 400);
        }

        $samedeletsuser = User::onlyTrashed()->where("email",auth()->user()->email)->pluck('id')->toArray();
        $deleteduserpollget = UserPollHistory::whereIn('user_id',$samedeletsuser)->pluck('poll_title_id')->toArray();

        /* Get Data */
        $poll = PollTitle::select('id', 'brand_id','title','total_questions','created_at','country_id')->where('brand_id', $request->brand_id)->where('type',"1")->whereNotIn('id',$deleteduserpollget)
            ->where('status', '1')->where('is_deleted','!=',"1")->where('total_questions','>','0')
            ->orderBy('created_at', 'DESC')
            ->with('brand:id,name,logo')->with(['question'=>function($q){
                $q->with('options:id,poll_id,option');
            }])->first();

           
        /* Get total active poll inside brand */
        $availablepoll = PollTitle::where('type',"1")->where('brand_id', $request->brand_id)->where('total_questions','>','0')->where('status', "1")->where('is_deleted','!=',"1")->whereHas('brand', function ($query)
        {
             $query->where('country_id', auth()->user()->country_id)->whereHas('settings', function ($query)
             {
                  $query->where('think',1);
             });
        })->pluck('id')->toArray();

        
        /* Get user submitted poll */
        $userpoll = UserPollHistory::where('user_id', auth()->user()->id)->pluck('poll_title_id')->toArray();

        /* Get avtive brand inside not submitted user poll active */
        $diff = array_diff(array_unique($availablepoll), array_unique($userpoll));

        /* Check brand inside any active poll submitted or not */
        if(empty($poll)){

           
            $userlastpoll = UserPollHistory::where('user_id', auth()->user()->id)->where('brand_id',$request->brand_id)->orderBy('id', 'DESC')->first();

            if(!empty($userlastpoll)){
                $poll = PollTitle::select('id', 'brand_id','title','total_questions','created_at','country_id')->where('brand_id', $request->brand_id)->where('id',$userlastpoll->poll_title_id)
                ->orderBy('created_at', 'DESC')
                ->with('brand:id,name,logo')->with(['question'=>function($q){
                    $q->with('options:id,poll_id,option');
                }])->first();
                $isSubmitted = true;
            }else{
                $isSubmitted = false;
            }
        }else{
            if($availablepoll === array_intersect($availablepoll, $userpoll) && $userpoll === array_intersect($userpoll, $availablepoll)) {
                $isSubmitted = true;     
            } else {
                if(!empty($diff)){
                    $isSubmitted = false;
                }
                else{
                    $isSubmitted = true;
                } 
            }
        }

        /* Return data */
        return response()->json([
            'success' => true,
            'data' => $poll,
            'isSubmitted' => $isSubmitted,
            'message' => 'Data retrieved successfully.',
            "code" => 200,
        ], 200);

    }


    /* Get user submitted poll histories */
    public function getuserpollshistories(Request $request)
    {
        /* Get user submitted poll */
        if($request->brand_id && $request->brand_id!=""){
            $userpollhistory = UserPollHistory::select('id', 'amount','brand_id','poll_title_id')->with('brand:id,name,logo')->with(['poll'=>function($q){
                $q->select('id', 'title')->with(['question'=>function($q){
                    $q->with('options:id,poll_id,option');
                }]);
            }])->where('user_id', Auth::user()->id)->where('brand_id', $request->brand_id)->orderBy('id', 'desc')->get();       
        }else{
            $userpollhistory = UserPollHistory::select('id', 'amount','brand_id','poll_title_id')->with('brand:id,name,logo')->with(['poll'=>function($q){
                $q->select('id', 'title')->with(['question'=>function($q){
                    $q->with('options:id,poll_id,option');
                }]);
            }])->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get();
        }   
    
        return response()->json([
            'success' => true,
            'data' => $userpollhistory,
            'message' => 'Data retrived.',
            "code" => 200,
        ], 200);
    }



    /* Get total available poll and brands list */
    public function getavailablepollbrands(Request $request)
    {
        /* check previous sam user email alredy submited poll */
        $samedeletsuser = User::onlyTrashed()->where("email",auth()->user()->email)->pluck('id')->toArray();
        $deleteduserpollget = UserPollHistory::whereIn('user_id',$samedeletsuser)->pluck('poll_title_id')->toArray();

        
        /* Get total active poll inside brand */
        $availablepoll = PollTitle::where('is_deleted','!=',"1")->where('total_questions','>','0')->where('status', "1")->where('type',"1")->whereNotIn('id',$deleteduserpollget)->whereHas('brand', function ($query)
        {
             $query->where('country_id', Auth::user()->country_id)->whereHas('settings', function ($query)
             {
                  $query->where('think',1);
             });
        })->pluck('id')->toArray();

        /* Get user submitted poll */
        $userpoll = UserPollHistory::where('user_id', Auth::user()->id)->pluck('poll_title_id')->toArray();

        /* Get avtive brand inside not submitted user poll active */
        $pollids = array_diff(array_unique($availablepoll), array_unique($userpoll));

        /* Get brand ids */
        $brandids = PollTitle::whereIn('id',$pollids)->pluck('brand_id')->toArray();

        /* Get brands */

        // $removedata = [];
        // foreach($brandids as $value){
        //     $setting = Setting::where('brand_id',$value)->where('think',1)->first();

        //     if(empty($setting)){
        //         array_push($removedata,$value);
        //     }
        // }

       
        // $brandids = array_diff($brandids,$removedata);

        $brands = Brand::whereIn('id',$brandids)->select('id', 'name')->whereHas('settings', function ($query)
        {
             $query->where('think',1);
        })->orderBy('id', 'desc')->get();

        /* return data */
        return response()->json([ 
            'success' => true,
            'wallet_amount' => number_format(Auth::user()->wallet_amount,2),
            'currency_symbol' => Auth::user()->getCurrencySymbolAttribute(),
            'available_poll' => Auth::user()->getAvailablePollAttribute(),
            'total_submitted_poll' => Auth::user()->getTotalSubmittedPollAttribute(),
            'minimum_withdrawal_amount' => Auth::user()->getMinimumWithdrawalAmountAttribute(),
            'success' => true,
            'data' => $brands,
            'message' => 'Data retrived.',
            "code" => 200,
        ], 200);
    }



    /* Create transaction history */
    public function createTransactionHistory($brand_id, $pay_for, $pay_fortitle, $data)
    {
        
        if(!empty($data)){
            $brand = Brand::find($brand_id);
            $subscriber = Admin::find($brand->subscriber_id);
            $rate =  isset($subscriber->currency->rate)? $subscriber->currency->rate : 0;
            $price = PriceSetting::where('title', $pay_for)->first();


            $subscriberamount = ($price->price*$rate);
            $subscriberamountusd = ($price->price);


            /* from wallet wallet */
            if($subscriber->bonus_amount == 0){

               
                TransactionHistory::create(['brand_id' => $brand_id,
                    'admin_id' => $brand->subscriber_id,
                    'amount' => ($price->price*$rate),
                    'amount_usd' => $price->price,
                    'admin_total_gross_revenue' => $price->price,
                    'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
                    'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
                    'pay_for' => $pay_fortitle,
                    'user_id' => auth()->user()->id,
                    'deducted_from' => 'wallet',
                    'submitted_data' => $data]);

                    
                /* Amount deduction from subscriber wallet */ 

                // dd(($subscriber->wallet_amount - $subscriberamount));
                $brand->subscriber()->decrement('wallet_amount',$subscriberamount);
                $brand->subscriber()->decrement('wallet_amount_usd', $price->price);

                Brand::where('id', $brand_id)->increment('spend_amount', ($subscriberamount));
                Brand::where('id', $brand_id)->increment('spend_amount_usd', ($price->price));

                return true;
            }


             /* from gift wallet */
            if($subscriber->bonus_amount > $subscriberamount){
                TransactionHistory::create(['brand_id' => $brand_id,
                'admin_id' => $brand->subscriber_id,
                'amount' => ($price->price*$rate),
                'amount_usd' => $price->price,
                // 'admin_total_gross_revenue' => $price->price,
                'admin_total_gross_revenue' => 0,
                'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
                'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
                'pay_for' => $pay_fortitle,
                'user_id' => auth()->user()->id,
                'deducted_from' => 'gift',
                'submitted_data' => $data]);
        
                /* Amount deduction from subscriber wallet */ 
                $brand->subscriber()->decrement('bonus_amount', ($price->price*$rate));
                $brand->subscriber()->decrement('bonus_amount_usd', $price->price);

                Brand::where('id', $brand_id)->increment('spend_amount', ($price->price*$rate));
                Brand::where('id', $brand_id)->increment('spend_amount_usd', ($price->price));

                return true;
            }



            /* from both wallet */
            if($subscriber->bonus_amount < $subscriberamount){
                $diffrenceamount = ($subscriberamount - $subscriber->bonus_amount);
                $diffrenceamountusd = ($subscriberamountusd - $subscriber->bonus_amount_usd);

                /* check admin grosss revenue. */
                $admingrossrevenue = $price->price;
                if($admingrossrevenue > 0){
                    $devide = ($price->price*$rate);
                    $admingrossrevenuenew = ($admingrossrevenue*$diffrenceamount)/$devide;
                }else{
                    $admingrossrevenuenew = 0;
                }

            
                 /* Create transactionHistory for subscriber from gift wallet */
                 TransactionHistory::create(['brand_id' => $brand_id,
                'admin_id' => $brand->subscriber_id,
                'amount' => ($subscriber->bonus_amount),
                'amount_usd' => $subscriber->bonus_amount_usd,
                'admin_total_gross_revenue' => 0,
                'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
                'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
                'pay_for' => $pay_fortitle,
                'user_id' => auth()->user()->id,
                'deducted_from' => 'gift',
                'is_counting' => 0,
                'submitted_data' => $data]);
 
                 /* Amount deduction from subscriber wallet */ 
                 $brand->subscriber()->decrement('bonus_amount', $subscriber->bonus_amount);
                 $brand->subscriber()->decrement('bonus_amount_usd', ($subscriber->bonus_amount_usd));


                 Brand::where('id', $brand_id)->increment('spend_amount', ($subscriber->bonus_amount));
                 Brand::where('id', $brand_id)->increment('spend_amount_usd', ($subscriber->bonus_amount_usd));


                /* Create transactionHistory for subscriber from wallet*/
                TransactionHistory::create(['brand_id' => $brand_id,
                'admin_id' => $brand->subscriber_id,
                'amount' => ($diffrenceamount),
                'amount_usd' =>  $diffrenceamountusd,
                'admin_total_gross_revenue' =>  $admingrossrevenuenew,
                'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
                'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
                'pay_for' => $pay_fortitle,
                'user_id' => auth()->user()->id,
                'deducted_from' => 'wallet',
                'submitted_data' => $data]);

                /* Amount deduction from subscriber wallet */ 
                $brand->subscriber()->decrement('wallet_amount', ($diffrenceamount ));
                $brand->subscriber()->decrement('wallet_amount_usd',  $diffrenceamountusd);

                Brand::where('id', $brand_id)->increment('spend_amount', ($diffrenceamount));
                Brand::where('id', $brand_id)->increment('spend_amount_usd', ($diffrenceamountusd));

                return true;
            }
           

        }
        return true;
    }



    /* create transaction history  for poll all question submit.*/
    public function createTransactionHistorypoll($brand_id, $pay_for, $pay_fortitle, $data,$totalquestion,$pollid)
    {
      
        if(!empty($data)){
            $brand = Brand::find($brand_id);
            $subscriber = Admin::find($brand->subscriber_id);

        
            $rate =  isset($subscriber->currency->rate)? $subscriber->currency->rate : 0;
            $price = PriceSetting::where('title', $pay_for)->first();

            $pollprice = PriceSetting::where('title', "User Poll Winning Price")->first();
            $winningamount = (($price->price*$totalquestion)*$pollprice->price)/100;


            /* Check andmin amount in USD */
            $foradmin = (100 - $pollprice->price);
            if($foradmin > 0){
                $admingrossrevenue = (($price->price*$totalquestion)*$foradmin)/100;
            }else{
                $admingrossrevenue = 0;
            }

           

            $subscriberamount = (($totalquestion*$price->price)*$rate);
            $allquestionusdamout = ($price->price*$totalquestion);

          
             /* from wallet wallet */
            if($subscriber->bonus_amount == 0){
                /* Create transactionHistory for subscriber */
                TransactionHistory::create(['brand_id' => $brand_id,
                'admin_id' => $brand->subscriber_id,
                'amount' => (($totalquestion*$price->price)*$rate),
                'amount_usd' => ($price->price*$totalquestion),
                'admin_total_gross_revenue' => $admingrossrevenue,
                'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
                'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
                'pay_for' => $pay_fortitle,
                'user_id' => auth()->user()->id,
                'deducted_from' => 'wallet',
                'submitted_data' => $data]);

                /* Amount deduction from subscriber wallet */ 
                $brand->subscriber()->decrement('wallet_amount', (($totalquestion*$price->price)*$rate));
                $brand->subscriber()->decrement('wallet_amount_usd', ($totalquestion*$price->price));

                Brand::where('id', $brand_id)->increment('spend_amount', (($totalquestion*$price->price)*$rate));
                Brand::where('id', $brand_id)->increment('spend_amount_usd', ($totalquestion*$price->price));


                PollTitle::where('id', $pollid)->increment('total_cost', (($totalquestion*$price->price)*$rate));
                PollTitle::where('id', $pollid)->increment('total_cost_usd', ($totalquestion*$price->price));

               


                 /* Conver USD to user currency */
                 $winningamountcurrency = ($winningamount*auth()->user()->currency->rate);


                 /* Create user poll history */
                 UserPollHistory::create(['brand_id' => $brand_id,
                 'user_id' => Auth::user()->id,
                 'poll_title_id'=> $pollid,
                 'amount' => $winningamountcurrency,
                 'amount_usd' => $winningamount,
                 'total_cost_usd' => ($totalquestion*$price->price),
                 'currency_id' => isset(Auth::user()->currency->id)? Auth::user()->currency->id : 0,
                 'currency' => isset(Auth::user()->currency->code)? Auth::user()->currency->code : "",
               ]);
 
                 /* Add amount in user wallet */
                 User::where('id', Auth::user()->id)->increment('wallet_amount', $winningamountcurrency);
                 User::where('id', Auth::user()->id)->increment('wallet_amount_usd', $winningamount);
 
                 $pdata = PollTitle::find($pollid);
                 PollTitle::where('id', $pollid)->increment('total_used_quantity', 1);
 
                 if(!empty($pdata->total_quantity) && ($pdata->total_quantity == ($pdata->total_used_quantity + 1))){
                     $pdata->type = "0";
                     $pdata->save();
                 }

               return true;
            }




            /* from gift wallet */
            if($subscriber->bonus_amount > $subscriberamount){
                /* Create transactionHistory for subscriber */
                TransactionHistory::create(['brand_id' => $brand_id,
                'admin_id' => $brand->subscriber_id,
                'amount' => (($totalquestion*$price->price)*$rate),
                'amount_usd' => ($price->price*$totalquestion),
                // 'admin_total_gross_revenue' => $admingrossrevenue,
                'admin_total_gross_revenue' => 0,
                'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
                'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
                'pay_for' => $pay_fortitle,
                'user_id' => auth()->user()->id,
                'deducted_from' => 'gift',
                'submitted_data' => $data]);

                /* Amount deduction from subscriber wallet */ 
                $brand->subscriber()->decrement('bonus_amount', (($totalquestion*$price->price)*$rate));
                $brand->subscriber()->decrement('bonus_amount_usd', ($totalquestion*$price->price));


                PollTitle::where('id', $pollid)->increment('total_cost', (($totalquestion*$price->price)*$rate));
                PollTitle::where('id', $pollid)->increment('total_cost_usd', ($totalquestion*$price->price));

                Brand::where('id', $brand_id)->increment('spend_amount', (($totalquestion*$price->price)*$rate));
                Brand::where('id', $brand_id)->increment('spend_amount_usd', ($totalquestion*$price->price));

                  /* Conver USD to user currency */
                  $winningamountcurrency = ($winningamount*auth()->user()->currency->rate);


                  /* Create user poll history */
                  UserPollHistory::create(['brand_id' => $brand_id,
                  'user_id' => Auth::user()->id,
                  'poll_title_id'=> $pollid,
                  'amount' => $winningamountcurrency,
                  'amount_usd' => $winningamount,
                  'total_cost_usd' => ($totalquestion*$price->price),
                  'currency_id' => isset(Auth::user()->currency->id)? Auth::user()->currency->id : 0,
                  'currency' => isset(Auth::user()->currency->code)? Auth::user()->currency->code : "",
                ]);
  
                  /* Add amount in user wallet */
                  User::where('id', Auth::user()->id)->increment('wallet_amount', $winningamountcurrency);
                  User::where('id', Auth::user()->id)->increment('wallet_amount_usd', $winningamount);
  
                  $pdata = PollTitle::find($pollid);
                  PollTitle::where('id', $pollid)->increment('total_used_quantity', 1);
  
                  if(!empty($pdata->total_quantity) && ($pdata->total_quantity == ($pdata->total_used_quantity + 1))){
                      $pdata->type = "0";
                      $pdata->save();
                  }

                return true;
            }

         



            /* from both wallet */
            if($subscriber->bonus_amount < $subscriberamount){
                $diffrenceamount = ($subscriberamount - $subscriber->bonus_amount);
                $diffrenceamountusd = ($allquestionusdamout - $subscriber->bonus_amount_usd);

              
   
                
                /* check admin grosss revenue. */
                if($admingrossrevenue > 0){
                    $devide = ($totalquestion*$price->price)*$rate;
                    $admingrossrevenuenew = ($admingrossrevenue*$diffrenceamount)/$devide;
                }else{
                    $admingrossrevenuenew = 0;
                }

            
                 /* from bonus wallet */


                 /* Create transactionHistory for subscriber from gift wallet */
                 TransactionHistory::create(['brand_id' => $brand_id,
                 'admin_id' => $brand->subscriber_id,
                 'amount' => ($subscriber->bonus_amount),
                 'amount_usd' => ( $subscriber->bonus_amount_usd),
                 'admin_total_gross_revenue' => 0,
                 'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
                 'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
                 'pay_for' => $pay_fortitle,
                 'user_id' => auth()->user()->id,
                 'deducted_from' => 'gift',
                 'is_counting' => 0,
                 'submitted_data' => $data]);
 
                 /* Amount deduction from subscriber wallet */ 
                 $brand->subscriber()->decrement('bonus_amount', $subscriber->bonus_amount);
                 $brand->subscriber()->decrement('bonus_amount_usd', ($subscriber->bonus_amount_usd));

                 Brand::where('id', $brand_id)->increment('spend_amount', $subscriber->bonus_amount);
                 Brand::where('id', $brand_id)->increment('spend_amount_usd', ($subscriber->bonus_amount_usd));

                 PollTitle::where('id', $pollid)->increment('total_cost', ($subscriber->bonus_amount));
                 PollTitle::where('id', $pollid)->increment('total_cost_usd', ($subscriber->bonus_amount_usd));



                /* from main wallet */

                /* Create transactionHistory for subscriber from wallet*/
                TransactionHistory::create(['brand_id' => $brand_id,
                'admin_id' => $brand->subscriber_id,
                'amount' => ($diffrenceamount),
                'amount_usd' => ($diffrenceamountusd),
                'admin_total_gross_revenue' => $admingrossrevenuenew,
                'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
                'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
                'pay_for' => $pay_fortitle,
                'user_id' => auth()->user()->id,
                'deducted_from' => 'wallet',
                'submitted_data' => $data]);

                /* Amount deduction from subscriber wallet */ 
                $brand->subscriber()->decrement('wallet_amount', $diffrenceamount);
                $brand->subscriber()->decrement('wallet_amount_usd',$diffrenceamountusd);

                PollTitle::where('id', $pollid)->increment('total_cost', ($diffrenceamount));
                PollTitle::where('id', $pollid)->increment('total_cost_usd', ($diffrenceamountusd));

                Brand::where('id', $brand_id)->increment('spend_amount', $diffrenceamount);
                Brand::where('id', $brand_id)->increment('spend_amount_usd', ($diffrenceamountusd));

                 /* Conver USD to user currency */
                $winningamountcurrency = ($winningamount*auth()->user()->currency->rate);


                /* Create user poll history */
                UserPollHistory::create(['brand_id' => $brand_id,
                'user_id' => Auth::user()->id,
                'poll_title_id'=> $pollid,
                'amount' => $winningamountcurrency,
                'amount_usd' => $winningamount,
                'total_cost_usd' => ($totalquestion*$price->price),
                'currency_id' => isset(Auth::user()->currency->id)? Auth::user()->currency->id : 0,
                'currency' => isset(Auth::user()->currency->code)? Auth::user()->currency->code : "",
                ]);

                /* Add amount in user wallet */
                User::where('id', Auth::user()->id)->increment('wallet_amount', $winningamountcurrency);
                User::where('id', Auth::user()->id)->increment('wallet_amount_usd', $winningamount);

                $pdata = PollTitle::find($pollid);
                PollTitle::where('id', $pollid)->increment('total_used_quantity', 1);

                if(!empty($pdata->total_quantity) && ($pdata->total_quantity == ($pdata->total_used_quantity + 1))){
                    $pdata->type = "0";
                    $pdata->save();
                }

                return true;

            }
           
        }
        return true;
    }

}
