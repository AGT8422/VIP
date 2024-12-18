<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;



class MobileApp extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public static function createNew($data)
    {
        $MobileApp                 =  new MobileApp();
        $MobileApp->name           =  $data["name"];  
        $MobileApp->surname        =  $data["surname"];  
        $MobileApp->username	   =  $data["username"];	  
        $MobileApp->password       =  Hash::make($data["password"]);  
        $MobileApp->email          =  $data["email"]; 
        $MobileApp->mobile         =  $data["mobile"]; 
        $MobileApp->api_url        =  $data["api_url"]; 
        $MobileApp->save();

    }
    public static function editNew($data,$id)
    {
        $MobileApp                 =  MobileApp::find($id);
        $MobileApp->name           =  $data["name"];  
        $MobileApp->surname        =  $data["surname"];  
        $MobileApp->username	   =  $data["username"];
        if($data["password"] != "" || $data["password"] != null){
            $MobileApp->password       =  Hash::make($data["password"]);  
        } 	  
        $MobileApp->email          =  $data["email"]; 
        $MobileApp->mobile         =  $data["mobile"]; 
        $MobileApp->api_url        =  $data["api_url"]; 
        $MobileApp->update();

    }
    public static function create($data)
    {
        $MobileApp                 =  new MobileApp();
        $MobileApp->name           =  $data["name"];  
        $MobileApp->surname        =  $data["surname"];  
        $MobileApp->username	   =  $data["username"];	  
        $MobileApp->password       =  Hash::make($data["password"]);  
        $MobileApp->device_id      =  $data["device_id"];  
        $MobileApp->device_ip      =  $data["device_ip"];  
        $MobileApp->email          =  $data["email"]; 
        $MobileApp->mobile         =  $data["mobile"]; 
        $MobileApp->api_url        =  $data["api_url"]; 
        $MobileApp->last_login     =  $data["last_login"]; 
        $MobileApp->save();

    }

    public static function edit($id,$data)
    {
       $MobileApp                  =  MobileApp::find($id);
        $MobileApp->name           =  $data["name"];  
        $MobileApp->surname        =  $data["surname"];  
        $MobileApp->username	   =  $data["username"];	  
        $MobileApp->password       =  Hash::make($data["password"]);  
        $MobileApp->device_id      =  $data["device_id"];  
        $MobileApp->device_ip      =  $data["device_ip"];  
        $MobileApp->email          =  $data["email"]; 
        $MobileApp->mobile         =  $data["mobile"]; 
        $MobileApp->api_url        =  $data["api_url"]; 
        $MobileApp->last_login     =  $data["last_login"]; 
        $MobileApp->save();

    }
    public static function replicateTable($id,$data)
    {
        $MobileApp                 =  MobileApp::find($id);
        $newMobileApp              =  $MobileApp->replicate();
        $newMobileApp->device_id   =  $data["device_id"];  
        $newMobileApp->device_ip   =  $data["device_ip"];
        $newMobileApp->last_login  =  $data["last_login"]; 
        $newMobileApp->save();

    }
    
    public static function remove($id)
    {
        $MobileApp                 =  MobileApp::find($id);
        $MobileApp->delete();
    }


    public static function allDeviceName()
    {
        $array = [];
        $MobileApp  = MobileApp::get();
        foreach($MobileApp as $key => $value){$array[$value->name] = $value->name;}
        return $array;
        
    }
    public static function allUsername()
    {
        $array = [];
        $MobileApp  = MobileApp::get();
        foreach($MobileApp as $key => $value){$array[$value->username] = $value->username;}
        return $array;
        
    }

    public static function allIdUsername()
    {
        $array          = [];
        $MobileApp   = MobileApp::get();
        foreach($MobileApp as $key => $value){$array[$MobileApp->id] = $value->username . " | " . $value->device_id;}
         return $array;
        
    }

    

}
