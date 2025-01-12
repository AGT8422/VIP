<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SystemAccount;

class Pattern extends Model
{
    use HasFactory;

    // ** main functions
    // *****************
    public static function create($data,$business_id,$user_id)
    {
        $pattern                 =   new Pattern();
        $pattern->code           =  $data["code"];  
        $pattern->business_id    =  $business_id;  
        $pattern->location_id    =  $data["location_id"];  
        $pattern->name           =  $data["name"];  
        $pattern->invoice_scheme =  $data["invoice_scheme"];  
        $pattern->invoice_layout =  $data["invoice_layout"];  
        $pattern->type           =  $data["pattern_type"]; 
        $pattern->pos            =  $data["pos"]; 
        $pattern->printer_type   =  $data["printer_type"]; 
        $pattern->user_id        =  $user_id; 
        $pattern->save();

    }

    public static function edit($id,$data,$business_id,$user_id)
    {
      
        $pattern                 =  Pattern::find($id);
        $pattern->code           =  $data["code"];  
        $pattern->business_id    =  $business_id;  
        $pattern->location_id    =  $data["location_id"];  
        $pattern->name           =  $data["name"];  
        $pattern->invoice_scheme =  $data["invoice_scheme"];  
        $pattern->invoice_layout =  $data["invoice_layout"];  
        $pattern->pos            =  $data["pos"]; 
        $pattern->type           =  $data["pattern_type"]; 
        $pattern->printer_type   =  $data["printer_type"]; 
        $pattern->user_id        =  $user_id; 
        $pattern->update();

    }
    
    public static function remove($id)
    {
        $pattern                 =  Pattern::find($id);
        $pattern->delete();
    }

    public static function allname($business_id)
    {
        $array = [];
        $pattern_name  = Pattern::where("business_id",$business_id)->get();
        foreach($pattern_name as $key => $value){$array[$value->name] = $value->name;}
        return $array;
        
    }
    public static function allname_id($business_id)
    {
        $array = [];
        $pattern_name  = Pattern::where("business_id",$business_id)->get();
        foreach($pattern_name as $key => $value){$array[$value->id] = $value->name;}
        return $array;
        
    }

    public static function allname_id_account($business_id)
    {
        $array          = [];
        $array_exist    = [];
        $pattern_name   = Pattern::where("business_id",$business_id)->get();
        $pattern_exist  = SystemAccount::where("business_id",$business_id)->get();
        foreach($pattern_exist as $key => $value){$array_exist[] = $value->pattern_id;}
        foreach($pattern_name as $key => $value){if(!in_array($value->id,$array_exist)){$array[$value->id] = $value->name;}}
        return $array;
        
    }

    public static function DefaultPattern($id) {
        $patterns            = \App\Models\Pattern::where("id","!=",$id)
                                                    ->update([
                                                        "default_p" => 0
                                                    ]);
        $pattern             = \App\Models\Pattern::find($id);
        $pattern->default_p  = 1 ;
        $pattern->update();

    }

    // ** relation function  
    // ********************
    public function location()
    {
       return $this->belongsTo(\App\BusinessLocation::class,"location_id");
    }
    public function scheme()
    {
       return $this->belongsTo(\App\InvoiceScheme::class,"invoice_scheme");
    }
    public function layout()
    {
       return $this->belongsTo(\App\InvoiceLayout::class,"invoice_layout");
    }
    public function printer()
    {
       return $this->belongsTo(\App\Models\PrinterTemplate::class,"printer_type");
    }
    public function user()
    {
       return $this->belongsTo(\App\User::class,"user_id");
    }
    public static function forDropdown() {
        $patterns      = \App\Models\Pattern::get();
        $patterns_list = [] ;
        foreach($patterns as $i){
            $patterns_list[$i->id]= $i->name;
        }
        return $patterns_list;
        
    }



}
