<?php

namespace App\Models\FrontEnd\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END VARIATION *** // 
    // **1** ALL VARIATION
    public static function getVariation($user) {
        try{
            $list               = [];
            $business_id        = $user->business_id;
            $variation          =  Variation::allData("all",null,$business_id); 
            if($variation == false){ return false;}
            return $variation;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE VARIATION
    public static function createVariation($user,$data) {
        try{
            $business_id        = $user->business_id;
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT VARIATION
    public static function editVariation($user,$data,$id) {
        try{
            $business_id           = $user->business_id;
            $variation             = Variation::allData(null,$id,$business_id);
            if(!$variation){ return false; }
            return $variation;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE VARIATION
    public static function storeVariation($user,$data) {
        try{
            \DB::beginTransaction();
            $data["business_id"] =  $user->business_id;
            $data["name"]        =  trim($data["name"]);
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\VariationTemplate::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Variation::createNewVariation($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE VARIATION
    public static function updateVariation($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $data["business_id"] =  $user->business_id;
            $data["name"]        =  trim($data["name"]);
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\VariationTemplate::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return "old";}
            }
            $output              = Variation::updateOldVariation($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE VARIATION
    public static function deleteVariation($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id      = $user->business_id;
            $variation        = \App\VariationTemplate::find($id);
            if(!$variation){ return false; }
            $variationItem    = \App\VariationValueTemplate::where("variation_template_id",$id)->get();
            foreach($variationItem as $e){
                $e->delete();
            }
            $variation->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **7** DELETE ROW VARIATION
    public static function deleteRowVariation($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id      = $user->business_id;
            $variationItem    = \App\VariationValueTemplate::find($id);
            if(!$variationItem){ return false; }
            $variationItem->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    
    // ****** MAIN FUNCTIONS 
    // **1** CREATE VARIATION
    public static function createNewVariation($user,$data) {
        try{
            $business_id             = $user->business_id;
            $variation               = new \App\VariationTemplate();
            $variation->business_id  = $business_id;
            $variation->name         = $data["name"];
            $variation->save();
            if(isset($data["items"]) && $data["items"] != "" && $data["items"] != null){
                foreach($data["items"] as $i){
                    $variationItem                         = new \App\VariationValueTemplate();
                    $variationItem->name                   = $i;
                    $variationItem->variation_template_id  = $variation->id;
                    $variationItem->save();
                }
            }
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE VARIATION
    public static function updateOldVariation($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $variation               = \App\VariationTemplate::find($id);
            if(empty($variation)){return "empty";}
            $variation->name         = $data["name"];
            $id_delete               = [];
            // ..0........................................................................delete item..
                foreach($data["old_items"] as $key => $ie){
                    $id_delete[]  = $key;
                }
                $all_delete_ids = \App\VariationValueTemplate::whereNotIn("id",$id_delete)->where("variation_template_id",$id)->get();
                foreach($all_delete_ids as $de){
                    $de->delete();
                }
            // ..1...........................................................................old item..
            if(isset($data["old_items"]) && $data["old_items"] != "" && $data["old_items"] != null){
                foreach($data["old_items"] as $key => $i){
                 
                    $variationItem                         = \App\VariationValueTemplate::find($key);
                    $variationItem->name                   = $i;
                    $variationItem->variation_template_id  = $id;
                    $variationItem->update();
                }
            }
            // ..2............................................................................new item..
            if(isset($data["items"]) && $data["items"] != "" && $data["items"] != null ){
                foreach($data["items"] as $key => $i){
                    $variationItem                         = new \App\VariationValueTemplate();
                    $variationItem->name                   = $i;
                    $variationItem->variation_template_id  = $id;
                    $variationItem->save();
                }
            }
            // .....................................................................................
            $variation->update();
            return "true"; 
        }catch(Exception $e){
            return "false";
        }
    }
    
    // **3** GET  VARIATIONS
    public static function allData($type=null,$id=null,$business_id) {
        try{
            $list   = [];
            if($type != null){
                $variations     = \App\VariationTemplate::where("business_id",$business_id)->get();
                if(count($variations) == 0 ){ return false; }
                foreach($variations as $ie){
                    $lines   = [];
                    $items   = \App\VariationValueTemplate::where("variation_template_id",$ie->id)->get();
                    foreach($items as $ii){
                        $lines[] = [
                            "id"   => $ii->id,
                            "name" => $ii->name,
                        ];
                    }
                    $list[] = [
                        "id"   => $ie->id,
                        "name" => $ie->name,
                        "list" => $lines,
                    ];
                }
            }else{
                $variations  = \App\VariationTemplate::find($id);
                if(empty($variations)){ return false; }
                $lines       = [];
                $items       = \App\VariationValueTemplate::where("variation_template_id",$id)->get();
                foreach($items as $ii){
                    $lines[] = [
                        "id"   => $ii->id,
                        "name" => $ii->name,
                    ];
                }
                $list[] = [
                    "id"   => $variations->id,
                    "name" => $variations->name,
                    "list" => $lines,
                ];
                
            }
            return $list; 
        }catch(Exception $e){
            return false;
        }
    }


}
