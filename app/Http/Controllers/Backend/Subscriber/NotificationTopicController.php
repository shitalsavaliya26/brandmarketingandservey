<?php

namespace App\Http\Controllers\Backend\Subscriber;

use PDF;
use File;
use App\User;
use ZipArchive;
use Carbon\Carbon;
use App\Models\Brand;
use Illuminate\Support\Str;
use App\Models\PriceSetting;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use Illuminate\Validation\Rule;
use App\Models\NotificationTopic;
use App\Models\TransactionHistory;
use Illuminate\Support\Collection;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use App\Models\UserNotificationTopic;
use App\Models\NotificationTopicImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Admin;

class NotificationTopicController extends Controller
{
    public function __construct(Request $request)
    {
        $this->limit = $request->limit ? $request->limit : 20;
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
        $notificationTopic = new NotificationTopic();
        $brand = Brand::find($brandId);
        $notificationTopics = $notificationTopic->with('images')->orderBy('id', 'desc')->where('brand_id', $brandId)->paginate($this->limit);

        // foreach ($notificationTopics as $rowNotification) {
        //     $messageDistribution = 0.0;
        //     $notificationTopicAdd = 0.0;

        //     $distributionHistory = TransactionHistory::where('pay_for', 'notification_distribution')->where('brand_id', $brandId)->get();
        //     foreach ($distributionHistory as $row) {
        //         if ($row->submitted_data['id'] == $rowNotification->id) {
        //             $messageDistribution += $row->amount;
        //         }
        //     }

        //     $addHistory = TransactionHistory::where('pay_for', 'notification_topic_add')->where('brand_id', $brandId)->get();
        //     foreach ($addHistory as $row) {
        //         if ($row->submitted_data['notification_topic_id'] == $rowNotification->id) {
        //             $notificationTopicAdd += $row->amount;
        //         }
        //     }

        //     $rowNotification['message_distribution'] = $messageDistribution;
        //     $rowNotification['notification_topic_add'] = $notificationTopicAdd;
        // }
        $price['message_distribution'] = PriceSetting::where('title', 'Info Distribute')->first()->price;
        $price['notification_topic_add'] = PriceSetting::where('title', 'Info')->first()->price;
        return view('backend.subscriber.notification-topic.index', compact('notificationTopics', 'price','brand'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brandId = CustomHelper::getDecrypted(request()->route('brandId'));
        $brand = Brand::find($brandId);
        $countrycount = 0;
        if($brand){
            $countrycount = Brand::where("group_id",$brand->group_id)->count();
        }
        return view('backend.subscriber.notification-topic.create',compact("countrycount",'brand'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {

        $brandId = CustomHelper::getDecrypted($id);
        $brand = Brand::find($brandId);  

        if($request->has("save") && $request->save == "save"){
        $uniqueRule = Rule::unique('notification_topics')->where(function ($query) use ($request, $brandId) {
            return $query->where('brand_id', $brandId)
                ->where('name', $request->name)
                ->where('deleted_at', NULL);
        });

        $request->validate([
            'name' => ['required', 'string', 'max:255', $uniqueRule],
            'pdf' => 'required_if:attachment_type,pdf|mimes:pdf',
            'form' => 'required_if:attachment_type,image|array',
            'form.file' => 'required_if:attachment_type,image|array',
            'link' => 'required_if:attachment_type,link',
        ], [
            'form.required_if' => 'Please select attachment',
        ]);
            try {
                $notificationTopic = new NotificationTopic();
                $notificationTopic->brand_id = $brandId;
                $notificationTopic->name = $request->name;
                $notificationTopic->attachment_type = $request->attachment_type;
                $notificationTopic->slug = Str::slug($request->name.Str::random(10), '-');
                $notificationTopic->notify_flag = '1';

                // if ($request->hasfile('image')) {
                //     $path = ('uploads/notificationimage');
                //     if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                //         \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                //     }
                //     $file_name = time() . '_notificationimage.' . $request->image->getClientOriginalExtension();
                //     $image = $request->image->move(public_path('uploads/notificationimage'), $file_name);
                //     $notificationTopic->image = $file_name;
                // }
                $notificationTopic->save();

                if ($request->hasfile('pdf') && $request->attachment_type == 'pdf') {
                    $path = ('uploads/notificationimage');
                    if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                        \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                    }
                    $file_name = $notificationTopic->slug . '.pdf';
                    $pdf = $request->pdf->move(public_path('uploads/notificationimage'), $file_name);
                }
                if ($request->attachment_type == 'link') {
                    $notificationTopicImage = new NotificationTopicImage;
                    $notificationTopicImage->notification_topic_id = $notificationTopic->id;
                    $notificationTopicImage->image = $request->link;
                    $notificationTopicImage->save();
                }

                if ($request->attachment_type == 'image') {
                    $data = $request->all();
                    $image = $data['form']['file'];
                    $totalImages = count($data['form']['file']);
                    $validImageCount = 0;
                    if ($image) {
                        $validExtension = ['jpg', 'jpeg', 'png'];
                        foreach ($image as $file) {
                            $image_parts = explode(";base64,", $file);
                            $image_type_aux = explode("image/", $image_parts[0]);
                            if (key_exists(1, $image_type_aux)) {
                                if (in_array($image_type_aux[1], $validExtension)) {
                                    $validImageCount++;
                                }
                            }
                        }
                    }

                    if ($totalImages == $validImageCount) {
                        $image = $data['form']['file'];
                        if ($image) {
                            if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                                \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                            }
                            foreach ($image as $file) {
                                $image_parts = explode(";base64,", $file);
                                $image_type_aux = explode("image/", $image_parts[0]);
                                $image_type = key_exists(1, $image_type_aux) ? $image_type_aux[1] : time();
                                $image_base64 = base64_decode($image_parts[1]);

                                $renamed = time() . rand() . '_notificationimage.' . $image_type;
                                file_put_contents(public_path('uploads/notificationimage/') . $renamed, $image_base64);

                                $notificationTopicImage = new NotificationTopicImage;
                                $notificationTopicImage->notification_topic_id = $notificationTopic->id;
                                $notificationTopicImage->image = $renamed;
                                $notificationTopicImage->save();
                                $request->session()->pull('uploadImage', $image);
                            }
                        }
                    } else {
                        return redirect()->route('notification-topic.index', $id)->with('error', 'Please upload only jpg, jpeg and png files');
                    }

                    $notificationTopicImages = NotificationTopicImage::where('notification_topic_id', $notificationTopic->id)->get();

                    $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf/notification-topic', ['notificationTopicImages' => $notificationTopicImages, 'notificationName' => $notificationTopic->name]);

                    if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                        \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                    }
                    $file_name = $notificationTopic->slug . '.pdf';
                    $content = $pdf->output();

                    file_put_contents(public_path('uploads/notificationimage/') . $notificationTopic->slug . '.pdf', $content);

                    $zip = new ZipArchive;

                    $fileName = $notificationTopic->slug . '.zip';

                    $notificationTopicImages = NotificationTopicImage::where('notification_topic_id', $notificationTopic->id)->get();
                    $array = [];
                    foreach ($notificationTopicImages as $item) {
                        $array[] = $item->getRawOriginal('image');
                    }

                    if ($zip->open(public_path('uploads/notificationimage/' . $fileName), ZipArchive::CREATE) === true) {
                        $files = File::files(public_path('uploads/notificationimage/'));
                        foreach ($files as $key => $value) {
                            $filename = $value->getFilename();
                            if (in_array($filename, $array)) {
                                $relativeNameInZipFile = basename($value);
                                $zip->addFile($value, $relativeNameInZipFile);
                            }
                        }
                        $zip->close();
                    }
                }

                return redirect()->route('notification-topic.index', $id)->with('success', 'Notification topic created successfully');

            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
        else{

            $allcountrybrand = Brand::where("group_id",$brand->group_id)->pluck('id')->toArray();

            $uniqueRule = Rule::unique('notification_topics')->where(function ($query) use ($request, $brandId) {
                return $query->where('brand_id', $brandId)
                    ->where('name', $request->name)
                    ->where('deleted_at', NULL);
            });
    
            $request->validate([
                'name' => ['required', 'string', 'max:255', $uniqueRule],
                'pdf' => 'required_if:attachment_type,pdf|mimes:pdf',
                'form' => 'required_if:attachment_type,image|array',
                'form.file' => 'required_if:attachment_type,image|array',
                'link' => 'required_if:attachment_type,link',
            ], [
                'form.required_if' => 'Please select attachment',
            ]);



            try {

            $pdftopic = [];
            foreach($allcountrybrand as $keyindex => $value){

                $notificationTopic = new NotificationTopic();
                $notificationTopic->brand_id = $value;
                $notificationTopic->name = $request->name;
                $notificationTopic->attachment_type = $request->attachment_type;
                $notificationTopic->slug = Str::slug($request->name.Str::random(10), '-');
                $notificationTopic->notify_flag = '1';

                // if ($request->hasfile('image')) {
                //     $path = ('uploads/notificationimage');
                //     if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                //         \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                //     }
                //     $file_name = time() . '_notificationimage.' . $request->image->getClientOriginalExtension();
                //     $image = $request->image->move(public_path('uploads/notificationimage'), $file_name);
                //     $notificationTopic->image = $file_name;
                // }
                $notificationTopic->save();

                array_push($pdftopic,$notificationTopic->id);

                if($keyindex == 0){
                    if ($request->hasfile('pdf') && $request->attachment_type == 'pdf') {
                        $path = ('uploads/notificationimage');
                        if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                            \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                        }
                        $file_name = $notificationTopic->slug . '.pdf';
                        $pdf = $request->pdf->move(public_path('uploads/notificationimage'), $file_name);
                    }
                }
              
                
                if ($request->attachment_type == 'link') {
                    $notificationTopicImage = new NotificationTopicImage;
                    $notificationTopicImage->notification_topic_id = $notificationTopic->id;
                    $notificationTopicImage->image = $request->link;
                    $notificationTopicImage->save();
                }

                if ($request->attachment_type == 'image') {
                    $data = $request->all();
                    $image = $data['form']['file'];
                    $totalImages = count($data['form']['file']);
                    $validImageCount = 0;
                    if ($image) {
                        $validExtension = ['jpg', 'jpeg', 'png'];
                        foreach ($image as $file) {
                            $image_parts = explode(";base64,", $file);
                            $image_type_aux = explode("image/", $image_parts[0]);
                            if (key_exists(1, $image_type_aux)) {
                                if (in_array($image_type_aux[1], $validExtension)) {
                                    $validImageCount++;
                                }
                            }
                        }
                    }

                    if ($totalImages == $validImageCount) {
                        $image = $data['form']['file'];
                        if ($image) {
                            if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                                \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                            }
                            foreach ($image as $file) {
                                $image_parts = explode(";base64,", $file);
                                $image_type_aux = explode("image/", $image_parts[0]);
                                $image_type = key_exists(1, $image_type_aux) ? $image_type_aux[1] : time();
                                $image_base64 = base64_decode($image_parts[1]);

                                $renamed = time() . rand() . '_notificationimage.' . $image_type;
                                file_put_contents(public_path('uploads/notificationimage/') . $renamed, $image_base64);

                                $notificationTopicImage = new NotificationTopicImage;
                                $notificationTopicImage->notification_topic_id = $notificationTopic->id;
                                $notificationTopicImage->image = $renamed;
                                $notificationTopicImage->save();
                                $request->session()->pull('uploadImage', $image);
                            }
                        }
                    } else {
                        return redirect()->route('notification-topic.index', $id)->with('error', 'Please upload only jpg, jpeg and png files');
                    }

                    $notificationTopicImages = NotificationTopicImage::where('notification_topic_id', $notificationTopic->id)->get();

                    $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf/notification-topic', ['notificationTopicImages' => $notificationTopicImages, 'notificationName' => $notificationTopic->name]);

                    if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                        \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                    }
                    $file_name = $notificationTopic->slug . '.pdf';
                    $content = $pdf->output();

                    file_put_contents(public_path('uploads/notificationimage/') . $notificationTopic->slug . '.pdf', $content);

                    $zip = new ZipArchive;

                    $fileName = $notificationTopic->slug . '.zip';

                    $notificationTopicImages = NotificationTopicImage::where('notification_topic_id', $notificationTopic->id)->get();
                    $array = [];
                    foreach ($notificationTopicImages as $item) {
                        $array[] = $item->getRawOriginal('image');
                    }

                    if ($zip->open(public_path('uploads/notificationimage/' . $fileName), ZipArchive::CREATE) === true) {
                        $files = File::files(public_path('uploads/notificationimage/'));
                        foreach ($files as $key => $value) {
                            $filename = $value->getFilename();
                            if (in_array($filename, $array)) {
                                $relativeNameInZipFile = basename($value);
                                $zip->addFile($value, $relativeNameInZipFile);
                            }
                        }
                        $zip->close();
                    }
                }
            }


            if ($request->hasfile('pdf') && $request->attachment_type == 'pdf') {
                $slug = NotificationTopic::find($pdftopic[0]);
                NotificationTopic::whereIn('id',$pdftopic)->update(['slug'=>$slug->slug]);
            }
           

            return redirect()->route('notification-topic.index', $id)->with('success', 'Notification topic created successfully');

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

        $notificationTopic = NotificationTopic::find($id);
        if (!$notificationTopic) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $brand = Brand::find($notificationTopic->brand_id);
        return view('backend.subscriber.notification-topic.edit', compact('notificationTopic','brand'));
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
        // return $request;
        $id = CustomHelper::getDecrypted($id);

        $notificationTopic = NotificationTopic::find($id);
        $previoustype = $notificationTopic->attachment_type;

        if (!$notificationTopic) {
            return redirect()->back()->with('error', 'No recored found.');
        }

        $uniqueRule = Rule::unique('notification_topics')->where(function ($query) use ($notificationTopic, $request) {
            return $query->where('brand_id', $notificationTopic->brand_id)
                ->where('name', $request->name)
                ->where('deleted_at', NULL);
        });

        $request->validate([
            'name' => ['required', 'string', 'max:255', $uniqueRule->ignore($notificationTopic->id)],
            'pdf' => 'mimes:pdf',
            // 'image' => 'mimes:jpeg,jpg,png,gif',
        ]);

        if ($previoustype != $request->attachment_type) {
            $request->validate([
                'pdf' => 'required_if:attachment_type,pdf|mimes:pdf',
                'form' => 'required_if:attachment_type,image|array',
                'form.file' => 'required_if:attachment_type,image|array',
                'link' => 'required_if:attachment_type,link',
            ], [
                'form.required_if' => 'Please select attachment',
            ]);
        }
        try {
            $notificationTopic->name = $request->name;
            $notificationTopic->notify_flag = '1';

            // if ($request->image) {
            //     $path = ('notificationimage');
            //     if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
            //         \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
            //     }
            //     $file_name = time() . '_notificationimage.' . $request->image->getClientOriginalExtension();
            //     $image = $request->image->move(public_path('uploads/notificationimage'), $file_name);
            //     $notificationTopic->image = $file_name;
            // }

            if ($request->attachment_type == 'link') {
                $notificationTopicImages = NotificationTopicImage::where('notification_topic_id', $id)->get();
                foreach ($notificationTopicImages as $image) {
                    if (file_exists(public_path('uploads/notificationimage') . '/' . $image->getRawOriginal('image'))) {
                        // unlink(public_path('uploads/notificationimage') . '/' . $image->getRawOriginal('image'));
                    }
                }
                $notificationTopicImage = NotificationTopicImage::where('notification_topic_id', $id)->first();
                $notificationTopicImage = ($notificationTopicImage) ? $notificationTopicImage : new NotificationTopicImage;
                $notificationTopicImage->notification_topic_id = $notificationTopic->id;
                $notificationTopicImage->image = $request->link;
                NotificationTopicImage::where('notification_topic_id', $id)->where('id', '!=', $notificationTopicImage->id)->delete();
                $notificationTopicImage->save();
                $file_name = $notificationTopic->slug . '.pdf';

                if (file_exists(public_path('uploads/notificationimage') . '/' . $file_name)) {
                    // unlink(public_path('uploads/notificationimage') . '/' . $file_name);
                }
            }

            if ($request->pdf && $request->attachment_type == 'pdf') {
                $notificationTopicImages = NotificationTopicImage::where('notification_topic_id', $id)->get();
                foreach ($notificationTopicImages as $image) {
                    if ($image->image != '') {
                        if (file_exists(public_path('uploads/notificationimage') . '/' . $image->getRawOriginal('image'))) {
                            // unlink(public_path('uploads/notificationimage') . '/' . $image->getRawOriginal('image'));
                        }
                    }
                }
                NotificationTopicImage::where('notification_topic_id', $id)->delete();
                $path = ('uploads/notificationimage');
                if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                    \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                }
                $file_name = $notificationTopic->slug . '.pdf';

                // if (file_exists(public_path('uploads/notificationimage') . '/' . $file_name)) {
                //     unlink(public_path('uploads/notificationimage') . '/' . $file_name);
                // }

                $pdf = $request->pdf->move(public_path('uploads/notificationimage'), $file_name);
            }

            $brandId = CustomHelper::getEncrypted($notificationTopic->brand_id);
            $data = $request->all();
            if (isset($data['remove_img']) && $data['remove_img'] != '') {
                $removeImage = explode(",", $data['remove_img']);
                foreach ($removeImage as $image) {
                    NotificationTopicImage::find($image)->delete();
                }

                $notificationTopicImages = NotificationTopicImage::where('notification_topic_id', $id)->get();

                    $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf/notification-topic', ['notificationTopicImages' => $notificationTopicImages, 'notificationName' => $notificationTopic->name]);

                    if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                        \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                    }
                    $file_name = $notificationTopic->slug . '.pdf';

                    if (file_exists(public_path('uploads/notificationimage') . '/' . $file_name)) {
                        // unlink(public_path('uploads/notificationimage') . '/' . $file_name);
                    }
                    $content = $pdf->output();

                file_put_contents(public_path('uploads/notificationimage/') . $notificationTopic->slug . '.pdf', $content);
            }

            $notificationTopic->attachment_type = $request->attachment_type;
            $notificationTopic->save();

            if (isset($data['form']) && $request->attachment_type == 'image') {
                if ($previoustype == 'link') {
                    NotificationTopicImage::where('notification_topic_id', $id)->delete();
                }
                $image = $data['form']['file'];
                $totalImages = count($data['form']['file']);
                $validImageCount = 0;
                if ($image) {
                    $validExtension = ['jpg', 'jpeg', 'png'];
                    foreach ($image as $file) {
                        $image_parts = explode(";base64,", $file);
                        $image_type_aux = explode("image/", $image_parts[0]);
                        if (key_exists(1, $image_type_aux)) {
                            if (in_array($image_type_aux[1], $validExtension)) {
                                $validImageCount++;
                            }
                        }
                    }
                }
                if ($totalImages == $validImageCount) {
                    $images = $request->session()->get('uploadImage');
                    if ($request->session()->get('uploadImage') != '') {
                        foreach ($images as $image) {
                            $notificationTopicImage = new NotificationTopicImage;
                            $notificationTopicImage->notification_topic_id = $notificationTopic->id;
                            $notificationTopicImage->image = $image;
                            $notificationTopicImage->save();
                            $request->session()->pull('uploadImage', $image);
                        }
                    }
                    if (isset($data['form'])) {
                        $image = $data['form']['file'];

                        if ($image) {
                            $deleteNotificationTopicImages = NotificationTopicImage::where('notification_topic_id', $id)->get();
                            foreach ($deleteNotificationTopicImages as $oldImage) {
                                if (file_exists(public_path('uploads/notificationimage') . '/' . $oldImage->getRawOriginal('image'))) {
                                    // unlink(public_path('uploads/notificationimage') . '/' . $oldImage->getRawOriginal('image'));
                                }
                            }
                            NotificationTopicImage::where('notification_topic_id', $id)->delete();
                            foreach ($image as $file) {
                                $image_parts = explode(";base64,", $file);
                                $image_type_aux = explode("image/", $image_parts[0]);
                                $image_type = $image_type_aux[1];
                                $image_base64 = base64_decode($image_parts[1]);
                                $renamed = time() . rand() . '_notificationimage.' . $image_type;
                                file_put_contents(public_path('uploads/notificationimage/') . $renamed, $image_base64);

                                $notificationTopicImage = new NotificationTopicImage;
                                $notificationTopicImage->notification_topic_id = $notificationTopic->id;
                                $notificationTopicImage->image = $renamed;
                                $notificationTopicImage->save();
                                $request->session()->pull('uploadImage', $image);
                            }
                        }
                    }
                    $notificationTopicImages = NotificationTopicImage::where('notification_topic_id', $id)->get();

                    $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf/notification-topic', ['notificationTopicImages' => $notificationTopicImages, 'notificationName' => $notificationTopic->name]);

                    if (!\File::isDirectory(public_path('uploads/notificationimage'))) {
                        \File::makeDirectory(public_path('uploads/notificationimage'), $mode = 0755, $recursive = true);
                    }
                    $file_name = $notificationTopic->slug . '.pdf';

                    if (file_exists(public_path('uploads/notificationimage') . '/' . $file_name)) {
                        // unlink(public_path('uploads/notificationimage') . '/' . $file_name);
                    }
                    $content = $pdf->output();

                    file_put_contents(public_path('uploads/notificationimage/') . $notificationTopic->slug . '.pdf', $content);

                } else {
                    return redirect()->back()->with('error', 'Please upload only jpg, jpeg and png files');
                }
            }

            return redirect()->route('notification-topic.edit', [CustomHelper::getEncrypted($id), $brandId])->with('success', 'Notification topic updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewImages(Request $request, $id)
    {
        // dd($id);
        // $notificationTopic = NotificationTopic::find($id);
        $notificationTopicId = CustomHelper::getDecrypted($id);
        $attachment = new NotificationTopicImage();
        $notificationTopic = NotificationTopic::find($notificationTopicId);
        $attachments = $attachment->where('notification_topic_id', $notificationTopicId)->get();

        $brand = Brand::find($notificationTopic->brand_id);
        return view('backend.subscriber.notification-topic.view-attachment', compact('attachments','notificationTopic','brand'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userNotification($id)
    {
        $notificationTopic = NotificationTopic::with('brand')->find($id);
        $notificationTopic->notify_flag = '0';
        $notificationTopic->notify_date = date("Y-m-d H:i:s", strtotime('now'));
        $notificationTopic->save();

        $userNotification = UserNotificationTopic::where('notification_topic_id', $id)->pluck('user_id')->toArray();

        
        // echo count($userNotification);die();
        if (count($userNotification) > 0) {
            $user = User::whereIn('id', $userNotification)->get();
            $title = $notificationTopic->brand->name;
            $message = $notificationTopic->name;
            NotificationHelper::send_pushnotification($user, $title, $message, 1);

            foreach ($user as $row) {
                $this->createTransactionHistorynew($notificationTopic->brand_id, 'Info Distribute', 'notification_distribution', $notificationTopic, $row->id);
            }
        }
        

        try {
            $userNotification = UserNotificationTopic::where('notification_topic_id', $id)->update(['reading_status' => "0",'notift_at' => Carbon::now()]);
            return response()->json(['success' => 'Notification sent successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Oops! Something went.']);
        }
    }

    public function notificationTopicHistory($id, $brandId)
    {
        $id = CustomHelper::getDecrypted($id);
        $brandId = CustomHelper::getDecrypted($brandId);

        $transactionHistory = [];
        $transactionHistories = TransactionHistory::where('pay_for', 'notification_distribution')->where('brand_id', $brandId)->get();
        foreach ($transactionHistories as $row) {
            if ($row->submitted_data['id'] == $id) {
                $transactionHistory[] = $row;
            }
        }
        $transactionHistory = $this->paginate($transactionHistory);

        $brand = Brand::find($brandId);
        return view('backend.subscriber.notification-topic.history', compact('transactionHistory','brand'));
    }

    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /* create transaction history */
    public function createTransactionHistory($brand_id, $pay_for, $pay_fortitle, $data, $userId)
    {
        $brand = Brand::find($brand_id);
        $price = PriceSetting::where('title', $pay_for)->first();

        $subscriber = Admin::find($brand->subscriber_id);
        $rate =  isset($subscriber->currency->rate)? $subscriber->currency->rate : 0;

        TransactionHistory::create(['brand_id' => $brand_id,
            'admin_id' => $brand->subscriber_id,
            'amount' => ($price->price*$rate),
            'amount_usd' => $price->price,
            'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
            'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
            'pay_for' => $pay_fortitle,
            'user_id' => $userId,
            'submitted_data' => $data]);

        $brand->subscriber()->decrement('wallet_amount', ($price->price*$rate));
        $brand->subscriber()->decrement('wallet_amount_usd', $price->price);
        return true;
    }











    /* create transaction history */
    public function createTransactionHistorynew($brand_id, $pay_for, $pay_fortitle, $data, $userId)
    {
        $brand = Brand::find($brand_id);
        $price = PriceSetting::where('title', $pay_for)->first();

        $subscriber = Admin::find($brand->subscriber_id);
        $rate =  isset($subscriber->currency->rate)? $subscriber->currency->rate : 0;

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
            'user_id' => $userId,
            'deducted_from' => 'wallet',
            'submitted_data' => $data]);

            $brand->subscriber()->decrement('wallet_amount', ($price->price*$rate));
            $brand->subscriber()->decrement('wallet_amount_usd', $price->price);

            Brand::where('id', $brand_id)->increment('spend_amount', ($price->price*$rate));
            Brand::where('id', $brand_id)->increment('spend_amount_usd', $price->price);
            return true;

        }


         /* from gift wallet */
         if($subscriber->bonus_amount > $subscriberamount){

            TransactionHistory::create(['brand_id' => $brand_id,
            'admin_id' => $brand->subscriber_id,
            'amount' => ($price->price*$rate),
            'amount_usd' => $price->price,
            'admin_total_gross_revenue' => 0,
            'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
            'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
            'pay_for' => $pay_fortitle,
            'user_id' => $userId,
            'deducted_from' => 'gift',
            'submitted_data' => $data]);

            $brand->subscriber()->decrement('bonus_amount', ($price->price*$rate));
            $brand->subscriber()->decrement('bonus_amount_usd', $price->price);

            Brand::where('id', $brand_id)->increment('spend_amount', ($price->price*$rate));
            Brand::where('id', $brand_id)->increment('spend_amount_usd', $price->price);
            
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
            'user_id' => $userId,
            'deducted_from' => 'gift',
            'submitted_data' => $data]);

            $brand->subscriber()->decrement('bonus_amount', ($subscriber->bonus_amount));
            $brand->subscriber()->decrement('bonus_amount_usd',$subscriber->bonus_amount_usd);

            Brand::where('id', $brand_id)->increment('spend_amount', ($subscriber->bonus_amount));
            Brand::where('id', $brand_id)->increment('spend_amount_usd', $subscriber->bonus_amount_usd);


            TransactionHistory::create(['brand_id' => $brand_id,
            'admin_id' => $brand->subscriber_id,
            'amount' => ($diffrenceamount),
            'amount_usd' => $diffrenceamountusd,
            'admin_total_gross_revenue' =>  $admingrossrevenuenew,
            'currency_id' => isset($subscriber->currency->id)? $subscriber->currency->id : 0,
            'currency' => isset($subscriber->currency->code)? $subscriber->currency->code : "",
            'pay_for' => $pay_fortitle,
            'user_id' => $userId,
            'deducted_from' => 'wallet',
            'submitted_data' => $data]);

            $brand->subscriber()->decrement('wallet_amount', ($diffrenceamount));
            $brand->subscriber()->decrement('wallet_amount_usd', $diffrenceamountusd);

            Brand::where('id', $brand_id)->increment('spend_amount', ($diffrenceamount));
            Brand::where('id', $brand_id)->increment('spend_amount_usd', $diffrenceamountusd);

            return true;
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
        $notificationTopic = NotificationTopic::find($id);
        if (!$notificationTopic) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $brandId = CustomHelper::getEncrypted($notificationTopic->brand_id);

        $topics = UserNotificationTopic::where('notification_topic_id', $notificationTopic->id)->get();
        if ($topics->count() > 0) {
            foreach($topics as $topic){
                $topic->delete();
            }
        }
        $notificationTopic->delete();
        return redirect()->route('notification-topic.index', $brandId)->with('success', 'Notification topic deleted successfully');
    }
}
