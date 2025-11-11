<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Events\NotifyEvent;
use App\Events\SellerNotifyEvent;
use App\Models\Follower;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Pvideo;
use App\Models\Record;
use App\Models\Review;
use App\Models\Reviewphoto;
use App\Models\Reviewvideo;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDO;

class BuyerController extends Controller
{
    public function searchShops(Request $request){
        try{

            $shop_info = json_decode($request->shop_info);
            $search_text = $request->search_text;
            
            Log::info("data", ['data'=>$shop_info]);
            $result = User::searchUserShop($shop_info, $search_text);
            Log::infO("result", ['result'=>$result]);
            return response()->json(['message'=>$result]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function returnShopProfile(Request $request){
        try{
            $id = $request->id;
            $sender_id = $request->sender_id;
            $result = User::returnShopinfo($id);

            Log::info('data', ['data'=>[$id, $sender_id]]);

            $messages = Message::where(function ($query) use($id, $sender_id) {
                                    $query->where("from_id",$sender_id)
                                        ->where('to_id', $id);
                                })
                               ->orWhere(function ($query) use($id, $sender_id) {
                                    $query->where("from_id", $id)
                                          ->where("to_id", $sender_id);
                               })
                               ->get();

            return response()->json(['info'=>$result, 'messages'=>$messages]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function returnProfile_info(){
        try{
            if(!session()->has("email")){
                return response()->json(['message'=>'no email']);
            }
            $email = session('email');

            $user = User::returnProfileInfo($email);
            return response()->json(['message'=>$user]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function messageSent(Request $request){
        try{

            $receiver = User::with('shop')->where("id", $request->receiver_id)->first();
            Log::info('username', ['username'=>$receiver]);
            if(empty($receiver)){
                return response()->json(['message'=>'receiver not found']);
            }

            $message = new Message();

            $message->from_id = $request->sender_id;
            $message->to_id = $request->receiver_id;
            $message->messages = $request->message_text;
            $message->mention = json_decode($request->mention_id);

            $image = $request->file('photo');
            Log::info("img",['img'=>$image]);
            if($image !== null){
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/message_img/', $filename);
                $path = "storage/message_img/" . $filename;

                $message->message_pic = $path;
            }
            else{
                $message->message_pic = null;
            }

            $message->seen = 0;
            $message->seen_at = null;

            if($message->save()){
                $notify = new Notification();
                $message = $notify->addNotification('message', $request->sender_id, null, $request->receiver_id, null, $message->id);
                broadcast(new MessageEvent($receiver->name));
                return response()->json(['message'=>$message]);
            }
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function returnMessages(Request $request){

        try{
            $id = (int) $request->id;
            $latest = DB::table('messages')
                        ->select(
                            DB::raw('LEAST(from_id, to_id) AS user1'),
                            DB::raw('GREATEST(from_id, to_id) AS user2'),
                            DB::raw('MAX(id) as latest_id')
                        )
                        ->groupBy('user1', 'user2')
                        ->where('to_id', $request->id)
                        ->orWhere('from_id', $request->id)
                        ->get();
            Log::info('userid', ['userid'=>$id]);
            
            Log::info('latest',['latest'=>$latest]);

            $array = [];
            $shop_id = [];
            $result = [];
            foreach($latest as $i){
                if($i->user1 !== $id){
                    array_push($shop_id, $i->user1);
                }
                else{
                    array_push($shop_id, $i->user2);
                }
                array_push($array, $i->latest_id);
            }

            Log::info('id',['id'=>$shop_id]);

            $shops = Shop::with('user')
                         ->join('users', 'shops.user_id', '=', 'users.id')
                         ->where('users.is_deactivate', 0)
                         ->whereIn('user_id', $shop_id)
                         ->get();

            $messages = Message::select('messages.from_id', 'messages.to_id', 'messages.updated_at', 'messages.messages')
                                ->join("users as sender",'messages.from_id','=','sender.id')
                                ->join("users as receiver",'messages.to_id','=','receiver.id')
                                ->whereIn('messages.id',$array)
                               ->get();
            
            Log::info("shops", ['shops'=>$messages]);

            for($i = 0; $i < count($shops); $i++){

                for($j = 0; $j < count($messages); $j++){
                    if($shops[$i]->user_id === $messages[$j]->from_id){
                        array_push($result, [$shops[$i], $messages[$j]]);
                        break;
                    }
                    else if($shops[$i]->user_id === $messages[$j]->to_id){
                        array_push($result, [$shops[$i], $messages[$j]]);
                        break;
                    }
                }
            }

            return response()->json(['messages'=>$result]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function deleteMessage(Request $request){
        try{

            $id = $request->id;

            $message = Message::where('id',$id)->first();

            if(empty($message)){
                return response()->json(['message'=>'message not exist']);
            }

            $notification = Notification::where('message_id', $id)->first();

            if($notification){

                $notification->delete();
            }

            if($message->delete()){
            
                return response()->json(['message'=>'successful']);
            }
            return response()->json(['message'=>'not successful']);
        }
         catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function editMessage(Request $request){
        try{

            $message = Message::where('id',$request->id)->first();

            $message->messages = $request->text;

            if($message->save()){
                return response()->json(['message'=>'successful']);
            }
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function returnShops(){
        try{
            $shops = Shop::join('users', 'shops.user_id', '=', 'users.id')
                         ->where('users.is_deactivate', 0)
                         ->get();

            return response()->json(['shops'=>$shops]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function returnProducts(){
        try{

            $popular = Product::returnProducts('popular');
            $new = Product::returnProducts('new');

            return response()->json(['popular' => $popular, 'new'=>$new]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function returnProductsBySearch(Request $request){
        try{    
            $info = json_decode($request->shop_info);
            Log::info('filter',['filter'=>$info->filter]);
            $query = Product::with(['photos','shop', 'shop.user', 'records', 'reviews'])
                               ->where('name','like',"%$request->search_text%")
                               ->whereHas('shop.user', function ($q){

                                    $q->where('is_deactivate', 0);
                               })
                               ->where(function($q) use($info) {

                                    if($info->category && $info->category !== ""){
                                        if($info->category !== "Any"){
                                            $q->where('category','like',"%$info->category%");
                                        }
                                    }
                               });

            if($info->filter && $info->filter !== ""){
                if($info->filter === 'New'){
                    
                    $query->where('created_at','>=',now()->subDays(30))
                          ->orderBy('created_at', 'desc');
                }

                else if($info->filter === "Popular"){
                    $query->orderBy('overall_rate', 'desc');
                }
            }

            $products = $query->get();

            Log::info('products',['products'=>$products]);


            return response()->json(['products'=>$products]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function addReview(Request $request){
        try{
            $review = new Review();
            $message = $review->addReview($request);

    

            return response()->json(['message'=>$message]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function returnReviews(Request $request){
        try{    

            // $seller_username = Product::select('users.name')
            //                ->join('shops', 'products.shop_id','=','shops.id')
            //                ->join('users', 'shops.user_id', '=', 'users.id')
            //                ->where('products.id', $request->product_id)
            //                ->first();

            // Log::info('seller', ['seller' => $seller_username]);

            if($request->type === "product"){

                $reviews = Review::with(['user', 'reviewphotos', 'reviewvideos'])
                                 ->where('product_id', $request->product_id)
                                 ->where('to',$request->type)
                                 ->get();
            }
            else if($request->type === "shop"){

                $reviews = Review::with('shop')
                                 ->where('shop_id', $request->shop_id)
                                 ->where('to',$request->type)
                                 ->get();
            }

            return response()->json(['message'=>$reviews]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function returnAllShops(){

        try{

            $popular = Shop::with('reviews', 'user')
                           ->whereHas('user', function ($q){

                                $q->where('is_deactivate', 0);
                           })
                           ->orderBy("overall_rate", "desc")
                           ->get();

            $new = Shop::with('reviews','user')
                        ->whereHas('user', function ($q){

                            $q->where('is_deactivate', 0);
                        })
                       ->orderBy("created_at", "desc")
                       ->get();

            return response()->json(['popular'=>$popular, 'new'=>$new]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function returnProduct(Request $request){
        try{
            $product = Product::with(['photos','shop', 'records'])
                              ->where('id',$request->id)->first();

            return response()->json(['message'=>'successful', 'product'=>$product]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function followShop(Request $request){
        try{
            
            $follower = new Follower();
            $follower->user_id = $request->user_id;
            $follower->follower_id = $request->id;

            if($follower->save()){

                //add notification for admin

                    $buyer = User::where('id', $request->id)->first();
                    $shop = Shop::where('user_id', $request->user_id)->first();

                    Log::info('buyer', ['buyer' => $buyer]);

                    $notif = new Notification();

                    $notif->to_admin = 1;
                    $notif->from_id = $buyer->id;
                    $notif->text = "Buyer $buyer->firstname $buyer->lastname followed shop $shop->name.";
                    $notif->seen = 0;
                    $notif->type = 'follow';
                    $notif->favorite = 0;

                    $notif->save();
                //end here

                return response()->json(['message'=>'successful']);
            }
            return response()->json(['message'=>'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function searchMessage(Request $request){
        try{

            Log::info('text',['text'=>$request->text]);
            $id = (int) $request->id;
            $latest = DB::table('messages')
                        ->select(
                            DB::raw('LEAST(from_id, to_id) AS user1'),
                            DB::raw('GREATEST(from_id, to_id) AS user2'),
                            DB::raw('MAX(id) as latest_id')
                        )
                        ->groupBy('user1', 'user2')
                        ->where('to_id', $request->id)
                        ->orWhere('from_id', $request->id)
                        ->get();

            Log::info('userid', ['userid'=>$id]);
            
            Log::info('latest',['latest'=>$latest]);

            $array = [];
            $shop_id = [];
            $result = [];
            foreach($latest as $i){
                if($i->user1 !== $id){
                    array_push($shop_id, $i->user1);
                }
                else{
                    array_push($shop_id, $i->user2);
                }
                array_push($array, $i->latest_id);
            }

            Log::info('id',['id'=>$shop_id]);

            $shops = Shop::whereIn('user_id',$shop_id)->get();

            $messages = Message::select('messages.from_id', 'messages.to_id', 'messages.updated_at', 'messages.messages')
                                ->join("users as sender",'messages.from_id','=','sender.id')
                                ->join("users as receiver",'messages.to_id','=','receiver.id')
                                ->whereIn('messages.id',$array)
                               ->get();
            
            Log::info("shops", ['shops'=>$messages]);

            for($i = 0; $i < count($shops); $i++){


                if(strpos($shops[$i]->name, $request->text) === false){

                    continue;
                }       

                for($j = 0; $j < count($messages); $j++){
                    if($shops[$i]->user_id === $messages[$j]->from_id){
                        array_push($result, [$shops[$i], $messages[$j]]);
                        break;
                    }
                    else if($shops[$i]->user_id === $messages[$j]->to_id){
                        array_push($result, [$shops[$i], $messages[$j]]);
                        break;
                    }
                }
            }

            return response()->json(['messages'=>$result]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function returnProduct_info(Request $req){
        try{

            $product = Product::with(['photos', 'reviews', 'shop', 'records'])
                              ->where('id', $req->id)
                              ->first();

            return response()->json(['product'=>$product]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function returnFollowing(Request $req){
        try{

            $follows = Follower::with('follows.shop')
                               ->join('users', 'followers.user_id', '=', 'users.id')
                               ->where('users.is_deactivate', 0)
                               ->where('follower_id',$req->id)
                               ->get();
            return response()->json(['message'=>$follows]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function changeProfile(Request $req){
        try{

            $user = User::find($req->id);
            
            if(!$user){
                return response()->json(['message'=>'not exist']);
            }

            $file = $req->file('file');
            $name = basename($user->profile);

            if($file){
                $filename = time() . "_" . $file->getClientOriginalName();
                $file->storeAs("public/uploads", $filename);
                $path = "storage/uploads/" . $filename;

                $user->profile = $path;

                if(Storage::disk("public")->exists("uploads/" . $name)){
                    Storage::disk("public")->delete("uploads/" . $name);
                }

                $user->save();

                return response()->json(['message'=>$path]);
            }
            return response()->json(['message'=>'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function editinfo(Request $req){
        try{

            $info = json_decode($req->user_info);
            $user = User::where('email', $req->email)->first();

            if(!$user){
                return response()->json(['message'=>'not exist']);
            }

            $user->gender = $info->gender;
            $user->age = (int) $info->age;
            $user->birthday = $info->birthday;
            $user->contact_no = $info->contact_no;
            $user->nearby_km = $info->nearby_km;

            if($user->save()){

                //add notification for admin
                    $notif = new Notification();

                    $notif->to_admin = 1;
                    $notif->from_id = $user->id;
                    $notif->text = "Buyer $user->name change his profile info.";
                    $notif->seen = 0;
                    $notif->type = 'profile';
                    $notif->favorite = 0;

                    $notif->save();
                //end here

                return response()->json(['message' => 'successful',
                                         'info'=> $info,
                                         'user' => $user]);
            }
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function editCredential(Request $req){
        try{

            $info = json_decode($req->user_info);
            $user = User::where('email', $req->email)->first();

            if(!$user){
                return response()->json(['message'=>'not exist']);
            }

            $user->name = $info->username;

            Log::info('username',['username'=>$info->username]);

            if($info->password !== "" && $info->password){

                $user->password = Hash::make($info->password);
            }

            if($user->save()){
                return response()->json(['message' => 'successful',
                                         'username'=> $user->name,
                                         'user' => $user]);
            }
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function record_respond(Request $req){
        try{
            
            $action = $req->action;

            $record = Record::where('id', $req->record_id)->first();

            if(!$record){
                return response()->json(['message' => 'record not found']);
            }

            if($action === 'confirm'){

                $record->status = 'confirmed';
            }
            else{
                $record->status = 'ignored';
            }

            if($record->save()){

                //this notification for updating the notify from buyer
                $notification = Notification::where('id', $req->notify_id)->first();

                if(!$notification){
                    return response()->json(['message' => 'notification not exist']);
                }

                $notification->status = 'answered';

                if($notification->save()){
                    //this notification is for the seller to notify him/her that buyer answered his/her record confirmation
                    $notify = new Notification();

                    $user = User::where('id', $req->user_id)->first();

                    $notify->from_id = $req->user_id;
                    $notify->seen = 0;
                    $notify->type = 'customer record';
                    $notify->record_id = $record->id;
                    $notify->product_id = $req->product_id;
                    $notify->user_id = $req->seller_id;
                    $notify->status = 'answered';
                    $notify->text = "$user->firstname $user->lastname has $action the record confirmation you sent.";

                    if($notify->save()){
                        
                        if($action === 'confirm'){
                            $message = new Message();

                            $message->from_id = $req->seller_id;
                            $message->to_id = $req->user_id;
                            $message->messages = "Hi, you've purchase my product. Your review will be a big help for my store. Please click the view above.";
                            $message->mention = $req->product_id;
                            $message->seen = 0;
                            $message->seen_at = null;

                            if($message->save()){
                                $notify = new Notification();
                                $message = $notify->addNotification('message', $req->seller_id, null, $req->user_id, null, $message->id);

                                $buyer = User::where('id', $req->user_id)->first();

                                if($buyer){
                                    broadcast(new MessageEvent($buyer->name));
                                    broadcast(new NotifyEvent($req->user_id, 'specific'));
                                }

                                return response()->json(['message'=>$message]);
                            }
                        }
                    }

                    broadcast(new SellerNotifyEvent($req->seller_username));
                    return response()->json(['message' => 'success']);
                }
            }

            return response()->json(['message' => 'errpr']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function unfollow(Request $req){
        try{
            $follows = Follower::where('user_id', $req->user_id)
                               ->where('follower_id', $req->follower_id)
                               ->first();

            if(!$follows){
                return response()->json(['message' => 'Something went wrong. Please reload the page']);
            }

            //add notification for admin

                $buyer = User::where('id', $follows->follower_id)->first();
                $shop = Shop::where('user_id', $follows->user_id)->first();

                $notif = new Notification();

                $notif->to_admin = 1;
                $notif->from_id = $buyer->id;
                $notif->text = "Buyer $buyer->firstname $buyer->lastname unfollowed shop $shop->name.";
                $notif->seen = 0;
                $notif->type = 'follow';
                $notif->favorite = 0;

                $notif->save();
            //end here

            $follows->delete();

            return response()->json(['message' => 'success']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function return_videos_product(Request $req){
        try{

            $videos = Pvideo::where('product_id', $req->id)->get();

            return response()->json(['videos' => $videos]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function buyer_deactivate(Request $req){
        try{

            $id = $req->id;

            $user = User::where('id', $id)->first();

            if(!$user){
                return response()->json(['message' => 'user not found']);
            }

            $user->is_deactivate = 1;

            if($user->save()){
                return response()->json(['message' => 'successful']);
            }

            return response()->json(['message' => 'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function buyer_delete_account(Request $req){
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

                $account->delete();
            }

            return response()->json(['message' => 'successful']);

        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }
}
