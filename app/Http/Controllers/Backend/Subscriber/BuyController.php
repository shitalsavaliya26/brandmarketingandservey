<?php

namespace App\Http\Controllers\Backend\Subscriber;

use App\Models\Buy;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;

class BuyController extends Controller
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

        $buy = Buy::where('brand_id', $brandId)->first();

        $brand = Brand::find($brandId);
        $countrycount = 0;
        if($brand){
            $countrycount = Brand::where("group_id",$brand->group_id)->count();
        }


        return view('backend.subscriber.brand.buy', compact('buy','countrycount','brand'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.subscriber.buy.create');
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
        
        $request->validate([
            'link' => ['required', 'regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
        ]);

        if($request->has("save") && $request->save == "save"){
            try {
                $buyData = Buy::where('brand_id', $brandId)->first();
                if (!$buyData) {
                    $buy = new Buy();
                    $buy->brand_id = $brandId;
                    $buy->link = $request->link;
                    $buy->save();
                } else {
                    $buyData->link = $request->link;
                    $buyData->save();
                }
    
                return redirect()->route('buy.index', $id)->with('success', 'Shop created successfully');
    
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
        else{
            $allcountrybrand = Brand::where("group_id",$brand->group_id)->pluck('id')->toArray();
            try {
                foreach($allcountrybrand as $value){
                    $buyData = Buy::where('brand_id', $value)->first();
                    if (!$buyData) {
                        $buy = new Buy();
                        $buy->brand_id = $value;
                        $buy->link = $request->link;
                        $buy->save();
                    } else {
                        $buyData->link = $request->link;
                        $buyData->save();
                    }
                }
                return redirect()->route('buy.index', $id)->with('success', 'Shop created successfully for all country');
    
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

        $buy = Buy::find($id);
        if (!$buy) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        return view('backend.subscriber.buy.edit', compact('buy'));
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

        $buy = Buy::find($id);
        if (!$buy) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $request->validate([
            'link' => ['required', 'regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
        ]);
        try {
            $buy->link = $request->link;
            $buy->save();

            $brandId = CustomHelper::getEncrypted($buy->brand_id);
            return redirect()->route('buy.index', $brandId)->with('success', 'Shop updated successfully');
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
        $buy = Buy::find($id);
        if (!$buy) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $brandId = CustomHelper::getEncrypted($buy->brand_id);
        $buy->delete();
        return redirect()->route('buy.index', $brandId)->with('success', 'Shop deleted successfully');
    }
}
