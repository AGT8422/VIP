<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Twilio\Jwt\JWT; 
class SupportActivate extends Model
{
    use HasFactory;
    use SoftDeletes;
    // JWT::encode($payload, "sss", 'HS256');
    public static function createEmailToken($data){
        try {
            $email = SupportActivate::where('email',$data['email'])->first();
            if(empty($email)){
                // $code                              = new SupportActivate;
                // $code->email                       = ;
                // $code->mobile                      = ;
                // $code->whatsapp                    = ;
                // $code->email_activation_code       = ;
                // $code->email_activation_token      = ;
                // $code->mobile_activation_code      = ;
                // $code->mobile_activation_token     = ;
                // $code->whatsapp_activation_code    = ;
                // $code->whatsapp_activation_token   = ;
                // $code->save();
            }else{
                // $code->email                       = ;
                // $code->mobile                      = ;
                // $code->whatsapp                    = ;
                // $code->email_activation_code       = ;
                // $code->email_activation_token      = ;
                // $code->mobile_activation_code      = ;
                // $code->mobile_activation_token     = ;
                // $code->whatsapp_activation_code    = ;
                // $code->whatsapp_activation_token   = ;
                // $code->update();
            }
        } catch (Exception $e) {

        }
    }

    public static function createMobileToken($data){
        try {
            $mobile = SupportActivate::where('mobile',$data['mobile'])->first();
            if(empty($mobile)){
                // $code                              = new SupportActivate;
                // $code->email                       = ;
                // $code->mobile                      = ;
                // $code->whatsapp                    = ;
                // $code->email_activation_code       = ;
                // $code->email_activation_token      = ;
                // $code->mobile_activation_code      = ;
                // $code->mobile_activation_token     = ;
                // $code->whatsapp_activation_code    = ;
                // $code->whatsapp_activation_token   = ;
                // $code->save();
            }else{
                // $code->email                       = ;
                // $code->mobile                      = ;
                // $code->whatsapp                    = ;
                // $code->email_activation_code       = ;
                // $code->email_activation_token      = ;
                // $code->mobile_activation_code      = ;
                // $code->mobile_activation_token     = ;
                // $code->whatsapp_activation_code    = ;
                // $code->whatsapp_activation_token   = ;
                // $code->update();
            }
        } catch (Exception $e) {

        }
    }

    public static function createWhatsappToken($data){
        try { 
            $whatsapp = SupportActivate::where('whatsapp',$data['whatsapp'])->first();
            if(empty($whatsapp)){
                // $code                              = new SupportActivate;
                // $code->email                       = ;
                // $code->mobile                      = ;
                // $code->whatsapp                    = ;
                // $code->email_activation_code       = ;
                // $code->email_activation_token      = ;
                // $code->mobile_activation_code      = ;
                // $code->mobile_activation_token     = ;
                // $code->whatsapp_activation_code    = ;
                // $code->whatsapp_activation_token   = ;
                // $code->save();
            }else{
                // $code->email                       = ;
                // $code->mobile                      = ;
                // $code->whatsapp                    = ;
                // $code->email_activation_code       = ;
                // $code->email_activation_token      = ;
                // $code->mobile_activation_code      = ;
                // $code->mobile_activation_token     = ;
                // $code->whatsapp_activation_code    = ;
                // $code->whatsapp_activation_token   = ;
                // $code->update();
            }
        } catch (Exception $e) {

        }
    }




}
