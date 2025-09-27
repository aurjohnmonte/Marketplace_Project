<?php

namespace App\Http\Controllers;

use App\Events\NotifyEvent;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Photo;
use App\Models\Product;
use App\Models\Record;
use App\Models\Review;
use App\Models\Reviewphoto;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDO;

class ProductController extends Controller
{
    public function addproduct(Request $request){
        try{

            $shop = Shop::where('user_id', $request->id)->first();

            if(empty($shop)){
                return response()->json(['message'=>'empty']);
            }

            $productData = json_decode($request->product);
            $product = new Product();
/*
name: '',
description: '',
category: '',
status: '',
price: '',
quantity: '',
images: [],
materials: '',
dimensions: '',
weight: ''
*/
            $product->shop_id = $shop->id;
            $product->description = $productData->description;
            $product->name = $productData->name;
            $product->category = $productData->category;
            $product->status = $productData->status;
            $product->price = $productData->price;
            $product->quantity = $productData->quantity;
            $product->materials = $productData->materials;
            $product->dimensions = $productData->dimensions;
            $product->weight = $productData->weight;
            $product->total_views = 0;
            $product->overall_rate = 0;
            

            if($product->save()){
                Log::info('images', ['images'=>$request->images]);
                foreach ($request->file('images') as $image) {
                    $photo = new Photo(); // Make sure this is the right model
                    Log::info('img', ['img'=>$image]);
                    if ($image !== null) {
                        $filename = time() . "_" . $image->getClientOriginalName();
                        $image->storeAs('public/product_img/', $filename);
                        $path = "storage/product_img/" . $filename;

                        $photo->product_id = $product->id;
                        $photo->filename = $path;

                        if (!$photo->save()) {
                            return response()->json(['message' => 'error']);
                        }
                    }
                }
                
                $notify = new Notification();
                $notify->from_id = $shop->user_id;
                $notify->text = "$shop->name added new product. Check it out.";
                $notify->seen = false;
                $notify->type= "product";
                $notify->product_id = $product->id;
                $notify->favorite = false;

                if($notify->save()){

                    broadcast(new NotifyEvent($shop->user_id));
                    $products = Product::with(['shop', 'records', 'photos', 'reviews'])->where('shop_id', $shop->id)->get();
                }

                return response()->json(['message'=>'successful', 'products' => $products]);
            }
        }   
        catch(\Exception $err){
            return response()->json(['message'=> $err->getMessage()]);
        }
    }

    public function returnproducts(Request $request){
        try{

            $id = $request->id;

            $shop = Shop::where('user_id',$id)->first();

            if(empty($shop)){
                return response()->json(['message'=>'shop not found']);
            }

            $products = Product::with('photos', 'reviews', 'reviews.user', 'reviews.reviewphotos', 'reviews.reviewvideos')
                               ->where('shop_id', $shop->id)
                               ->orderBy('created_at','desc')->get();


            return response()->json(['message'=>$products]);
        }
        catch(\Exception $err){
            return response()->json(['message'=> $err->getMessage()]);
        }
    }

    public function editProduct(Request $request){
        try{
            
            $product_id = $request->id;
            $product_info = json_decode($request->product_info);
            Log::info('product', ['product'=>$product_info]);
            $list_id = json_decode($request->list_id);

            //delete photo who already exist
            if(count($list_id) > 0){

                
                $photos = Photo::whereIn('id', $list_id)->get();

                foreach($photos as $photo){
                    $filename = basename($photo->filename);
                    Log::info('name', ['name'=>$filename]);
                    
                    if(Storage::disk('public')->exists('product_img/' . $filename)){

                        Storage::disk('public')->delete('product_img/' . $filename);
                    }
                    $photo->delete();
                }
            }

            Log::info('images', ['images'=>$request->images]);
            
            //add new photo
            if($request->hasFile('images') && $request->file('images') !== null){
                foreach($request->file('images') as $image){
                    $photo = new Photo();

                    $filename = time() . "_" . $image->getClientOriginalName();
                    $image->storeAs('public/product_img/', $filename);
                    $path = 'storage/product_img/' . $filename;

                    $photo->filename = $path;
                    $photo->product_id = $product_id;

                    if(!$photo->save()){
                        return response()->json(['message'=>'error update']);
                    }
                }
            }

            $photos = Photo::where('product_id',$product_id)->get();

            $product = Product::where('id', $product_id)->first();

            if(empty($product)){
                return response()->json(['message'=>'product not found']);
            }

            $product->name = $product_info->name;
            $product->category = $product_info->category;
            $product->quantity = $product_info->quantity;
            $product->status = $product_info->status;
            $product->description = $product_info->description;
            $product->materials = $product_info->materials;
            $product->dimensions = $product_info->dimensions;
            $product->weight = $product_info->weight;

            if($product->save()){
                return response()->json(['message'=>'successful', 'photos' => $photos]);
            }


        }
        catch(\Exception $err){
            return response()->json(['message'=> $err->getMessage()]);
        }
    }

    public function deleteProduct(Request $request){
        try{

            $product = Product::where('id',$request->id)->first();

            $notifications = Notification::where('product_id', $product->id)->get();

            $records = Record::where('product_id', $product->id)->get();

            $messages = Message::where('mention', $product->id)->get();

            $reviews = Review::select('id')->where('product_id', $product->id)->get();
            $review_arrID = $reviews->toArray();

            if(count($review_arrID) > 0){

                $review_photos = Reviewphoto::whereIn('review_id', $review_arrID)->get();
                if(!$review_photos->isEmpty()){

                    foreach($review_photos as $photo){
                        $photo->delete();
                    }
                }

                foreach($reviews as $review){
                    $review->delete();
                }
            }

            if(!$records->isEmpty()){

                foreach($records as $record){
                    $record->delete();
                }
            }

            if(!$messages->isEmpty()){

                foreach($messages as $message){
                    $message->mention = null;

                    if(!$message->save()){
                        return response()->json(['message'=>'error']);
                    }
                }
            }

            if($notifications){
                foreach($notifications as $notify){
                    $notify->delete();
                }
            }

            if(empty($product)){

                return response()->json(['message'=>'error']);
            }

            $photos = Photo::where('product_id', $product->id)->get();

            if(!empty($photos)){

                foreach($photos as $photo){
                    $photo->delete();
                }
            }

            $product->delete();

            return response()->json(['message'=>'successful']);
        }
        catch(\Exception $err){
            return response()->json(['message'=> $err->getMessage()]);
        }
    }

    public function searchProduct(Request $request){
        try{

            $data = json_decode($request->data);
            Log::info('data',['data'=>$data]);

            $products = Product::with('photos')
                               ->where('name', 'like', "%$data->text%")
                               ->where(function ($query) use($data) {

                                    if($data->category !== ""){
                                        $query->where('category', 'like', "%$data->category%");
                                    }
                                    if($data->status !== ""){
                                        $query->where('status','like', "%$data->status%");
                                    }
                               })
                               ->get();
    ;

            if($products->isEmpty()){
                return response()->json(['message'=>null]);
            }
            
            return response()->json(['message'=>$products]);
        }
        catch(\Exception $err){
            return response()->json(['message'=> $err->getMessage()]);
        }
    }
}
