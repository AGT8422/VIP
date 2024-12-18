<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\WarehouseInfo;
use App\Product;
use DB;

class MovementWarehouse extends Model
{
     use HasFactory,SoftDeletes;
     protected $guarded = ['id'];
     
     // ***  FOR PURCHASE OR SALE WAREHOUSE MOVEMENT 
     // *1*  AGT8422  FINISH
     // ******************
          public static function movemnet_warehouse($data,$product,$quantity,$store_id,$line,$type="plus",$id=NULL,$wrong_id=NULL,$checker=null)
          {
              
               $info          =  WarehouseInfo::where('store_id',$store_id)->where('product_id',$product->id)->first();
               $lin_id        =  null;
               if($data->type == "sale"  || $data->type == "sell_return" || $data->type == "production_sell" || $data->type == "Stock_Out" ){
                    $li       = \App\TransactionSellLine::OrderBy('id','desc')->where("transaction_id",$data->id)->where("id",$line->id)->where("product_id",$product->id)->first();
                    $movement = \App\MovementWarehouse::where("transaction_id",$data->id)->where("transaction_sell_line_id",$line->id)->first();
                    
               }else if($data->type == "purchase" || $data->type == "production_purchase" || $data->type == "Stock_In" ){
                    if($data->type == "production_purchase"){
                         $li     = \App\PurchaseLine::OrderBy('id','desc')->where("transaction_id",$data->mfg_parent_production_purchase_id)->where("product_id",$product->id)->where("id",$line->id)->first();
                         if(!empty($li)){ $lin_id  =  $li->id; } 
                         if($wrong_id==NULL){
                              $movement = \App\MovementWarehouse::where("transaction_id",$data->id)->where("purchase_line_id", $lin_id)->first();
                         }else{
                              $movement = \App\MovementWarehouse::where("transaction_id",$data->id)->where("purchase_line_id", $lin_id)->first();
                         }
                    }elseif($data->type == "Stock_In"){
                         $li     = \App\PurchaseLine::OrderBy('id','desc')->where("transaction_id",$data->id)->where("product_id",$product->id)->where("id",$line->id)->first();
                         if(!empty($li)){ $lin_id  =  $li->id; }
                         if($wrong_id==NULL){
                              $movement = \App\MovementWarehouse::where("transaction_id",$data->id)->where("recived_previous_id",$id)->where("purchase_line_id", $lin_id)->first();
                         }else{
                              $movement = \App\MovementWarehouse::where("transaction_id",$data->id)->where("recieved_wrong_id",$wrong_id)->where("purchase_line_id", $lin_id)->first();
                         }
                    }else{
                         $li       = \App\PurchaseLine::OrderBy('id','desc')->where("transaction_id",$data->id)->where("product_id",$product->id)->where("id",$line->id)->first();
                         if(!empty($li)){ $lin_id  =  $li->id; } 
                         if($wrong_id==NULL){
                              if($id){
                                   $previous = \App\Models\RecievedPrevious::find($id); 
                                   $movement = \App\MovementWarehouse::where("transaction_id",$data->id)->where("recived_previous_id",$id)->where("purchase_line_id",$previous->line_id)->first();
                              }else{
                                   $movement = \App\MovementWarehouse::where("transaction_id",$data->id)->where("recived_previous_id",$id)->where("purchase_line_id", $lin_id)->first();
                              }
                         }else{
                              $movement = \App\MovementWarehouse::where("transaction_id",$data->id)->where("recieved_wrong_id",$wrong_id)->where("purchase_line_id", $lin_id)->first();
                         }
                    }
               }
               if(empty($movement)){
                    $move                        =  new MovementWarehouse;
                    $move->business_id           =  $data->business_id;
                    $move->transaction_id        =  $data->id  ;
                    $move->product_name          =  $product->name;
                    $move->unit_id               =  $product->unit_id;
                    $move->store_id              =  $store_id  ;
                    $move->movement              =  $data->type;
                    $move->product_id            =  $product->id;
                    $move->recived_previous_id   =  ($wrong_id == null)?$id:null;
                    $move->recieved_wrong_id     =  $wrong_id;
                    if ($type == 'plus') {
                         $move->plus_qty         =  $quantity;
                         $move->minus_qty        =  0;
                         $move->current_qty      =  (isset($info->product_qty))?$info->product_qty:0 ;
                    }else {
                         $move->plus_qty         =  0;
                         $move->minus_qty        =  $quantity;
                         $move->current_qty      =  (isset($info->product_qty))?$info->product_qty:0;
                    }
                    if (isset($line->pp_without_discount)) {
                         $move->current_price    =  $line->pp_without_discount;
                    }elseif (isset($line->unit_price_before_discount)) {
                         $move->current_price    =  $line->unit_price_before_discount;
                    }else{
                         $move->current_price    =  0;
                    }
                    if($data->type == "sale" || $data->type == "sell_return" || $data->type == "production_sell" || $data->type == "Stock_Out"){
                         $move->transaction_sell_line_id   =  $line->id;
                    }else if($data->type == "purchase" || $data->type == "production_purchase" || $data->type == "Stock_In"){
                         $move->purchase_line_id           =  $lin_id;
                    }
                    if($wrong_id!=NULL){
                         $transaction_id  = \App\Models\RecievedWrong::find($id) ;
                    }else{
                         $transaction_id  = \App\Models\RecievedPrevious::find($id) ;
                    }                    
                    $move->date      = ($checker != null) ?  ( (!empty($transaction_id) )? (  ($transaction_id->TrRecieved)  ?  (  ($transaction_id->TrRecieved->date!=null) ?  $transaction_id->TrRecieved->date  :  $data->transaction_date  )  :  $data->transaction_date)  :  $data->transaction_date  )    :   $data->transaction_date;
                    $move->save();
               }else{
                    if($quantity!=0){
                         $movement->store_id              =  $store_id  ;
                         if ($type == 'plus') {
                              $movement->plus_qty         =  $quantity;
                              $movement->minus_qty        =  0;
                              $movement->current_qty      =  (isset($info->product_qty))?$info->product_qty:0 ;
                         }else {
                              $movement->plus_qty         =  0;
                              $movement->minus_qty        =  $quantity;
                              $movement->current_qty      =  (isset($info->product_qty))?$info->product_qty:0;
                         }
                         if (isset($line->pp_without_discount)) {
                              $movement->current_price       =  $line->pp_without_discount;
                         }else if (isset($line->unit_price_before_discount)) {
                              $movement->current_price       =  $line->unit_price_before_discount;
                         }else {
                              $movement->current_price =  0;
                         }
                        if($wrong_id!=NULL){
                             $transaction_id  = \App\Models\RecievedWrong::find($id) ;
                        }else{
                             $transaction_id  = \App\Models\RecievedPrevious::find($id) ;
                        }                         
                        $movement->date    = ($checker != null) ?  ( (!empty($transaction_id) )? (  ($transaction_id->TrRecieved)  ?  (  ($transaction_id->TrRecieved->date!=null) ?  $transaction_id->TrRecieved->date  :  $data->transaction_date  )  :  $data->transaction_date)  :  $data->transaction_date  )    :   $data->transaction_date;
                         $movement->update();
                    }else{
                         $movement->delete();
                    }
               }
               
          }
     // ****************** 
     
     // *** FOR SALE JUST  
     // *2* AGT8422  FINISH
     // ****************** 
          public static function movemnet_warehouse_sell($data,$product,$quantity,$store_id,$line,$id=NULL,$wrong_id=NULL,$type=null)
          {
               
               $info =  WarehouseInfo::where('store_id',$store_id)
                                   ->where('product_id',$product->id)->first();
               $move                  =  new MovementWarehouse;
               $move->business_id     =  $data->business_id;
               $move->transaction_id  =  $data->id  ;
               $move->product_name    =  $product->name;
               $move->unit_id         =  $product->unit_id;
               $move->store_id        =  $store_id  ;
               $move->movement        =  $data->type;
               if($type != null){
                    $move->plus_qty        =  $quantity;
                    $move->minus_qty       =  0;
               }else{
                    $move->plus_qty        =  0;
                    $move->minus_qty       =  $quantity;
               }
               $x =  (isset($info->product_qty))?$info->product_qty:0;
               $move->current_qty     =  $x ;
               $move->product_id      =  $product->id;
               $move->current_price   =  (isset($line->unit_price_before_discount))?$line->unit_price_before_discount:0;
               $move->delivered_previouse_id =  $id;
               $move->delivered_wrong_id =  $wrong_id;
               $move->transaction_sell_line_id =  (isset($line->id))?$line->id:null;
               if($id != null){
                    $tr_Delivery  = \App\Models\DeliveredPrevious::find($id); 
                    $move->date   = ($tr_Delivery)?(($tr_Delivery->T_delivered)?$tr_Delivery->T_delivered->date:$data->transaction_date):$data->transaction_date;
               }else{
                    if($wrong_id != null){
                         $tr_Delivery  = \App\Models\DeliveredWrong::find($wrong_id); 
                         $move->date   = ($tr_Delivery)?(($tr_Delivery->T_delivered)?$tr_Delivery->T_delivered->date:$data->transaction_date):$data->transaction_date;
                    }else{
                         $move->date   =  $data->transaction_date ;
                    }
               }
               $move->save();
          }
     // ******************
     
     // *** FOR RECEIVE RETURN JUST  
     // *3* AGT8422 
     // ****************** 
          public static function sell_return($id,$quantity,$type="correct")
          {
               $data =  MovementWarehouse::where(function($query ) use($type,$id){
                                        if ($type == 'correct') {
                                             $query->where('delivered_previouse_id',$id);
                                        }else{
                                             $query->where('delivered_wrong_id',$id);
                                        }
                                   })->first();
               if ($data ) {
                         $info =  WarehouseInfo::where('store_id',$data->store_id)
                                        ->where('product_id',$data->product_id)->first();
                         $data->plus_qty        =  ($quantity > 0) ?$quantity:0 ;
                         $data->minus_qty       =  ($quantity < 0) ?$quantity:0;
                         $x                     =  isset($info->product_qty)?$info->product_qty:0;
                         $data->current_qty     =  $x +  $quantity;
                         $data->save();
               }
          }
     // ******************

     // *** FOR RECEIVE RETURN JUST  
     // *4* AGT8422   FINISH
     // ****************** 
          public static function recieve_return($id,$quantity,$type="correct",$store=null,$return=null)
          {
               $data =  MovementWarehouse::where(function($query ) use($type,$id){
                                   if ($type == 'correct') {
                                        $query->where('recived_previous_id',$id);
                                   }else{
                                        $query->where('recieved_wrong_id',$id)  ;
                                   }
                                   })->first();
                                   
               if ($data) {
                    $info =  WarehouseInfo::where('store_id',$data->store_id)
                                             ->where('product_id',$data->product_id)
                                             ->first();
                    if($store!=null){
                         $info =  WarehouseInfo::where('store_id',$store)
                                                  ->where('product_id',$data->product_id)
                                                  ->first();
                         $data->store_id        =  $store ;
                    }
                    if($return!=null){
                         $data->minus_qty  =  $quantity ;
                         $data->plus_qty   =  0;
                    }else{
                         $data->plus_qty   =  $quantity ;
                         $data->minus_qty  =  0;
                    }
                    $x                     =  isset($info->product_qty)?$info->product_qty:0;
                    $data->current_qty     =  $x ;
                    $data->update();
               }
          }
     // ******************

     // *** AGT8422 FOR UPDATE WAREHOUSE ROWS *********************************************************************
          public static function update_receive($data,$prev,$quantity,$type="correct",$store=null,$return=null)
          {
               
               if($type=="wrong"){
                    if($data->type == "purchase"){
                              $movement_Warehouse = \App\MovementWarehouse::where("transaction_id",$data->id)->where("recieved_wrong_id",$prev->id)->first();
                    }else{
                         $movement_Warehouse  = \App\MovementWarehouse::where("transaction_id",$data->id)->where("delivered_wrong_id",$prev->id)->first();
                    }
               }else{
                    if($data->type == "purchase"){
                         $movement_Warehouse = \App\MovementWarehouse::where("transaction_id",$data->id)->where("recived_previous_id",$prev->id)->first();
                    }else{
                         $movement_Warehouse = \App\MovementWarehouse::where("transaction_id",$data->id)->where("delivered_previouse_id",$prev->id)->first();
                    }
               }
               if(!empty($movement_Warehouse)){
                    $qty    = ($movement_Warehouse->plus_qty != 0)?$movement_Warehouse->plus_qty:$movement_Warehouse->minus_qty;
                    $margin = $prev->current_qty - $qty;
                    $info   =  WarehouseInfo::where('store_id',$store)->where('product_id',$prev->product->id)->first();
                    if($margin > 0){
                         if($movement_Warehouse->plus_qty != 0){
                              $movement_Warehouse->plus_qty    = $prev->current_qty ;
                         }else{
                              $movement_Warehouse->minus_qty   = $prev->current_qty ;
                         }
                    }elseif($margin < 0){
                         if($movement_Warehouse->plus_qty != 0){
                              $movement_Warehouse->plus_qty    = $margin * -1 ;
                         }else{
                              $movement_Warehouse->minus_qty   = $margin * -1 ;
                         }
                    } 
                    if($prev->purchase_line){
                         $movement_Warehouse->current_price = $prev->purchase_line->purchase_price;
                    }
                    $movement_Warehouse->date          =  ($prev->date != null)?$prev->date:$prev->created_at;
                    $movement_Warehouse->current_qty   = (isset($info->product_qty))?$info->product_qty:0 ;
                    $movement_Warehouse->update();
               }else{
                    $move                      =  new MovementWarehouse;
                    $move->business_id         =  $data->business_id;
                    $move->transaction_id      =  $data->id  ;
                    $move->product_name        =  $prev->product->name;
                    $move->unit_id             =  $prev->product->unit_id;
                    $move->store_id            =  $store  ;
                    $move->movement            =  $data->type;
                    $move->plus_qty            =  $prev->current_qty;
                    $move->minus_qty           =  0;
                    $move->current_qty         =  (isset($info->product_qty))?$info->product_qty:0 ;
                    $move->product_id          =  $prev->product->id;
                    if (isset($prev->purchase_line->pp_without_discount)) {
                         $move->current_price       =  $prev->purchase_line->pp_without_discount;
                    }elseif (isset($prev->purchase_line->unit_price_before_discount)) {
                         $move->current_price       =  $prev->purchase_line->unit_price_before_discount;
                    }else{
                         $move->current_price =  0;
                    }
                    $move->recived_previous_id =  ($type=="wrong")?null:$prev->id;
                    $move->recieved_wrong_id   =  ($type=="wrong")?$prev->id:null;
                    $move->date                =  ($prev->date != null)?$prev->date:$prev->created_at;
                    $move->purchase_line_id    =  isset($prev->purchase_line)?$prev->purchase_line->id:null;
                    $move->save(); 
               }
                
          }
     // *** *******FINISH***************** ****************************************************************END**

     // *** FOR TRANSFER SECTION CREATE
     // *5* AGT8422  FINISH
     // ******************
          public static function store_moves($store_from,$store_to,$product_id,$quantity,$data_from,$data_to)
          {
               
                    $product   =  Product::find($product_id);
                    $line      = \App\PurchaseLine::OrderBy('id','desc')->where('product_id',$product_id)->first();
                    $sell      = \App\TransactionSellLine::OrderBy('id','desc')->where('product_id',$product_id)->first();
                    MovementWarehouse::movemnet_warehouse($data_from,$product,$quantity,$store_from,$sell,$type="decrease");
                    MovementWarehouse::movemnet_warehouse($data_to,$product,$quantity,$store_to,$line,$type="plus");
                    
          }
     // ******************

     // *** FOR TRANSFER SECTION CREATE
     // *6* AGT8422 FINISH
     // ******************
          public static function update_move_transafer($store_old,$store_new,$sell_transfer,$purchase_transfer)
          {
                    //......... 
                    $ids_purchase   = \App\PurchaseLine::where("transaction_id",$purchase_transfer->id)->get();
                    $ids_sell       = \App\TransactionSellLine::where("transaction_id",$sell_transfer->id)->get();
                    //......... 
                    foreach($ids_purchase as $it){
                         $info =  WarehouseInfo::where('store_id',$store_new)
                                             ->where('product_id',$it->product->id)->sum("product_qty");
                         $move_purhcase  = MovementWarehouse::where("transaction_id",$purchase_transfer->id)->where("purchase_line_id",$it->id)->first();
                         if(empty($move_purhcase)){
                              $move                      =  new MovementWarehouse;
                              $move->business_id         =  $purchase_transfer->business_id;
                              $move->transaction_id      =  $purchase_transfer->id  ;
                              $move->product_name        =  $it->product->name;
                              $move->unit_id             =  $it->product->unit_id;
                              $move->store_id            =  $store_new  ;
                              $move->movement            =  $purchase_transfer->type;
                              $move->plus_qty            =  $it->quantity;
                              $move->minus_qty           =  0;
                              $move->current_qty         =  (isset($info->product_qty))?$info->product_qty:0 ;
                              $move->product_id          =  $it->product_id;
                              $move->current_price       =  $it->pp_without_discount;     
                              $move->for_move            =  1;     
                              $move->purchase_line_id    =  $it->id;
                              $move->date                =  $purchase_transfer->transaction_date;
                              $move->save();
                         }else{
                              if($it->quantity != 0){
                                   $move_purhcase->store_id       =  $store_new  ;
                                   $move_purhcase->current_price  =  $it->pp_without_discount;
                                   $move_purhcase->plus_qty       =  $it->quantity;
                                   $move_purhcase->minus_qty      =  0;
                                   $move_purhcase->current_qty    =  $info   ;
                                   $move_purhcase->date           =  $purchase_transfer->transaction_date;
                                   $move_purhcase->update() ;
                              }else{
                                   $move_purhcase->delete() ;
                              }
                         }
                    }
                    //......... 
                    foreach($ids_sell as $it){
                         $info =  WarehouseInfo::where('store_id',$store_old)
                                                  ->where('product_id',$it->product->id)->sum("product_qty");
                         $move_sell      = MovementWarehouse::where("transaction_id",$sell_transfer->id)->where("transaction_sell_line_id",$it->id)->first();
                         if(empty($move_sell)){
                              $move                           =  new MovementWarehouse;
                              $move->business_id              =  $sell_transfer->business_id;
                              $move->transaction_id           =  $sell_transfer->id  ;
                              $move->product_name             =  $it->product->name;
                              $move->unit_id                  =  $it->product->unit_id;
                              $move->store_id                 =  $store_old  ;
                              $move->movement                 =  $sell_transfer->type;
                              $move->plus_qty                 =  0;
                              $move->minus_qty                =  $it->quantity;
                              $move->current_qty              =  (isset($info->product_qty))?$info->product_qty:0;
                              $move->product_id               =  $it->product->id;
                              $move->current_price            =  $it->unit_price_before_discount;
                              $move->for_move                 =  1;
                              $move->transaction_sell_line_id =  $it->id;
                              $move->date                     =  $sell_transfer->transaction_date;
                              $move->save();
                         }else{
                              if($it->quantity != 0){
                                   // ... here
                                   $move_sell->store_id            =  $store_old  ;
                                   $move_sell->current_price       =  $it->unit_price_before_discount;
                                   $move_sell->plus_qty            =  0;
                                   $move_sell->minus_qty           =  $it->quantity;
                                   $move_sell->current_qty         =  $info   ;
                                   $move_sell->date                =  $sell_transfer->transaction_date;
                                   $move_sell->update() ;
                              }else{
                                   $move_sell->delete() ;
                              }
                         }
                    }
          }
     // ******************

     //  *** FOR  SUPPLIER RETURN SECTION 
     //  *7* AGT8422
     // ******************
          public static function supplier_return($data,$product_id,$quantity,$store_id,$price,$type="plus")
          {
               $product =  Product::find($product_id);
               $info    =  WarehouseInfo::where('store_id',$store_id)
                              ->where('product_id',$product->id)->first();
               $move                      =  new MovementWarehouse;
               $move->business_id         =  $data->business_id;
               $move->transaction_id      =  $data->id  ;
               $move->product_name        =  $product->name;
               $move->unit_id             =  $product->unit_id;
               $move->store_id            =  $store_id  ;
               $move->movement            =  $data->type;
               if ($type == 'plus') {
                    $move->plus_qty         =  $quantity;
                    $move->minus_qty        =  0;
                    $move->current_qty      =  (isset($info->product_qty))?$info->product_qty:0 ;
               }else {
                    $move->plus_qty         =  0;
                    $move->minus_qty        =  $quantity;
                    $move->current_qty      =  (isset($info->product_qty))?$info->product_qty:0;
               }
               $move->product_id          =  $product->id;
               $move->current_price       =  $price;
               $move->date                =  $data->transaction_date;
               $move->save();
          }
     // ******************
     
     //  *** FOR OPENING QTY SECTION 
     //  *8* AGT8422  FINISH
     // ******************
          public static function opening_qty($data,$product,$quantity,$store_id,$line,$type="plus",$id)
          {
               $info =  WarehouseInfo::where('store_id',$store_id)
                              ->where('product_id',$product->id)->first();
               $move =  MovementWarehouse::where('purchase_line_id',$id)->first();
               if (empty($move)) {
                    $move                      =  new MovementWarehouse;
               }
               $move->business_id         =  $data->business_id;
               $move->transaction_id      =  $data->id  ;
               $move->product_name        =  $product->name;
               $move->unit_id             =  $product->unit_id;
               $move->store_id            =  $store_id  ;
               $move->movement            =  $data->type;
               if ($type == 'plus') {
                    $move->plus_qty         =  $quantity;
                    $move->minus_qty        =  0;
                    $move->current_qty      =  (isset($info->product_qty))?$info->product_qty:0 ;
               }else {
                    $move->plus_qty         =  0;
                    $move->minus_qty        =  $quantity;
                    $move->current_qty      =  (isset($info->product_qty))?$info->product_qty:0;
               }
               $move->product_id          =  $product->id;
               if (isset($line->pp_without_discount)) {
                    $move->current_price       =  $line->pp_without_discount;
               }elseif (isset($line->unit_price_before_discount)) {
                    $move->current_price       =  $line->unit_price_before_discount;
               }else{
                    $move->current_price =  0;
               }
               $move->purchase_line_id =  $id;
               $move->date             =  ($line->transaction)?$line->transaction->transaction_date:$line->created_at;
               $move->save();
               if ($quantity == 0) {
                    $move->delete();
                    \App\PurchaseLine::where('id',$id)->delete();
               }
          }
     // ******************
     
     //  *** FOR PRODUCTION SECTION 
     //  *9* AGT8422  FINISH
     // ******************
          public static function production($data,$product,$quantity,$store_id,$price,$type="plus")
          {
               $line     = \App\PurchaseLine::where("transaction_id",$data->mfg_parent_production_purchase_id)->first();
               $info     = WarehouseInfo::where('store_id',$store_id)->where('product_id',$product->id)->first();
               $movement = \App\MovementWarehouse::where("transaction_id",$data->id)->where("purchase_line_id",$line->id)->first();
               if(empty($movement)){
                    $move                      =  new MovementWarehouse;
                    $move->business_id         =  $data->business_id;
                    $move->transaction_id      =  $data->id  ;
                    $move->product_name        =  $product->name;
                    $move->unit_id             =  $product->unit_id;
                    $move->store_id            =  $store_id  ;
                    $move->movement            =  "production_purchase";
                    $move->plus_qty            =  $quantity;
                    $move->minus_qty           =  0;
                    $move->current_qty         =  (isset($info->product_qty))?$info->product_qty:0 ;
                    $move->product_id          =  $product->id;
                    $move->current_price       =  $price;
                    $move->purchase_line_id    =  $line->id;
                    $move->date                =  $data->transaction_date;
                    $move->save();
               }else{
                    if($quantity != 0){
                         $movement->movement        =  "production_purchase";
                         $movement->store_id        =  $store_id  ;
                         $movement->plus_qty        =  $quantity;
                         $movement->minus_qty       =  0;
                         $movement->current_price   =  $price;
                         $movement->date            =  $data->transaction_date;
                         $movement->update();
                    }else{
                         $movement->delete();
                    }
               }
          }
     // ******************
     
     //  **** FOR RETURN PURCHASE SECTION 
     //  *10* AGT8422  FINISH
     // ******************
          public static function return_purchase($data,$product,$quantity,$store_id,$line)
          {
               $info   =  WarehouseInfo::where('store_id',$store_id)
                                        ->where('product_id',$product->id)->first();
               $move   = MovementWarehouse::where('purchase_line_id',$line->id)->first();
               if (empty($move)) {
                    $move                      =  new MovementWarehouse;
                    $move->business_id         =  $data->business_id;
                    $move->transaction_id      =  $data->id  ;
                    $move->product_name        =  $product->name;
                    $move->unit_id             =  $product->unit_id;
                    $move->store_id            =  $store_id  ;
                    $move->movement            =  'purchase return ';
                    $move->purchase_line_id    =  $line->id;
               }
               $move->plus_qty            =  0;
               $move->minus_qty           =  abs($quantity) ;
               $move->current_qty         =  (isset($info->product_qty))?$info->product_qty:0 ;
               $move->product_id          =  $product->id;
               $move->current_price       =  $line->pp_without_discount;
               $move->date                =  $data->transaction_date;
               $move->save();
          }
     // ******************
     
     //  **** FOR RETURN SALE SECTION 
     //  *11* AGT8422
     // ******************
          public static function return_sell($data,$product,$quantity,$store_id,$line)
          {
               $info =  WarehouseInfo::where('store_id',$store_id)
                              ->where('product_id',$product->id)->first();
               $move   = MovementWarehouse::where('transaction_sell_line_id',$line->id)->first();
               if (empty($move)) {
                         $move                      =  new MovementWarehouse;
                         $move->business_id         =  $data->business_id;
                         $move->transaction_id      =  $data->id  ;
                         $move->product_name        =  $product->name;
                         $move->unit_id             =  $product->unit_id;
                         $move->store_id            =  $store_id  ;
                         $move->movement            =  'sales return '.$product->name.' ('.abs($quantity).')';
                         $move->transaction_sell_line_id    =  $line->id;
               }
               $move->plus_qty            =  abs($quantity);
               $move->minus_qty           =   0;
               $move->current_qty         =  (isset($info->product_qty))?$info->product_qty:0 ;
               $move->product_id          =  $product->id;
               $move->current_price       =  $line->unit_price;
               $move->date                =  $data->transaction_date;
               $move->save();
          }
     // ******************
     
     //  **** FOR DELETE MOVE SECTION 
     //  *12* AGT8422
     // ******************
          public static function delete_move($move)
          {    
               foreach($move as $it){
                    $it->delete();
               }
          }
     // ****************** 


     // ******* RELATION SECTION
          public function store()
          {
               return $this->belongsTo('\App\Models\Warehouse','store_id');
          }
          public function product()
          {
               return $this->belongsTo('\App\Product','product_id');
          }
          public  function business()
          {
               return $this->belongsTo("\App\Business","business_id");
          }
          public  function receivedPrevious()
          {
               return $this->belongsTo("\App\Models\RecievedPrevious","recived_previous_id");
          }
          public  function receivedWrong()
          {
               return $this->belongsTo("\App\Models\RecievedWrong","recieved_wrong_id");
          }
          public  function deliveredPrevious()
          {
               return $this->belongsTo("\App\Models\DeliveredPrevious","delivered_previouse_id");
          }
          public  function deliveredWrong()
          {
               return $this->belongsTo("\App\Models\DeliveredWrong","delivered_wrong_id");
          }
          public function transaction()
          {
               return $this->belongsTo("\App\Transaction","transaction_id");
          }
     // ******** END

  
}
