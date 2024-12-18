<?php

namespace App\Http\Controllers\ApiController\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebBarController extends Controller
{
    // ** 1 ** get floating 
    public function getListFloatingBar(Request $request)
    {
        try{
            $data   = $request->only(["token"]);
            $check  = \App\Models\e_commerceClient::getListFloatingBar($data); 
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403, 
                "message"  => __("Failed Action"), 
            ]);
        }  
    }

    // ** 2 ** save floating
    public function saveFloatingBar(Request $request)
    {
        try{
            $data   = $request->only(["token","title","icon"]);
            $check  = \App\Models\e_commerceClient::saveFloatingBar($data,$request); 
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403, 
                "message"  => __("Failed Action"), 
            ]);
        }  
    }
     
    // ** 3 ** update floating
    public function updateFloatingBar(Request $request ,$id)
    {
        try{
            $data       = $request->only(["token"]);
            $data["id"] = $id;
            $check      = \App\Models\e_commerceClient::updateFloatingBar($data); 
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403, 
                "message"  => __("Failed Update Action"), 
            ]);
        }  
    }

    // ** 4 ** delete  floating
    public function deleteFloatingBar(Request $request,$id)
    {
        try{
            $data       = $request->only(["token"]);
            $data["id"] = $id;
            $check      = \App\Models\e_commerceClient::deleteFloatingBar($data); 
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403, 
                "message"  => __("Failed Delete Action"), 
            ]);
        }  
    }

    // ** 5 **  get navigation
    public function getListNavigationBar(Request $request)
    {
        try{
       
            $data   = $request->only(["token"]);
            $check  = \App\Models\e_commerceClient::getListNavigationBar($data); 
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403, 
                "message"  => __("Failed Action"), 
            ]);
        }  
    }

    // ** 6 ** save navigation
    public function saveNavigationBar(Request $request)
    {
        try{
            $data   = $request->only(["token","title","icon"]);
            $check  = \App\Models\e_commerceClient::saveNavigationBar($data,$request); 
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403, 
                "message"  => __("Failed Add Action"), 
            ]);
        }  
    }

    // ** 7 ** update navigation
    public function updateNavigationBar(Request $request, $id)
    {
        try{
            $data       = $request->only(["token"]);
            $data["id"] = $id;
            $check      = \App\Models\e_commerceClient::updateNavigationBar($data); 
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403, 
                "message"  => __("Failed Update Action"), 
            ]);
        }  
    }

    // ** 8 ** delete navigation
    public function deleteNavigationBar(Request $request,$id)
    {
        try{
            $data       = $request->only(["token"]);
            $data["id"] = $id;
            $check      = \App\Models\e_commerceClient::deleteNavigationBar($data); 
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403, 
                "message"  => __("Failed Delete Action"), 
            ]);
        }  
    }
}
