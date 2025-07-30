<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

            $receiver = User::where("id", $request->receiver_id)->first();
            Log::info('username', ['username'=>$receiver->name]);
            if(empty($receiver)){
                return response()->json(['message'=>'receiver not found']);
            }

            $message = new Message();

            $message->from_id = $request->sender_id;
            $message->to_id = $request->receiver_id;
            $message->messages = $request->message_text;

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
                broadcast(new MessageEvent($receiver->name));
                return response()->json(['message'=>'successful']);
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
}
