<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Service\DatabaseConnection;
use App\Services\DatabaseConnection as ServicesDatabaseConnection;
use Closure;
use Illuminate\Http\Request;

class EmailVarified
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
        if(!empty(request('password'))){
            app('App\Http\Requests\LoginRequest');
        }else{
            app('App\Http\Requests\ForgotPasswordRequest');
        }

        try{
            $user_data=User::where('email',request('email'))->first();
            if(!empty($user_data)){
                if ($user_data->email_verified_at===null) {
                    $data['error']="You didn't confirm your email yet!!";
                    $data['message']="Someting went Worng";
                    return response()->error($data,404);
                }else{
                    $request->merge(['user_data'=>$user_data]);
                    return $next($request);
                }
            }else{
                $data['message']="Email not Register";
                return response()->error($data,404);
            }
        }catch(\Exception $ex){

            // $data['error']=;
            $data['message']="Something went worng";
            return response()->error($data,404);
        }
    }

}
