<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function returnShopInfo(Request $request){
        try{

            $shop = Shop::with(['user.followers','products.photos', 'products.records', 'products.reviews', 'products.shop', 'reviews.user'])
                        ->where('id',$request->id)
                        ->get();

            return response()->json(['shop'=>$shop]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }
}
