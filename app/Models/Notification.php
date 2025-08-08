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
}
