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
use Illuminate\Support\Facades\DB;
use App\Models\RecievedWrong;
use App\Models\WarehouseInfo;
use App\MovementWarehouse;


class HomeReturnController extends Controller
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
         $type           = 1;
         $sub_total      = $request->total_subtotal_input;
         $purchase_total = $request->final_total_hidden_items;
         $shipping_total = $request->total_final_items_;
         $data           = Transaction::find($id);  
        DB::beginTransaction();
        //.. delete  wrong not existed
      //   $exist_items  = $request->recieve_previous_id?$request->recieve_previous_id:[];
      //   $removes      = RecievedPrevious::where('transaction_id',$id)->whereNotIn('id',$exist_items)->get();
      //   foreach ($removes as $re) {
      //        $info =  WarehouseInfo::where('store_id',$re->store_id)
      //                      ->where('product_id',$re->product_id)->first();
      //       if ($info) {
      //           $info->increment('product_qty',$re->current_qty);
      //           $info->save();
      //       }
      //       MovementWarehouse::where('recived_previous_id',$re->id)->delete();
      //       $q  =  $re->current_qty;
      //       $this->update_variation($re->product_id,$q,$re->transaction->location_id);
      //       $re->delete();
      //   }

        //.. delete  wrong not existed
      //   $wrong_id     = $request->recieved_wrong_id?$request->recieved_wrong_id:[];
      //   $wrongs       =  RecievedWrong::where('transaction_id',$id)->whereNotIn('id',$wrong_id)->get();
      //   foreach ($wrongs as $re) {
      //          $info =  WarehouseInfo::where('store_id',$re->store_id)
      //                   ->where('product_id',$re->product_id)->first();
      //          if ($info) {
      //                $info->increment('product_qty',$re->current_qty);
      //                $info->save();
      //          }
      //          MovementWarehouse::where('recieved_wrong_id',$re->id)->delete();
      //          $q  =  $re->current_qty;
      //          $this->update_variation($re->product_id,$q,$re->transaction->location_id);
      //          $re->delete();
      //   }
        
        $check_for_wrong = 0;         //.. for item movment
        if ($request->purchases ) {
            $data        =    Transaction::find($id);
            $total       = 0 ;
            $type        = 'purchase_receive';
            $ref_count   = $this->productUtil->setAndGetReferenceCount($type);
            $reciept_no  = $this->productUtil->generateReferenceNumber($type, $ref_count);

            $tr_recieved                  =  new TransactionRecieved;
            $tr_recieved->store_id        =  $data->store;
            $tr_recieved->transaction_id  =  $id;
            $tr_recieved->business_id     =  $data->business_id ;
            $tr_recieved->is_returned     =  1 ;
            $tr_recieved->reciept_no      =  $reciept_no ;
            $tr_recieved->ref_no          =  $data->ref_no;
            $tr_recieved->date            =  $request->date;
            $tr_recieved->status          = 'Return Purchase';
            $tr_recieved->save();
            $counter = 0;
            foreach ($request->purchases as $key => $single) {
               $tr       = \App\Transaction::where("id",$data->return_parent_id)->first();
               $margin   =  PurchaseLine::check_transation_product_return($tr->id,$single['product_id'],$counter);
               $diff     =  $margin - $single['quantity'];
               $product  =  Product::find($single['product_id']);
               $line     =  PurchaseLine::where('transaction_id',$tr->id)
                                                   ->where('product_id',$single['product_id'])->first();
                
               $counter++;
               if($request->stores_id[$key] != null){
                  $store_   = $request->stores_id[$key];
               }else{
                  $store_   = $request->store_id;
               }
               if ($diff > 0) {
                  $this->recieve($data,$product,$single['quantity'],$store_,$tr_recieved,$line);
               }else if ($diff < 0 && $line) {
                  // correct  recieve
                  ($margin > 0 )? $this->recieve($data,$product,$margin,$store_,$tr_recieved,$line):'';
                  // wrong  recieve
                  $this->wrong_recive($data,$product,abs($diff),$store_,$tr_recieved,$line);
                  $check_for_wrong = 1;
               }else if ($diff ==  0 && $line){
                  $this->recieve($data,$product,$single['quantity'],$store_,$tr_recieved,$line);
               }else {
                  $this->wrong_recive($data,$product,$single['quantity'],$store_,$tr_recieved);
                  $check_for_wrong = 1;
               }
            }
        }
       

      // Transaction::update_status($id);
      \App\Models\ItemMove::return_recieve($data,$tr_recieved->id);
      if( $check_for_wrong == 1){
         \App\Models\ItemMove::Wrong_recieve_return($data,$tr_recieved->id);
      }
      DB::commit();
      return redirect('purchase-return')
                ->with('yes',trans('home.Done Successfully'));
    }

    public function update(Request $request,$id)
    {
      DB::beginTransaction();
      $TranRed           = TransactionRecieved::find($id);  
      $data              = Transaction::find($TranRed->transaction_id);  
      $exist_items       = $request->recieve_previous_id?$request->recieve_previous_id:[];
      $removes           = RecievedPrevious::where('transaction_id',$data->id)
                                             ->where("transaction_deliveries_id",$id)
                                             ->whereNotIn('id',$exist_items)
                                             ->get();
      $TranRed->date     = $request->date;
      $TranRed->update();  
      foreach ($removes as $re) {
          $info =  WarehouseInfo::where('store_id',$re->store_id)
                                       ->where('product_id',$re->product_id)
                                       ->first();
         if ($info) {
               $info->decrement('product_qty',$re->current_qty);
               $info->save();
         }
         MovementWarehouse::where('recived_previous_id',$re->id)->delete();
         $q  =  $re->current_qty*-1;
         $this->update_variation($re->product_id,$q,$re->transaction->location_id);
         \App\Models\ItemMove::delete_recieve($data->id,$re->id,$re);
         $re->delete();
      }
      $wrong_id     = $request->recieved_wrong_id?$request->recieved_wrong_id:[];
      $wrongs       =  RecievedWrong::where('transaction_id',$data->id)->where("transaction_deliveries_id",$id)->whereNotIn('id',$wrong_id)->get();
      $wrongs_return =  RecievedWrong::where('transaction_id',$data->id)->where("transaction_deliveries_id",$id)->whereIn('id',$wrong_id)->get();
      $check_for_wrong = 0;
      foreach ($wrongs as $re) {
            $info =  WarehouseInfo::where('store_id',$re->store_id)
                     ->where('product_id',$re->product_id)->first();
            if ($info) {
                  $info->decrement('product_qty',$re->current_qty);
                  $info->save();
            }
            MovementWarehouse::where('recieved_wrong_id',$re->id)->delete();
            $q  =  $re->current_qty*-1;
            $this->update_variation($re->product_id,$q,$re->transaction->location_id);
            $array_del      = [];
            $line_id        = [];
            $product_id     = [];
            $move_id        = []; 
            if(!in_array($re->product_id,$line_id)){
               $line_id[]    = $re->product_id;
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
        
      if ($request->recieve_previous_id) {
            foreach ($request->recieve_previous_id  as $key => $pr_id) {
            $tr = \App\Transaction::where("id",$data->return_parent_id)->first();
            $prev              =  RecievedPrevious::find($pr_id);
            $sum_product_id    =  PurchaseLine::where("transaction_id",$tr->id)->where("product_id",$prev->product_id)->sum("quantity_returned");

            $line              =  PurchaseLine::where("transaction_id",$tr->id)->where("product_id",$prev->product_id)->first();
            $old_store         =  $prev->store_id;
            $old_qty           =  $prev->current_qty;
            $diff              =  $request->recieve_previous_qty[$key] - $prev->current_qty;
            $_store            =  $request->old_store_id[$key] ;
            $prev->store_id    =  $request->old_store_id[$key];
            $prev->total_qty   =  $sum_product_id;
            $prev->current_qty =  $request->recieve_previous_qty[$key];
            $prev->save();
            if ($old_store ==  $request->old_store_id[$key]) {
               WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$diff*-1,$data->business_id);
            }else{
               WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$request->recieve_previous_qty[$key]*-1,$data->business_id);
               WarehouseInfo::update_stoct($prev->product_id,$old_store,$old_qty,$data->business_id);
               }
               $this->update_variation($prev->product_id,$diff,$prev->transaction->location_id);
               MovementWarehouse::recieve_return($pr_id,$request->recieve_previous_qty[$key],"correct",$_store,"return");

            }
      }
        
      if ($request->purchases ) {
         $data =    Transaction::find($data->id);
         $total   = 0 ;
         $tr_recieved                  = TransactionRecieved::find($id);
         $tr_recieved->date            = ($request->date == null)?\Carbon::now():$request->date;
         $tr_recieved->update();
         foreach ($request->purchases as $key => $single) {
            // $single['product_id'];
            // check transcation  
            $tr = \App\Transaction::where("id",$data->return_parent_id)->first();
            $margin =  PurchaseLine::check_transation_product_return($tr->id,$single['product_id']);
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
     
      // Transaction::update_status($data->id);
      \App\Models\ItemMove::return_recieve_update($id,$data,$TranRed);
      if( $check_for_wrong == 1 || (count($wrongs_return)>0)){
         \App\Models\ItemMove::Wrong_recieve_return($data,$TranRed->id);
      }
      DB::commit();
      return redirect('purchase-return/')
               ->with('yes',trans('home.Done Successfully'));
    }

    public function wrong_recive($data,$product,$quntity,$store_id,$tr_recieved)
    {
            $type  = 'Wrong_receive';
            $ref_count = $this->productUtil->setAndGetReferenceCount($type);
            $reciept_no  = $this->productUtil->generateReferenceNumber($type, $ref_count);
            $prev =  new RecievedWrong;
            $prev->product_id      =  $product->id;
            $prev->store_id        =  $store_id;
            $prev->business_id     =  $data->business_id ;
            $prev->transaction_id  =  $data->id;
            $prev->unit_id         =  $product->unit_id;
            $prev->total_qty       =  0;
            $prev->current_qty     =  $quntity;
            $prev->remain_qty      =  ($quntity*-1);
            $prev->transaction_deliveries_id   =  $tr_recieved->id;
            $prev->product_name    =  $product->name;
            $prev->save();
            // must be the same arrangement
            WarehouseInfo::update_stoct($product->id,$store_id,$quntity*-1,$data->business_id);
            $line =  PurchaseLine::OrderBy('id','desc')->where('product_id',$product->id)->first();
            MovementWarehouse::movemnet_warehouse($data,$product,$quntity,$store_id,$line,'minus',NULL,$prev->id,"received");
            //*** eb ..............................................................
            $variation_id = Variation::where("product_id" , $product->id)->first();
            //.....................................................................
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($data->business_id);
    } 

    public function recieve($data,$product,$quntity,$store_id,$tr_recieved,$line)
    {
        $prev                  =  new RecievedPrevious;
        $prev->product_id      =  $product->id;
        $prev->store_id        =  $store_id;
        $prev->business_id     =  $data->business_id ;
        $prev->transaction_id  =  $data->id;
        $prev->unit_id         =  $product->unit_id;
        $prev->total_qty       =  $line->quantity_returned;
        $prev->current_qty     =  $quntity;
        $prev->remain_qty      =  0;
        $prev->transaction_deliveries_id   =  $tr_recieved->id;
        $prev->product_name    =  $product->name;  
        $prev->line_id         =  $line->id;  
        $prev->is_returned     =  1;  
        $prev->save();
        // must be the same arrangement
        WarehouseInfo::update_stoct($product->id,$store_id,$quntity*-1,$data->business_id);
        MovementWarehouse::movemnet_warehouse($data,$product,$quntity,$store_id,$line,'minus',$prev->id,"received");
        
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($data->business_id);
 
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
