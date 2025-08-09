<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Notification;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Empty_;

class NotificationController extends Controller
{
    public function returnNotifications(Request $request){
        try{
            if($request->type === "buyer"){
                $following_shops = Follower::select('user_id')->where('follower_id',$request->id)->get()->toArray();
                $notifications = Notification::with(['users','products.photos', 'products.shop'])
                                            ->whereIn('notifications.from_id', $following_shops)
                                            ->get();
            }
            else{
                $following_shops = Follower::select('follower_id')->where('user_id',$request->id)->get()->toArray();
                $notifications = Notification::with(['users','products.photos', 'products.shop'])
                                            ->whereIn('notifications.from_id', $following_shops)
                                            ->get();
            }
            
            return response()->json(['message'=>$notifications]);
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

            $notify = Notification::where('user_id', $req->id)->get();

            if($notify->isEmpty()){
                return response()->json(['message'=>false]);
            }

            foreach($notify as $n){
                
                if($n->seen === 0){
                    return response()->json(['message'=>true]);
                }
            }

            return response()->json(['message'=>false]);
       }
       catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage()]);
        } 
    }
}
