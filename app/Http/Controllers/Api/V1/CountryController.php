<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function getCountry(Request $request)
    {
        // Country::select('id', 'name', 'calling_code')->whereDoesntHave('city')->delete();

        $country = Country::select('id', 'name', 'calling_code','iso')->whereHas('city')->get();
        if ($country->count() > 0) {
            return response()->json(['success' => true, 'data' => $country, 'message' => 'All country fatched successfully.', 'code' => 200], 200);
        } else {
            return response()->json(['success' => true, 'message' => 'No country found in the system.', 'code' => 200], 200);
        }
    }

    public function getCity(Request $request)
    {
        $city = City::select('id', 'name')->where('country_id', $request->country_id)->get();
        if ($city->count() > 0) {
            return response()->json(['success' => true, 'data' => $city, 'message' => 'All city of given country fatched successfully.', 'code' => 200], 200);
        } else {
            return response()->json(['success' => true, 'message' => 'No city found of given country in the system.', 'code' => 200], 200);
        }
    }
}
