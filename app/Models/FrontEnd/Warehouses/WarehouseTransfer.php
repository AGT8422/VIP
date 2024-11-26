<?php

namespace App\Models\FrontEnd\Warehouses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\TransactionUtil;
use App\Utils\ProductUtil;
use App\Models\FrontEnd\Utils\GlobalUtil;

class WarehouseTransfer extends Model
{
    use HasFactory;
    // *** REACT FRONT-END WAREHOUSE TRANSFER *** // 
    // **1** ALL WAREHOUSE TRANSFER
    public static function getWarehouseTransfer($user) {
        try{
            $business_id   = $user->business_id;
            $data          = WarehouseTransfer::allData("all",null,$business_id);
            if($data == false){ return false;}
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE WAREHOUSE TRANSFER
    public static function createWarehouseTransfer($user,$data) {
        try{
            $business_id             = $user->business_id;
            $create                  = WarehouseTransfer::requirement($user);
            return $create;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT WAREHOUSE TRANSFER
    public static function editWarehouseTransfer($user,$data,$id) {
        try{
            $business_id             = $user->business_id;
            $data                    = WarehouseTransfer::allData(null,$id,$business_id);
            $edit                    = WarehouseTransfer::requirement($user);
            if($data  == false){ return false; }
            $list["info"]            = $data;
            $list["require"]         = $edit;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE WAREHOUSE TRANSFER
    public static function storeWarehouseTransfer($user,$data,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = WarehouseTransfer::createNewWarehouseTransfer($user,$data,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE WAREHOUSE TRANSFER
    public static function updateWarehouseTransfer($user,$data,$id,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = WarehouseTransfer::updateOldWarehouseTransfer($user,$data,$id,$request);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE WAREHOUSE TRANSFER
    public static function deleteWarehouseTransfer($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
           
            //Get purchase transfer transaction
            $purchase_transfer = Transaction::where('id', $id)->where('type', 'Stock_In')->with(['purchase_lines'])->first();

            //Get sell transfer transaction
            $sell_transfer     = Transaction::where('ref_no', $purchase_transfer->ref_no)->where('type', 'Stock_Out')->with(['sell_lines'])->first();
            $move_purchase     = \App\MovementWarehouse::where("business_id",$purchase_transfer->business_id)->where("transaction_id",$purchase_transfer->id)->get();
            $move_sell         = \App\MovementWarehouse::where("business_id",$purchase_transfer->business_id)->where("transaction_id",$sell_transfer->id)->get();
            foreach($move_purchase as $it){
                    $it->delete();
            }
            foreach($move_sell as $it){
                    $it->delete();
            }

            \App\Models\ItemMove::delete_transafer($sell_transfer,$purchase_transfer);

            $ids_pur = $purchase_transfer->purchase_lines->pluck("id"); 
            $ids_sel = $sell_transfer->sell_lines->pluck("id"); 

            foreach($ids_pur as $it){
                $purchase = \App\PurchaseLine::find($it);
                if(!empty($purchase)){
                        //.... decrement from purchase store 
                        \App\Models\WarehouseInfo::update_stoct($purchase->product_id,$purchase->store_id,($purchase->quantity*-1),$purchase_transfer->business_id);
                }
            }
            foreach($ids_sel as $it){
                $sell = \App\TransactionSellLine::find($it);
                if(!empty($sell)){
                    //.... increment from Sale store 
                    \App\Models\WarehouseInfo::update_stoct($sell->product_id,$sell->store_id,($sell->quantity),$purchase_transfer->business_id);
                }
            }

            //Check if any transfer stock is deleted and delete purchase lines
            $purchase_lines = $purchase_transfer->purchase_lines;
            foreach ($purchase_lines as $purchase_line) {
                if ($purchase_line->quantity_sold > 0) {
                    return  false;
                }
            }

            //Get purchase lines from transaction_sell_lines_purchase_lines and decrease quantity_sold
            $sell_lines                = $sell_transfer->sell_lines;
            $deleted_sell_purchase_ids = [];
            $products                  = []; //variation_id as array

            foreach ($sell_lines as $sell_line) {
                $purchase_sell_line = \App\TransactionSellLinesPurchaseLines::where('sell_line_id', $sell_line->id)->first();
                if (!empty($purchase_sell_line)) {
                    //Decrease quantity sold from purchase line
                    \App\PurchaseLine::where('id', $purchase_sell_line->purchase_line_id)->decrement('quantity_sold', $sell_line->quantity);
                    $deleted_sell_purchase_ids[] = $purchase_sell_line->id;
                    //variation details
                    if (isset($products[$sell_line->variation_id])) {
                        $products[$sell_line->variation_id]['quantity']  += $sell_line->quantity;
                        $products[$sell_line->variation_id]['product_id'] = $sell_line->product_id;
                    } else {
                        $products[$sell_line->variation_id]['quantity']   = $sell_line->quantity;
                        $products[$sell_line->variation_id]['product_id'] = $sell_line->product_id;
                    }
                }
            }

            //Update quantity available in both location
            if (!empty($products)) {
                foreach ($products as $key => $value) {

                    //Decrease from location 2
                    $productUtil->decreaseProductQuantity(
                        $products[$key]['product_id'],
                        $key,
                        $purchase_transfer->location_id,
                        $products[$key]['quantity']
                    );
                    //Increase in location 1
                    $productUtil->updateProductQuantity(
                        $sell_transfer->location_id,
                        $products[$key]['product_id'],
                        $key,
                        $products[$key]['quantity']
                    );

                    
                }
            }

            //Delete sale line purchase line
            if (!empty($deleted_sell_purchase_ids)) {
                \App\TransactionSellLinesPurchaseLines::whereIn('id', $deleted_sell_purchase_ids)->delete();
            }

            //Delete both transactions
            $sell_transfer->delete();
            $purchase_transfer->delete();

                 
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE WAREHOUSE TRANSFER
    public static function createNewWarehouseTransfer($user,$data,$request) {
       try{
            $transactionUtil                = new TransactionUtil();  
            $productUtil                    = new ProductUtil();
            $business_id                    = $user->business_id;
            $trans                          = \App\BusinessLocation::where("business_id",$business_id)->first();
            //Check if subscribed or not
            // if (!$this->moduleUtil->isSubscribed($business_id)) {
            //     return $this->moduleUtil->expiredResponse(action('StockTransferController@index'));
            // }
            $input_data                     = $request->only([ 'location_id', 'ref_no', 'transaction_date', 'additional_notes', 'shipping_charges', 'final_total']);
            $status                         = $request->input('status');
            $user_id                        = $user->id;
            $input_data['final_total']      = $productUtil->num_uf($input_data['final_total']);
            $input_data['total_before_tax'] = $input_data['final_total'];
            $input_data['type']             = 'Stock_Out';
            $input_data['business_id']      = $business_id;
            $input_data['created_by']       = $user_id;
            $input_data['transaction_date'] = $productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['shipping_charges'] = 0;
            $input_data['payment_status']   = 'paid';
            $input_data['status']           = $status == 'completed' ? 'final' : $status;
            $input_data['store']            = $request->location_id; 
            $input_data["location_id"]      = $trans->id;
            //Update reference count
            $ref_count                = $productUtil->setAndGetReferenceCount('stock_transfer',$business_id);
            //Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $productUtil->generateReferenceNumber('stock_transfer', $ref_count,$business_id);
            }
            $products                 = $request->input('products');
            $sell_lines               = [];
            $purchase_lines           = [];

            if (!empty($products)) {
                foreach ($products as $product) {
                    $sell_line_arr = [
                                'product_id'   => $product['product_id'],
                                'variation_id' => $product['variation_id'],
                                'quantity'     => $productUtil->num_uf($product['quantity']),
                                'store_id'     => $request->transfer_location_id,
                                'item_tax'     => 0,
                                'tax_id'       => null
                    ];

                    $purchase_line_arr                   = $sell_line_arr;
                    $pr                                  = \App\Variation::find($product['variation_id']);
                   // $sell_line_arr['unit_price'] = $this->productUtil->num_uf($product['unit_price']);
                    $sell_line_arr['unit_price']         = isset($pr->default_purchase_price)?$pr->default_purchase_price:1 ;
                    $sell_line_arr['unit_price_inc_tax'] = isset($pr->dpp_inc_tax)?$pr->dpp_inc_tax:1 ;
                    $purchase_line_arr['purchase_price'] = $sell_line_arr['unit_price'];
                    $purchase_line_arr['purchase_price_inc_tax'] = $sell_line_arr['unit_price'];
                    if (!empty($product['lot_no_line_id'])) {
                        //Add lot_no_line_id to sell line
                        $sell_line_arr['lot_no_line_id'] = $product['lot_no_line_id'];
                        //Copy lot number and expiry date to purchase line
                        $lot_details                     = \App\PurchaseLine::find($product['lot_no_line_id']);
                        $purchase_line_arr['lot_number'] = $lot_details->lot_number;
                        $purchase_line_arr['mfg_date']   = $lot_details->mfg_date;
                        $purchase_line_arr['exp_date']   = $lot_details->exp_date;
                    }
                    $sell_lines[]     = $sell_line_arr;
                    $purchase_lines[] = $purchase_line_arr;
                }
            }
             
            //Create Sell Transfer transaction
            $sell_transfer = \App\Transaction::create($input_data);

            //Create Purchase Transfer at transfer location
            $input_data['type']               = 'Stock_In';
            $input_data['location_id']        =  $trans->id;
            $input_data['transfer_parent_id'] =  $request->transfer_location_id;
            $input_data['status']             =  $status == 'completed' ? 'completed' : $status;

            $purchase_transfer = \App\Transaction::create($input_data);
            //Sell Product from first location
            if (!empty($sell_lines)) {
                $transactionUtil->createOrUpdateSellLines($sell_transfer, $sell_lines, $input_data['location_id'],null,null,[],null,null,null,$request);
            }
            
            //Purchase product in second location
            if (!empty($purchase_lines)) {
                $purchase_transfer->purchase_lines()->createMany($purchase_lines);
            }
            
            //Decrease product stock from sell location
            //And increase product stock at purchase location
            if ($status == 'completed') {
                foreach ($products as $product) {
                    if ($product['enable_stock']) {
                        
                        //**.............. EB
                        \App\Models\WarehouseInfo::transferfromTo($product["product_id"],$request->location_id,$request->transfer_location_id,$product["quantity"],$business_id);
                       
                        $productUtil->decreaseProductQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $trans->id,
                            $product['quantity'] 
                        );

                        $productUtil->updateProductQuantity(
                            $trans->id,
                            $product['product_id'],
                            $product['variation_id'],
                            $product['quantity']
                        );
                        \App\MovementWarehouse::store_moves($request->location_id,$request->transfer_location_id,$product["product_id"],$product["quantity"],$sell_transfer,$purchase_transfer);

                    }
                }
                \App\Models\ItemMove::transfer($purchase_transfer,$sell_transfer,0,0);

                //Adjust stock over selling if found
                $productUtil->adjustStockOverSelling($purchase_transfer);

                //Map sell lines with purchase lines
                // $business = [
                //             'id'                => $business_id,
                //             'accounting_method' => $request->session()->get('business.accounting_method'),
                //             'location_id'       => $sell_transfer->location_id
                //         ];
                // $this->transactionUtil->mapPurchaseSell($business, $sell_transfer->sell_lines, 'purchase');
            }

            $transactionUtil->activityLog($sell_transfer, 'added');

             
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE WAREHOUSE TRANSFER
    public static function updateOldWarehouseTransfer($user,$data,$id,$request) {
       try{
            $transactionUtil      = new TransactionUtil();  
            $productUtil          = new ProductUtil();
            $business_id          = $user->business_id;
            //Check if subscribed or not
            // if (!$this->moduleUtil->isSubscribed($business_id)) {
            //     return $this->moduleUtil->expiredResponse(action('StockTransferController@index'));
            // }
            $location_id          = \App\BusinessLocation::where('business_id',$business_id)->first()->id;
            $sell_transfer        = \App\Transaction::where('business_id', $business_id)
                                                ->where('type', 'Stock_Out')
                                                ->findOrFail($id);

            $sell_transfer_before = $sell_transfer->replicate();
            $purchase_transfer    = \App\Transaction::where('business_id', 
                                                    $business_id)
                                                ->where('ref_no', $sell_transfer->ref_no)
                                                ->where('type', 'Stock_In')
                                                ->with(['purchase_lines'])
                                                ->first();

            $status                         = $request->input('status');
            $input_data                     = $request->only(['transaction_date', 'additional_notes', 'shipping_charges', 'final_total']);
            $status                         = ($request->input('status') == "")? "completed" : $request->input('status');
            $input_data['final_total']      = $productUtil->num_uf($input_data['final_total']);
            $input_data['total_before_tax'] = $input_data['final_total'];
            $input_data['transaction_date'] = $productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['shipping_charges'] = NULL;
            $input_data['shipping_charges'] = NULL;
            $input_data['status'] = $status == 'completed' ? 'final' : $status;

            $products              = $request->input('products');
            $sell_lines            = [];$purchase_lines        = [];
            $edited_purchase_lines = [];
     
            if (!empty($products)) {
                $ids = [] ;
                foreach($products as $it){
                    if(isset($it["transaction_sell_lines_id"])){
                        $ids[] = $it["transaction_sell_lines_id"];
                    }
                }
                $idp = [] ;
                foreach($products as $it){
                    if(isset($it["number"])){
                        $idp[] = $it["number"];
                    }
                }
                $tr_selline    = \App\TransactionSellLine::where("transaction_id",$id)->whereNotIn("id",$ids)->get();
                $ids_delete    = $tr_selline->pluck("id");
                $tr_purchase   = \App\PurchaseLine::where("transaction_id",$purchase_transfer->id)->whereNotIn("id",$idp)->get();
                $id_pur_delete = $tr_purchase->pluck("id");
              
                if(count($ids_delete)>0){
                    foreach($ids_delete as $key => $it){
                        $purchase = \App\PurchaseLine::find($id_pur_delete[$key]);
                        $line     = \App\TransactionSellLine::find($it);
                        \App\Models\WarehouseInfo::update_stoct($line->product_id,$purchase->store_id,$line->quantity*-1,$business_id);
                        \App\Models\WarehouseInfo::update_stoct($line->product_id,$line->store_id,$line->quantity,$business_id);

                        $delete_move_pur = \App\MovementWarehouse::where("purchase_line_id",$purchase->id)->first(); 
                        $delete_move_sal = \App\MovementWarehouse::where("transaction_sell_line_id",$line->id)->first();
                        
                        $delete_item_move_pur = \App\Models\ItemMove::where("purchase_line_id",$purchase->id)->first(); 
                        $delete_item_move_sal = \App\Models\ItemMove::where("sells_line_id",$line->id)->first();
                        
                        if(!empty($delete_item_move_pur)){
                            $delete_item_move_pur->delete();
                        }
                        if(!empty($delete_item_move_sal)){
                            $delete_item_move_sal->delete();
                        }
                        if(!empty($delete_move_pur)){
                            $delete_move_pur->delete();
                        }
                        if(!empty($delete_move_sal)){
                            $delete_move_sal->delete();
                        }
                     }
                }
                //Decrease product stock from sell location
                //And increase product stock at purchase location
                if ($status == 'completed' && $purchase_transfer->status != "completed") {  
                    foreach ($products as $product) {   
                        if ($product['enable_stock']) {
                            //**.............. EB
                            \App\Models\WarehouseInfo::transferfromTo($product["product_id"],$request->location_id,$request->transfer_location_id,$product["quantity"],$business_id);
                            $productUtil->decreaseProductQuantity(
                                $product['product_id'],
                                $product['variation_id'],
                                $location_id,
                                $product['quantity'] 
                            );
                            $productUtil->updateProductQuantity(
                                $location_id,
                                $product['product_id'],
                                $product['variation_id'],
                                $product['quantity']
                            );
                            \App\MovementWarehouse::store_moves($request->location_id,$request->transfer_location_id,$product["product_id"],$product["quantity"],$sell_transfer,$purchase_transfer);
                        }
                    }
                
                }else if($status != 'completed' && $purchase_transfer->status == "completed") {
                    foreach ($products as $product) {
                        if ($product['enable_stock']) {
                            //**.............. EB
                            \App\Models\WarehouseInfo::transferfromTo($product["product_id"],$request->transfer_location_id,$request->location_id,$product["quantity"],$business_id);
                            $productUtil->updateProductQuantity(
                                $location_id,
                                $product['product_id'],
                                $product['variation_id'],
                                $product['quantity']
                            );
                            $productUtil->decreaseProductQuantity(
                                $product['product_id'],
                                $product['variation_id'],
                                $location_id,
                                $product['quantity'] 
                            );
                            \App\Models\MovementWarehouse::store_moves($request->transfer_location_id,$request->location_id,$product["product_id"],$product["quantity"],$sell_transfer,$purchase_transfer);
                        }
                    }
                }else if(($status == 'completed' && $purchase_transfer->status == "completed")){
                    foreach ($products as $product) {
                        if($request->transfer_location_id != $purchase_transfer->transfer_parent_id){
                            $purchase = \App\PurchaseLine::where("transaction_id",$purchase_transfer->id)->where("product_id",$product['product_id'])->first();
                            $margin   = $product["quantity"];
                            if($request->transfer_location_id != $sell_transfer->store){
                                //..... here 
                                if(!empty($purchase)){
                                    $margin      = $purchase->quantity - $product["quantity"];
                                    if($margin > 0 ){
                                        //... here 
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($purchase->quantity*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$request->transfer_location_id,($product["quantity"]),$business_id);
                                    }elseif($margin < 0 ){
                                        ///....here 
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($purchase->quantity*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);                                   
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$request->transfer_location_id,($product["quantity"]),$business_id);
                                    }else{
                                        // ..here 
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($purchase->quantity*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$request->transfer_location_id,($product["quantity"]),$business_id);
                                    } 
                                } 
                            }else{
                                //.. here 
                                if(!empty($purchase)){
                                    $margin      = $purchase->quantity - $product["quantity"];
                                    if($margin > 0 ){
                                        //.....here
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($purchase->quantity*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);                                   
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$request->transfer_location_id,($product["quantity"]),$business_id);
                                    }elseif($margin < 0 ){
                                        //.... here
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($purchase->quantity*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);                                   
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$request->transfer_location_id,($product["quantity"]),$business_id);
                                    }else{
                                        ///....here 
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($product["quantity"]*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($product["quantity"]),$business_id);
                                    } 
                                } 
                            }
                        }else{
                            //.... here 
                            $purchase = \App\PurchaseLine::where("transaction_id",$purchase_transfer->id)->where("product_id",$product['product_id'])->first();
                             
                            $margin   = $product["quantity"];
                            if(!empty($purchase)){
                                $margin      = $purchase->quantity - $product["quantity"];
                                if($margin > 0 ){
                                    //..... here 
                                    \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($margin*-1),$business_id);
                                    \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);
                                    
                                }elseif($margin < 0 ){
                                    //.... here
                                    \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($margin*-1),$business_id);                                   
                                    \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);
                                }
                            }else{
                                $margin      = $product["quantity"];
                                //.... here
                                \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,$margin,$business_id);                                   
                                \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,$margin*-1,$business_id);
                            }
                                
                        }
                    }
                } 
                foreach ($products as $product) {
                    $sell_line_arr = [
                                'product_id'   => $product['product_id'],
                                'variation_id' => $product['variation_id'],
                                'quantity'     => $productUtil->num_uf($product['quantity']),
                                'item_tax'     => 0,
                                'tax_id'       => null
                    ];

                    $purchase_line_arr = $sell_line_arr;

                    $sell_line_arr['unit_price']                 = (isset($product['unit_price']))?$this->productUtil->num_uf($product['unit_price']):0;
                    $sell_line_arr['unit_price_inc_tax']         = $sell_line_arr['unit_price'];
        
                    $purchase_line_arr['purchase_price']         = $sell_line_arr['unit_price'];
                    $purchase_line_arr['store_id']               = $request->transfer_location_id;
                    $purchase_line_arr['purchase_price_inc_tax'] = $sell_line_arr['unit_price'];
                    if (isset($product['transaction_sell_lines_id'])) {
                        $sell_line_arr['transaction_sell_lines_id'] = $product['transaction_sell_lines_id'];
                    }

                    if (!empty($product['lot_no_line_id'])) {
                        //Add lot_no_line_id to sell line
                        $sell_line_arr['lot_no_line_id'] = $product['lot_no_line_id'];

                        //Copy lot number and expiry date to purchase line
                        $lot_details                     = \App\PurchaseLine::find($product['lot_no_line_id']);
                        $purchase_line_arr['lot_number'] = $lot_details->lot_number;
                        $purchase_line_arr['mfg_date']   = $lot_details->mfg_date;
                        $purchase_line_arr['exp_date']   = $lot_details->exp_date;
                    }

                    $sell_lines[]  = $sell_line_arr;
                    $purchase_line = [];
                    //check if purchase_line for the variation exists else create new 
                    foreach ($purchase_transfer->purchase_lines as $pl) {
                        if ($pl->variation_id == $purchase_line_arr['variation_id']) {
                            $pl->update($purchase_line_arr);
                            $edited_purchase_lines[] = $pl->id;
                            $purchase_line = $pl;
                            break;
                        }
                    }
                    if (empty($purchase_line)) {
                        $purchase_line = new \App\PurchaseLine($purchase_line_arr);
                    }
                    $purchase_lines[]  = $purchase_line;
                }
            }

            //Create Sell Transfer transaction
            $sell_transfer->update($input_data);
            $sell_transfer->save();
            
            //Create Purchase Transfer at transfer location
            $input_data['status']             = $status == 'completed' ? 'completed' : $status;
            $input_data['transfer_parent_id'] = $request->transfer_location_id;

            $purchase_transfer->update($input_data);
            $purchase_transfer->save();

            //Sell Product from first location
            if (!empty($sell_lines)) {
                $transactionUtil->createOrUpdateSellLines($sell_transfer, $sell_lines, $sell_transfer->location_id, false, 'draft',[],null,null,null,$request);
            }
 
            //Purchase product in second location
            if (!empty($purchase_lines)) {
                if (!empty($edited_purchase_lines)) {
                    \App\PurchaseLine::where('transaction_id', $purchase_transfer->id)
                                        ->whereNotIn('id', $edited_purchase_lines)
                                        ->delete();
                }
                $purchase_transfer->purchase_lines()->saveMany($purchase_lines);
            }

            \App\MovementWarehouse::update_move_transafer($sell_transfer->store,$purchase_transfer->transfer_parent_id,$sell_transfer,$purchase_transfer);

            \App\Models\ItemMove::transfer($purchase_transfer,$sell_transfer,0,0);

            $transactionUtil->activityLog($sell_transfer, 'edited', $sell_transfer_before);

            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET  WAREHOUSE TRANSFER
    public static function allData($type=null,$id=null,$business_id) {
        try{
            $list     = [];
            if($type != null){
                $stockIn     = \App\Transaction::OrderBy('id','desc')->where('business_id',$business_id)->where('type','Stock_In')->get();
                if(count($stockIn) == 0 ){ return false; }
                foreach($stockIn as $ie){
                    $FromWarehouse = \App\Models\Warehouse::find($ie->store);
                    $ToWarehouse   = \App\Models\Warehouse::find($ie->transfer_parent_id);
                       
                    $list[] = [
                        "id"               => $ie->id,
                        "reference"        => $ie->ref_no,
                        "from_store"       => $FromWarehouse->name,
                        "to_store"         => $ToWarehouse->name,
                        "final_total"      => $ie->purchase_lines->sum('quantity'),
                        "shipping_charges" => $ie->shipping_charges,
                        "status"           => $ie->status == 'final' ? 'completed' : $ie->status ,
                    ];
                }
            }else{
                $stockIn  = \App\Transaction::find($id);
                if(empty($stockIn)){ return false; }
                $FromWarehouse = \App\Models\Warehouse::find($stockIn->store);
                $ToWarehouse   = \App\Models\Warehouse::find($stockIn->transfer_parent_id);
                   
                $list[] = [
                    "id"               => $stockIn->id,
                    "reference"        => $stockIn->ref_no,
                    "from_store"       => $FromWarehouse->name,
                    "to_store"         => $ToWarehouse->name,
                    "final_total"      => $stockIn->purchase_lines->sum('quantity'),
                    "shipping_charges" => $stockIn->shipping_charges,
                    "status"           => $stockIn->status == 'final' ? 'completed' : $stockIn->status ,
                ];
                
            }
            return $list; 
        }catch(Exception    $e){
            return false;
        }
    }
    // **4** REQUIRE  WAREHOUSE TRANSFER
    public static function requirement($user) {
        try{
            $allData              = []; 
            $warehouseData        = []; 
            $status               = [
                "pending"    => "Pending",
                "in_transit" => "In Transit",
                "completed"  => "Completed"
            ];
            $ware   = \App\Models\Warehouse::whereNotNull("parent_id")->get();
            foreach($ware as $i){
                $warehouseData[$i->id] = $i->name;
            }
            $allData["status"]    = $status;
            $allData["stores"]    = GlobalUtil::arrayToObject($warehouseData);
            return $allData; 
        }catch(Exception $e){
           return false; 
        }
    }

}
