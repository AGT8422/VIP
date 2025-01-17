<?php

namespace App\Models\FrontEnd\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FrontEnd\Utils\GlobalUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Excel;
class OpeningQuantity extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END OPENING QUANTITY *** // 
    // **1** ALL OPENING QUANTITY
    public static function getOpeningQuantity($user,$filter=null) {
        try{
            $business_id       = $user->business_id;
            if($filter != null){ 
                $data              = OpeningQuantity::allData("all",null,$business_id,$filter);
            }else{
                $data              = OpeningQuantity::allData("all",null,$business_id);
            } 
            if($data == false){ return false;}
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE OPENING QUANTITY
    public static function createOpeningQuantity($user,$data) {
        try{
            $business_id             = $user->business_id;
            $create                  = OpeningQuantity::requirement($user);
            return $create;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT OPENING QUANTITY
    public static function editOpeningQuantity($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $data                    = OpeningQuantity::allData(null,$id,$business_id);
            if($data  == false){ return false; }
            $edit                    = OpeningQuantity::requirement($user);
            $list["info"]            = $data;
            $list["require"]         = $edit;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE OPENING QUANTITY
    public static function storeOpeningQuantity($user,$data) {
        try{
            \DB::beginTransaction();
            $output              = OpeningQuantity::createNewOpeningQuantity($user,$data);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE OPENING QUANTITY
    public static function updateOpeningQuantity($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $output              = OpeningQuantity::updateOldOpeningQuantity($user,$data,$id);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE OPENING QUANTITY
    public static function deleteOpeningQuantity($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id     = $user->business_id;
            $transaction     = \App\Transaction::find($id);
            $purchase        = \App\PurchaseLine::where("transaction_id",$id)->get();
            if(!$transaction){ return false; }
            foreach($purchase as $i){
                $openQ                        = \App\Models\OpeningQuantity::where("transaction_id",$id)->where("purchase_line_id",$i->id)->first();
                $itemMove                     = \App\Models\ItemMove::where("line_id",$i->id)->first();
                $oldMove                      = \App\Models\ItemMove::where("product_id",$itemMove->product_id)->where("line_id","!=",$i->id)->first();
                $warehouseMove                = \App\MovementWarehouse::where('opening_quantity_id',$openQ->id)->first();
                \App\Models\WarehouseInfo::update_stoct($i->product_id,$i->store_id,($i->quantity*-1),$transaction->business_id);
                $warehouseMove->delete();
                     $itemMove->delete();
                        $openQ->delete();
                            $i->delete();
                if(!empty($oldMove)){ \App\Models\ItemMove::updateRefresh($oldMove,$oldMove,[],$oldMove->date); }

            }
            $transaction->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
        
    }
    // **7** VIEW OPENING QUANTITY
    public static function viewOpeningQuantity($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $data      = OpeningQuantity::allData(null,$id,$business_id);
            if($data  == false){ return false; } 
            \DB::commit();
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **8** EXPORT 
    public static function openQuantityExport($user,$data){
        return  asset('files/import_opening_stock_csv_template.xls') ;
    }
    // **9** IMPORT 
    public static function openQuantityImport($user,$data,$request){
        try {
            $productUtil     = new ProductUtil();
            $transactionUtil = new TransactionUtil();
            $notAllowed      = $productUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }
            
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            
            if ($request->hasFile('products_csv')) {
                $file = $request->file('products_csv');
                
                $parsed_array  = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id   = $user->business_id ;
                $user_id       = $user->id ;

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';
                
                \DB::beginTransaction();
                $business_id     = $user->business_id;
                $ref_count       = GlobalUtil::SetReferenceCount('Open Quantity',$business_id);
                $ref_no          = GlobalUtil::GenerateReferenceCount('Open Quantity', $ref_count,$business_id);
               
                $store           = \App\Models\Warehouse::where('name',$imported_data[0][2])->first();
                if (empty($store)) {
                    $store    =  \App\Models\Warehouse::where('business_id',$business_id)
                                    ->where('parent_id','>',0)->first();
                }
                $location     =  \App\BusinessLocation::where('business_id',$business_id)
                                    ->where('name',$imported_data[0][1])->first();
                if (empty($location)) {
                    $location =  \App\BusinessLocation::where('business_id',$business_id)
                                    ->first();
                }
                
                $tr =   \App\Transaction::create([
                                            'ref_no'          => $ref_no,
                                            'type'            => 'opening_stock',
                                            'status'          => 'received',
                                            'business_id'     => $business_id,
                                            'store'           => $store->id,
                                            'location_id'     => $location->id,  
                                            'transaction_date'=> date('Y-m-d h:i:s a',time())
                                        ]);
                foreach ($imported_data as $key => $value) {
                   // process start
                    $product       =  \App\Product::where('sku',$value[0])->first();
                    if ($product) {
                        $store     = \App\Models\Warehouse::where('name',$value[2])->first();
                        if (empty($store)) {
                            $store =  \App\Models\Warehouse::where('business_id',$business_id)
                                            ->where('parent_id','>',0)->first();
                        }
                        // create_purchase line 
                        $pr                            = new \App\PurchaseLine;
                        $pr->store_id                  = $request->store_id;
                        $pr->product_id                = $product->id;
                        $pr->transaction_id            = $tr->id;
                        $pr->variation_id              = isset($product->variations[0]->id)?$product->variations[0]->id:NULL;
                        $pr->quantity                  = $value[3];
                        $pr->pp_without_discount       = $value[4];
                        $pr->discount_percent          = 0;
                        $pr->purchase_price            = $value[4];
                        $pr->purchase_price_inc_tax    = ($value[4] + $value[4]*.05);
                        $pr->item_tax                  = $value[4]*.05;
                        $pr->tax_id                    =  1;
                        $pr->save();
                        //end
                        $data                          =  new \App\Models\OpeningQuantity;
                        $data->warehouse_id            =  $store->id;
                        $data->business_location_id    =  $location->id;
                        $data->quantity                =  $value[3];
                        $data->product_id              =  $product->id;
                        $data->price                   =  $value[4];
                        $data->transaction_id          =  $tr->id;
                        $data->purchase_line_id        =  $pr->id;
                        $data->save();
                        $data =  \App\Models\OpeningQuantity::find($data->id);
                        \App\Models\WarehouseInfo::update_stoct($data->product_id,$data->warehouse_id,$data->quantity,$business_id);
                        //****** eb ..............................................................
                        $variation_id = Variation::where('product_id', $data->product_id)->first();
                         //.........................................................................
                        //move
                        $info                      =  \App\Models\WarehouseInfo::where('store_id',$data->warehouse_id)
                                                        ->where('product_id',$data->product_id)->first();
                        $move                      =  new \App\MovementWarehouse;
                        $move->business_id         =  $tr->business_id;
                        $move->transaction_id      =  $tr->id  ;
                        $move->product_name        =  $data->product->name;
                        $move->unit_id             =  $data->product->unit_id;
                        $move->store_id            =  $data->warehouse_id  ;
                        $move->movement            =   'opening quantity';
                        $move->plus_qty            =  $data->quantity ;
                        $move->minus_qty           =  0;
                        $move->current_qty         =  $info->product_qty ;
                        
                        $move->product_id          =  $data->product_id;
                        $move->current_price       =  $data->price;
                        $move->opening_quantity_id =  $data->id;
                        $move->save();
                        $currency_details = $transactionUtil->purchaseCurrencyDetails($tr->business_id);
                        $productUtil->updateProductQuantity($location->id,$data->product_id,$variation_id->id 
                                                          ,$data->quantity,0, $currency_details);
                        $before = \App\Models\WarehouseInfo::qty_before($tr);
                        \App\Models\ItemMove::create_open($tr,0,$before,null,0,$pr->id); 
                        
                    }
                    
                    
                   
                }
            }
            if (!$is_valid) {
                $output = ['success' => 0,
                                'msg' => $error_msg
                            ];
                return response([
                        "status"  => 403 ,
                        "message" => $output["msg"]
                    ],403);
            }

            $output = ['success' => 1,
                            'msg' => __('product.file_imported_successfully')
                        ];

            \DB::commit();
            return $output["msg"];
        } catch (\Exception $e) {
            return false;
        }
    }
    // **10** LastProduct 
    public static function lastProduct($user,$data) {
        $business_id           = $user->business_id;
        $location              = \App\BusinessLocation::where("business_id",$business_id)->first();
        $check_enable_stock    = false;
        $only_variations       = false;
      
        $product = \App\Product::leftJoin('variations','products.id','=','variations.product_id')
                            ->active()
                            ->where('business_id', $business_id)
                            ->whereNull('variations.deleted_at')
                            ->select(
                                'products.id as product_id',
                                'products.unit_id as unit_id',
                                'products.sub_unit_ids as sub_unit',
                                'products.id as product_id',
                                'products.name',
                                'products.type',
                                'products.sku as sku',
                                'variations.id as variation_id',
                                'variations.name as variation',
                                'variations.sub_sku as sub_sku'
                            )
                            ->groupBy('variation_id')->orderBy('id','desc')->first();

        $products_array = [];
        $list_unit = []; $un = [];
        if($product->sub_unit != null){
            $list_price      = \App\Product::getProductPrices($product->product_id);
            $unit2           = \App\Unit::find($product->unit_id);
            $list_unit[]     = ["id" => $unit2->id ,"value" => $unit2->actual_name ,"unit_quantity"=>($unit2->base_unit_multiplier == null)?1:$unit2->base_unit_multiplier,"list_price"=> isset($list_price[$unit2->id])?$list_price[$unit2->id]:[]];
            $un []           = $unit2->id;
            $all             = json_decode( $product->sub_unit );
            foreach($all as $i){
                $unit            = \App\Unit::find($i);
                if(!in_array($i,$un)){
                    $list_unit[]   = ["id" => $i ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$i])?$list_price[$i]:[]];
                    $un [] =  $i;
                }
            }
            $products_array[$product->product_id]['all_units']              =  $list_unit ;
            
        }else{
            $unit                      = \App\Unit::find($product->unit_id);$list_price      = \App\Product::getProductPrices($product->product_id);
            $list_unit[]   = ["id" => $product->unit_id ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$product->unit])?$list_price[$product->unit]:[]];
            $products_array[$product->product_id]['all_units']              =  $list_unit ;
        }
        $products_array[$product->product_id]['name']         = $product->name;
        $products_array[$product->product_id]['sku']          = $product->sub_sku;
        $products_array[$product->product_id]['type']         = $product->type;
        $products_array[$product->product_id]['variations'][] = [ 'variation_id'  => $product->variation_id, 'variation_name' => $product->variation,'sub_sku' => $product->sub_sku ];
        $result  = []; $i = 1; $no_of_records = $products->count();
        if (!empty($products_array)) {
            foreach ($products_array as $key => $value) {
                $cost         = \App\Models\ItemMove::orderBy("date","desc")->orderBy("id","desc")->where("product_id",$key)->first();
                $stock_qty    = \App\Models\WarehouseInfo::where("product_id",$key)->sum("product_qty");
                if ($no_of_records > 1 && $value['type'] != 'single' && !$only_variations) {
                    $result[] = [   'id'           => $i,
                                    "open"         => "open",    
                                    'text'         => $value['name'] . ' - ' . $value['sku'],
                                    'variation_id' => 0,
                                    'product_id'   => $key,
                                    'type'         => $value['type'],
                                    'all_units'    => $value['all_units'],
                                    'cost'         => (!empty($cost))?$cost->unit_cost:0,
                                    'stock'        => $stock_qty
                            ];
                }
                $name = $value['name'];
                foreach ($value['variations'] as $variation) {
                    $text = $name;
                    if ($value['type'] == 'variable') {
                        $text = $text . ' (' . $variation['variation_name'] . ')';
                    }
                    $i++;
                    $result[] = [   'id'           => $i,
                                    "open"         => "open",
                                    "edit"         => null,
                                    'text'         => $text . ' - ' . $variation['sub_sku'],
                                    'product_id'   => $key ,
                                    'type'         => $value['type'] ,
                                    'all_units'    => $value['all_units'],
                                    'variation_id' => $variation['variation_id'],
                                    'cost'         => (!empty($cost))?$cost->unit_cost:0,
                                    'stock'        => $stock_qty

                                    ];
                }
                $i++;
            }
        }
        return json_encode($result);
    }


    // ****** MAIN FUNCTIONS 
    // **1** CREATE OPENING QUANTITY
    public static function createNewOpeningQuantity($user,$data) {
       try{  
                // *1* Main Function
                $transaction                             = GlobalUtil::transaction($data);
            foreach($data["items"] as $e){
                // *2* Second Function
                $sub_unit                                = \App\Unit::find($e['unit_id']); 
                $line                                    = GlobalUtil::purchase_line($e,$transaction);
                // *3* Third Function
                $OpeningQuantity                         = new \App\Models\OpeningQuantity();
                $OpeningQuantity->warehouse_id           = ($e["line_store"] != "")?$e["line_store"]:$data["store"];
                $OpeningQuantity->product_id             = $e["product_id"];
                $OpeningQuantity->business_location_id   = $data["business_id"];
                $OpeningQuantity->quantity               = $e["quantity"];
                $OpeningQuantity->price                  = $e["price"];
                $OpeningQuantity->transaction_id         = $transaction->id;
                $OpeningQuantity->purchase_line_id       = $line->id;
                $OpeningQuantity->product_unit           = ($sub_unit)?$sub_unit->id:null;
                $OpeningQuantity->product_unit_qty       = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;
                $OpeningQuantity->list_price             = ($e['list_price']!=null || $e['list_price']!="")?$e['list_price']:$transaction->list_price;
                $OpeningQuantity->date                   = \Carbon\Carbon::parse($data["date"]);
                $OpeningQuantity->save();
                // *4* FOURTH FUNCTION
                $Movement                                = GlobalUtil::storeItemMovement($OpeningQuantity,$transaction,$line,$sub_unit);
                if($Movement == false){return false;}
            }
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE OPENING QUANTITY
    public static function updateOldOpeningQuantity($user,$data,$id) {
       try{
            // *** 1 ** main function
            $transaction                      = GlobalUtil::updateTransaction($data,$id);
            // *** 2 ** second function
            $array_lines          = [];
            foreach($data["items"] as $item){
                if($item["line_id"] != ""){
                    $array_lines[] = $item["line_id"];
                }
            } 
            $purchase_line_not_in             = \App\PurchaseLine::whereNotIn("id",$array_lines)->where("transaction_id",$id)->get();
          
            foreach($purchase_line_not_in as $i){
                $openQ                        = \App\Models\OpeningQuantity::where("transaction_id",$id)->where("purchase_line_id",$i->id)->first();
                $itemMove                     = \App\Models\ItemMove::where("line_id",$i->id)->first();
                $warehouseMove                = \App\MovementWarehouse::where('opening_quantity_id',$openQ->id)->first();
                $multiple_product             = ($i->sub_unit_qty != null)?(($i->sub_unit_qty != 0)?$i->sub_unit_qty:1):1;
                $deleteQty                    = $multiple_product * $i->quantity;
                \App\Models\WarehouseInfo::update_stoct($i->product_id,$i->store_id,($deleteQty*-1),$transaction->business_id);
                $warehouseMove->delete();
                     $itemMove->delete();
                        $openQ->delete();
                            $i->delete();
            }
            foreach( $data["items"] as $ie){
                $old_list                                 = [];
                $old_qty                                  = null;
                $old_store                                = null;
                
                $sub_unit                                 = \App\Unit::find($ie['unit_id']);
                $purchase_line                            = GlobalUtil::updatePurchase_line($ie,$transaction,$id);
                // *** 3 ** third function
              
                if($ie["line_id"] != "" || $ie["line_id"] != null){
                    $openQuantity                         = \App\Models\OpeningQuantity::where("transaction_id",$id)->where("purchase_line_id",$ie["line_id"])->first();
                    $old_list['old_quantity']             = $openQuantity->quantity;
                    $old_list['old_unit_id']              = $openQuantity->product_unit;
                    $old_list['old_unit_qty']             = $openQuantity->product_unit_qty;
                    $old_list['new_unit_qty']             = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;
                    $old_qty                              = $openQuantity->quantity;
                    $old_store                            = $openQuantity->warehouse_id;
                    $openQuantity->warehouse_id           = ($ie["line_store"]!= "")?$ie["line_store"]:$data["store"];
                    $openQuantity->product_id             = $ie["product_id"];
                    $openQuantity->quantity               = $ie["quantity"];
                    $openQuantity->price                  = $ie["price"];
                    $openQuantity->date                   = $data["date"];
                    $openQuantity->product_unit           = ($sub_unit)?$sub_unit->id:null;
                    $openQuantity->product_unit_qty       = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;
                    $openQuantity->list_price             = ($ie['list_price']!=null || $ie['list_price']!="")?$ie['list_price']:$transaction->list_price;
                    $openQuantity->update();
                }else{
                    $old_list['new_unit_qty']             = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;
                    $openQuantity                         = new \App\Models\OpeningQuantity();
                    $openQuantity->warehouse_id           = ($ie["line_store"]!= "")?$ie["line_store"]:$data["store"];
                    $openQuantity->product_id             = $ie["product_id"];
                    $openQuantity->business_location_id   = $transaction->business_id;
                    $openQuantity->quantity               = $ie["quantity"];
                    $openQuantity->price                  = $ie["price"];
                    $openQuantity->transaction_id         = $transaction->id;
                    $openQuantity->purchase_line_id       = $purchase_line->id;
                    $openQuantity->date                   = $data["date"];
                    $openQuantity->product_unit           = ($sub_unit)?$sub_unit->id:null;
                    $openQuantity->product_unit_qty       = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;
                    $openQuantity->list_price             = ($ie['list_price']!=null || $ie['list_price']!="")?$ie['list_price']:$transaction->list_price;
                    $openQuantity->save();
                }
              
                // *** 4 ** fourth function
                $Movement                                 = GlobalUtil::updateStoreItemMovement($ie,$openQuantity,$transaction,$purchase_line,$old_qty,$old_store,$sub_unit,$old_list);
                if($Movement == false){return false;}
            }
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET  OPENING QUANTITY
    public static function allData($type=null,$id=null,$business_id,$filter=null) {
        try{
            $list   = [];
            if($type != null){
                $line_list          = []; $final_list         = [];
                $temp_list          = []; $list_array         = [];
                $transaction        = \App\Transaction::where("business_id",$business_id)->join("opening_quantities as op","op.transaction_id","transactions.id")
                                                        ->join("purchase_lines as pl","pl.transaction_id","transactions.id")                                        
                                                        ->where("type","opening_stock")
                                                        ->whereNull("op.deleted_at")
                                                        ->select([ 
                                                            "transactions.id as tran_id", 
                                                            "transactions.list_price as list_price_main", 
                                                            "transactions.ref_no as ref_no", 
                                                            "op.id as id",
                                                            "op.purchase_line_id as line_id",
                                                            "op.quantity as qty",
                                                            "op.price as price",
                                                            "op.product_unit as product_unit",
                                                            "op.product_unit_qty as product_unit_qty",
                                                            "op.list_price as list_price",
                                                            "op.date as date",
                                                            "op.product_id as product_id",
                                                            "op.warehouse_id as store_id",
                                                            "pl.order_id as order_id"
                                                        ])
                                                        ->orderBy("tran_id","desc");
                if($filter!=null){
                    if($filter["startDate"] != null){
                        $transaction->whereDate("date",">=",$filter["startDate"]);
                    }
                    if($filter["endDate"] != null){
                        $transaction->whereDate("date","<=",$filter["endDate"]);
                    }
                    if($filter["month"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["month"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["month"])->format('Y');
                        $startD  = $y."-".$m."-01";
                        
                        $transaction->whereDate("date","<=",$filter["month"]); 
                        $transaction->whereDate("date",">=",$startD); 
                    }
                    if($filter["day"] != null){
                        $transaction->whereDate("date","=",$filter["day"]); 
                    }
                    if($filter["year"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["year"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["year"])->format('Y');
                        $startD  = $y."-01-01";
                        
                        $transaction->whereDate("date","<=",$filter["year"]); 
                        $transaction->whereDate("date",">=",$startD); 
                    }
                    if($filter["week"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["week"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["week"])->format('Y');
                        $d = \Carbon::createFromFormat('Y-m-d',$filter["week"])->format('d');
                        // list of date with 31 or 30
                        $dayOf31 = [1,3,5,7,9,10,12] ;
                        $dayOf30 = [2,4,6,8,11] ;
                        // for day of week
                        $d = $d - 7;
                        if($d < 0){
                                if((intVal($m) - 1)<0){
                                $y = (intVal($y) - 1);
                                $m = abs((intVal($m) - 1)%12);
                                    
                                }elseif((intVal($m) - 1)==0){
                                $y = intVal($y)-1;
                                $m = (((intVal($m) - 1) % 12)==0)?12:abs((intVal($m) - 1) % 12);
                                    
                                }else{
                                $m =  (intVal($m) - 1);
                                    
                                }
                            if(in_array(intVal($m),$dayOf31)){
                                $d = 31 - abs($d);
                            }else{
                                if(intVal($m) == 2){
                                    // Leap Years 1800 - 2400
                                    $mod = substr($y,3)%4;
                                    $numberOfDay = ($mod == 0)?29:28;
                                    $d = $numberOfDay - abs($d);
                                }else{
                                    $d = 30 - abs($d);
                                }
                            }
                        }elseif($d == 0){
                            if((intVal($m) - 1)<0){
                                $y = (intVal($y) - 1);
                                $m = abs((intVal($m) - 1)%12);
                                }elseif((intVal($m) - 1)==0){
                                $y = intVal($y)-1;
                                $m = (((intVal($m) - 1) % 12)==0)?12:abs((intVal($m) - 1) % 12);
                                }else{
                                $m =  (intVal($m) - 1);
                                }
                            if(in_array(intVal($m),$dayOf31)){
                                $d = 31;
                            }else{ 
                                    if(intVal($m) == 2){
                                        // Leap Years 1800 - 2400
                                    $mod = substr($y,3)%4;
                                    $numberOfDay = ($mod == 0)?29:28;
                                    $d = $numberOfDay - abs($d);
                                }else{
                                    $d = 30 - abs($d);
                                }
                            }
                        } 
                        $startD  = $y."-".$m."-".$d;
                        
                        $transaction->whereDate("date","<=",$filter["week"]); 
                        $transaction->whereDate("date",">=",$startD); 
                    }
                    $transaction     = $transaction->get();
                }else{
                    $transaction     = $transaction->get();
                }
                
                foreach($transaction as $ie){
                    $PRO = \App\Product::find($ie->product_id);
                    $STR = \App\Models\Warehouse::find($ie->store_id);
                    $list_price      = \App\Product::getProductPrices($ie->product_id);
                    $unit2           = \App\Unit::find($PRO->unit_id);
                    $list_unit[]     = ["id" => $unit2->id ,"value" => $unit2->actual_name ,"unit_quantity"=>($unit2->base_unit_multiplier == null)?1:$unit2->base_unit_multiplier,"list_price"=> isset($list_price[$unit2->id])?$list_price[$unit2->id]:[]];
                    $un []           = $unit2->id;
                    $all             = $PRO->sub_unit_ids ;
                    if($all != null){
                        foreach($all as $i){
                            $unit            = \App\Unit::find($i);
                            if(!in_array($i,$un)){
                                $list_unit[]   = ["id" => $i ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$i])?$list_price[$i]:[]];
                                $un [] =  $i;
                            }
                        }
                    }
                    if(!in_array($ie->tran_id,$list_array)){
                          
                        $list[$ie->tran_id][] = [
                            "tran_id"            => $ie->tran_id,
                            "ref_no"             => $ie->ref_no,
                            "id"                 => $ie->line_id,
                            "qty"                => $ie->qty,
                            "price"              => $ie->price,
                            "date"               => $ie->date,
                            "productName"        => ($PRO)?$PRO->name:$ie->product_id,
                            "product_id"         => $ie->product_id,
                            "storeName"          => ($STR)?$STR->name:$ie->store_id,
                            "store_id"           => $ie->store_id,
                            "list_price"         => $ie->list_price,
                            "product_unit_id"    => ($ie->product_unit != null)?$ie->product_unit:(($PRO)?$PRO->unit->id:null),
                            "product_unit_qty"   => ($ie->product_unit_qty != null)?$ie->product_unit_qty:(($PRO)?(($PRO->unit->base_unit_multiplier!=null)?$PRO->unit->base_unit_multiplier:1):1),
                            "all_units"          => $list_unit,
                            "order_id"           => $ie->order_id,
                        ];
                        array_push($list_array,$ie->tran_id);
                    }else{ 
                        $array   = [
                            "tran_id"            => $ie->tran_id,
                            "ref_no"             => $ie->ref_no,
                            "id"                 => $ie->line_id,
                            "qty"                => $ie->qty,
                            "price"              => $ie->price,
                            "date"               => $ie->date,
                            "productName"        => ($PRO)?$PRO->name:$ie->product_id,
                            "product_id"         => $ie->product_id,
                            "storeName"          => ($STR)?$STR->name:$ie->store_id,
                            "store_id"           => $ie->store_id,
                            "list_price"         => $ie->list_price,
                            "product_unit_id"    => ($ie->product_unit != null)?$ie->product_unit:(($PRO)?$PRO->unit->id:null),
                            "product_unit_qty"   => ($ie->product_unit_qty != null)?$ie->product_unit_qty:(($PRO)?(($PRO->unit->base_unit_multiplier!=null)?$PRO->unit->base_unit_multiplier:1):1),
                            "all_units"          => $list_unit,
                            "order_id"           => $ie->order_id,
                        ];
                        array_push($list[$ie->tran_id],$array);
                    }
                }
                foreach($list as $key   => $item){
                    $trans                = \App\Transaction::select("ref_no","transaction_date")->where("id",$key)->first();
                    $line_list["ref_no"]  = $trans->ref_no ;   
                    $line_list["date"]    = \Carbon::createFromFormat('Y-m-d h:s:i', $trans->transaction_date)->format("Y-m-d");  
                    $line_list["tran_id"] = $key ;   
                    foreach($item as $ky  => $lin){ if($lin["tran_id"] == $key){ unset($lin["tran_id"]) ;unset($lin["ref_no"]) ;$temp_list []   = $lin; }}
                    $line_list["items"]   = $temp_list;   
                    $final_list[]         = $line_list;
                    $temp_list            = [];
                } 
                $list = $final_list;
            }else{
                $transaction             = \App\Transaction::find($id);
                $openingQ                = \App\Models\OpeningQuantity::join('purchase_lines as pl','pl.id','opening_quantities.purchase_line_id')
                                                                        ->where("opening_quantities.transaction_id",$id)
                                                                        ->select([
                                                                            "opening_quantities.product_id as product_id",    
                                                                            "opening_quantities.purchase_line_id as purchase_line_id",    
                                                                            "opening_quantities.warehouse_id as warehouse_id",    
                                                                            "opening_quantities.quantity as quantity",    
                                                                            "opening_quantities.price as price",    
                                                                            "opening_quantities.date as date",    
                                                                            "opening_quantities.list_price as list_price",    
                                                                            "opening_quantities.warehouse_id as warehouse_id",    
                                                                            "opening_quantities.product_unit as product_unit",    
                                                                            "opening_quantities.product_unit_qty as product_unit_qty",    
                                                                        ])
                                                                        ->orderBy("pl.order_id","asc")
                                                                        ->get();
                $li                      = [];
                foreach($openingQ as $it){
                    $list_unit   = [];$un   = [];
                    $PRO         = \App\Product::find($it->product_id);
                    $pl          = \App\PurchaseLine::find($it->purchase_line_id);
                    $STR         = \App\Models\Warehouse::find($it->warehouse_id);
                    $list_price  = \App\Product::getProductPrices($it->product_id);
                    $id_unit     = ($it->product_unit != null)?$it->product_unit:(($PRO)?$PRO->unit->id:null);
                    $unit2       = \App\Unit::find($PRO->unit_id);
                    $list_unit[] = ["id" => $unit2->id ,"value" => $unit2->actual_name ,"unit_quantity"=>($unit2->base_unit_multiplier == null)?1:$unit2->base_unit_multiplier,"list_price"=> isset($list_price[$unit2->id])?$list_price[$unit2->id]:[]];
                
                    $un []       = $unit2->id;
                    $all         = $PRO->sub_unit_ids ;
                    if($all != null){
                        foreach($all as $i){
                            $unit            = \App\Unit::find($i);
                            if(!in_array($i,$un)){
                                $list_unit[]   = ["id" => $i ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier,"list_price"=> isset($list_price[$i])?$list_price[$i]:[]];
                                $un [] =  $i;
                            }
                        }
                    }
                    $li[]        = [
                        "id"                 => $it->purchase_line_id,
                        "store_id"           => $it->warehouse_id,
                        "storeName"          => ($STR)?$STR->name:$it->warehouse_id,
                        "product_id"         => $it->product_id,
                        "productName"        => ($PRO)?$PRO->name:$it->product_id,
                        "quantity"           => $it->quantity,
                        "price"              => $it->price,
                        "date"               => $it->date,
                        "list_price"         => $it->list_price,
                        "list_unit_price"    => isset($list_price[$id_unit])?$list_price[$id_unit]:[],
                        "product_unit_id"    => ($it->product_unit != null)?$it->product_unit:(($PRO)?$PRO->unit->id:null),
                        "product_unit_qty"   => ($it->product_unit_qty != null)?$it->product_unit_qty:(($PRO)?(($PRO->unit->base_unit_multiplier!=null)?$PRO->unit->base_unit_multiplier:1):1),
                        "line_id"            => $it->purchase_line_id,
                        "all_units"          => $list_unit,
                        "order_id"           => $pl->order_id,
                        
                    ];
                }
                $list[]=[
                    "ref_no"     => $transaction->ref_no, 
                    "list_price" => $transaction->list_price, 
                    "date"       => \Carbon::createFromFormat('Y-m-d h:s:i', $transaction->transaction_date)->format("Y-m-d")  ,  
                    "store"      => [
                            "id"     => $transaction->store,
                            "value"  => $transaction->warehouse->name
                    ],
                    "items"      => $li 
                ];
            }
            return $list; 
        }catch(Exception    $e){
            return false;
        }
    }
    // **4** GET REQUIRE DATA
    public static function requirement($user) {
        try{
            $list                   = [];   
            $allData                = [];  
            $line_prices            = [];#2024-8-6
            $stores                 = GlobalUtil::stores();
            $row                    = 1;
            $list_of_prices         = \App\Product::getListPrices($row);
            $allData["prices"]      = GlobalUtil::arrayToObject($list_of_prices);
            $allData["store"]       = $stores;
            return $allData; 
        }catch(Exception $e){
            return false; 
        }
    }
}
