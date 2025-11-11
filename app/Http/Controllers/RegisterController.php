<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\account;
use App\Models\Notification;
use App\Models\Otp;
use App\Models\Pendingaccounts;
use App\Models\Pendingshop;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function register(Request $request){
        try{
            $data = json_decode($request->data);

            $role = $request->role;

            Log::info('data', ['data'=>$data]);
            Log::info('req',['req'=>$request->file('image')]);

            $user = User::where('email',$data->email)
                        ->orWhere('name', $data->username)->first();

            if($user){
                return response()->json(['message'=>'exist']);
            }

            $image = $request->file('image');

            if($image){
                $filename = time(). '_' .$image->getClientOriginalName();
                $image->storeAs('public/uploads/', $filename);
                $imagepath = 'storage/uploads/'.$filename;
            }


            $account = new Pendingaccounts();
            $account->name = $data->username;
            $account->email = $data->email;
            $account->password = $data->password;
            $account->role = $role;
            $account->firstname = $data->firstname;
            $account->m_initial = $data->m_initial;
            $account->lastname = $data->lastname;
            $account->gender = $data->gender;
            $account->birthday = $data->birthday;
            $account->age = $data->age;
            $account->contact_no = $data->contact_no;
            $account->current_address = 'sabang';
            $account->total_followers = 0;
            $account->profile = $imagepath;

            if($account->save()){

                if($role == "seller"){
                    $shopdata = json_decode($request->shopdata);
                    $pendingshop = new Pendingshop();
                    $pendingshop->user_id = $account->id;
                    $pendingshop->name = $shopdata->shop_name;
                    $pendingshop->address = $shopdata->shop_address;
                    $pendingshop->description = $shopdata->shop_description;
                    $pendingshop->category = json_encode($shopdata->shop_category);
                    //profile photo
                    $image = $request->file('shopimage');

                    if($image){
                        $shopimgfilename = time() . "_" . $image->getClientOriginalName();
                        $image->storeAs('public/uploads/', $shopimgfilename);

                        $path = "storage/uploads/" . $shopimgfilename;

                        $pendingshop->profile_photo = $path;
                    }

                    $pendingshop->latitude = $shopdata->latitude;
                    $pendingshop->longitude = $shopdata->longitude;
                    $pendingshop->total_views = 0;
                    $pendingshop->overall_rate = 0;

                    if(!$pendingshop->save()){
                        return response()->json(['message'=>'pendinshop is error']);
                    }
                }

                $code = mt_rand(100000, 999999);
                
                $otp = new Otp();
                $otp->userpending_id = $account->id;
                $otp->otp = $code;

                if($otp->save()){
                    //send email to user thru gmail
                    Mail::to($data->email)->send(new OtpMail($code));

                    return response()->json(['message'=>'Successfully']);
                }
                return response()->json(['message'=>'otp failed']);
            }


            return response()->json(['message'=>$imagepath]);
        }
        catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage(), 500]);
        }
    }

    public function sendotp(Request $request){
        try{

            $code = $request->code;

            $otp = Otp::where('otp','=',$code)->first();

            if(!$otp){
                return response()->json(['message'=>'not exist']);
            }

            $account = Pendingaccounts::where('id','=',$otp->userpending_id)->first();

            $user = new User();
            $user->name = $account->name;
            $user->email = $account->email;
            $user->password = $account->password;
            $user->role = $account->role;
            $user->firstname = $account->firstname;
            $user->m_initial = $account->m_initial;
            $user->lastname = $account->lastname;
            $user->gender = $account->gender;
            $user->birthday = $account->birthday;
            $user->age = $account->age;
            $user->contact_no = $account->contact_no;
            $user->current_address = $account->current_address;
            $user->total_followers = $account->total_followers;
            $user->profile = $account->profile;

            if($user->save()){

                if($account->role == 'seller'){
                    $data = Pendingshop::where('user_id','=',$otp->userpending_id)->first();

                    $shop = new Shop();
                    $shop->user_id = $user->id;
                    $shop->name = $data->name;
                    $shop->address = $data->address;
                    $shop->description = $data->description;
                    $shop->category = $data->category;
                    $shop->profile_photo = $data->profile_photo;
                    $shop->latitude = $data->latitude;
                    $shop->longitude = $data->longitude;
                    $shop->total_views = $data->total_views;
                    $shop->overall_rate = $data->overall_rate;

                    if($shop->save()){
                        
                        $otp->delete();
                        $data->delete();
                        $account->delete();
                        return response()->json(['message'=>'successful']);
                    }

                    //add notification for admin
                    $notif = new Notification();

                    $notif->to_admin = 1;
                    $notif->from_id = $user->id;
                    $notif->text = "NEW SELLER ACCOUNT HAD BEEN REGISTERED. $user->firstname $user->lastname.";
                    $notif->seen = 0;
                    $notif->type = 'register';
                    $notif->favorite = 0;

                    $notif->save();
                }
                else{

                    //add notification for admin
                    $notif = new Notification();

                    $notif->to_admin = 1;
                    $notif->from_id = $user->id;
                    $notif->text = "NEW BUYER ACCOUNT HAD BEEN REGISTERED. $user->firstname $user->lastname.";
                    $notif->seen = 0;
                    $notif->type = 'register';
                    $notif->favorite = 0;

                    $notif->save();
                }

                $otp->delete();
                $account->delete();
            
                return response()->json(['message'=>'successful']);
            }

        }
        catch(\Exception $err){
            return response()->json(['message'=>$err->getMessage(), 500]);
        }
    }
}
