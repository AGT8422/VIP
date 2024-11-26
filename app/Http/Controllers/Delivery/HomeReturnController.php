<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionSellLine;
use App\Models\TransactionDelivery;
use App\Utils\ProductUtil;
use App\Models\DeliveredPrevious;
use App\Product;
use App\Models\DeliveredWrong;
use App\Models\WarehouseInfo;
use App\MovementWarehouse;
use DB;

class HomeReturnController extends Controller
{
    public function __construct(ProductUtil $productUtil)
    {        
        $this->productUtil = $productUtil;
    }
    public function index($id,Request $request)
    {
         
        $request->validate([
            'sell_document.*'=>'mimes:jpeg,png,jpg,JPG,PNG,JPEG,PDF,pdf'
        ],[
            'sell_document.mimes'=>trans('home.Please insert  valid document type ')
        ]);
        DB::beginTransaction();

        $data      =    Transaction::find($id);

        $prev_ids  =  $request->delivered_previouse_id?$request->delivered_previouse_id:[];
        
        $remove    =  DeliveredPrevious::where('transaction_id',$id)->whereNotIn('id',$prev_ids)->get();
        // foreach ($remove as $re) {
        //     //update stock info 
        //     WarehouseInfo::update_stoct($re->product_id,$re->store_id,$re->current_qty*-1,$data->business_id);
        //     // update stock movement
        //     MovementWarehouse::where('delivered_previouse_id',$re->id)->delete();
        //     $q  =  $re->current_qty;
        //     $this->update_variation($re->product_id,$q*-1,$re->transaction->location_id);
        //     \App\Models\ItemMove::delete_delivery($data->id,$re->id,$re);
        //     $re->delete();
            
        // }

        $w_ids     =  $request->wrong_ids?$request->wrong_ids:[];
        $w_removes =  DeliveredWrong::where('transaction_id',$id)->whereNotIn('id',$w_ids)->get();

        // foreach ($w_removes as $re) {
        //     WarehouseInfo::update_stoct($re->product_id,$re->store_id,$re->current_qty*-1,$data->business_id);
        //     MovementWarehouse::where('delivered_wrong_id',$re->id)->delete();
        //     $q  =  $re->current_qty;
        //     $this->update_variation($re->product_id,$q*-1,$re->transaction->location_id);
        //     $array_del      = [];
        //     $line_id_        = [];
        //     $product_id     = [];
        //     $move_id        = []; 
          
        //     if(!in_array($re->product_id,$line_id_)){
        //       $line_id_[]    = $re->product_id;
        //       $product_id[] = $re->product_id;
        //     }
        //     $wrongMove = \App\Models\ItemMove::where("transaction_id",$data->id)->where("recieve_id",$re->id)->first();
        //      if(!empty($wrongMove)){
        //       $move_id[] = $wrongMove->id; 
        //     }
        //     if(!empty($wrongMove)){
        //       $wrongMove->delete();
        //       \App\Models\ItemMove::refresh_item($wrongMove->id,$re->product_id);
        //       $move_all  = \App\Models\ItemMove::where("product_id",$re->product_id)
        //                                               ->whereNotIn("id",$move_id)
        //                                               ->get(); 
        //       if(count($move_all)>0){
        //           foreach($move_all as $key =>  $it){
        //               \App\Models\ItemMove::refresh_item($it->id,$it->product_id );
        //           }
        //       }
        //     }
        //     $re->delete();
        // }
        $check_for_wrong = 0 ;

        if ($request->products) {
            
            $total   = 0 ;
            $type  = 'trans_delivery';
            $ref_count = $this->productUtil->setAndGetReferenceCount($type);
            $reciept_no  = $this->productUtil->generateReferenceNumber($type, $ref_count);
            $arr  =  [];
            if ($request->sell_document) {
                foreach ($request->file('sell_document') as $key => $file) {
                    $mime =  $file->getClientOriginalExtension();
                    if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                        $filename =  'uploads/documents/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                        $file->move('uploads/documents',$filename);
                        array_push($arr,$filename);
                    }
                }
            }
            $tr_recieved                  =  new TransactionDelivery;
            $tr_recieved->store_id        =  $data->store;
            $tr_recieved->transaction_id  =  $id;
            $tr_recieved->business_id     =  $data->business_id ;
            $tr_recieved->reciept_no      =  $reciept_no ;
            $tr_recieved->invoice_no      =  $data->invoice_no;
            $tr_recieved->is_returned     =  1;
            //$tr_recieved->ref_no        =  $data->ref_no;
            $tr_recieved->date            = ($request->date)?$request->date:\Carbon\Carbon::now();
            $tr_recieved->status          = 'return  sales';
            $tr_recieved->document        =  json_encode($arr);
            if($request->date != null){
                $tr_recieved->created_at  = $request->date;
            }
            $tr_recieved->save();
            $x =  0;
            $list_product_sending = [];
            foreach ($request->products  as $key => $single) {
                if ($single['quantity'] > 0) {
                    $tr = \App\Transaction::where("id",$data->return_parent_id)->first();
                    $margin =  TransactionSellLine::check_transation_product_return($tr->id,$single['product_id']);
                    $diff   =  $margin -  $single['quantity'];
                    $product  =  Product::find($single['product_id']);
                    if(!in_array($single['product_id'],$list_product_sending)){
                        
                        $list_product_sending[] = $single['product_id'];
                    }
                    $line     =  TransactionSellLine::where('transaction_id',$tr->id)
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
        
        $sum        =  \App\Models\DeliveredPrevious::where("transaction_id",$data->id)->sum("current_qty");
        $delivery   =  \App\Models\DeliveredPrevious::where("transaction_id",$data->id)->first();
        if(!empty($delivery)){
        \App\Models\StatusLive::insert_data_sd($data->business_id,$data,$delivery,"Add Delivery",$sum);
        }
        $wrongD      =  \App\Models\DeliveredWrong::where("transaction_id",$data->id)->first();
        if(!empty($wrongD)){
        \App\Models\StatusLive::insert_data_sd($data->business_id,$data,$wrongD,"Wrong Delivery",$sum);
        }
        
        \App\Models\ItemMove::return_sale_delivery($data,null,$list_product_sending);
        if( $check_for_wrong == 1){
            \App\Models\ItemMove::Wrong_delivery_return($data,$tr_recieved->id);
        }
        DB::commit();
        return redirect('sell-return')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function update($id,Request $request)
    {
        $request->validate([
            'sell_document.*'=>'mimes:jpeg,png,jpg,JPG,PNG,JPEG,PDF,pdf'
        ],[
            'sell_document.mimes'=>trans('home.Please insert  valid document type ')
        ]);
        DB::beginTransaction();
        
        $td        =  TransactionDelivery::find($id);

        $data      =  Transaction::find($td->transaction_id);

        $prev_ids  =  $request->delivered_previouse_id?$request->delivered_previouse_id:[];

        //... remove not exist
        $remove    =  DeliveredPrevious::where('transaction_id',$data->id)
                                             ->where("transaction_recieveds_id",$id)
                                             ->whereNotIn('id',$prev_ids)
                                             ->get();
        foreach ($remove as $re) {
            //update stock info 
            WarehouseInfo::update_stoct($re->product_id,$re->store_id,$re->current_qty*-1,$data->business_id);
            // update stock movement
            MovementWarehouse::where('delivered_previouse_id',$re->id)->delete();
            $q  =  $re->current_qty;
            $this->update_variation($re->product_id,$q*-1,$re->transaction->location_id);
            \App\Models\ItemMove::delete_delivery($data->id,$re->id,$re);
            $re->delete();
        }
       
        //... new item
        if ($request->delivered_previouse_id ) {
            foreach ($request->delivered_previouse_id as $key => $pr_id) {
                $check             =  0;
                $prev              =  DeliveredPrevious::find($pr_id);
                $diff              =  $prev->current_qty  - $request->delivered_previouse_qty[$key];
                if($request->old_store_id[$key] != $prev->store_id){
                    $check         = 1;
                    \App\Models\WarehouseInfo::where("store_id",$prev->store_id)
                                                   ->where("product_id",$prev->product_id)
                                                   ->decrement("product_qty" , $prev->current_qty);
                    if($diff == 0){
                        \App\Models\WarehouseInfo::where("store_id",$request->old_store_id[$key])
                                                   ->where("product_id",$prev->product_id)
                                                   ->increment("product_qty" , $request->delivered_previouse_qty[$key]);
                    } 
                }
                $prev->store_id    =  $request->old_store_id[$key];
                $prev->created_at  =  $request->dates[$pr_id];
                $prev->current_qty =  $request->delivered_previouse_qty[$key];
               
                $prev->save();
               
                if($check == 0){
                    WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$diff*-1,$data->business_id);
                }else{
                    if($diff != 0){
                        \App\Models\WarehouseInfo::where("store_id",$request->old_store_id[$key])
                                                   ->where("product_id",$prev->product_id)
                                                   ->increment("product_qty" , $request->delivered_previouse_qty[$key]);
                    }
                } 
                
                // MovementWarehouse::sell_return($prev->id,$prev->current_qty);
                $info =  WarehouseInfo::where('store_id',$prev->store_id)
                                            ->where('product_id',$prev->product_id)
                                            ->first();
                
                MovementWarehouse::where('delivered_previouse_id',$prev->id)
                                       ->update([
                                                'store_id'     => $prev->store_id,
                                                'plus_qty'     => $prev->current_qty,
                                                'minus_qty'    => 0,
                                                'current_qty'  => $info->product_qty,
                                                'date'         => $request->date_old
                                        ]);
                $this->update_variation($prev->product_id,$diff,$prev->transaction->location_id);

            }
        }

        //... remove item
        $w_ids     =  $request->wrong_ids?$request->wrong_ids:[];
        $w_removes =  DeliveredWrong::where("transaction_recieveds_id",$id)->whereNotIn('id',$w_ids)->get();
        $w_removes_return =  DeliveredWrong::where("transaction_recieveds_id",$id)->whereIn('id',$w_ids)->get();
        $check_for_wrong = 0 ;
        foreach ($w_removes as $re) {
            WarehouseInfo::update_stoct($re->product_id,$re->store_id,$re->current_qty*-1,$data->business_id);
            MovementWarehouse::where('delivered_wrong_id',$re->id)->delete();
            $q  =  $re->current_qty;
            $this->update_variation($re->product_id,$q*-1,$re->transaction->location_id);
            $array_del      = [];
            $line_id_        = [];
            $product_id     = [];
            $move_id        = []; 
          
            if(!in_array($re->product_id,$line_id_)){
               $line_id_[]    = $re->product_id;
               $product_id[] = $re->product_id;
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
            $tr_recieved->save();

        if ($request->products) {
            
            $total     = 0 ;
            $type      = 'trans_delivery';
            $arr       = [];
            $tr_recieved                  =  TransactionDelivery::find($id);
            if ($request->sell_document) {
                foreach ($request->file('sell_document') as $key => $file) {
                    $mime =  $file->getClientOriginalExtension();
                    if (in_array($mime,['jpeg','png','jpg','pdf']))  {
                        $filename =  'uploads/documents/'.time().'_'.$key.'.'.$file->getClientOriginalExtension();
                        $file->move('uploads/documents',$filename);
                        array_push($arr,$filename);
                    }
                }
            $tr_recieved->is_returned     =  1;
            $tr_recieved->document        =  json_encode($arr);
            $tr_recieved->save();
            } 
            $x =  0;
            
            foreach ($request->products  as $key => $single) {
                if ($single['quantity'] > 0) {
                    $tr = \App\Transaction::where("id",$data->return_parent_id)->first();
                    $margin =  TransactionSellLine::check_transation_product_return($tr->id,$single['product_id']);
                    $diff   =  $margin -  $single['quantity'];
                    
                    $product  =  Product::find($single['product_id']);
                    $line     =  TransactionSellLine::where('transaction_id',$tr->id)
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
        \App\Models\ItemMove::return_sale_delivery($data);
        if( $check_for_wrong == 1 || (count($w_removes_return)>0)){
             \App\Models\ItemMove::Wrong_delivery_return($data,$td->id);
        }
        
        DB::commit();
        return redirect('sell-return')
                ->with('yes',trans('home.Done Successfully'));
    }



    public function recieve($data,$product,$quntity,$store_id,$tr_recieved,$line,$date=null)
    {
        $prev                  =  new DeliveredPrevious;
        $prev->product_id      =  $product->id;
        $prev->store_id        =  $store_id;
        $prev->business_id     =  $data->business_id ;
        $prev->transaction_id  =  $data->id;
        $prev->unit_id         =  $product->unit_id;
        $prev->total_qty       =  $line->quantity;
        $prev->current_qty     =  $quntity;
        $prev->remain_qty      =  0;
        $prev->transaction_recieveds_id   =  $tr_recieved->id;
        $prev->product_name   =  $product->name;
        $prev->line_id        =  $line->id;
         if($date != null){
            $prev->created_at  = $date;
        }
        $prev->save();
        WarehouseInfo::deliver_stoct($product->id,$store_id,$quntity,"return", $data->business_id);
        MovementWarehouse::movemnet_warehouse_sell($data,$product,$quntity,$store_id,$line,$prev->id,null,"return");
        // $this->productUtil->decreaseProductQuantity(
        //     $product->id,
        //     $line->variation_id,
        //     $data->location_id,
        //     $quntity
        // );
    }
    public function wrong_recive($data,$product,$quntity,$store_id,$tr_recieved,$date =null)
    {
            
            $type  = 'Wrong_receive';
            $ref_count = $this->productUtil->setAndGetReferenceCount($type);
            $reciept_no  = $this->productUtil->generateReferenceNumber($type, $ref_count);
            $prev =  new DeliveredWrong;
            $prev->product_id      =  $product->id;
            $prev->store_id        =  $store_id;
            $prev->business_id     =  $data->business_id ;
            $prev->transaction_id  =  $data->id;
            $prev->unit_id         =  $product->unit_id;
            $prev->total_qty       =  0;
            $prev->current_qty     =  $quntity;
            $prev->remain_qty      =  ($quntity*-1);
            $prev->transaction_recieveds_id   =  $tr_recieved->id;
            $prev->product_name    =  $product->name;
              if($date != null){
                $prev->created_at  = $date;
            }
            $prev->save();
            WarehouseInfo::deliver_stoct($product->id,$store_id,$quntity,"return",$data->business_id);
            $line =  DeliveredPrevious::OrderBy('id','desc')->where('product_id',$product->id)->first();
            MovementWarehouse::movemnet_warehouse_sell($data,$product,$quntity,$store_id,$line,NULL,$prev->id,"return");
            // $this->productUtil->decreaseProductQuantity(
            //     $product->id,
            //     $product->id,
            //     $data->location_id,
            //     $quntity
            // );
            
    }
    public function update_variation($id,$quntity,$location)
    {
         $data  =  \App\VariationLocationDetails::where('product_id',$id)
                              ->where('location_id',$location)->first();
         if ($data) {
            $data->qty_available =  $data->qty_available + $quntity;
            $data->save();
         }
    }

    public static function all_item($transaction,$arr)
    {
        \App\Services\Sells\Delivery::index($transaction,$arr);
    }

    
    
}
