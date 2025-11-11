<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    public function photos(){

        return $this->hasMany(Photo::class);
    }

    public function records(){

        return $this->hasMany(Record::class, 'product_id');
    }

    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    public function reviews(){
        return $this->hasMany(Review::class, 'product_id')->latest('created_at');
    }

    public function videos(){
        return $this->hasMany(Pvideo::class, 'product_id')->latest('created_at');
    }

    public static function returnProducts($filter): Collection
    {
        try {
            if ($filter === 'popular') {
                return self::with(['photos', 'shop', 'shop.user', 'reviews', 'records'])
                        ->whereHas('shop.user', function ($q){

                            $q->where('is_deactivate', 0);
                        })
                        ->orderBy('overall_rate', 'desc')
                        ->limit(10)
                        ->get();
            } elseif ($filter === 'new') {
                return self::with(['photos', 'shop', 'shop.user', 'reviews', 'records'])
                        ->where('created_at', '>=', now()->subDays(30))
                        ->whereHas('shop.user', function ($q){

                            $q->where('is_deactivate', 0);
                        })
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
            } else {
                return self::with(['photos', 'shop'])->get();
            }
        } catch (\Exception $ex) {
            Log::info('error: ', ['error' => $ex->getMessage()]);
            return collect(); // return an empty collection on error
        }
    }

}
