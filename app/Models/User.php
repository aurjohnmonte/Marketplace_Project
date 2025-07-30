<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
                              ->get()->toArray();
            }

            return $result;
        }
        catch(\Exception $err){
            return $err;
        }
    }

    public static function returnShopinfo($id){
        try{
            $shop = Shop::where('user_id','=',$id)->first();
            
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
            $user = self::where('email','=',$email)->first();

            if(empty($user)){
                return [];
            }

            return $user->toArray();
        }
        catch(\Exception $err){
            return $err;
        }
    }
}
