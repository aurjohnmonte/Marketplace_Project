<?php

use App\Http\Controllers\RegisterController;
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

    if(session()->has('email')){
        return redirect()->route('sellerhome');
    }

    return view('home');
})->name('home');

//BUYER SIDE
Route::get('/buyer', function(){

    return view('buyer_home');
});
Route::get('/buyer/browse/{name}/{category}/{filter}', function(){

    return view('buyer_home');
})->whereIn("name", ["Product"])
  ->whereIn("category", ["Any"])
  ->whereIn("filter", ["Popular", "New"]);

Route::get('/buyer/product/{id}', function(){

    return view('buyer_home');
})->where('id', '[0-9]+');

Route::get('/buyer/{p}', function(){

    return view('buyer_home');
})->whereIn("p", ["home", "message", "profile", "following", "map"]);

//buyer blade view
Route::get('/buyer/home', function() {
    return view('buyer_pages.buyer_home');
});

Route::get('/sample/email', function(){
    
    return view('sample');
});

//register user
Route::post('/user/register', [RegisterController::class, 'register']);

Route::post('/send/otp', [RegisterController::class, 'sendotp']);


Route::middleware('userlogin')->post('/user/login', function(){

    return response()->json(['message'=>'successful']);
});




Route::middleware('usercheck')->group(function() {

    Route::get('/seller', function() {
        return view('seller_pages.seller_home');
    })->name('sellerhome');

    Route::get('/seller/logout', function() {
        session()->flush();
        return redirect()->route('home');
    });

    //seller blade view
    Route::get('/seller/{p}', function(){

        return view('seller_pages.seller_home');
    })->whereIn("p", ['dashboard', 'home', 'profile', 'messages', 'map', 'followers', 'products']);//put all the components in the 2nd parameter in whereIn
});

