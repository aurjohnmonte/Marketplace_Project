<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

//buyer blade view
Route::get('/buyer/home', function() {
    return view('buyer_pages.buyer_home');
});


//seller blade view
Route::get('/seller/{p}', function(){

    return view('seller_pages.seller_home');
})->whereIn("p", ['dashboard', 'home', 'profile']);//put all the components in the 2nd parameter in whereIn
