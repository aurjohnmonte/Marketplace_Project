<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Empty_;

class NotificationController extends Controller
{
    public function returnNotifications(Request $request){
        try{
            if($request->type === "buyer"){
                $following_shops = Follower::select('user_id')->where('follower_id',$request->id)->get()->toArray();
                $data = Notification::with(['users', 'users.shop', 'products.photos', 'products.shop', 'products.records', 'reviews', 'messages'])
                                            ->where(function ($query) use($following_shops) {
                                                
                                                $query->where('notifications.type', '=', 'product')
                                                      ->whereIn('notifications.from_id', $following_shops);
                                            })
                                            ->orWhere('notifications.user_id', $request->id)
                                            ->orderBy('notifications.created_at', 'desc')
                                            ->get();
            }
            else{
                // $following_shops = Follower::select('follower_id')->where('user_id',$request->id)->get()->toArray();
                $notifications = Notification::with(['users','products.photos', 'products.shop', 'reviews', 'messages'])
                                            ->where('notifications.user_id', $request->id)
                                            ->orderBy('notifications.created_at', 'desc')
                                            ->get();
                    
                $data = [];

                foreach($notifications as $notify){

                    if($notify->users->is_deactivate === 0){
                        array_push($data, $notify);
                    }
                }
            }
            
            return response()->json(['message'=>$data]);
        }
        catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage()]);
        }
    }

    public function modifySeen(Request $request){
        try{

            $notify = Notification::where('id',$request->id)->first();

            $notify->seen = true;

            $notify->save();

            return response()->json(['message'=>'successful']);
        }
        catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage()]);
        }
    }

    public function actionNotify(Request $req){
        try{

            $type = null;
            if($req->type === 'notfav'){
                $type = 0;
            }
            else{
                $type = 1;
            }

            $notify = Notification::where('id', $req->notif_id)->first();

            if(!$notify){
                return response()->json(['message'=>'empty']);
            }

            $notify->favorite = $type;

            if($notify->save()){
                return response()->json(['message'=>'successful']);
            }

            return response()->json(['message'=>'error']);
        }
        catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage()]);
        }
    }

    public function changeSeen(Request $req){
        try{

            $notify = Notification::where('id',$req->id)->first();
            if(!$notify){
                return response()->json(['message'=>'empty']);
            }

            $notify->seen = 1;

            if($notify->save()){
                return response()->json(['message'=>'successful']);
            }

            return response()->json(['message'=>'error']);
        }
        catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage()]);
        }
    }

    public function deleteNotif(Request $req){
        try{

            $data = json_decode($req->data);

            $notifications = Notification::whereIn('id',$data)->get();

            if($notifications->isEmpty()){
                return response()->json(['message'=>'empty']);
            }

            foreach($notifications as $notify){
                $notify->delete();
            }

            return response()->json(['message'=>'successful']);
        }
        catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage()]);
        }
    }

    public function checkNotify(Request $req){
       try{

            $notify = Notification::where('user_id', $req->id)->orderBy('created_at', 'desc')->get();

            Log::info('notify', ['notify'=>$notify]);

            if($notify->isEmpty()){
                return response()->json(['message'=>false, 'notifications' => $notify]);
            }

            foreach($notify as $n){
                
                if($n->seen === 0){
                    return response()->json(['message'=>true, 'notifications' => $notify]);
                }
            }

            return response()->json(['message'=>false, 'notifications' => $notify]);
       }
       catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage()]);
        } 
    }

    public function return_messageList(Request $request){
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
            $user_id = [];
            $result = [];
            foreach($latest as $i){
                if($i->user1 !== $id){
                    array_push($user_id, $i->user1);
                }
                else{
                    array_push($user_id, $i->user2);
                }
                array_push($array, $i->latest_id);
            }

            Log::info('id',['id'=>$user_id]);

            $messages = Message::select('messages.from_id', 'messages.to_id', 'messages.updated_at', 'messages.messages')
                                ->join("users as sender",'messages.from_id','=','sender.id')
                                ->join("users as receiver",'messages.to_id','=','receiver.id')
                                ->whereIn('messages.id',$array)
                                ->where('sender.is_deactivate', '=', 0)
                                ->where('receiver.is_deactivate', '=', 0)
                               ->get();

            Log::info('messages', ['messages'=>$messages]);
            
            $users = User::whereIn('id', $user_id)->get();
            $conversation = [];

            if(!$users->isEmpty()){

                foreach($messages as $m){
                    
                    foreach($users as $user){

                        if($m->from_id === $user->id){
                            array_unshift($conversation, [$m, $user]);
                            break;
                        }
                        else if($m->to_id === $user->id){
                            array_unshift($conversation, [$m, $user]);
                            break;
                        }
                    }
                }
            }

            return response()->json(['messages'=>$conversation]);
        }
        catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage()]);
        } 
    }

    public function mark_as_read_many(Request $req){
        try{

            $list_id = json_decode($req->list_id);

            $notifications = Notification::whereIn('id', $list_id)->get();

            if(!$notifications->isEmpty()){

                foreach($notifications as $notify){
                    $notify->seen = true;
                    $notify->save();
                }
                    
                return response()->json(['message'=>'successful']);
            }
            return response()->json(['message'=>'error']);
        }
        catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage()]);
        } 
    }
}
