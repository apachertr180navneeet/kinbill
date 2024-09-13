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
    Pincode,
    StockReport
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

    public function checkStock(Request $request)
    {
        $item = StockReport::where('item_id',$request->item_id)->first();

        // Assuming the item model has a 'stock' column for available stock
        if ($item && $item->quantity >= $request->quantity) {
            return response()->json(['stock_available' => true]);
        } else {
            return response()->json(['stock_available' => false]);
        }
    }
}
