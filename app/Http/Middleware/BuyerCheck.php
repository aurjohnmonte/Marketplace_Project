<?php

namespace App\Http\Middleware;

use App\Models\Otp;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BuyerCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(session()->has('email') && session("user_type") === "buyer"){
            $email = session('email');

            Log::info('email', ['email'=>$email]);
            $user = User::where('email', $email)->first();

            Log::info('id', ['id'=>$user->id]);

            Otp::where('from_user', $user->id)->delete();

            return $next($request);
        }
        return redirect()->route('home');
    }
}
