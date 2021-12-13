<?php

namespace App\Http\Middleware;

use App\Models\AuthToken;
use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use App\Service\DatabaseConnection;
use App\Services\DatabaseConnection as ServicesDatabaseConnection;
use App\Services\JwtAuthentication;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;

class ImageShareAbleOrNot
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
            app('App\Http\Requests\SharedPostRequest');
            $post=Post::find(request('id'));
            $email=explode('|',$post->share_with);
            // dd($email);
            if($post->visibility=='Private'){
                if(!empty(request()->bearerToken())){
                    $decoded=$this->checkToken(request()->bearerToken());
                    if(isset($decoded->data)){
                        $user_data=User::find($decoded->data->id);
                        if($user_data->id==$post->user_id){
                            return $next($request);
                        }elseif(in_array($user_data->email,$email)){
                            return $next($request);
                        }else{
                            return response()->error(['message'=>'You Are Not Authorized'], 401);
                        }
                    }else{
                        return $decoded;
                    }

                }else{
                    return response()->error(['message'=>'Please Login'], 401);
                }
            }
            elseif($post->visibility=='Hidden')
            {
                if(!empty(request()->bearerToken())){
                    $decoded=$this->checkToken(request()->bearerToken());
                    if(isset($decoded->data)){
                        $user_data=User::find($decoded->data->id);
                        if($user_data->id==$post->user_id){
                            return $next($request);
                        }else{
                            return response()->error(['message'=>'You Are Not Authorized'], 401);
                        }
                    }else{
                        return $decoded;
                    }
                }else{
                    return response()->error(['message'=>'Please Login'], 401);
                }
            }
            elseif($post->visibility=='Public')
            {
                return $next($request);
            }
            else{
                    return response()->error(['message'=>'Invalid Link'], 401);
                }

        }catch(\Exception $ex){

            return response()->error(['message'=>$ex->getMessage()], 422);
        }
    }
    function checkToken($token){
        try{
            $decoded = JwtAuthentication::varifyToken($token);
            return $decoded;
        } catch (\Exception $e) {
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

