<?php

namespace App\Http\Controllers\Backend\Subscriber;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\AdvertisementImage;
use App\Models\Brand;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
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
        $advertisement = new Advertisement();
        $advertisements = $advertisement->with('advertisementImages')->with('brand')->orderBy('id', 'desc')->paginate($this->limit);
        return view('backend.subscriber.advertisement.index', compact('advertisements'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::get();
        return view('backend.subscriber.advertisement.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'brand_id' => 'required',
        ]);
        try {
            $advertisement = new Advertisement();
            $advertisement->brand_id = $request->brand_id;
            $advertisement->save();
            if (!empty($request->form)) {
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
                        if (!\File::isDirectory(public_path('uploads/advertisement'))) {
                            \File::makeDirectory(public_path('uploads/advertisement'), $mode = 0755, $recursive = true);
                        }
                        foreach ($image as $file) {
                            $image_parts = explode(";base64,", $file);
                            $image_type_aux = explode("image/", $image_parts[0]);
                            $image_type = key_exists(1, $image_type_aux) ? $image_type_aux[1] : time();
                            $image_base64 = base64_decode($image_parts[1]);

                            $renamed = time() . rand() . '_advertisement.' . $image_type;
                            file_put_contents(public_path('uploads/advertisement/') . $renamed, $image_base64);

                            $advertisementImage = new AdvertisementImage;
                            $advertisementImage->advertisement_id = $advertisement->id;
                            $advertisementImage->image = $renamed;
                            $advertisementImage->save();
                            $request->session()->pull('uploadImage', $image);
                        }
                    }
                } else {
                    return redirect()->route('advertisement.index')->with('error', 'Please upload only jpg, jpeg and png files');
                }
            }
            return redirect()->route('advertisement.index')->with('success', 'Advertisement created successfully');

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
        $brandId = CustomHelper::getDecrypted($id);
        $brand = Brand::find($brandId);
        $advertisement = AdvertisementImage::where('brand_id', $brandId)->get();

        $countrycount = 0;
        if ($brand) {
            $countrycount = Brand::where("group_id", $brand->group_id)->count();
        }

        return view('backend.subscriber.advertisement.edit', compact('advertisement', 'brandId', 'countrycount','brand'));
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

        if ($request->has("save") && $request->save == "save") {
            $advertisement = AdvertisementImage::where('brand_id', $id)->get();
            if (!$advertisement) {
                return redirect()->back()->with('error', 'No recored found.');
            }
            try {
                $data = $request->all();
                if (isset($data['remove_img']) && $data['remove_img'] != '') {
                    $removeImage = explode(",", $data['remove_img']);
                    foreach ($removeImage as $image) {
                        AdvertisementImage::find($image)->delete();
                    }
                }

                if (!empty($request->form)) {
                    $data = $request->all();
                    $image = $data['form']['file'];
                    $totalImages = count($data['form']['file']);
                    $validImageCount = 0;
                    if ($image) {
                        $validExtension = ['jpg', 'jpeg', 'png', 'pdf'];
                        foreach ($image as $file) {
                            $image_parts = explode(";base64,", $file);
                            $check = explode("data:", $image_parts[0]);
                            if ($check[1] == "application/pdf") {
                                $image_type_aux = explode("application/", $image_parts[0]);
                            } else {
                                $image_type_aux = explode("image/", $image_parts[0]);
                            }
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
                            if (!\File::isDirectory(public_path('uploads/advertisement'))) {
                                \File::makeDirectory(public_path('uploads/advertisement'), $mode = 0755, $recursive = true);
                            }
                            foreach ($image as $file) {
                                $image_parts = explode(";base64,", $file);
                                $checkbase = explode("data:", $image_parts[0]);

                                $advertisementImage = new AdvertisementImage;
                                if ($checkbase[1] == "application/pdf") {
                                    $image_type_aux = explode("application/", $image_parts[0]);
                                    $image_type = key_exists(1, $image_type_aux) ? $image_type_aux[1] : time();
                                    $image_base64 = base64_decode($image_parts[1]);
                                    $renamed = time() . rand() . '_advertisement.' . $image_type;
                                    file_put_contents(public_path('uploads/advertisement/') . $renamed, $image_base64);
                                    $advertisementImage->type = "2";
                                } else {
                                    $image_type_aux = explode("image/", $image_parts[0]);
                                    $image_type = key_exists(1, $image_type_aux) ? $image_type_aux[1] : time();
                                    $image_base64 = base64_decode($image_parts[1]);
                                    $renamed = time() . rand() . '_advertisement.' . $image_type;
                                    file_put_contents(public_path('uploads/advertisement/') . $renamed, $image_base64);
                                    $advertisementImage->type = "1";
                                }

                                $advertisementImage->brand_id = $id;
                                $advertisementImage->image = $renamed;
                                $advertisementImage->save();
                                $request->session()->pull('uploadImage', $image);
                            }
                        }
                    } else {
                        return redirect()->back()->with('error', 'Please upload only jpg, jpeg, png and pdf files');
                    }
                }
                return redirect()->route('brand.index')->with('success', 'Advertisement updated successfully');

            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            $allcountrybrand = Brand::where("group_id", $brand->group_id)->pluck('id')->toArray();

            // dd($allcountrybrand );

            foreach ($allcountrybrand as $keyindex => $value) {
                $advertisement = AdvertisementImage::where('brand_id', $value)->get();
                if (!$advertisement) {
                    return redirect()->back()->with('error', 'No recored found.');
                }
                try {
                    $data = $request->all();
                    if (isset($data['remove_img']) && $data['remove_img'] != '') {
                        $removeImage = explode(",", $data['remove_img']);
                        foreach ($removeImage as $image) {
                           $img = AdvertisementImage::find($image);

                           if($img){
                            $img->delete();
                           }
                        }
                    }

                    if (!empty($request->form)) {
                        $data = $request->all();
                        $image = $data['form']['file'];
                        $totalImages = count($data['form']['file']);
                        $validImageCount = 0;
                        if ($image) {
                            $validExtension = ['jpg', 'jpeg', 'png', 'pdf'];
                            foreach ($image as $file) {
                                $image_parts = explode(";base64,", $file);
                                $check = explode("data:", $image_parts[0]);
                                if ($check[1] == "application/pdf") {
                                    $image_type_aux = explode("application/", $image_parts[0]);
                                } else {
                                    $image_type_aux = explode("image/", $image_parts[0]);
                                }
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
                                if (!\File::isDirectory(public_path('uploads/advertisement'))) {
                                    \File::makeDirectory(public_path('uploads/advertisement'), $mode = 0755, $recursive = true);
                                }
                                foreach ($image as $file) {
                                    $image_parts = explode(";base64,", $file);
                                    $checkbase = explode("data:", $image_parts[0]);

                                    $advertisementImage = new AdvertisementImage;
                                    if ($checkbase[1] == "application/pdf") {
                                        $image_type_aux = explode("application/", $image_parts[0]);
                                        $image_type = key_exists(1, $image_type_aux) ? $image_type_aux[1] : time();
                                        $image_base64 = base64_decode($image_parts[1]);
                                        $renamed = time() . rand() . '_advertisement.' . $image_type;
                                        file_put_contents(public_path('uploads/advertisement/') . $renamed, $image_base64);
                                        $advertisementImage->type = "2";
                                    } else {
                                        $image_type_aux = explode("image/", $image_parts[0]);
                                        $image_type = key_exists(1, $image_type_aux) ? $image_type_aux[1] : time();
                                        $image_base64 = base64_decode($image_parts[1]);
                                        $renamed = time() . rand() . '_advertisement.' . $image_type;
                                        file_put_contents(public_path('uploads/advertisement/') . $renamed, $image_base64);
                                        $advertisementImage->type = "1";
                                    }

                                    $advertisementImage->brand_id = $value;
                                    $advertisementImage->image = $renamed;
                                    $advertisementImage->save();
                                    $request->session()->pull('uploadImage', $image);
                                }
                            }
                        } else {
                            return redirect()->back()->with('error', 'Please upload only jpg, jpeg, png and pdf files');
                        }
                    }

                } catch (Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }

               
            }

            return redirect()->route('brand.index')->with('success', 'Advertisement updated successfully');

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
        $advertisement = Advertisement::find($id);
        $path = ('uploads/advertisement');
        if (!$advertisement) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $advertisementImages = AdvertisementImage::where('advertisement_id', $id)->get();
        if ($advertisementImages->count() > 0) {
            foreach ($advertisementImages as $advertisementImage) {
                // if ($advertisementImage->image != null && file_exists(public_path($path) . '/' . $advertisementImage->getOriginal('image'))) {
                //     unlink(public_path($path) . '/' . $advertisementImage->getOriginal('image'));
                // }
                $advertisementImage->delete();
            }
        }
        $advertisement->delete();
        return redirect()->route('advertisement.index')->with('success', 'Advertisement delete successfully');
    }
}
