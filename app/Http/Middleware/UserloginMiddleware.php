<?php

namespace App\Http\Middleware;

use App\Events\ActiveEvent;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserloginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = $request->username;
        $password = $request->password;

        $user = User::where('name','=',$username)->first();

        if($user){
            if(Hash::check($password, $user->password)){

                if($user->deact_from_admin === 1){

                    return response()->json(['message'=>'Account has been deactivate from admin.']);
                }

                $user->is_active = true;
                
                if($user->is_deactivate){
                    $user->is_deactivate = 0;
                }

                $user->save();
                
                broadcast(new ActiveEvent());

                $request->session()->put(['email' => $user->email,
                                          'user_type' => $user->role ]);

                return $next($request);
            }
            return response()->json(['message'=>'password incorrect']);
        }
        return response()->json(['message'=>'user not found']);
    }
}
