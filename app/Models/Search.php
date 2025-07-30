<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    public function returnShopSearch($search_info, $search_text){
        try{

            $user = new User();
        }
        catch(\Exception $err){
            return $err;
        }
    }
}
