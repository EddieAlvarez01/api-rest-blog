<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Hash;

class JwtAuth{

    public $key;

    public function __construct()
    {
        $this->key = 'api-key-1997';
    }

    public function signup($email, $password, $getToken = null){
        $user = User::where('email', $email)->first();
        if($user != null && is_object($user)){
            if(Hash::check($password, $user->password)){
                $token = [
                    'sub' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'image' => $user->image,
                    'iat' => time(),
                    'exp' => time() + (7 * 24 * 60 * 60)
                ];
                $jwt = JWT::encode($token, $this->key, 'HS256');
                $decode = JWT::decode($jwt, $this->key, ['HS256']);
                if(is_null($getToken)){
                    $data = ['data' => $jwt];
                }else{
                    $data = ['data' => $decode];
                }
                return $data;
            }
        }
        return null;
    }

    //VERIFICAR TOKEN, PARAMETRO IDENTITY = TRUE, DEVUELVE LA INFORMACIÓN DEL USUSARIO EN SESION
    public function checkToken($token, $getIdentity = false){
        try {
            $decode = JWT::decode($token, $this->key, ['HS256']);
            if($getIdentity){
                return $decode;
            }
            return true;
        }catch (\Exception $e){
            //print_r('error');
        }
        return false;
    }

}
