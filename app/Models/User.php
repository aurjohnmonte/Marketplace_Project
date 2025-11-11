<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isEmpty;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function searchUserShop($search_info, $search_text){

        try{
            $result = [];

            if($search_info->name === "Shop"){
                $result = self::join('shops','shops.user_id','=','users.id')
                              ->where('shops.name','like',"%$search_text%")
                              ->where('users.is_deactivate', 0);

                if($search_info->filter === "Popular"){
                    $result = $result->orderBy("shops.overall_rate", 'desc')->limit(10)->get()->toArray();
                }
                else{
                    $result = $result->orderBy("shops.created_at", 'desc')->limit(10)->get()->toArray();
                }      
            
                $dummy = [];
                foreach($result as $shop){
                    
                    //push reviews array
                    $shop['reviews'] = Review::where('shop_id', $shop['id'])
                                            ->where('review_type', 'shop')
                                            ->get();
                    //push followers
                    $shop['followers'] = Follower::where('user_id', $shop['user_id'])->get();
                    Log::info('shop', ['shop' => $shop]);
                    
                    if($search_info->category !== "Any" && $search_info->category !== ""){
                        $category = json_decode($shop['category']);
                        Log::info('category', ['category'=>$category]);
                        if(in_array($search_info->category, $category)){
                            array_push($dummy, $shop);
                        }
                    }
                    else{
                        array_push($dummy, $shop);
                    }
                }

                $result = $dummy;
                Log::info('dummy',['dummy'=>$dummy]);
            }

            return $result;
        }
        catch(\Exception $err){
            return $err;
        }
    }

    public static function returnShopinfo($id){
        try{
            $shop = self::with('shop')->where('id','=',$id)->first();
            
            if(empty($shop)){
                return [];
            }

            return $shop->toArray();
        }
        catch(\Exception $err){
            return $err;
        }
    }

    public static function returnProfileInfo($email){
        try{
            $user = self::with('shop')->where('email','=',$email)->first();

            if(empty($user)){
                return [];
            }

            return $user->toArray();
        }
        catch(\Exception $err){
            return $err;
        }
    }

    public function followers(){
        return $this->hasmany(Follower::class, 'user_id');
    }

    public function shop(){
        return $this->hasOne(Shop::class, 'user_id');
    }

    public function reviews(){
        return $this->hasMany(Review::class, 'from_id')->latest('created_at');
    }
}
