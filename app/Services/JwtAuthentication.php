<?php

namespace App\Services;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class JwtAuthentication
{
    /**
     * Create Jwt Token
     *
     * @return Token
     */
    public static function createJwtToken($user_data)
    {
        unset($user_data['email_varified_token']);
        // unset($user_data['password']);
        unset($user_data['remember_token']);
        unset($user_data['created_at']);
        unset($user_data['deleted_at']);

        $payload_info= array(
        "iss" => "localhost",
        "iat" => time(),
        "nbf" => time()+2,
        "exp" => time()+(60*60),
        "aud" => "People Attach",
        "data" =>$user_data
        );
        try {

            $Auth_key = JWT::encode($payload_info,config('Jwt.KEY'));

            return array('token'=>$Auth_key);

        } catch (\Exception $e) {

        return array('error'=>$e->getMessage());

        }
    }
    /**
     * Verify Token
     *
     * @return Varified token or exception error
     */
    public static function varifyToken($token)
    {
        $decoded = JWT::decode($token, new Key(config('Jwt.KEY'), 'HS256'));
        return $decoded;
    }

}
