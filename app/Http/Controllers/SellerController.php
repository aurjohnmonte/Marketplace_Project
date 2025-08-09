<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SellerController extends Controller
{
    public function returnProfile_info(){
        try{
            if(!session()->has("email")){
                return response()->json(['message'=>'no email']);
            }
            $email = session('email');

            $user = User::returnProfileInfo($email);

            $shop = Shop::with(['products', 'products.photos','reviews'])->where('shops.user_id',$user['id'])->first();

            return response()->json(['message'=>$user, 'shop'=>$shop]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }
}
