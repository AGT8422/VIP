<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactBank extends Model
{
    use HasFactory;
    public function contact(Type $var = null)
    {
        return $this->belongsTo('\App\Contact','contact_id');
    }
    public function location(Type $var = null)
    {
        return $this->belongsTo('\App\BusinessLocation','location_id');
    }
    public static function items($id=NULL)
    {
        $business_id = request()->session()->get('user.business_id');
        $allData =  ContactBank::where(function($query) use($id,$business_id){
                            if ($id > 0) {
                                $query->where('location_id',$id);
                            }else {
                                $query->where('business_id',$business_id);
                            }
                        })->get();
        $arr     = [];
        foreach ($allData as $data) {
            $arr[$data->id] =  $data->name;
        }
        return $arr;
    }
}
