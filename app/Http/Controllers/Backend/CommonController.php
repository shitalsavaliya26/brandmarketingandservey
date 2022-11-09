<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function getCountryBasedCity(Request $request)
    {
        $data['cities'] = DB::table('cities')->where("country_id", $request->country_id)
            ->get(["name", "id"]);
        return response()->json($data);
    }

}
