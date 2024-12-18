<?php

namespace App\Models\FrontEnd\Manufacturing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END RECIPE *** // 
    // **1** ALL RECIPE
    public static function getRecipe($user) {
        try{
            $list               = [];
            $business_id        = $user->business_id;
            $recipe             = \App\Models\Recipe::where("business_id",$business_id)->get();
            if(count($recipe)==0) { return false; }
            foreach($recipe as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE RECIPE
    public static function createRecipe($user,$data) {
        try{
            $business_id        = $user->business_id;
            $list["type"]       = ["value" => $data["type"]];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT RECIPE
    public static function editRecipe($user,$data,$id) {
        try{
            $business_id          = $user->business_id;
            $recipe               = \App\Models\Recipe::find($id);
            $list["recipe"]       = $recipe;
            if(!$recipe){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE RECIPE
    public static function storeRecipe($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Models\Recipe::where("contact_id",$data["contact_id"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Recipe::createNewRecipe($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE RECIPE
    public static function updateRecipe($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Models\Recipe::where("contact_id",$data["contact_id"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Recipe::updateOldRecipe($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE RECIPE
    public static function deleteRecipe($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $recipe      = \App\Models\Recipe::find($id);
            if(!$recipe){ return false; }
            $recipe->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE RECIPE
    public static function createNewRecipe($user,$data) {
        try{
            $business_id  = $user->business_id;
            $recipe       = new \App\Models\Recipe();
            $recipe->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE RECIPE
    public static function updateOldRecipe($user,$data,$id) {
        try{
            $business_id   = $user->business_id;
            $recipe        = \App\Models\Recipe::find($id);
            $recipe->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
