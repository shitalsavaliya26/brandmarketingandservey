<?php

namespace App\Http\Controllers\Backend\Subscriber;

use Auth;
use Session;
use App\PollTitle;
use App\BrandGroup;
use App\Models\Buy;
use App\Models\Win;
use App\Models\Poll;
use App\Models\Brand;
use App\Models\Topic;
use App\Models\Country;
use App\Models\Setting;
use App\Models\WinImage;
use App\Models\PollOption;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use Illuminate\Validation\Rule;
use App\Models\NotificationTopic;
use App\Models\AdvertisementImage;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\UserNotificationTopic;
use App\Models\NotificationTopicImage;

class BrandController extends Controller
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
    public function index()
    {
        $brands = BrandGroup::with(['brands'=>function($q){
            $q->with('buy', 'win', 'advert','mainpolls');
        }])->withCount(['brands as spend' => function ($query1){
            $query1->select(DB::raw("SUM(spend_amount) as spend"))
                ->having(DB::raw("SUM(spend_amount)"),"!=",0)
                ->groupBy('group_id');
        }])->where('subscriber_id', Auth::user()->id)->orderBy('id', 'desc')->paginate($this->limit);       
        // $brand = new Brand();

        // $brands = $brand->with('buy', 'win', 'advert','mainpolls')->orderBy('id', 'desc')->where('subscriber_id', Auth::user()->id)->paginate($this->limit);

        // foreach ($brands as $brand) {
        //     $brand['spend'] = TransactionHistory::where('admin_id', Auth::user()->id)->where('brand_id', $brand->id)->sum('amount');
        // }

        return view('backend.subscriber.brand.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::toBase()->get(['id','name']);
        return view('backend.subscriber.brand.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $uniqueRule = Rule::unique('brands')->where(function ($query) use ($request) {
                return $query->where('country_id', $request->country_id ?? '')
                     ->where('subscriber_id', Auth::user()->id ?? '')
                    ->where('name', $request->name ?? '')
                    ->where('deleted_at', NULL);
            });
          
        /* validation Start */
        $request->validate([
            // 'name' => ['required', 'string', 'max:255', $uniqueRule],
            'name' => ['required', 'string', 'max:255'],
            // 'website_url' => ['required', 'regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
            'website_url' => ['required', 'regex:/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/'],
            'logo' => 'required|mimes:jpeg,jpg,png,gif|dimensions:min_width=450,min_height=250',
            'country_id' => ['required','array'],
        ], [
            'name.unique' => 'Brand is already exist.',
            'logo.dimensions' => 'Image ratio is invalid, Minimum size is 450*250.',
        ]);
        /* validation End */

       

        try {
            $brandgroup = new BrandGroup();
            $brandgroup->subscriber_id = Auth::user()->id;
            $brandgroup->name = $request->name;
            $brandgroup->website_url = $request->website_url;
            $brandgroup->status = '1';
            $brandgroup->save();


            foreach($request->country_id as $key => $country){
                $brand = new Brand();
                $brand->name = $request->name;
                $brand->website_url = $request->website_url;
                $brand->country_id = $country;
                $brand->subscriber_id = Auth::user()->id;
                $brand->status = '1';
                $brand->group_id = $brandgroup->id;
                $brand->save();
                if ($brand->id > 0) {
                    $setting = new Setting();
                    $setting->brand_id = $brand->id;
                    $setting->tell = 1;
                    $setting->know = 1;
                    $setting->think = 1;
                    $setting->buy = 1;
                    $setting->win = 1;
                    $setting->website = 1;
                    $setting->save();
                }
            }


            if ($request->logo) {
                $path = ('uploads/brand');
                if (!\File::isDirectory(public_path('uploads/brand'))) {
                    \File::makeDirectory(public_path('uploads/brand'), $mode = 0755, $recursive = true);
                }
                $file_name = time().'_'.$brandgroup->id.'_brand.' . $request->logo->getClientOriginalExtension();
                $logo = $request->logo->move(public_path('uploads/brand'), $file_name);
                Brand::where('group_id',$brandgroup->id)->update(['logo'=>$file_name]);
                BrandGroup::where('id',$brandgroup->id)->update(['logo'=>$file_name]);
            }

            return redirect()->route('brand.index')->with('success', 'Brand created successfully');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
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

        $brand = Brand::find($id);
        if (!$brand) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $countryData = Country::where('id', $brand->country_id)->first();

        $country = count((array) $countryData) > 0 ? $countryData['name'] : '';
        $countries = Country::toBase()->get(['id','name']);
        return view('backend.subscriber.brand.edit', compact('brand', 'country','countries'));
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
      
        $id = CustomHelper::getDecrypted($id);
        $brand = Brand::find($id);
        if (!$brand) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $brandgroup  = BrandGroup::find($brand->group_id);
        // $uniqueRule = Rule::unique('brands')->where(function ($query) use ($brand, $request) {
        //         return $query->where('country_id', $brand->country_id)
        //             ->where('subscriber_id', Auth::user()->id ?? '')
        //             ->where('name', $request->name)
        //             ->where('deleted_at', NULL);
        //     });
        $request->validate([
            // 'name' => ['required', 'string', 'max:255', $uniqueRule->ignore($brand->id)],
            'name' => ['required', 'string', 'max:255'],
            'website_url' => ['required', 'regex:/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/'],
            'logo' => 'mimes:jpeg,jpg,png,gif|dimensions:min_width=450,min_height=250',
        ], [
            'name.unique' => 'Brand is already exist',
            'logo.dimensions' => 'Image ratio is invalid, Minimum size is 450*250.',
        ]);

        try {
            $newbrandid = [];
            if($request->has('country_id')){
                foreach($request->country_id as $key => $country){
                    $brands = new Brand();
                    $brands->name = $request->name;
                    $brands->website_url = $request->website_url;
                    $brands->country_id = $country;
                    $brands->subscriber_id = Auth::user()->id;
                    $brands->status = ($brandgroup->status == "1") ? "1" : "0";
                    $brands->group_id = $brandgroup->id;
                    $brands->save();
                    array_push($newbrandid,$brands->id);
                    if ($brands->id > 0) {
                        $setting = new Setting();
                        $setting->brand_id = $brands->id;
                        $setting->tell = 1;
                        $setting->know = 1;
                        $setting->think = 1;
                        $setting->buy = 1;
                        $setting->win = 1;
                        $setting->website = 1;
                        $setting->save();
                    }
                }
            }
            if ($request->logo) {
                $path = ('brand');
                $file_name = time() . '_brand.' . $request->logo->getClientOriginalExtension();
                $logo = $request->logo->move(public_path('uploads/brand'), $file_name);
                $brandgroup->logo = $file_name;

                Brand::where('group_id',$brandgroup->id)->update(['logo'=>$file_name]);
            }
            else{
                Brand::where('group_id',$brandgroup->id)->update(['logo'=>basename($brandgroup->logo)]);
            }
            $brandgroup->name = $request->name;
            $brandgroup->website_url = $request->website_url;
            $brandgroup->save();

            if($request->has('country_id')){
                if($request->has('replica_brand_id')){
                    $this->createreplica($newbrandid,$request->replica_brand_id);
                }
            }
            
            Brand::where('group_id',$brandgroup->id)->update(['name'=>$request->name,'website_url'=>$request->website_url]);
          
            return redirect()->route('brand.index')->with('success', 'Brand updated successfully');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $id = CustomHelper::getDecrypted($id);
        $brand = Brand::find($id);

        $brandgroupcount =  Brand::where('group_id',$brand->group_id)->count();

        

        $path = ('uploads/brand');
        if (!$brand) {
            Session::flash('error',"No recored found.");  
            return;
        }
        // if ($brand->logo != null && file_exists(public_path($path) . '/' . $brand->getOriginal('logo'))) {
        //     unlink(public_path($path) . '/' . $brand->getOriginal('logo'));
        // }

        $advertisementImages = AdvertisementImage::where('brand_id', $id)->get();
        if ($advertisementImages->count() > 0) {
            foreach($advertisementImages as $advertisementImage){
                if ($advertisementImage->image != null && file_exists(public_path($path) . '/' . $advertisementImage->getOriginal('image'))) {
                    // unlink(public_path($path) . '/' . $advertisementImage->getOriginal('image'));
                }
                $advertisementImage->delete();
            }
        }

        $buy = Buy::where('brand_id', $id)->first();
        if ($buy) {
            $buy->delete();
        }

        $notificationTopics = NotificationTopic::where('brand_id', $id)->get();
        if ($notificationTopics->count() > 0) {
            foreach($notificationTopics as $notificationTopic){
                $topics = UserNotificationTopic::where('notification_topic_id', $notificationTopic->id)->get();
                if ($topics->count() > 0) {
                    foreach($topics as $topic){
                        $topic->delete();
                    }
                }
                $notificationTopic->delete();
            }
        }

        $total = ($brandgroupcount-1);
        if($total <= 0){
            BrandGroup::where('id',$brand->group_id)->delete();
        }
        $brand->delete();

        PollTitle::where('brand_id',$id)->update(['is_deleted'=>"1"]);

        Session::flash('success',"Brand delete successfully in ".$request->country);  
        return;
    }

    public function changeBrandStatus(Request $request)
    {
        
        $brand = Brand::find($request->brand_id);
        if (!$brand) {
            return response()->json(['error' => 'Record not found.']);
        }
        $brand->status = $request->status == '1' ? '0' : '1';
        $brand->save();

       

        $totalcount = Brand::where('group_id',$brand->group_id)->count();

        
       
        if($request->status == '1'){
            $activecount = Brand::where('group_id',$brand->group_id)->where('status',"0")->count();
            if($totalcount == $activecount){
                BrandGroup::where('id',$brand->group_id)->update(['status'=>'0']);
            }
            else{
                BrandGroup::where('id',$brand->group_id)->update(['status'=>'1']);
            }
        }

        if($request->status == '0'){
            $activecount = Brand::where('group_id',$brand->group_id)->where('status',"1")->count();
            BrandGroup::where('id',$brand->group_id)->update(['status'=>'1']);
        }


        return response()->json(['success' => 'Brand status changed successfully.']);
    }









    /* new group flow */

    public function changeBrandGroupStatus(Request $request)
    {
        $brandgroup = BrandGroup::find($request->group_id);
        if (!$brandgroup) {
            return response()->json(['error' => 'Record not found.']);
        }
        $brandgroup->status = $request->status == '1' ? '0' : '1';
        $brandgroup->save();

        Brand::where('group_id',$request->group_id)->update(['status'=>$brandgroup->status]);

        return response()->json(['success' => 'Brand status changed successfully.']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mainbrandedit($groupId)
    {
        $id = CustomHelper::getDecrypted($groupId);
        $brand = BrandGroup::find($id);
        if (!$brand) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $countryData = Country::where('id', $brand->country_id)->first();
        $country = count((array) $countryData) > 0 ? $countryData['name'] : '';
        $countries = Country::toBase()->get(['id','name']);

        $mycountry = Brand::where('group_id',$id)->select('country_id','id')->with("country")->get()->toArray();

        $countryremovedata = Brand::where('group_id',$id)->pluck('country_id')->toArray();

        return view('backend.subscriber.brand.maingrorpedit', compact('brand', 'country','countries','mycountry','countryremovedata'));
    }



    public function mainbrandupdate(Request $request, $id)
    {
        $id = CustomHelper::getDecrypted($id);

        $brandgroup = BrandGroup::find($id);
        if (!$brandgroup) {
            return redirect()->back()->with('error', 'No recored found.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'website_url' => ['required', 'regex:/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/'],
            'logo' => 'mimes:jpeg,jpg,png,gif|dimensions:min_width=450,min_height=250',
            'country_id' => ['array'],
        ], [
            'name.unique' => 'Brand is already exist',
            'logo.dimensions' => 'Image ratio is invalid, Minimum size is 450*250.',
        ]);

        try {
            $newbrandid = [];
            if($request->has('country_id')){

                foreach($request->country_id as $key => $country){
                    $brand = new Brand();
                    $brand->name = $request->name;
                    $brand->website_url = $request->website_url;
                    $brand->country_id = $country;
                    $brand->subscriber_id = Auth::user()->id;
                    $brand->status = ($brandgroup->status == "1") ? "1" : "0";
                    $brand->group_id = $brandgroup->id;
                    $brand->save();

                    array_push($newbrandid,$brand->id);

                    if ($brand->id > 0) {
                        $setting = new Setting();
                        $setting->brand_id = $brand->id;
                        $setting->tell = 1;
                        $setting->know = 1;
                        $setting->think = 1;
                        $setting->buy = 1;
                        $setting->win = 1;
                        $setting->website = 1;
                        $setting->save();
                    }
                }

            }
            if ($request->logo) {
                $path = ('brand');
                $file_name = time() . '_brand.' . $request->logo->getClientOriginalExtension();
                $logo = $request->logo->move(public_path('uploads/brand'), $file_name);
                $brandgroup->logo = $file_name;

                Brand::where('group_id',$brandgroup->id)->update(['logo'=>$file_name]);
            }
            else{
                Brand::where('group_id',$brandgroup->id)->update(['logo'=>basename($brandgroup->logo)]);
            }
            $brandgroup->name = $request->name;
            $brandgroup->website_url = $request->website_url;
            $brandgroup->save();

            Brand::where('group_id',$brandgroup->id)->update(['name'=>$request->name,'website_url'=>$request->website_url]);
          

           
            if($request->has('country_id')){
                if($request->has('replica_brand_id')){
                    $this->createreplica($newbrandid,$request->replica_brand_id);
                }
            }
            return redirect()->route('brand.index')->with('success', 'Brand updated successfully');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /* delete main brand */
    public function mainbranddelete(Request $request,$id)
    {
        $id = CustomHelper::getDecrypted($id);
        $brandGroup = BrandGroup::find($id);
        $path = ('uploads/brand');
        $brands =  Brand::where('group_id',$id)->pluck('id')->toArray();

        if (!$brandGroup) {
            Session::flash('error',"No recored found.");  
            return;
        }

        foreach($brands as $value){

            $brand = Brand::find($value);
            if ($brand->logo != null && file_exists(public_path($path) . '/' . $brand->getOriginal('logo'))) {
                // unlink(public_path($path) . '/' . $brand->getOriginal('logo'));
            }

            $advertisementImages = AdvertisementImage::where('brand_id',$brand->id)->get();
            if ($advertisementImages->count() > 0) {
                foreach($advertisementImages as $advertisementImage){
                    if ($advertisementImage->image != null && file_exists(public_path($path) . '/' . $advertisementImage->getOriginal('image'))) {
                        // unlink(public_path($path) . '/' . $advertisementImage->getOriginal('image'));
                    }
                    $advertisementImage->delete();
                }
            }

            $buy = Buy::where('brand_id',$brand->id)->first();
            if ($buy) {
                $buy->delete();
            }

            $notificationTopics = NotificationTopic::where('brand_id',$brand->id)->get();
            if ($notificationTopics->count() > 0) {
                foreach($notificationTopics as $notificationTopic){
                    $topics = UserNotificationTopic::where('notification_topic_id', $notificationTopic->id)->get();
                    if ($topics->count() > 0) {
                        foreach($topics as $topic){
                            $topic->delete();
                        }
                    }
                    $notificationTopic->delete();
                }
            }

            PollTitle::where('brand_id',$brand->id)->update(['is_deleted'=>"1"]);
            $brand->delete();
        }
        
        $brandGroup->delete();


        Session::flash('success',$request->brand." Brand delete successfully ");  
        return;
    }


   /* check brand brandExits */
    public function brandExits(Request $request)
    {
        $query = BrandGroup::where('subscriber_id', Auth::user()->id ?? '')->where('name', $request->name ?? '');
        if($request->has('group_id')){
            $query->where('id',"!=",$request->group_id);
        }
       
        $brandExits = $query->where('deleted_at', NULL)->first();

        if ($brandExits === null) {
            $isValid = true;
        } else {
            $isValid = false;
        }
        echo json_encode(array(
            'valid' => $isValid,
        ));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getcountrylist(Request $request)
    {
      
      $html = "";
      $brandgroup = BrandGroup::where('id',$request->groupid)->first();
      $brands = Brand::where('group_id',$request->groupid)->get();
      $html = view('backend.subscriber.brand.countrylist', compact('brands', 'brandgroup'))->render();
      return response()->json([
        'html' =>  $html
    ]);
    }





    /* sinle collapse brand edit */
    public function sinlecollapsebrandedit(Request $request,$brandId)
    {
        $id = CustomHelper::getDecrypted($brandId);

        $brand = Brand::find($id);
        if (!$brand) {
            return redirect()->back()->with('error', 'No recored found.');
        }

        if ($request->isMethod('POST')) {

                /* validation Start */
            $request->validate([
                'website_url' => ['required', 'regex:/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/'],
            ]);
            /* validation End */

            try {
                $brand->website_url = $request->website_url;
                $brand->save();
                return redirect()->route('brand.index')->with('success', 'Brand updated successfully');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
        else{
            return view('backend.subscriber.brand.sinlecollapsebrandedit', compact('brand'));
        }
       
    }

    /* create replica */
    public function createreplica($newbrandids,$brandid)
    {
        $brand = Brand::find($brandid);
        if(!$brand){
            return false;
        }
        foreach($newbrandids as $newbrand){
            /* create notification topic */
            $alltopic = Topic::where('brand_id',$brand->id)->get();
            foreach($alltopic as $toipcnew){
                $topic = new Topic();
                $topic->name = $toipcnew->name;
                $topic->brand_id = $newbrand;
                $topic->email_list_to = $toipcnew->email_list_to;
                $topic->email_list_cc = $toipcnew->email_list_cc;
                $topic->save();
            }

            /* shoppping */
            $buyData = Buy::where('brand_id',$brand->id)->first();
            if ($buyData) {
                $buy = new Buy();
                $buy->brand_id = $newbrand;
                $buy->link = $buyData->link;
                $buy->save();
            }


            /* Win */
            $windata = Win::where('brand_id',$brand->id)->with('images')->first();
            if ($windata) {
                $win = new Win();
                $win->brand_id = $newbrand;
                $win->slug = $windata->slug;
                $win->attachment_type = $windata->attachment_type;
                $win->attachment = $windata->attachment;
                $win->save();

                if($windata->attachment_type == "image"){
                    if(isset($windata->images) && !empty($windata->images)){
                        foreach($windata->images as $img){
                            $winImage = new WinImage;
                            $winImage->win_id = $win->id;
                            $winImage->image = basename($img->image);
                            $winImage->save();
                        }
                    }
                   
                }
            }


            /* Advertisements */
            $advertisements = AdvertisementImage::where('brand_id',$brand->id)->get();
            foreach($advertisements as $advertisement){
                $advertise = new AdvertisementImage();
                $advertise->brand_id = $newbrand;
                $advertise->image = basename($advertisement->image);
                $advertise->type  = $advertisement->type;
                $advertise->save();
            }


            /* notification-topic */
            $notificationtopics = NotificationTopic::where('brand_id',$brand->id)->with('images')->get();
            foreach($notificationtopics as $topic){
                $notification = new NotificationTopic();
                $notification->brand_id = $newbrand;
                $notification->name = $topic->name;
                $notification->attachment_type = $topic->attachment_type;
                $notification->slug = $topic->slug;
                $notification->notify_flag = '1';
                $notification->save();

                if($topic->attachment_type == "image" || $topic->attachment_type == "link"){
                    if(isset($topic->images) && !empty($topic->images)){
                        foreach($topic->images as $nimg){
                            $notificationImage = new NotificationTopicImage;
                            $notificationImage->notification_topic_id = $notification->id;
                            $notificationImage->image = basename($nimg->image);
                            $notificationImage->save();
                        }
                    }
                   
                }
            }


            /* Poll */
            $polls = PollTitle::where('brand_id',$brand->id)->with(['question' => function ($q) {
                $q->with('options');
            }])->where('is_deleted','!=',"1")->get();

           

            $nbrand = Brand::find($newbrand);

            foreach($polls as $poll){

                $totalpoll = PollTitle::where('brand_id',$newbrand)->where('status', "1")->where('is_deleted','!=',"1")->pluck('id')->toArray();
                $pollcount = PollTitle::where('brand_id',$newbrand)->where('is_deleted','!=',"1")->count();
                $total = ($pollcount + 1);
                $title = "Poll ".$total;

              
                $polltitle = new PollTitle();
                $polltitle->title = $title;
                $polltitle->brand_id = $newbrand;
                $polltitle->status = $poll->status;
                $polltitle->subscriber_id = $poll->subscriber_id;
                $polltitle->total_quantity =  0;
                $polltitle->total_used_quantity =  0;
                $polltitle->country_id = $nbrand->country_id;
                $polltitle->total_questions = $poll->total_questions;
                $polltitle->save();

                if(isset($poll->question) && !empty($poll->question)){
                    foreach($poll->question as $question){
                        $pollquestion = new Poll();
                        $pollquestion->poll_title_id = $polltitle->id;
                        $pollquestion->brand_id = $newbrand;
                        $pollquestion->question = $question->question;
                        $pollquestion->status = '1';
                        $pollquestion->save();  

                        if(isset($question->options)){
                            foreach($question->options as $options){
                                $polloptions = new PollOption();
                                $polloptions->poll_id = $pollquestion->id;
                                $polloptions->option = $options->option;
                                $polloptions->save();  
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

}