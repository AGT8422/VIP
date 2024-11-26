<?php

namespace App\Http\Controllers\ApiController\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrivacyConditionController extends Controller
{
    // *1* index
    public function PrivacyCondition(Request $request){
        $type    = request()->input("type");
        $privacy = \App\Models\Ecommerce\PrivacyCondition::first();
        
        if($type == "term"){
            $text  = ($privacy)?$privacy->terms:"";
            $image = ($privacy)?$privacy->image_url:"";
        }elseif($type == "return"){
            $text  = ($privacy)?$privacy->return_policy:"";
            $image = ($privacy)?$privacy->img_url:"";
            
        }elseif($type == "privacy"){
            $text  = ($privacy)?$privacy->privacy:"";
            $image = ($privacy)?$privacy->icon_url:"";
             
        }else{
            $text  = "";
            $image = "";
             
        }
        return response([
                "status"  => 200,
                "value"   => ["text" =>$text ,"image" =>$image],
                "message" => "Access Successfully"
        ],200);
    }
    // *2* create
    public function CreatePrivacyCondition(Request $request){
        
    }
    // *3* edit
    public function EditPrivacyCondition(Request $request,$id){
        
    }
    // *4* store
    public function StorePrivacyCondition(Request $request){
        
    }
    // *5* update
    public function UpdatePrivacyCondition(Request $request,$id){
        
    }
    // *6* delete
    public function DeletePrivacyCondition(Request $request,$id){
        
    }
    // *6* sheet
    public function SEOSheet(Request $request){
        $type         = request()->input("type");
        $data["type"] = $type;
        $seo          = \App\Models\Ecommerce\PrivacyCondition::SEOSheet($data);
        return response([
                "status"  => 200,
                "value"   => ["title" =>$seo->original["title"] ,"description" =>$seo->original["description"]],
                "message" => "Access Successfully"
        ],200);
    }
}
