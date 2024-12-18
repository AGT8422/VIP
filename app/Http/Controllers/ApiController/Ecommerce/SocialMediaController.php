<?php

namespace App\Http\Controllers\ApiController\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class SocialMediaController extends Controller
{

    // **1**  SAVE LINKS
    public function saveLink(Request $request) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            $data           = $request->only(["link","icon","title"]);
            $data["token"]  = $token;
            $check          = \App\Models\e_commerceClient::saveLink($data,$request); 
            return $check;
        }catch(Exception $e){
            return response([
                    "status"  => 403,
                    "message" => __("Failed Actions"),
            ],403); 
        }
    }
    
    // **2** UPDATE LINKS
    public function updateLink(Request $request,$id) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            $data           = $request->only(["link","icon","title"]);
            $data['id']     = $id ;
            $data["token"]  = $token;
            $check          = \App\Models\e_commerceClient::updateLink($data,$request); 
            return $check;
        }catch(Exception $e){
            return response([
                    "status"  => 403,
                    "message" => __("Failed Actions"),
            ],403); 
        }
    }

    // **3** DELETE LINKS
    public function deleteLink(Request $request,$id) {
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            $data['token']  = $token ;
            $data['id']     = $id ;
            $check          = \App\Models\e_commerceClient::deleteLink($data); 
            return $check;
        }catch(Exception $e){
            return response([
                    "status"  => 403,
                    "message" => __("Failed Actions"),
            ],403); 
        }
    }
    
    // **4** GET LIST LINKS 
    public function getLinks(Request $request){
        try{
            $main_token     = $request->header('Authorization');
            $token          = substr($main_token,7);
            $data['token']  = $token ;
            $check          = \App\Models\e_commerceClient::getLink($data);
            return $check;
        }catch(Exception $e){
            return response([
                    "status"  => 403,
                    "message" => __("Failed Actions"),
            ],403); 
        }
    } 

}
