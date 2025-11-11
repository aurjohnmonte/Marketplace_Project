<?php

namespace App\Http\Middleware;

use App\Models\Adminotp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if($request->session()->has('admin_email')){
            return $next($request);
        }

        return redirect()->route('admin_login');
    }
}
