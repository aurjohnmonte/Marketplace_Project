<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Review extends Model
{
    public function user(){
        return $this->belongsTo(User::class, 'from_id');
    }

    public function reviewphotos(){
        return $this->hasMany(Reviewphoto::class, 'review_id');
    }

    public function reviewvideos(){
        return $this->hasMany(Reviewvideo::class, 'review_id');
    }

    public function calculate_averate_rate($reviews){

        if($reviews->isEmpty()){
            Log::info('reviews',['reviews'=>$reviews]);
            return 'empty';
        }

        $reviews = $reviews->toArray();

        $sum = 0;
        $count = count($reviews);
        foreach($reviews as $review){
            $sum += $review['rate'];
            Log::info("sum",["sum"=>$sum]);
        }

        return ($sum / $count);
    }

    public function addReview(Request $req): string
    {
        try{

            $review_info = json_decode($req->review_info);
            
            $arr = ['shop', 'product'];

            foreach($arr as $turn){

                $review = new Review();
                $review->from_id = $req->from_id;

                if($turn === "product"){
                    $review->product_id = $req->to_id;
                    $review->shop_id = $req->shop_id;   

                    $review->review_type = $turn;
                    $review->comment = $review_info->comment;
                    $review->rate = (int) $review_info->product_rate;
                    $review->to = $turn;
                }
                else if($turn === "shop"){
                    Log::info('enter', ['enter' => 'sulod 1']);
                    $review->shop_id = $req->shop_id;

                    $review->review_type = $turn;
                    $review->rate = (int) $review_info->shop_rate;
                    $review->to = $turn;
                }

                if(!$review->save()){

                    return response()->json(['message' => 0]);
                }

                //upload photo and video for product review
                if($turn === "product"){
                    //check if user sent a photo in review
                    Log::info('photos', ['photos'=>$req->file('photos')]);
                    if($req->file('photos')){

                        foreach($req->file('photos') as $photo){
                            $reviewphoto = new Reviewphoto();

                            $filename = time() . "_" . $photo->getClientOriginalName();
                            $photo->storeAs('public/review_photo/',$filename);
                            $path = "storage/review_photo/" . $filename;

                            $reviewphoto->path = $path;
                            $reviewphoto->review_id = $review->id;

                            if(!$reviewphoto->save()){
                                return "error";
                            }
                        }
                    }

                    //check if user send a video in review
                    Log::info('videos', ['videos'=>$req->file('videos')]);
                    if($req->file('videos')){

                        foreach($req->file('videos') as $video){
                            $reviewvideo = new Reviewvideo();

                            $filename = time() . "_" . $video->getClientOriginalName();
                            $video->storeAs('public/review_video/',$filename);
                            $path = "storage/review_video/" . $filename;

                            $reviewvideo->path = $path;
                            $reviewvideo->review_id = $review->id;

                            if(!$reviewvideo->save()){
                                return "error";
                            }
                        }
                    }
                }

                if(true){

                    $notify = new Notification();

                    if($turn === "product"){

                        $reviews_product = $this->select('rate')
                                                ->where('product_id', $req->to_id)
                                                ->where('review_type', 'product')
                                                ->get();

                        Log::info('reviews', ['reviews'=>$reviews_product]);
                        $result = $this->calculate_averate_rate($reviews_product);
                        $data = Product::where('id', $req->to_id)->first();


                        //ADD NEW NOTIFICATION WHEN BUYER RATE PRODUCT
                        $message = $notify->addNotification('rate product', $req->from_id, $req->to_id, $req->user_id, $review->id);

                        Log::info('message', ['message'=>$message]);
                    }
                    else if($turn === "shop"){

                        Log::info('enter', ['enter' => 'sulod 2']);
                        
                        // $reviews_shop = $this->select('reviews.rate')->where('shop_id', $req->to_id)->get();
                        $reviews_shop =  $review->select('reviews.rate')
                                            ->where('shop_id', $req->shop_id)
                                            ->where('review_type', 'shop')
                                            ->get();

                        Log::info('reviews', ['reviews'=>$reviews_shop]);

                        $result = $this->calculate_averate_rate($reviews_shop);

                        //get the shop
                        $data = Shop::where('id', $req->shop_id)->first();

                        //get the seller
                        $seller = User::where('id', $data->user_id)->first();

                        //ADD NEW NOTIFICATION WHEN BUYER RATE SHOP
                        // $message = $notify->addNotification('rate shop', $req->from_id, null, $seller->id, $this->id);
                        // Log::info('message', ['message'=>$message]);
                    }

                    if($result === 'empty'){
                        $data->overall_rate = (int) $review_info->product_rate;
                    }
                    else{
                        $o_rate = ceil(($result * 10))/10;
                        $data->overall_rate = $o_rate;
                    }

                    if(!$data->save()){

                        Log::info("error", ["error"=>"$reviews_product - $data"]);
                    }
                    
                    if($turn === 'product'){
                        return $o_rate;
                    }
                }
            }


            return (int) $review_info->product_rate;
        }
        catch(\Exception $err){
            Log::info('error: ', ['error'=>$err->getMessage()]);
            return "empty";
        }
    }
}
