<?php

namespace App\Http\Controllers\ApiController\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ExceptionOccured;
use Illuminate\Support\Facades\Mail;

class StoreFeatureController extends Controller
{
    // *1** get store feature 
    public function getStoreFeature(Request $request) {
        try{
            $main_token    = $request->header("Authorization");
            $token         = substr($main_token,7);
            $data["token"] = $token;
            // $MSG           = "HI 8422"; 
            // Mail::send(new ExceptionOccured($MSG)); 
            $check         = \App\Models\e_commerceClient::getStoreFeature($data);
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403,
                "message" => __('Failed Access'),
            ]);
        }
    }
    // *2** get  one store feature 
    public function getStoreFeatureOne(Request $request,$id) {
        try{
            $main_token    = $request->header("Authorization");
            $token         = substr($main_token,7);
            $data["token"] = $token;
            // $MSG           = "HI 8422"; 
            // Mail::send(new ExceptionOccured($MSG)); 
            $check         = \App\Models\e_commerceClient::getStoreFeatureOne($data,$id);
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403,
                "message" => __('Failed Access'),
            ]);
        }
    }
    
    // *3** save store feature 
    public function addStoreFeature(Request $request) {
        try{
            $main_token    = $request->header("Authorization");
            $token         = substr($main_token,7);
            $data          = $request->only(["title","image","description","business_id"]);
            $data["token"] = $token;
            $check         = \App\Models\e_commerceClient::addStoreFeature($data,$request);
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403,
                "message" => __('Failed Added'),
            ]);
        }        
    }

    // *4** update store feature 
    public function updateStoreFeature(Request $request,$id) {
        try{
            $main_token    = $request->header("Authorization");
            $token         = substr($main_token,7);
            $data          = $request->only(["title","image","description","business_id"]);
            $data["token"] = $token;
            $data["id"]    = $id; 
            $check         = \App\Models\e_commerceClient::updateStoreFeature($data,$request);
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403,
                "message" => __('Failed Updated'),
            ]);
        }
        
    }

    // *5** delete store feature 
    public function deleteStoreFeature(Request $request,$id) {
        try{
            $main_token    = $request->header("Authorization");
            $token         = substr($main_token,7);
            $data["token"] = $token;
            $data["id"]    = $id; 
            $check         = \App\Models\e_commerceClient::deleteStoreFeature($data);
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403,
                "message" => __('Failed Deleted'),
            ]);
        }
        
    }
    // *6** get business_id
    public function getBusiness(Request $request) {
        try{
            $list     = [];
            $business = \App\Business::get();
            foreach($business as $i){
                $list[]     = [
                    "id"    => $i->id ,
                    "value" => $i->name ,
                ];
            }
            return response([
                "status"  => 200,
                "list"    => $list,
                "message" => "Access Successfully" ,
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action " ,
            ],403);
        }
    }
}
