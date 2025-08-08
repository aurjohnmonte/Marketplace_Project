<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    public function follows(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
