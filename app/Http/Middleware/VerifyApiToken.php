<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->header('Authorization');
        $token = substr($bearerToken, 7);
        $user = User::where('token',$token)->first();
        if($user == null){
            return response()->json(['status'=>'error','data'=>'Unauthorized token'], 401);
        }
        Auth::login($user);
        return $next($request);
    }
}
