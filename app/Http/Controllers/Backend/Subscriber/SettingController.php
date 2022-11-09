<?php

namespace App\Http\Controllers\Backend\Subscriber;

use App;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;

class SettingController extends Controller
{
    /**
     * Retrieve settings of requested brand.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        
            $brandId = CustomHelper::getDecrypted($id);
            $brand = Brand::find($brandId);
            $countrycount = 0;
            if($brand){
                $countrycount = Brand::where("group_id",$brand->group_id)->count();
            }
            $settings = Setting::where('brand_id', $brandId)->first();
            if (!empty($settings)) {
                return view('backend.subscriber.setting.index', compact('settings','countrycount','brand'));
            } else {
                return redirect()->route('brand.index')->with('error','Not found setting of selected brand.');
            }
        
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'tell' => 'required|in:0,1',
            'know' => 'required|in:0,1',
            'buy' => 'required|in:0,1',
            'think' => 'required|in:0,1',
            'win' => 'required|in:0,1',
            'website' => 'required|in:0,1'
        ]);
        try {
            $id = CustomHelper::getDecrypted($request->id);

            $setting = Setting::where('id', $id)->first();

            $brand = Brand::find($setting->brand_id);
            if(!$setting){
                return redirect()->back()->with('error','No recored found.');
            }else{


                if($request->has("save") && $request->save == "save"){
                    $setting->tell = $request->tell;
                    $setting->know = $request->know;
                    $setting->buy = $request->buy;
                    $setting->think = $request->think;
                    $setting->win = $request->win;
                    $setting->website = $request->website;
                    $setting->save();
                }else{
                    $allcountrybrand = Brand::where("group_id",$brand->group_id)->pluck('id')->toArray();

                  
                    foreach($allcountrybrand as $value){
                        $allsettings = Setting::where('brand_id',$value)->first();
                        if($allsettings){
                            $allsettings->tell = $request->tell;
                            $allsettings->know = $request->know;
                            $allsettings->buy = $request->buy;
                            $allsettings->think = $request->think;
                            $allsettings->win = $request->win;
                            $allsettings->website = $request->website;
                            $allsettings->save();
                        }
                    }
                }
            }
            return redirect()->route('brand.index')->with('success','Setting updated successfully');
            
        } catch (Exception $e) {
            return redirect()->back()->with('error',  $e->getMessage());
        }
    }
}
