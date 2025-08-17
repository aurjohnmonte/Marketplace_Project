<?php

namespace App\Models;

use App\Events\SellerNotifyEvent;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public function users(){
        return $this->belongsTo(User::class, 'from_id');
    }
    public function products(){
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function messages(){
        return $this->belongsTo(Message::class, "message_id");
    }
    public function reviews(){
        return $this->belongsTo(Review::class, "review_id");
    }

    public function addNotification($type, $from_id, $product_id, $user_id = null, $review_id = null, $message_id = null): string
    {
        if($from_id){

            $sender = User::where('id', $from_id)->first();
            $receiver = User::where('id', $user_id)->first();

            if(!$sender || !$receiver){
                return "no user from ID";
            }
                
            $this->from_id = $from_id;
            $this->user_id = $user_id;
            $this->review_id = $review_id;
            $this->seen = 0;
            $this->favorite = false;
            
            if($type === "message"){

                $this->text = "$sender->firstname $sender->lastname has sent a message.";
                $this->message_id = $message_id;
                $this->type = "message";
            }
            else if($type === "rate product"){

                $this->text = "$sender->firstname $sender->lastname has rate your product. Check your product to see the review.";
                $this->product_id = $product_id;
                $this->type = "rate product";
            }
            else if($type === "rate shop"){

                $this->text = "$sender->firstname $sender->lastname has rate your shop. Check your profile";
                $this->type = "rate shop";
            }

            if($this->save()){
                
                broadcast(new SellerNotifyEvent($receiver->name));

                return 'successful';
            }

            return "no type";
        }

        return "no data";
    }
}
