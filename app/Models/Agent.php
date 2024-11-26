<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;
    public static function items()
    {
        $business_id = request()->session()->get('user.business_id');
        $arr  = [];
        foreach (Agent::where('business_id',$business_id)->get() as $data) {
            $arr[$data->id] =  $data->name;
        }
        return $arr;
    }
}
