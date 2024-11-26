<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
use Illuminate\Support\Facades\DB;
class QuatationTerm extends Model
{
    use HasFactory;


    public  function business()
    {
        return $this->belongTo("\App\Business","business_id");

    }

    public static function create($input,$business_id)
    {
        $data              = new QuatationTerm;
        $data->business_id = $business_id;
        $data->name        = $input->name;
        $data->description = $input->description_qutation;
        $data->save();
    }
    public static function names($business_id)
    {
        $name= [];
        $terms_name = \App\Models\QuatationTerm::where("business_id",$business_id)->select("id","name")->get();
        foreach($terms_name as $value){  $name[$value->id]=$value->name; }
        return $name;
    }
    public static function update_term($id,$input)
    {
        $data              = QuatationTerm::find($id);
        $data->name        = $input->name;
        $data->description = $input->description_qutation;
        $data->update();
    }
    public static function delete_term($id)
    {
        
        $data              = QuatationTerm::find($id);
        $data->delete();
    }

}
