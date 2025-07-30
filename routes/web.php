<?php

use App\Events\MessageEvent;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SellerController;
use App\Http\Middleware\BuyerCheck;
use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\Pendingaccounts;
use App\Models\Pendingshop;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    Otp::query()->delete();
    Pendingshop::query()->delete();
    Pendingaccounts::query()->delete();

    if(session()->has('email') && session('user_type') === 'seller'){
        return redirect()->route('sellerhome');
    }
    else if(session()->has('email') && session('user_type') === "buyer"){
        return redirect()->route('buyerhome');
    }

    return view('home');
})->name('home');





//BUYER SIDE
Route::middleware('buyercheck')->group(function() {


    Route::get('/buyer', function(){

        return view('buyer_home');
    })->name('buyerhome');
    Route::get('/buyer/browse/{name}/{category}/{filter}', function(){

        return view('buyer_home');
    })->whereIn("name", ["Product", "Shop", "Seller"])
    ->whereIn("category", ["Any"])
    ->whereIn("filter", ["Popular", "New"]);

    Route::get('/buyer/browse-shop/{name}/{category}/{filter}', function(){

        return view('buyer_home');
    })->whereIn("name", ["Product", "Shop", "Seller"])
    ->whereIn("category", ["Any"])
    ->whereIn("filter", ["Popular", "New"]);

    Route::get('/buyer/product/{id}', function(){

        return view('buyer_home');
    })->where('id', '[0-9]+');

    Route::get("/buyer/browse/shop/{id}/{p?}", function(){

        return view('buyer_home');
    })->where("id", '[A-Za-z0-9]+')
    ->where('p', '[A-Za-z_-]+');

    Route::get('/buyer/message/conversation/{id}', function(){

        return view('buyer_home');
    })->where('id', '[0-9]+');

    Route::get('/buyer/{p}', function(){

        return view('buyer_home');
    })->whereIn("p", ["home", "message", "profile", "following", "map"]);

    //buyer blade view
    Route::get('/buyer/home', function() {
        return view('buyer_pages.buyer_home');
    });

    //return shops that are searched
    Route::post("/buyer/search-shop", [BuyerController::class, "searchShops"]);
    //return shop profile info
    Route::post("/shop/return-info", [BuyerController::class, "returnShopProfile"]);
    //return profile info
    Route::post("/return/user-buyer/info", [BuyerController::class, "returnProfile_info"]);
    //return conversation messages
    Route::post("/send-message/from-buyer", [BuyerController::class, "messageSent"]);
    //return list of message
    Route::post("/return/list-messages", [BuyerController::class, "returnMessages"]);

    //delete message
    Route::post('/delete/message', [BuyerController::class, "deleteMessage"]);
    //edit message
    Route::post("/edit/message", [BuyerController::class, "editMessage"]);
    //return shop
    Route::get("/return/shop", [BuyerController::class, "returnShops"]);
});



Route::get('/try-lang', function(){

    broadcast(new MessageEvent("lilibeth123"));
    return response()->json(['message'=>'successfull']);
});










Route::get('/sample/email', function(){
    
    return view('sample');
});

//register user
Route::post('/user/register', [RegisterController::class, 'register']);

Route::post('/send/otp', [RegisterController::class, 'sendotp']);


Route::middleware('userlogin')->post('/user/login', function(){

    if(session('user_type') === "seller"){
        return response()->json(['message'=>'successful', 'type'=>'seller']);
    }

    return response()->json(['message'=>'successful', 'type'=>'buyer']);
});










Route::middleware('usercheck')->group(function() {

    Route::get('/seller', function() {
        return view('seller_pages.seller_home');
    })->name('sellerhome');

    //seller blade view
    Route::get('/seller/{p}', function(){

        return view('seller_pages.seller_home');
    })->whereIn("p", ['dashboard', 'home', 'profile', 'messages', 'map', 'followers', 'products']);//put all the components in the 2nd parameter in whereIn

    //return seller info
    Route::post("/return/user-seller/info", [SellerController::class, "returnProfile_info"]);
});



Route::get('/seller/logout', function() {
    session()->flush();
    return redirect()->route('home');
});