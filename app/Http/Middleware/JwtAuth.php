<?php

namespace App\Http\Middleware;

use App\Models\AuthToken;
use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use App\Service\DatabaseConnection;
use App\Services\DatabaseConnection as ServicesDatabaseConnection;
use App\Services\JwtAuthentication;
use Illuminate\Support\Facades\DB;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            $decoded = JwtAuthentication::varifyToken($request->bearerToken());

            request()->user_data=User::find($decoded->data->id);

            $token_exist = AuthToken::where('user_id',$decoded->data->id)->first();
            if(empty($token_exist->id)){
                return response()->error(['message'=>'Already Logout'], 404);
            }else{
                return $next($request);
            }
        } catch (Exception $e) {
            if ($e instanceof \Firebase\JWT\SignatureInvalidException){
                return response()->error(['message' => 'Token is Invalid'],422);
            }else if ($e instanceof \Firebase\JWT\ExpiredException){
                return response()->error(['message' => 'Token is Expired'],422);
            }else if ($e instanceof \Firebase\JWT\BeforeValidException){
                return response()->error(['message' => 'Try Again after 10 Second'],422);
            }else{
                return response()->error(['message'=>$e->getMessage()], 500);
            }
        }
    }
}
