<?php

namespace App\Models\FrontEnd\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Draft extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END DRAFT *** // 
    // **1** ALL DRAFT
    public static function getDraft($user) {
        try{
            $list        = [];
            $business_id = $user->business_id;
            $draft       = \App\Transaction::where("business_id",$business_id)->get();
            if(count($draft)==0) { return false; }
            foreach($draft as $i){ $list[] = $i; }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE DRAFT
    public static function createDraft($user,$data) {
        try{
            $business_id             = $user->business_id;
            $list["type"]            = ["value" => $data["type"]];
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT DRAFT
    public static function editDraft($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $draft                   = \App\Transaction::find($id);
            $list["draft"]           = $draft;
            if(!$draft){ return false; }
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE DRAFT
    public static function storeDraft($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Transaction::where("contact_id",$data["contact_id"])->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Draft::createNewDraft($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE DRAFT
    public static function updateDraft($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $data["name"]        = implode(" ", [$data["first_name"]]);
            $data["business_id"] = $business_id;
            $data["created_by"]  = $user->id;
            if(!empty($data["contact_id"]) && $data["contact_id"] != ""){
                $old             = \App\Transaction::where("contact_id",$data["contact_id"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old){return false;}
            }
            $output              = Draft::updateOldDraft($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE DRAFT
    public static function deleteDraft($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $draft       = \App\Transaction::find($id);
            if(!$draft){ return false; }
            $draft->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE DRAFT
    public static function createNewDraft($user,$data) {
       try{
            $business_id   = $user->business_id;
            $draft         = new \App\Transaction();
            $draft->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE DRAFT
    public static function updateOldDraft($user,$data,$id) {
       try{
            $business_id   = $user->business_id;
            $draft         = \App\Transaction::find($id);
            $draft->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }


}
