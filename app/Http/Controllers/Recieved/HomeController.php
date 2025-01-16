<?php

namespace App\Http\Controllers\Recieved;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transaction;
use App\PurchaseLine;
use App\Models\TransactionRecieved;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Models\RecievedPrevious;
use App\Product;
use App\Variation;
use App\Models\RecievedWrong;
use App\Models\WarehouseInfo;
use App\MovementWarehouse;


class HomeController extends Controller
{

    
    protected $productUtil;

    /**
     * Constructor
    * * @param ProductUtils $product

     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(ProductUtil $productUtil ,TransactionUtil $transactionUtil)
    {        
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }
    
    public function index(Request $request,$id)
    {
      try{
         
         \DB::beginTransaction();
         // **1** Shipment Document
            $additional_inputs = $request->only([
                                    'contact_id'
                                    ,'shipping_amount'
                                    ,'shipping_vat'
                                    ,'shipping_total'
                                    ,'shipping_account_id'
                                    ,'shiping_text'
                                    ,'shiping_date'
                                    ,'shipping_contact_id'
                                    ,'shipping_cost_center_id'
                                    ,'cost_center_id'
                                 ]);
            $document_expense  = [];
            if ($request->hasFile('document_expense')) {
               foreach ($request->file('document_expense') as $file) {
                  $file_name =  'public/uploads/documents/'.time().'.'.$file->getClientOriginalExtension();
                  $file->move('public/uploads/documents',$file_name);
                  array_push($document_expense,$file_name);
               }
            }
         // **2** Additional Total
            $type           = 1;
            $sub_total      = $request->total_subtotal_input;
            $purchase_total = $request->final_total_hidden_items;
            $shipping_total = $request->total_final_items_;
            \App\Models\AdditionalShipping::add_purchase($request->transaction_id,$additional_inputs,$document_expense,$type,$sub_total, $purchase_total,$shipping_total);
         // **3** Status Live
            $data         = Transaction::find($id);  
            if($request->shipping_amount != null){
               $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$id)->where("type",1)->first();
               $ids       = $data_ship->items->pluck("id");
               foreach($ids as $i){
                  $data_shippment = \App\Models\AdditionalShippingItem::find($i);
                  \App\Models\StatusLive::insert_data_sh($data->business_id,$data,$data_shippment,"Add Expense",);
               }
            }
            $check_for_wrong = 0;
         // **4** Update Section 
            if ($request->purchases ) {
               $data                         =  Transaction::find($id);
               $total                        =  0 ;
               $type                         =  'purchase_receive';
               $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
               $reciept_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
               $tr_recieved                  =  new TransactionRecieved;
               $tr_recieved->store_id        =  $data->store;
               $tr_recieved->transaction_id  =  $id;
               $tr_recieved->business_id     =  $data->business_id ;
               $tr_recieved->reciept_no      =  $reciept_no ;
               $tr_recieved->ref_no          =  $data->ref_no;
               $tr_recieved->date            =  ($request->date == null)?\Carbon::now():$request->date;
               $tr_recieved->status          = 'purchase';
               $tr_recieved->save();
               
               foreach ($request->purchases as $key => $single) {
                  
                  // *** if not select store from line ($store_)
                     if($request->stores_id[$key] != null){
                        $store_   = $request->stores_id[$key];
                     }else{
                        $store_   = $request->store_id;
                     }
                  // *******
                  $product  =  Product::find($single['product_id']);
                  $margin   =  PurchaseLine::check_transation_product($id,$single['product_id']);
                  $line     =  PurchaseLine::where('transaction_id',$id)
                                             ->where('product_id',$single['product_id'])
                                             ->first();
                  $diff     =  $margin - $single['quantity'];
                  if ($diff > 0) {
                     $this->recieve($data,$product,$single['quantity'],$store_,$tr_recieved,$line);
                  }else if ($diff < 0 && $line) {
                     // correct  receive
                     ($margin > 0 )? $this->recieve($data,$product,$margin,$store_,$tr_recieved,$line):'';
                     // wrong  receive
                     $this->wrong_recive($data,$product,abs($diff),$store_,$tr_recieved,$line);
                     $check_for_wrong = 1;
                  }else if ($diff ==  0 && $line){
                     $this->recieve($data,$product,$single['quantity'],$store_,$tr_recieved,$line);
                  }else {
                     $this->wrong_recive($data,$product,$single['quantity'],$store_,$tr_recieved);
                     $check_for_wrong = 1;
                  }
               }
            
               $total_prev        = \App\Models\RecievedPrevious::where("transaction_id",$data->id)->get();
               $total_wrong       = \App\Models\RecievedWrong::where("transaction_id",$data->id)->get();
               $previous          = \App\Models\RecievedPrevious::orderBy("id","desc")->where("transaction_id",$data->id)->first();
               $wrong             = \App\Models\RecievedWrong::orderBy("id","desc")->where("transaction_id",$data->id)->first();
               $total_final       = 0;
               $total_final_wrong = 0;
               if(!empty($previous)){
                  foreach($total_prev as $item) {
                     $cost = \App\Product::product_cost($item->id) * $item->current_qty; 
                     $total_final += $cost;
                  }
                  \App\Models\StatusLive::insert_data_pr($data->business_id,$data,$previous,"Receive items",$total_final);
               
               } 
               if(!empty($wrong)){
                  foreach($total_wrong as $item) {
                     $cost = \App\Product::product_cost($item->id) * $item->current_qty; 
                     $total_final_wrong += $cost;
                  }
                  $wrong    = \App\Models\RecievedWrong::where("transaction_id",$data->id)->first();
                  if(!empty($wrong)){
                     \App\Models\StatusLive::insert_data_pr($data->business_id,$data,$wrong,"Wrong receive",$total_final_wrong);
                  }
               }
            }
         // **5** Map Section
            // Transaction::update_status($id);
            if($request->shipping_amount != null){
               $TransacRecieved   = \App\Models\TransactionRecieved::orderBy("id","desc")->where("transaction_id",$data->id)->first();
               $it                = \App\Models\AdditionalShipping::orderBy("id","desc")->where("transaction_id",$data->id)
                                                                     ->where("type",1)->whereNull("t_recieved")->first();
               $it->t_recieved    = $TransacRecieved->id;
               $it->update(); 
               \App\Models\AdditionalShipping::add_purchase_payment($request->transaction_id,$type,$TransacRecieved->id);
               $map               = \App\Models\StatusLive::where("transaction_id",$data->id)
                                                                  ->whereNotNull("shipping_item_id")->get();
               foreach($map as $item){
                     $item->t_received = $TransacRecieved->id;
                     $item->update();
               }
            }
         // **6** Shipment From Received
            $cost=0;$without_contact=0;
            $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$data->id)->where("type","!=",1)->first();
            if(!empty($data_ship)){
               $ids = $data_ship->items->pluck("id");
               foreach($ids as $i){
                  $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                  if($data_shippment->contact_id == $data->contact_id){ 
                     $cost += $data_shippment->amount;
                  }else{
                     $without_contact += $data_shippment->amount;
                  }
               }
            }
         // **7** Shipment From purchase
            $cost_recieve=0;$without_contact_recieve=0;
            $data_ship_recieve = \App\Models\AdditionalShipping::orderBy("id","desc")->where("transaction_id",$data->id)->where("t_recieved",$tr_recieved->id)->where("type",1)->first();
            if(!empty($data_ship_recieve)){
               $ids_recieve = $data_ship_recieve->items->pluck("id");
               foreach($ids_recieve as $i){
                  $data_shippment_recieve   = \App\Models\AdditionalShippingItem::find($i);
                  if($data_shippment_recieve->contact_id == $data->contact_id){ 
                     $cost_recieve += $data_shippment_recieve->amount;
                  }else{
                     $without_contact_recieve += $data_shippment_recieve->amount;
                  }
               }
            }
            $total_expense         = $cost + $without_contact;
            $total_expense_recieve = $cost_recieve + $without_contact_recieve;
            \App\Models\ItemMove::receive($data,$total_expense,$total_expense_recieve,$tr_recieved->id);
            if( $check_for_wrong == 1){
               \App\Models\ItemMove::Wrong_recieve($data->id,$tr_recieved->id);
            }
            \DB::commit();
            return redirect('purchases?id='.$id)
                  ->with('status',["success"=>1,"msg"=>__('home.Done Successfully')]);
         // **5**
      }catch(Exception $e){
         return redirect('purchases?id='.$id)
                  ->with('status',["success"=>0,"msg"=>$e->getMessage()]);
      }
    }
    public function update(Request $request,$id)
    {
      try{
         
            \DB::beginTransaction();
            // DD($request);
            $TranRed      = TransactionRecieved::find($id);  
            $data         = Transaction::find($TranRed->transaction_id);  
         
         // ** delete old main removed lines
            $exist_items  = $request->recieve_previous_id?$request->recieve_previous_id:[];
            $removes      = RecievedPrevious::where('transaction_id',$data->id)
                                                   ->where("transaction_deliveries_id",$id)
                                                   ->whereNotIn('id',$exist_items)
                                                   ->get();
            foreach ($removes as $re) {
               //.1.//
               $info =  WarehouseInfo::where('store_id',$re->store_id)
                                             ->where('product_id',$re->product_id)
                                             ->first();
               if ($info) {
                     $info->decrement('product_qty',$re->current_qty);
                     $info->save();
               }
               //.2.//
               MovementWarehouse::where('recived_previous_id',$re->id)->delete();
               //.3.//
               $q  =  $re->current_qty*-1;
               $this->update_variation($re->product_id,$q,$re->transaction->location_id);
               //.4.//
               \App\Models\ItemMove::delete_recieve($data->id,$re->id,$re);
               //.5.//
               $re->delete();
            }
         // ** end first section
         
         // ** delete old wrong removed lines
            $wrong_id     = $request->recieved_wrong_id?$request->recieved_wrong_id:[];
            $wrongs       = RecievedWrong::where('transaction_id',$data->id)->where("transaction_deliveries_id",$id)->whereNotIn('id',$wrong_id)->get();
            $check_for_wrong = 0;
            foreach ($wrongs as $re) {
                  //.1.//
                  $info =  WarehouseInfo::where('store_id',$re->store_id)
                           ->where('product_id',$re->product_id)->first();
                  if ($info) {
                        $info->decrement('product_qty',$re->current_qty);
                        $info->save();
                  }
                  //.2.//
                  MovementWarehouse::where('recieved_wrong_id',$re->id)->delete();
                  //.3.// 
                  $q  =  $re->current_qty*-1;
                  $this->update_variation($re->product_id,$q,$re->transaction->location_id);
                  //.4.// 
                  $array_del      = [];   $line_id        = [];
                  $product_id     = [];   $move_id        = []; 
                  if(!in_array($re->product_id,$line_id)){
                     $line_id[]    = $re->product_id;
                     $product_id[] = $re->product_id;
                  }
                  $wrongMove = \App\Models\ItemMove::where("transaction_id",$data->id)->where("recieve_id",$re->id)->first();
                  if(!empty($wrongMove)){
                     $move_id[] = $wrongMove->id; 
                  }
                  if(!empty($wrongMove)){
                     $date  = ($wrongMove->date != null) ? $wrongMove->date : $wrongMove->created_at  ;
                     $wrongMove->delete();
                     // *** refresh in new way #$%
                     \App\Models\ItemMove::updateRefresh($wrongMove,$wrongMove,$move_id,$date);
                  }
                  //.5.//
                  $re->delete();
            }
         // ** end second section

         if ($wrong_id != [] && count($wrong_id)>0){
            $check_for_wrong = 1;
         }

         if ($request->recieve_previous_id) {
               foreach ($request->recieve_previous_id  as $key => $pr_id) {
                     $prev              =  RecievedPrevious::find($pr_id);
                     $sum_product_id    =  PurchaseLine::where("transaction_id",$data->id)->where("product_id",$prev->product_id)->sum("quantity");

                     $line              =  PurchaseLine::where("transaction_id",$data->id)->where("product_id",$prev->product_id)->first();
                     $old_store         =  $prev->store_id;
                     $old_qty           =  $prev->current_qty*-1;
                     $diff              =  $request->recieve_previous_qty[$key] - $prev->current_qty;
                     $_store            =  $request->old_store_id[$key] ;
                     $prev->store_id    =  $request->old_store_id[$key];
                     $prev->total_qty   =  $sum_product_id;
                     $prev->current_qty =  $request->recieve_previous_qty[$key];
                     $prev->save();
                     if ($old_store ==  $request->old_store_id[$key]) {
                        WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$diff,$data->business_id);
                     }else{
                        WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$request->recieve_previous_qty[$key],$data->business_id);
                        WarehouseInfo::update_stoct($prev->product_id,$old_store,$old_qty,$data->business_id);
                     }
                  $this->update_variation($prev->product_id,$diff,$prev->transaction->location_id);
                  MovementWarehouse::recieve_return($pr_id,$request->recieve_previous_qty[$key],"correct",$_store);
                  // *** AGT8422 FOR UPDATE WAREHOUSE ROWS ********************************************************************
                  MovementWarehouse::update_receive($data,$prev,$request->recieve_previous_qty[$key],"correct",$_store,$line);   
                  // *** **************************** **** ********************************************************************
               }
         }
         
         if ($request->recieved_wrong_id){
            foreach ($request->recieved_wrong_id  as $key => $pr_id) {
               $prev              =  \App\Models\RecievedWrong::find($pr_id);
               $line              =  PurchaseLine::where("transaction_id",$data->id)->where("product_id",$prev->product_id)->first();
               MovementWarehouse::update_receive($data,$prev,$prev->current_qty,"wrong",$prev->store_id,$line);   
            }
         }

         // ** change the date recieved receipt
            $tr_recieved                  = TransactionRecieved::find($id);
            $tr_recieved->date            = ($request->date == null)?\Carbon::now():$request->date;
            $tr_recieved->update();
         // ** 

         if ($request->purchases ) {
            $data    =    Transaction::find($data->id);
            $total   = 0 ;
               
            foreach ($request->purchases as $key => $single) {
               // check transaction  
               $margin =  PurchaseLine::check_transation_product($data->id,$single['product_id']);
               $diff   =  $margin - $single['quantity'];
         
               $product  =  Product::find($single['product_id']);
               $line     =  PurchaseLine::where('transaction_id',$data->id)
                                    ->where('product_id',$single['product_id'])->first();
               if($request->stores_id[$key] != null){
                  $store_   = $request->stores_id[$key];
               }else{
                  $store_   = $request->store_id;
               }
               if ($diff > 0) {
                  $this->recieve($data,$product,$single['quantity'],$store_,$tr_recieved,$line);
               }elseif ($diff < 0 && $line) {
                  // correct  recieve
                  ($margin > 0 )? $this->recieve($data,$product,$margin,$store_,$tr_recieved,$line):'';
                  //wrong  recieve
                  $this->wrong_recive($data,$product,abs($diff),$store_,$tr_recieved);
                  $check_for_wrong = 1 ;
               }elseif($diff ==  0 && $line){
                  $this->recieve($data,$product,$single['quantity'],$store_,$tr_recieved,$line);
               }else{
                  $this->wrong_recive($data,$product,$single['quantity'],$store_,$tr_recieved);
                  $check_for_wrong = 1 ;
               }

            }

         }

         $additional_inputs = $request->only([
            'contact_id','shipping_amount','shipping_vat','shipping_total','shipping_account_id','shiping_text',
            'shiping_date','additional_shipping_item_id','old_shipping_amount','old_shipping_vat','old_shipping_total','old_shipping_account_id',
            'old_shiping_text','old_shiping_date','old_shipping_contact_id','shipping_contact_id','old_shipping_cost_center_id','cost_center_id'
         ]);

         $document_expense = $request->old_document??[];
         if ($request->hasFile('document_expense')) {
               foreach ($request->file('document_expense') as $file) {
                  $file_name =  'public/uploads/documents/'.time().'.'.$file->getclientoriginalextension();
                  $file->move('public/uploads/documents',$file_name);
                  array_push($document_expense,$file_name);
               }
         } 

         $total_prev        = \App\Models\RecievedPrevious::where("transaction_id",$data->id)->get();
         $total_wrong       = \App\Models\RecievedWrong::where("transaction_id",$data->id)->get();
         $previous          = \App\Models\RecievedPrevious::orderBy("id","desc")->where("transaction_id",$data->id)->first();
         $wrong             = \App\Models\RecievedWrong::orderBy("id","desc")->where("transaction_id",$data->id)->first();
         $line              = PurchaseLine::where('transaction_id',$data->id)->sum("quantity");
         $type              = "update";
         \App\Models\AdditionalShipping::update_purchase($data->id,$additional_inputs,$document_expense,$type,$id);
         \App\Models\AdditionalShipping::add_purchase_payment($data->id,$type,$id);
         \App\Models\StatusLive::update_data_pr($data->business_id,$data,$previous,"Receive items");
         // Transaction::update_status($data->id);
         $info = TransactionRecieved::where("transaction_id",$data->id)->get();
         if(!(count($info)>0)){
               $trans_change_status = Transaction::find($data->id);
               // $trans_change_status->update();
         }elseif($total_prev->sum("current_qty")<$line){
               $trans_change_status = Transaction::find($data->id);
               // $trans_change_status->update();
         }
         \App\Models\ItemMove::recieve_update($id,$data->id,$TranRed->id);
         if( $check_for_wrong == 1){
            \App\Models\ItemMove::Wrong_recieve($data->id,$tr_recieved->id);
         }
         \DB::commit();
           return redirect('purchases/')
                  ->with('status',["success"=>1,"msg"=>__('home.Done Successfully')]);
      }catch(Exception $e){
         return redirect('purchases/')
                     ->with('status',["success"=>0,"msg"=>__('Failed Actions')]);
      }
    }
    public function wrong_recive($data,$product,$quantity,$store_id,$tr_recieved)
    {
          
            $type                  =  'Wrong_receive';
            $prev                  =  new RecievedWrong;
            $prev->product_id      =  $product->id;
            $prev->store_id        =  $store_id;
            $prev->business_id     =  $data->business_id ;
            $prev->transaction_id  =  $data->id;
            $prev->unit_id         =  $product->unit_id;
            $prev->total_qty       =  0;
            $prev->current_qty     =  $quantity;
            $prev->remain_qty      =  ($quantity*-1);
            $prev->transaction_deliveries_id   =  $tr_recieved->id;
            $prev->product_name    =  $product->name;
            $prev->save();
            // must be the same arrangement
           
            WarehouseInfo::update_stoct($product->id,$store_id,$quantity,$data->business_id);
            $line =  PurchaseLine::OrderBy('id','desc')->where('product_id',$product->id)->first();
            MovementWarehouse::movemnet_warehouse($data,$product,$quantity,$store_id,$line,'plus',NULL,$prev->id,"received");
            //*** eb ..............................................................
            $variation_id = Variation::where("product_id" , $product->id)->first();
            //.....................................................................
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($data->business_id);
            $this->productUtil->updateProductQuantity($data->location_id, $product->id, $variation_id->id, $quantity, 0, $currency_details);
            
            

         }

    public function recieve($data,$product,$quntity,$store_id,$tr_recieved,$line)
    {
        $prev                  =  new RecievedPrevious;
        $prev->product_id      =  $product->id;
        $prev->store_id        =  $store_id;
        $prev->business_id     =  $data->business_id ;
        $prev->transaction_id  =  $data->id;
        $prev->unit_id         =  $product->unit_id;
        $prev->total_qty       =  $line->quantity;
        $prev->current_qty     =  $quntity;
        $prev->remain_qty      =  0;
        $prev->transaction_deliveries_id   =  $tr_recieved->id;
        $prev->product_name    =  $product->name;  
        $prev->line_id         =  $line->id;  
        $prev->save();
        // must be the same arrangement
        WarehouseInfo::update_stoct($product->id,$store_id,$quntity,$data->business_id);
        MovementWarehouse::movemnet_warehouse($data,$product,$quntity,$store_id,$line,'plus',$prev->id,null,"received");
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($data->business_id);
        $this->productUtil->updateProductQuantity($data->location_id, $product->id, $line->variation_id, $quntity, 0, $currency_details);

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
    
    
}
