<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionSellLine;
use App\Models\TransactionDelivery;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Models\DeliveredPrevious;
use App\Product;
use App\Models\DeliveredWrong;
use App\Models\WarehouseInfo;
use App\MovementWarehouse;
use DB;

class HomeController extends Controller
{
    public function __construct(ProductUtil $productUtil ,TransactionUtil $transactionUtil)
    {        
        $this->productUtil     = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }
    public function index($id,Request $request)
    {
        
        $request->validate([
            'sell_document.*'=>'mimes:jpeg,png,jpg,JPG,PNG,JPEG,PDF,pdf'
        ],[
            'sell_document.mimes'=>trans('home.Please insert  valid document type ')
        ]);
        DB::beginTransaction();
        $company_name      = request()->session()->get("user_main.domain");
        $data      =    Transaction::find($id);
        if(isset($request->separate_sell)){
            $new_separate                     =  $data->replicate();
            $sale_type                        =  'sale';
            $invoice_scheme_id                = ($data->pattern)?$data->pattern->invoice_scheme:null;
            $invoice_no                       =  $this->transactionUtil->getInvoiceNumber($data->business_id, "final", $data->location_id, $invoice_scheme_id, $sale_type);
            $new_separate->invoice_no         = $invoice_no;
            $new_separate->transaction_date   = \Carbon::now();
            $new_separate->status             = "final";
            $new_separate->sub_status         = "final";
            $new_separate->separate_type      = "partial" ;
            $new_separate->separate_parent    = $data->id ;
            $new_separate->created_by         = auth()->user()->id ;
            $new_separate->save();
            $taxes_amount                     = \App\TaxRate::find($new_separate->tax_id);
            $value_tax                        =  ($taxes_amount)?$taxes_amount->amount:0;
            $for_sub_total                    = 0;$sub_total_line= 0;
            if($data->discount_type == "fixed_before_vat"){
                $before_total_discount            = $data->discount_amount;
            }elseif($data->discount_type == "fixed_after_vat"){
                $before_total_discount            = round($data->discount_amount * 100 / (100 + $value_tax),3);
            }else{
                $before_total_discount            = ( $data->total_before_tax * $data->discount_amount ) / 100;
                
            }
            $before_sub_total                 = $data->total_before_tax;
            $before_percentage                = $before_total_discount / $data->total_before_tax;/** discount percentage */
            
            $prev_ids  =  $request->delivered_previouse_id?$request->delivered_previouse_id:[];
            
            $remove    =  DeliveredPrevious::where('transaction_id',$new_separate->id)->whereNotIn('id',$prev_ids)->get();
            foreach ($remove as $re) {
                //update stock info 
                WarehouseInfo::update_stoct($re->product_id,$re->store_id,$re->current_qty,$new_separate->business_id);
                // update stock movement
                MovementWarehouse::where('delivered_previouse_id',$re->id)->delete();
                $q  =  $re->current_qty;
                $this->update_variation($re->product_id,$q,$re->transaction->location_id);
                \App\Models\ItemMove::delete_delivery($new_separate->id,$re->id,$re);
                $re->delete();
                
            }

            $w_ids     =  $request->wrong_ids?$request->wrong_ids:[];
            if($w_ids != []){
            $w_removes =  DeliveredWrong::where('transaction_id',$new_separate->id)->whereNotIn('id',$w_ids)->get();
            }else{
            $w_removes =  [];
            }
            $check_for_wrong = 0 ;
            
            
            if ($request->products) {
                $total       = 0 ;
                $type        = 'trans_delivery';
                $ref_count   = $this->productUtil->setAndGetReferenceCount($type,$new_separate->business_id);
                $receipt_no  = $this->productUtil->generateReferenceNumber($type, $ref_count,$new_separate->business_id);
                $arr         =  [];
                if ($request->sell_document) {
                    foreach ($request->file('sell_document') as $key => $file) {
                        $mime =  $file->getClientOriginalExtension();
                        
                        if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                            $filename =  'uploads/companies/'.$company_name.'/documents/delivery/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                            $file->move('uploads/companies/'.$company_name.'/documents/delivery',$filename);
                            array_push($arr,$filename);
                        }
                    }
                }
                $tr_recieved                  =  new TransactionDelivery;
                $tr_recieved->store_id        =  $new_separate->store;
                $tr_recieved->transaction_id  =  $new_separate->id;
                $tr_recieved->business_id     =  $new_separate->business_id ;
                $tr_recieved->reciept_no      =  $receipt_no ;
                $tr_recieved->invoice_no      =  $new_separate->invoice_no;
                $tr_recieved->is_invoice      =  $new_separate->id;
                $tr_recieved->date            =  ($request->date)?$request->date:\Carbon\Carbon::now();
                $tr_recieved->status          = 'sales';
                $tr_recieved->document        =  json_encode($arr);
                if($request->date != null){
                    $tr_recieved->created_at  = $request->date;
                }
                $tr_recieved->save();
                $x =  0;
                // $unique_id = [] ; 
                
                foreach ($request->products  as $key => $single) {
                    if ($single['quantity'] > 0) {
                       
                        $product  =  Product::find($single['product_id']);
                        $line     =  TransactionSellLine::where('transaction_id',$id)
                                                        ->where('product_id',$single['product_id'])
                                                        ->where("id",$single['price_from_bill'])->first();
                        // if(!in_array($line->id,$unique_id)){
                        //     $unique_id[] = $line->id;
                        // }else{
                        //     $line     =  TransactionSellLine::where('transaction_id',$id)
                        //                                 ->whereNotIn('id',$unique_id)
                        //                                 ->where('product_id',$single['product_id'])
                        //                                 ->first();
                        // }
                        $lineSellTransaction                     = $line->replicate();
                        $lineSellTransaction->transaction_id     = $new_separate->id;
                        $lineSellTransaction->quantity           = $single['quantity'];
                        $for_sub_total                          += $single['quantity'] * ($lineSellTransaction->unit_price_inc_tax - $lineSellTransaction->item_tax);
                        $sub_total_line                         += $before_percentage * ($single['quantity'] * ($lineSellTransaction->unit_price_inc_tax - $lineSellTransaction->item_tax));
                        
                        $lineSellTransaction->save();
                        $margin   =  TransactionSellLine::check_transation_product($new_separate->id,$single['product_id']);
                        $diff     =  $margin -  $single['quantity'];
                        if ($diff > 0) {
                            $date =  $request->date;
                            $this->recieve($new_separate,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$lineSellTransaction,$date);
                        }elseif ($diff < 0 && $lineSellTransaction) {
                            $date = $request->date;
                            ($margin > 0 )? $this->recieve($new_separate,$product,$margin,$request->stores_id[$x],$tr_recieved,$lineSellTransaction,$date):'';
                            $this->wrong_recive($new_separate,$product,abs($diff),$request->stores_id[$x],$tr_recieved,$date);
                            $check_for_wrong = 1 ;
                        }elseif($diff ==  0 && $lineSellTransaction){
                            $date = $request->date;
                            $this->recieve($new_separate,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$lineSellTransaction,$date);
                        }else{
                            $date = $request->date;
                            $this->wrong_recive($new_separate,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$date);
                            $check_for_wrong = 1 ;
                        }
                    }
                    $x++;
                }
            }
            
            //.......... start eb
            if($request->sell_document_ != null){
                foreach($request->sell_document_ as $keys => $value){
                            $trd = \App\Models\TransactionDelivery::find($keys);
                    if ($request->sell_document_) {
                        foreach ($request->file('sell_document_') as $key => $file) {
                            $arr  =  [];
                            $mime =  $file->getClientOriginalExtension();
                            if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                                $filename =  'uploads/companies/'.$company_name.'/documents/delivery/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                                $file->move('uploads/companies/'.$company_name.'/documents/delivery',$filename);
                                array_push($arr,$filename);
                            }
                            $trd->document = json_encode($arr);
                            $trd->save();
                        }
                    }
                }
            }

            $tax_amount                       = \App\TaxRate::find($new_separate->tax_id);
            $value                            = ($tax_amount)?$tax_amount->amount:0;
            $new_separate->total_before_tax   = $for_sub_total ;

            if($new_separate->discount_type == "fixed_after_vat"){
                $new_discount_amount              =  $sub_total_line + ($sub_total_line * $value / (100 ))  ;
                $new_separate->discount_amount    =  $new_discount_amount ;
                $new_separate->tax_amount         = (($for_sub_total - $sub_total_line) * (($value))/100);
                $new_separate->final_total        = (($for_sub_total - $sub_total_line)   + ((($for_sub_total - $sub_total_line)  * ($value))/100));
            }elseif($new_separate->discount_type == "fixed_before_vat"){
                $new_discount_amount              =  $sub_total_line   ;
                $new_separate->discount_amount    =  $new_discount_amount ;
                $new_separate->tax_amount         = (($for_sub_total - $sub_total_line) * (($value))/100);
                $new_separate->final_total        = (($for_sub_total - $sub_total_line)   + ((($for_sub_total - $sub_total_line)  * ($value))/100));
            }else{
                $new_discount_amount              = ( $sub_total_line * 100 /  $for_sub_total ) * 100 ;
                $new_separate->discount_amount    =  $new_discount_amount ;
                $new_separate->tax_amount         = (($for_sub_total - ($sub_total_line * 100 )) * (($value))/100);
                $new_separate->final_total        = (($for_sub_total - ($sub_total_line * 100 ))   + ((($for_sub_total - ($sub_total_line * 100))  * ($value))/100));
            }
            $new_separate->update();
           
             
            \App\AccountTransaction::add_sell_pos($new_separate,$new_separate->pattern_id);
            //........ end eb
            Transaction::update_status($new_separate->id,'deliver');
            
            $sum        =  \App\Models\DeliveredPrevious::where("transaction_id",$new_separate->id)->sum("current_qty");
            $delivery   =  \App\Models\DeliveredPrevious::where("transaction_id",$new_separate->id)->first();
            if(!empty($delivery)){
                \App\Models\StatusLive::insert_data_sd($new_separate->business_id,$new_separate,$delivery,"Add Delivery",$sum);
            }
            $wrongD      =  \App\Models\DeliveredWrong::where("transaction_id",$new_separate->id)->first();
            if(!empty($wrongD)){
                \App\Models\StatusLive::insert_data_sd($new_separate->business_id,$new_separate,$wrongD,"Wrong Delivery",$sum);
            }
            
            \App\Models\ItemMove::create_sell_itemMove($new_separate);
            if( $check_for_wrong == 1){
                \App\Models\ItemMove::Wrong_delivery($new_separate->id,$tr_recieved->id);
            }

        } else{
          

            $data      =    Transaction::find($id);

            $prev_ids  =  $request->delivered_previouse_id?$request->delivered_previouse_id:[];
            
            $remove    =  DeliveredPrevious::where('transaction_id',$id)->where("transaction_recieveds_id",$id)->whereNotIn('id',$prev_ids)->get();
            // foreach ($remove as $re) {
            //     //update stock info 
            //     WarehouseInfo::update_stoct($re->product_id,$re->store_id,$re->current_qty,$data->business_id);
            //     // update stock movement
            //     MovementWarehouse::where('delivered_previouse_id',$re->id)->delete();
            //     $q  =  $re->current_qty;
            //     $this->update_variation($re->product_id,$q,$re->transaction->location_id);
            //     \App\Models\ItemMove::delete_delivery($data->id,$re->id,$re);
            //     $re->delete();
                
            // }

            $w_ids     =  $request->wrong_ids?$request->wrong_ids:[];
            if($w_ids != []){
            $w_removes =  DeliveredWrong::where('transaction_id',$id)->whereNotIn('id',$w_ids)->get();
            }else{
            $w_removes =  [];
            }
            $check_for_wrong = 0 ;

            if ($request->products) {
                
                $total       = 0 ;
                $type        = 'trans_delivery';
                $ref_count   = $this->productUtil->setAndGetReferenceCount($type);
                $receipt_no  = $this->productUtil->generateReferenceNumber($type, $ref_count);
                $arr  =  [];
                if ($request->sell_document) {
                    foreach ($request->file('sell_document') as $key => $file) {
                        $mime =  $file->getClientOriginalExtension();
                        if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                            $filename =  'uploads/companies/'.$company_name.'/documents/delivery/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                            $file->move('uploads/companies/'.$company_name.'/documents/delivery',$filename);
                            array_push($arr,$filename);
                        }
                    }
                }
                $tr_recieved                  =  new TransactionDelivery;
                $tr_recieved->store_id        =  $data->store;
                $tr_recieved->transaction_id  =  $id;
                $tr_recieved->business_id     =  $data->business_id ;
                $tr_recieved->reciept_no      =  $receipt_no ;
                $tr_recieved->invoice_no      =  $data->invoice_no;
                $tr_recieved->date            =  ($request->date)?$request->date:\Carbon\Carbon::now();
                $tr_recieved->status          = 'sales';
                $tr_recieved->document        =  json_encode($arr);
                if($request->date != null){
                    $tr_recieved->created_at  = $request->date;
                }
                $tr_recieved->save();
                $x =  0;
                // $unique_id = [] ; 
                 
                foreach ($request->products  as $key => $single) {
                    if ($single['quantity'] > 0) {
                        $margin   =  TransactionSellLine::check_transation_product($id,$single['product_id']);
                        $diff     =  $margin -  $single['quantity'];
                        $product  =  Product::find($single['product_id']);
                        $line     =  TransactionSellLine::where('transaction_id',$id)
                                                        ->where('product_id',$single['product_id'])
                                                        // ->where("id",$single['price_from_bill'])
                                                        ->first();
                          // if(!in_array($line->id,$unique_id)){
                        //     $unique_id[] = $line->id;
                        // }else{
                        //     $line     =  TransactionSellLine::where('transaction_id',$id)
                        //                                 ->whereNotIn('id',$unique_id)
                        //                                 ->where('product_id',$single['product_id'])
                        //                                 ->first();
                        // }
                       
                        if ($diff > 0) {
                            $date =  $request->date;
                            $this->recieve($data,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$line,$date);
                        }elseif ($diff < 0 && $line) {
                            $date = $request->date;
                            ($margin > 0 )? $this->recieve($data,$product,$margin,$request->stores_id[$x],$tr_recieved,$line,$date):'';
                            
                            $this->wrong_recive($data,$product,abs($diff),$request->stores_id[$x],$tr_recieved,$date);
                            $check_for_wrong = 1 ;
                        }elseif($diff ==  0 && $line){
                            $date = $request->date;
                            $this->recieve($data,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$line,$date);
                        }else{
                            $date = $request->date;
                            $this->wrong_recive($data,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$date);
                            $check_for_wrong = 1 ;
                        }
                    }
                    $x++;
                }
            }
            //.......... start eb
            if($request->sell_document_ != null){
                foreach($request->sell_document_ as $keys => $value){
                            $trd = \App\Models\TransactionDelivery::find($keys);
                    if ($request->sell_document_) {
                        foreach ($request->file('sell_document_') as $key => $file) {
                            $arr  =  [];
                            $mime =  $file->getClientOriginalExtension();
                            if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                                $filename =  'uploads/companies/'.$company_name.'/documents/delivery/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                                $file->move('uploads/companies/'.$company_name.'/documents/delivery',$filename);
                                array_push($arr,$filename);
                            }
                            $trd->document = json_encode($arr);
                            $trd->save();
                        }
                    }
                }
            }
            //........ end eb
            Transaction::update_status($id,'deliver');
            
            $sum        =  \App\Models\DeliveredPrevious::where("transaction_id",$data->id)->sum("current_qty");
            $delivery   =  \App\Models\DeliveredPrevious::where("transaction_id",$data->id)->first();
            if(!empty($delivery)){
            \App\Models\StatusLive::insert_data_sd($data->business_id,$data,$delivery,"Add Delivery",$sum);
            }
            $wrongD      =  \App\Models\DeliveredWrong::where("transaction_id",$data->id)->first();
            if(!empty($wrongD)){
            \App\Models\StatusLive::insert_data_sd($data->business_id,$data,$wrongD,"Wrong Delivery",$sum);
            }
            
            \App\Models\ItemMove::create_sell_itemMove($data);
            if( $check_for_wrong == 1){
                \App\Models\ItemMove::Wrong_delivery($data->id,$tr_recieved->id);
            }
        }
        DB::commit();
        
        if(app("request")->input("approved")){
            return redirect('sells/QuatationApproved')
                ->with('yes',trans('home.Done Successfully'));
        }else{
            return redirect('sells')
                ->with('yes',trans('home.Done Successfully'));
        }
    }
    public function update($id,Request $request)
    {
        $request->validate([
            'sell_document.*'=>'mimes:jpeg,png,jpg,JPG,PNG,JPEG,PDF,pdf'
        ],[
            'sell_document.mimes'=>trans('home.Please insert  valid document type ')
        ]);
        DB::beginTransaction();
        
        $td        =    TransactionDelivery::find($id);
        $company_name      = request()->session()->get("user_main.domain");
        if($td->is_invoice != null){
            
            $data                             =  Transaction::find($td->transaction_id);
            $taxes_amount                     =  \App\TaxRate::find($data->tax_id);
            $value_tax                        =  ($taxes_amount)?$taxes_amount->amount:0;
            /** START  */
            if($data->discount_type == "fixed_before_vat"){
                $before_total_discount            = $data->discount_amount;
            }elseif($data->discount_type == "fixed_after_vat"){
                $before_total_discount            = round($data->discount_amount * 100 / (100 + $value_tax),3);
            }else{
                $before_total_discount            = ( $data->total_before_tax / $data->discount_amount ) / 100;
                
            }
            $before_sub_total                 = $data->total_before_tax;
            $before_percentage                = $before_total_discount / $data->total_before_tax;
            /** END */ 
            $prev_ids  =  $request->delivered_previouse_id?$request->delivered_previouse_id:[];

            $remove    =  DeliveredPrevious::where('transaction_id',$data->id)
                                                ->where("transaction_recieveds_id",$id)
                                                ->whereNotIn('id',$prev_ids)
                                                ->get();
            $for_sub_total  = 0;
            $sub_total_line = 0;

            foreach ($remove as $re) {
                //update stock info 
                WarehouseInfo::update_stoct($re->product_id,$re->store_id,$re->current_qty,$data->business_id);
                // update stock movement
                MovementWarehouse::where('delivered_previouse_id',$re->id)->delete();
                $q  =  $re->current_qty;
                $this->update_variation($re->product_id,$q,$re->transaction->location_id);
                \App\Models\ItemMove::delete_delivery($data->id,$re->id,$re);
                $re->delete();
            }

            $check_for_wrong = 0 ;
            if ($request->delivered_previouse_id ) {
                foreach ($request->delivered_previouse_id as $key => $pr_id) {
                    $check             =  0;
                    $prev              =  DeliveredPrevious::find($pr_id);
                    $diff              =  $prev->current_qty  - $request->delivered_previouse_qty[$key];
                    
                    if($request->old_store_id[$key] != $prev->store_id){
                        $check         = 1;
                        \App\Models\WarehouseInfo::where("store_id",$prev->store_id)
                                                    ->where("product_id",$prev->product_id)
                                                    ->increment("product_qty" , $prev->current_qty);
                        if($diff == 0){
                            \App\Models\WarehouseInfo::where("store_id",$request->old_store_id[$key])
                                                    ->where("product_id",$prev->product_id)
                                                    ->decrement("product_qty" , $request->delivered_previouse_qty[$key]);
                        } 
                    }

                    $prev->store_id    =  $request->old_store_id[$key];
                    $prev->created_at  =  $request->dates[$pr_id];
                    $prev->current_qty =  $request->delivered_previouse_qty[$key];
                    $prev->save();

                    /**  HERE NEED JUST CHANGE QUANTITY WITHOUT PRICES  */
                    $product_id         =  $prev->product_id;
                    $product_qty        =  $prev->current_qty;
                    $product_line       =  $prev->line_id;
                    // SEARCH **** //
                    $sel_line           = \App\TransactionSellLine::find($product_line);
                    $sel_line->quantity = $product_qty;
                    $sel_line->save();
                    /** END */


                    if($check == 0){
                        WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$diff,$data->business_id);
                    }else{
                        if($diff != 0){
                            \App\Models\WarehouseInfo::where("store_id",$request->old_store_id[$key])
                                                    ->where("product_id",$prev->product_id)
                                                    ->decrement("product_qty" , $request->delivered_previouse_qty[$key]);
                        }
                    } 
                    
                    // MovementWarehouse::sell_return($prev->id,$prev->current_qty);
                    $info =  WarehouseInfo::where('store_id',$prev->store_id)
                                                ->where('product_id',$prev->product_id)
                                                ->first();
                    MovementWarehouse::where('delivered_previouse_id',$prev->id)
                                            ->update([
                                                'store_id'    => $prev->store_id,
                                                'minus_qty'   => $prev->current_qty,
                                                'current_qty' => $info->product_qty,
                                                'date'        => $request->date_old
                                            ]);
                    $this->update_variation($prev->product_id,$diff,$prev->transaction->location_id);

                }
            }

            if ($request->wrong_ids){
                $check_for_wrong = 1;
            }
            
            $w_ids     =  $request->wrong_ids?$request->wrong_ids:[];
            $w_removes =  DeliveredWrong::where("transaction_recieveds_id",$id)->whereNotIn('id',$w_ids)->get();
            

            foreach ($w_removes as $re) {
                WarehouseInfo::update_stoct($re->product_id,$re->store_id,$re->current_qty,$data->business_id);
                MovementWarehouse::where('delivered_wrong_id',$re->id)->delete();
                $q  =  $re->current_qty;
                $this->update_variation($re->product_id,$q,$re->transaction->location_id);
                $array_del      = [];
                $line_id_        = [];
                $product_id     = [];
                $move_id        = []; 
            
                if(!in_array($re->product_id,$line_id_)){
                $line_id_[]    = $re->product_id;
                $product_id[]  = $re->product_id;
                }
                $wrongMove = \App\Models\ItemMove::where("transaction_id",$data->id)->where("recieve_id",$re->id)->first();
                if(!empty($wrongMove)){
                $move_id[] = $wrongMove->id; 
                }
                if(!empty($wrongMove)){
                $wrongMove->delete();
                \App\Models\ItemMove::refresh_item($wrongMove->id,$re->product_id);
                $move_all  = \App\Models\ItemMove::where("product_id",$re->product_id)
                                                        ->whereNotIn("id",$move_id)
                                                        ->get(); 
                if(count($move_all)>0){
                    foreach($move_all as $key =>  $it){
                        \App\Models\ItemMove::refresh_item($it->id,$it->product_id );
                    }
                }
                }
                $re->delete();
            }

            $tr_recieved                  =  TransactionDelivery::find($id);
            $tr_recieved->date            =  $request->date_old;
            $arr       = [];
            if ($request->sell_document) {
                foreach ($request->file('sell_document') as $key => $file) {
                    $mime =  $file->getClientOriginalExtension();
                    if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                        $filename =  'uploads/companies/'.$company_name.'/documents/delivery/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                        $file->move('uploads/companies/'.$company_name.'/documents/delivery',$filename);
                        array_push($arr,$filename);
                    }
                }
                $tr_recieved->document        =  json_encode($arr);
                
            } 
            $tr_recieved->save();

            if ($request->products) {
                $total     = 0 ;
                $type      = 'trans_delivery';
                $arr       = [];
                // $tr_recieved                  =  TransactionDelivery::find($id);
                // if ($request->sell_document) {
                //     foreach ($request->file('sell_document') as $key => $file) {
                //         $mime =  $file->getClientOriginalExtension();
                //         if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                //             $filename =  'uploads/companies/'.$company_name.'/documents/delivery/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                //             $file->move('uploads/companies/'.$company_name.'/documents/delivery',$filename);
                //             array_push($arr,$filename);
                //         }
                //     }
                // $tr_recieved->document        =  json_encode($arr);
                // $tr_recieved->save();
                // } 
                $x =  0;
                
                foreach ($request->products  as $key => $single) {
                    if ($single['quantity'] > 0) {
                        
                        $line_new           =  TransactionSellLine::where('transaction_id',$data->id)
                                                                ->where('product_id',$single['product_id'])->first();
                        $line_new->quantity = $single['quantity'];                    
                        $line_new->update();                    
                        $margin   =  TransactionSellLine::check_transation_product($data->id,$single['product_id']);
                        $diff     =  $margin -  $single['quantity'];
                    
                        $product  =  Product::find($single['product_id']);
                        $line     =  TransactionSellLine::where('transaction_id',$data->id)
                                            ->where('product_id',$single['product_id'])->first();
                        if ($diff > 0) {
                        $date =  $request->date;
                        $this->recieve($data,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$line,$date);
                        }elseif ($diff < 0 && $line) {
                        // correct  recieve
                        $date = $request->date;
                        ($margin > 0 )? $this->recieve($data,$product,$margin,$request->stores_id[$x],$tr_recieved,$line,$date):'';
                        //wrong  recieve
                        $this->wrong_recive($data,$product,abs($diff),$request->stores_id[$x],$tr_recieved,$date);
                        $check_for_wrong = 1 ;
                        }elseif($diff ==  0 && $line){
                        $date = $request->date;
                        $this->recieve($data,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$line,$date);
                        }else{
                        $date = $request->date;
                        $this->wrong_recive($data,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$date);
                        $check_for_wrong = 1 ;

                        }
                    }
                    $x++;
                }
            } 

            //.......... start eb
            if($request->sell_document_ != null){
                foreach($request->sell_document_ as $keys => $value){
                            $trd = \App\Models\TransactionDelivery::find($keys);
                    if ($request->sell_document_) {
                        foreach ($request->file('sell_document_') as $key => $file) {
                            $arr  =  [];
                            $mime =  $file->getClientOriginalExtension();
                            if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                                $filename =  'uploads/companies/'.$company_name.'/documents/delivery/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                                $file->move('uploads/companies/'.$company_name.'/documents/delivery',$filename);
                                array_push($arr,$filename);
                            }
                            $trd->document = json_encode($arr);
                            $trd->save();
                        }
                    }
                }
            }
            
            $transaction_new = \App\Transaction::find($td->transaction_id);
            $lines_sells     = \App\TransactionSellLine::where("transaction_id",$td->is_invoice)->get();
            foreach($lines_sells as $li){
                $for_sub_total      += $sel_line->quantity * ($sel_line->unit_price_inc_tax - $sel_line->item_tax);
                $sub_total_line     += $before_percentage  * ($sel_line->quantity * ($sel_line->unit_price_inc_tax - $sel_line->item_tax));
            }

            $tax_amount                          = \App\TaxRate::find($transaction_new->tax_id);
            $value                               = ($tax_amount)?$tax_amount->amount:0;
            $transaction_new->total_before_tax   = $for_sub_total ;
            if($transaction_new->discount_type == "fixed_after_vat"){
                $new_discount_amount              =  $sub_total_line + ($sub_total_line * $value / (100 ))  ;
                $transaction_new->discount_amount    =  $new_discount_amount ;
                $transaction_new->tax_amount         = (($for_sub_total - $sub_total_line) * (($value))/100);
                $transaction_new->final_total        = (($for_sub_total - $sub_total_line)   + ((($for_sub_total - $sub_total_line)  * ($value))/100));
            }elseif($transaction_new->discount_type == "fixed_before_vat"){
                $new_discount_amount              =  $sub_total_line   ;
                $transaction_new->discount_amount    =  $new_discount_amount ;
                $transaction_new->tax_amount         = (($for_sub_total - $sub_total_line) * (($value))/100);
                $transaction_new->final_total        = (($for_sub_total - $sub_total_line)   + ((($for_sub_total - $sub_total_line)  * ($value))/100));
            }else{
                $new_discount_amount              = ( $sub_total_line * 100 /  $for_sub_total ) * 100 ;
                $transaction_new->discount_amount    =  $new_discount_amount ;
                $transaction_new->tax_amount         = (($for_sub_total - ($sub_total_line * 100 )) * (($value))/100);
                $transaction_new->final_total        = (($for_sub_total - ($sub_total_line * 100 ))   + ((($for_sub_total - ($sub_total_line * 100))  * ($value))/100));
            }
            $transaction_new->update() ;

            $old_trans           = $transaction_new->cost_center_id;
            $old_account         = $transaction_new->contact_id;
            $old_discount        = $transaction_new->discount_amount;
            $old_tax             = $transaction_new->tax_amount;
            $pattern_id          = $transaction_new->pattern_id;
            \App\AccountTransaction::update_sell_pos_($transaction_new,null,$old_trans,$old_account,$old_discount,$old_tax,$pattern_id,$pattern_id);
            //........ end eb
            Transaction::update_status($id,'deliver');

            // $sum        =  \App\Models\DeliveredPrevious::where("transaction_id",$data->id)->sum("current_qty");
            // $delivery   =  \App\Models\DeliveredPrevious::where("transaction_id",$data->id)->first();
            // if(!empty($delivery)){
            //     \App\Models\StatusLive::insert_data_sd($data->business_id,$data,$delivery,"Add Delivery",$sum);
            // }
            // $wrongD      =  \App\Models\DeliveredWrong::where("transaction_id",$data->id)->first();
            // if(!empty($wrongD)){
            //     \App\Models\StatusLive::insert_data_sd($data->business_id,$data,$wrongD,"Wrong Delivery",$sum);
            // }
            \App\Models\ItemMove::create_sell_itemMove($data);
            if( $check_for_wrong == 1){
                \App\Models\ItemMove::Wrong_delivery($data->id,$tr_recieved->id);
            }
        }else{
        
            $data      =    Transaction::find($td->transaction_id);

            $prev_ids  =  $request->delivered_previouse_id?$request->delivered_previouse_id:[];

            $remove    =  DeliveredPrevious::where('transaction_id',$data->id)
                                                ->where("transaction_recieveds_id",$id)
                                                ->whereNotIn('id',$prev_ids)
                                                ->get();
            
            foreach ($remove as $re) {
                //update stock info 
                WarehouseInfo::update_stoct($re->product_id,$re->store_id,$re->current_qty,$data->business_id);
                // update stock movement
                MovementWarehouse::where('delivered_previouse_id',$re->id)->delete();
                $q  =  $re->current_qty;
                $this->update_variation($re->product_id,$q,$re->transaction->location_id);
                \App\Models\ItemMove::delete_delivery($data->id,$re->id,$re);
                $re->delete();
            }

            $check_for_wrong = 0 ;
            if ($request->delivered_previouse_id ) {
                foreach ($request->delivered_previouse_id as $key => $pr_id) {
                    $check             =  0;
                    $prev              =  DeliveredPrevious::find($pr_id);
                    $diff              =  $prev->current_qty  - $request->delivered_previouse_qty[$key];
                    if($request->old_store_id[$key] != $prev->store_id){
                        $check         = 1;
                        \App\Models\WarehouseInfo::where("store_id",$prev->store_id)
                                                    ->where("product_id",$prev->product_id)
                                                    ->increment("product_qty" , $prev->current_qty);
                        if($diff == 0){
                            \App\Models\WarehouseInfo::where("store_id",$request->old_store_id[$key])
                                                    ->where("product_id",$prev->product_id)
                                                    ->decrement("product_qty" , $request->delivered_previouse_qty[$key]);
                        } 
                    }
                    $prev->store_id    =  $request->old_store_id[$key];
                    $prev->created_at  =  $request->dates[$pr_id];
                    $prev->current_qty =  $request->delivered_previouse_qty[$key];
                
                    $prev->save();
                    if($check == 0){
                        WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$diff,$data->business_id);
                    }else{
                        if($diff != 0){
                            \App\Models\WarehouseInfo::where("store_id",$request->old_store_id[$key])
                                                    ->where("product_id",$prev->product_id)
                                                    ->decrement("product_qty" , $request->delivered_previouse_qty[$key]);
                        }
                    } 
                    
                    // MovementWarehouse::sell_return($prev->id,$prev->current_qty);
                    $info =  WarehouseInfo::where('store_id',$prev->store_id)
                                                ->where('product_id',$prev->product_id)
                                                ->first();
                    MovementWarehouse::where('delivered_previouse_id',$prev->id)
                                        ->update([
                                                    'store_id'    => $prev->store_id,
                                                    'minus_qty'   => $prev->current_qty,
                                                    'current_qty' => $info->product_qty,
                                                    'date'        => $request->date_old
                                            ]);
                    $this->update_variation($prev->product_id,$diff,$prev->transaction->location_id);

                }
            }

            if ($request->wrong_ids){
                $check_for_wrong = 1;
            }
            
            $w_ids     =  $request->wrong_ids?$request->wrong_ids:[];
            $w_removes =  DeliveredWrong::where("transaction_recieveds_id",$id)->whereNotIn('id',$w_ids)->get();
            

            foreach ($w_removes as $re) {
                WarehouseInfo::update_stoct($re->product_id,$re->store_id,$re->current_qty,$data->business_id);
                MovementWarehouse::where('delivered_wrong_id',$re->id)->delete();
                $q  =  $re->current_qty;
                $this->update_variation($re->product_id,$q,$re->transaction->location_id);
                $array_del      = [];
                $line_id_        = [];
                $product_id     = [];
                $move_id        = []; 
            
                if(!in_array($re->product_id,$line_id_)){
                $line_id_[]    = $re->product_id;
                $product_id[]  = $re->product_id;
                }
                $wrongMove = \App\Models\ItemMove::where("transaction_id",$data->id)->where("recieve_id",$re->id)->first();
                if(!empty($wrongMove)){
                $move_id[] = $wrongMove->id; 
                }
                if(!empty($wrongMove)){
                $wrongMove->delete();
                \App\Models\ItemMove::refresh_item($wrongMove->id,$re->product_id);
                $move_all  = \App\Models\ItemMove::where("product_id",$re->product_id)
                                                        ->whereNotIn("id",$move_id)
                                                        ->get(); 
                if(count($move_all)>0){
                    foreach($move_all as $key =>  $it){
                        \App\Models\ItemMove::refresh_item($it->id,$it->product_id );
                    }
                }
                }
                $re->delete();
            }

            $tr_recieved                  =  TransactionDelivery::find($id);
            $tr_recieved->date            =  $request->date_old;
            $arr       = [];
            if ($request->sell_document) {
                foreach ($request->file('sell_document') as $key => $file) {
                    $mime =  $file->getClientOriginalExtension();
                    if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                        $filename =  'uploads/documents/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                        $file->move('uploads/documents',$filename);
                        array_push($arr,$filename);
                    }
                }
                $tr_recieved->document        =  json_encode($arr);
                
            } 
            $tr_recieved->save();

            if ($request->products) {
                $total     = 0 ;
                $type      = 'trans_delivery';
                $arr       = [];
                // $tr_recieved                  =  TransactionDelivery::find($id);
                // if ($request->sell_document) {
                //     foreach ($request->file('sell_document') as $key => $file) {
                //         $mime =  $file->getClientOriginalExtension();
                //         if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                //             $filename =  'uploads/documents/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                //             $file->move('uploads/documents',$filename);
                //             array_push($arr,$filename);
                //         }
                //     }
                // $tr_recieved->document        =  json_encode($arr);
                // $tr_recieved->save();
                // } 
                $x =  0;
                
                foreach ($request->products  as $key => $single) {
                    if ($single['quantity'] > 0) {
                        $margin   =  TransactionSellLine::check_transation_product($data->id,$single['product_id']);
                        $diff     =  $margin -  $single['quantity'];
                    
                        $product  =  Product::find($single['product_id']);
                        $line     =  TransactionSellLine::where('transaction_id',$data->id)
                                            ->where('product_id',$single['product_id'])->first();
                        if ($diff > 0) {
                        $date =  $request->date;
                        $this->recieve($data,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$line,$date);
                        }elseif ($diff < 0 && $line) {
                        // correct  recieve
                        $date = $request->date;
                        ($margin > 0 )? $this->recieve($data,$product,$margin,$request->stores_id[$x],$tr_recieved,$line,$date):'';
                        //wrong  recieve
                        $this->wrong_recive($data,$product,abs($diff),$request->stores_id[$x],$tr_recieved,$date);
                        $check_for_wrong = 1 ;
                        }elseif($diff ==  0 && $line){
                        $date = $request->date;
                        $this->recieve($data,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$line,$date);
                        }else{
                        $date = $request->date;
                        $this->wrong_recive($data,$product,$single['quantity'],$request->stores_id[$x],$tr_recieved,$date);
                        $check_for_wrong = 1 ;

                        }
                    }
                    $x++;
                }
            }else{
                
            }

            //.......... start eb
            if($request->sell_document_ != null){
                foreach($request->sell_document_ as $keys => $value){
                            $trd = \App\Models\TransactionDelivery::find($keys);
                    if ($request->sell_document_) {
                        foreach ($request->file('sell_document_') as $key => $file) {
                            $arr  =  [];
                            $mime =  $file->getClientOriginalExtension();
                            if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                                $filename =  'uploads/documents/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                                $file->move('uploads/documents',$filename);
                                array_push($arr,$filename);
                            }
                            $trd->document = json_encode($arr);
                            $trd->save();
                        }
                    }
                }
            }

            //........ end eb
            Transaction::update_status($id,'deliver');

            // $sum        =  \App\Models\DeliveredPrevious::where("transaction_id",$data->id)->sum("current_qty");
            // $delivery   =  \App\Models\DeliveredPrevious::where("transaction_id",$data->id)->first();
            // if(!empty($delivery)){
            //     \App\Models\StatusLive::insert_data_sd($data->business_id,$data,$delivery,"Add Delivery",$sum);
            // }
            // $wrongD      =  \App\Models\DeliveredWrong::where("transaction_id",$data->id)->first();
            // if(!empty($wrongD)){
            //     \App\Models\StatusLive::insert_data_sd($data->business_id,$data,$wrongD,"Wrong Delivery",$sum);
            // }
            \App\Models\ItemMove::create_sell_itemMove($data);
            if( $check_for_wrong == 1){
                \App\Models\ItemMove::Wrong_delivery($data->id,$tr_recieved->id);
            }
        } 
        DB::commit();
        if(app("request")->input("approved")){
            return redirect('sells/QuatationApproved')
                ->with('yes',trans('home.Done Successfully'));
        }else{
            return redirect('sells')
                ->with('yes',trans('home.Done Successfully'));
        }
    }
    public function recieve($data,$product,$quantity,$store_id,$tr_recieved,$line,$date=null)
    {
        $prev                             =  new DeliveredPrevious;
        $prev->product_id                 =  $product->id;
        $prev->store_id                   =  $store_id;
        $prev->business_id                =  $data->business_id ;
        $prev->transaction_id             =  $data->id;
        $prev->unit_id                    =  $product->unit_id;
        $prev->total_qty                  =  $line->quantity;
        $prev->current_qty                =  $quantity;
        $prev->remain_qty                 =  0;
        $prev->transaction_recieveds_id   =  $tr_recieved->id;
        $prev->product_name               =  $product->name;
        $prev->line_id                    =  $line->id;
         if($date != null){
            $prev->created_at             =  $date;
        }
         
        $prev->save();
        WarehouseInfo::deliver_stoct($product->id,$store_id,$quantity,null,$data->business_id);
        MovementWarehouse::movemnet_warehouse_sell($data,$product,$quantity,$store_id,$line,$prev->id);
        $this->productUtil->decreaseProductQuantity(
            $product->id,
            $line->variation_id,
            $data->location_id,
            $quantity
        );
    }
    public function wrong_recive($data,$product,$quantity,$store_id,$tr_recieved,$date =null)
    {
            $prev                             =  new DeliveredWrong;
            $prev->product_id                 =  $product->id;
            $prev->store_id                   =  $store_id;
            $prev->business_id                =  $data->business_id ;
            $prev->transaction_id             =  $data->id;
            $prev->unit_id                    =  $product->unit_id;
            $prev->total_qty                  =  0;
            $prev->current_qty                =  $quantity;
            $prev->remain_qty                 =  ($quantity*-1);
            $prev->transaction_recieveds_id   =  $tr_recieved->id;
            $prev->product_name               =  $product->name;
              if($date != null){
                $prev->created_at             = $date;
            }
            $prev->save();
            WarehouseInfo::deliver_stoct($product->id,$store_id,$quantity,null,$data->business_id);
            $line =  DeliveredPrevious::OrderBy('id','desc')->where('product_id',$product->id)->first();
            MovementWarehouse::movemnet_warehouse_sell($data,$product,$quantity,$store_id,$line,NULL,$prev->id);
            $this->productUtil->decreaseProductQuantity(
                $product->id,
                $product->id,
                $data->location_id,
                $quantity
            );
            
    }
    public function update_variation($id,$quantity,$location)
    {
         $data  =  \App\VariationLocationDetails::where('product_id',$id)
                              ->where('location_id',$location)->first();
         if ($data) {
            $data->qty_available =  $data->qty_available + $quantity;
            $data->save();
         }
    }
    public static function all_item($transaction,$arr)
    {
        \App\Services\Sells\Delivery::index($transaction,$arr);
    }

    
    
}
