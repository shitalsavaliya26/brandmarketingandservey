<?php

namespace App\Http\Controllers\Backend\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\PriceSetting;
use Illuminate\Http\Request;

class PriceSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $priceSetting = new PriceSetting();
        $priceSettings = $priceSetting->orderBy('id', 'asc')->get();
        return view('backend.superadmin.price-setting.index', compact('priceSettings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            foreach ($request->price as $key => $price) {
                $setPrice = PriceSetting::where('title', $key)->first();
                $setPrice->price = $price;
                $setPrice->save();
            }

            $priceSetting = new PriceSetting();
            $priceSettings = PriceSetting::orderBy('id', 'asc')->get();

            return redirect()->route('price-setting.index')->with('success', 'Price setting updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
