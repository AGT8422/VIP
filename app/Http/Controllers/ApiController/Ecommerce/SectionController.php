<?php

namespace App\Http\Controllers\ApiController\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    // *** get ALL SECTIONS IN E-Commerce 
    // *** 1 GET ALL 
    public function getAllSection(Request $request) {
        try{
            $main_token    = $request->header("Authorization");
            $token         = substr($main_token,7);
            $data["token"] = $token;
            // $MSG           = "HI 8422"; 
            // Mail::send(new ExceptionOccured($MSG)); 
            $check         = \App\Models\Ecommerce\Section::getAllSections($data);
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403,
                "message" => __('Failed Access'),
            ],403);
        }
    }
    // *** 2 POST EDIT  
    public function editAllSection(Request $request) {
        try{
            $main_token    = $request->header("Authorization");
            $token         = substr($main_token,7);
            $data          = $request->only(["sections"]);
            $data["token"] = $token;
            // $MSG           = "HI 8422"; 
            // Mail::send(new ExceptionOccured($MSG)); 
            $check         = \App\Models\Ecommerce\Section::editAllSections($data);
            return $check;
        }catch(Exception $e){
            return response([
                "status"   => 403,
                "message" => __('Failed Access'),
            ],403);
        }
    }
}
