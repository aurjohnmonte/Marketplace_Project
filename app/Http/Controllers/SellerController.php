<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Events\NotifyEvent;
use App\Models\Follower;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SellerController extends Controller
{
    public function returnProfile_info(){
        try{
            if(!session()->has("email")){
                return response()->json(['message'=>'no email']);
            }
            $email = session('email');

            $user = User::returnProfileInfo($email);

            $shop = Shop::with(['products', 'products.shop', 'products.photos','reviews', 'products.reviews', 'user.followers.followedBy', 'reviews.user'])->where('shops.user_id',$user['id'])->first();

            return response()->json(['message'=>$user, 'shop'=>$shop]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function returnConversation(Request $request){
        try{

            $user = User::where('name', $request->username)->first();
            $id = $request->id;

            if(!$user){
                return response()->json(['message'=>'no user found']);
            }

            $messages = Message::where(function ($query) use($user, $id) {
                                    $query->where("from_id",$user->id)
                                        ->where('to_id', $id);
                                })
                               ->orWhere(function ($query) use($user, $id) {
                                    $query->where("from_id", $id)
                                          ->where("to_id", $user->id);
                               })
                               ->get();

            return response()->json(['messages'=>$messages, 'user_info'=>$user]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function sentMessage(Request $request){
        try{
            $receiver = User::where("name", $request->username)->first();
            $mention_id = json_decode($request->mention_product_id);
            Log::info('username', ['username'=>$receiver]);
            if(!$receiver){
                return response()->json(['message'=>'receiver not found']);
            }

            $message = new Message();

            $message->from_id = $request->from_id;
            $message->to_id = $receiver->id;
            $message->messages = $request->message;
            $message->mention = $mention_id;

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
                $message = $notify->addNotification('message', $request->from_id, null, $receiver->id, null, $message->id);
                broadcast(new MessageEvent($receiver->name));
                broadcast(new NotifyEvent($request->from_id));
                return response()->json(['message'=>$message]);
            }
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function editMessage(Request $req){
        try{

            $message = Message::where('id', $req->message_id)->first();

            $message->messages = $req->text;

            if($message->save()){
                return response()->json(['message'=>'successful']);
            }
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function deleteMessage(Request $req){
        try{

            $message = Message::where('id', $req->message_id)->first();

            if(!$message){
                return response()->json(['message'=>'no exist']);
            }

            $notification = Notification::where('message_id', $req->message_id)->first();

            if($notification){

                $notification->delete();
            }

            $message->delete();
            
            return response()->json(['message'=>'successful']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function returnDashboardProducts(Request $req){
        try{

            $products = Product::with(['reviews'])->where('shop_id', $req->shop_id)->get();

            return response()->json(['message'=>$products]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function change_coverPhoto(Request $req){
        try{
            $shop = Shop::find($req->id);
            
            if(!$shop){
                return response()->json(['message'=>'not exist']);
            }

            $file = $req->file('file');

            if($file){
                $filename = time() . "_" . $file->getClientOriginalName();
                $file->storeAs("public/uploads", $filename);
                $path = "storage/uploads/" . $filename;

                if($shop->cover_photo){
                    $name = basename($shop->cover_photo);
                    if(Storage::disk("public")->exists("uploads/" . $name)){
                        Storage::disk("public")->delete("uploads/" . $name);
                    }
                }

                $shop->cover_photo = $path;

                $shop->save();

                return response()->json(['path'=>$path, 'message' => 'successful']);
            }
            return response()->json(['message'=>'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function change_profilePhoto(Request $req){
        try{
            $shop = Shop::find($req->id);
            
            if(!$shop){
                return response()->json(['message'=>'not exist']);
            }

            $file = $req->file('file');

            if($file){
                $filename = time() . "_" . $file->getClientOriginalName();
                $file->storeAs("public/uploads", $filename);
                $path = "storage/uploads/" . $filename;

                if($shop->profile_photo){
                    $name = basename($shop->profile_photo);
                    if(Storage::disk("public")->exists("uploads/" . $name)){
                        Storage::disk("public")->delete("uploads/" . $name);
                    }
                }

                $shop->profile_photo = $path;

                $shop->save();

                return response()->json(['path'=>$path, 'message' => 'successful']);
            }
            return response()->json(['message'=>'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function seller_shopDetails(Request $req){
        try{

            $shop = Shop::where('id', $req->id)->first();

            $shop->description = $req->description;

            if($shop->save()){
                return response()->json(['message'=>'successful', 'description' => $req->description]);
            }

            return response()->json(['message'=>'error', 'description' => 'error']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function seller_changeInfo(Request $req){
        try{

            $user = User::where('id', $req->id)->first();

            Log::info('user', ['user' => $user]);

            if(!$user){
                return response()->json(['message' => 'user not found']);
            }
            
            $user->firstname = $req->fname;
            $user->m_initial = $req->m_i;
            $user->lastname = $req->lname;
            $user->contact_no = $req->contact_no;
            $user->gender = $req->gender;
            $user->birthday = $req->birthday;
            $user->current_address = $req->address;
            
            $message = '';
            if(!$user->save()){
                $message = 'error';
            }
            else{
                $message = 'successful';
            }

            $user_info = User::returnProfileInfo($user->email);
            $shop = Shop::with(['products', 'products.shop', 'products.photos','reviews', 'products.reviews', 'user.followers.followedBy', 'reviews.user'])->where('shops.user_id',$req->id)->first();

            return response()->json(['message'=>$message, 'info'=>$user_info, 'shop' =>$shop]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function return_shopReviews(Request $req){
        try{
            $reviews = Review::where('shop_id', $req->shop_id)
                             ->where('review_type', 'shop')
                             ->get();
            
            return response()->json(['message' => $reviews]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function return_followers(Request $req){
        try{
            
            $followers = Follower::with('followedBy')->where('user_id', $req->id)->get();

            Log::info('followers', ['followers' => $followers]);

            $this_month_total = 0;

            if(count($followers) > 0){
                $current_month = (int) date('n');
                Log::info('month', ['month' => $current_month]);
                $this_month_total = 0;

                foreach($followers as $follower){

                    $month = $follower->created_at->month;

                    if($month === $current_month){
                        $this_month_total++;
                    }
                }
            }


            return response()->json(['followers' => $followers, 'current_month_total' => $this_month_total]);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }

    public function remove_follower(Request $req){
        try{

            $follower = Follower::where('follower_id', $req->follower_id)
                                ->where('user_id', $req->user_id)
                                ->first();

            if(!$follower){
                return response()->json(['message' => 'Follower not exist in system.']);
            }

            $follower->delete();

            return response()->json(['message' => 'successful']);
        }
        catch(\Exception $ex){
            return response()->json(['message'=>$ex]);
        }
    }
}
