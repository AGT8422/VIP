<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Eadmin extends Model
{
    use HasFactory,SoftDeletes;
    
    // 1***** login
    public static function login($data)
    {
        try{
            $check = Eadmin::where("username",$data["username"])->first();
            if(empty($check))   { return false; }
            $check_password     = Hash::check($data['password'],$check->password);
            if($check_password) {
                $username     = trim($data["username"]);
                $payload = [
                    'username'  => $username,
                    'exp'       => \Carbon::now()->addDay(),
                ];
                $random    = Str::random(40);
                $secretKey = 'izo-'.$random.$username;
                $headers   = [
                    'alg' => 'HS256', // HMAC SHA-256 algorithm (default)
                    'typ' => 'JWT',
                ];
                $token = JWT::encode($payload, $secretKey, 'HS256', null, $headers);
                $check->api_token = $token;
                $check->update();
                return $out  = ["status"=>true,"token"=>$token]; 
            }else{ 
                return $out  = ["status"=>false,"token"=>""]; 
            }
        }catch(Exception $e){
            return false;
        }
    }
    // 2***** logout           -/
    public static function logout($data){
        if(count($data)>0){
            $EAdmin       =  Eadmin::where("api_token",$data['token'])->first(); 
            if(!$EAdmin){
                return response([
                    "status"   => 401 ,
                    "messages" => __('Token Expire') ,
                ],401);
            }
            $EAdmin->api_token = null;
            $EAdmin->update();
            return response([
                "status"  => 200,
                "message" => "Logout Successfully",
            ]);
        }else{
            return response([
                "status"  => 403,
                "message" => "Failed Logout",
            ],403);
        }
    }
    
}
