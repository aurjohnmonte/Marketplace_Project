<?php

namespace App\Http\Controllers;

use App\Mail\ActivateMail;
use App\Mail\BlockProductMail;
use App\Mail\DeactivateMail;
use App\Mail\DeleteAccountMail;
use App\Mail\OtpMail;
use App\Mail\UnblockProductMail;
use App\Models\Admin;
use App\Models\Adminotp;
use App\Models\Follower;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Otp;
use App\Models\Photo;
use App\Models\Product;
use App\Models\Pvideo;
use App\Models\Record;
use App\Models\Review;
use App\Models\Reviewphoto;
use App\Models\Reviewvideo;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{

    public function admin_returnData_dashboard(){

        try{

            $notif = Notification::with(['users'])->where('to_admin', 1)->get();

            $products = Product::with(['shop', 'shop.user'])->get();

            $users = User::get();

            $data = [
                'notif' => $notif,
                'products' => $products,
                'users' => $users
            ];

            return response()->json(['message' => 'success', 'data' => $data]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function admin_mark_all_read_notif(){

        try{

            $notif = Notification::where('to_admin', 1)->get();

            if(!$notif->isEmpty()){

                foreach($notif as $n){

                    $n->seen = 1;

                    $n->save();
                }
            }

            return response()->json(['message' => 'success']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function admin_delete_all_notif(){

        try{

            $notif = Notification::where('to_admin', 1)->get();

            if(!$notif->isEmpty()){

                foreach($notif as $n){

                    $n->delete();
                }
            }

            return response()->json(['message' => 'success']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function admin_delete_notif(Request $req){

        try{

            $notif = Notification::where('id', $req->id)->first();

            if(!$notif){

                return response()->json(['message' => 'notif not found']);
            }

            $notif->delete();

            return response()->json(['message' => 'success']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function admin_return_notif(Request $req){

        try{

            $notifications = Notification::with(['users', 'products', 'reviews'])
                                         ->where('to_admin', 1)->get();

            return response()->json(['notif' => $notifications]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function admin_editInfo(Request $req){

        try{

            $admin = Admin::where('id', 1)->first();

            $data = json_decode($req->data);

            if(!$admin){

                return response()->json(['message' => 'admin not found']);
            }

            $image = $req->file('file_photo');

            if($image){
                $filename = time(). '_' .$image->getClientOriginalName();
                $image->storeAs('public/uploads/', $filename);
                $imagepath = 'storage/uploads/'.$filename;
            }

            $admin->username = $data->username;
            $admin->password = $data->password;
            $admin->path = $imagepath;

            if($admin->save()){

                $admin = Admin::where('id', 1)->first();
                return response()->json(['message' => 'success', 'admin' => $admin]);
            }

            return response()->json(['message' => 'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function admin_returnInfo(Request $req){

        try{

            $admin = Admin::where('id', 1)->first();

            return response()->json(['message' => 'success', 'info' => $admin]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function returnShops(Request $req){

        try{
            $shops = Shop::all();

            return response()->json(['shops'=>$shops]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }



    public function admin_deleteBuyer_account(Request $req){
        
        try{

            $id = $req->id;

            //notification and message delete
            $messages = Message::where('from_id', $id)
                               ->orWhere('to_id', $id)
                               ->get();

            if(!$messages->isEmpty()){

                foreach($messages as $m){

                    $notification = Notification::where('message_id', $m->id)->first();

                    if($notification){
                        $notification->delete();
                    }

                    $m->delete();
                }
            }

            //reviews delete
            $reviews = Review::where('from_id', $id)->get();

            if(!$reviews->isEmpty()){

                foreach($reviews as $r){

                    //photo
                    $photos = Reviewphoto::where('review_id', $r->id)->get();
                    //video
                    $videos = Reviewvideo::where('review_id', $r->id)->get();
                    //notification
                    $notifs = Notification::where('review_id', $r->id)->get();

                    if(!$photos->isEmpty()){
                        
                        foreach($photos as $photo){
                            $photo->delete();
                        }
                    }
                    if(!$videos->isEmpty()){
                        
                        foreach($videos as $video){
                            $video->delete();
                        }
                    }
                    if(!$notifs->isEmpty()){
                        
                        foreach($notifs as $n){
                            $n->delete();
                        }
                    }


                    //delete review
                    $r->delete();

                }
            }
            

            //records
            $records = Record::where('user_id', $id)->get();

            if(!$records->isEmpty()){

                foreach($records as $re){

                    $notif = Notification::where('record_id', $re->id)->first();

                    if($notif){
                        $notif->delete();
                    }

                    $re->delete();
                }
            }

            //follower
            $follower = Follower::where('follower_id', $id)->first();

            if($follower){
                $follower->delete();
            }

            //delete completely notification

            $notifications = Notification::where('from_id', $id)->get();

            if(!$notifications->isEmpty()){
                
                foreach($notifications as $notif){

                    $notif->delete();
                }
            }

            //DELETE ACCOUNT
            $account = User::where('id', $id)->first();

            if($account){

                $data = [
                    'username' => $account->name,
                    'message' => $req->message,
                    'fullname' => "$account->firstname $account->lastname",
                    'email' => $account->email,
                    'username' => $account->name
                ];

                Mail::to($account->email)->send(new DeleteAccountMail(($data)));
                $account->delete();
            }

            return response()->json(['message' => 'success']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }







    public function admin_deleteSeller_account(Request $req){

        try{

            $seller_id = $req->id;

            //get shop
            $shop = Shop::where('user_id', $seller_id)->first();
            //get seller
            $seller = User::where('id', $seller_id)->first();
            
            if(!$shop){

                return response()->json(['message' => 'shop not exist']);
            }

            //notification and message delete
            $messages = Message::where('from_id', $seller_id)
                               ->orWhere('to_id', $seller_id)
                               ->get();

            if(!$messages->isEmpty()){

                foreach($messages as $m){

                    $notification = Notification::where('message_id', $m->id)->first();

                    if($notification){
                        $notification->delete();
                    }

                    $m->delete();
                }
            }

            
            //delete followers
            $followers = Follower::where('user_id', $seller_id)->get();
            if(!$followers->isEmpty()){

                foreach($followers as $f){

                    $f->delete();
                }
            }

            //reviews delete
            $reviews = Review::where('shop_id', $shop->id)->get();

            if(!$reviews->isEmpty()){

                foreach($reviews as $r){

                    //photo
                    $photos = Reviewphoto::where('review_id', $r->id)->get();
                    //video
                    $videos = Reviewvideo::where('review_id', $r->id)->get();
                    //notification
                    $notifs = Notification::where('review_id', $r->id)->get();

                    if(!$photos->isEmpty()){
                        
                        foreach($photos as $photo){
                            $photo->delete();
                        }
                    }
                    if(!$videos->isEmpty()){
                        
                        foreach($videos as $video){
                            $video->delete();
                        }
                    }
                    if(!$notifs->isEmpty()){
                        
                        foreach($notifs as $n){
                            $n->delete();
                        }
                    }
                    //delete review
                    $r->delete();
                }
            }

            //delete records
            $records = Record::where('seller_id', $seller_id)->get();

            if(!$records->isEmpty()){

                foreach($records as $re){

                    $notif = Notification::where('record_id', $re->id)->first();

                    if($notif){
                        $notif->delete();
                    }

                    $re->delete();
                }
            }

            //delete products
            $products = Product::where('shop_id', $shop->id)->get();
            if(!$products->isEmpty()){

                foreach($products as $p){

                    //delete video and photo of product
                    $photos = Photo::where('product_id', $p->id)->get();
                    foreach($photos as $photo){
                        $photo->delete();
                    }
                    $videos = Pvideo::where('product_id', $p->id)->get();
                    foreach($videos as $v){
                        $v->delete();
                    }

                    //delete notification
                    $notifications = Notification::where('product_id', $p->id)->get();

                    foreach($notifications as $notify){
                        $notify->delete();
                    }

                    //delete the product
                    $p->delete();
                }
            } 
            
            //delete completely notification

            $notifications = Notification::where('user_id', $seller_id)->get();

            if(!$notifications->isEmpty()){
                
                foreach($notifications as $notif){

                    $notif->delete();
                }
            }

            //delete shop
            $shop->delete();
            //delete seller
            $seller->delete();

            $data = [
                'username' => $seller->name,
                'message' => $req->message,
                'fullname' => "$seller->firstname $seller->lastname",
                'email' => $seller->email,
                'username' => $seller->name
            ];


            Mail::to($seller->email)->send(new DeleteAccountMail(($data)));

            return response()->json(['message' => 'success']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function admin_activate_user(Request $req){

        try{

            $id = $req->id;

            $user = User::where('id', $id)->first();

            if(!$user){
                return response()->json(['message' => 'user not found']);
            }

            $user->is_deactivate = 0;
            $user->deact_from_admin = 0;

            $data = [
                'user' => $user,
                'message' => $req->message
            ];

            $data = [
                'message' => $req->message,
                'fullname' => "$user->firstname $user->lastname",
                'email' => $user->email,
                'username' => $user->name
            ];

            if($user->save()){

                Mail::to($user->email)->send(new ActivateMail(($data)));

                return response()->json(['message' => 'success']);
            }

            return response()->json(['message' => 'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function admin_deactivate_user(Request $req){

        try{

            $id = $req->id;

            $user = User::where('id', $id)->first();

            if(!$user){
                return response()->json(['message' => 'user not found']);
            }

            $user->is_deactivate = 1;
            $user->deact_from_admin = 1;

            $data = [
                'user' => $user,
                'message' => $req->message
            ];

            $data = [
                'message' => $req->message,
                'fullname' => "$user->firstname $user->lastname",
                'email' => $user->email,
                'username' => $user->name
            ];

            if($user->save()){

                Mail::to($user->email)->send(new DeactivateMail(($data)));

                return response()->json(['message' => 'success']);
            }

            return response()->json(['message' => 'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function admin_unblock_product(Request $req){

        try{

            $product = Product::where('id', $req->product_id)->first();

            $data = json_decode($req->data);

            if(!$product){

                return response()->json(['message' => 'product not exist']);
            }

            $product->is_blocked = 0;

            if($product->save()){

                Mail::to($req->gmail)->send(new UnblockProductMail(($data)));

                return response()->json(['message' => 'success']);
            }

            return response()->json(['message' => 'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function admin_block_product(Request $req){

        try{

            $product = Product::where('id', $req->product_id)->first();
            
            $data = json_decode($req->data);

            if(!$product){

                return response()->json(['message' => 'product not exist']);
            }

            $product->is_blocked = 1;

            if($product->save()){

                Mail::to($req->gmail)->send(new BlockProductMail($data));

                return response()->json(['message' => 'success']);
            }

            return response()->json(['message' => 'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function admin_delete_review(Request $req){

        try{

            $review = Review::where('id', $req->review_id)->first();

            $product = null;

            if(!$review){

                return response()->json(['message' => 'review not exist']);
            }

            //get the product
            $product = Product::where('id', $review->product_id)->first();

            if(!$product){

                return response()->json(['message' => 'product not exist']);
            }

            //delete notification
            $notifications = Notification::where('review_id', $review->id)->get();

            if(!$notifications->isEmpty()){

                foreach($notifications as $notif){

                    $notif->delete();
                }
            }

            //delete review photos
            $r_photos = Reviewphoto::where('review_id', $review->id)->get();

            if(!$r_photos->isEmpty()){

                foreach($r_photos as $photo){

                    $photo->delete();
                }
            }

            //delete review videos
            $r_videos = Reviewvideo::where('review_id', $review->id)->get();

            if(!$r_videos->isEmpty()){

                foreach($r_videos as $vid){

                    $vid->delete();
                }
            }


            //delete the review mismo

            $review->delete();


            //recalculate the overall rate of a product
            if($product){

                if($this->recalculate_product($product)){

                    return response()->json(['message' => 'success']);
                }
            }
            

            return response()->json(['message' => 'Something went wrong.']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function recalculate_product(Product $product){

        $reviews = Review::where('product_id', $product->id)->get();

        $average_rate = 0;

        if(!$reviews->isEmpty()){

            //calculate
            $sum = 0;

            foreach($reviews as $r){

                $sum += $r->rate;
                Log::info('sum', ['sum' => $sum]);
            }

            $average_rate = $sum / count($reviews);
        }

        $product->overall_rate = $average_rate;

        return $product->save();
    }

    public function admin_returnAll_reviews(Request $req){
        try{

            $reviews = Review::with(['user', 'shop', 'shop.reviews', 'shop.user', 'product', 'reviewphotos', 'reviewvideos'])
                             ->where('review_type', 'product')->get();
            
            $products = Product::get();
            
            $shops = Shop::get();

            return response()->json(['reviews' => $reviews, 'shops' => $shops, 'products' => $products]);   
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function admin_return_reviews_filterSearch(Request $req){
        try{

            $data = json_decode($req->search_data);

            if($data){

                // Get the current date and year
                $now = Carbon::now();
                $intyear = $now->year;

                // Initialize variables for from and to date
                $from = null;
                $to = $now;

                switch ($data->when) {
                    case '1': // Today
                        $from = $now->copy()->startOfDay(); // Start of today (00:00)
                        $to = $now->copy()->endOfDay();     // End of today (23:59:59)
                        break;

                    case '2': // Yesterday
                        $from = $now->copy()->subDay()->startOfDay(); // Start of yesterday
                        $to = $now->copy()->subDay()->endOfDay();     // End of yesterday
                        break;

                    case '3': // Last 7 Days
                        $from = $now->copy()->subDays(6)->startOfDay(); // 6 days ago to include today (7 days total)
                        $to = $now->copy()->endOfDay();                 // End of today
                        break;

                    case '4': // Last 30 Days
                        $from = $now->copy()->subDays(29)->startOfDay(); // 29 days ago to include today (30 days total)
                        $to = $now->copy()->endOfDay();                  // End of today
                        break;

                    case '5': // This Month
                        $from = $now->copy()->startOfMonth(); // Start of this month
                        $to = $now->copy()->endOfDay();       // End of today
                        break;

                    case '6': // Last Month
                        $from = $now->copy()->subMonth()->startOfMonth(); // Start of last month
                        $to = $now->copy()->subMonth()->endOfMonth();     // End of last month
                        break;

                    // case '7': // Custom Range
                    //     // Assume you're parsing custom dates from the request
                    //     $from = Carbon::parse($request->custom_from)->startOfDay();
                    //     $to = Carbon::parse($request->custom_to)->endOfDay();
                    //     break;
                }
            }


            if($req->client_type === 'buyer'){

                $shop = Shop::where('name', $data->shop_name)->first();
                
                if($shop){
                    $product = Product::where('name', $data->product_name)->first();
                    $reviews = Review::with(['product', 'user', 'shop', 'shop.user']) 
                                     ->where('reviews.shop_id', $shop->id)  
                                     ->where(function($query) use($product) {
                                    
                                        if($product){

                                            $query->where('reviews.product_id', $product->id);
                                        }
                                    })
                                    ->whereBetween('reviews.created_at', [$from, $to])
                                    ->get();
                }  
                else{
                    $reviews = Review::with(['product', 'user', 'shop', 'shop.user']) 
                                    ->whereBetween('reviews.created_at', [$from, $to])
                                    ->get();
                }
            }
            else{

                $buyer = User::where('name', $data->buyer_name)->first();

                if($buyer){

                    $reviews = Review::with(['product', 'user', 'shop', 'shop.user']) 
                                    ->where('from_id', $buyer->id)
                                    ->whereBetween('reviews.created_at', [$from, $to])
                                    ->get();
                }
                else{
                    $reviews = Review::with(['product', 'user', 'shop', 'shop.user']) 
                                    ->whereBetween('reviews.created_at', [$from, $to])
                                    ->get();
                }
            }



            return response()->json(['reviews' => $reviews]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function admin_return_names(Request $req){

        try{

            if($req->type === 'buyer'){
                $shops = Shop::with(['products'])->get();

                return response()->json(['data' => $shops]);
            }
            else{

                $buyers = User::where('role', 'buyer')->get();

                return response()->json(['data' => $buyers]);
            }
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function admin_edit_Shop(Request $req){
        try{

            // name: this.user_clicked.shop.name,
            // address: this.user_clicked.shop.address,
            // description: this.user_clicked.shop.description,
            
            $data = json_decode($req->data);
            $user_id = $req->user_id;
            $shop = Shop::where('user_id', $user_id)->first();

            if(!$shop){
                return response()->json(['message' => 'Shop not exist']);
            }

            $shop->name = $data->name;
            $shop->description = $data->description;
            $shop->address = $data->address;

            if($shop->save()){

                return response()->json(['message' => 'success']);
            }

            return response()->json(['message' => 'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }
    public function admin_edit_UserInfo(Request $req){
        try{

            $data = json_decode($req->data);
            $user_id = $req->user_id;

            $user = User::where('id', $user_id)->first();
            
            if(!$user){
                return response()->json(['message' => 'user not exist']);
            }

            $user->age = $data->age;
            $user->gender = $data->gender;
            $user->birthday = $data->birthday;
            $user->contact_no = $data->contact_no;
            $user->current_address = $data->current_address;

            if($user->save()){

                $user = User::where('id', $user_id)->first();
                return response()->json(['message' => 'success', 'user' => $user]);
            }

            return response()->json(['message' => 'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }
    public function returnSeller_info(Request $req){
        try{
            
            $seller = User::where('id', $req->seller_id)->first();

            $shop = Shop::with(['products', 
                                'products.videos',
                                'products.shop', 
                                'products.photos',
                                'reviews', 
                                'products.reviews.reviewphotos',
                                'products.reviews.reviewvideos',
                                'reviews.reviewphotos',
                                'products.reviews.user', 
                                'user.followers.followedBy', 
                                'reviews.user'])->where('shops.user_id',$seller->id)->first();

            return response()->json(['message'=>$seller, 'shop'=>$shop]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function admin_return_allusers(Request $req){
        
        try{

            $buyers = [];
            $sellers = [];

            $u_buyers = User::with([
                'reviews',
                'reviews.reviewphotos',
                'reviews.reviewvideos'
            ])->where('role', 'buyer')->get();
            
            $u_sellers = User::with([
                'shop', 
                'shop.reviews', 
                'followers',
                ])->where('role', 'seller')->get();

            $buyers = $u_buyers;
            $sellers = $u_sellers;

            return response()->json(['buyers' => $buyers, 'sellers' => $sellers]);
        }
        catch(\Exception $ex){

            Log::info('error', ['error' => $ex->getMessage()]);
            return response()->json(['message' => $ex->getMessage()]);
        }
    }

    public function admin_check_login(Request $req){

        $data = $req->input();

        Log::info('data', ['data' => $data]);

        $admin = Admin::where('username', '=', $data['username'])->first();

        if($admin){

            if($admin->password === $data['password']){

                // $req->session()->put('email', [$admin->email]);

                //CREATE AND SEND THE OTP TO THE EMAIL
                $code = mt_rand(100000, 999999);

                $otp = new Adminotp();
                $otp->otp = $code;
                $otp->admin_id = $admin->id;

                if($otp->save()){
                    //send email to user thru gmail
                    Mail::to($admin->email)->send(new OtpMail($code));

                    return redirect()->route('admin_login')->with(['email' => $admin->email, 'response' => 'exist']);
                }

                return redirect()->route('admin_login')->with(['message' => 'OTP FAILED. PLEASE RELOAD AND TRY AGAIN.']);
            }

            return redirect()->route('admin_login')->with('message', "PASSWORD IS NOT CORRECT");
        }

        return redirect()->route('admin_login')->with(['message' => 'ACCOUNT NOT FOUND']);
    }
}
