<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['parent_name'];
    public  static function childs($id)
    {
        $allData =  Warehouse::where('parent_id',"!=",null)->where('business_id',$id)->get();
        $arr =  [];
        foreach ($allData as $data) {
            $arr[$data->id] =  $data->name;
        }
        return $arr;
    }
    public  static function product_stores($id,$business_id=null)
    {
        $business_id = ($business_id!=null)?$business_id:request()->session()->get('user.business_id'); 
        $allData =  Warehouse::where('parent_id',"!=",null)->where('business_id',$business_id)->whereHas('infos',function($query) use($id){
            $query->where('product_id',$id);
            $query->where('product_qty','>',0);
        })->get();
        $arr =  [];
        foreach ($allData as $data) {
            $qty            =  WarehouseInfo::where('store_id',$data->id)->where('product_id',$id)->sum('product_qty');
            $arr[$data->id] =  ['name'=>$data->name,'available_qty'=>$qty];
        }
        return $arr;
    }
    public  static function product_stores_return($id,$trans_id)
    {

        $business_id = request()->session()->get('user.business_id'); 
        $qty         = \App\TransactionSellLine::where('transaction_id',$trans_id)->sum("quantity_returned");
        $allData     = Warehouse::where('parent_id',"!=",null)->where('business_id',$business_id)->get();
        $arr         =  [];
        foreach ($allData as $data) {
            $arr[$data->id] =  ['name'=>$data->name,'available_qty'=>$qty];
        }
        return $arr;
    }
    
    public static function parents($id)
    {
        $allData  = Warehouse::whereNull('parent_id')->where('business_id',$id)->get();
        $arr      =  [];
        foreach ($allData as $data) {
            $arr[$data->id] = $data->name;
        }
        return $arr;
    }
    public function parent()
    {
      return $this->belongsTo('App\Models\Warehouse','parent_id');
    }
    public function getParentNameAttribute(Type $var = null)
    {
        return ($this->parent)?$this->parent->name:' ';
    }
    public function sub_stores()
    {
        return $this->hasMany('\App\Models\Warehouse','parent_id');
    }
    public static function all_stores($id)
    {
        $allData  = Warehouse::where('business_id',$id)->get();
        $arr      =  [];
        foreach ($allData as $data) {
            $arr[$data->id] = $data->name;
        }
        return $arr;
    }
    public function infos()
    {
        return $this->hasMany('\App\Models\WarehouseInfo','store_id');
    }
   
}
