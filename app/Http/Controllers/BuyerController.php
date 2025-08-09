<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Follower;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Review;
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
                $message = $notify->addNotification('message', $request->sender_id, $request->receiver_id);
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

            $shops = Shop::with('user')->whereIn('user_id',$shop_id)->get();

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
            $shops = Shop::all();

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
            $query = Product::with(['photos','shop'])
                               ->where('name','like',"%$request->search_text%")
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

            if($request->type === "product"){
                $reviews = Review::with(['user', 'reviewphotos'])
                                 ->where('product_id', $request->product_id)
                                 ->where('to',$request->type)
                                 ->orderBy('created_at', 'desc')
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

            $popular = Shop::with('reviews')->orderBy("overall_rate", "desc")->get();
            $new = Shop::with('reviews')->orderBy("created_at", "desc")->get();

            return response()->json(['popular'=>$popular, 'new'=>$new]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    public function returnProduct(Request $request){
        try{
            $product = Product::with(['photos','shop'])
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

            $product = Product::with(['photos', 'reviews', 'shop'])
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

            if($user->save()){
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
}
