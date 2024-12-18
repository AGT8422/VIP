<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\ProductUtil; 
use App\Utils\ModuleUtil; 
use App\Utils\TransactionUtil; 
use App\Utils\BusinessUtil; 
use Yajra\DataTables\Facades\DataTables;

class ItemMoveController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $businessUtil;


    /**
     * Constructor
     * 
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil , TransactionUtil $transactionUtil)
    {
        $this->productUtil     = $productUtil;
        $this->moduleUtil      = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil    = $businessUtil;
      
      }

    //  .. list 
    public function index($id)
    {   
       
        if(!auth()->user()->can("ReadOnly.views")  && !auth()->user()->can("warehouse.views") && !auth()->user()->can("admin_supervisor.views") && !auth()->user()->can("SalesMan.views") && !auth()->user()->can("manufuctoring.views") && !auth()->user()->can("admin_supervisor.views")){
            abort(403,"Unauthorized actions.");
        }
        $business_id   =  request()->session()->get("user.business_id");
        $product       =  \App\Product::find($id);
        $variation_id  =  request()->input("variation_id");
       
        // \App\Models\ItemMove::delete_all_movement_not_connect($business_id);
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $movment_cost     = \App\Product::product_cost($id,$variation_id);

        $move_id  = [];
        $itemMoves = \App\Models\ItemMove::orderByRaw('ISNULL(date), date asc, created_at asc')
                                            ->orderBy("order_id","asc")
                                            ->orderBy("id","asc")
                                            ->where("product_id",$product->id);
        if($variation_id != null){
            $itemMoves->where("variation_id",$variation_id);
        }
        $itemMoves = $itemMoves->first();
         
        if(!empty($itemMoves)){
            $move_id [] = $itemMoves->id;
            $date       = $itemMoves->date;
            if($variation_id != null){ 
                \App\Models\ItemMove::updateRefresh($itemMoves,$itemMoves,$move_id,$date,$variation_id);
            }else{
                \App\Models\ItemMove::updateRefresh($itemMoves,$itemMoves,$move_id,$date);
            }
        }

        if(request()->ajax()){
            // $movment = \App\Models\ItemMove::orderBy("id",'DESC')->where("product_id",$product->id)->where("business_id",$business_id)->get();
            $variation_id  =  request()->input("variation_id");
            $movement = \App\Models\ItemMove::orderByRaw('ISNULL(date), date desc, created_at desc')
                                            ->orderBy("id","desc")
                                            ->orderBy("order_id","desc")
                                            ->where("product_id",$product->id)
                                            ->where("business_id",$business_id);
            if($variation_id != null){ 
                $movement->where("variation_id",$variation_id);
            }
            $start_date       = request()->input('start_date');
            $end_date         = request()->input('end_date');

            if(request()->input("date") != null){
                $date = request()->input("date");
                $movement->whereDate("date","<=",$date);
            }
            $quantity = 1;
            // #2024-8-6
            if(request()->input("unit_id") != null){
                $unit_id      = request()->input("unit_id");
                $unit_details = \App\Unit::find($unit_id);
                $quantity     = ($unit_details)?(($unit_details->base_unit_multiplier != null)?$unit_details->base_unit_multiplier:1):1;
            }
            
           
            $movement->whereBetween(\DB::raw('date(date)'), [$start_date, $end_date]);
            $before = \App\Models\ItemMove::orderByRaw('ISNULL(date), date desc, created_at desc')
                                            ->orderBy("id","desc")
                                            ->orderBy("order_id","desc")
                                            ->where("product_id",$product->id)
                                            ->where("business_id",$business_id)
                                            ->where("date","<",$start_date);
            if($variation_id != null){ 
                $before->where("variation_id",$variation_id);
            }
            $before = $before->first(); 
            return DataTables::of($movement)->addColumn("product",function($row) use($product,$variation_id){
                $product_name = $row->product->name;
                if($variation_id != null){
                    $variations = \App\Variation::find($variation_id);
                    $product_name = ($variations)?($row->product->name . ' | ' . $variations->name): $row->product->name; 
                }
                $html = '<button type="button"  data-href="' . action('ProductController@view', [$product->id]) . '" class="btn btn-link space btn-modal" data-container=".view_modal"  >' . $product_name . '</button>';
                return $html;
            })->addColumn("account",function($row){
                $array = ["opening_stock","production_sell","production_purchase"];
                 if($row->account){
                    if(!in_array($row->transaction->type,$array)) {
                        $html = '<a href="' . \URL::to('account/account/'.$row->account->id)   . '"    >' . $row->account->name . '</a>';
                        return $html;
                    }else{
                        return "---";
                    }
                }else{
                    return "---";
                }
            })->addColumn("state",function($row){
                return $row->state;
            })->addColumn("references",function($row){
                    if(isset($row->transaction)){
                        $m_id = $row->transaction->id;
                        $tr   = \App\Transaction::where("return_parent_id",$m_id)->orWhere("id",$m_id)->first();
                        // if(!isset($row->transaction->type)){
                        //     dd($row);
                        // }
                        if($tr){
                            $type = $tr->type; 
                        }else{
                            $type = $row->transaction->type;
                        }
                        if($type  == "purchase"){
                         // if(!empty($tr_return)){
                        //     $html = '<button type="button" data-href="' . action('PurchaseReturnController@show', [$tr_return->id])
                        //     . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                        // }else{
                            $html = '<button type="button" data-href="' . action('PurchaseController@show', [$row->transaction->id])
                            . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                        // }
                        }else if($type  == "Stock_In"  ){
                            $html = '<button type="button" data-href="' . action('StockTransferController@show', [$row->transaction->id])
                            . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                        }else if($type  == "sale"  ){
                            $html = '<button type="button" data-href="' . action('SellController@show', [$row->transaction->id])
                            . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                        }else if($type  == "Stock_Out"  ){
                            $transaction = \App\Transaction::where("type","Stock_In")->where("ref_no",$row->transaction->ref_no)->first();
                            $html = '<button type="button" data-href="' . action('StockTransferController@show', [$transaction->id])
                            . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                        }else if($type  == "opening_stock"  ){
                            $open = \App\Models\OpeningQuantity::where("transaction_id",$row->transaction->id)->first();
                            $html = '<button type="button" data-href="' . action('ProductController@ViewOpeningProduct', [$open->id])
                            . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                        }else if(  $row->state == "Manufacturing - ( In )" ){
                             $html = '<button type="button" data-href="' . action('\Modules\Manufacturing\Http\Controllers\ProductionController@show', [$row->transaction->id])
                            . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->transaction->ref_no . '</button>';
                        }else if(  $row->state == "purchase_return" || $row->state == "Wrong - purchase_return<br>More Delivery"|| $row->state == "Wrong - purchase_return<br>Other Product"){
                            $html = '<button type="button" data-href="' . action('PurchaseReturnController@show', [$tr->id])
                                  . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                        }else if(  $row->state == "sell_return" || $row->state == "Wrong - sell_return<br>More Delivery"|| $row->state == "Wrong - sell_return<br>Other Product"){
                            $html = '<button type="button" data-href="' . action('SellReturnController@show', [$tr->id])
                                  . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                        }else if($row->state == "Manufacturing - ( Out )"   ){
                            $trans  = \App\Transaction::where("id",$row->transaction->mfg_parent_production_purchase_id)->first();
                            $html = '<button type="button" data-href="' . action('\Modules\Manufacturing\Http\Controllers\ProductionController@show', [$trans->id])
                           . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $trans->ref_no . '</button>';
                        }else{
                            $html = "---";
                        }
                    }else{
                        $html = "---";
                    }
                return $html;
            })->addColumn("qty_plus",function($row) use($quantity){
                $with_unit = ($quantity!=0)?($row->qty/$quantity):$row->qty;
                $final_qty = ($row->signal=="+")?$with_unit:0;
                return number_format($final_qty,2);
            })->addColumn("qty_minus",function($row) use($quantity){
                $with_unit = ($quantity!=0)?($row->qty/$quantity):$row->qty;
                $final_qty = ($row->signal=="-")?$with_unit:0;
                return number_format($final_qty,2);
            })->addColumn("row_price",function($row) use($quantity){
                $with_unit = ($quantity!=0)?($row->row_price*$quantity):$row->row_price;
                if($row->state == "Manufacturing - ( Out )" || $row->state == "sale" ){
                    $pr = number_format($with_unit,2);
                }else{
                    $pr = number_format($with_unit,2);
                }
                return $pr;
            })->addColumn("row_price_inc_exp",function($row) use($quantity){
                $with_unit = ($quantity!=0)?($row->row_price_inc_exp*$quantity):$row->row_price_inc_exp;
                if($row->state == "Manufacturing - ( Out )" || $row->state == "sale" ){
                   $pr =  number_format($with_unit,2);
                }else{
                   $pr =  number_format($with_unit,2);
                }
                return $pr;
            })->addColumn("unit_cost",function($row) use($quantity){
                $with_unit = ($quantity!=0)?($row->unit_cost*$quantity):$row->unit_cost;
                return number_format($with_unit,2);
            })->addColumn("current_qty",function($row) use($quantity){
                $with_unit = ($quantity!=0)?($row->current_qty/$quantity):$row->current_qty;
                return number_format($with_unit,2);
            })->addColumn("store_id",function($row){
                if(isset($row->store) && $row->store != null){
                    $store = $row->store->name;
                    if( $row->state  == "sale" || $row->state == "sell_return" || $row->state == "Wrong - sale<br>More Delivery" || $row->state == "Wrong - sale<br>Other Product"  ){
                        
                        if(isset($row->transaction_d) && $row->transaction_d != null){
                            
                            $html = '<button type="button" data-href="' . action('TransactionPaymentController@viewDelivered', [$row->transaction_d->id])
                                    . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->transaction_d->reciept_no . '</button>';
                            $receipt = $html;
                        }else{
                            if($row->state == "sell_return"){
                                $tr      = \App\models\TransactionDelivery::where("transaction_id",$row->transaction->return_parent_id)->first();
                                $html    = '<button type="button" data-href="' . action('TransactionPaymentController@viewDelivered', [$tr->id])
                                            . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $tr->invoice_no . '</button>';
                                $receipt = $html;
                            }else{
                                $receipt = "No Delivery Receipt";
                            }
                        }
                    }elseif($row->state  == "purchase" || $row->state == "purchase_return"  || $row->state == "Wrong - purchase<br>More Delivery" || $row->state == "Wrong - purchase<br>Other Product"|| $row->state == "Wrong - purchase_return<br>More Delivery" || $row->state == "Wrong - purchase_return<br>Other Product" ){
                        if(isset($row->transaction_r) && $row->transaction_r != null){
                            $html = '<button type="button" data-href="' . action('TransactionPaymentController@viewRecieve', [$row->transaction_r->id])
                                    . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->transaction_r->reciept_no . '</button>';
                            $receipt = $html; 
                        }else{
                            if( $row->state == "Wrong - purchase_return<br>More Delivery" || $row->state == "Wrong - purchase_return<br>Other Product"){
                                $tr_received = \App\models\TransactionRecieved::where("transaction_id",$row->transaction->return_parent_id)->first();
                                  
                                $html = '<button type="button" data-href="' . action('TransactionPaymentController@viewRecieve', [$tr_received->id])
                                    . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $tr_received->reciept_no . '</button>';
                                $receipt = $html;
                            }else{
                                $receipt = "No Received Receipt";
                            }
                        }
                    }else{
                        $receipt = "";
                    }
                }else{
                   
                    $product_id     = $row->product_id;
                    $transaction_id = $row->transaction_id;
                    $warehouse      = \App\MovementWarehouse::where("transaction_id",$transaction_id)->where("product_id",$product_id)->first();
                    if($warehouse){
                         
                        $store          = $warehouse->store->name;
                        if(isset($warehouse->receivedPrevious)){
                            $html = '<button type="button" data-href="' . action('TransactionPaymentController@viewRecieve', [$warehouse->receivedPrevious->TrRecieved->id])
                                    . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $warehouse->receivedPrevious->TrRecieved->reciept_no . '</button>';
                            $receipt        = $html;
                            
                        }elseif(isset($warehouse->receivedWrong)){
                            $html = '<button type="button" data-href="' . action('TransactionPaymentController@viewRecieve', [$warehouse->receivedWrong->TrRecieved->id])
                                    . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $warehouse->receivedWrong->TrRecieved->reciept_no . '</button>';
                            $receipt        = $html;
                            
                        }elseif(isset($warehouse->deliveredPrevious)){
                            $html = '<button type="button" data-href="' . action('TransactionPaymentController@viewDelivered', [$warehouse->deliveredPrevious->T_delivered->id])
                            . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $warehouse->deliveredPrevious->T_delivered->reciept_no . '</button>';
                            $receipt        = $html;
                            
                        }elseif(isset($warehouse->deliveredWrong)){
                            $html = '<button type="button" data-href="' . action('TransactionPaymentController@viewDelivered', [$warehouse->deliveredWrong->T_delivered->id])
                                    . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $warehouse->deliveredWrong->T_delivered->reciept_no . '</button>';
                            $receipt        = $html;
                            
                        }else{
                            $receipt        = "";
                        }
                    }else{
                        $store          = "No Store";
                        $receipt        = "";
                    }
                }
                return $store ."\n". $receipt ;
            })->addColumn("created_at",function($row){
                if($row->date != null){
                    $date = date_format(\Carbon\Carbon::parse($row->date),"Y-m-d");
                }else{
                    $date = date_format($row->created_at,"Y-m-d");
                }
                return $date;
            })->addColumn("before",function($row) use($before){
                $bf = (!empty($before))?$before:null;
                if($bf){
                    $list["cost"] =  $bf->unit_cost  ;
                    $list["qty"]  =  $bf->current_qty  ;
                }else{
                   $list["cost"] =  0  ;
                   $list["qty"]  =  0  ;
                }
                return $list ;
            })->addColumn("created_at",function($row){
                if($row->date != null){
                    $date = date_format(\Carbon\Carbon::parse($row->date),"Y-m-d");
                }else{
                    $date = date_format($row->created_at,"Y-m-d");
                }
                return $date;
            })->rawColumns(["product",
                          "store_id",
                          "transaction_rd_id",
                          "account",
                          "state",
                          "references",
                          "qty_plus",
                          "before",
                          "qty_minus",
                          "row_price_inc_exp",
                          "row_price",
                          "unit_cost",
                          "current_qty",
                          "created_at"
            ])->make(true);

        }
      

        $checkItems    = \App\Transaction::where("business_id",$business_id)->get();
       
        $check = [];

        foreach($checkItems as $it){
            //...... transaction id info
            $id               = $it->id;

            //....... id's for purchase line  
            //....... id's for previous line  
            $purchaseline     = $it->purchase_lines->pluck("id");

            //........ get product's from purchase line
            //........ get product's from previous line
            $line_product     = \App\PurchaseLine::whereIn("id",$purchaseline)->groupby("product_id")->select("product_id")->get();  


            //........ Total Qty for previous line  
            //........ Total Qty for purchase line 
            foreach($line_product as $key => $value){
              
                $Items    = \App\Models\RecievedPrevious::whereHas("transaction",function($query) use($id){
                                                                $query->where("id",$id);
                                                           })->where("business_id",$business_id)
                                                            ->where("product_id",$value->product_id)
                                                            ->sum("current_qty");

                $checkPurchase = \App\PurchaseLine::whereHas("transaction",function($query) use($id ,$business_id){
                                                            $query->where("business_id",$business_id);
                                                            $query->where("id",$id);
                                                        })->where("product_id",$value->product_id)
                                                        ->sum("quantity");
                $margin = $Items - $checkPurchase ;
                if($Items > $checkPurchase){
                    $margin = $margin*-1;
                    $check[$it->id] = $margin; 
                }elseif($Items < $checkPurchase){
                    $margin = $margin*-1;
                    $check[$it->id] = $margin;
                }


            }
        }

        // #2024-8-6
        $var            = $product->variations->first();
        $var            = ($var)?$var->default_purchase_price:0;
        $allUnits       = []; 
        $business       = \App\Business::find($product->business_id);
        $allUnits[$product->unit_id] = $product->unit->actual_name; 
        if($product->sub_unit_ids != null){
            foreach($product->sub_unit_ids  as $i){
                $row_price    =  0;
                $un           = \App\Unit::find($i);
                $row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$product->id)->where("number_of_default",0)->first();
                $allUnits[$i] = $un->actual_name ;
            }
        } 
        $units   = $allUnits  ;
                                                    
        return view("item_move.index")->with(compact("product","movment_cost",'units','check',"currency_details","variation_id"));
    }

    //  .. create page
    public function create()
    {
         return view("itemMove.create");
    }
    //  .. create page
    public function showMovement()
    {
        $business_id  = request()->session()->get("user.business_id");
        $products     = \App\Product::where("business_id",$business_id)->get();
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $products_list = [];
        foreach($products as $prd){
            $products_list[$prd->id] = $prd->name . " || " . $prd->sku;
        }

        return view("item_move.showMovement")->with(compact("products_list","currency_details"));
    }
    //  .. edit page
    public function edit($id)
    {
         return view("itemMove.edit");
    }
    //  .. store itemMove
    public function store(Request $request)
    {
        if(auth()->user()->can("purchase.create")){
            abort(403,"Unautherized action. ");
        }
        try{

            $output = [
                'sucess'=> 1,
                'msg'=>"sucessfully"
            ];
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            $output = [
                'sucess'=> 0,
                'msg'=>"faild"
            ];
        }
        return $output;
    }
    //  .. update itemMove
    public function update($id)
    {
        try{
            $output = [
                'sucess'=> 1,
                'msg'=>"sucessfully"
            ];
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            $output = [
                'sucess'=> 0,
                'msg'=>"faild"
            ];
        }
        return $output;
    }
    //  .. delete itemMove
    public function destroy()
    {
         return redirect()->back();
    }
    public function getMove()
    {
        if(request()->ajax()){
            $business_id  = request()->session()->get("user.business_id");
            $product      = \App\Product::where("business_id",$business_id)->get();
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
             
            $business_id   =  request()->session()->get("user.business_id");
            $product       =  \App\Product::find(request()->input("id_product"));
             
            // \App\Models\ItemMove::delete_all_movement_not_connect($business_id);
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $movment_cost     = \App\Product::product_cost(request()->input("id_product"));
     
    
            return view("item_move.mv_show")->with(compact('product','currency_details','movment_cost'));
        }
       
    }
        // .. previous amount 
    function getPrevious(Request $request){
        if(request()->ajax()){
            
            $br          = [];
            $br["cost"]  = 0;
            $br["qty"]   = 0;
            $br["signal"]   = "+";
            $business_id = session()->get("user.business_id"); 
            $start_date  = request()->input("start_date");
            $product_id  = request()->input("product_id");
         
            $before      = \App\Models\ItemMove::orderByRaw('ISNULL(date), date desc, created_at desc')->orderBy("id","desc")->orderBy("order_id","desc")->where("product_id",$product_id)->where("business_id",$business_id)->where("date","<",$start_date            )->first();
            if(!empty($before)){
                $br["cost"] = $before->unit_cost;
                $br["qty"]  = $before->current_qty;
                $br["signal"]  = $before->signal;
            }
            return $br;
        }
    }
}
