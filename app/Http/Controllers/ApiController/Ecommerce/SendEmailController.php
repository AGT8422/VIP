<?php

namespace App\Http\Controllers\ApiController\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\VerfyEmail;
use Illuminate\Support\Facades\Mail;

class SendEmailController extends Controller
{
    //
    public function saveEmail(Request $request) {
            try{
                $data = [
                    "title"    => "for test",
                    "message" => "hi friend",
                ];
                Mail::to("albaseetcompany8422@gmail.com")->send(new VerfyEmail($data));
                return response([
                    "status"   => 200 ,
                    "message" => __("action successfully") ,
                ]);
            }catch(Exception $e){
                return response([
                    "status"   => 403 ,
                    "message" => __("action failed") ,
                ],403);
                
            }
    }   
}
