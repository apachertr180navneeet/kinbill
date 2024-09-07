<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    User,
    Variation,
    Item,
    city,
    State,
    Pincode
};

class LocationController extends Controller
{
    public function getCities($state)
    {
        $cities = City::where('state_id', $state)->get(['id', 'city_name']);
        return response()->json($cities);
    }

    public function getPincodes($city)
    {
        $pincodes = Pincode::where('city_id', $city)->get();
        return response()->json($pincodes);
    }
}
