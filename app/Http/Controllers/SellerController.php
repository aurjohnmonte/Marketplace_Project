<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Events\NotifyEvent;
use App\Models\Message;
use App\Models\Notification;
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

            $shop = Shop::with(['products', 'products.photos','reviews', 'products.reviews', 'user.followers'])->where('shops.user_id',$user['id'])->first();

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
            Log::info('username', ['username'=>$receiver]);
            if(!$receiver){
                return response()->json(['message'=>'receiver not found']);
            }

            $message = new Message();

            $message->from_id = $request->from_id;
            $message->to_id = $receiver->id;
            $message->messages = $request->message;

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
}
