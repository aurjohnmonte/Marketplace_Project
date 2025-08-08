<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products(){
        return $this->hasMany(Product::class, 'shop_id');
    }

    public function reviews(){
        return $this->hasMany(Review::class, "shop_id")->latest('created_at');
    }
}
