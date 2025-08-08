<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function returnNotifications(Request $request){
        try{
            $following_shops = Follower::select('user_id')->where('follower_id',$request->id)->get()->toArray();
            $notifications = Notification::with(['users','products.photos', 'products.shop'])
                                         ->whereIn('notifications.from_id', $following_shops)
                                         ->get();
            
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
}
