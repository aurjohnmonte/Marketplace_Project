<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $primaryKey = 'id';
    
    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

    public function buyers(){

        return $this->belongsTo(User::class, 'user_id');
    }

}
