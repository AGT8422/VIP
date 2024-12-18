<?php

namespace App\Models\FrontEnd\Warehouses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\ProductUtil;
use App\Models\FrontEnd\Utils\GlobalUtil;
class Warehouse extends Model
{
    use HasFactory ;
    // *** REACT FRONT-END WAREHOUSE *** // 
    // **1** ALL WAREHOUSE
    public static function getWarehouse($user) {
        try{
            $list         = [];
            $business_id  = $user->business_id;
            $warehouse    =  Warehouse::allData("all",null,$business_id); 
            if($warehouse == false){ return false;}
            return $warehouse;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE WAREHOUSE
    public static function createWarehouse($user,$data) {
        try{
            $business_id        = $user->business_id;
            $require            = Warehouse::getRequire($business_id);
            return $require;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT WAREHOUSE
    public static function editWarehouse($user,$data,$id) {
        try{
            $list                 = [];
            $business_id          = $user->business_id;
            $warehouse            = Warehouse::allData(null,$id,$business_id);
            $require              = Warehouse::getRequire($business_id);
            if(!$warehouse){ return false; }
            $list["info"]   =$warehouse;
            $list["require"]=$require;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE WAREHOUSE
    public static function storeWarehouse($user,$data) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Models\Warehouse::where("name",$data["name"])->where("business_id",$business_id)->first();
                if($old){return "old";}
            }
            $output              = Warehouse::createNewWarehouse($user,$data);
            if($output == false){ $output =  "failed"; }else{ $output = "true";} 
            \DB::commit(); 
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE WAREHOUSE
    public static function updateWarehouse($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Models\Warehouse::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$business_id)->first();
                if($old){return "old";}
            }
            if(!empty($data["parent_id"]) && $data["parent_id"] != ""){
                $checkParent     = \App\Models\Warehouse::where("parent_id",$id)->first();
                if(!empty($checkParent)){
                    return "failed";
                }
            }
            $output              = Warehouse::updateOldWarehouse($user,$data,$id);
            if($output == false){ $output =  "failed"; }else{ $output = "true";} 
            \DB::commit(); 
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE WAREHOUSE
    public static function deleteWarehouse($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id     = $user->business_id;
            $warehouse       = \App\Models\Warehouse::find($id);
            if(!$warehouse){ return "false"; }
            $check           = GlobalUtil::check("store",$id);
            if($check){ return "cannot"; }
            $warehouse->delete();
            \DB::commit();
            return "true";
        }catch(Exception $e){
            return "false";
        }
    }
    // **7** MOVEMENT WAREHOUSE
    public static function getWarehouseMovement($user,$id) {
        try{
            $business_id        = $user->business_id;
            $query             = \App\MovementWarehouse::where("product_id",$id)->orderByRaw('ISNULL(date), date asc, created_at asc')->orderBy("id","asc");
            if(request()->input("main_store") != null){
                $store_id = request()->input("main_store");
                $store_id_list = \App\Models\Warehouse::where("parent_id",$store_id)->pluck("id");
                $query->whereIn("store_id",$store_id_list);
            }
            if(request()->input("store") != null){
                $store_id = request()->input("store");
                $query->where("store_id",$store_id);
            }
            if(request()->input("date") != null){
                $end_date = request()->input("date");
                $query->whereDate("date","<=",$end_date);
            }
            $movementWarehouse = $query->get();
            if(count($movementWarehouse)==0){ return false; }
            $move = []; 
            $qty_plus   = 0; 
            $qty_minus  = 0; 
            foreach($movementWarehouse as $i){
                $qty_plus                += $i->plus_qty; 
                $qty_minus               += $i->minus_qty; 
                $moveLine                 = [];
                $moveLine["id"]           = $i->id ;
                $moveLine["product_name"] = $i->product_name ;
                $moveLine["unit_id"]      = $i->unit_id ;
                $moveLine["store_id"]     = $i->store_id ;
                $moveLine["movement"]     = $i->movement ;
                $moveLine["plus_qty"]     = $i->plus_qty ;
                $moveLine["minus_qty"]    = $i->minus_qty ;
                $moveLine["current_qty"]  = $qty_plus - $qty_minus ;
                $moveLine["date"]         = ($i->date !=null)?$i->date:$i->created_at->format("Y-m-d") ;
                if($i->delivered_previouse_id != null){
                    $deliveryItem       = \App\Models\DeliveredPrevious::find($i->delivered_previouse_id); 
                    $delivery           = \App\Models\TransactionDelivery::find($deliveryItem->transaction_recieveds_id);
                    $moveLine["source"] = $delivery->reciept_no;
                }elseif($i->delivered_wrong_id  != null){
                    $deliveryItem       = \App\Models\DeliveredWrong::find($i->delivered_wrong_id); 
                    $delivery           = \App\Models\TransactionDelivery::find($deliveryItem->transaction_recieveds_id);
                    $moveLine["source"] = $delivery->reciept_no;
                }elseif($i->recived_previous_id  != null){
                    $deliveryItem       = \App\Models\RecievedPrevious::find($i->recived_previous_id); 
                    $delivery           = \App\Models\TransactionRecieved::find($deliveryItem->transaction_deliveries_id);
                    $moveLine["source"] = $delivery->reciept_no;
                }elseif($i->recieved_wrong_id  != null){
                    $deliveryItem       = \App\Models\RecievedPrevious::find($i->recieved_wrong_id); 
                    $delivery           = \App\Models\TransactionRecieved::find($deliveryItem->transaction_deliveries_id);
                    $moveLine["source"] = $delivery->reciept_no;
                }elseif($i->purchase_line_id  != null){
                    $line               = \App\PurchaseLine::find($i->purchase_line_id); 
                    $transaction        = \App\Transaction::find($line->transaction_id); 
                    $moveLine["source"] = $transaction->ref_no;
                }elseif($i->transaction_sell_line_id  != null){
                    $line               = \App\TransactionSellLine::find($i->transaction_sell_line_id); 
                    $transaction        = \App\Transaction::find($line->transaction_id); 
                    $moveLine["source"] = $transaction->ref_no;
                }elseif($i->opening_quantity_id  != null){
                    $transaction        = \App\Transaction::find($i->transaction_id); 
                    $moveLine["source"] = $transaction->ref_no;
                }else{
                    $moveLine["source"] = "";
                }
                $move[]                 = $moveLine;
            }
            $moveList["movements"]      = $move;
            $moveList["current_qty"]    = $qty_plus - $qty_minus;
            return $moveList;
        }catch(Exception $e){
            return false;
        }
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE WAREHOUSE
    public static function createNewWarehouse($user,$data) {
       try{
            $business_id              = $user->business_id;
            $Warehouse                = new \App\Models\Warehouse();
            $Warehouse->business_id   = $business_id;
            $Warehouse->name          = $data["name"];
            $Warehouse->mainStore     = ($data["parent_id"] != 0)?((\App\Models\Warehouse::find($data["parent_id"]))?\App\Models\Warehouse::find($data["parent_id"])->name:""):"";
            $Warehouse->status        = ($data["parent_id"] != 0)?1:0;
            $Warehouse->parent_id     = ($data["parent_id"] != 0)?$data["parent_id"] :null;
            $Warehouse->description   = $data["description"];
            $Warehouse->save();
        
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE WAREHOUSE
    public static function updateOldWarehouse($user,$data,$id) {
       try{
            $business_id              = $user->business_id;
            $Warehouse                = \App\Models\Warehouse::find($id);
            $Warehouse->name          = $data["name"];
            $Warehouse->mainStore     = ($data["parent_id"] != 0)?((\App\Models\Warehouse::find($data["parent_id"]))?\App\Models\Warehouse::find($data["parent_id"])->name:""):"";
            $Warehouse->status        = ($data["parent_id"] != 0)?1:0;
            $Warehouse->parent_id     = ($data["parent_id"] != 0)?$data["parent_id"] :null;
            $Warehouse->description   = $data["description"];
            $Warehouse->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }

    // **3** GET WAREHOUSE  
    public static function allData($type=null,$id=null,$business_id) {
        try{
            $list   = [];
            if($type != null){
                $warehouse     = \App\Models\Warehouse::where("business_id",$business_id)->get();
                if(count($warehouse) == 0 ){ return false; }
                foreach($warehouse as $ie){
                    $list[] = [
                        "id"                 => $ie->id,
                        "name"               => $ie->name,
                        "description"        => $ie->description,
                        "main_store_name"    => $ie->mainStore,
                        "parent_id"          => $ie->parent_id,
                        "date"               => $ie->created_at->format("Y-m-d h:i:s a"),
                        
                    ];
                }
            }else{
                $warehouse  = \App\Models\Warehouse::find($id);
                if(empty($warehouse)){ return false; }
                $list["info"] = [
                    "id"                 => $warehouse->id,
                    "name"               => $warehouse->name,
                    "description"        => $warehouse->description,
                    "main_store_name"    => $warehouse->mainStore,
                    "parent_id"          => $warehouse->parent_id,
                    "date"               => $warehouse->created_at->format("Y-m-d h:i:s a"),
                ];
                $list["require"]      = Warehouse::getRequire($business_id);
            }
            return $list; 
        }catch(Exception $e){
            return false;
        }
    }
    // **4** GET REQUIRE
    public static function getRequire($business_id){
        $list_1          = [];$list_2 = [];$list_3  = [];$list = [];
        $warehouse       = \App\Models\Warehouse::where("business_id",$business_id)->get();
        foreach($warehouse as $e){
            $list_1[] = [
                "id"   => $e->id,
                "name" => $e->name,
            ];
        }
        $list["stores"]  = $list_1;
        return  $list;
    }

}
