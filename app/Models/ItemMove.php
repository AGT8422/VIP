<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\TransactionUtil;

class ItemMove extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * **************************************************************************** *
     * 1  Finish......... . . . .  create purchase -bill . . . . .........      /   *
     *          ................. *********************** .................         *
     * **************************************************************************** *
     */
    public static function create_itemMove($source,$expense,$before,$after=null,$dis)
    { 

        DB::beginTransaction();
        $purchase       = \App\PurchaseLine::where("transaction_id",$source->id)->get();
        $receive        = \App\Models\RecievedPrevious::where("transaction_id",$source->id)->get();
        $total_purchase = \App\PurchaseLine::where("transaction_id",$source->id)->select(DB::raw("SUM(quantity*purchase_price) as total_price"))->first(); 
        $account        = \App\Account::where("contact_id",$source->contact_id)->first();
        if(count($purchase)>0){
            foreach($purchase as $key => $pli){
                //...... for second price in item movement
                if($total_purchase->total_price != 0){
                    $percent      = ($expense - $dis) / $total_purchase->total_price;
                    $additional   = $pli->purchase_price * $percent ;
                    $cost_inc_exp = $pli->purchase_price + $additional ;
                }else{
                    $cost_inc_exp = $pli->purchase_price;
                }
               
                // *** ADD PURCHASE RECEIVED STRAIT
                ItemMove::saveItemMove("purchase",$account,NULL,$source,$pli,$cost_inc_exp,$pli->id,$receive[$key]);
                // **********************************
                DB::commit();
            }
        }  
         
    }
    /**
     * **************************************************************************** *
     *  2 Finish......... . . . .  update purchase -bill . . . . .........     /    *
     *          ................. *********************** .................         *
     * **************************************************************************** *
     */
    public static function update_itemMove($source,$expense,$before,$after=null,$dis)
    { 
        DB::beginTransaction();
        // *1* FOR INITIALIZE DATA 
        // *** AGT8422
            $receive            = \App\Models\RecievedPrevious::where("transaction_id",$source->id)->get();
            $purchase           = \App\PurchaseLine::where("transaction_id",$source->id)->get();
            $total_purchase     = \App\PurchaseLine::where("transaction_id",$source->id)->select(DB::raw("SUM(quantity*purchase_price) as total_price"))->first(); 
            $account            = \App\Account::where("contact_id",$source->contact_id)->first();
            $ids                = $source->purchase_lines->pluck("id"); 
            $move_id            = [];
        // ************
        // *2* FOR DELETE SECTION 
        // *** AGT8422
            $purchase_id_delete = \App\Models\ItemMove::where("transaction_id",$source->id)->whereNotIn("line_id",$ids)->get();
            $ware_id_delete     = \App\MovementWarehouse::where("transaction_id",$source->id)->whereNotIn("purchase_line_id", $ids)->get();
            $rp                 = \App\Models\RecievedPrevious::where("transaction_id",$source->id)->whereNotIn("line_id",$ids)->get();
            ItemMove::deleteItemMovement($purchase_id_delete);
            ItemMove::deleteWarehouseStock($ware_id_delete);
            ItemMove::deletePrevious($rp);
        // ************
        // *3* FOR UPDATE OLD ITEM MOVEMENT OR CREATE NEW 
        // *** AGT8422 
            if(count($purchase)>0){
                foreach($purchase as $key=>$pli){
                    $itemMove = \App\Models\ItemMove::where("transaction_id",$source->id)->where("line_id",$pli->id)->first();
                    if(!empty($itemMove)){ $move_id[] = $itemMove->id; }
                    //...... for second price in item movement
                    if($total_purchase->total_price != 0){
                        $percent      = ($expense - $dis) / $total_purchase->total_price;
                        $additional   = $pli->purchase_price * $percent ;
                        $cost_inc_exp = $pli->purchase_price + $additional ;
                    }else{
                        $cost_inc_exp = $pli->purchase_price;
                    }
                    
                    if(empty($itemMove)){
                        ItemMove::saveItemMove("purchase",$account,$itemMove,$source,$pli,$cost_inc_exp,$pli->id,$receive[$key]);
                    }else{
                        ItemMove::updateItemMove("purchase",$account,$itemMove,$source,$pli,$cost_inc_exp,$pli->id,$move_id,$receive[$key]);
                    }
                }
            }  
            DB::commit();
        // ************
    }
    /**
     * **************************************************************************** *
     *  3 Finish......... . . . .  create/E open quantity . . . . .........    /    *
     *          ................. ************************ .................        *
     * **************************************************************************** *
     */
    public static function create_open($source,$expense,$before,$after=null,$dis,$line,$variation_id = null)
    { 

        $move_id        = []; 
        $line_id        = [];
        $array_del      = [];
        $product_id     = [];
        $account        = \App\Account::where("contact_id",$source->contact_id)->first();
        $purchase       = \App\PurchaseLine::where("transaction_id",$source->id)->get();
        $total_purchase = \App\PurchaseLine::where("transaction_id",$source->id)->select(DB::raw("SUM(quantity*purchase_price) as total_price"))->first(); 
        if(count($purchase)>0){
            foreach($purchase as $pli){
                if(!in_array($pli->product_id,$line_id)){ $line_id[]    = $pli->product_id ; $product_id[] = $pli->product_id ; }
                $itemMove  = ItemMove::where("line_id",$line)->first();
                if(!empty($itemMove)){ $move_id[] = $itemMove->id; }
                //...... for second price in item movement
                if($total_purchase->total_price != 0){
                    $percent          = ($expense - $dis) / $total_purchase->total_price;
                    $additional       = $pli->purchase_price * $percent ;
                    $multiple_product = ($pli->sub_unit_qty != null)?(($pli->sub_unit_qty != 0)?$pli->sub_unit_qty:1):1;
                    $cost_inc_exp     = ($pli->purchase_price + $additional) / ($pli->quantity*$multiple_product);
                }else{
                    $multiple_product = ($pli->sub_unit_qty != null)?(($pli->sub_unit_qty != 0)?$pli->sub_unit_qty:1):1;
                    $cost_inc_exp     = $pli->purchase_price / ($pli->quantity*$multiple_product);
                }
                 
                if($pli->id == $line){
                    if(empty($itemMove)){
                        if($variation_id != null){
                            ItemMove::saveItemMove("create_open",$account,$itemMove,$source,$pli,$cost_inc_exp,$line,NULL,NULL,NULL,$variation_id);
                        }else{
                            ItemMove::saveItemMove("create_open",$account,$itemMove,$source,$pli,$cost_inc_exp,$line);
                        }
                    }else{
                        ItemMove::updateItemMove("create_open",$account,$itemMove,$source,$pli,$cost_inc_exp,$line,$move_id);
                    }
                }
            }  
                
        }
    }
    /**
     * **************************************************************************** *
     *  4 Finish......... . . . .  create warehouse transfer . . . . .........   /  *
     *          ................. *************************** .................     *
     * **************************************************************************** *
     */
    public static function transfer($source,$destination,$expense,$dis)
    {
        $line_id        = [];$product_id     = [];
        $line_id_       = [];$product_id_    = [];
        $move_id        = [];$move_id_       = []; 
        $sells          = \App\TransactionSellLine::where("transaction_id",$destination->id)->get();
        $total_sells    = \App\TransactionSellLine::where("transaction_id",$destination->id)->select(DB::raw("SUM(quantity*unit_price) as total_price"))->first();
        if(count($sells)>0){
            foreach($sells as $sli){
 
                if(!in_array($sli->product_id,$line_id)){
                    $line_id[]    = $sli->product_id;
                    $product_id[] = $sli->product_id;
               }
               $itemMove_sell = ItemMove::where("transaction_id",$destination->id)->where("sells_line_id",$sli->id)->first(); 
               if(!empty($itemMove_sell)){
                   $move_id[] = $itemMove_sell->id; 
               }
                //...... for second price in item movement
                if($total_sells->total_price != 0){
                    $percent      = ($expense - $dis) / $total_sells->total_price;
                    $additional   = $sli->unit_price * $percent ;
                    $cost_inc_exp = $sli->unit_price + $additional ;
                }else{
                    $cost_inc_exp = $sli->unit_price;
                }

                 if(empty($itemMove_sell)){
                    // *** AGT8422 SAVE SELL ITEMS
                    ItemMove::saveItemMove("transfer",null,$itemMove_sell,$destination,$sli,$cost_inc_exp,$sli->id);
                    // *********************************** 
                 }else{
                    // *** AGT8422 UPDATE SELL ITEMS
                    ItemMove::updateItemMove("transfer",null,$itemMove_sell,$destination,$sli,$cost_inc_exp,$sli->id,$move_id);
                    // *********************************** 
                 }                                                 
            }
        }
        $purchase       = \App\PurchaseLine::where("transaction_id",$source->id)->get();
        $total_purchase = \App\PurchaseLine::where("transaction_id",$source->id)->select(DB::raw("SUM(quantity*purchase_price) as total_price"))->first();
        if(count($purchase)>0){
            foreach($purchase as $pli){

               if(!in_array($pli->product_id,$line_id_)){
                    $line_id_[]    = $pli->product_id;
                    $product_id_[] = $pli->product_id;
               }
               $itemMove_purchase = ItemMove::where("transaction_id",$source->id)->where("purchase_line_id",$pli->id)->first(); 
               if(!empty($itemMove_purchase)){
                   $move_id_[] = $itemMove_purchase->id; 
               }
                //...... for second price in item movement
                if($total_purchase->total_price != 0){
                    $percent      = ($expense - $dis) / $total_purchase->total_price;
                    $additional   = $pli->purchase_price * $percent ;
                    $cost_inc_exp = $pli->purchase_price + $additional ;
                }else{
                    $cost_inc_exp = $pli->purchase_price;
                }

                if(empty($itemMove_purchase)){
                     // *** AGT8422 SAVE PURCHASE ITEMS
                     ItemMove::saveItemMove("purchase_transfer",null,$itemMove_purchase,$source,$pli,$cost_inc_exp,$pli->id);
                     // *********************************** 
                }else{
                    // *** AGT8422 UPDATE PURCHASE ITEMS
                    ItemMove::updateItemMove("purchase_transfer",null,$itemMove_purchase,$source,$pli,$cost_inc_exp,$pli->id,$move_id);
                    // ***********************************    
                }
            }
        }
       
    }
    /**
     * **************************************************************************** *
     *  5 Finish......... . . . .  delete warehouse transfer . . . . .........   /  *
     *          ................. *************************** .................     *
     * **************************************************************************** *
     */
    public static function delete_transafer($sell,$purchase)
    {
        $itemMove_purchase = \App\Models\ItemMove::where("business_id",$purchase->business_id)->where("transaction_id",$purchase->id)->get();
        $itemMove_sell     = \App\Models\ItemMove::where("business_id",$purchase->business_id)->where("transaction_id",$sell->id)->get();
        foreach($itemMove_purchase as $it){
             $it->delete();
        }
        foreach($itemMove_sell as $it){
             $it->delete();
        }
    }
    /**
     * **************************************************************************** *
     *  6 Finish......... . . . .  receive - ' - purchase  . . . . .........     /  *
     *          ................. ************************* .................       *
     * **************************************************************************** *
     */
    public static function receive($source,$expense,$receive_exp,$tr_recieve=null)
    { 
        $account              = \App\Account::where("contact_id",$source->contact_id)->first();
        $receive              = \App\Models\RecievedPrevious::where("transaction_id",$source->id)->get();
        $tr                   = \App\Transaction::find($source->id);
        $row                  = \App\Models\ItemMove::subtotal_recieve_correct($source->id,$tr_recieve);
        $sub_total_in_recieve = 0;
        foreach($row as $it){
            $qty                   = $it[2];
            $price                 = $it[1];
            $total_                = $it[2] * $it[1];
            $sub_total_in_recieve += $total_; 
        }
        $total_receive_price       =  $sub_total_in_recieve;
        if( count($receive) > 0 ){
            foreach($receive as $recv){
                $PREV_ITEM          = \App\Models\ItemMove::where("transaction_id",$recv->transaction_id)->where("entry_option",1)->where("recieve_id",$recv->id)->first(); 
                ///... get  product info
                $product_id         = $recv->product_id;
                $qty_product        = $recv->current_qty;
                ///... get  one product
                $row_purchase_one   = \App\PurchaseLine::where("transaction_id",$source->id)
                                                                ->where("product_id",$product_id)
                                                                ->select(
                                                                    "purchase_price",
                                                                    "quantity",
                                                                    DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                    DB::raw("SUM(quantity) as qty")
                                                                )->first();
                ///... get  purchase price cost
                $row_purchase_price = \App\PurchaseLine::where("transaction_id",$source->id)
                                                                ->select(
                                                                    DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                    DB::raw("SUM(quantity) as qty")
                                                                )->first();
                if(empty($PREV_ITEM)){  
                    ///... choice the cost 
                    if( !empty($row_purchase_price) ){
                        if ($tr->discount_type == "fixed_before_vat"){
                            $dis = $tr->discount_amount;
                        }else if ($tr->discount_type == "fixed_after_vat"){
                            $tax = \App\TaxRate::find($tr->tax_id);
                            $dis = ($tr->discount_amount*100)/(100+$tax->amount) ;
                        }else if ($tr->discount_type == "percentage"){
                            $dis = ($tr->total_before_tax *  $tr->discount_amount)/100;
                        }else{
                            $dis = 0;
                        }
                        $add             = \App\Models\AdditionalShippingItem::whereHas('additional_shipping',function($query) use($tr){
                                                                                        $query->where("type",0);
                                                                                        $query->where('transaction_id',$tr->id);
                                                                                })->sum('amount') ;
                        
                        ($row_purchase_price->qty != 0)? $row_cost = ( $row_purchase_one->total_row_price / $row_purchase_one->qty ) :  $row_cost = 0;
                        if($add != 0){
                            if($row_purchase_price->total_row_price != 0){
                                $percent       = ($add - $dis) / $row_purchase_price->total_row_price;
                            }else{
                                $percent       = 0;
                            }
                            $additional    = $row_cost * $percent ;
                            $cost_inc_exp  = $row_cost  ;
                            $cost_inc_exp_ = $row_cost + $additional ;
                            
                        }else{
                            if($row_purchase_price->total_row_price != 0){
                               $percent       = ($dis) / $row_purchase_price->total_row_price;
                            }else{
                                $percent       = 0;
                            }
                            
                            $additional    = $row_cost * $percent ;
                            $cost_inc_exp  = $row_cost ;
                            $cost_inc_exp_ = $row_cost - $additional ;
                        }
                    }else{
                        $row_cost      = \App\Product::product_cost($product_id);
                        if($row_purchase_price->total_row_price != 0){
                           $percent       = ($dis) / $row_purchase_price->total_row_price;
                        }else{
                            $percent       = 0;
                        }
                        $additional    = $row_cost * $percent ;
                        $cost_inc_exp  = $row_cost;
                        $cost_inc_exp_ = $row_cost - $additional ;
                    }
                    if($total_receive_price != 0){
                        $percent          = $receive_exp / $total_receive_price;
                        $additional       = $cost_inc_exp_   * $percent ;
                        $cost_inc_exp_rec = $cost_inc_exp_   + $additional ;
                    }else{
                        $cost_inc_exp_rec = $row_cost ;
                    }
                    $prices    = [];
                    $prices[]  = $cost_inc_exp_rec;
                    // *** AGT8422 SAVE RECEIVED ITEMS
                    ItemMove::saveItemMove("receive",$account,$PREV_ITEM,$source,$recv->purchase_line,$cost_inc_exp,$recv->line_id,$recv,$prices);
                    // *********************************** 
                    
                }
            }
        }
    }

    /** 
     * **************************************************************************** *
     *  7 Finish......... . . . . purchase update recieve . . . . .........     /   *
     *          ................. *********************** .................         *
     * **************************************************************************** *
     */
    public static function recieve_update($id,$transaction_id,$tr_recieve=null)
    {
            $tr                        = \App\Transaction::find($transaction_id);
            $account                   = \App\Account::where("contact_id",$tr->contact_id)->first();
            $trd                       = \App\Models\TransactionRecieved::find($id);
            $ids                       = \App\Models\TransactionRecieved::childs($id);
            $row                       = \App\Models\ItemMove::subtotal_recieve_correct($transaction_id,$tr_recieve);
            $move_id                   = [];
            $sub_total_in_recieve      = 0;
            foreach($row as $it){
                $qty                   = $it[2];
                $price                 = $it[1];
                $total_                = $it[2] * $it[1];
                $sub_total_in_recieve += $total_; 
            }
            $total_receive_price       =  $sub_total_in_recieve;

            $cost_recieve=0;$without_contact_recieve=0;
            $data_ship_recieve = \App\Models\AdditionalShipping::where("transaction_id",$transaction_id)->where("type",1)->where("t_recieved",$id)->first();
            if(!empty($data_ship_recieve)){
               $ids_recieve = $data_ship_recieve->items->pluck("id");
               foreach($ids_recieve as $i){
                  $data_shippment_recieve   = \App\Models\AdditionalShippingItem::find($i);
                  if($data_shippment_recieve->contact_id == $tr->contact_id){ 
                     $cost_recieve += $data_shippment_recieve->amount;
                  }else{
                     $without_contact_recieve += $data_shippment_recieve->amount;
                  }
               }
            }
            $receive_exp = $cost_recieve + $without_contact_recieve;

            //.... search for movment not in new updates
            //.../.......................................\...
            $itemMove = \App\Models\ItemMove::where("transaction_id",$transaction_id)->where("entry_option",1)->whereNotNull("line_id")->whereNotIn("recieve_id",$ids)->get();

            //........... delete not in updates lines
            //........./..............................\..
            if(count($itemMove)>0){
                foreach($itemMove as $it){
                    $id         = $it->id;
                    $prodcut_id = $it->product_id;
                    // $it->delete();
                    // ItemMove::refresh_item($id,$prodcut_id);
                }
            }
            
            // ....... update previous lines
            // ....../.......................\..
            if($ids){
                foreach($ids as $idm){
                    $it          = \App\Models\RecievedPrevious::find($idm);
                    $product_id  = $it->product_id;
                    $qty_product = $it->current_qty;
                    $PREV_ITEM   = \App\Models\ItemMove::where("transaction_id",$transaction_id)->where("entry_option",1)->where("recieve_id",$idm)->first(); 
                    if(empty($PREV_ITEM)){
                           $PREV_ITEM   =  \App\Models\ItemMove::where("transaction_id",$transaction_id)->where("entry_option",0)->where("recieve_id",$idm)->first(); 
                    }
                    
                    ///... get  purchase one product
                    $row_purchase_one   = \App\PurchaseLine::where("transaction_id",$transaction_id)
                                                            ->where("product_id",$product_id)
                                                            ->select(
                                                                DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();
                    ///... get  purchase price cost
                    $row_purchase_price = \App\PurchaseLine::where("transaction_id",$transaction_id)
                                                             ->select(
                                                                DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();
                     
                    ///... choice the cost 
                    if( !empty($row_purchase_price) ){
                        if ($tr->discount_type == "fixed_before_vat"){
                            $dis = $tr->discount_amount;
                        }else if ($tr->discount_type == "fixed_after_vat"){
                            $tax = \App\TaxRate::find($tr->tax_id);
                            $dis = ($tr->discount_amount*100)/(100+$tax->amount) ;
                        }else if ($tr->discount_type == "percentage"){
                            $dis = ($tr->total_before_tax *  $tr->discount_amount)/100;
                        }else{
                            $dis = 0;
                        }
                        $add               = \App\Models\AdditionalShippingItem::whereHas('additional_shipping',function($query) use($tr){
                                                                                        $query->where("type",0);
                                                                                        $query->where('transaction_id',$tr->id);
                                                                                })->sum('amount') ;
                        
                        ($row_purchase_price->qty != 0)? $row_cost = ( $row_purchase_one->total_row_price / $row_purchase_one->qty ) :  $row_cost = 0;
                        if($add != 0){
                            if($row_purchase_price->total_row_price != 0){
                                $percent       = ($add - $dis) / $row_purchase_price->total_row_price;
                            }else{
                                $percent       = 0;
                            }
                            $additional    = $row_cost * $percent ;
                            $cost_inc_exp  = $row_cost;
                            $cost_inc_exp_ = $row_cost + $additional ;
                            
                        }else{
                            if($row_purchase_price->total_row_price != 0){
                                $percent       = ($dis) / $row_purchase_price->total_row_price;
                            }else{
                                $percent       = 0;
                            }
                            $additional    = $row_cost * $percent ;
                            $cost_inc_exp  = $row_cost;
                            $cost_inc_exp_ = $row_cost - $additional ;
                        }
        
                    }else{
                        $row_cost         = \App\Product::product_cost($product_id);
                        if($row_purchase_price->total_row_price != 0){
                            $percent       = ($dis) / $row_purchase_price->total_row_price;
                        }else{
                            $percent       = 0;
                        }
                        $additional       = $row_cost * $percent ;
                        $cost_inc_exp     = $row_cost;
                        $cost_inc_exp_    = $row_cost - $additional ;
                    }

                    if($total_receive_price != 0){
                        $percent          = $receive_exp / $total_receive_price;
                        $additional       = $cost_inc_exp_  * $percent ;
                        $cost_inc_exp_rec = $cost_inc_exp_  + $additional ;
                    }else{ 
                        $cost_inc_exp_rec = $row_cost ;
                    }
                    $prices               = [];
                    $prices[]             = $cost_inc_exp_rec;
                    if(empty($PREV_ITEM)){  
                        // *** AGT8422 SAVE RECEIVED ITEMS
                        ItemMove::saveItemMove("receive",$account,$PREV_ITEM,$tr,$it->purchase_line,$cost_inc_exp,$it->line_id,$it,$prices);
                        // *********************************** 
                    }else{
                        // *** AGT8422 UPDATE RECEIVED ITEMS
                        ItemMove::updateItemMove("receive",$account,$PREV_ITEM,$tr,$it->purchase_line,$cost_inc_exp,$it->line_id,$move_id,$it,$prices);
                        // *********************************** 
                    }
                }
            }


    }
    /**
     * **************************************************************************** *
     *  8  Finish........ . . .   R/recieve - ' - purchase . . . . .........    /   *
     *           ..............  ************************* .................        *
     * **************************************************************************** *
     */
    public static function return_recieve($transaction_return ,$tr_recieve)
    {
        $account        = \App\Account::where("contact_id",$transaction_return->contact_id)->first();
        $receive        = \App\Models\RecievedPrevious::where("transaction_id",$transaction_return->id)->get();
        $tr             = \App\Transaction::find($transaction_return->id);
        $row            = \App\Models\ItemMove::subtotal_recieve_correct($transaction_return->id,$tr_recieve,"return");
       
        $sub_total_in_recieve = 0;
        foreach($row as $it){
            $qty    = $it[2];
            $price  = $it[1];
            $total_ = $it[2] * $it[1];
            $sub_total_in_recieve += $total_; 
        }

        $total_receive_price =  $sub_total_in_recieve;
        DB::beginTransaction();
        if( count($receive) > 0 ){
            foreach($receive as $recv){
                $PREV_ITEM = \App\Models\ItemMove::where("transaction_id",$recv->transaction_id)->where("entry_option",1)->where("recieve_id",$recv->id)->first(); 
                
                ///... get  product info
                $product_id  = $recv->product_id;
                $qty_product = $recv->current_qty;

                $tn   =  \App\Transaction::where("id",$transaction_return->return_parent_id)->first();

                ///... get  one product
                $row_purchase_one = \App\PurchaseLine::where("transaction_id",$tn->id)
                                                                ->where("product_id",$product_id)
                                                                ->select(
                                                                    "bill_return_price",
                                                                    "quantity_returned",
                                                                    DB::raw("SUM(quantity_returned*bill_return_price) as total_row_price"),
                                                                    DB::raw("SUM(quantity_returned) as qty")
                                                                )->first();
                ///... get  purchase price cost
                $row_purchase_price = \App\PurchaseLine::where("transaction_id",$tn->id)
                                                                ->select(
                                                                    DB::raw("SUM(quantity_returned*bill_return_price) as total_row_price"),
                                                                    DB::raw("SUM(quantity_returned) as qty")
                                                                )->first();
            if(empty($PREV_ITEM)){  
                ///... choice the cost 
                if( !empty($row_purchase_price) ){
                    
                    if ($tr->discount_type == "fixed_before_vat"){
                        $dis = $tr->discount_amount;
                    }else if ($tr->discount_type == "fixed_after_vat"){
                        $tax = \App\TaxRate::find($tr->tax_id);
                        $dis = ($tr->discount_amount*100)/(100+$tax->amount) ;
                    }else if ($tr->discount_type == "percentage"){
                        $dis = ($tr->total_before_tax *  $tr->discount_amount)/100;
                    }else{
                        $dis = 0;
                    }
                    $add             = \App\Models\AdditionalShippingItem::whereHas('additional_shipping',function($query) use($tr){
                                                                                    $query->where("type",0);
                                                                                    $query->where('transaction_id',$tr->id);
                                                                            })->sum('amount') ;
                    
                    ($row_purchase_one->qty != 0)? $row_cost = ( $row_purchase_one->total_row_price / $row_purchase_one->qty ) :  $row_cost = 0;
                    if($add != 0){
                        if($row_purchase_price->total_row_price != 0){
                            $percent       = ($add - $dis) / $row_purchase_price->total_row_price;
                        }else{
                            $percent       = 0;
                        }
                        $additional    = $row_cost * $percent ;
                        $cost_inc_exp  = $row_cost  ;
                        $cost_inc_exp_ = $row_cost + $additional ;
                        
                    }else{
                        if($row_purchase_price->total_row_price != 0){
                            $percent       = ($dis) / $row_purchase_price->total_row_price;
                        }else{
                            $percent       = 0;
                        }
                        $additional    = $row_cost * $percent ;
                        $cost_inc_exp  = $row_cost ;
                        $cost_inc_exp_ = $row_cost - $additional ;
                    }
                 }else{
                    $row_cost      = \App\Product::product_cost($product_id);
                    if($row_purchase_price->total_row_price != 0){
                        $percent       = ($dis) / $row_purchase_price->total_row_price;
                    }else{
                        $percent       = 0;
                    }
                    $additional    = $row_cost * $percent ;
                    $cost_inc_exp  = $row_cost;
                    $cost_inc_exp_ = $row_cost - $additional ;
                }
                $prices   = [];
                $prices[] = $cost_inc_exp;
                $prices[] = $cost_inc_exp_;
                // *** ADD PURCHASE RETURN RECEIVED ITEMS
                ItemMove::saveItemMove("receivex",$account,$PREV_ITEM,$transaction_return,$recv->purchase_line,$cost_inc_exp,$recv->line_id,$recv,$prices);
                // ****************************************
                DB::commit();
            }
        }
        }
    }
    /** 
     * **************************************************************************** *
     *  9 Finish........ . . . . R/recieve update purchase . . . . .........    /   *
     *          ................ ************************** .................       *
     * **************************************************************************** *
     */
    public static function return_recieve_update($id,$transaction_return,$tr_recieve=null)
    {       
            $tr       = \App\Transaction::find($transaction_return->id);
            $account  = \App\Account::where("contact_id",$tr->contact_id)->first();
            $trd      = \App\Models\TransactionRecieved::find($id->id);
            $ids      = \App\Models\TransactionRecieved::childs($id->id);
            $row      = \App\Models\ItemMove::subtotal_recieve_correct($transaction_return->id,$tr_recieve,"return");
            $move_id  = [];
            $sub_total_in_recieve = 0;
            foreach($row as $it){
                $qty                   = $it[2];
                $price                 = $it[1];
                $total_                = $it[2] * $it[1];
                $sub_total_in_recieve += $total_ ; 
            }
            $total_receive_price       =  $sub_total_in_recieve;

            //.... search for movement not in new updates
            //.../.......................................\...
            $itemMove = \App\Models\ItemMove::where("transaction_id",$transaction_return->id)->where("entry_option",1)->whereNotNull("line_id")->whereNotIn("recieve_id",$ids)->get();
           

            //........... delete not in updates lines
            //........./..............................\..
            if(count($itemMove)>0){
                foreach($itemMove as $it){
                    $id         = $it->id;
                    $product_id = $it->product_id;
                    $it->delete();
                    // ItemMove::refresh_item($id,$product_id);
                }
            }

            // ....... update previous lines
            // ....../.......................\..
            if($ids){
                foreach($ids as $idm){
                    $it = \App\Models\RecievedPrevious::find($idm);
                    
                    $product_id  = $it->product_id;
                    $qty_product = $it->current_qty;

                    $PREV_ITEM = \App\Models\ItemMove::where("transaction_id",$transaction_return->id)->where("entry_option",1)->where("recieve_id",$idm)->first(); 
                    if(empty($PREV_ITEM)){
                           $PREV_ITEM =  \App\Models\ItemMove::where("transaction_id",$transaction_return->id)->where("entry_option",0)->where("recieve_id",$idm)->first(); 
                    }
                    ///... get  purchase one product
                    $row_purchase_one = \App\PurchaseLine::where("transaction_id",$transaction_return->return_parent_id)
                                                            ->where("product_id",$product_id)
                                                            ->select(
                                                                DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();
                    ///... get  purchase price cost
                    $row_purchase_price = \App\PurchaseLine::where("transaction_id",$transaction_return->return_parent_id)
                                                             ->select(
                                                                DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();
                   
                    ///... choice the cost 
                    if( !empty($row_purchase_price) ){
    
                        $exchange = ($tr->currency_id)?$tr->exchange_price:1;
                        if ($tr->discount_type == "fixed_before_vat"){
                            $dis = $tr->discount_amount*$exchange;
                        }else if ($tr->discount_type == "fixed_after_vat"){
                            $tax = \App\TaxRate::find($tr->tax_id);
                            $dis = ($tr->discount_amount*$exchange*100)/(100+$tax->amount) ;
                        }else if ($tr->discount_type == "percentage"){
                            $dis = ($tr->total_before_tax *  $tr->discount_amount)/100;
                        }else{
                            $dis = 0;
                        }

                        $add             = \App\Models\AdditionalShippingItem::whereHas('additional_shipping',function($query) use($tr){
                                                                                        $query->where("type",0);
                                                                                        $query->where('transaction_id',$tr->id);
                                                                                })->sum('amount') ;
                        
                        ($row_purchase_price->qty != 0)? $row_cost = ( $row_purchase_one->total_row_price / $row_purchase_one->qty ) :  $row_cost = 0;
                        if($add != 0){
                            if($row_purchase_price->total_row_price != 0){
                                $percent       = ($add - $dis) / $row_purchase_price->total_row_price;
                            }else{
                                $percent       = 0;
                            }
                            $additional    = $row_cost * $percent ;
                            $cost_inc_exp  = $row_cost  ;
                            $cost_inc_exp_ = $row_cost + $additional ;
                            
                        }else{
                            if($row_purchase_price->total_row_price != 0){
                                $percent       = ($dis) / $row_purchase_price->total_row_price;
                            }else{
                                $percent       = 0;
                            }
                            $additional     = $row_cost * $percent ;
                            $cost_inc_exp   = $row_cost   ;
                            $cost_inc_exp_  = $row_cost - $additional ;
                        }
        
                    }else{
                        $row_cost      = \App\Product::product_cost($product_id);
                        if($row_purchase_price->total_row_price != 0){
                            $percent       = ($dis) / $row_purchase_price->total_row_price;
                        }else{
                            $percent       = 0;
                        }
                        $additional    = $row_cost * $percent ;
                        $cost_inc_exp  = $row_cost;
                        $cost_inc_exp_ = $row_cost - $additional ;
                    }
                     
                    $prices   = [];
                    $prices[] = $cost_inc_exp;
                    $prices[] = $cost_inc_exp_;
                    if(empty($PREV_ITEM)){ 
                        // *** ADD PURCHASE RETURN RECEIVED ITEMS
                        ItemMove::saveItemMove("receivex",$account,$PREV_ITEM,$tr,$it->purchase_line,$cost_inc_exp,$it->line_id,$it,$prices);
                        // **************************************** 
                    }else{
                        // *** AGT8422 UPDATE RETURN RECEIVED ITEMS
                        ItemMove::updateItemMove("receivex",$account,$PREV_ITEM,$tr,$it->purchase_line,$cost_inc_exp,$it->line_id,$move_id,$it,$prices);
                        // *****************************************                       
                    }

                }
            }


    }
    /**
     * **************************************************************************** *
     *  10 Finish......... . . .  sub total       recieve . . . . .........     /   *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function subtotal_recieve($id)
    {
        $receive        = \App\Models\RecievedPrevious::where("transaction_id",$id)->get();
        $tr             = \App\Transaction::find($id);
        $total_receive_price = 0;
        if( count($receive) > 0 ){
            foreach($receive as $recv){
                ///... get  product info
                $product_id  = $recv->product_id;
                $qty_product = $recv->current_qty;
                ///... get  purchase price cost
                $row_purchase_price = \App\PurchaseLine::where("transaction_id",$id)->where("product_id",$product_id)->select(
                                                                    DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                    DB::raw("SUM(quantity) as qty")
                                                                )->first();
                if ($tr->discount_type == "fixed_before_vat"){
                    $dis = $tr->discount_amount;
                }else if ($tr->discount_type == "fixed_after_vat"){
                    $tax = \App\TaxRate::find($tr->tax_id);
                    $dis = ($tr->discount_amount*100)/(100+$tax->amount) ;
                }else if ($tr->discount_type == "percentage"){
                    $dis = ($tr->total_before_tax *  $tr->discount_amount)/100;
                }else{
                    $dis = 0;
                }

                $add             = \App\Models\AdditionalShippingItem::whereHas('additional_shipping',function($query) use($id){
                                                                    $query->where("type",0);
                                                                    $query->where('transaction_id',$id);
                                                            })->sum('amount') ;
                
                ///... choice the cost 
                if( !empty($row_purchase_price) ){
                    ($row_purchase_price->qty != 0)? $row_cost = ( $row_purchase_price->total_row_price / $row_purchase_price->qty ) :  $row_cost = 0;
                    if($add != 0){
                        if($row_purchase_price->total_row_price != 0){
                            $percent       = ($add - $dis) / $row_purchase_price->total_row_price;
                        }else{
                            $percent       = 0;
                        }
                        $additional   = $row_cost * $percent ;
                        $cost_inc_exp = $row_cost + $additional ;
                    }else{
                        $cost_inc_exp = $row_cost;
                    }
                }else{
                    $row_cost = \App\Product::product_cost($product_id);
                    $cost_inc_exp = $row_cost;
                }
                 
                $total_receive_price += ($cost_inc_exp*$qty_product);
            }
        }
       
        return $total_receive_price;
    }
    /**
     * **************************************************************************** *
     *  11 Finish......... . . .  sub total correct recieve . . . . .........    /  *
     *           ................ ************************* .................       *
     * **************************************************************************** *
     */
    public static function subtotal_recieve_correct($id,$tr_recieve=null,$return=null)
    {
        
        $array           = [] ;
        $array_unique_id = [] ;
        // $received        = \App\Models\RecievedPrevious::where("transaction_id",$id)->where("transaction_deliveries_id",$tr_recieve)->get();
        $received        = \App\Models\RecievedPrevious::where("transaction_id",$id)->where("transaction_deliveries_id",$tr_recieve->id)->get();
        foreach($received as $it){
            if(!in_array($it->product_id,$array_unique_id)){
                array_push($array_unique_id,$it->product_id);
            }
        }
        
        foreach($array_unique_id as $it){
            $array_item      = [] ;
            // $receive         = \App\Models\RecievedPrevious::where("transaction_id",$id)->where("transaction_deliveries_id",$tr_recieve)->where("product_id",$it)->sum("current_qty");
            $receive         = \App\Models\RecievedPrevious::where("transaction_id",$id)->where("transaction_deliveries_id",$tr_recieve->id)->where("product_id",$it)->sum("current_qty");
            if($return != null){
                $row = ItemMove::one_Item($id,$it,"return");
            }else{
                $row = ItemMove::one_Item($id,$it);
            }
            $array_item[] = $it; 
            $array_item[] = $row; 
            $array_item[] = $receive; 
            $array[]      = $array_item; 
        }

        return $array;
    }
    /**
     * **************************************************************************** *
     *  12 Finish......... . . .   One Item        recieve . . . . ........     /   *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function one_Item($id,$product_id,$return=null)
    {

        $total            =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id) {
                                                                $query->where("type",1);
                                                                $query->where("transaction_id",$id);
                                                            })->sum("amount");

        $total_purchase   =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id) {
                                                                $query->where("type",0);
                                                                $query->where("transaction_id",$id);
                                                            })->sum("amount");
        
        if($return != null){
            
                $tn = \App\Transaction::where("id",$id)->first();
                $tr = \App\Transaction::where("id",$tn->return_parent_id)->first();
                 
                $row_purchase_price_total = \App\PurchaseLine::where("transaction_id",$tr->id)
                                                                ->select(
                                                                    DB::raw("SUM(quantity_returned*bill_return_price) as total_row_price"),
                                                                    DB::raw("SUM(quantity_returned) as qty")
                                                                )->first();

                $row_purchase_price = \App\PurchaseLine::where("transaction_id",$tr->id)
                                                    ->where("product_id",$product_id)
                                                    ->select(
                                                        DB::raw("SUM(quantity_returned*bill_return_price) as total_row_price"),
                                                        DB::raw("SUM(quantity_returned) as qty")
                                                    )->first();
        }else{
                $row_purchase_price_total = \App\PurchaseLine::where("transaction_id",$id)
                                                                ->select(
                                                                    DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                    DB::raw("SUM(quantity) as qty")
                                                                )->first();
                $row_purchase_price = \App\PurchaseLine::where("transaction_id",$id)
                                                    ->where("product_id",$product_id)
                                                    ->select(
                                                        DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                        DB::raw("SUM(quantity) as qty")
                                                    )->first();
        }

        $transaction_basic  = \App\Transaction::find($id); 

        $exchange = ($transaction_basic->currency_id)?$transaction_basic->exchange_price:1;
        if ($transaction_basic->discount_type == "fixed_before_vat"){
            $dis = $transaction_basic->discount_amount*$exchange;
        }else if ($transaction_basic->discount_type == "fixed_after_vat"){
            $tax = \App\TaxRate::find($transaction_basic->tax_id);
            $dis = ($transaction_basic->discount_amount*$exchange*100)/(100+$tax->amount) ;
        }else if ($transaction_basic->discount_type == "percentage"){
            $dis = ($transaction_basic->total_before_tax *  $transaction_basic->discount_amount)/100;
        }else{
            $dis = 0;
        }
         
        if( !empty($row_purchase_price) ){
            if($row_purchase_price->qty == 0){
                $row_cost = \App\Product::product_cost($product_id);
            }else{
                ///... choice the cost 
                $final_prices      =  $row_purchase_price_total->total_row_price - $dis + $total_purchase;
                if($row_purchase_price_total->total_row_price != 0){
                    $pecent_prices     =  $final_prices / $row_purchase_price_total->total_row_price;
                }else{
                    $pecent_prices     =  0;
                }
                if($row_purchase_price->qty != 0){
                    $additional_prices =  $pecent_prices * ($row_purchase_price->total_row_price / $row_purchase_price->qty);
                }else{
                    $additional_prices =  $pecent_prices * 0;
                }
                $row_cost          =  $additional_prices    ;
            }

        }else{
                $row_cost = \App\Product::product_cost($product_id);
        }
         
        return $row_cost;
    }
    /**
     * **************************************************************************** *
     *  13 Finish......... . . . . costs - - - - - - - --  . . . . ........         *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function costs($id,$line,$cost_inc_exp,$type=null,$update=null,$qty=null,$plus_qty=null,$date=null,$variation_id=null)
    {
        
        if($update != null){
            $costs = [];
            //...... for item cost  in item movement
            $cost_plus                              = ItemMove::getCostTotalQuantityCost("+",$line->product_id,$date,$id,"<=","!=",$variation_id);
            $cost_minus                             = ItemMove::getCostTotalQuantityCost("-",$line->product_id,$date,$id,"<=","!=",$variation_id);
            $cost_minus_production                  = ItemMove::getCostTotalQuantityListOutPriceCost("-",$line->product_id,$date,$id,"<=","!=",$variation_id);
            $cost_in_transaction_plus               = ItemMove::getCostTotalQuantityCost("+",$line->product_id,$date,$id,"<","=",$variation_id);
            $cost_in_transaction_minus              = ItemMove::getCostTotalQuantityCost("-",$line->product_id,$date,$id,"<","=",$variation_id);
            $cost_in_transaction_minus_production   = ItemMove::getCostTotalQuantityListOutPriceCost("-",$line->product_id,$date,$id,"<=","=",$variation_id);
            $cost_in_transaction_minus_rtn          = ItemMove::getCostTotalQuantityListCost("-",$line->product_id,$date,["purchase_return","Wrong - purchase_return<br>More Received","Wrong - purchase_return<br>Other Product"],$id,"<=","=",$variation_id);
            $cost_transaction_minus_rtn             = ItemMove::getCostTotalQuantityListCost("-",$line->product_id,$date,["purchase_return","Wrong - purchase_return<br>More Received","Wrong - purchase_return<br>Other Product"],$id,"<=","<",$variation_id);
        }else{
            $costs = [];
            //...... for item cost  in item movement
            $cost_plus                              = ItemMove::getCostTotalQuantityCost("+",$line->product_id,$date,$id,"<=","!=",$variation_id);
            $cost_minus                             = ItemMove::getCostTotalQuantityCost("-",$line->product_id,$date,$id,"<=","!=",$variation_id);
            $cost_minus_production                  = ItemMove::getCostTotalQuantityListOutPriceCost("-",$line->product_id,$date,$id,"<=","!=",$variation_id);
            $cost_in_transaction_plus               = ItemMove::getCostTotalQuantityCost("+",$line->product_id,$date,$id,"<","=",$variation_id);
            $cost_in_transaction_minus              = ItemMove::getCostTotalQuantityCost("-",$line->product_id,$date,$id,"<","=",$variation_id);
            $cost_in_transaction_minus_production   = ItemMove::getCostTotalQuantityListOutPriceCost("-",$line->product_id,$date,$id,"<=","=",$variation_id);
            $cost_in_transaction_minus_rtn          = ItemMove::getCostTotalQuantityListCost("-",$line->product_id,$date,["purchase_return","Wrong - purchase_return<br>More Received","Wrong - purchase_return<br>Other Product"],$id,"<=","=",$variation_id);
            $cost_transaction_minus_rtn             = ItemMove::getCostTotalQuantityListCost("-",$line->product_id,$date,["purchase_return","Wrong - purchase_return<br>More Received","Wrong - purchase_return<br>Other Product"],$id,"<=","<",$variation_id);
        }
        
        if($type == "return_p" || $type == "return_s" ){
            //...... for item cost  in item movement
            $cost_plus_return          = ItemMove::getCostTotalQuantityCost("+",$line->product_id,$date,$id,"<=","=",$variation_id);
            //...... for item cost  in item movement
            $cost_minus_return         = ItemMove::getCostTotalQuantityCost("-",$line->product_id,$date,$id,"<=","=",$variation_id);
            $return_qty                = $cost_plus_return->qty - $cost_minus_return->qty;
        }else{
            $return_qty  = 0;
        }
        
        $Qty_i            = ($qty != null)?$qty:0;
        $multiple_product = ($type=="receive"||$type=="receivex"||$type=="delivery"||$type=="deliveryx")?1:(($line->sub_unit_qty != null)?(($line->sub_unit_qty != 0)?$line->sub_unit_qty:1):1);
        $line_Qty         = ($type=="receive"||$type=="receivex"||$type=="delivery"||$type=="deliveryx")?$line->current_qty:($line->quantity*$multiple_product);
        $final_Qty        = $line_Qty + $Qty_i     ;
        $price_before     = ($cost_plus->total) - ($cost_minus_production->total)   ;
        $price_after      = ($cost_in_transaction_plus->total) - ($cost_in_transaction_minus_production->total)  ;
        $price_row        = (($qty != null) ? $Qty_i : $line_Qty) * $cost_inc_exp   ;
        $price_production = (($cost_plus->qty) - ($cost_minus->qty)!=0)?($price_before/(($cost_plus->qty)-($cost_minus->qty))):$price_before;
        $ready_qty        = ( $cost_plus->qty  - $cost_minus->qty )    +  ( $cost_in_transaction_plus->qty - $cost_in_transaction_minus->qty );
         
        if( $type == "minus"      ||
            $type == "delivery"   || 
            $type == "production" || 
            $type == "receivex"     
        ){
            $total_qty        =  $ready_qty - $final_Qty  ; 
        }elseif( $type == "return_p" ||
                 $type == "return_s"
        ){
            $Returned_qty     =  ($type=="return_s")?$line->quantity_returned:$line->quantity_returned*-1; 
            $total_qty        =  $ready_qty  + $Returned_qty ; 
        }else{
            $total_qty        =  $ready_qty  +  $final_Qty  ; 
        }

        if( $type == "return_p" ){
            $total_price         =  $price_before  +  $price_after -   $price_row      ;
        }else{
            $price_before_finish =  ( $type == "receivex" )? $price_row*-1 : $price_row;
            $price_after_finish  =  $price_before -  $cost_in_transaction_minus_rtn->total - $cost_transaction_minus_rtn->total  +  $price_after;
            $total_price         =  $price_after_finish + $price_before_finish     ;
        }
        $FINAL_COST   = ($total_qty!=0)? ($total_price / $total_qty) : 0;
        $costs[]      = $total_qty;
        $costs[]      = $FINAL_COST;
        $costs[]      = $price_production;
           
         
        return $costs;
        
    }
    // ***************************
    // **** COST Costs DATA   ****
    // ***************************
        public static function getCostTotalQuantityListCost($signal,$product_id,$date,$list_status,$id,$type,$trans_type,$variation_id=null) {
            $cost       =  ItemMove::where("transaction_id",$trans_type,$id)
                                    ->where("signal",$signal)
                                    ->where("product_id",$product_id)
                                    ->whereDate("date",$type,$date)
                                    ->whereIn("state",$list_status)
                                    ->select(
                                        DB::raw("SUM(qty) as qty"),
                                        DB::raw("SUM(qty*row_price_inc_exp) as total")
                                    );
            if($variation_id != null){
                $cost->where("variation_id",$variation_id);
            }

            $cost       =  $cost->first();
            return $cost;
        }
        public static function getCostTotalQuantityListOutPriceCost($signal,$product_id,$date,$id,$type,$trans_type,$variation_id=null) {
            $cost       =  ItemMove::where("transaction_id",$trans_type,$id)
                                        ->whereDate("date",$type,$date)
                                        ->where("signal",$signal)
                                        ->where("product_id",$product_id)
                                        ->select(
                                            DB::raw("SUM(qty) as qty"),
                                            DB::raw("SUM(IF(state = 'Manufacturing - ( Out )' OR state = 'sale',qty*out_price,0)) as total")
                                        );
            if($variation_id != null){
                $cost->where("variation_id",$variation_id);
            }

            $cost       =  $cost->first();
            return $cost;
        }
        public static function getCostTotalQuantityCost($signal,$product_id,$date,$id,$type,$trans_type,$variation_id=null) {
            $cost_less       =  ItemMove::where("transaction_id",$trans_type,$id)
                                            ->whereDate("date",$type,$date)
                                            ->where("signal",$signal)
                                            ->where("product_id",$product_id)
                                            ->select(
                                                DB::raw("SUM(qty) as qty"),
                                                DB::raw("SUM(qty*row_price_inc_exp) as total")
                                            );
            if($variation_id != null){
                $cost_less->where("variation_id",$variation_id);
            }

            $cost_less      = $cost_less->first();
                
            $FINAL["qty"]   = $cost_less->qty   ;
            $FINAL["total"] = $cost_less->total ;
            return $cost_less;
        }
        public static function getCostTotalQuantityCostReturn($signal,$product_id,$date,$id,$type,$trans_type,$variation_id=null) {
            $cost_less      =   ItemMove::where("transaction_id",$trans_type,$id)
                                        ->whereDate("date",$type,$date)
                                        ->where("signal",$signal)
                                        ->where("product_id",$product_id)
                                        ->select(
                                            DB::raw("SUM(qty) as qty"),
                                            DB::raw("SUM(qty*row_price_inc_exp) as total")
                                        );
            if($variation_id != null){
                $cost_less->where("variation_id",$variation_id);
            }

            $cost_less      = $cost_less->first();
                
            $FINAL["qty"]   = $cost_less->qty   ;
            $FINAL["total"] = $cost_less->total ;
            return $cost_less;
        }
    // ***************************
    // **END*****************END**
    /**
     * **************************************************************************** *
     *  14 Finish......... . . . . cost refresh    recieve . . . . ........   /     *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function cost_refresh($id,$product_id,$qty=1,$line_id=null,$date=null,$variation_id=null)
    {
        if($qty!=null){
            $costs = [];
            //...... for item cost  in item movement
            
            $cost_plus                  =  ItemMove::getCostTotalQuantity("+",$product_id,$date,$id,$variation_id); 
            $cost_minus                 =  ItemMove::getCostTotalQuantity("-",$product_id,$date,$id,$variation_id);   
            $cost_minus_production      =  ItemMove::where("signal","-")
                                                    ->whereDate("date","<=",$date)
                                                    ->where("id","<",$id)
                                                    ->where("product_id",$product_id)
                                                    ->select(
                                                    DB::raw("SUM(qty) as qty"),
                                                    DB::raw("SUM(IF(state = 'Manufacturing - ( Out )' OR state = 'sale' OR state = 'Wrong - sale<br>More Delivery'   OR state = 'Wrong - sale<br>Other Product',qty*out_price,0)) as total")
                                                    );
            if($variation_id!=null){
                $cost_minus_production->where('variation_id',$variation_id);
            }
            $cost_minus_production      =  $cost_minus_production->first();
            $cost_minus_return          =  ItemMove::getCostTotalQuantityList("-",$product_id,$date,["purchase_return","Wrong - purchase_return<br>More Received","Wrong - purchase_return<br>Other Product"],$id,$variation_id);
            $cost_minus_sale            =  ItemMove::getCostTotalQuantityListOutPrice("-",$product_id,$date,["sale"],$id,$variation_id);
            
                                                        
                                                                                                      
        }else{
            $costs = [];
            //...... for item cost  in item movement
            $cost_plus                  =  ItemMove::getCostTotalQuantity("+",$product_id,$date,$id,$variation_id);
            $cost_minus                 =  ItemMove::getCostTotalQuantity("-",$product_id,$date,$id,$variation_id); 
            $cost_minus_production      =  ItemMove::where("signal","-")
                                                    ->whereDate("date","<=",$date)
                                                    ->where("product_id",$product_id)
                                                    ->where("id","<",$id)
                                                    ->select(
                                                    DB::raw("SUM(qty) as qty"),
                                                    DB::raw("SUM(IF(state = 'Manufacturing - ( Out )' OR state = 'sale' OR state = 'Wrong - sale<br>More Delivery'  OR state = 'Wrong - sale<br>Other Product',qty*out_price,0)) as total")
                                                    );
            if($variation_id!=null){
                $cost_minus_production->where('variation_id',$variation_id);
            }
            $cost_minus_production      =  $cost_minus_production->first();
            $cost_minus_return          =  ItemMove::getCostTotalQuantityList("-",$product_id,$date,["purchase_return","Wrong - purchase_return<br>More Received","Wrong - purchase_return<br>Other Product"],$id,$variation_id); 
            $cost_minus_sale            =  ItemMove::getCostTotalQuantityListOutPrice("-",$product_id,$date,["sale"],$id,$variation_id);
        }
         
        $total_qty          = ($cost_plus["qty"]  -  $cost_minus["qty"])   ; 
        $total_price        = $cost_plus["total"] -  $cost_minus_return->total    - $cost_minus_production->total ;  
        $total_final        = $cost_plus["qty"]   -  $cost_minus_return->qty;
        $FINAL_COST_final   = ($total_final != 0)? (($cost_plus["total"]  -  $cost_minus_return->total)) : 0;
        $FINAL_COST         = ($total_qty!=0)? ($total_price / $total_qty) : 0;
         
        $costs[]      = $total_qty; //1
        $costs[]      = $FINAL_COST;//3750   
        $costs[]      = $total_price;//3750   
        $costs[]      = $FINAL_COST_final;//3750   
        $costs[]      = $total_final;//2  
         
        return     $costs;                                                                                     
    }
    // ***************************
    // **** COST REFRESH DATA ****
    // ***************************
        public static function getCostTotalQuantityList($signal,$product_id,$date,$list_status,$id,$variation_id=null) {
            $cost       =  ItemMove::where("signal",$signal)
                                    ->where("product_id",$product_id)
                                    ->whereIn("state",$list_status)
                                    ->whereDate("date","<=",$date)
                                    ->where("id","<",$id)
                                    ->select(
                                        DB::raw("SUM(qty) as qty"),
                                        DB::raw("SUM(qty*row_price_inc_exp) as total")
                                    );
            if($variation_id != null){
                $cost->where("variation_id",$variation_id);
            }

            $cost       =  $cost->first();
            return $cost;
        }
        public static function getCostTotalQuantityListOutPrice($signal,$product_id,$date,$list_status,$id,$variation_id=null) {
            $cost       =  ItemMove::where("signal",$signal)
                                    ->where("product_id",$product_id)
                                    ->whereIn("state",$list_status)
                                    ->where("id","<",$id)
                                    ->whereDate("date","<=",$date)
                                    ->select(
                                        DB::raw("SUM(qty) as qty"),
                                        DB::raw("SUM(qty*out_price) as total")
                                    );
            if($variation_id != null){
                $cost->where("variation_id",$variation_id);
            }

            $cost       =  $cost->first();
            return $cost;
        }
        public static function getCostTotalQuantity($signal,$product_id,$date,$id,$variation_id=null) {
            $list =  ($signal == "-")?["Stock_Out","Stock_In"]:["Stock_Out","Stock_In"];
            $cost_less       =  ItemMove::where("signal",$signal)
                                    ->where("product_id",$product_id)
                                    ->whereNotIn("state",$list)
                                    ->whereDate("date","<",$date)
                                    ->select(
                                        DB::raw("SUM(qty) as qty"),
                                        DB::raw("SUM(qty*row_price_inc_exp) as total")
                                    );
            if($variation_id!=null){
                $cost_less->where('variation_id',$variation_id);
            }
            $cost_less       =  $cost_less->first();
             
            $cost_more       =  ItemMove::where("signal",$signal)
                                    ->where("product_id",$product_id)
                                    ->whereNotIn("state",$list)
                                    ->whereDate("date","=",$date)
                                    ->where("id","!=",$id)
                                    ->where("id","<",$id)
                                    ->select(
                                        DB::raw("SUM(qty) as qty"),
                                        DB::raw("SUM(qty*row_price_inc_exp) as total")
                                    );

            if($variation_id!=null){
                $cost_more->where('variation_id',$variation_id);
            }
            $cost_more     =  $cost_more->first();

            $li_more       =  ItemMove::where("signal",$signal)
                                    ->where("product_id",$product_id)
                                    ->whereNotIn("state",$list)
                                    ->whereDate("date","=",$date)
                                    ->where("id","!=",$id)
                                    ->where("order_id","<",)
                                    ->where("id",">",$id)
                                    ->select(
                                        DB::raw("SUM(qty) as qty"),
                                        DB::raw("SUM(qty*row_price_inc_exp) as total")
                                    );

            if($variation_id!=null){
                $li_more->where('variation_id',$variation_id);
            }
            $li_more       =  $li_more->first();

            $FINAL["qty"]   = $cost_less->qty   + $cost_more->qty  ;
            $FINAL["total"] = $cost_less->total + $cost_more->total;
            return $FINAL;
        }
    // ***************************
    // **END*****************END**
    
    /**
     * **************************************************************************** *
     *  15 Finish........ . . . . cost   refresh   upper  . . . . .........    /    *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function cost_refresh_upper($id,$product_id,$qty=null,$date,$variation_id=null)
    {
        if($qty != null){
            $costs = [];
            //...... for item cost  in item movement
            $cost_plus                  = ItemMove::getCostTotalQuantityUpper("+",$product_id,$date,$id,">=",$variation_id);
            $cost_minus                 = ItemMove::getCostTotalQuantityUpper("-",$product_id,$date,$id,">=",$variation_id);
            $cost_minus_production      = ItemMove::getCostTotalQuantityListOutPrice("-",$product_id,$date,["sale"],">=",$variation_id);
        }else{
            $costs = [];
            //...... for item cost  in item movement
            $cost_plus                  = ItemMove::getCostTotalQuantityUpper("+",$product_id,$date,$id,">",$variation_id);
            $cost_minus                 = ItemMove::getCostTotalQuantityUpper("-",$product_id,$date,$id,">",$variation_id);
            $cost_minus_production      = ItemMove::getCostTotalQuantityListOutPrice("-",$product_id,$date,["sale"],">",$variation_id);
        
        }
        $total_qty    = ( $cost_plus["qty"] - $cost_minus["qty"] )   ; 
        $total_price  = $cost_plus["total"] - $cost_minus_production["total"] ;  
        $FINAL_COST   = ($total_qty!=0)? ($total_price / $total_qty) : 0;
        $costs[]      = $total_qty;
        $costs[]      = $FINAL_COST;   
        $costs[]      = $total_price;   
        return     $costs;                                                                                     
    }
    // *********************************
    // **** COST REFRESH UPPER DATA ****
    // *********************************
        public static function getCostTotalQuantityListUpper($signal,$product_id,$date,$list_status,$compare,$variation_id=null) {
            $cost       =  ItemMove::where("signal",$signal)
                                    ->where("product_id",$product_id)
                                    ->whereIn("state",$list_status)
                                    ->whereDate("date",$compare,$date)
                                    ->select(
                                        DB::raw("SUM(qty) as qty"),
                                        DB::raw("SUM(IF(state = 'Manufacturing - ( Out )' OR state = 'sale',qty*out_price,0)) as total")
                                        );
            if($variation_id != null){
                $cost->where("variation_id",$variation_id);
            }

            $cost       =  $cost->first();
            return $cost;
        }
        public static function getCostTotalQuantityListOutPriceUpper($signal,$product_id,$date,$list_status,$compare,$variation_id=null) {
            $cost       =  ItemMove::where("signal",$signal)
                                    ->where("product_id",$product_id)
                                    ->whereIn("state",$list_status)
                                    ->whereDate("date",$compare,$date)
                                    ->select(
                                        DB::raw("SUM(qty) as qty"),
                                        DB::raw("SUM(IF(state = 'Manufacturing - ( Out )' OR state = 'sale',qty*out_price,0)) as total")
                                        );
            if($variation_id != null){
                $cost->where("variation_id",$variation_id);
            }

            $cost       =  $cost->first();
            return $cost;
        }
        public static function getCostTotalQuantityUpper($signal,$product_id,$date,$id,$compare,$variation_id=null) {
            $cost_less       =  ItemMove::where("signal",$signal)
                                    ->where("product_id",$product_id)
                                    ->whereDate("date",">",$date)
                                    ->select(
                                        DB::raw("SUM(qty) as qty"),
                                        DB::raw("SUM(qty*row_price_inc_exp) as total")
                                    );
            if($variation_id != null){
                $cost_less->where("variation_id",$variation_id);
            }

            $cost_less       =  $cost_less->first();
            $cost_more       =  ItemMove::where("signal",$signal)
                                    ->where("product_id",$product_id)
                                    ->whereDate("date","=",$date)
                                    ->where("id","!=",$id)
                                    ->where("id",">",$id)
                                    ->select(
                                        DB::raw("SUM(qty) as qty"),
                                        DB::raw("SUM(qty*row_price_inc_exp) as total")
                                    );
            if($variation_id != null){
                $cost_more->where("variation_id",$variation_id);
            }

            $cost_more       =  $cost_more->first();
            $FINAL["qty"]   = $cost_less->qty   + ($compare != ">=")?0:$cost_more->qty;
            $FINAL["total"] = $cost_less->total + ($compare != ">=")?0:$cost_more->total;
            return $FINAL;
        }
    // ***************************
    // **END*****************END**
    /**
     * **************************************************************************** *
     *  16 Finish......... . . .  sale   - ' -  delivery . . . . .........     /    *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function create_sell_itemMove($source,$combo=null,$service=null)
    { 
        
        $sell                = \App\TransactionSellLine::where("transaction_id",$source->id)->get();
        $account             = \App\Account::where("contact_id",$source->contact_id)->first();
        // $delivery            = \App\Models\DeliveredPrevious::where("transaction_id",$source->id)->get();
        if($service!=null ){
            $delivery            = \App\Models\DeliveredPrevious::where("transaction_id",$source->id)
                                                                ->whereHas("T_delivered",function($q){
                                                                    $q->where("status","=","Service Item");
                                                                })->get();
        }else{
            $delivery            = \App\Models\DeliveredPrevious::where("transaction_id",$source->id)
                                                                ->whereHas("T_delivered",function($q){
                                                                    $q->where("status","!=","Service Item");
                                                                })->get();
        }
        $line_id             = [];
        $product_id_         = [];
        $move_id             = [];  
        if(count($delivery)>0){
            foreach($delivery as   $del){
                
                if(!in_array($del->product_id,$line_id)){
                    $line_id[]     = $del->product_id;
                    $product_id_[] = $del->product_id;
                }

                $itemMove = ItemMove::where("transaction_id",$source->id)->where("recieve_id",$del->id)->first();
                if(!empty($itemMove)){
                   $move_id[] = $itemMove->id; 
                }

                //...... for second price in item movement
                $product_id  = $del->product_id;
                $qty_product = $del->current_qty;
                
                ///... get  sell price cost
                $row_sell_price_all = \App\TransactionSellLine::where("transaction_id",$source->id)
                                                             ->select(
                                                                DB::raw("SUM(quantity*unit_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();
                ///... get  sell price cost
                $row_sell_price = \App\TransactionSellLine::where("transaction_id",$source->id)
                                                            ->where("product_id",$product_id)
                                                            ->select(
                                                                DB::raw("SUM(quantity*unit_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();
                ///... choice the cost 
                if( !empty($row_sell_price) ){
                    ($row_sell_price->qty != 0)? $row_cost = ( $row_sell_price->total_row_price / $row_sell_price->qty ) :  $row_cost = 0;
                    $r = floatval(number_format($row_cost, 0,',',''));;
                    $row_cost = $r;
                    if ($source->discount_type == "fixed_before_vat"){
                        $dis = $source->discount_amount;
                    }else if ($source->discount_type == "fixed_after_vat"){
                        $tax = \App\TaxRate::find($source->tax_id);
                        $dis = ($source->discount_amount*100)/(100+$tax->amount) ;
                    }else if ($source->discount_type == "percentage"){
                        $dis = ($source->total_before_tax *  $source->discount_amount)/100;
                    }else{
                        $dis = 0;
                    }
                    if( !empty($row_sell_price_all) ){
                        ///... choice the cost 
                        $format = floatval(number_format($row_sell_price_all->total_row_price, 0,',',''));;
                        if($format != 0){
                            $percent            =  $dis / $format;
                        }else{
                            $percent            =  0;
                        }
                        $format_row = floatval(number_format($row_sell_price->total_row_price, 0,',',''));;
                        if($row_sell_price->qty != 0){
                            $additional_prices  =  $percent * ( $format_row / $row_sell_price->qty);
                            $D_final =   floatval(number_format(($format_row / $row_sell_price->qty), 0,',',''));
                        }else{
                            $additional_prices  =  $percent * 0;
                            $D_final =   floatval(number_format(0, 0,',',''));
                            
                        }
                        $row_cost_exp       =  $D_final - $additional_prices      ;
                     }else{
                        $row_cost_exp = $row_cost ;
                    }
                }else{ 
                    $row_cost = \App\Product::product_cost($product_id);
                    $row_cost_exp = $row_cost ;
                }
                $prices       = [];
                $prices[]     = $row_cost;
                $prices[]     = $row_cost_exp;
                if(empty($itemMove)){
                    // *** AGT8422 SAVE RECEIVED ITEMS
                    ItemMove::saveItemMove("delivery",$account,$itemMove,$source,$del->line,0,$del->line_id,$del,$prices);
                     // *********************************** 
                    if($del->product->type == "combo"){
                        //.. ***************** create movement *************** ..\\
                        $item                     = new ItemMove();
                        
                        $item->business_id        = $source->business_id;
                        $item->account_id         = $account->id;
                        $item->product_id	      = $del->product_id;
                        $item->state	          = $source->type;
                        $item->ref_no             = $source->invoice_no;
                        $item->qty                = $del->current_qty;
                        $item->signal             = "+";
                        $item->row_price          = $row_cost ;
                        $item->row_price_inc_exp  = $row_cost_exp ;
                        $item->unit_cost          = $FINAL_COST;
                        $item->current_qty        = $total_qty + $del->current_qty;
                        $item->transaction_id     = $source->id ;
                        $item->recieve_id         = $del->id ;
                        $item->out_price          = $FINAL_COST ;
                        $item->date               = $del->T_delivered->date ;
                        //** ... NEW ITEMS
                            $item->store_id       = $del->store_id  ;
                            $item->transaction_rd_id  = $del->transaction_recieveds_id ;
                        //** ...
                        $item->save();
                    }
                }else{
                    // *** AGT8422 UPDATE RECEIVED ITEMS
                    ItemMove::updateItemMove("delivery",$account,$itemMove,$source,$del->line,0,$del->line_id,$move_id,$del,$prices);
                    // ***********************************
                }
            }
        }  
         
    }
     /**
     * **************************************************************************** *
     *  17      ......... . . . . R/sale  - ' - delivery . . . . .........     /    *
     *          ................. *********************** .................         *
     * **************************************************************************** *
     */
    public static function return_sale_delivery($source,$type=null,$list_product_sending=null)
    { 
        $tr                  = \App\Transaction::where("id",$source->return_parent_id)->first();
        $sell                = \App\TransactionSellLine::where("transaction_id",$tr->id)->get();
        $account             = \App\Account::where("contact_id",$source->contact_id)->first();
        $id_source           = ($type != null)?$source->return_parent_id:$source->id;
        $delivery            = \App\Models\DeliveredPrevious::where("transaction_id",$id_source);
        if($type != null){
            $delivery->whereNotNull("is_returned");
        }else{
            $delivery->whereNull("is_returned");
        }
        if($list_product_sending != null){
            $delivery->whereIn("product_id",$list_product_sending);
        }
        $delivery            = $delivery->get();
        $line_id             = [];
        $product_id_         = [];
        $move_id             = [];  
    
        if(count($delivery)>0){ 
            foreach($delivery as $del){

                if(!in_array($del->product_id,$line_id)){
                    $line_id[]     = $del->product_id;
                    $product_id_[] = $del->product_id;
                }
                $itemMove = ItemMove::where("transaction_id",$source->id)->where("recieve_id",$del->id)->first();
                if(!empty($itemMove)){
                    $move_id[] = $itemMove->id; 
                }
                //...... for second price in item movement
                $product_id  = $del->product_id;
                $qty_product = $del->current_qty;
                
                ///... get  sell price cost
                $row_sell_price_all = \App\TransactionSellLine::where("transaction_id",$tr->id)
                                                             ->select(
                                                                DB::raw("SUM(quantity_returned*bill_return_price) as total_row_price"),
                                                                DB::raw("SUM(quantity_returned) as qty")
                                                            )->first();
                ///... get  sell price cost
                $row_sell_price = \App\TransactionSellLine::where("transaction_id",$tr->id)
                                                            ->where("product_id",$product_id)
                                                            ->select(
                                                                DB::raw("SUM(quantity_returned*bill_return_price) as total_row_price"),
                                                                DB::raw("SUM(quantity_returned) as qty")
                                                            )->first();
               
                ///... choice the cost 
                if( !empty($row_sell_price) ){
                    ($row_sell_price->qty != 0)? $row_cost = ( $row_sell_price->total_row_price / $row_sell_price->qty ) :  $row_cost = 0;
                    $r = floatval(number_format($row_cost, 0,',',''));;
                    $row_cost = $r;
                    if ($source->discount_type == "fixed_before_vat"){
                        $dis = $source->discount_amount;
                    }else if ($source->discount_type == "fixed_after_vat"){
                        $tax = \App\TaxRate::find($source->tax_id);
                        $dis = ($source->discount_amount*100)/(100+$tax->amount) ;
                    }else if ($source->discount_type == "percentage"){
                        $dis = ($source->total_before_tax *  $source->discount_amount)/100;
                    }else{
                        $dis = 0;
                    }
                    if( !empty($row_sell_price_all) ){
                        ///... choice the cost 
                        $format = floatval(number_format($row_sell_price_all->total_row_price, 0,',',''));;
                        if($format != 0){
                            $percent            =  $dis / $format;
                        }else{
                            $percent            =  0;
                        }
                        $format_row             =   floatval(number_format($row_sell_price->total_row_price, 0,',',''));;
                        if($row_sell_price->qty != 0){
                            $additional_prices  =   $percent * ( $format_row / $row_sell_price->qty);
                            $D_final            =   floatval(number_format(($format_row / $row_sell_price->qty), 0,',',''));
                        }else{
                            $additional_prices  =   $percent * 0;
                            $D_final            =   floatval(number_format(0, 0,',',''));
                        }
                        $row_cost_exp       =   $D_final - $additional_prices      ;
                    }else{
                        $row_cost_exp = $row_cost ;
                    }
                }else{
                    
                    $row_cost     = \App\Product::product_cost($product_id);
                    $row_cost_exp = $row_cost ;
                }
                
                $prices   = [];
                $prices[] = $row_cost;
                $prices[] = $row_cost_exp; 
                if(empty($itemMove)){
                    // *** ADD SALE RETURN DELIVERED ITEMS
                    ItemMove::saveItemMove("deliveryx",$account,$itemMove,$source,$del->line,$row_cost_exp,$del->line_id,$del,$prices);
                    // ****************************************
                }else{
                    // *** AGT8422 UPDATE RETURN DELIVERED ITEMS
                    ItemMove::updateItemMove("deliveryx",$account,$itemMove,$source,$del->line,0,$del->line_id,$move_id,$del,$prices);
                    // *******************************************
                }
  
            }
        } 

         
    }
    /**
     * **************************************************************************** *
     *  18 Finish......... . . .  transaction connect     . . . . ........     /    *
     *           ................ *********************** ................          *
     * **************************************************************************** *
     */
    public function transaction()
    {
        return $this->belongsTo("\App\Transaction","transaction_id");
    }  
    /**
     * **************************************************************************** *
     *  19 Finish......... . . .  account     connect     . . . . .........   /     *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */ 
    public function account()
    {
        return $this->belongsTo("\App\Account","account_id");
    }  
    /**
     * **************************************************************************** *
     *  20 Finish......... . . .   product     connect     . . . . ........   /     *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */ 
    public function product()
    {
        return $this->belongsTo("\App\Product","product_id");
    }  
    /**
     * **************************************************************************** *
     *  21 Finish......... . . .  delete all purchase not connect . . . .   /       *
     *           ................ ******************************* .........         *
     * **************************************************************************** *
     */ 
    public static function delete_all_purchase_not_connect($id)
    {
        $purchase    = \App\PurchaseLine::whereHas("transaction" ,function($query) use($id){
                                                $query->where("business_id",$id);  
                                                $query->where("type","opening_stock");  
                                        })->get();
        foreach($purchase as $pli){
            $open  = \App\Models\OpeningQuantity::where("transaction_id",$pli->transaction_id)->first();
            if(empty($open)){
                $pli->delete();
            }
        }
    }
    /**
     * **************************************************************************** *
     *  22 Finish......... . . .  delete all movement not connect . . . . ......../ *
     *           ................ *********************** ******* ................. *
     * **************************************************************************** *
     */
    public static function delete_all_movement_not_connect($id)
    {
        $itemMove  = ItemMove::orderBy("id","asc")->where("business_id",$id)->get();
        $count     = 0;
        foreach($itemMove as $move){
            $count      = $move->id ;
            $product_id = $move->product_id ;
            if($move->line_id == null){
                $move->delete();
                ItemMove::refresh_item($count,$product_id);
            }
        }
    }

    /**
     * **************************************************************************** *
     *  23      ......... . . . . refresh item            . . . . .........   /     *
     *          ................. *********************** .................         *
     * **************************************************************************** *
     */
    public static function refresh_item($id,$product_id,$t=null,$ti=null,$cm=null,$variation_id=null){
        // $itemMove            = ItemMove::find($id);
        
        ItemMove::CollectAllRows($product_id);
        // if(empty($itemMove)){
        //     $date            = (isset($itemMove->date))?(($itemMove->date != null) ? $itemMove->date : $itemMove->created_at ):\Carbon::now();
        //     $itemMoveAfterId = ItemMove::whereDate("date","<=",$date)->where("product_id",$product_id);
        //     if($variation_id!=null){
        //         $itemMoveAfterId->where("variation_id",$variation_id);
        //     } 
        //     $itemMoveAfterId = $itemMoveAfterId->get();
        //     foreach($itemMoveAfterId as $move){
        //             if($variation_id != null){
        //                 $cost     =  ItemMove::cost_refresh($id,$product_id,null,null,$date,$variation_id);
        //             }else{
        //                 $cost     =  ItemMove::cost_refresh($id,$product_id,null,null,$date);
        //             }
                    
        //             if($cost != 0){
        //                 $total_price  = ($move->qty*$move->row_price_inc_exp) + $cost[2];
        //                 if($move->signal == "-"){
        //                     $total_qty    = $cost[0]  - $move->qty   ;
        //                     if($total_qty != 0){
        //                         if($cost[0] != 0){
        //                             $cost_final        = $cost[2]/$cost[0] ;
        //                         }else{
        //                             $cost_final        = 0 ;
        //                         }
        //                         $move->unit_cost   = $cost_final ; 
        //                         $move->current_qty = $total_qty ;
        //                     } 
        //                 }else{
        //                     $total_qty    = $cost[0] + $move->qty     ;
        //                     if($total_qty != 0){
        //                         $cost_final        = $total_price / $total_qty ;
        //                         $move->unit_cost   = $cost_final ; 
        //                         $move->current_qty = $total_qty ;
        //                     } 
        //                 }
        //             }else{
        //                 if($variation_id != null){
        //                     $cost_        =  ItemMove::cost_refresh_upper($id,$product_id,null,null,$variation_id);
        //                 }else{
        //                     $cost_        =  ItemMove::cost_refresh_upper($id,$product_id);
        //                 }
        //                 $total_price  = ($move->qty*$move->row_price_inc_exp) + $cost_[2];
        //                 if($move->signal == "-"){
        //                     $total_qty    = $cost_[0] - $move->qty     ;
        //                     if($total_qty != 0){
        //                         if($cost_[0] != 0){
        //                             $cost_final        = $cost_[2]/$cost_[0] ;
        //                         }else{
        //                             $cost_final        = 0 ;
        //                         }
        //                         $move->unit_cost   = $cost_final ; 
        //                         $move->current_qty = $total_qty ;
        //                     } 
        //                 }else{
        //                     $total_qty    = $cost_[0] + $move->qty    ;
        //                     if($total_qty != 0){
        //                         $cost_final        = $total_price / $total_qty ;
        //                         $move->unit_cost   = $cost_final ; 
        //                         $move->current_qty = $total_qty ;
        //                     } 
        //                 }
        //             }
        //             $move->update();
        //         } 
        // }else{
        //     $date            = ($itemMove->date != null) ? $itemMove->date : $itemMove->created_at ; 
        //     if($cm != null){
               
        //         $itemMoveAfterIdBefore = ItemMove::orderBy("date","asc")    
        //                                             ->orderBy("order_id","asc")
        //                                             ->where("id","<",$id)
        //                                             ->whereDate("date","<=",$date)
        //                                             ->where("product_id",$product_id);
                
        //         if($variation_id != null){
        //             $itemMoveAfterIdBefore->where("variation_id",$variation_id);
        //         }
        //         $itemMoveAfterIdBefore = $itemMoveAfterIdBefore->first();
                
        //         if(empty($itemMoveAfterIdBefore)){
        //             // .. .  take all lines after this line upper this date
        //             $itemMoveAfterIdFirst = ItemMove::orderBy("date","desc")
        //                                                 ->where("id","=",$id)
        //                                                 ->where("product_id",$product_id);
        //             if($variation_id != null){
        //                 $itemMoveAfterIdFirst->where("variation_id",$variation_id);
        //             }                                    
        //             $itemMoveAfterIdFirst = $itemMoveAfterIdFirst->get();
                    
        //             //... search in all lines 
        //             foreach($itemMoveAfterIdFirst as $move){
        //                 // .. . for take the info of current line
        //                 $qty  = "check"; 
        //                 if($variation_id != null){
        //                     //... item cost and total qty 
        //                     $cost =  ItemMove::cost_refresh($id,$product_id,$qty,null,$date,$variation_id);
                            
        //                 }else{
        //                     //... item cost and total qty 
        //                     $cost =  ItemMove::cost_refresh($id,$product_id,$qty,null,$date);
        //                 }
                      
        //                 if($cost[0] != 0){
        //                     if($move->signal == "-"){
        //                         if($move->state ==  "Stock_Out"){
        //                             $cost_final        = ItemMove::orderBy("date","desc")
        //                                                          ->orderBy("order_id","desc")
        //                                                          ->orderBy("id","desc")
        //                                                          ->where("product_id",$product_id)
        //                                                          ->whereDate("date","<=",$date)
        //                                                          ->whereNotIn("state",["Stock_Out","Stock_In"]);
        //                             if($variation_id != null){
        //                                 $cost_final->where("variation_id",$variation_id);
        //                             }
        //                             $cost_final = $cost_final->first() ;
                                    
        //                             $total_qty    = $cost[0]     ;
        //                             if($total_qty != 0){
        //                                 if($cost[0] == 0){
        //                                     $cost_final        = 0 ;
        //                                     $move->current_qty = $total_qty ;
        //                                 }else{
        //                                     $cost_final        = ($cost_final)?(($cost_final->unit_cost != 0)?$cost_final->unit_cost:$cost_final->out_price):0 ;
        //                                     $move->current_qty = $total_qty ;
        //                                 }
        //                                 $move->row_price   = $cost_final ; 
        //                                 $move->row_price_inc_exp   = $cost_final ; 
        //                                 $move->unit_cost   = $cost_final ; 
        //                                 $move->out_price   = $cost_final ; 
        //                             } else{
        //                                 $cost_final        = ($cost_final)?(($cost_final->unit_cost != 0)?$cost_final->unit_cost:$cost_final->out_price):0 ;
        //                                 $move->current_qty = 0 ;
        //                                 $move->row_price   = $cost_final ; 
        //                                 $move->row_price_inc_exp   = $cost_final ; 
        //                                 $move->out_price   = $cost_final ;
        //                             }
        //                         }else{
        //                             $total_qty    = $cost[0]     ;
        //                             if($total_qty != 0){
        //                                 if($cost[0] == 0){
        //                                     $cost_final        = 0 ;
        //                                     $move->current_qty = $total_qty ;
        //                                 }else{
        //                                     $cost_final        = $cost[2] / $cost[0] ;
        //                                     $move->current_qty = $total_qty ;
        //                                 }
        //                                 $move->unit_cost   = $cost_final ; 
        //                             } else{
        //                                 $cost_final        = ($cost_final)?(($cost_final->unit_cost != 0)?$cost_final->unit_cost:$cost_final->out_price):0 ;
        //                                 $move->current_qty = 0 ;
        //                                 $move->unit_cost   = $cost_final ; 
        //                                 $move->out_price   = ($cost_final)?$cost_final->unit_cost:0 ; 
        //                             }
        //                         } 
                                        
        //                     }else{
        //                         if($move->state ==  "Stock_In"){
        //                             $total_price  =   ($move->qty*$move->row_price_inc_exp) +  $cost[2];
        //                             $total_qty    =   $cost[0]      ;
        //                             if($total_qty != 0){
        //                                 $cost_final        = ItemMove::orderBy("date","desc")
        //                                                             ->orderBy("order_id","desc")
        //                                                             ->orderBy("id","desc")
        //                                                             ->where("product_id",$product_id)
        //                                                             ->where("transaction_id",$move->transaction_id-1)
        //                                                             ->where("id","<",$id);
        //                                 if($variation_id != null){
        //                                     $cost_final->where("variation_id",$variation_id);
        //                                 }
        //                                 $cost_final = $cost_final->first() ;
                                        
        //                                 $move->unit_cost   = ($cost_final)?(($cost_final->unit_cost != 0)?$cost_final->unit_cost:$cost_final->out_price):0 ; 
        //                                 $move->current_qty = $total_qty ;
        //                                 $move->row_price   = ($cost_final)?$cost_final->unit_cost:0 ; 
        //                                 $move->row_price_inc_exp   = ($cost_final)?$cost_final->unit_cost:0 ;  
        //                             } 
        //                         }else{
        //                             $total_price  =   ($move->qty*$move->row_price_inc_exp) +  $cost[2];
        //                             $total_qty    = $cost[0]  + $move->qty   ;
        //                                 if($total_qty != 0){
        //                                     if($cost[4] != 0){
        //                                     $cost_final        = $cost[3] /$cost[4] ;
                                            
        //                                 }else{
        //                                     $cost_final        = $total_price / $total_qty ;
        //                                 }
        //                                 $move->unit_cost   = round($cost_final,2) ; 
        //                                 $move->current_qty = round($total_qty,2) ;
                                        
        //                             } 
        //                         }
        //                     }
        //                 }else{
        //                     $cost_final        = $move->row_price_inc_exp ;
        //                     $move->unit_cost   = $move->row_price_inc_exp ; 
                            
        //                     $dates             = \App\Models\ItemMove::where("product_id",$move->product_id)
        //                                                             ->where("id","!=",$move->id)
        //                                                             ->orderBy("date","desc")
        //                                                             ->orderBy("id","desc"); 
                           
        //                     if($variation_id != null){
        //                         $dates->where("variation_id",$variation_id);
        //                     }
        //                     if($move->date != null){
        //                         $dates->where("date","<=",$move->date);
        //                     }
        //                     $dates             = $dates->get();
                            
                                    
        //                     $index              = null ;
        //                     $list_of_ids        = [];
        //                     $list_of_dates      = [];
        //                     foreach($dates as $iess){
        //                         $list_of_ids[]             = $iess->id; 
        //                         $list_of_dates[$iess->id]  = $iess->date; 
        //                     }
                            
        //                     if(count($list_of_ids)>0 ){
        //                         foreach($list_of_dates as $k => $ied){ 
        //                             if($move->date == $ied){
        //                                 if($k < $move->id){
        //                                     $index = $k; 
        //                                     break;
        //                                 }
        //                             }else{
        //                                 $index = $k; 
        //                                 break;
        //                             }
                                    
        //                         }
        //                         $item_move_b = \App\Models\ItemMove::find($index);
        //                         if(!empty($item_move_b)){
        //                             $move->current_qty = ($move->signal == "-")? (($item_move_b->current_qty - $move->qty )  ):( $item_move_b->current_qty + $move->qty ) ;
        //                         }else{
        //                             $move->current_qty = ($move->signal == "-")?($move->qty*-1):$move->qty ;
        //                         }
        //                     }else{
        //                         $move->current_qty = ($move->signal == "-")? ((-$move->qty)  ):( $move->qty ) ;
        //                     }
                            
        //                 }
        //                 $move->update();
        //             }
        //         }else{
        //             //... search in all lines 
        //             // .. . for take the info of current line
        //             $qty  = "check"; 
        //             if($variation_id != null){
        //                 //... . item cost and total qty 
        //                 $cost =  ItemMove::cost_refresh($id,$product_id,$qty,null,$date,$variation_id);
        //             }else{
        //                 //... . item cost and total qty 
        //                 $cost =  ItemMove::cost_refresh($id,$product_id,$qty,null,$date);
        //             }
                   
        //             if($cost[0] != 0){
        //                 $total_price  = ($itemMoveAfterIdBefore->qty*$itemMoveAfterIdBefore->row_price_inc_exp) + $cost[2];
        //                 if($itemMoveAfterIdBefore->signal == "-"){
        //                     if($itemMoveAfterIdBefore->state ==  "Stock_Out"){
        //                         $cost_final        = ItemMove::orderBy("date","desc")
        //                                                         ->orderBy("order_id","desc")
        //                                                         ->orderBy("id","desc")
        //                                                         ->where("product_id",$product_id)
        //                                                         ->whereDate("date","<=",$date)
        //                                                         ->whereNotIn("state",["Stock_Out","Stock_In"]);
        //                         if($variation_id != null){
        //                             $cost_final->where("variation_id",$variation_id);
        //                         }    
        //                         $cost_final        = $cost_final->first() ;
                               
        //                         $total_qty         = $cost[0] - $itemMoveAfterIdBefore->qty    ;
        //                         if($total_qty != 0){
        //                             if($cost[0] == 0){
        //                                 $cost_final                         = 0 ;
        //                                 $itemMoveAfterIdBefore->current_qty = round($total_qty,2) ;
        //                             }else{
        //                                 $cost_final        = ($cost_final)?(($cost_final->unit_cost != 0)?$cost_final->unit_cost:$cost_final->out_price):0 ;
        //                                 $itemMoveAfterIdBefore->current_qty = round($total_qty,2) ;
        //                             }
        //                             $itemMoveAfterIdBefore->unit_cost   = round($cost_final,2) ; 
        //                             $itemMoveAfterIdBefore->row_price   = round($cost_final,2) ; 
        //                             $itemMoveAfterIdBefore->row_price_inc_exp   = round($cost_final,2) ; 
        //                             $itemMoveAfterIdBefore->out_price   = round($cost_final,2) ; 
        //                         } else{
        //                             $cost_final                         = 0 ;
        //                             $itemMoveAfterIdBefore->current_qty = 0 ;
        //                             $itemMoveAfterIdBefore->row_price   = round($cost_final,2) ; 
        //                             $itemMoveAfterIdBefore->row_price_inc_exp   = round($cost_final,2) ; 
        //                             $itemMoveAfterIdBefore->unit_cost   = round($cost_final,2) ; 
        //                             $itemMoveAfterIdBefore->out_price   = round($cost_final,2) ; 

        //                         }
        //                     }else{
        //                         $total_qty    = $cost[0] - $itemMoveAfterIdBefore->qty    ;
        //                         if($total_qty != 0){
        //                             if($cost[0] == 0){
        //                                 $cost_final                         = 0 ;
        //                                 $itemMoveAfterIdBefore->current_qty = round($total_qty,2) ;
        //                             }else{
        //                                 $cost_final                         = $cost[2] / $cost[0] ;
        //                                 $itemMoveAfterIdBefore->current_qty = round($total_qty,2) ;
        //                             }
        //                             $itemMoveAfterIdBefore->unit_cost   = round($cost_final,2) ; 
        //                         } else{
        //                             $cost_final                         = 0 ;
        //                             $itemMoveAfterIdBefore->current_qty = 0 ;
        //                             $itemMoveAfterIdBefore->unit_cost   = round($cost_final,2) ; 
        //                         }
        //                     } 
        //                 }else{
        //                     if($itemMoveAfterIdBefore->state ==  "Stock_In"){
        //                         $total_qty    = $cost[0]    ;
        //                         if($total_qty != 0){
        //                             $cost_final                         = ItemMove::orderBy("date","desc")
        //                                                                         ->orderBy("order_id","desc")
        //                                                                         ->orderBy("id","desc")
        //                                                                         ->where("product_id",$product_id)
        //                                                                         ->where("transaction_id",$itemMoveAfterIdBefore->transaction_id-1)
        //                                                                         ->where("id","<",$id);
        //                             if($variation_id != null){
        //                                 $cost_final->where("variation_id",$variation_id);
        //                             }                                            
        //                             $cost_final                = $cost_final->first() ;
                                  
        //                             $itemMoveAfterIdBefore->unit_cost   = ($cost_final)?(($cost_final->unit_cost != 0)?$cost_final->unit_cost:$cost_final->out_price):0 ; 
        //                             $itemMoveAfterIdBefore->row_price   = ($cost_final)?(($cost_final->unit_cost != 0)?$cost_final->unit_cost:$cost_final->out_price):0; 
        //                             $itemMoveAfterIdBefore->row_price_inc_exp   = ($cost_final)?(($cost_final->unit_cost != 0)?$cost_final->unit_cost:$cost_final->out_price):0; 
        //                             $itemMoveAfterIdBefore->current_qty = $total_qty ;
        //                         } 
        //                     }else{
        //                         $total_qty    = $cost[0] + $itemMoveAfterIdBefore->qty    ;
        //                         if($total_qty != 0){
        //                             $cost_final                         = $total_price / $total_qty ;
        //                             $itemMoveAfterIdBefore->unit_cost   = round($cost_final,2) ; 
        //                             $itemMoveAfterIdBefore->current_qty = round($total_qty,2) ;
        //                         } 
        //                     }
        //                 }
        //             }else{
        //                 // $cost_        =  ItemMove::cost_refresh_upper($id,$product_id,$qty,$date);
        //                 // $total_price  = ($itemMoveAfterIdBefore->qty*$itemMoveAfterIdBefore->row_price_inc_exp) + $cost_[2];
        //                 // if($itemMoveAfterIdBefore->signal == "-"){
        //                 //     $total_qty    = $cost_[0] -  $itemMoveAfterIdBefore->qty    ;
        //                 //     if($total_qty != 0){
        //                 //         if($cost_[0] == 0){
        //                 //             $cost_final                         = 0 ;
        //                 //             $itemMoveAfterIdBefore->current_qty = $cost_[1] ;
        //                 //         }else{
        //                 //             $cost_final                         = $cost_[2] / $cost_[0] ;
        //                 //             $itemMoveAfterIdBefore->current_qty = $total_qty ;
        //                 //         }
        //                 //         $itemMoveAfterIdBefore->unit_cost   = $cost_final ; 
        //                 //     } 
        //                 // }else{
        //                 //     $total_qty     = $cost_[0] + $itemMoveAfterIdBefore->qty    ;
        //                 //     if($total_qty != 0){
        //                 //         $cost_final                         = $total_price / $total_qty ;
        //                 //         $itemMoveAfterIdBefore->unit_cost   = $cost_final ; 
        //                 //         $itemMoveAfterIdBefore->current_qty = $total_qty ;
        //                 //     } 
        //                 // }
        //                 $cost_final                         = round($itemMoveAfterIdBefore->row_price_inc_exp,2) ;
        //                 $itemMoveAfterIdBefore->unit_cost   = round($itemMoveAfterIdBefore->row_price_inc_exp,2) ; 
        //                 $itemMoveAfterIdBefore->current_qty = ($itemMoveAfterIdBefore->signal == "-")?round(($itemMoveAfterIdBefore->qty*-1),2):round($itemMoveAfterIdBefore->qty,2) ;
        //             }
        //             $itemMoveAfterIdBefore->update();
                                                              
        //         }
        //     }else{
        //         $id_move  = [];
        //         // .. .  take all lines after this line upper this date
        //         $itemMoveAfterId      = ItemMove::orderBy("date","ASC")
        //                                     ->where("id","=",$id)
        //                                     ->where("product_id",$product_id);
        //         if($variation_id != null){
        //             $itemMoveAfterId->where("variation_id",$variation_id);
        //         }
        //         $itemMoveAfterId      = $itemMoveAfterId->get();
                
        //         //... search in all lines 
               
        //         foreach($itemMoveAfterId as $key => $move){
        //             // .. . for take the info of current line
        //             $qty  = "check"; 
        //             if($variation_id != null){
        //                 //... . item cost and total qty 
        //                 $cost =  ItemMove::cost_refresh($id,$product_id,$qty,null,$date,$variation_id);
        //             }else{
        //                 //... . item cost and total qty 
        //                 $cost =  ItemMove::cost_refresh($id,$product_id,$qty,null,$date);
        //             }

        //             if($cost[0] != 0){
                        
        //                 if($move->signal == "-"){
        //                     if($move->state ==  "Stock_Out"){
        //                         $cost_final        = ItemMove::orderBy("date","desc")
        //                                                         ->orderBy("order_id","desc")
        //                                                         ->orderBy("id","desc")
        //                                                         ->where("product_id",$product_id)
        //                                                         ->whereDate("date","<=",$date)
        //                                                         ->whereNotIn("state",["Stock_Out","Stock_In"]);
        //                         if($variation_id != null){
        //                             $cost_final->where("variation_id",$variation_id);
        //                         }
        //                         $cost_final        = $cost_final->first() ;
                                
        //                         $total_qty         = $cost[0] - $move->qty    ;
        //                         if($total_qty != 0){
        //                             if($cost[0] == 0){
        //                                 $cost_final        = 0 ;
        //                                 $move->current_qty = round($total_qty,2) ;
        //                             }else{
        //                                 $cost_final        = ($cost_final)?(($cost_final->unit_cost != 0)?$cost_final->unit_cost:$cost_final->out_price):0 ;
        //                                 $move->current_qty = round($total_qty,2) ;
        //                             }
        //                             $move->unit_cost   = round($cost_final,2) ;
        //                             $move->row_price   = round($cost_final,2); 
        //                             $move->row_price_inc_exp   = round($cost_final,2); 
        //                             $move->out_price   = round($cost_final,2) ;
 
        //                         } else{
        //                             $cost_final        = ($cost_final)?(($cost_final->unit_cost != 0)?round($cost_final->unit_cost,2):round($cost_final->out_price,2)):0 ;
        //                             $move->current_qty = 0 ;
        //                             $move->unit_cost   = round($cost_final,2) ;
        //                             $move->row_price   = round($cost_final,2); 
        //                             $move->row_price_inc_exp   = round($cost_final,2);   
        //                             $move->out_price   = round($cost_final,2);   
        //                         }
        //                     }else{
                                
        //                         $total_qty    = $cost[0] - $move->qty    ;
                                
        //                         if($total_qty != 0){
        //                             if($cost[0] == 0){
        //                                 $cost_final        = 0 ;
        //                                 $move->current_qty = round($total_qty,2) ;
        //                             }else{
        //                                 if($move->state == "purchase_return"){
        //                                     $cost_final        = ($total_qty != 0)? $cost[2] - $move->qty*$move->row_price_inc_exp   / $total_qty : $move->qty*$move->row_price_inc_exp  ;
        //                                 }else{
        //                                     $cost_final        = ($cost[4] != 0)? $cost[3] / $cost[4]:$cost[3] ;
        //                                 }
        //                                 $move->current_qty = round($total_qty,2) ;
        //                             }
        //                             $move->unit_cost   = round($cost_final,2) ; 
        //                             $move->out_price   = round($cost_final,2) ; 
        //                         } else{
        //                             $cost_final        = 0 ;
        //                             $move->current_qty = 0 ;
        //                             $move->unit_cost   = ($cost[4] != 0)?round( ($cost[3] / $cost[4]),2):round($cost[3],2) ; 
        //                             $move->out_price   = ($cost[4] != 0)?round(( $cost[3] / $cost[4]),2):round($cost[3],2) ;
        //                         }
        //                     } 
        //                 }else{
                             
        //                     if($move->state ==  "Stock_In"){
                                
        //                         $total_qty    = $cost[0]      ;
        //                         if($total_qty != 0){
        //                             $cost_final        = ItemMove::orderBy("date","desc")
        //                                                             ->orderBy("order_id","desc")
        //                                                             ->orderBy("id","desc")
        //                                                             ->where("product_id",$product_id)
        //                                                             ->where("transaction_id",$move->transaction_id-1)
        //                                                             ->where("id","<",$id);
        //                             if($variation_id != null){
        //                                 $cost_final->where("variation_id",$variation_id);
        //                             }       
        //                             $cost_final        = $cost_final->first() ;
                               
        //                             $move->unit_cost   = ($cost_final)?$cost_final->unit_cost:0 ; 
        //                             $move->current_qty = $total_qty ;
        //                             $move->row_price   = ($cost_final)?$cost_final->unit_cost:0 ; 
        //                             $move->row_price_inc_exp   = ($cost_final)?$cost_final->unit_cost:0 ;  
        //                         } 
        //                     }else{
        //                         $total_price  = ($move->qty*$move->row_price_inc_exp) + $cost[2];
        //                         $total_qty    = $cost[0] + $move->qty    ;
        //                             // if($product_id == 1210){
        //                             //     // dd($move);
        //                             //     if($id == 2968){
        //                             //         dd($total_qty);
        //                             //     }
        //                             // }
        //                         if($total_qty != 0){
        //                             if($cost[4] != 0 && $move->state !=  "purchase" && $move->state !=  "opening_stock"){
        //                                 $cost_final        = $cost[3] /$cost[4] ;
                                        
        //                             }else{
        //                                 $cost_final        = $total_price / $total_qty ;
        //                             }
        //                             $move->unit_cost   = round($cost_final,2) ; 
        //                             $move->current_qty = round($total_qty,2) ;
                                    
        //                         }else{
        //                             $cost_final        = round($move->row_price_inc_exp,2)  ;
        //                             $move->unit_cost   = round($cost_final,2) ; 
        //                             $move->current_qty = round($total_qty,2) ;  
                                    
        //                         }
        //                     }
        //                 }
                        
        //             }else{
                        
        //                 // $cost_ =  ItemMove::cost_refresh_upper($id,$product_id,$qty,$date);
        //                 // $total_price  = ($move->qty*$move->row_price_inc_exp) + $cost_[2];
        //                 // if($move->signal == "-"){
        //                 //     $total_qty    = $cost_[0] -  $move->qty    ;
        //                 //     if($total_qty != 0){
        //                 //         if($cost_[0] == 0){
        //                 //             $cost_final        = 0 ;
        //                 //             $move->current_qty = $cost_[1] ;
        //                 //         }else{
        //                 //             $cost_final        = $cost_[2] / $cost_[0] ;
        //                 //             $move->current_qty = $total_qty ;
        //                 //         }
        //                 //         $move->unit_cost   = $cost_final ; 
        //                 //     } 
        //                 // }else{
        //                 //     $total_qty     = $cost_[0] + $move->qty    ;
        //                 //     if($total_qty != 0){
        //                 //         $cost_final        = $total_price / $total_qty ;
        //                 //         $move->unit_cost   = $cost_final ; 
        //                 //         $move->current_qty = $total_qty ;
        //                 //     } 
        //                 // }
                        
        //                 $cost_final        = $move->row_price_inc_exp ;
        //                 $move->unit_cost   = ($cost[4] != 0)? $cost[3] / $cost[4]:$cost[3];
        //                 $dte               = ($move->date != null)?$move->date:$move->created_at;
        //                 $dates             = \App\Models\ItemMove::where("product_id",$move->product_id)
        //                                                             ->where("date","<=",$dte)
        //                                                             ->where("id","!=",$move->id)
        //                                                             ->orderBy("date","desc")
        //                                                             ->orderBy("id","desc");
        //                 if($variation_id != null){
        //                     $dates->where("variation_id",$variation_id);
        //                 }                                    
        //                 $dates             = $dates->get();
        //                 // if($id == 6611){
                                
        //                     $index             = null ;
        //                     $list_of_ids       = [];
        //                     $list_of_dates      = [];
        //                     foreach($dates as $iess){
        //                         $list_of_ids[]       = $iess->id; 
        //                         $list_of_dates[$iess->id]     = $iess->date; 
        //                     }
        //                     if(count($list_of_ids)>0 ){
                         
        //                         foreach($list_of_dates as $k => $ied){ 
        //                             if($move->date == $ied){
        //                                 if($k < $move->id){
        //                                     $index = $k; 
        //                                     break;
        //                                 }
        //                             }else{
        //                                 $index = $k; 
        //                                 break;
        //                             }
                                    
        //                         }
                               
        //                         $item_move_b = \App\Models\ItemMove::find($index);
        //                         if(!empty($item_move_b)){
        //                             $move->current_qty = ($move->signal == "-")? round((($item_move_b->current_qty - $move->qty )  ),2):round(( $item_move_b->current_qty + $move->qty ),2) ;
        //                         }else{
        //                             $move->current_qty = ($move->signal == "-")?round(($move->qty*-1),2):round($move->qty,2) ;
        //                         }
        //                     }else{
                                
        //                             // $move->current_qty = ($move->signal == "-")?($move->qty*-1):$move->qty ;
                                
        //                     }
        //                 // }
                        
                        
                        
        //             }
        //             $move->update(); 
                  
        //         }
                
                
        //     }
        // }
        
    }
    /**
     * **************************************************************************** *
     *  24 Finish......... . . .    delete move            . . . . ......... /      *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function delete_move($transaction_id)
    {
        $item = ItemMove::where("transaction_id",$transaction_id)->get();
        foreach($item as $it){
            $it->delete();
        }
    }
    /**
     * **************************************************************************** *
     *  25 Finish......... . . .  delete line             . . . . .........      /  *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function delete_line($transaction_id,$line_id)
    {
        $item = ItemMove::where("transaction_id",$transaction_id)->where("line_id",$line_id)->first();
        if($item){
            $item->delete();
        }
        
    }
    /**
     * **************************************************************************** *
     *  26 Finish........ . . . . delete recieve          . . . . .........   /     *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function delete_recieve($transaction_id,$line_id,$recieve)
    {
        $array_del      = [];
        $line_id_       = [];
        $product_id     = [];
        $move_id        = []; 
        if(!in_array($recieve->product_id,$line_id_)){
           $line_id_[]    = $recieve->product_id;
           $product_id[]  = $recieve->product_id;
        }
        $wrongMove = \App\Models\ItemMove::where("transaction_id",$transaction_id)->where("recieve_id",$recieve->id)->first();
         if(!empty($wrongMove)){
           $move_id[] = $wrongMove->id; 
        }
        if(!empty($wrongMove)){
            $date = ($wrongMove->date != null) ? $wrongMove->date : $wrongMove->created_at  ; 
            $wrongMove->delete();
            ItemMove::updateRefresh($wrongMove,$recieve,$move_id,$date);
        }
    }
    /**
     * **************************************************************************** *
     *  27 Finish........ . . . . delete delivery         . . . . ......... /       *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function delete_delivery($transaction_id,$line_id,$recieve)
    { 
        $array_del      = [];
        $line_id_       = [];
        $product_id     = [];
        $move_id        = []; 
        if(!in_array($recieve->product_id,$line_id_)){
           $line_id_[]    = $recieve->product_id;
           $product_id[] = $recieve->product_id;
        }
        $wrongMove = \App\Models\ItemMove::where("transaction_id",$transaction_id)->where("recieve_id",$recieve->id)->first();
         if(!empty($wrongMove)){
           $move_id[] = $wrongMove->id; 
        }
        if(!empty($wrongMove)){
            $date = ($wrongMove->date != null) ? $wrongMove->date : $wrongMove->created_at  ; 
            $wrongMove->delete();
            ItemMove::updateRefresh($wrongMove,$recieve,$move_id,$date);
        }
    }
    /**
     * **************************************************************************** *
     *  28 Finish......... . . . .  production            . . . . .........    /    *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */
    public static function production($transaction_sell,$transaction_purchase,$wastage)
    { 
        //-1-//...... For Sale
        $tr_sell        = \App\Transaction::find($transaction_sell);
        $items_sell     = $tr_sell->sell_lines->pluck("id");
        $ids_delete     = \App\Models\ItemMove::where("transaction_id",$tr_sell->id)->whereNotIn("sells_line_id",$items_sell)->get();
        if(count($ids_delete)>0){
            foreach($ids_delete as $it){
                $it->delete();
            }
        }
        $ids_active     = \App\TransactionSellLine::where("transaction_id",$tr_sell->id)->get();
        $account        = \App\Account::where("contact_id",$tr_sell->contact_id)->first();
        $line_id        = [];$product_id     = [];
        $move_id        = [];$move_id_       = []; 
        $line_id_       = [];$product_id_    = [];
        foreach($ids_active as $it){
            $line = \App\Models\ItemMove::where("sells_line_id",$it->id)->where("product_id",$it->product_id)->first();
            ///... get  sale price cost
            $row_sell_price = \App\TransactionSellLine::where("transaction_id",$tr_sell->id)
                                                        ->where("product_id",$it->product_id)
                                                        ->select(
                                                            DB::raw("SUM(quantity*unit_price) as total_row_price"),
                                                            DB::raw("SUM(quantity) as qty")
                                                        )->first();
            ///... choice the cost 
            if( !empty($row_sell_price) ){
                ($row_sell_price->qty != 0)? $row_cost = ( $row_sell_price->total_row_price / $row_sell_price->qty ) :  $row_cost = 0;
            }else{
                $row_cost = \App\Product::product_cost($it->product_id);
            }
            $prices   = [];
            $prices[] = $row_cost;
            if(empty($line)){
                // *** AGT8422 SAVE SALE PRODUCTION ITEMS
                ItemMove::saveItemMove("production",null,$line,$tr_sell,$it,0,$it->id,$it,$prices);
                // *********************************** 
            }else{
                // *** AGT8422 UPDATE SALE PRODUCTION ITEMS
                ItemMove::updateItemMove("production",null,$line,$tr_sell,$it,0,$it->id,$move_id,$it,$prices);
                // *********************************** 
            }
        }
        //-2-//...... For Purchase
        $tr_purchase     = \App\Transaction::find($transaction_purchase);
        $items_purchase  = $tr_purchase->purchase_lines->pluck("id");
        $ids_delete_pr   = \App\Models\ItemMove::where("transaction_id",$tr_purchase->id)->whereNotIn("purchase_line_id",$items_purchase)->get();
        if(count($ids_delete_pr)>0){
            foreach($ids_delete_pr as $it){
                $it->delete();
            }
        }
        $ids_active_pr   = \App\PurchaseLine::where("transaction_id",$tr_purchase->id)->get();
        $account         = \App\Account::where("contact_id",$tr_purchase->contact_id)->first();
        foreach($ids_active_pr as $it){
            if(!in_array($it->product_id,$line_id_)){
                $line_id_[]    = $it->product_id;
                $product_id_[] = $it->product_id;
            }
            $line = \App\Models\ItemMove::where("purchase_line_id",$it->id)->where("product_id",$it->product_id)->first();
            if(!empty($line)){
                $move_id_[] = $line->id; 
            }
            ///... get  purchase price cost
            $row_purchase_price = \App\PurchaseLine::where("transaction_id",$tr_purchase->id)
                                                        ->where("product_id",$it->product_id)
                                                        ->select(
                                                    DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                    DB::raw("SUM(quantity) as qty")
                                                    )->first();
            ///... choice the cost 
            if( !empty($row_purchase_price) ){
                ($row_purchase_price->qty != 0)? $row_cost = ( $row_purchase_price->total_row_price / $row_purchase_price->qty ) :  $row_cost = 0;
            }else{
                $row_cost = \App\Product::product_cost($it->product_id);
            }
            $prices       = [];
            $prices[]     = $row_cost;
            $prices[]     = $wastage;
            if(empty($line)){
                 // *** AGT8422 SAVE PURCHASE PRODUCTION ITEMS
                 ItemMove::saveItemMove("purchase_production",null,$line,$tr_purchase,$it,$row_cost,$it->id,$it,$prices);
                 // *******************************************             
            }else{
                // *** AGT8422 UPDATE PURCHASE PRODUCTION ITEMS
                ItemMove::updateItemMove("purchase_production",null,$line,$tr_purchase,$it,$row_cost,$it->id,$move_id,$it,$prices);
                // ********************************************     
            }
        }
    }

    /**
     * **************************************************************************** *
     *  29 Finish........ . . . . wrong purchase receive . . . . .........     /    *
     *           ................ ********************** .................          *
     * **************************************************************************** *
     */
    
    
    public static function Wrong_recieve($transaction,$transaction_r)
    {
        $tr                        = \App\Transaction::find($transaction);
        $account                   = \App\Account::where("contact_id",$tr->contact_id)->first();
        $purchase_line             = \App\PurchaseLine::where("transaction_id",$tr->id)->get();
        $wrongReceive              = \App\Models\RecievedWrong::where("transaction_id",$tr->id)->get();
        $row                       = \App\Models\ItemMove::subtotal_recieve_correct($tr->id,$transaction_r);
        $sub_total_in_recieve      = 0;
        foreach($row as $it){
            $qty                   = $it[2];
            $price                 = $it[1];
            $total_                = $it[2] * $it[1];
            $sub_total_in_recieve += $total_; 
        }
        $total_receive_price       =  $sub_total_in_recieve;
        foreach($wrongReceive as $it_wrong){
            ///... get  product info
            $product_id  = $it_wrong->product_id;
            $qty_product = $it_wrong->current_qty;
            ///... get  one product
            $row_purchase_one = \App\PurchaseLine::where("transaction_id",$tr->id)
                                                            ->where("product_id",$product_id)
                                                            ->select(
                                                                "purchase_price",
                                                                "quantity",
                                                                DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();
            ///... get  purchase price cost
            $row_purchase_price = \App\PurchaseLine::where("transaction_id",$tr->id)
                                                            ->select(
                                                                DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();

            
            if($row_purchase_one->qty == null){
                $style  =  "Other Product";
            }else{
                $style  =  "More Delivery";
            }
            $CheckState =  "Wrong - ".$tr->type."<br>".$style ;
            $itemMove   =  \App\Models\ItemMove::where("business_id",$tr->business_id)->where("state",$CheckState)->where("recieve_id",$it_wrong->id)->first();

            if(!empty($itemMove)){
                $move_id[] = $itemMove->id; 
            }
            if( !empty($row_purchase_price) ){
                if ($tr->discount_type == "fixed_before_vat"){
                    $dis = $tr->discount_amount;
                }else if ($tr->discount_type == "fixed_after_vat"){
                    $tax = \App\TaxRate::find($tr->tax_id);
                    if(!empty($tax)){
                       $dis = ($tr->discount_amount*100)/(100+$tax->amount) ;
                   }else{
                       $dis = ($tr->discount_amount*100)/(100) ;
                   }
                }else if ($tr->discount_type == "percentage"){
                    $dis = ($tr->total_before_tax *  $tr->discount_amount)/100;
                }else{
                    $dis = 0;
                }
                $add             = \App\Models\AdditionalShippingItem::whereHas('additional_shipping',function($query) use($tr){
                                                                                $query->where("type",0);
                                                                                $query->where('transaction_id',$tr->id);
                                                                        })->sum('amount') ;
                
                if($row_purchase_price->qty != 0){
                    if($row_purchase_one->qty != 0){
                        $row_cost = ( $row_purchase_one->total_row_price / $row_purchase_one->qty );
                    }else{
                        $row_cost = 0;
                    }
                }else{ 
                     $row_cost = 0;
                }
                if($add != 0){
                    if($row_purchase_price->total_row_price != 0){
                        $percent      = ($add - $dis) / $row_purchase_price->total_row_price;
                    }else{
                        $percent      = 0;
                    }
                    $additional   = $row_cost * $percent ;
                    $cost_inc_exp = $row_cost + $additional ;
                }else{
                    $cost_inc_exp = $row_cost;
                }
                if($row_purchase_one->qty != null){
                    $cost_inc_exp = 0;
                }
            }else{
                $row_cost     = \App\Product::product_cost($product_id);
                $cost_inc_exp = $row_cost;
            }
            $exp         = ItemMove::shipp($transaction_r);
            $receive_exp = $exp[0];
            if($total_receive_price != 0){
                $percent          = $receive_exp    / $total_receive_price;
                $additional       = $cost_inc_exp   * $percent ;
                $cost_inc_exp_rec = $cost_inc_exp   + $additional ;
            }else{
                $cost_inc_exp_rec = $row_cost ;
            }
            $prices   = [];
            $prices[] = $cost_inc_exp_rec;
            if(empty($itemMove)){
                // *** AGT8422 SAVE WRONG RECEIVED ITEMS
                ItemMove::saveItemMove("wrong_receive",$account,$itemMove,$tr,$it_wrong->purchase_line,$cost_inc_exp,($it_wrong)?$it_wrong->line_id:null,$it_wrong,$prices,$style);
                // **************************************
            }else{
                // *** AGT8422 UPDATE WRONG RECEIVED ITEMS
                ItemMove::updateItemMove("wrong_receive",$account,$itemMove,$tr,$it_wrong->purchase_line,$cost_inc_exp,($it_wrong)?$it_wrong->line_id:null,$move_id,$it_wrong,$prices,$style);
                // *************************************** 
            }
            DB::commit();
        }
    }
    /**
     * **************************************************************************** *
     *  30 Finish........ . . . . purchase wrong R/recieve . . . . .........  /     *
     *           ................ ************************* .................       *
     * **************************************************************************** *
     */
    public static function Wrong_recieve_return($transaction_return,$transaction_r)
    {
        $tr                   = \App\Transaction::find($transaction_return->return_parent_id);
        $account              = \App\Account::where("contact_id",$transaction_return->contact_id)->first();
        $purchase_line        = \App\PurchaseLine::where("transaction_id",$tr->id)->get();
        $wrongReceive         = \App\Models\RecievedWrong::where("transaction_id",$transaction_return->id)->get();
        $row                  = \App\Models\ItemMove::subtotal_recieve_correct($transaction_return->id,$transaction_r,"return");
        $sub_total_in_recieve = 0;
        $move_id = [];
        foreach($row as $it){
            $qty                   = $it[2];
            $price                 = $it[1];
            $total_                = $it[2] * $it[1];
            $sub_total_in_recieve += $total_; 
        }
        $total_receive_price   =  $sub_total_in_recieve;
        foreach($wrongReceive as $it_wrong){

            ///... get  product info
            $product_id  = $it_wrong->product_id;
            $qty_product = $it_wrong->current_qty;
            ///... get  one product
            $row_purchase_one = \App\PurchaseLine::where("transaction_id",$tr->id)
                                                            ->where("product_id",$product_id)
                                                            ->select(
                                                                "bill_return_price",
                                                                "quantity_returned",
                                                                DB::raw("SUM(quantity_returned*bill_return_price) as total_row_price"),
                                                                DB::raw("SUM(quantity_returned) as qty")
                                                            )->first();
            ///... get  purchase price cost
            $row_purchase_price = \App\PurchaseLine::where("transaction_id",$tr->id)
                                                            ->select(
                                                                DB::raw("SUM(quantity_returned*bill_return_price) as total_row_price"),
                                                                DB::raw("SUM(quantity_returned) as qty")
                                                            )->first();

            if($row_purchase_one->qty == null){
                $style = "Other Product";
            }else{
                $style = "More Delivery";
            }
            $CheckState =  "Wrong - ".$transaction_return->type."<br>".$style ;
            $itemMove  = \App\Models\ItemMove::where("business_id",$tr->business_id)->where("state",$CheckState)->where("recieve_id",$it_wrong->id)->first();
            if(!empty($itemMove)){
                $move_id[] = $itemMove->id; 
            }
            if( !empty($row_purchase_price) ){
                                                            
                if ($transaction_return->discount_type == "fixed_before_vat"){
                    $dis = $transaction_return->discount_amount;
                }else if ($transaction_return->discount_type == "fixed_after_vat"){
                    $tax = \App\TaxRate::find($transaction_return->tax_id);
                    if(!empty($tax)){
                        $dis = ($transaction_return->discount_amount*100)/(100+$tax->amount) ;
                    }else{
                        $dis = ($transaction_return->discount_amount*100)/(100) ;
                    }
                }else if ($transaction_return->discount_type == "percentage"){
                    $dis = ($transaction_return->total_before_tax *  $transaction_return->discount_amount)/100;
                }else{
                    $dis = 0;
                }
                
                if($row_purchase_price->qty != 0){
                    if($row_purchase_one->qty != 0){
                        $row_cost = ( $row_purchase_one->total_row_price / $row_purchase_one->qty );
                    }else{
                        $row_cost = 0;
                    }
                }else{ 
                    $row_cost = 0;
                }
                if($row_purchase_price->total_row_price != 0){
                    $percent       = ($dis) / $row_purchase_price->total_row_price;
                }else{
                    $percent       = 0;
                }
                $additional    = $row_cost * $percent ;
                $cost_inc_exp  = $row_cost ;
                $cost_inc_exp_ = $row_cost - $additional ;
                
                
            }else{
                $row_cost = \App\Product::product_cost($product_id);
                if($row_purchase_price->total_row_price != 0){
                    $percent       = ($dis) / $row_purchase_price->total_row_price;
                }else{
                    $percent       = 0;
                }
                $additional    = $row_cost * $percent ;
                $cost_inc_exp  = $row_cost;
                $cost_inc_exp_ = $row_cost - $additional ;
            }
            $prices   = [];
            $prices[] = $cost_inc_exp;
            $prices[] = $cost_inc_exp_;
            if(empty($itemMove)){
                // *** AGT8422 SAVE WRONG RECEIVED ITEMS
                ItemMove::saveItemMove("wrong_receivex",$account,$itemMove,$transaction_return,$it_wrong->purchase_line,$cost_inc_exp,($it_wrong)?$it_wrong->line_id:null,$it_wrong,$prices,$style);
                // **************************************        
            }else{
                // *** AGT8422 UPDATE WRONG RECEIVED ITEMS
                ItemMove::updateItemMove("wrong_receivex",$account,$itemMove,$transaction_return,$it_wrong->purchase_line,$cost_inc_exp,($it_wrong)?$it_wrong->line_id:null,$move_id,$it_wrong,$prices,$style);
                // *************************************** 
            }
            DB::commit();

        }
    }

    /**
     * **************************************************************************** *
     *  31 Finish........ . . . . sell - wrong - delivery . . . . .........  /      *
     *           ................ *********************** .................         *                                                                    *
     * **************************************************************************** *
     */
    public static function Wrong_delivery($transaction,$transaction_r)
    {
        
        $source              = \App\Transaction::find($transaction);  
        $sell                = \App\TransactionSellLine::where("transaction_id",$source->id)->get();
        $account             = \App\Account::where("contact_id",$source->contact_id)->first();
        $delivery            = \App\Models\DeliveredWrong::where("transaction_id",$source->id)->get();
        $line_id             = []; $product_id_         = []; $move_id             = []; 
      
        if(count($delivery)>0){
            foreach($delivery as $del){

                if(!in_array($del->product_id,$line_id)){
                    $line_id[]    = $del->product_id;
                    $product_id_[] = $del->product_id;
                }
                
                //...... for second price in item movement
                $product_id  = $del->product_id;
                $qty_product = $del->current_qty;
                
                ///... get  sell price cost one
                $row_sell_price_one = \App\TransactionSellLine::where("transaction_id",$source->id)
                                                            ->where("product_id",$product_id)
                                                            ->select(
                                                                DB::raw("SUM(quantity*unit_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();

                ///... get  sell price cost
                $row_sell_price = \App\PurchaseLine::where("transaction_id",$source->id)
                                                        ->select(
                                                            DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                            DB::raw("SUM(quantity) as qty")
                                                        )->first();
                   
                if($row_sell_price_one->qty == null){
                    $style = "Other Product";
                }else{
                    $style = "More Delivery";
                }
                $CheckState =  "Wrong - ".$source->type."<br>".$style;
                $itemMove = ItemMove::where("transaction_id",$source->id)->where("state",$CheckState)->where("recieve_id",$del->id)->first();
                if(!empty($itemMove)){
                    $move_id[] = $itemMove->id; 
                }
                
                ///... choice the cost 
                if($row_sell_price->qty != 0){
                    if($row_sell_price_one->qty != 0){
                        $row_cost = ( $row_sell_price_one->total_row_price / $row_sell_price_one->qty );
                    }else{
                        $row_cost = 0;
                    }
                }else{ 
                        $row_cost = 0;
                }
                
                $prices      = [];
                $prices[]    = $row_cost;
                if(empty($itemMove)){
                    // *** AGT8422 SAVE WRONG DELIVERED ITEMS
                    ItemMove::saveItemMove("wrong_delivered",$account,$itemMove,$source,$del->line,0,$del->line_id,$del,$prices,$style);
                    // *********************************** 
                }else{
                     
                    // *** AGT8422 UPDATE WRONG DELIVERED ITEMS
                    ItemMove::updateItemMove("wrong_delivered",$account,$itemMove,$source,$del->line,0,($del)?$del->line_id:null,$move_id,$del,$prices,$style);
                    // *************************************** 
                } 
                
                 
            }
        } 
    }

    /**
     * **************************************************************************** *
     *  32 Finish........ . . . .  sell - wrong - R/delivery  . . . . .........  /  *
     *           ................ *************************** ..................    *                                                                    *
     * **************************************************************************** *
     */
    
     public static function Wrong_delivery_return($transaction_return,$transaction_r)
     {
         $source              = \App\Transaction::find($transaction_return->return_parent_id);  
         $sell                = \App\TransactionSellLine::where("transaction_id",$source->id)->get();
         $account             = \App\Account::where("contact_id",$transaction_return->contact_id)->first();
         $delivery            = \App\Models\DeliveredWrong::where("transaction_id",$transaction_return->id)->get();
         $line_id             = [];
         $product_id_         = [];
         $move_id             = []; 
         if(count($delivery)>0){
             foreach($delivery as $del){
 
                 if(!in_array($del->product_id,$line_id)){
                     $line_id[]    = $del->product_id;
                     $product_id_[] = $del->product_id;
                 }
            
                 //...... for second price in item movement
                 $product_id  = $del->product_id;
                 $qty_product = $del->current_qty;
                 
                 ///... get  sell price cost one
                 $row_sell_price_one = \App\TransactionSellLine::where("transaction_id",$source->id)
                                                             ->where("product_id",$product_id)
                                                             ->select(
                                                                 DB::raw("SUM(quantity_returned*bill_return_price) as total_row_price"),
                                                                 DB::raw("SUM(quantity_returned) as qty")
                                                             )->first();
 
                 ///... get  sell price cost
                 $row_sell_price = \App\PurchaseLine::where("transaction_id",$source->id)
                                                         ->select(
                                                             DB::raw("SUM(quantity_returned*bill_return_price) as total_row_price"),
                                                             DB::raw("SUM(quantity_returned) as qty")
                                                         )->first();
                 
                if($row_sell_price_one->qty == null){
                    $style = "Other Product";
                }else{
                    $style = "More Delivery";
                }
                $CheckState =  "Wrong - ".$transaction_return->type."<br>".$style ;
                $itemMove  = \App\Models\ItemMove::where("business_id",$transaction_return->business_id)->where("state",$CheckState)->where("recieve_id",$del->id)->first();
                if(!empty($itemMove)){
                    $move_id[] = $itemMove->id; 
                }
                if( !empty($row_sell_price) ){

                    if ($transaction_return->discount_type == "fixed_before_vat"){
                        $dis = $transaction_return->discount_amount;
                    }else if ($transaction_return->discount_type == "fixed_after_vat"){
                        $tax = \App\TaxRate::find($transaction_return->tax_id);
                        if(!empty($tax)){
                           $dis = ($transaction_return->discount_amount*100)/(100+$tax->amount) ;
                       }else{
                           $dis = ($transaction_return->discount_amount*100)/(100) ;
                       }
                    }else if ($transaction_return->discount_type == "percentage"){
                        $dis = ($transaction_return->total_before_tax *  $transaction_return->discount_amount)/100;
                    }else{
                        $dis = 0;
                    }

                    ///... choice the cost 
                    if($row_sell_price->qty != 0){
                        if($row_sell_price_one->qty != 0){
                            $row_cost = ( $row_sell_price_one->total_row_price / $row_sell_price_one->qty );
                        }else{
                            $row_cost = 0;
                        }
                    }else{ 
                            $row_cost = 0;
                    }
                    if($row_sell_price->total_row_price != 0){
                        $percent       = ($dis) / $row_sell_price->total_row_price;
                    }else{
                        $percent       = 0;
                    }
                    $additional    = $row_cost * $percent ;
                    $cost_inc_exp  = $row_cost ;
                    $cost_inc_exp_ = $row_cost - $additional ;
                }else{
                    $row_cost = \App\Product::product_cost($product_id);
                    if($row_sell_price->total_row_price != 0){
                        $percent       = ($dis) / $row_sell_price->total_row_price;
                    }else{
                        $percent       = 0;
                    }
                    $additional    = $row_cost * $percent ;
                    $cost_inc_exp  = $row_cost;
                    $cost_inc_exp_ = $row_cost - $additional ;
                }
                $prices            = [];
                $prices[]          = $cost_inc_exp;
                $prices[]          = $cost_inc_exp_;
                if(empty($itemMove)){
                    // *** AGT8422 SAVE RETURN WRONG DELIVERED ITEMS
                    ItemMove::saveItemMove("wrong_delivered_r",$account,$itemMove,$transaction_return,$del->line,0,$del->line_id,$del,$prices,$style);
                    // *************************************** 
                }else{
                    // *** AGT8422 UPDATE RETURN WRONG DELIVERED ITEMS
                    ItemMove::updateItemMove("wrong_delivered_r",$account,$itemMove,$transaction_return,$del->line,0,($del)?$del->line_id:null,$move_id,$del,$prices,$style);
                    // ************************************************
                }  
             }
         } 
     }
    /**
     * **************************************************************************** *
     *  33 Finish...... . . . .  shipment      Movement  . . . . ........      /    *
     *           ............. ************************* .................          *
     * **************************************************************************** *
     */
    public static function shipp($id)
    {
        $tr = \App\Models\TransactionRecieved::where("id",$id)->first();
        $cost_recieve=0;$without_contact_recieve=0;
        if(!empty($tr)){
                $data_ship_recieve = \App\Models\AdditionalShipping::orderBy("id","desc")->where("transaction_id",$tr->transaction_id)->where("type",1)->where("t_recieved",$id)->first();
                if(!empty($data_ship_recieve)){
                $ids_recieve = $data_ship_recieve->items->pluck("id");
                $data = \App\Transaction::where("id",$tr->transaction_id)->first();
                foreach($ids_recieve as $i){
                    $data_shippment_recieve   = \App\Models\AdditionalShippingItem::find($i);
                    if($data_shippment_recieve->contact_id == $data->contact_id){ 
                        $cost_recieve += $data_shippment_recieve->amount;
                    }else{
                        $without_contact_recieve += $data_shippment_recieve->amount;
                    }
                }
            }
            $total_expense_recieve = $cost_recieve + $without_contact_recieve;
            $arra=[];
            $arra[] = $total_expense_recieve;
            return $arra;
        }
        
    }
    /**
     * **************************************************************************** *
     *  34 Finish...... . . .   Return Purchase Movement . . . . .........      /   *
     *           ............. ************************* .................          *                                                                          *
     * **************************************************************************** *
     */ 
    public static function return_purchase($id)
    {
            
            $source         = \App\Transaction::find($id->return_parent_id);
            $purchase       = \App\PurchaseLine::where("transaction_id",$source->id)->get();
            $account        = \App\Account::where("contact_id",$source->contact_id)->first();
            $array_del      = [];
            $line_id_       = [];
            $product_id     = [];
            $move_id_       = [];
            $ti             = 0;
            
            if(count($purchase)>0){
                foreach($purchase as $key => $pli){
                    $ch             = 0;
                    if($pli->quantity_returned != 0 ){
                        if(!in_array($pli->product_id,$line_id_)){
                            $line_id_[]    = $pli->product_id;
                            $product_id[]  = $pli->product_id;
                        }
                        $CheckState = "Return <br>" . $source->type;
                        $itemMove   = ItemMove::where("transaction_id",$source->id)->where("state",$CheckState)->where("line_id",$pli->id)->whereNotNull("is_returned")->orWhere('recieve_id',$pli->id)->first();
                        
                        if(empty($itemMove)){
                            $recieved =  \App\Models\RecievedPrevious::where("transaction_id",$source->id)->where("line_id",$pli->id)->first();
                        
                            if(!empty($recieved) ){
                                $itemMove_check = ItemMove::where("transaction_id",$source->id)->Where('recieve_id',$recieved->id)->first();
                                $first_price    = \App\Product::purchase_cost_before_global_dis($recieved->product_id,$recieved->transaction_id);
                                // $secound_price  = \App\Product::product_cost_expense($recieved->product_id,$recieved->transaction_id,$recieved->transaction_deliveries_id) ;
                                $secound_price  = $pli->bill_return_price ;
                                $unit_price     = $itemMove_check->unit_cost;
                                $costs          = ItemMove::costs($source->id,$pli,$secound_price,"return_p");
                                $total_qty      = $costs[0] ;
                                $cos            = $costs[1] ;
                                //.. ***************** create movement *************** ..\\
                                $item                     = new ItemMove();

                                $item->business_id        = $source->business_id;
                                $item->account_id         = $account->id;
                                $item->product_id	      = $pli->product_id;
                                $item->state	          = "Return <br>" . $source->type;
                                $item->ref_no             = ($source->type == "purchase" || $source->type == "purchase_return" || $source->type == "opening_stock")?$source->ref_no:$source->invoice_no;
                                $item->qty                = $pli->quantity_returned;
                                $item->signal             = ($source->type == "purchase" || $source->type == "purchase_return" ||  $source->type == "opening_stock")?"-":"+";
                                $item->row_price          = $first_price ;
                                $item->row_price_inc_exp  = $secound_price;
                                $item->unit_cost          = $cos  ;
                                $item->current_qty        = $total_qty ;
                                $item->transaction_id     = $source->id ;
                                $item->line_id            = $pli->id ;
                                $item->entry_option       = 1 ;
                                $item->out_price          = $unit_price;
                                $item->is_returned        = 1 ;
                                $item->recieve_id         = $recieved->id ;
                                //** ... NEW ITEMS
                                    $item->store_id       = $recieved->store_id  ;
                                    $item->transaction_rd_id  = $recieved->transaction_deliveries_id ;
                                //** ...
         
                                $item->save();
                                
                                
                            }else{
                                $ids  = \App\Models\RecievedPrevious::where("transaction_id",$source->id)->get();
                                $id_s = $ids->pluck("id"); 
                                $itemMove_check = ItemMove::orderBy("id","desc")->where("transaction_id",$source->id)->WhereIn('recieve_id',$id_s)->first();
                                $first_price    = \App\Product::purchase_cost_before_global_dis($itemMove_check->product_id,$itemMove_check->transaction_id);
                                // $secound_price  = \App\Product::product_cost_expense($itemMove_check->product_id,$itemMove_check->transaction_id,$itemMove_check->transaction_deliveries_id) ;
                                $secound_price  = $pli->bill_return_price ;
                                $unit_price     = $itemMove_check->unit_cost; 
                                $costs          = ItemMove::costs($source->id,$pli,$secound_price,"return_p");
                                $total_qty      = $costs[0] ;
                                $cos            = $costs[1] ;
                                //.. ***************** create movement *************** ..\\
                                $item                     = new ItemMove();

                                $item->business_id        = $source->business_id;
                                $item->account_id         = $account->id;
                                $item->product_id	      = $pli->product_id;
                                $item->state	          = "Return <br>" . $source->type;
                                $item->ref_no             = ($source->type=="purchase"  || $source->type=="purchase_return" || $source->type=="opening_stock")?$source->ref_no:$source->invoice_no;
                                $item->qty                = $pli->quantity_returned;
                                $item->signal             = ($source->type=="purchase" || $source->type=="purchase_return" || $source->type=="opening_stock")?"-":"+";
                                $item->row_price          = $first_price ;
                                $item->row_price_inc_exp  = $secound_price;
                                $item->unit_cost          = $cos;
                                $item->current_qty        = $total_qty ;
                                $item->transaction_id     = $source->id ;
                                $item->line_id            = $pli->id ;
                                $item->entry_option       = 1 ;
                                $item->out_price          = $unit_price;
                                $item->is_returned        = 1 ;
                                $item->recieve_id         = $itemMove_check->recieve_id ;
                                //** ... NEW ITEMS
                                    $received_id_last     = \App\Models\RecievedPrevious::where("id",$itemMove_check->recieve_id)->first();
                                    $item->store_id       = $received_id_last->store_id  ;
                                    $item->transaction_rd_id  = $received_id_last->transaction_deliveries_id ;
                                //** ...

                                $item->save();
                            }
                        }else{ 
                        
                            $ch = $pli->product_id;
                            $ti++;
                            $first_price   = $itemMove->row_price;
                            $secound_price = $pli->bill_return_price;
                            $unit_price    = $itemMove->unit_cost;
                            $costs         = ItemMove::costs($source->id,$pli,$secound_price,"return_p");
                            $total_qty     = $costs[0] ;
                            $cost          = $costs[1] ;

                            //.. ***************** create movement *************** ..\\
 
                            $itemMove->account_id         = $account->id;
                            $itemMove->qty                = $pli->quantity_returned;
                            $itemMove->row_price          = $first_price ;
                            $itemMove->row_price_inc_exp  = $secound_price;
                            $itemMove->unit_cost          = $cost;
                            $itemMove->current_qty        = $total_qty ;
                            $itemMove->out_price          = $unit_price ;

                            $itemMove->update();
 
                        }

                    }
                    if($ch != 0 ){
                        // \App\Models\ItemMove::refresh_item($ch,$pli->product_id);
                        ItemMove::updateRefresh($itemMove,$pli,$move_id_,$itemMove->date);
                    }
                }
            }  
         
    }
    /**
     * **************************************************************************** *
     *  35  Finish..... . . .   Return  Sale  Movement   . . . . .........          *
     *            ........... ************************** .................          *
     * **************************************************************************** *
     */
    public static function return_sale($id)
    {

            $source         = \App\Transaction::find($id->return_parent_id);
            $purchase       = \App\TransactionSellLine::where("transaction_id",$source->id)->get();
            $account        = \App\Account::where("contact_id",$source->contact_id)->first();
            $array_del      = [];
            $line_id_       = [];
            $product_id     = [];
            $move_id_       = []; 
            if(count($purchase)>0){
                foreach($purchase as $key => $pli){
                    if($pli->quantity_returned != 0 ){

                       if(!in_array($pli->product_id,$line_id_)){
                            $line_id_[]    = $pli->product_id;
                            $product_id[]  = $pli->product_id;
                       }
                       $CheckState = "Return <br>" . $source->type;
                       $itemMove = ItemMove::where("transaction_id",$source->id)->where("state",$CheckState)->where("line_id",$pli->id)->whereNotNull("is_returned")->orWhere('recieve_id',$pli->id)->first();
                       if(!empty($itemMove)){
                           $move_id_[] = $itemMove->id; 
                       }
                       
                        if(empty($itemMove)){
                            $recieved =  \App\Models\DeliveredPrevious::where("transaction_id",$source->id)->where("line_id",$pli->id)->first();
                            if(!empty($recieved)){
                                $itemMove_check = ItemMove::where("transaction_id",$source->id)->Where('recieve_id',$recieved->id)->first();
                                $first_price    = $itemMove_check->row_price;
                                $secound_price  = $pli->bill_return_price;
                                $unit_price     = $itemMove_check->unit_cost; 
                                $costs          = ItemMove::costs($source->id,$pli,$secound_price,"return_s");
                                $total_qty      = $costs[0] ;
                                $costs_         = $costs[1] ;
                                //.. ***************** create movement *************** ..\\
                                $item                     = new ItemMove();

                                $item->business_id        = $source->business_id;
                                $item->account_id         = $account->id;
                                $item->product_id	      = $pli->product_id;
                                $item->state	          = "Return <br>" . $source->type;
                                $item->ref_no             = ($source->type=="purchase" || $source->type=="opening_stock")?$source->ref_no:$source->invoice_no;
                                $item->qty                = $pli->quantity_returned;
                                $item->signal             = ($source->type=="purchase" || $source->type=="opening_stock")?"-":"+";
                                $item->row_price          = $first_price ;
                                $item->row_price_inc_exp  = $secound_price ;
                                $item->unit_cost          = $costs_ ;
                                $item->current_qty        = $total_qty ;
                                $item->transaction_id     = $source->id ;
                                $item->line_id            = $pli->id ;
                                $item->entry_option       = 1 ;
                                $item->out_price          = $unit_price ;
                                $item->is_returned        = 1 ;
                                $item->recieve_id         = $recieved->id ;
                                //** ... NEW ITEMS
                                    // $received_id_last     = \App\Models\RecievedPrevious::where("id",$itemMove_check->recieve_id)->first();
                                    $item->store_id       = $recieved->store_id  ;
                                    $item->transaction_rd_id  = $recieved->transaction_recieveds_id ;
                                //** ...

                                $item->save();
                                
                            }else{
                                $ids  = \App\Models\DeliveredPrevious::where("transaction_id",$source->id)->get();
                                $id_s = $ids->pluck("id"); 
                                $itemMove_check = ItemMove::orderBy("id","desc")->where("transaction_id",$source->id)->WhereIn('recieve_id',$id_s)->first();
                                $first_price    = $itemMove_check->row_price;
                                $secound_price  = $pli->bill_return_price;
                                $unit_price     = $itemMove_check->unit_cost; 
                                $costs          = ItemMove::costs($source->id,$pli,$secound_price,"return_s");
                                $total_qty      = $costs[0] ;
                                $costs_         = $costs[1] ;
                                //.. ***************** create movement *************** ..\\
                                $item                     = new ItemMove();

                                $item->business_id        = $source->business_id;
                                $item->account_id         = $account->id;
                                $item->product_id	      = $pli->product_id;
                                $item->state	          = "Return <br>" . $source->type;
                                $item->ref_no             = ($source->type=="purchase" || $source->type=="opening_stock")?$source->ref_no:$source->invoice_no;
                                $item->qty                = $pli->quantity_returned;
                                $item->signal             = ($source->type=="purchase" || $source->type=="opening_stock")?"-":"+";
                                $item->row_price          = $first_price ;
                                $item->row_price_inc_exp  = $secound_price;
                                $item->unit_cost          = $costs_;
                                $item->current_qty        = $total_qty ;
                                $item->transaction_id     = $source->id ;
                                $item->line_id            = $pli->id ;
                                $item->entry_option       = 1 ;
                                $item->out_price          = $unit_price;
                                $item->is_returned        = 1 ;
                                $item->recieve_id         = $itemMove_check->recieve_id ;
                                //** ... NEW ITEMS
                                    $received_id_last     = \App\Models\DeliveredPrevious::where("id",$itemMove_check->recieve_id)->first();
                                    $item->store_id       = $received_id_last->store_id  ;
                                    $item->transaction_rd_id  = $received_id_last->transaction_recieveds_id ;
                                //** ...

                                $item->save();
                            }
                        }else{ 
                            $first_price   = $itemMove->row_price;
                            $secound_price = $pli->bill_return_price;
                            $unit_price    = $itemMove->out_price;
                            $costs         = ItemMove::costs($source->id,$pli,$secound_price,"return_s",$itemMove,$pli->quantity_returned);
                            $total_qty     = $costs[0] ;
                            $costs_        = $costs[1] ;
                            //.. ***************** create movement *************** ..\\
 
                            $itemMove->account_id         = $account->id;
                            $itemMove->qty                = $pli->quantity_returned;
                            $itemMove->row_price          = $first_price ;
                            $itemMove->row_price_inc_exp  = $secound_price;
                            $itemMove->unit_cost          = $costs_;
                            $itemMove->current_qty        = $total_qty ;
                            $itemMove->out_price          = $unit_price ;

                            $itemMove->update();

                            ItemMove::updateRefresh($itemMove,$pli,$move_id_,$itemMove->date);

                            
                        }

                    }
                }
            }  
         
    }
    /**
     * **************************************************************************** *
     *  36 Finish......... . . . . store                   . . . . ........   /     *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */ 
    public function store()
    {
        return $this->belongsTo("\App\Models\Warehouse","store_id");
    }  
    /**
     * **************************************************************************** *
     *  37 Finish......... . . . . Transaction Recieved    . . . . ........   /     *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */ 
    public function transaction_r()
    {
        return $this->belongsTo("\App\Models\TransactionRecieved","transaction_rd_id");
    }  
    /**
     * **************************************************************************** *
     *  38 Finish......... . . . . Transaction Delivery    . . . . ........   /     *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */ 
    public function transaction_d()
    {
        return $this->belongsTo("\App\Models\TransactionDelivery","transaction_rd_id");
    } 
    /**
     * **************************************************************************** *
     *  39 Finish........ . . . . FUNCTION SAVE ITEM_MOVE . . . . .........   /     *
     *           ................ *********************** .................         *
     * **************************************************************************** *
     */ 
    public static function saveItemMove($type,$account,$itemMove,$source,$pli,$cost_inc_exp,$line,$recieve=null,$prices=null,$style=null,$variation_id=null) {
        try{
            if($type == "receive" || $type == "wrong_receive" || $type == "wrong_receivex"){
                $value                         = ($type == "wrong_receivex")?"receivex":"receive"; 
                $price_x                       = ($type == "wrong_receivex")?$prices[1]:$prices[0]; 
                $costs                         = ItemMove::costs($source->id,$recieve,$price_x,$value,null,null,null,$source->transaction_date,$variation_id);
            }elseif($type == "delivery" || $type == "wrong_delivered" || $type == "production" || $type == "wrong_delivered_r"){
                $filter_type                   = ($type == "production")?"minus":(($type == "wrong_delivered_r")?"deliveryx":"delivery");
                $costs                         = \App\Product::product_cost($recieve->product_id,$variation_id);
                $cost                          = ItemMove::costs($source->id,$recieve,0,$filter_type,null,null,null,$source->transaction_date,$variation_id);
                $total_qty                     = $cost[0] ;
                $FINAL_COST                    = $costs;
            }elseif($type == "transfer" || $type == "purchase_transfer" ){
                $value                         = ($type == "purchase_transfer")?null:"minus";
                $costs                         = ItemMove::costs($source->id,$pli,$cost_inc_exp,$value,null,null,null,$source->transaction_date,$variation_id);
                $total_qty                     = $costs[0]; 
                $FINAL_COST                    = \App\Product::product_cost($pli->product_id,$variation_id); 
            }elseif($type == "receivex" ){
                $costs                         = ItemMove::costs($source->id,$recieve,$prices[1],"receivex",null,null,null,$source->transaction_date,$variation_id);
            }elseif($type == "purchase_production" || $type == "deliveryx"  ){
                $value                         = ($type == "deliveryx" )?"deliveryx":"pr";
                $price_x                       = ($type == "wrong_delivered_r")?$prices[1]:$cost_inc_exp;
                $costs                         = \App\Product::product_cost($pli->product_id,$variation_id);
                $cost                          = ItemMove::costs($source->id,$pli,$price_x,$value,null,null,null,$source->transaction_date,$variation_id);
                $total_qty                     = $cost[0] ;
                $FINAL_COST                    = ($type == "deliveryx")?$cost[1]:$costs;
                $cost_wastage                  = TransactionUtil::calc_percentage_s($cost_inc_exp, $prices[1]); 
            }else{
                $costs                         = ItemMove::costs($source->id,$pli,$cost_inc_exp,null,null,null,null,$source->transaction_date,$variation_id);
            }

            if(  $type != "delivery"   && $type != "wrong_delivered"     &&
                 $type != "transfer"   && $type != "purchase_transfer"   && 
                 $type != "production" && $type != "purchase_production" &&
                 $type != "deliveryx"  && $type != "wrong_delivered_r"
             ){
               
                $total_qty                    = $costs[0] ;
                $FINAL_COST                   = $costs[1] ;
            }
            
            //.. ***************** create movement *************** ..\\
            $item                             = new ItemMove();
            
            $item->business_id                = $source->business_id;
            $item->account_id                 = ($type == "transfer" || $type == "purchase_transfer" || $type == "production" || $type == "purchase_production")?0:$account->id;
            if($type == "delivery" || $type == "wrong_delivered" || $type == "production" || $type == "deliveryx" || $type == "wrong_delivered_r"){
                $item->out_price              = $FINAL_COST ;
            }
            if($type == "wrong_receive" || $type == "wrong_delivered" || $type == "wrong_receivex" || $type == "wrong_delivered_r"){
                $item->state	              = "Wrong - ".$source->type."<br>".$style ;
            }elseif($type == "production" || $type == "purchase_production"){
                $item->state	              = ($type == "production")?"Manufacturing - ( Out )":"Manufacturing - ( In )";
            }else{
                $item->state	              = $source->type;
            }
            
            $item->ref_no                     = ($source->type=="purchase" || $source->type=="opening_stock" || $source->type=="purchase_return"|| $source->type=="Stock_Out" || $source->type=="Stock_In" || $type=="purchase_production" || $type=="wrong_receivex")?$source->ref_no:$source->invoice_no;
            $item->signal                     = ($source->type=="purchase" || $source->type=="opening_stock" || $source->type=="Stock_In"  || $type=="purchase_production" || $type=="deliveryx" || $type == "wrong_delivered_r")?"+":"-";
            
            if($type == "receive" || $type == "wrong_receive" || $type == "delivery" || $type == "wrong_delivered"){
                $item->qty                    = $recieve->current_qty;
                $item->product_id	          = $recieve->product_id;
                $item->row_price              = ($type == "delivery" || $type == "wrong_delivered")?$prices[0]:$cost_inc_exp ;
                $item->row_price_inc_exp      = ($type == "delivery")?$prices[1]:$prices[0] ;
            }elseif($type == "production" ||  $type == "purchase_production"){ 
                $item->product_id	          = $pli->product_id;
                $item->qty                    = $pli->quantity;
                $item->row_price              = $prices[0];
                $item->row_price_inc_exp      = ($type == "production")?$prices[0]:$prices[0]+$prices[1];
            }elseif($type == "receivex" || $type == "wrong_receivex" || $type == "deliveryx"|| $type == "wrong_delivered_r"){ 
                $item->product_id	          = $recieve->product_id;
                $item->qty                    = $recieve->current_qty;
                $item->row_price              = $prices[0];
                $item->row_price_inc_exp      = $prices[1];
            }else{
    
                $multiple_product             = ($pli->sub_unit_qty != null)?(($pli->sub_unit_qty != 0)?$pli->sub_unit_qty:1):1;
                $item->product_id	          = $pli->product_id;
                $item->qty                    = $multiple_product*$pli->quantity;
                $item->row_price              = ($type=="transfer" || $type == "purchase_transfer")?$FINAL_COST:$pli->purchase_price/($pli->quantity*$multiple_product) ;
                $item->row_price_inc_exp      = ($type=="transfer" || $type == "purchase_transfer")?$FINAL_COST:$cost_inc_exp;
                $item->product_unit           = ($pli->sub_unit_id != null)?$pli->sub_unit_id:(($pli->product)?$pli->product->unit->id:null);
                $item->product_unit_qty       = ($pli->sub_unit_qty != null)?(($pli->sub_unit_qty != 0)?$pli->sub_unit_qty:1):1;
            }
            if( $type == "wrong_receivex" ){
                $item->unit_cost              = ($total_qty>0)?$FINAL_COST:0;
            }else{ 
                $item->unit_cost              = $FINAL_COST;
            }
            
            $item->current_qty                = $total_qty ;
            $item->transaction_id             = $source->id ;
            $item->line_id                    = $line ;
            $item->variation_id               = ($variation_id!=null)?$variation_id:null ;
            if($type == "create_open"){
                $item->order_id               = $pli->order_id ;
            }
            //** ... NEW ITEMS
            if($recieve != null){
                if($type == "production" || $type == "purchase_production"){
                    $item->store_id           = $pli->store_id  ;
                    $item->sells_line_id      = ($type == "production")?$pli->id:null;
                    $item->purchase_line_id   = ($type == "purchase_production")?$pli->id:null ; 
                }elseif($type == "receivex" || $type == "wrong_receivex" || $type == "wrong_delivered_r"){
                    $item->store_id           = $recieve->store_id  ;
                    $item->transaction_rd_id  = ($type == "wrong_receivex" || $type == "wrong_delivered_r" )?$recieve->transaction_recieveds_id:$recieve->transaction_deliveries_id;
                    $item->recieve_id         = $recieve->id ;
                    $item->entry_option       = ($type == "wrong_delivered_r")?null:1 ;
                    $item->is_returned        = 1 ;
                }else{
                    $item->store_id           = $recieve->store_id  ;
                    $item->transaction_rd_id  = ($type == "delivery" || $type == "wrong_delivered" || $type == "deliveryx")?$recieve->transaction_recieveds_id:$recieve->transaction_deliveries_id ;
                    $item->recieve_id         = $recieve->id ;
                    $item->entry_option       = ($type == "delivery" || $type == "wrong_delivered" || $type == "deliveryx")?null:(($type == "receive")?1:0) ;
                }
            }else{
                $item->store_id               = $pli->store_id  ;
                $item->sells_line_id          = ($type=="transfer")?$pli->id:null ;
                $item->purchase_line_id       = ($type=="purchase_transfer")?$pli->id:null ; 
               // $item->transaction_rd_id  = $receive[$key]->transaction_deliveries_id ;
            }
            //** ...
            $item->date                       = ($type == "delivery" || $type == "wrong_delivered" || $type == "wrong_delivered_r")?$recieve->T_delivered->date:(($type == "receive" || $type == "wrong_receive" || $type == "wrong_receivex")?(($recieve->TrRecieved->date != null)?$recieve->TrRecieved->date:$source->transaction_date):$source->transaction_date) ;
            $item->save();
            $move_id[]                        = $item->id;
            if($variation_id!=null){
                ItemMove::tableFresh($item->product_id,$variation_id);
            }else{
                ItemMove::tableFresh($item->product_id);
            }
            // ItemMove::updateRefresh($item,($pli)?$pli:$recieve,$move_id,$source->transaction_date);
            return true;   
        }catch(Exception $e){
            return false;   
        }
    }

    /**
     * ***************************************************************************** *
     *  40 Finish........ . . . . FUNCTION UPDATE ITEM_MOVE . . . . .........   /    *
     *           ................ ************************* .................        *
     * ***************************************************************************** *
     */ 
    public static function updateItemMove($type,$account,$itemMove,$source,$pli,$cost_inc_exp,$line,$move_id,$receive=null,$prices=null,$style=null) {
        try{
            if($type == "receive" || $type == "wrong_receive" || $type == "wrong_receivex" || $type == "wrong_delivered_r"){
                $price_x                          = ($type == "wrong_receivex"|| $type == "wrong_delivered_r")?$prices[1]:$prices[0];
                $costs                            = ItemMove::costs($source->id,$receive,$price_x,null,$itemMove,$receive->current_qty,null,$source->transaction_date);
                $margin                           = $receive->current_qty - $itemMove->qty ;
            }elseif($type == "delivery" || $type == "wrong_delivered"){
                $costs                            = ItemMove::costs($source->id,$receive,0,"minus",$itemMove,$receive->current_qty,null,$source->transaction_date);
                $margin                           = $receive->current_qty - $itemMove->qty ;
            }elseif($type == "transfer" || $type == "purchase_transfer" || $type == "production"){
                $value                            = ($type == "purchase_transfer")?null:"minus";
                $costs                            = ItemMove::costs($source->id,$pli,$cost_inc_exp,$value,$itemMove,null,null,$source->transaction_date);
                $margin                           = $pli->quantity - $itemMove->qty ;
                $production                       = ($type == "production")?$costs[2]:null;
            }elseif($type == "receivex" || $type == "deliveryx" ){
                $price                            = ($type == "deliveryx")?0:$prices[1];
                $states                           = ($type == "deliveryx")?"plus":null;
                $costs                            = ItemMove::costs($source->id,$receive,$price,$states,$itemMove,$receive->current_qty,null,$source->transaction_date);
                $margin                           = $receive->current_qty - $itemMove->qty ;
            }elseif($type == "purchase_production"){
                $value                            = "pr";
                $costs                            = ItemMove::costs($source->id,$pli,$cost_inc_exp,$value,$itemMove,null,null,$source->transaction_date);
                $margin                           = $pli->quantity - $itemMove->qty ;
                $production                       = ($type == "production")?$costs[2]:null;
                $cost_wastage                     = TransactionUtil::calc_percentage_s($pli->purchase_price, $prices[1]); 
            }else{
                $multiple_product                 = ($pli->sub_unit_qty != null)?(($pli->sub_unit_qty != 0)?$pli->sub_unit_qty:1):1;
                $costs                            = ItemMove::costs($source->id,$pli,$cost_inc_exp,null,$itemMove,null,null,$source->transaction_date);
                $margin                           = ($pli->quantity*$multiple_product) - $itemMove->qty ;
            }
            
            $total_qty                            = $costs[0] ;
            $FINAL_COST                           = $costs[1] ;
            if($type == "delivery" || $type == "wrong_delivered" || $type == "wrong_delivered_r"){
                $itemMove->out_price              = $FINAL_COST ;
            }
            if($type == "wrong_receive" || $type == "wrong_delivered" || $type == "wrong_delivered_r"){
                $itemMove->state	              = "Wrong - ".$source->type."<br>".$style ;
            } 
            if($type == "production" || $type == "purchase_production"){
                $itemMove->state	              = ($type == "production")?"Manufacturing - ( Out )":"Manufacturing - ( In )" ;
            } 
            if($type == "receive" || $type == "wrong_receive" || $type == "delivery" || $type == "wrong_delivered"){
                $itemMove->qty                    = $receive->current_qty;
                $itemMove->row_price              = ($type == "delivery" || $type == "wrong_delivered")?$prices[0]:$cost_inc_exp ;
                $itemMove->row_price_inc_exp      = ($type == "delivery")?$prices[1]:$prices[0];
            }elseif($type == "production"){
                $itemMove->qty                    = $pli->quantity;
                $itemMove->row_price              = $pli->unit_price;
                $itemMove->row_price_inc_exp      = $pli->unit_price;
            }elseif($type == "purchase_production"){
                $itemMove->qty                    = $pli->quantity;
                $itemMove->row_price              = $pli->purchase_price;
                $itemMove->row_price_inc_exp      = $pli->purchase_price + $cost_wastage  ;
            }elseif($type == "receivex" || $type == "wrong_receivex" || $type == "deliveryx" || $type == "wrong_delivered_r" ){
                $itemMove->qty                    = $receive->current_qty;
                $itemMove->row_price              = $prices[0];
                $itemMove->row_price_inc_exp      = $prices[1];
            }else{
                $multiple_product                 = ($pli->sub_unit_qty != null)?(($pli->sub_unit_qty != 0)?$pli->sub_unit_qty:1):1;
                $itemMove->qty                    = $multiple_product*$pli->quantity;
                $itemMove->row_price              = round($pli->purchase_price/($pli->quantity*$multiple_product),config('constants.currency_precision')) ;
                $itemMove->row_price_inc_exp      = round($cost_inc_exp,config('constants.currency_precision'));
                $itemMove->product_unit           = ($pli->sub_unit_id  != null)?$pli->sub_unit_id:(($pli->product)?$pli->product->unit->id:null);
                $itemMove->product_unit_qty       = ($pli->sub_unit_qty != null)?(($pli->sub_unit_qty != 0)?$pli->sub_unit_qty:1):1;
            }
            if($type == "create_open"){
                $itemMove->order_id               = $pli->order_id ;
            }
            if($receive != null){
                if($type == "production" || $type == "purchase_production"){
                    $itemMove->store_id           = $pli->store_id;
                }else{
                    $itemMove->store_id           = $receive->store_id ;
                    $itemMove->transaction_rd_id  = ($type == "delivery" || $type == "wrong_delivered")?$receive->transaction_recieveds_id:$receive->transaction_deliveries_id ;
                    $itemMove->recieve_id         = $receive->id ;
                    $itemMove->entry_option       = ($type == "delivery" || $type == "wrong_delivered")?null:(($type == "receive")?1:0) ;
                }
            }else{
                $itemMove->store_id               = $pli->store_id  ;
            }
            if($type == "purchase_production"){
                $itemMove->unit_cost              = ($FINAL_COST==0)?$pli->purchase_price:$FINAL_COST;
            }else{
                $itemMove->unit_cost              = ($type == "production")?$production:round($FINAL_COST,config('constants.currency_precision'));
            }
            $itemMove->date                       = ($type == "delivery" || $type == "wrong_delivered"|| $type == "wrong_delivered_r")?$receive->T_delivered->date:(($type == "receive" || $type == "wrong_receive" || $type == "wrong_receivex")?(($receive->TrRecieved->date)?$receive->TrRecieved->date:$source->transaction_date):$source->transaction_date) ;
            $Old_Qty                              = ($pli)?$pli->quantity:$receive->current_qty;
            
            if($margin > 0 || $margin < 0){
                $itemMove->current_qty            = $total_qty ;
                $itemMove->update();
            }elseif($margin  ==  $Old_Qty){
                $itemMove->delete();
            }else{
                $itemMove->current_qty            = $total_qty ;
                $itemMove->update();
            }
            $date = ($itemMove->date != null) ? $itemMove->date : $itemMove->created_at  ; 
            ItemMove::tableFresh($itemMove->product_id);
            // ItemMove::updateRefresh($itemMove,($pli)?$pli:$receive,$move_id,$date);
            return true;   
        }catch(Exception $e){
            return false;   
        }
    }

    /**
     * ***************************************************************************** *
     *  41 Finish......... . .   FUNCTION REFRESH ITEM_MOVE  . . . . ........   /    *
     *           ..............  ************************** .................        *
     * ***************************************************************************** *
     */ 
    public static function updateRefresh($itemMove,$pli,$move_id,$date,$variation_id=null) {
        try{
            
            if($variation_id!=null){
                ItemMove::refresh_item($itemMove->id,$pli->product_id,null,null,1,$variation_id);
            }else{
                ItemMove::refresh_item($itemMove->id,$pli->product_id,null,null,1);
            }
            // ** take all item movement without this id ($itemMove->id)
            $id_move    = [];
            $move_all   = ItemMove::where("product_id",$pli->product_id)
                                    // ->whereNotIn("id",$move_id)
                                    // ->whereDate("date","=",$date)
                                    ->OrderBy("date","asc") 
                                    ->OrderBy("id","asc"); 
            if($variation_id != null){
                $move_all->where("variation_id",$variation_id);
            }
            $move_all   = $move_all->get(); 
            // foreach($move_all as $i){
            //     if($i->date == $date){
            //         if($i->id > $itemMove->id){
            //             $id_move[] = $i;
            //         }
            //     }else{
            //         $id_move[] = $i;
            //     }
            // }
            // dd($move_all);
            if(count($move_all)>0){
                foreach($move_all as $key =>  $it){
                    if($itemMove->id != $it->id){
                        if($it->variation_id != null){
                            ItemMove::refresh_item($it->id,$it->product_id,null,null,null,$it->variation_id);
                        }else{
                            ItemMove::refresh_item($it->id,$it->product_id);
                        }
                        
                    }
                }
            }
             
             
            return true;   
        }catch(Exception $e){
            return false;   
        }
    }

    /**
     * ***************************************************************************** *
     *  42 Finish.... . . . . FUNCTION DELETE RECEIVE PREVIOUS . . . . ......   /    *
     *           ............ ******************************** ..............        *
     * ***************************************************************************** *
     */ 
    public static function deletePrevious($previous) {
        try{
            \DB::beginTransaction();
            if(count($previous)>0){
                foreach($previous as $i){
                $ii =  \App\Models\RecievedPrevious::find($i->id);
                $ii->delete();
                }
            }
            \DB::commit();
            return true;   
        }catch(Exception $e){
            return false;   
        }
    } 

    /**
     * ***************************************************************************** *
     *  43 Finish..... . . .  FUNCTION DELETE WAREHOUSE STOCK  . . . . ......   /    *
     *      ................. ******************************** ..............        *
     * ***************************************************************************** *
     */ 
    public static function deleteWarehouseStock($id_delete) {
        try{
            \DB::beginTransaction();
            if(count($id_delete)>0){
                foreach($id_delete as $ware){
                    $i  = \App\MovementWarehouse::find($ware->id);
                    if($i->plus_qty == 0){
                        \App\Models\WarehouseInfo::update_stoct($i->product_id,$i->store_id,($i->minus_qty),$i->business_id);
                    }else{
                        \App\Models\WarehouseInfo::update_stoct($i->product_id,$i->store_id,($i->plus_qty*-1),$i->business_id);
                    }
                    $i->delete();
                }
            }
            \DB::commit();
            return true;   
        }catch(Exception $e){
            return false;   
        }
    } 

    /**
     * ***************************************************************************** *
     *  44 Finish...... . . . FUNCTION DELETE ItemMovement  . . . . . . .....   /    *
     *           ............ ****************************  .................        *
     * ***************************************************************************** *
     */ 
    public static function deleteItemMovement($id_delete) {
        try{
            \DB::beginTransaction();
            if(count($id_delete)>0){
                foreach($id_delete as $item_Move){
                    $i = \App\Models\ItemMove::find($item_Move->id);
                    $i->delete();
                }
            }
            \DB::commit();
            return true;   
        }catch(Exception $e){
            return false;   
        }
    } 

    /**
     * ***************************************************************************** *
     *  45 Finish...... . . . FUNCTION Refresh All          . . . . . . .....   /    *
     *           ............ ****************************  .................        *
     * ***************************************************************************** *
     */ 
    public static function refreshAll() {
        try{
            \DB::beginTransaction();
            
            
            // $product  = \App\Product::where("id",">",3000)->where("id","<=",3500)->get(); 
            // $move_id  = [];
            // foreach($product as $pro){
            //     $itemMove = \App\Models\ItemMove::orderBy("date","asc")->orderBy("order_id","desc")->orderBy("id","asc")->where("product_id",$pro->id)->first();
            //     if(!empty($itemMove)){
            //         $move_id [] = $itemMove->id;
            //         $date       = $itemMove->date;
            //         // DD($itemMove);
            //         \App\Models\ItemMove::updateRefresh($itemMove,$itemMove,$move_id,$date);
            //     }
            // }
            // $product  = \App\Product::where("id",">",3000)->where("id","<=",3500)->get(); 
            // $move_id  = [];
            // foreach($product as $pro){
            //     $itemMove = \App\Models\ItemMove::orderBy("date","asc")->orderBy("order_id","desc")->orderBy("id","asc")->where("product_id",$pro->id)->first();
            //     if(!empty($itemMove)){
            //         $move_id [] = $itemMove->id;
            //         $date       = $itemMove->date;
            //         \App\Models\ItemMove::updateRefresh($itemMove,$itemMove,$move_id,$date);
            //     }
            // }
            
            // $list = [] ; 
            // $account = \App\AccountTransaction::select("amount","id")->get();
            // foreach($account as $ie){
            //     $lenght = substr($ie->amount,strpos($ie->amount, ".") + 1);
            //     $count = 0; $sr = "";
            //     $array = str_split($lenght);
            //     foreach($array as $ies){
            //         if($ies != 0 ){
            //             $count++;
            //             // $sr = $sr .  strpos($lenght,$i);
            //         }
            //     }
                
            //         if($count == 3 ){
            //                 $list[$ie->id] =   $ie->amount;   
            //         }
                    
                 
            // }
        //   dd($list); 
            
            // $list = [];
            // $account = \App\AccountTransaction::whereHas("daily_payment_item",function($q){
            //                 $q->where("daily_payment_id",463);                
            //             })->get();
            // $debit   = 0;
            // $credit  = 0;
            // foreach($account as $ie){
            //     if($ie->type == "debit"){
            //       $debit   += $ie->amount;
            //     }
            //     if($ie->type == "credit"){
            //       $credit   += $ie->amount;
            //         // $list [$ie->id] =   $credit ;
                    
            //     }
            // }
            // $list [ ] = [ "credit" => $credit , "debit" => $debit] ;
            // dd($list);
            
            // $accountTransaction = \App\Models\Entry::pluck("id");
            // $account = [];
            // foreach($accountTransaction   as $i){
            //     $debit  = 0;
            //     $credit = 0;$balance=0;
            //     $act    = \App\AccountTransaction::where("entry_id",$i)->where("for_repeat",null)->where("deleted_at",null)->whereHas("account",function($query){
            //                     $query->where("cost_center",0);
            //                 })->get();
            //     foreach($act as $ie){
            //         if($ie->type == "debit"){
            //             $debit  += $ie->amount;
            //         }
            //         if($ie->type == "credit"){
            //             $credit += $ie->amount;
            //         }
            //     }
            //     $balance     = $debit - $credit;
            //     if($balance != 0){
            //         $account[$i] = [ 
            //             "debit"    => $debit,
            //             "credit"   => $credit,
            //             "balance"  => $balance
            //     ];
            //     }
            // }
            // dd($account);
            
            
            
            // foreach($itemMovement as $i){
            //     if(
            //         $i->state == "purchase" && $i->entry_option == 1         ||
            //         $i->state == "purchase_return" && $i->entry_option == 1  ||
            //         $i->state == "Stock_Out"                                 ||
            //         $i->state == "Stock_In"                                  ||
            //         $i->state == "Wrong - sale<br>Other Product"             ||
            //         $i->state == "Wrong - sale<br>More Delivery"             ||
            //         $i->state == "Wrong - purchase<br>More Received"         ||
            //         $i->state == "Wrong - purchase_return<br>More Received"  ||
            //         $i->state == "Wrong - purchase_return<br>Other Product"  ||  
            //         $i->state == "Wrong - purchase<br>Other Product"   
            //      ){
            //         $transaction  = \App\Transaction::find($i->transaction_id); 
            //         $i->date      = $transaction->transaction_date;
            //         $i->update();
            //     }
            // }
            //  if($i->state == "Manufacturing - ( In )"){
            //      $transaction  = \App\Transaction::find($i->transaction_id); 
            //      $i->date      = $transaction->transaction_date;
            //      $i->update();
            // }}
            // if($i->state == "Wrong - sale<br>Other Product"){
            //      $transaction  = \App\Transaction::find($i->transaction_id); 
            //      $i->date      = $transaction->transaction_date;
            //      $i->update();
            // }}
            // if($i->state == "sale" ){
            //     $purchase     = \App\Models\DeliveredPrevious::find($i->recieve_id);
                
            //     if(!empty($purchase)){
            //         $tr_rd        = \App\Models\TransactionDelivery::find($purchase->transaction_recieveds_id);
            //         if(!empty($tr_rd)){
            //             $i->date      = ($tr_rd->date != null && $tr_rd->date != "")?$tr_rd->date:$tr_rd->created_at;
            //             $i->update();
            //         }else{
            //           $list []  = $purchase;     
                        
            //         }
            //     }
            // }}
            // $list           = [];
            // $listItems      = [];
            // $listItemsLines = [];
            // $product      = \App\MovementWarehouse::select(["product_id"])->whereNotNull("product_id")->groupBy("product_id")->pluck("product_id");
            // foreach($product as $idd){
            //     $store        = \App\MovementWarehouse::where("product_id",$idd)->select(["product_id","store_id"])->groupBy("store_id")->pluck("store_id"); 
            //     $list[$idd]   = $store->ToArray();
            // }
            // foreach($list as  $key => $idd){
            //     foreach($idd as $id_store){
            //         $lines              = \App\MovementWarehouse::where("product_id",$key)->where("store_id",$id_store)->orderBy("date","asc")->orderBy("id","asc")->get(); 
            //         $current            = 0;
            //         foreach($lines as $li){
            //             if($li->movement == "opening_stock"){
            //                 $current += $li->plus_qty;
            //             }elseif($li->movement == "purchase"){
            //                 $current += $li->plus_qty;
            //             }elseif($li->movement == "Stock_In"){
            //                 $current += $li->plus_qty;
            //             }elseif($li->movement == "sell_return"){
            //                 $current += $li->plus_qty;
            //             }elseif($li->movement == "production_purchase"){
            //                 $current += $li->plus_qty;
            //             }elseif($li->movement == "Stock_Out"){
            //                 $current -= $li->minus_qty;
            //             }elseif($li->movement == "sale"){
            //                 $current -= $li->minus_qty;
            //             }elseif($li->movement == "purchase_return"){
            //                 $current -= $li->minus_qty;
            //             }elseif($li->movement == "production_sell"){
            //                 $current -= $li->minus_qty;
            //             }
            //             $li->current_qty    = $current;
            //             $li->update();
            //             $listItemsLines[]   = $current;
            //         }
            //     }
            //     $listItems[] = [
            //             "product_id" => $key,
            //             "list"       => $listItemsLines
            //     ];
            // }
           
            // $wareMovement = \App\MovementWarehouse::get();
            // foreach($wareMovement as $i){
            //     if( $i->movement != "sale" ){
            //         $transaction              = \App\Transaction::find($i->transaction_id); 
            //         $i->date                  = (!empty($transaction))?$transaction->transaction_date:$i->created_at;
            //         $i->update();
            //     }else{
            //         $item                     = \App\Models\DeliveredPrevious::where("transaction_id",$i->transaction_id)->where("product_id",$i->product_id)->first();
            //         if(!empty($item)){
            //             $transactionDelivered = \App\Models\TransactionDelivery::find($item->transaction_recieveds_id);
            //             $i->date              = (!empty($transactionDelivered) && $transactionDelivered->date != null)?$transactionDelivered->date:$i->created_at;
            //             $i->update();
            //         }
            //         $itemWrong                     = \App\Models\DeliveredWrong::where("transaction_id",$i->transaction_id)->where("product_id",$i->product_id)->first();
            //         if(!empty($itemWrong)){
            //             $transactionDelivered = \App\Models\TransactionDelivery::find($itemWrong->transaction_recieveds_id);
            //             $i->date              = (!empty($transactionDelivered) && $transactionDelivered->date != null)?$transactionDelivered->date:$i->created_at;
            //             $i->update();
            //         }
            //     }
            // }
            // $products = \App\Product::where("id",">=",1000)->where("id","<=",1500)->get();
            // foreach($products as $pro){
            //     $itemMove = ItemMove::orderBy("date","asc")->orderBy("order_id","desc")->orderBy("id","asc")->where("product_id",$pro->id)->first();
                
            //     if(!empty($itemMove)){
            //         $move_id [] = $itemMove->id;
            //         $date       = $itemMove->date;
            //         ItemMove::updateRefresh($itemMove,$itemMove,$move_id,$date);
            //     }
            // }
            
            // $closing_cost      = 0;
            // $products          = \App\Product::where("business_id",1)->get();
            // $listed            = [];
            // foreach($products as $it){
            //     $cost          = \App\Product::product_cost($it->id);
            //     $product_id    = \App\Product::find($it->id);
            //     $it_product    = \App\Models\WarehouseInfo::where("product_id",$it->id)->sum("product_qty");
            //     if($it_product > 0){$listed[$product_id->id] = $product_id->name; }
            //     $closing_cost += $cost * $it_product;
            // }   
            
            // $listed  = [];
            // $account = \App\Account::where("a")->get();
            // foreach($contact as $K =>  $i){
                
            //         //  if(substr($i->contact_id,0,1) != "S" && substr($i->contact_id,0,1) != "G" && substr($i->contact_id,0,1) != "C"){
            //             $listed[] =  $i->contact_id ;
            //             $item     =  $i->contact_id; 
            //             // $i->contact_id = "S".$item; 
            //             // $i->update(); 
            //         //  }
                
            // }
             
            //  .......................................
            // $account            = \App\Account::where("cost_center",1)->pluck("id");    
            // $accountTransaction = \App\AccountTransaction::whereIn("account_id",$account)->get();
           
            // foreach($accountTransaction as $i){
            //     $list_of_ids = [];
            //     // ....sales...sales_discount...sale_return...purchase....purchase_discount...purchase_return...
            //     $settings = \App\Models\SystemAccount::select(["sale","sale_discount","sale_return","purchase","purchase_discount","purchase_return"])->get();
            //     foreach( $settings  as $sa) {
            //         $list_of_ids[]  = $sa->sale;
            //         $list_of_ids[]  = $sa->sale_discount;
            //         $list_of_ids[]  = $sa->sale_return;
            //         $list_of_ids[]  = $sa->purchase;
            //         $list_of_ids[]  = $sa->purchase_discount;
            //         $list_of_ids[]  = $sa->purchase_return;
            //     }
            //     if($i->transaction_id != null ){
            //         $accountTn = \App\AccountTransaction::where("transaction_id",$i->transaction_id)->whereIn("account_id",$list_of_ids)->where("id","!=",$i->id)->get();
            //         foreach($accountTn as $iem){
            //             $iem->cs_related_id = $i->account_id;
            //             $iem->update();
            //         }
            //     }
            //     // ...................................................................
            //     if($i->daily_payment_item_id != null ){
            //         $accountTn = \App\AccountTransaction::where("daily_payment_item_id",$i->daily_payment_item_id)->where("id","!=",$i->id)->get();
            //         foreach($accountTn as $iem){
            //             $iem->cs_related_id = $i->account_id;

            //             $iem->update();
            //         }
            //     }
            //     if($i->gournal_voucher_item_id != null ){
            //         $accountTn = \App\AccountTransaction::where("gournal_voucher_item_id",$i->gournal_voucher_item_id)->where("id","!=",$i->id)->get();
            //         foreach($accountTn as $iem){
            //             $iem->cs_related_id = $i->account_id;

            //             $iem->update();
            //         }
            //     }
            //     if($i->additional_shipping_item_id != null ){
            //         $accountTn = \App\AccountTransaction::where("additional_shipping_item_id",$i->additional_shipping_item_id)->where("id","!=",$i->id)->get();
            //         foreach($accountTn as $iem){
            //             $iem->cs_related_id = $i->account_id;

            //             $iem->update();
            //         }
            //     }
            //     if($i->return_transaction_id != null ){
            //         $accountTn = \App\AccountTransaction::where("return_transaction_id",$i->return_transaction_id)->where("id","!=",$i->id)->get();
            //         foreach($accountTn as $iem){
            //             $iem->cs_related_id = $i->account_id;

            //             $iem->update();
            //         }
            //     }
                // ...................................................................
            // }
             

            \DB::commit();
            return true;   
        }catch(Exception $e){
            return false;   
        }
    } 
    /**
     * ***************************************************************************** *
     *  46 Finish...... . . . FUNCTION Refresh Warehouse    . . . . . . .....   /    *
     *           ............ ****************************  .................        *
     * ***************************************************************************** *
     */ 
    public static function refreshWarehouse($product_id) {
        try{
            \DB::beginTransaction();
            $list           = [];
            $listItems      = [];
            $listItemsLines = [];
          
            $store               = \App\MovementWarehouse::where("product_id",$product_id)->select(["product_id","store_id"])->groupBy("store_id")->pluck("store_id"); 
            $list[$product_id]   = $store->ToArray();
           
            foreach($list as  $key => $idd){
                foreach($idd as $id_store){
                    $lines              = \App\MovementWarehouse::where("product_id",$key)->where("store_id",$id_store)->orderBy("date","asc")->orderBy("id","asc")->get(); 
                    $current            = 0;
                    foreach($lines as $li){
                        if($li->movement == "opening_stock"){
                            $current += $li->plus_qty;
                        }elseif($li->movement == "purchase"){
                            $current += $li->plus_qty;
                        }elseif($li->movement == "Stock_In"){
                            $current += $li->plus_qty;
                        }elseif($li->movement == "sell_return"){
                            $current += $li->plus_qty;
                        }elseif($li->movement == "production_purchase"){
                            $current += $li->plus_qty;
                        }elseif($li->movement == "Stock_Out"){
                            $current -= $li->minus_qty;
                        }elseif($li->movement == "sale"){
                            $current -= $li->minus_qty;
                        }elseif($li->movement == "purchase_return"){
                            $current -= $li->minus_qty;
                        }elseif($li->movement == "production_sell"){
                            $current -= $li->minus_qty;
                        }
                        $li->current_qty    = $current;
                        $li->update();
                        $listItemsLines[]   = $current;
                    }
                }
                $listItems[] = [
                        "product_id" => $key,
                        "list"       => $listItemsLines
                ];
            }
            \DB::commit();
            return true;   
        }catch(Exception $e){
            return false;   
        }
    } 

    /**
     * ***************************************************************************** *
     *  47 Finish...... . . . FUNCTION Refresh after execute  . . . . . . .....   /  *
     *           ............ ******************************  .................      *
     * ***************************************************************************** *
     */ 
    public static function tableFresh($product_id,$variation_id=null) {
        try{
            \DB::beginTransaction();
            $itemMove = \App\Models\ItemMove::orderBy("date","asc")
                                            ->orderBy("order_id","desc")
                                            ->orderBy("id","asc")
                                            ->where("product_id",$product_id);
            if($variation_id!=null){
                $itemMove->where("variation_id",$variation_id);
            }
            $itemMove = $itemMove->first();
            if(!empty($itemMove)){
                $move_id [] = $itemMove->id;
                $date       = $itemMove->date;
                if($variation_id!=null){ 
                    \App\Models\ItemMove::updateRefresh($itemMove,$itemMove,$move_id,$date,$variation_id);
                }else{
                    \App\Models\ItemMove::updateRefresh($itemMove,$itemMove,$move_id,$date);
                }
            }
            \DB::commit();
            return true;   
        }catch(Exception $e){
            return false;   
        }
    } 

    #0#...#new
    public static function FirstBalance($product_id){
        try{ 
            $FirstLineItemMovement = ItemMove::where("product_id",$product_id)->orderByRaw("ISNULL(date) , date asc, created_at asc")->orderBy("order_id","asc")->orderBy("id","asc")->first(); 
            $FirstTotalBalance     = $FirstLineItemMovement->qty * $FirstLineItemMovement->row_price_inc_exp;
            $FirstQtyBalance       = $FirstLineItemMovement->qty;
            $FirstCostBalance      = $FirstLineItemMovement->row_price_inc_exp;
            $FirstTotalDate        = ($FirstLineItemMovement->date !=null)?$FirstLineItemMovement->date:$FirstLineItemMovement->created_at;
            #...........................................
            $FirstLineItemMovement->current_qty = $FirstQtyBalance;
            $FirstLineItemMovement->unit_cost   = $FirstCostBalance ;
            #...........................................
            $allData               = [];
            $allData["id"]         = $FirstLineItemMovement->id;
            $allData["cost"]       = $FirstCostBalance;
            $allData["total_qty"]  = $FirstQtyBalance;
            $allData["total_cost"] = $FirstTotalBalance;
            $allData["date"]       = $FirstTotalDate;
            // dd($FirstLineItemMovement);
            $FirstLineItemMovement->update();
            #...........................................
            return $allData; 
        }catch(Exception $e){ 
            return false; 
        }
    }
    #1#...#new
    public static function CollectAllRows($product_id){
        try{ 
            $allData = ItemMove::FirstBalance($product_id); 
            if($allData === false){return false;}
            $firstDate         = $allData["date"];
            $firstBalance      = $allData["cost"];
            $firstQtyBalance   = $allData["total_qty"];
            $firstTotalBalance = $allData["total_cost"];
            $FirstLineItemMovement = ItemMove::where("product_id",$product_id)->where("id","<>",$allData["id"])->where("date",">=",$allData["date"])->orderByRaw("ISNULL(date) ,date  asc, created_at asc")->orderBy("order_id","asc")->orderBy("id","asc")->get();
            // dd($allData,
            //     $firstBalance,
            //     $firstQtyBalance,
            //     $firstTotalBalance,
            //     $FirstLineItemMovement);
            
            foreach($FirstLineItemMovement as $K => $line){
                #.ROW INFO...................
                $state     = $line->state;
                $signal    = $line->signal;
                $qty       = $line->qty;
                $price     = $line->row_price_inc_exp;
                $total_row = $qty*$price;
                #..FINAL ROW INFO............
                    if($line->id == 4877	){
                        // dd($firstQtyBalance);
                    }
                    #..check state........
                        switch($state){
                            case "purchase":{
                                #.... effect on unit cost & QTY (+) from stock
                                $total_qty         = ($firstQtyBalance+$qty) ;
                                $total_cost        = $firstTotalBalance + $total_row ;
                                $firstBalance      = ($total_qty!=0)?$total_cost/$total_qty:0;
                                $line->current_qty = $total_qty;
                                $line->unit_cost   = $firstBalance ;
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = $total_cost;
                                break;
                            }
                            case "sale":{
                                #.... XXX don't effect on unit cost  & QTY (-) from stock XXX
                                $total_qty         = ($firstQtyBalance-$qty) ;
                                $total_cost        = $firstTotalBalance  - (($qty)*($firstTotalBalance/$firstQtyBalance)) ;
                                $firstBalance      = ($firstQtyBalance!=0)?$total_cost/$firstQtyBalance:0;
                                $line->current_qty = $total_qty;
                                $line->out_price   = ($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0 ;
                                $line->unit_cost   = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0);
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = ( $total_qty == 0)?0:$total_cost ;
                                break;
                            }
                            case "purchase_return":{
                                #.... effect on unit cost & QTY  (-) from stock
                                $total_qty         = ($firstQtyBalance-$qty) ;
                                $total_cost        = $firstTotalBalance - $total_row ;
                                $firstBalance      = ($total_qty!=0)?$total_cost/$total_qty:0;
                                $line->current_qty = $total_qty;
                                $line->unit_cost   = $firstBalance ;
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = $total_cost;
                                break;
                            }
                            case "sell_return":{
                                #.... effect on unit cost & QTY  (+) from stock
                                $total_qty         = ($firstQtyBalance+$qty) ;
                                $total_cost        = $firstTotalBalance + $total_row ;
                                $firstBalance      = ($total_qty!=0)?$total_cost/$total_qty:0;
                                $line->current_qty = $total_qty;
                                $line->unit_cost   = $firstBalance ;
                                $line->out_price   = $firstBalance ;
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = $total_cost;
                                break;
                            }
                            case "opening_stock":{
                                #.... effect on unit cost & QTY  (+) from stock
                                $total_qty         = ($firstQtyBalance+$qty) ;
                                $total_cost        = $firstTotalBalance + $total_row ;
                                $firstBalance      = ($total_qty!=0)?$total_cost/$total_qty:0;
                                $line->current_qty = $total_qty;
                                $line->unit_cost   = $firstBalance ;
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = $total_cost;
                                break;
                            }
                            case "Stock_In":{
                                    
                                #.... XXX don't effect on unit cost  & QTY (+) from stock XXX 
                                $total_qty                 = ($firstQtyBalance+$qty) ;
                                $total_cost                = ($firstQtyBalance!=0)?($firstTotalBalance   + (($qty)*($firstTotalBalance/$firstQtyBalance))):0 ;
                                $firstBalance              = ($firstQtyBalance!=0)?$total_cost/$firstQtyBalance:0;
                                $line->current_qty         = $total_qty;
                                $line->out_price           = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?(($firstTotalBalance/$firstQtyBalance)):(($firstTotalBalance>0)?($firstTotalBalance):0)) ;
                                $line->row_price           = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?(($firstTotalBalance/$firstQtyBalance)):(($firstTotalBalance>0)?($firstTotalBalance):0)) ;
                                $line->row_price_inc_exp   = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?(($firstTotalBalance/$firstQtyBalance)):(($firstTotalBalance>0)?($firstTotalBalance):0)) ;
                                $line->unit_cost           = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?(($firstTotalBalance/$firstQtyBalance)):(($firstTotalBalance>0)?($firstTotalBalance):0)) ;
                                $firstTotalBalance         = ($firstQtyBalance!=0)?$total_cost:(($firstTotalBalance>0)?($total_qty*$firstTotalBalance):0);
                                $firstQtyBalance           = $total_qty; 
                                if($line->id == 4877){
                                    // dd($firstTotalBalance);
                                }   
                                break;
                            }
                            case "Stock_Out":{
                                #.... XXX don't effect on unit cost  & QTY (-) from stock XXX 
                                $total_qty                 = ($firstQtyBalance-$qty) ;
                                $oldBalance                = null;
                                $total_cost                = $firstTotalBalance   - (($qty)*($firstTotalBalance/$firstQtyBalance)) ;
                                if($firstTotalBalance   == (($qty)*($firstTotalBalance/$firstQtyBalance))){
                                    $oldBalance = $firstTotalBalance/$firstQtyBalance;
                                }
                                $firstBalance              = ($firstQtyBalance!=0)?$total_cost/$firstQtyBalance:0;
                                $line->current_qty         = $total_qty;
                                $line->out_price           = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0) ;
                                $line->row_price           = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0);
                                $line->row_price_inc_exp   = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0);
                                $line->unit_cost           = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0);
                                if($line->id == 4351){
                                    // dd(($qty)*$firstTotalBalance/$firstQtyBalance);
                                }
                                $firstQtyBalance           = $total_qty;    
                                $firstTotalBalance         = ($oldBalance!=null)?$oldBalance:$total_cost;
                                // $firstTotalBalance         = (($firstTotalBalance   - (($qty)*($firstTotalBalance/$firstQtyBalance)))<=0)?$firstTotalBalance:$total_cost;
                                break;
                            }
                            case "Manufacturing - ( Out )":{
                                #.... XXX don't effect on unit cost  & QTY (-) from stock XXX 
                                $total_qty         = ($firstQtyBalance-$qty) ;
                                $total_cost        = $firstTotalBalance  - (($qty)*($firstTotalBalance/$firstQtyBalance)) ;
                                $firstBalance      = ($firstQtyBalance!=0)?$total_cost/$firstQtyBalance:0;
                                $line->current_qty = $total_qty;
                                $line->out_price   = ($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0 ;
                                $line->unit_cost   = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0);
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = ( $total_qty == 0)?0:$total_cost ;
                                break;
                            }
                            case "Manufacturing - ( In )":{
                                #.... effect on unit cost & QTY  (+) from stock
                                $total_qty         = ($firstQtyBalance+$qty) ;
                                $total_cost        = $firstTotalBalance + $total_row ;
                                $firstBalance      = ($total_qty!=0)?$total_cost/$total_qty:0;
                                $line->current_qty = $total_qty;
                                $line->unit_cost   = $firstBalance ;
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = $total_cost;
                                break;
                            }
                            case "Wrong - sale<br>More Delivery":{
                                #.... XXX don't effect on unit cost  & QTY (-) from stock XXX
                                $total_qty         = ($firstQtyBalance-$qty) ;
                                $total_cost        = $firstTotalBalance  - (($qty)*($firstTotalBalance/$firstQtyBalance)) ;
                                $firstBalance      = ($firstQtyBalance!=0)?$total_cost/$firstQtyBalance:0;
                                $line->current_qty = $total_qty;
                                $line->out_price   = ($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0 ;
                                $line->unit_cost   = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0);
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = ( $total_qty == 0)?0:$total_cost ;
                                break;
                            }
                            case "Wrong - purchase<br>More Delivery":{
                                #.... effect on unit cost & QTY  (+) from stock
                                $total_qty         = ($firstQtyBalance+$qty) ;
                                $total_cost        = $firstTotalBalance + $total_row ;
                                $firstBalance      = ($total_qty!=0)?$total_cost/$total_qty:0;
                                $line->current_qty = $total_qty;
                                $line->unit_cost   = $firstBalance ;
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = $total_cost;
                                break;
                            }
                            case "Wrong - purchase<br>Other Product":{
                                #.... effect on unit cost & QTY  (+) from stock
                                $total_qty         = ($firstQtyBalance+$qty) ;
                                $total_cost        = $firstTotalBalance + $total_row ;
                                $firstBalance      = ($total_qty!=0)?$total_cost/$total_qty:0;
                                $line->current_qty = $total_qty;
                                $line->unit_cost   = $firstBalance ;
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = $total_cost;
                                break;
                            }
                            case "Wrong - sale<br>Other Product":{
                                #.... XXX don't effect on unit cost  & QTY (-) from stock XXX 
                                $total_qty         = ($firstQtyBalance-$qty) ;
                                $total_cost        = $firstTotalBalance  - (($qty)*($firstTotalBalance/$firstQtyBalance)) ;
                                $firstBalance      = ($firstQtyBalance!=0)?$total_cost/$firstQtyBalance:0;
                                $line->current_qty = $total_qty;
                                $line->out_price   = ($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0 ;
                                $line->unit_cost   = ( $total_qty == 0)?$line->out_price:(($firstQtyBalance!=0)?($firstTotalBalance/$firstQtyBalance):0);
                                $firstQtyBalance   = $total_qty; 
                                $firstTotalBalance = ( $total_qty == 0)?0:$total_cost ;
                                break;
                            }
                            default:{
                                
                                break;
                            }
                        }
                    #..check state....end.
                    #..update unitcost........
                    // $total_qty         = ($signal == "-")?($firstQtyBalance+$qty):($firstQtyBalance-$qty);
                    // $total_cost        = $firstTotalBalance + $total_row ;
                    // $firstQtyBalance   = $total_qty; 
                    // $firstTotalBalance = $total_cost;
                    // $firstBalance      = ($total_qty!=0)?$total_cost/$total_qty:$total_cost;
                    #..update unitcost....end.
                
                
                #..UPDATE ROW INFO...........
                // $line->current_qty = $firstQtyBalance;
                // $line->unit_cost   = $firstBalance;
                // $line->out_price   = $firstBalance;
                $line->update();
                // DD($line);
            }
            return true; 
        }catch(Exception $e){ 
            
            return false; 
        }
    }
    #2#...#new
    public static function CollectAllRowsAfterThisDate($product_id,$date){
        try{ 
            
            return true; 
        }catch(Exception $e){ 
            
            return false; 
        }
    }

}
