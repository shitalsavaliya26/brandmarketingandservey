<?php

namespace App\Http\Controllers\Backend\Subscriber;

use App\BrandGroup;
use App\Models\Brand;
use App\Models\Topic;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use Illuminate\Validation\Rule;
use App\Models\MessageAttachment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class TopicController extends Controller
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
    public function index(Request $request, $id)
    {
        $brandId = CustomHelper::getDecrypted($id);
        $topic = new Topic();
        $brand = Brand::find($brandId );
        $topics = $topic->with(['messages' => function($q) {
                $q->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            }])->orderBy('name', 'asc')->where('brand_id', $brandId)->paginate($this->limit);
        return view('backend.subscriber.topic.index', compact('topics','brand'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brandId = CustomHelper::getDecrypted(request()->route('brandId'));
        $brand = Brand::find($brandId );
        $countrycount = 0;
        if($brand){
            $countrycount = Brand::where("group_id",$brand->group_id)->count();
        }
        return view('backend.subscriber.topic.create',compact("countrycount",'brand'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {

        // dd($request->all());
        $brandId = CustomHelper::getDecrypted($id);
        $brand = Brand::find($brandId);  
        if($request->has("save") && $request->save == "save"){
           
            $uniqueRule = Rule::unique('topics')->where(function ($query) use ($request, $brandId) {
                return $query->where('brand_id', $brandId)
                    ->where('name', $request->name)
                    ->where('deleted_at', NULL);
            });

            $request->validate([
                'name' => ['required', 'string', 'max:255', $uniqueRule],
                // 'email_list_cc' => 'required|min:1',
                'email_list_to' => 'required|min:1',
            ]);

            $emailListTo = explode(',', $request->email_list_to);
            $emailListCc = explode(',', $request->email_list_cc);

            $compare=array_intersect($emailListTo,$emailListCc);

            if(!empty($compare)){
                return redirect()->back()->with('error', 'You entered same mail address in email list cc and email list to.');
            }

            try {
                $topic = new Topic();
                $topic->name = $request->name;
                $topic->brand_id = $brandId;
                $topic->email_list_to = $request->email_list_to;
                $topic->email_list_cc = $request->email_list_cc;
                $topic->save();

                return redirect()->route('topic.index', $id)->with('success', 'Topic created successfully');

            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
        else{
            
                $allcountrybrand = Brand::where("group_id",$brand->group_id)->pluck('id')->toArray();
                $uniqueRule = Rule::unique('topics')->where(function ($query) use ($request,$brandId) {
                    return $query->where('brand_id', $brandId)
                        ->where('name', $request->name)
                        ->where('deleted_at', NULL);
                });
    
                $request->validate([
                    'name' => ['required', 'string', 'max:255', $uniqueRule],
                    // 'email_list_cc' => 'required|min:1',
                    'email_list_to' => 'required|min:1',
                ]);
    
                $emailListTo = explode(',', $request->email_list_to);
                $emailListCc = explode(',', $request->email_list_cc);
    
                $compare=array_intersect($emailListTo,$emailListCc);
    
                if(!empty($compare)){
                    return redirect()->back()->with('error', 'You entered same mail address in email list cc and email list to.');
                }
    
                try {
                    foreach($allcountrybrand as $value){
                        $topic = Topic::where('name',$request->name)->where('brand_id',$value)->first();
                        if($topic){
                            $topic->name = $request->name;
                            $topic->brand_id = $value;
                            $topic->email_list_to = $request->email_list_to;
                            $topic->email_list_cc = $request->email_list_cc;
                            $topic->save();
                        }else{
                            $topic = new Topic();
                            $topic->name = $request->name;
                            $topic->brand_id = $value;
                            $topic->email_list_to = $request->email_list_to;
                            $topic->email_list_cc = $request->email_list_cc;
                            $topic->save();
                        }
                    }
                    return redirect()->route('topic.index', $id)->with('success', 'Topic created successfully for all country.');
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

        $topic = Topic::find($id);

       
        if (!$topic) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $brand = Brand::find($topic->brand_id );
        return view('backend.subscriber.topic.edit', compact('topic','brand'));
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

        $topic = Topic::find($id);
        if (!$topic) {
            return redirect()->back()->with('error', 'No recored found.');
        }

        $uniqueRule = Rule::unique('topics')->where(function ($query) use ($topic, $request) {
            return $query->where('brand_id', $topic->brand_id)
                ->where('name', $request->name)
                ->where('deleted_at', NULL);
        });

        $request->validate([
            'name' => ['required', 'string', 'max:255', $uniqueRule->ignore($topic->id)],
            'email_list_to' => 'required|min:1',
            // 'email_list_cc' => 'required|min:1',
        ]);

        $emailListTo = explode(',', $request->email_list_to);
        $emailListCc = explode(',', $request->email_list_cc);

        $compare=array_intersect($emailListTo,$emailListCc);

        if(!empty($compare)){
            return redirect()->back()->with('error', 'You entered same mail address in email list cc and email list to.');
        }
        try {
            $topic->name = $request->name;
            $topic->email_list_to = $request->email_list_to;
            $topic->email_list_cc = $request->email_list_cc;
            $topic->save();

            $brandId = CustomHelper::getEncrypted($topic->brand_id);

            return redirect()->route('topic.index', $brandId)->with('success', 'Topic updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function messageHistory(Request $request, $id)
    {
        $topicId = CustomHelper::getDecrypted($id);
        $message = new Message();
        $topic = Topic::find($topicId);
    
        $brand = Brand::find($topic->brand_id );
        $messages = $message->with(['user', 'attachments'])->orderBy('id', 'desc')->where('topic_id', $topicId)->paginate($this->limit);
        return view('backend.subscriber.topic.message-history', compact('messages','topic','brand'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewAttachment(Request $request, $id)
    {
        $messageId = CustomHelper::getDecrypted($id);
        $attachment = new MessageAttachment();

        $attachments = $attachment->where('message_id', $messageId)->get();

        foreach ($attachments as $document) {
            $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief', 'jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd', 'JPG', 'JPEG', 'GIF', 'PNG', 'BMP', 'SVG', 'SVGZ', 'CGM', 'DJV', 'DJVU', 'ICO', 'IEF','JPE', 'PBM', 'PGM', 'PNM', 'PPM', 'RAS', 'RGB', 'TIF', 'TIFF', 'WBMP', 'XBM', 'XPM', 'XWD'];
            $audioExtensions = ['mp3', 'MP3'];
            $explodeDocument = explode('.', $document->attachment);
            $extension = end($explodeDocument);
            if (in_array($extension, $imageExtensions)) {
                $image = 'true';
                return view('backend.subscriber.topic.view-attachment', compact('attachments' , 'image'));
            } elseif (in_array($extension, $audioExtensions)) {
                $audio = 'true';
                return view('backend.subscriber.topic.view-attachment', compact('attachments', 'audio'));
            } else {
                return Redirect::away($image->attachment);
            }
        }
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
        $topic = Topic::find($id);
        if (!$topic) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $brandId = CustomHelper::getEncrypted($topic->brand_id);
        $topic->delete();
        return redirect()->route('topic.index', $brandId)->with('success', 'Topic deleted successfully');
    }

    public function changeTopicStatus(Request $request)
    {
        $topic = Topic::find($request->topic_id);
        if (!$topic) {
            return response()->json(['error' => 'Record not found.']);
        }
        $topic->status = $request->status == '1' ? '0' : '1';
        $topic->save();

        return response()->json(['success' => 'Topic status changed successfully.']);
    }
}
