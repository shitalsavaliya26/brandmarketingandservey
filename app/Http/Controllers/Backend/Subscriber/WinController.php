<?php

namespace App\Http\Controllers\Backend\Subscriber;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Win;
use App\Models\WinImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PDF;

class WinController extends Controller
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

        $win = Win::where('brand_id', $brandId)->first();

        $brand = Brand::find($brandId);
        $countrycount = 0;
        if ($brand) {
            $countrycount = Brand::where("group_id", $brand->group_id)->count();
        }

        if (!$win) {
            return view('backend.subscriber.brand.win', compact('win', 'countrycount','brand'));
        }
        return view('backend.subscriber.brand.win', compact('win', 'countrycount','brand'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.subscriber.win.create');
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

        if ($request->has("save") && $request->save == "save") {
            $regex = "/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/";

            try {

                $windata = Win::where('brand_id', $brandId)->first();
                if (!$windata) {
                    $request->validate([
                        'attachment_type' => 'in:link,image,pdf',
                        'attachment' => array("required_if:attachment_type,link", "regex:" . $regex, "nullable"),
                        'pdf' => 'required_if:attachment_type,pdf|mimes:pdf',
                        'form' => 'required_if:attachment_type,image|array',
                        'form.file' => 'required_if:attachment_type,image|array',
                    ], [
                        'form.required_if' => 'Please select attachment',
                    ]);

                    $win = new Win();
                    $win->brand_id = $brandId;
                    $win->slug = $pdfName = Str::slug(Str::random(25));
                    $win->attachment_type = $request->attachment_type;
                    if ($request->attachment_type == 'pdf') {
                        $path = ('uploads/win');
                        if (!\File::isDirectory(public_path('uploads/win'))) {
                            \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                        }
                        $file_name = $pdfName . '.pdf';
                        $pdf = $request->pdf->move(public_path('uploads/win'), $file_name);
                        $win->attachment = $file_name;
                    } else {
                        $win->attachment = $request->attachment;
                    }
                    $win->save();

                    if (!empty($request->form) && ($request->pdf == null || empty($request->pdf))) {
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
                                if (!\File::isDirectory(public_path('uploads/win'))) {
                                    \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                                }
                                foreach ($image as $file) {
                                    $image_parts = explode(";base64,", $file);
                                    $image_type_aux = explode("image/", $image_parts[0]);
                                    $image_type = key_exists(1, $image_type_aux) ? $image_type_aux[1] : time();
                                    $image_base64 = base64_decode($image_parts[1]);

                                    $renamed = time() . rand() . '_win.' . $image_type;
                                    file_put_contents(public_path('uploads/win/') . $renamed, $image_base64);

                                    $winImage = new WinImage;
                                    $winImage->win_id = $win->id;
                                    $winImage->image = $renamed;
                                    $winImage->save();
                                    $request->session()->pull('uploadImage', $image);
                                }
                            }
                        } else {
                            return redirect()->route('win.index', $id)->with('error', 'Please upload only jpg, jpeg and png files');
                        }

                        $winImages = WinImage::where('win_id', $win->id)->get();

                        $pdf = PDF::loadView('pdf/win', ['notificationTopicImages' => $winImages]);

                        if (!\File::isDirectory(public_path('uploads/win'))) {
                            \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                        }
                        $file_name = $win->slug . '.pdf';
                        $content = $pdf->output();

                        file_put_contents(public_path('uploads/win/') . $win->slug . '.pdf', $content);
                    }
                } else {
                    $request->validate([
                        'attachment_type' => 'in:link,image,pdf',
                        'attachment' => array("required_if:attachment_type,link", "regex:" . $regex, "nullable"),
                    ]);

                    $windata->slug = ($windata->slug == '' || $windata->slug == null) ? $pdfName = Str::slug(Str::random(25)) : $windata->slug;

                    $windata->attachment_type = $request->attachment_type;
                    if ($request->attachment_type == 'pdf') {
                        $removeWinImages = WinImage::where('win_id', $windata->id)->get();

                        if ($removeWinImages) {
                            foreach ($removeWinImages as $remove) {
                                // if ($remove->image != null && file_exists(public_path('uploads/win') . '/' . $remove->getRawOriginal('image'))) {
                                //     unlink(public_path('uploads/win') . '/' . $remove->getRawOriginal('image'));
                                // }
                                WinImage::find($remove->id)->delete();
                            }
                        }

                        if ($request->hasFile('pdf')) {
                            $path = ('uploads/win');
                            if (!\File::isDirectory(public_path('uploads/win'))) {
                                \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                            }
                            $file_name = $windata->slug . '.pdf';
                            $pdf = $request->pdf->move(public_path('uploads/win'), $file_name);
                            $windata->attachment = $file_name;
                        }
                    } else if ($request->attachment_type == 'link') {
                        $removeWinImages = WinImage::where('win_id', $windata->id)->get();

                        if ($removeWinImages) {
                            foreach ($removeWinImages as $remove) {
                                // if ($remove->image != null && file_exists(public_path('uploads/win') . '/' . $remove->getRawOriginal('image'))) {
                                //     unlink(public_path('uploads/win') . '/' . $remove->getRawOriginal('image'));
                                // }
                                WinImage::find($remove->id)->delete();
                            }
                        }
                        $windata->attachment = $request->attachment;
                    } else {
                        $windata->attachment = null;
                    }
                    $windata->save();

                    $brandId = CustomHelper::getEncrypted($windata->brand_id);

                    $data = $request->all();
                    if (isset($data['remove_img']) && $data['remove_img'] != '') {
                        $removeImage = explode(",", $data['remove_img']);
                        foreach ($removeImage as $image) {
                            $winImageData = WinImage::find($image);
                            // unlink(public_path('uploads/win') . '/' . $winImageData->getRawOriginal('image'));
                            $winImageData->delete();
                        }
                        $winImages = WinImage::where('win_id', $windata->id)->get();

                        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf/win', ['notificationTopicImages' => $winImages, 'notificationName' => $request->attachment_type]);

                        if (!\File::isDirectory(public_path('uploads/win'))) {
                            \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                        }
                        $file_name = $windata->slug . '.pdf';

                        // if (file_exists(public_path('uploads/win') . '/' . $file_name)) {
                        //     unlink(public_path('uploads/win') . '/' . $file_name);
                        // }
                        $content = $pdf->output();

                        file_put_contents(public_path('uploads/win/') . $windata->slug . '.pdf', $content);

                    }

                    if (!\File::isDirectory(public_path('uploads/win'))) {
                        \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                    }
                    if ($request->attachment_type == 'image') {
                        if (isset($data['form']) && ($request->pdf == null || empty($request->pdf))) {
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
                                        $winImage = new WinImage;
                                        $winImage->win_id = $windata->id;
                                        $winImage->image = $image;
                                        $winImage->save();
                                        $request->session()->pull('uploadImage', $image);
                                    }
                                }
                                if (isset($data['form'])) {
                                    $image = $data['form']['file'];

                                    if ($image) {
                                        foreach ($image as $file) {
                                            $image_parts = explode(";base64,", $file);
                                            $image_type_aux = explode("image/", $image_parts[0]);
                                            $image_type = $image_type_aux[1];
                                            $image_base64 = base64_decode($image_parts[1]);
                                            $renamed = time() . rand() . '_win.' . $image_type;
                                            file_put_contents(public_path('uploads/win/') . $renamed, $image_base64);

                                            $winImage = new WinImage;
                                            $winImage->win_id = $windata->id;
                                            $winImage->image = $renamed;
                                            $winImage->save();
                                            $request->session()->pull('uploadImage', $image);
                                        }
                                    }
                                }
                                $winImages = WinImage::where('win_id', $windata->id)->get();

                                $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf/win', ['notificationTopicImages' => $winImages, 'notificationName' => $request->attachment_type]);

                                if (!\File::isDirectory(public_path('uploads/win'))) {
                                    \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                                }
                                $file_name = $windata->slug . '.pdf';

                                // if (file_exists(public_path('uploads/win') . '/' . $file_name)) {
                                //     unlink(public_path('uploads/win') . '/' . $file_name);
                                // }
                                $content = $pdf->output();

                                file_put_contents(public_path('uploads/win/') . $windata->slug . '.pdf', $content);

                            } else {
                                return redirect()->back()->with('error', 'Please upload only jpg, jpeg and png files');
                            }
                        }
                    }

                }
                return redirect()->route('win.index', $id)->with('success', 'Win created successfully');

            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }

        }

        /* for all country */
        else {

            $allcountrybrand = Brand::where("group_id", $brand->group_id)->pluck('id')->toArray();

            $regex = "/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/";

            $request->validate([
                'attachment_type' => 'in:link,image,pdf',
                'attachment' => array("required_if:attachment_type,link", "regex:" . $regex, "nullable"),
                'pdf' => 'required_if:attachment_type,pdf|mimes:pdf',
                'form' => 'required_if:attachment_type,image|array',
                'form.file' => 'required_if:attachment_type,image|array',
            ], [
                'form.required_if' => 'Please select attachment',
            ]);

            try {

                $pdftopic = [];
                foreach ($allcountrybrand as $keyindex => $value) {

                    $windata = Win::where('brand_id',$value)->first();
                    if (!$windata) {

                        $win = new Win();
                        $win->brand_id = $value;
                        $win->slug = $pdfName = Str::slug(Str::random(25));
                        $win->attachment_type = $request->attachment_type;
                        if ($request->attachment_type == 'pdf') {

                            if($keyindex == 0){
                            $path = ('uploads/win');
                            if (!\File::isDirectory(public_path('uploads/win'))) {
                                \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                            }
                            $file_name = $pdfName . '.pdf';
                            $pdf = $request->pdf->move(public_path('uploads/win'), $file_name);
                            $win->attachment = $file_name;
                            }   
                        } else {
                            $win->attachment = $request->attachment;
                        }
                        $win->save();

                        array_push($pdftopic,$win->id);


                        if (!empty($request->form) && ($request->pdf == null || empty($request->pdf))) {
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
                                    if (!\File::isDirectory(public_path('uploads/win'))) {
                                        \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                                    }
                                    foreach ($image as $file) {
                                        $image_parts = explode(";base64,", $file);
                                        $image_type_aux = explode("image/", $image_parts[0]);
                                        $image_type = key_exists(1, $image_type_aux) ? $image_type_aux[1] : time();
                                        $image_base64 = base64_decode($image_parts[1]);

                                        $renamed = time() . rand() . '_win.' . $image_type;
                                        file_put_contents(public_path('uploads/win/') . $renamed, $image_base64);

                                        $winImage = new WinImage;
                                        $winImage->win_id = $win->id;
                                        $winImage->image = $renamed;
                                        $winImage->save();
                                        $request->session()->pull('uploadImage', $image);
                                    }
                                }
                            } else {
                                return redirect()->route('win.index', $id)->with('error', 'Please upload only jpg, jpeg and png files');
                            }

                            $winImages = WinImage::where('win_id', $win->id)->get();

                            $pdf = PDF::loadView('pdf/win', ['notificationTopicImages' => $winImages]);

                            if (!\File::isDirectory(public_path('uploads/win'))) {
                                \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                            }
                            $file_name = $win->slug . '.pdf';
                            $content = $pdf->output();

                            file_put_contents(public_path('uploads/win/') . $win->slug . '.pdf', $content);
                        }
                    } else {
                        $request->validate([
                            'attachment_type' => 'in:link,image,pdf',
                            'attachment' => array("required_if:attachment_type,link", "regex:" . $regex, "nullable"),
                        ]);

                        $windata->slug = ($windata->slug == '' || $windata->slug == null) ? $pdfName = Str::slug(Str::random(25)) : $windata->slug;

                        $windata->attachment_type = $request->attachment_type;



                        if ($request->attachment_type == 'pdf') {

                            
                                $removeWinImages = WinImage::where('win_id', $windata->id)->get();

                                if ($removeWinImages) {
                                    foreach ($removeWinImages as $remove) {
                                        // if ($remove->image != null && file_exists(public_path('uploads/win') . '/' . $remove->getRawOriginal('image'))) {
                                        //     unlink(public_path('uploads/win') . '/' . $remove->getRawOriginal('image'));
                                        // }
                                        WinImage::find($remove->id)->delete();
                                    }
                                }
                            if($keyindex == 0){
                                if ($request->hasFile('pdf')) {
                                    $path = ('uploads/win');
                                    if (!\File::isDirectory(public_path('uploads/win'))) {
                                        \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                                    }
                                    $file_name = $windata->slug . '.pdf';
                                    $pdf = $request->pdf->move(public_path('uploads/win'), $file_name);
                                    $windata->attachment = $file_name;
                                }
                            }
                        } else if ($request->attachment_type == 'link') {
                            $removeWinImages = WinImage::where('win_id', $windata->id)->get();

                            if ($removeWinImages) {
                                foreach ($removeWinImages as $remove) {
                                    // if ($remove->image != null && file_exists(public_path('uploads/win') . '/' . $remove->getRawOriginal('image'))) {
                                    //     unlink(public_path('uploads/win') . '/' . $remove->getRawOriginal('image'));
                                    // }
                                    WinImage::find($remove->id)->delete();
                                }
                            }
                            $windata->attachment = $request->attachment;
                        } else {
                            $windata->attachment = null;
                        }
                        $windata->save();

                        array_push($pdftopic,$windata->id);


                        $brandId = CustomHelper::getEncrypted($windata->brand_id);

                        $data = $request->all();
                        if (isset($data['remove_img']) && $data['remove_img'] != '') {
                            $removeImage = explode(",", $data['remove_img']);
                            foreach ($removeImage as $image) {
                                $winImageData = WinImage::find($image);
                                // unlink(public_path('uploads/win') . '/' . $winImageData->getRawOriginal('image'));
                                $winImageData->delete();
                            }
                            $winImages = WinImage::where('win_id', $windata->id)->get();

                            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf/win', ['notificationTopicImages' => $winImages, 'notificationName' => $request->attachment_type]);

                            if (!\File::isDirectory(public_path('uploads/win'))) {
                                \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                            }
                            $file_name = $windata->slug . '.pdf';

                            // if (file_exists(public_path('uploads/win') . '/' . $file_name)) {
                            //     unlink(public_path('uploads/win') . '/' . $file_name);
                            // }
                            $content = $pdf->output();

                            file_put_contents(public_path('uploads/win/') . $windata->slug . '.pdf', $content);

                        }

                        if (!\File::isDirectory(public_path('uploads/win'))) {
                            \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                        }
                        if ($request->attachment_type == 'image') {
                            if (isset($data['form']) && ($request->pdf == null || empty($request->pdf))) {
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
                                            $winImage = new WinImage;
                                            $winImage->win_id = $windata->id;
                                            $winImage->image = $image;
                                            $winImage->save();
                                            $request->session()->pull('uploadImage', $image);
                                        }
                                    }
                                    if (isset($data['form'])) {
                                        $image = $data['form']['file'];

                                        if ($image) {
                                            foreach ($image as $file) {
                                                $image_parts = explode(";base64,", $file);
                                                $image_type_aux = explode("image/", $image_parts[0]);
                                                $image_type = $image_type_aux[1];
                                                $image_base64 = base64_decode($image_parts[1]);
                                                $renamed = time() . rand() . '_win.' . $image_type;
                                                file_put_contents(public_path('uploads/win/') . $renamed, $image_base64);

                                                $winImage = new WinImage;
                                                $winImage->win_id = $windata->id;
                                                $winImage->image = $renamed;
                                                $winImage->save();
                                                $request->session()->pull('uploadImage', $image);
                                            }
                                        }
                                    }
                                    $winImages = WinImage::where('win_id', $windata->id)->get();

                                    $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf/win', ['notificationTopicImages' => $winImages, 'notificationName' => $request->attachment_type]);

                                    if (!\File::isDirectory(public_path('uploads/win'))) {
                                        \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                                    }
                                    $file_name = $windata->slug . '.pdf';

                                    // if (file_exists(public_path('uploads/win') . '/' . $file_name)) {
                                    //     unlink(public_path('uploads/win') . '/' . $file_name);
                                    // }
                                    $content = $pdf->output();

                                    file_put_contents(public_path('uploads/win/') . $windata->slug . '.pdf', $content);

                                } else {
                                    return redirect()->back()->with('error', 'Please upload only jpg, jpeg and png files');
                                }
                            }
                        }

                    }

                }

                if ($request->hasfile('pdf') && $request->attachment_type == 'pdf') {
                    $attachment = Win::find($pdftopic[0]);
                    Win::whereIn('id',$pdftopic)->update(['attachment'=>$attachment->attachment]);
                }


                return redirect()->route('win.index', $id)->with('success', 'Win created successfully');

            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }

        }
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

        $win = Win::with('images')->find($id);
        if (!$win) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        return view('backend.subscriber.win.edit', compact('win'));
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
        $regex = "/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/";

        $request->validate([
            'attachment_type' => 'in:link,image,pdf',
            'attachment' => array("required_if:attachment_type,link", "regex:" . $regex, "nullable"),
        ]);

        $id = CustomHelper::getDecrypted($id);

        $win = Win::find($id);
        if (!$win) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        try {
            $win->attachment_type = $request->attachment_type;
            if ($request->attachment_type == 'pdf') {
                if ($request->hasFile('pdf')) {

                    $path = ('uploads/win');
                    if (!\File::isDirectory(public_path('uploads/win'))) {
                        \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                    }
                    $file_name = time() . '_win.' . $request->pdf->getClientOriginalExtension();
                    $pdf = $request->pdf->move(public_path('uploads/win'), $file_name);
                    $win->attachment = $file_name;
                }
            } else {
                $win->attachment = $request->attachment;
            }
            $win->save();

            $brandId = CustomHelper::getEncrypted($win->brand_id);

            $data = $request->all();
            if (isset($data['remove_img']) && $data['remove_img'] != '') {
                $removeImage = explode(",", $data['remove_img']);
                foreach ($removeImage as $image) {
                    WinImage::find($image)->delete();
                }

                $winImages = WinImage::where('win_id', $win->id)->get();

                return $winImages;
                $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf/win', ['notificationTopicImages' => $winImages, 'notificationName' => $win->attachment_type]);

                if (!\File::isDirectory(public_path('uploads/win'))) {
                    \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                }
                $file_name = $win->brand_id . '.pdf';

                // if (file_exists(public_path('uploads/win') . '/' . $file_name)) {
                //     unlink(public_path('uploads/win') . '/' . $file_name);
                // }
                $content = $pdf->output();

                file_put_contents(public_path('uploads/win/') . $win->brand_id . '.pdf', $content);
            }
            if (isset($data['form']) && ($request->pdf == null || empty($request->pdf))) {
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
                            $winImage = new WinImage;
                            $winImage->win_id = $win->id;
                            $winImage->image = $image;
                            $winImage->save();
                            $request->session()->pull('uploadImage', $image);
                        }
                    }
                    if (isset($data['form'])) {
                        $image = $data['form']['file'];

                        if ($image) {
                            foreach ($image as $file) {
                                $image_parts = explode(";base64,", $file);
                                $image_type_aux = explode("image/", $image_parts[0]);
                                $image_type = $image_type_aux[1];
                                $image_base64 = base64_decode($image_parts[1]);
                                $renamed = time() . rand() . '_win.' . $image_type;
                                file_put_contents(public_path('uploads/win/') . $renamed, $image_base64);

                                $winImage = new WinImage;
                                $winImage->win_id = $win->id;
                                $winImage->image = $renamed;
                                $winImage->save();
                                $request->session()->pull('uploadImage', $image);
                            }
                        }
                    }
                    $winImages = WinImage::where('win_id', $id)->get();

                    $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdf/win', ['notificationTopicImages' => $winImages, 'notificationName' => $win->attachment_type]);

                    if (!\File::isDirectory(public_path('uploads/win'))) {
                        \File::makeDirectory(public_path('uploads/win'), $mode = 0755, $recursive = true);
                    }
                    $file_name = $win->brand_id . '.pdf';

                    // if (file_exists(public_path('uploads/win') . '/' . $file_name)) {
                    //     unlink(public_path('uploads/win') . '/' . $file_name);
                    // }
                    $content = $pdf->output();

                    file_put_contents(public_path('uploads/win/') . $win->brand_id . '.pdf', $content);

                } else {
                    return redirect()->back()->with('error', 'Please upload only jpg, jpeg and png files');
                }
            }

            return redirect()->route('win.index', $brandId)->with('success', 'Win updated successfully');
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
    public function destroy($id)
    {
        //
        $win = Win::find($id);
        if (!$win) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $brandId = CustomHelper::getEncrypted($win->brand_id);
        $win->delete();
        return redirect()->route('win.index', $brandId)->with('success', 'Win deleted successfully');
    }
}
