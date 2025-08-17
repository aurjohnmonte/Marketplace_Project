<?php

use App\Events\ActiveEvent;
use App\Events\MessageEvent;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ShopController;
use App\Http\Middleware\BuyerCheck;
use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\Pendingaccounts;
use App\Models\Pendingshop;
use App\Models\User;
use Illuminate\Http\Request;
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

    Route::get('/buyer/{p}/{c?}', function(){

        return view('buyer_home');
    })->whereIn("p", ["home", "message", "profile", "following", "map", "notification"])
      ->whereIn("c", ['account-setting', 'deactivate', 'delete', 'view-all']);

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
    //return products to hom
    Route::get('/buyer/return/products', [BuyerController::class, "returnProducts"]);
    //return products by search
    Route::post('/buyer/search-product', [BuyerController::class, 'returnProductsBySearch']);
    //buyer add review
    Route::post('/buyer/add/review', [BuyerController::class, 'addReview']);
    //return reviews
    Route::post('/buyer/return/review', [BuyerController::class, "returnReviews"]);
    //return new and popular shops
    Route::get('/return/shops/popular-new', [BuyerController::class, "returnAllShops"]);
    Route::post('/return/product', [BuyerController::class, "returnProduct"]);
    //return shop info
    Route::post('/return/shop-info', [ShopController::class, "returnShopInfo"]);
    //follow shop
    Route::post('/buyer/follow-shop', [BuyerController::class, "followShop"]);
    //search messages
    Route::get('/buyer/search-message', [BuyerController::class, "searchMessage"]);
    //return user info about the shop for buyermessage.vue
    Route::get('/shop/info', function(Request $request){

        $user = User::where('id',$request->id)->first();

        return response()->json(['user'=>$user]);
    });
    //return product info
    Route::get('/buyer/return-product/info', [BuyerController::class, "returnProduct_info"]);
    //return following
    Route::get('/buyer/return-following', [BuyerController::class, 'returnFollowing']);
    //change profile
    Route::post('/user/change-profile', [BuyerController::class, 'changeProfile']);
    //user edit info
    Route::post('/buyer/edit-info', [BuyerController::class, 'editinfo']);
    //user edit credential
    Route::post('/buyer/edit-credential', [BuyerController::class, 'editCredential']);
    //buyer mark as read the notification
    Route::post('/buyer/mark-as-read/notification', [NotificationController::class, 'mark_as_read_many']);

    //LOGOUT
    Route::get('/buyer/logout', function(Request $request) {

        $id = (int) $request->id;

        $user = User::where('id', $id)->first();

        Log::info('user: ', ['user'=>$user]);
        if(!$user){
            return "User not found";
        }

        $user->is_active = false;
        $user->time_logout = now();

        if($user->save()){
            broadcast(new ActiveEvent());
            session()->flush();
            return redirect()->route('home');
        }
        
        return "Something went wrong ...";
    });
});

//validate otp
    Route::post('/verify-otp', function(Request $req){

        $type = $req->type;

        $user = User::where('email', $req->email)->first();
        Log::info('user', ['user'=>$user]);

        if($user){

            $otp = Otp::where('from_user', $user->id)->first();

            Log::info('otp', ['otp'=>$otp]);

            if($otp->otp === $req->otp){

                $otp->delete();

                if($type === "deactivate"){

                    $user->is_deactivate = 1;

                    if($user->save()){
                        return response()->json(['message'=>'successful']);
                    }
                }else{

                    Log::info('m', ['m'=>'DELETIOOOOON OF THE ACCOUNT']);
                    if($user->save()){
                        return response()->json(['message'=>'successful']);
                    }
                }
                return response()->json(['message'=>'type not exist']);
            }
        }

        return response()->json(['message'=>'error']);

    });

Route::post('/create-otp', function(Request $req){
    
    $code = mt_rand(100000, 999999);

    $user = User::where('email', $req->email)->first();

    if(session('email') !== $req->email){
        return response()->json(['message'=>'error']);
    }

    if(!$user){
        return response()->json(['message'=>'not exist']);
    }
            
    $otp = new Otp();
    $otp->otp = $code;
    $otp->from_user = $user->id;

    if($otp->save()){
        //send email to user thru gmail
        Mail::to($req->email)->send(new OtpMail($code));

        return response()->json(['message'=>'Successfully']);
    }
    return response()->json(['message'=>'error']);
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









//SELLER SIDE
Route::middleware('usercheck')->group(function() {

    Route::get('/seller', function() {
        return view('seller_pages.seller_home');
    })->name('sellerhome');

    //seller blade view
    Route::get('/seller/{p}/{c?}', function(){

        return view('seller_pages.seller_home');
    })->whereIn("p", ['dashboard', 'home', 'profile', 'messages', 'map', 'followers', 'products', 'product', 'notifications'])
      ->whereIn("c", ["add", "view", 'chats']);//put all the components in the 2nd parameter in whereIn

    //return seller info
    Route::post("/return/user-seller/info", [SellerController::class, "returnProfile_info"]);
    
    //add product
    Route::post('/seller/add/product', [ProductController::class, 'addproduct']);
    //return products
    Route::post('/seller/return/products', [ProductController::class, 'returnproducts']);
    //edit products
    Route::post('/seller/edit/product', [ProductController::class, 'editProduct']);
    //delete product
    Route::post('/seller/delete/product', [ProductController::class, 'deleteProduct']);
    //search product
    Route::post('/seller/search/product', [ProductController::class, "searchProduct"]);
    ///action-notification
    Route::post('/action-notification', [NotificationController::class, 'actionNotify']);
    //change seen
    Route::get('/change-seen', [NotificationController::class, 'changeSeen']);
    //check notification if there is unread
    Route::get('/check/notification', [NotificationController::class, 'checkNotify']);
    //return message list
    Route::get('/seller/return-message/list', [NotificationController::class, "return_messageList"]);
    //return conversation
    Route::get('/return/conversation', [SellerController::class, 'returnConversation']);
    //seller sent a message
    Route::post('/seller/sent-message', [SellerController::class, 'sentMessage']);
    //seller edit message
    Route::get('/message/seller-edit', [SellerController::class, "editMessage"]);
    //seller delete message
    Route::get('/seller/delete-message', [SellerController::class, 'deleteMessage']);
    //seller return product to dashboard
    Route::get('/seller/dashboard/return-products', [SellerController::class, 'returnDashboardProducts']);


    Route::get('/seller/logout', function(Request $request) {

        $id = (int) $request->id;

        $user = User::where('id', $id)->first();

        Log::info('user: ', ['user'=>$user]);
        if(!$user){
            return "User not found";
        }

        $user->is_active = false;
        $user->time_logout = now();

        if($user->save()){
            broadcast(new ActiveEvent());

            session()->flush();
            return redirect()->route('home');
        }
        
        return "Something went wrong ...";
    });
});

//RETURN NOTIFICATIONS FOR BOTH SIDE
Route::get('/return/notifications', [NotificationController::class, 'returnNotifications']);
//change seen in  notification
Route::get('/seen-notify', [NotificationController::class, "modifySeen"]);
//delete notification
Route::post('/delete-notif', [NotificationController::class, "deleteNotif"]);

Route::get('/logout', function(Request $request){
    session()->flush();
    return redirect()->route('home');
});

