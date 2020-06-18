<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

use App\User;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */


    public function handle($request, Closure $next)
    {
       try {

            $token = $request->get('token');

             if(!empty($token)) {
                $userInstance = User::where('token', $token)->get();
                
                if($userInstance->count() > 0) {
                    $users = $userInstance->first();

                    Auth::login($users);

                } else {
                    return response()->json(['error'=>'Token is Invalid','status' => false],401);
                }

            } else {
                return response()->json(['error'=>'Token is missing' , 'status' => false],404);
            }

        } catch (Exception $e) {
            return response()->json(['error'=>'Something is wrong','status' => false],403);
            
        }
        return $next($request);

    }
}
