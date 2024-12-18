<?php

namespace App\Models\FrontEnd\Cheques;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\ProductUtil;
use App\Models\FrontEnd\Utils\GlobalUtil;
class ContactBank extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END CONTACT BANK *** // 
    // **1** ALL CONTACT BANK
    public static function getContactBank($user) {
        try{
            $business_id   = $user->business_id;
            $data          = ContactBank::allData("all",null,$business_id);
            if($data == false){ return false;}
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE CONTACT BANK
    public static function createContactBank($user,$data) {
        try{
            $business_id        = $user->business_id;
            $create             = ContactBank::requirement($user);
            return $create;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT CONTACT BANK
    public static function editContactBank($user,$data,$id) {
        try{
            $business_id          = $user->business_id;
            $data                 = ContactBank::allData(null,$id,$business_id);
            $edit                 = ContactBank::requirement($user);
            if($data  == false){ return false; }
            $list["info"]            = $data;
            $list["require"]         = $edit;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE CONTACT BANK
    public static function storeContactBank($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = ContactBank::createNewContactBank($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE CONTACT BANK
    public static function updateContactBank($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = ContactBank::updateOldContactBank($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE CONTACT BANK
    public static function deleteContactBank($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id     = $user->business_id;
            $contactBank     = \App\Models\ContactBank::find($id);
            if(!$contactBank){ return "no"; }
            $check           = \App\Models\Check::where("contact_bank_id",$id)->first();
            if(!empty($check)){
                return "false";
            }
            $contactBank->delete();
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE CONTACT BANK
    public static function createNewContactBank($user,$data) {
        try{
            $business_id          = $user->business_id;
            $contact              = new \App\Models\ContactBank();
            $contact->business_id = $business_id;
            $contact->location_id = isset($data["location_id"])?$data["location_id"]:1;
            $contact->name        = isset($data["name"])?$data["name"]:"Wrong entry";
            $contact->contact_id  = isset($data["contact_id"])?$data["contact_id"]:null;
            $contact->save();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE CONTACT BANK
    public static function updateOldContactBank($user,$data,$id) {
        try{
            $business_id              = $user->business_id;
            $contactBank              = \App\Models\ContactBank::find($id);
            $contactBank->location_id = isset($data["location_id"])?$data["location_id"]:1;
            $contactBank->name        = isset($data["name"])?$data["name"]:"Wrong entry";
            $contactBank->contact_id  = isset($data["contact_id"])?$data["contact_id"]:null;
            $contactBank->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET  CONTACT BANK
    public static function allData($type=null,$id=null,$business_id) {
        try{
            $list     = [];
            if($type != null){
                $contact     = \App\Models\ContactBank::where("business_id",$business_id)->get();
                if(count($contact) == 0 ){ return false; }
                foreach($contact as $ie){
                    $list[] = [
                        "id"                  => $ie->id,
                        "name"                => $ie->name,
                        "location_id"         => $ie->location_id,
                        "business_id"         => $ie->business_id,
                        "date"                => $ie->created_at->format("Y-m-d"),
                    ];
                }
            }else{
                $contact  = \App\Models\ContactBank::find($id);
                if(empty($contact)){ return false; }
                $list[] = [
                    "id"                  => $contact->id,
                    "name"                => $contact->name,
                    "location_id"         => $contact->location_id,
                    "business_id"         => $contact->business_id,
                    "date"                => $contact->created_at->format("Y-m-d"),
               ];
                
            }
            return $list; 
        }catch(Exception    $e){
            return false;
        }
    }
    // **4** GET  REQUIREMENT
    public static function requirement($user) {
        try{   
            $allData               = []; $list               = []; 
            $locations             = \App\BusinessLocation::where("business_id",$user->business_id)->get();
            foreach($locations as $i){
                $list[$i->id] = $i->name;
            }
            $allData["locations"]  = GlobalUtil::arrayToObject($list);
            return $allData; 
        }catch(Exception $e){
           return false; 
        }
    }
    
}
