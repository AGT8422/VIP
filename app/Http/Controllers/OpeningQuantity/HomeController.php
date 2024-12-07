<?php

namespace App\Http\Controllers\OpeningQuantity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OpeningQuantity;
use  App\Models\WarehouseInfo;
use App\Transaction;
use App\BusinessLocation;
use App\Unit;
use App\CustomerGroup;
use App\Models\Warehouse;
use App\Variation;
use App\ProductLocation;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;

use App\MovementWarehouse;
use App\PurchaseLine;
use DB;
class HomeController extends Controller
{
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil     = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil    = $businessUtil;
        $this->moduleUtil      = $moduleUtil;

        $this->dummyPaymentLine = [ 'method'                  => 'cash'
                                  , 'amount'                  => 0
                                  , 'note'                    => ''
                                  , 'card_transaction_number' => ''
                                  , 'card_number'             => ''
                                  , 'card_type'               => ''
                                  , 'card_holder_name'        => ''
                                  , 'card_month'              => ''
                                  , 'card_year'               => ''
                                  , 'card_security'           => ''
                                  , 'cheque_number'           => ''
                                  , 'bank_account_number'     => ''
                                  , 'is_return'               => 0
                                  , 'transaction_no'          => ''
                                ];
    }
    public function add(Request $request)
    {
        
        if ($request->purchases) {
            DB::beginTransaction();
            $business_id         = request()->session()->get('user.business_id');
            $user_id             = request()->session()->get('user.id');
            $ref_count           = $this->productUtil->setAndGetReferenceCount('Open Quantity');
            $ref_no              = $this->productUtil->generateReferenceNumber('Open Quantity', $ref_count);
            $location            = \App\BusinessLocation::where('business_id',$business_id)->first();
            $tr                  = Transaction::create([
                                        'ref_no'            => $ref_no,
                                        'type'              => 'opening_stock',
                                        'status'            => 'received',
                                        'list_price'        => $request->list_price,
                                        'business_id'       => $business_id,
                                        'created_by'        => $user_id,
                                        'store'             => $request->store_id,
                                        'location_id'       => $location->id,  
                                        'transaction_date'  => ($request->date)?\Carbon\Carbon::parse($request->date):date('Y-m-d h:i:s',time())
                                    ]);
                                    
            foreach ($request->purchases as $key=> $single) {
                if($request->stores_id[$key] != null){
                    // line store
                    $store = $request->stores_id[$key];
                }else{
                    // main store
                    $store = $request->store_id_;
                }
                $sub_unit                      =  \App\Unit::find($single['sub_unit_id']);  
                $product                       =  \App\Product::find($single['product_id']);
                // create_purchase line 
                $pr                            = new PurchaseLine;
                $pr->store_id                  = $store;
                $pr->product_id                = $single['product_id'];
                $pr->transaction_id            = $tr->id;
                $pr->quantity                  = $single['quantity'];
                $pr->pp_without_discount       = $single['pp_without_discount'];
                $pr->discount_percent          = 0;
                $pr->purchase_price            = $single['pp_without_discount'];
                $pr->purchase_price_inc_tax    = ($single['pp_without_discount'] + $single['pp_without_discount']*.05);
                $pr->item_tax                  = $single['pp_without_discount']*.05;
                $pr->sub_unit_id               = ($sub_unit)?$sub_unit->id:null;#2024-8-6
                $pr->sub_unit_qty              = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;#2024-8-6
                $pr->list_price                = ($single['list_price']!=null || $single['list_price']!="")?$single['list_price']:$request->list_price;
                $pr->tax_id                    = 1;
                $pr->order_id                  = ($request->line_sort)?$request->line_sort[$key]:null;
                $pr->variation_id              = ($product->type == "single")?(isset($product->variations[0]->id)?$product->variations[0]->id:NULL):(($product->type == "variable")?($single['variation_id']):(($product->type == "combo")?$single['variation_id']:(isset($product->variations[0]->id)?$product->variations[0]->id:NULL)));
                $pr->save();
               
                //  end
                $data                          =  new OpeningQuantity;
                $data->warehouse_id            =  $store;
                $data->business_location_id    =  $request->location_id;
                $data->quantity                =  $single['quantity'];
                $data->product_id              =  $single['product_id'];
                $data->price                   =  $single['pp_without_discount'];
                $data->product_unit            =  ($sub_unit)?$sub_unit->id:null;#2024-8-6
                $data->product_unit_qty        =  ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;#2024-8-6
                $data->list_price              =  ($single['list_price']!=null || $single['list_price']!="")?$single['list_price']:$request->list_price;
                $data->transaction_id          =  $tr->id;
                $data->purchase_line_id        =  $pr->id;
                $data->date                    =  ($request->date)?\Carbon\Carbon::parse($request->date):date('Y-m-d h:i:s',time());
                $data->variation_id            =  ($product->type == "single")?(isset($product->variations[0]->id)?$product->variations[0]->id:NULL):(($product->type == "variable")?($single['variation_id']):(($product->type == "combo")?$single['variation_id']:(isset($product->variations[0]->id)?$product->variations[0]->id:NULL)));
                $data->save();
                
                $variation_id                  = \App\Variation::find($data->variation_id);
                $quantity_if_multiple_unit     = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;  
                $final_quantity                = $quantity_if_multiple_unit * $data->quantity;  

                if($product->type == "single"){
                    WarehouseInfo::update_stoct($data->product_id,$data->warehouse_id,$final_quantity,$business_id);
                    $info          =  WarehouseInfo::where('store_id',$data->warehouse_id)->where('product_id',$data->product_id)->first();
                }elseif($product->type == "variable"){
                    WarehouseInfo::update_stoct($data->product_id,$data->warehouse_id,$final_quantity,$business_id,$data->variation_id);
                    $info          =  WarehouseInfo::where('store_id',$data->warehouse_id)->where('variation_id',$data->variation_id)->where('product_id',$data->product_id)->first();
                }
 
                //move
                $move                      =  new MovementWarehouse;
                $move->business_id         =  $tr->business_id;
                $move->transaction_id      =  $tr->id  ;
                $move->product_name        =  $data->product->name;
                $move->unit_id             =  ($sub_unit)?$sub_unit->id:null;
                $move->store_id            =  $data->warehouse_id  ;
                $move->movement            =  'opening_stock';
                $move->plus_qty            =  $final_quantity ;#2024-8-6
                $move->minus_qty           =  0;
                $move->product_unit        =  ($sub_unit)?$sub_unit->id:null;#2024-8-6
                $move->product_unit_qty    =  ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;#2024-8-6
                $move->current_qty         =  $info->product_qty ;
                $move->date                =  ($request->date)?\Carbon\Carbon::parse($request->date):date('Y-m-d h:i:s',time()) ;
                $move->product_id          =  $data->product_id;
                if($product->type == "variable"){
                    $move->variation_id    =  $data->variation_id;
                }                           
                $move->current_price       =  $data->price;
                $move->opening_quantity_id =  $data->id;
                $move->save();
                //****** eb ...............................................................................................................................
                $currency_details = $this->transactionUtil->purchaseCurrencyDetails($tr->business_id);
                $this->productUtil->updateProductQuantity($request->location_id,$data->product_id,$variation_id->id ,$final_quantity,0, $currency_details);
                //.........................................................................................................................................
                $before = \App\Models\WarehouseInfo::qty_before($tr);
                if($product->type == "variable"){
                    \App\Models\ItemMove::create_open($tr,0,$before,null,0,$pr->id,$data->variation_id);
                }else{
                    \App\Models\ItemMove::create_open($tr,0,$before,null,0,$pr->id);
                }
            }
             
            DB::commit();
        }
        return redirect('products/Opening_product')
                 ->with('yes',trans('home.Done Successfully'));
        
    }
    public function update(Request $request)
    {
        \DB::beginTransaction();
       
        $business_id           = request()->session()->get('user.business_id');
        $ids                   = ($request->old_item_id)??[];
        $count                 = count($ids);
        $date_open             = \Carbon\Carbon::parse($request->date);
        // ..................................................................
        $tr                    = Transaction::find($request->transaction_id);
        $tr->list_price        = ($request->list_price)?$request->list_price:null;
        $tr->transaction_date  = ($request->date)?:date('Y-m-d h:i:s',time());
        $tr->update();
        // ..................................................................
        $Purchase_Line_id      = PurchaseLine::where('transaction_id',$request->transaction_id)->whereNotIn('id',$ids)->get();
        foreach ($Purchase_Line_id as $re_line) {
            $open              =  OpeningQuantity::where('purchase_line_id',$re_line->id)->first();
            $itemMove          =  \App\Models\ItemMove::where("line_id",$re_line->id)->first();
            MovementWarehouse::where('opening_quantity_id',$open->id)->delete();
            $open ->delete();
            $product_unit_qty       = ($re_line->sub_unit_qty != null)?(($re_line->sub_unit_qty != 0)?$re_line->sub_unit_qty:1):1;
            $product_quantity       = $re_line->quantity*$product_unit_qty ;
            WarehouseInfo::update_stoct($re_line->product_id,$re_line->store_id,($product_quantity*-1),$business_id);
            $re_line->delete();
            if(!empty($itemMove)){
                $itemMove->delete();
            }
        }
        $key = 0 ;
        foreach($request->purchases as $x => $pl){
           
            if ($key < $count) {
                $id                     = $pl["open_id"];
                $store                  = ($pl["store_id"] != null)?$pl["store_id"]:$request->main_store_id;
                $pr_id                  = $pl["purchase_line_id"];
                $price                  = $pl["pp_without_discount_s"];
                $quantity               = $pl["quantity"];
                $data                   = OpeningQuantity::find($id);
                $sub_unit               = \App\Unit::find($pl['sub_unit_id']);  
                if(isset($data)){
                    // #2024-8-6
                    $old_store_id            = $data->warehouse_id;
                    $old_quantity            = $data->quantity;
                    $old_unit_id             = $data->product_unit;
                    $old_unit_qty            = $data->product_unit_qty;
                    $new_unit_qty            = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;
                    $data->quantity          = $quantity;
                    $data->date              = ($request->date)?\Carbon\Carbon::parse($request->date):date('Y-m-d h:i:s',time());
                    $data->product_unit      = ($sub_unit)?$sub_unit->id:null;#2024-8-6
                    $data->product_unit_qty  = $new_unit_qty;#2024-8-6
                    $data->list_price        = ($pl['list_price']!=null || $pl['list_price']!="")?$pl['list_price']:$request->list_price;
                    $data->price             = $price;
                    $data->warehouse_id      = $store;
                    $data->save();
                    \App\PurchaseLine::where('id',$pr_id)
                        ->update([
                            'quantity'               => $quantity,
                            'store_id'               => $store,
                            'sub_unit_id'            => ($sub_unit)?$sub_unit->id:null,#2024-8-6
                            'sub_unit_qty'           => $new_unit_qty,#2024-8-6
                            'purchase_price'         => $price,
                            'list_price'             => ($pl['list_price']!=null || $pl['list_price']!="")?$pl['list_price']:$request->list_price,
                            'order_id'               => ($request->line_sort)?$request->line_sort[$x]:null,
                            'pp_without_discount'    => $price,
                            'purchase_price_inc_tax' => ($price + $price*.05),
                            'item_tax'               => $price*.05
                        ]);
                    $quantity           =  ($new_unit_qty!=0)?$quantity*$new_unit_qty:$quantity;
                    $old_quantity       =  ($old_unit_qty!=0)?$old_quantity*$old_unit_qty:$old_quantity;
                    $diff               =  $quantity - $old_quantity;
                   
                    if ($old_store_id  ==  $store) {
                            WarehouseInfo::update_stoct($data->product_id,$data->warehouse_id,$diff,$business_id);
                    }else{
                        if($old_store_id){
                            WarehouseInfo::update_stoct($data->product_id,$old_store_id,($old_quantity*-1),$business_id);
                        }
                        if($data->warehouse_id){
                            WarehouseInfo::update_stoct($data->product_id,$data->warehouse_id,$quantity,$business_id);
                        }
                    }
                    $info                      =  WarehouseInfo::where('store_id',$data->warehouse_id)->where('product_id',$data->product_id)->first();
                    $move                      =  MovementWarehouse::where('opening_quantity_id',$data->id)->first();
                    $move->store_id            =  $data->warehouse_id  ;
                    $move->plus_qty            =  $quantity ;
                    $move->minus_qty           =  0;
                    $move->product_unit        =  ($sub_unit)?$sub_unit->id:null;#2024-8-6
                    $move->product_unit_qty    =  $new_unit_qty;#2024-8-6
                    $move->current_qty         =  $info->product_qty ;
                    $move->date                =  ($request->date)?\Carbon\Carbon::parse($request->date):date('Y-m-d h:i:s',time()) ;
                    $move->current_price       =  $data->price;
                    $move->movement            =  'opening_stock';
                    $move->save();
                    $before                    = \App\Models\WarehouseInfo::qty_before($tr);
                    \App\Models\ItemMove::create_open($tr,0,$before,null,0,$pr_id);
                }
            }
            $key++;
           
        }
        $key = 0 ;
        foreach($request->purchases as $x => $single){
            if ($key > ($count-1)) {
                // return $key.'_________'.($count-1);
                // create_purchase line 
                $sub_unit                   = \App\Unit::find($single['sub_unit_id']); 
                $product                    = \App\Product::find($single['product_id']);
                $pr                         = new PurchaseLine;
                $pr->store_id               = ($request->stores_id[($key)] != null)?$request->stores_id[($key)]:$request->main_store_id ;
                $pr->product_id             = $single['product_id'];
                $pr->transaction_id         = $tr->id;
                $pr->variation_id           = isset($product->variations[0]->id)?$product->variations[0]->id:NULL;
                $pr->quantity               = $single['quantity'];
                $pr->pp_without_discount    = $single['pp_without_discount'];
                $pr->discount_percent       = 0;
                $pr->purchase_price         = $single['pp_without_discount'];
                $pr->purchase_price_inc_tax = ($single['pp_without_discount'] + $single['pp_without_discount']*.05);
                $pr->item_tax               = $single['pp_without_discount']*.05;
                $pr->sub_unit_id            = ($sub_unit)?$sub_unit->id:null;#2024-8-6
                $pr->sub_unit_qty           = ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;#2024-8-6
                $pr->list_price             = ($single['list_price']!=null || $single['list_price']!="")?$single['list_price']:$request->list_price;
                $pr->order_id               = ($request->line_sort)?$request->line_sort[$x]:null;
                $pr->tax_id                 =  1;
                $pr->save();
                //end
             
                $data                       =  new OpeningQuantity;
                $data->warehouse_id         =  ($request->stores_id[($key)] != null)?$request->stores_id[($key)]:$request->main_store_id;
                $data->business_location_id =  $request->location_id;
                $data->quantity             =  $single['quantity'];
                $data->product_id           =  $single['product_id'];
                $data->price                =  $single['pp_without_discount'];
                $data->transaction_id       =  $tr->id;
                $data->purchase_line_id     =  $pr->id;
                $data->date                 =  ($request->date)?\Carbon\Carbon::parse($request->date):date('Y-m-d h:i:s',time());
                $data->product_unit         =  ($sub_unit)?$sub_unit->id:null;#2024-8-6
                $data->product_unit_qty     =  ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;#2024-8-6
                $data->list_price           =  ($single['list_price']!=null || $single['list_price']!="")?$single['list_price']:$request->list_price;
                $data->save();
                $data                       =  OpeningQuantity::find($data->id);
                $quantity_if_multiple_unit  =  ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;  
                $final_quantity             =  $quantity_if_multiple_unit * $data->quantity;  
                WarehouseInfo::update_stoct($data->product_id,$data->warehouse_id,$final_quantity,$business_id);
                //****** eb ..............................................................
                $variation_id = Variation::where('product_id', $data->product_id)->first();
                //.........................................................................
                //move
                $info                       =  WarehouseInfo::where('store_id',$data->warehouse_id)->where('product_id',$data->product_id)->first();
                $move                       =  new MovementWarehouse;
                $move->business_id          =  $tr->business_id;
                $move->transaction_id       =  $tr->id  ;
                $move->product_name         =  $data->product->name;
                $move->unit_id              =  $data->product->unit_id;
                $move->store_id             =  $data->warehouse_id;
                $move->movement             =  'opening_stock';
                $move->plus_qty             =  $final_quantity ;
                $move->minus_qty            =  0;
                $move->product_unit         =  ($sub_unit)?$sub_unit->id:null;#2024-8-6
                $move->product_unit_qty     =  ($sub_unit)?(($sub_unit->base_unit_multiplier!=null)?$sub_unit->base_unit_multiplier:1):1;#2024-8-6
                $move->current_qty          =  $info->product_qty ;
                $move->date                 =  ($request->date)?\Carbon\Carbon::parse($request->date):date('Y-m-d h:i:s',time());
                $move->product_id           =  $data->product_id;
                $move->current_price        =  $data->price;
                $move->opening_quantity_id  =  $data->id;
                $move->save();
                //****** eb ...............................................................................................................................
                $currency_details           = $this->transactionUtil->purchaseCurrencyDetails($tr->business_id);
                $this->productUtil->updateProductQuantity($request->location_id,$data->product_id,$variation_id->id ,$final_quantity,0, $currency_details);
                $before                     = \App\Models\WarehouseInfo::qty_before($tr);
                \App\Models\ItemMove::create_open($tr,0,$before,null,0,$pr->id); 
            }
            $key++;
        }

       
        DB::commit();
        return redirect('products/Opening_product')
                 ->with('yes',trans('home.Done Successfully'));
        
    }
    public function edit($id)
    {
       $business_id          = request()->session()->get('user.business_id');
       $currency_details     = $this->transactionUtil->purchaseCurrencyDetails($business_id);
       $data                 = OpeningQuantity::find($id);
       $date_tr              = ($data->transaction->transaction_date)?$data->transaction->transaction_date:$data->created_at;
       $date                 = \Carbon\Carbon::parse($date_tr);
       $stores               = \App\Models\Warehouse::childs($business_id);
       $customer_groups      = CustomerGroup::forDropdown($business_id);
       $business_locations   = BusinessLocation::forDropdown($business_id);
       $OpeningQuantity      = OpeningQuantity::Where("transaction_id",$data->transaction_id)->get();
       $purchase_lines       = PurchaseLine::Where("transaction_id",$data->transaction_id)->orderBy("order_id","desc")->get();
       $open_store           = [];
       $pline_store          = [];
       $open_id              = [];
       $mainstore_categories = $stores;
        foreach ($purchase_lines as $key => $pl) {
                $pline_store[$key] = $pl->id;
        }
        foreach ($OpeningQuantity as $key => $op) {
                $open_store[$key] = $op->warehouse_id;
                $open_id[$key]    = $op->id;
        }
        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $transaction = \App\Transaction::find($data->transaction_id);
        $customer_groups = CustomerGroup::forDropdown($business_id);
        $row            = 1;$line_prices  = [];#2024-8-6
        $list_of_prices         = \App\Product::getListPrices($row);
        
       return view("product.EditProduct")->with(compact("data","list_of_prices","date","types","pline_store","open_id","open_store","currency_details","purchase_lines","OpeningQuantity","mainstore_categories","customer_groups",'business_locations', "stores","transaction"));
    }

    public function destroy($id)
    {
         
        $data           = OpeningQuantity::find($id);
        $move_id_       = [];  
        if ($data) {
            $business_id    = request()->session()->get('user.business_id');
            $purchase_line  = \App\PurchaseLine::where("transaction_id",$data->transaction_id)->get();
            foreach($purchase_line as $pli){
                $open     = \App\Models\OpeningQuantity::where("transaction_id",$data->transaction_id)->where("purchase_line_id",$pli->id)->first(); 
                WarehouseInfo::update_stoct($pli->product_id,$pli->store_id,($pli->quantity*-1),$business_id);
                MovementWarehouse::where('opening_quantity_id',$open->id)->delete();
                $itemMove = \App\Models\ItemMove::where("transaction_id",$data->transaction_id)->where("line_id",$pli->id)->first(); 
                if(!empty($itemMove)){
                        $move_id_[] = $itemMove->id; 
                        $pli->delete();
                        $itemMove->delete();
                        if(!empty($open)){
                            $open->delete();
                        }
                        \App\Models\ItemMove::updateRefresh($itemMove,$pli,$move_id_);
                }else{
                        if(!empty($open)){
                            $open->delete();
                        }
                        $pli->delete();
                }   
            }
            \App\Models\ItemMove::updateRefresh($itemMove,$pli,$move_id_);
        }
        return back()
                 ->with('yes',trans('home.Done Successfully'));
       

    }
}
