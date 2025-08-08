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
            $this->from_id = $req->from_id;
            $this->review_type = $req->type;
            $this->comment = $review_info->comment;
            $this->rate = (int) $review_info->rate;
            $this->to = $req->type;

            if($req->type === "product"){
                $this->product_id = $req->to_id;
            }
            else if($req->type === "shop"){
                $this->shop_id = $req->to_id;
            }

            //check if user sent a photo in review
            Log::info('photos', ['photos'=>$req->file('photos')]);
            if($req->file('photos') && $this->save()){

                foreach($req->file('photos') as $photo){
                    $reviewphoto = new Reviewphoto();

                    $filename = time() . "_" . $photo->getClientOriginalName();
                    $photo->storeAs('public/review_photo/',$filename);
                    $path = "storage/review_photo/" . $filename;

                    $reviewphoto->path = $path;
                    $reviewphoto->review_id = $this->id;

                    if(!$reviewphoto->save()){
                        return "error";
                    }
                }
            }

            if($this->save()){

                if($req->type === "product"){

                    $reviews_product = $this->select('rate')->where('product_id', $req->to_id)->get();

                    Log::info('reviews', ['reviews'=>$reviews_product]);
                    $result = $this->calculate_averate_rate($reviews_product);
                    $data = Product::where('id', $req->to_id)->first();
                }
                else if($req->type === "shop"){
                    
                    $reviews_shop = $this->select('reviews.rate')->where('shop_id', $req->to_id)->get();

                    $result = $this->calculate_averate_rate($reviews_shop);
                    $data = Shop::where('id', $req->to_id)->first();
                }

                if($result === 'empty'){
                    $data->overall_rate = (int) $review_info->rate;
                }
                else{
                    $o_rate = ceil(($result * 10))/10;
                    $data->overall_rate = $o_rate;
                }

                if(!$data->save()){

                    Log::info("error", ["error"=>"$reviews_product - $data"]);
                }
                return $o_rate;
            }


            return (int) $review_info->rate;
        }
        catch(\Exception $err){
            Log::info('error: ', ['error'=>$err->getMessage()]);
            return "empty";
        }
    }
}
