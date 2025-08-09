<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public function users(){
        return $this->belongsTo(User::class, 'from_id');
    }
    public function products(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function addNotification($type, $from_id, $to_id): string
    {
        if($from_id){

            if($type === "message"){
                
                $sender = User::where('id', $from_id)->first();

                if(!$sender){
                    return "no user from ID";
                }

                $this->from_id = $from_id;
                $this->user_id = $to_id;
                $this->text = "$sender->firstname $sender->lastname has sent a message.";
                $this->seen = 0;
                $this->favorite = false;
                $this->type = "message";

                if($this->save()){
                    return 'successful';
                }
            }

            return "no type";
        }

        return "no data";
    }
}
