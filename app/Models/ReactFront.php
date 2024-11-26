<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReactFront extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public static function createNew($data)
    {
        $ReactFront                 =  new ReactFront();
        $ReactFront->name           =  $data["name"];  
        $ReactFront->surname        =  $data["surname"];  
        $ReactFront->username	   =  $data["username"];	  
        $ReactFront->password       =  Hash::make($data["password"]);  
        $ReactFront->email          =  $data["email"]; 
        $ReactFront->mobile         =  $data["mobile"]; 
        $ReactFront->api_url        =  $data["api_url"]; 
        $ReactFront->save();

    }
    public static function editNew($data,$id)
    {
        $ReactFront                 =  ReactFront::find($id);
        $ReactFront->name           =  $data["name"];  
        $ReactFront->surname        =  $data["surname"];  
        $ReactFront->username	   =  $data["username"];
        if($data["password"] != "" || $data["password"] != null){
            $ReactFront->password       =  Hash::make($data["password"]);  
        } 	  
        $ReactFront->email          =  $data["email"]; 
        $ReactFront->mobile         =  $data["mobile"]; 
        $ReactFront->api_url        =  $data["api_url"]; 
        $ReactFront->update();

    }
    public static function create($data)
    {
        $ReactFront                 =  new ReactFront();
        $ReactFront->name           =  $data["name"];  
        $ReactFront->surname        =  $data["surname"];  
        $ReactFront->username	   =  $data["username"];	  
        $ReactFront->password       =  Hash::make($data["password"]);  
        $ReactFront->device_id      =  $data["device_id"];  
        $ReactFront->device_ip      =  $data["device_ip"];  
        $ReactFront->email          =  $data["email"]; 
        $ReactFront->mobile         =  $data["mobile"]; 
        $ReactFront->api_url        =  $data["api_url"]; 
        $ReactFront->last_login     =  $data["last_login"]; 
        $ReactFront->save();

    }

    public static function edit($id,$data)
    {
       $ReactFront                  =  ReactFront::find($id);
        $ReactFront->name           =  $data["name"];  
        $ReactFront->surname        =  $data["surname"];  
        $ReactFront->username	   =  $data["username"];	  
        $ReactFront->password       =  Hash::make($data["password"]);  
        $ReactFront->device_id      =  $data["device_id"];  
        $ReactFront->device_ip      =  $data["device_ip"];  
        $ReactFront->email          =  $data["email"]; 
        $ReactFront->mobile         =  $data["mobile"]; 
        $ReactFront->api_url        =  $data["api_url"]; 
        $ReactFront->last_login     =  $data["last_login"]; 
        $ReactFront->save();

    }
    public static function replicateTable($id,$data)
    {
        $ReactFront                 =  ReactFront::find($id);
        $newReactFront              =  $ReactFront->replicate();
        $newReactFront->device_id   =  $data["device_id"];  
        $newReactFront->device_ip   =  $data["device_ip"];
        $newReactFront->last_login  =  $data["last_login"]; 
        $newReactFront->save();

    }
    
    public static function remove($id)
    {
        $ReactFront                 =  ReactFront::find($id);
        $ReactFront->delete();
    }


    public static function allDeviceName()
    {
        $array = [];
        $ReactFront  = ReactFront::get();
        foreach($ReactFront as $key => $value){$array[$value->name] = $value->name;}
        return $array;
        
    }
    public static function allUsername()
    {
        $array = [];
        $ReactFront  = ReactFront::get();
        foreach($ReactFront as $key => $value){$array[$value->username] = $value->username;}
        return $array;
        
    }

    public static function allIdUsername()
    {
        $array          = [];
        $ReactFront   = ReactFront::get();
        foreach($ReactFront as $key => $value){$array[$ReactFront->id] = $value->username . " | " . $value->device_id;}
         return $array;
        
    }
}
