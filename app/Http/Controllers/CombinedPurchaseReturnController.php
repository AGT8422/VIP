<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\PurchaseLine;
use App\TaxRate;
use App\Transaction;
use App\CustomerGroup;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\MovementWarehouse;
use App\Models\TransactionRecieved;
use App\Models\RecievedPrevious;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CombinedPurchaseReturnController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('purchase_return.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse();
            } 
        }
        $currency     =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies   = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $business_locations = BusinessLocation::forDropdown($business_id);

        $taxes                = TaxRate::where('business_id', $business_id)->ExcludeForTaxGroup()->get();
        $ship_from            = 1;
        $mainstore_categories = \App\Models\Warehouse::childs($business_id);
        $cost_centers         =  \App\Account::cost_centers();
        $orderStatuses        = $this->productUtil->orderStatuses();
        $row                  = 1;$line_prices  = [];#2024-8-6
        $list_of_prices       = \App\Product::getListPrices($row);
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
        $customer_groups = \App\CustomerGroup::forDropdown($business_id);
    
        return view('purchase_return.create')
            ->with(compact('business_locations','types','customer_groups','list_of_prices','currencies', 'orderStatuses', 'ship_from', 'taxes', 'mainstore_categories', 'cost_centers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        
        if (!auth()->user()->can('purchase_return.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
           
            $input_data = $request->only([ 
                'location_id', 'transaction_date', 'final_total','total_finals_','status',
                'ref_no','cost_center_id','store_id','discount_amount2','discount_type','currency_id','currency_id_amount',
                'tax_id', 'tax_amount2', 'contact_id','sup_ref_no','shipping_details','additional_notes'
                ]);
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                    return $this->moduleUtil->expiredResponse();
                } 
            }
            $company_name                   = request()->session()->get("user_main.domain");
            // dd($request);
            $edit_days          = request()->session()->get('business.transaction_edit_days');
            $edit_date          = request()->session()->get('business.transaction_edit_date');
           
            if (!$this->transactionUtil->canBeEdited(0, $edit_date,$request->transaction_date)) {
                $output =  [
                            'success' => 0,
                            'msg'     => __('messages.transaction_add_not_allowed', ['days' => $edit_date])];
                return redirect('purchase-return')->with('status', $output);
            }
            $user_id                        = $request->session()->get('user.id');
            $input_data['store']            = $input_data['store_id'];
            $input_data['type']             = 'purchase_return';
            $input_data['tax_amount']       = $input_data['tax_amount2'];
            $input_data['business_id']      = $business_id;
            $input_data['created_by']       = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['total_before_tax'] = $input_data['total_finals_'] - $input_data['tax_amount2'];
            $input_data['sup_refe']         = $input_data['sup_ref_no'];
            $input_data['ship_amount']      = 0;
            $input_data['payment_status']   = 2;
            $input_data['currency_id']      = $input_data['currency_id'];
            $input_data['exchange_price']   = ($input_data['currency_id']!= null && $input_data['currency_id_amount'] != 0)?($input_data['currency_id_amount']):1;
            $input_data['amount_in_currency']   = ($input_data['currency_id']!= null && $input_data['currency_id_amount'] != 0)?($input_data['final_total'] / $input_data['currency_id_amount']):null ;
            $input_data['discount_amount']  = $input_data['discount_amount2'];
            # Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('purchase_return');
            # Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('purchase_return', $ref_count);
            }
            # upload document
            # $input_data['document'] = $this->productUtil->uploadFile($request, 'document', 'documents');
            $document_purchase = [];
            if ($request->hasFile('document_purchase')) {
                $id_cc = 1;
                $referencesNewStyle = str_replace('/', '-', $input_data['ref_no']);
                foreach ($request->file('document_purchase') as $file) {
                    #................
                    if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                        if ($file->getSize() <= config('constants.document_size_limit')){ 
                            $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$id_cc++.'_'.$file->getClientOriginalName();
                            $file->move('uploads/companies/'.$company_name.'/documents/purchase_return',$file_name_m);
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase_return/'. $file_name_m;
                        }
                    }else{
                        if ($file->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time().'_'.$referencesNewStyle.'_'.$id_cc++.'_'.$file->getClientOriginalName();
                            $Data         = getimagesize($file);
                            $width         = $Data[0];
                            $height        = $Data[1];
                            $half_width    = $width/2;
                            $half_height   = $height/2; 
                            $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase_return/'. $new_file_name;
                            // if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\purchase_return\\$new_file_name"),20)) {
                            //     $uploaded_file_name = $new_file_name;
                            // }
                            $public_path = public_path('uploads/companies/'.$company_name.'/documents/purchase_return');
                            if (!file_exists($public_path)) {
                                mkdir($public_path, 0755, true);
                            }
                            if ($imgs->save($public_path ."/" . $new_file_name)) {
                                $uploaded_file_name = $new_file_name;
                            }
                        }
                    }
                    #................
                   array_push($document_purchase,$file_name);
                }
            }
            
            $sub_total_rt_purchase    = 0;
            $products                 = $request->input('products');
            $input_data['document']   = json_encode($document_purchase);
            
            if (!empty($products)) {
                $product_data = [];
                $purchase_return                   =  Transaction::create($input_data);
                $archive                           =  \App\Models\ArchiveTransaction::save_parent($purchase_return,"create");
                $purchase_return->return_parent_id =  $purchase_return->id;
                $purchase_return->save();
                foreach ($products as $key=>$product) {
                    $unit_price                    =  $this->productUtil->num_uf($product['unit_price_before_dis_exc']);
                    $unit_price_after_dis_exc      =  $this->productUtil->num_uf($product['unit_price_after_dis_exc']);
                    $unit_price_after_dis_inc      =  $this->productUtil->num_uf($product['unit_price_after_dis_inc']);
                    $return_line = [
                        'product_id'               => $product['product_id'],
                        'store_id'                 => $input_data['store_id'],
                        'variation_id'             => $product['variation_id'],
                        'quantity'                 => $this->productUtil->num_uf($product['quantity']),
                        'pp_without_discount'      => $unit_price,
                        'purchase_price'           => $unit_price_after_dis_exc,
                        'bill_return_price'        => $unit_price_after_dis_exc,
                        'discount_percent'         => $product["discount_percent_return"],  
                        'purchase_price_inc_tax'   => $unit_price_after_dis_inc,
                        'quantity_returned'        => $this->productUtil->num_uf($product['quantity']),
                        'lot_number'               => !empty($product['lot_number']) ? $product['lot_number'] : null,
                        'exp_date'                 => !empty($product['exp_date']) ? $this->productUtil->uf_date($product['exp_date']) : null
                    ];
                    $sub_total_rt_purchase        += ($product['quantity']*$unit_price_after_dis_exc) ;
                    $product_data[]                = $return_line;
                }
                $purchase_return->purchase_lines()->createMany($product_data);
                $purchaseLines = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
                foreach($purchaseLines as $it){
                    \App\Models\ArchivePurchaseLine::save_purchases($archive , $it);
                }
                # update payment status
            }
            if($request->status == "received" || $request->status == "final"){
                 \App\AccountTransaction::return_purchase($purchase_return,$input_data['discount_amount2'],$request->total_finals_,$sub_total_rt_purchase,$input_data['tax_amount2']);
            }
            
            $additional_inputs = $request->only([
                'contact_id','shipping_amount','shipping_vat','shipping_total','shipping_account_id','shiping_text',
                'shiping_date','shipping_contact_id','shipping_cost_center_id','cost_center_id'
            ]);

            $document_expense = [];
            if ($request->hasFile('document_expense')) {
                $i =1;
                $referencesNewStyle = str_replace('/', '-', $input_data['ref_no']);
                foreach ($request->file('document_expense') as $file) {
                    #................
                    if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                        if ($file->getSize() <= config('constants.document_size_limit')){ 
                            $file_name_m    =   time().'_'.$referencesNewStyle.'_Expense_'.$i++.'_'.$file->getClientOriginalName();
                            $file->move('uploads/companies/'.$company_name.'/documents/purchase_return/expense',$file_name_m);
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase_return/expense/'. $file_name_m;
                        }
                    }else{
                        if ($file->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time().'_'.$referencesNewStyle.'_Expense_'.$i++.'_'.$file->getClientOriginalName();
                            $Data         = getimagesize($file);
                            $width         = $Data[0];
                            $height        = $Data[1];
                            $half_width    = $width/2;
                            $half_height   = $height/2; 
                            $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase_return/expense/'. $new_file_name;
                            // if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\purchase_return\\expense\\$new_file_name"),20)) {
                            //     $uploaded_file_name = $new_file_name;
                            // }
                            $public_path = public_path('uploads/companies/'.$company_name.'/documents/purchase_return/expense');
                            if (!file_exists($public_path)) {
                                mkdir($public_path, 0755, true);
                            }
                            if ($imgs->save($public_path ."/" . $new_file_name)) {
                                $uploaded_file_name = $new_file_name;
                            } 
                        }
                    }
                    #................
                    array_push($document_expense,$file_name);
                }
            }
            //...................... entries for shipping .............................
            //..........................................................................
            if($request->shipping_amount != null){
                \App\Models\AdditionalShipping::add_purchase($purchase_return->id,$additional_inputs,$document_expense);
            }
            if($request->status == "received" || $request->status == "final"){
                if($request->shipping_amount != null){
                     \App\Models\AdditionalShipping::add_purchase_payment($purchase_return->id,null,null,1);
                }
                //..........................................................................
                //..........................................................................
                $type="PReturn";
                \App\Models\Entry::create_entries($purchase_return,$type,null,null,null,null,$purchase_return->id);
                
                $entry    = \App\Models\Entry::orderBy("id","desc")->where('account_transaction',$purchase_return->id)->first();
                if(!empty($entry)){
                    $accountTransaction = \App\AccountTransaction::where("transaction_id",$purchase_return->id)->get();
                    foreach($accountTransaction as $it){
                        $it->entry_id = ($entry)? $entry->id:null;
                        $it->update();
                    }
                }
            }
            if($request->status == "received"  ){
 
                $pr_lines = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
                foreach($pr_lines as $it){
                    $price  = \App\PurchaseLine::orderby("id","desc")->where("product_id",$it->product_id)->first();
                    \App\Models\WarehouseInfo::update_stoct($it->product_id,$it->store_id,$it->quantity*-1,$it->transaction->business_id);
                    
                    $prev                  =  new RecievedPrevious;
                    $prev->product_id      =  $it->product_id;
                    $prev->store_id        =  $it->store_id;
                    $prev->business_id     =  $it->transaction->business_id ;
                    $prev->product_name    =  $it->product->name ;
                    $prev->transaction_id  =  $it->transaction->id;
                    $prev->unit_id         =  $it->product->unit->id;
                    $prev->total_qty       =  $it->quantity;
                    $prev->current_qty     =  $it->quantity;
                    $prev->remain_qty      =  0;
                    $prev->line_id         =  $it->id;  
                    $prev->is_returned     =  1;  
                    $prev->save();
                    
                    //***  ........... eb
                    MovementWarehouse::movemnet_warehouse($it->transaction,$it->product,$it->quantity,$it->store_id,$price,'minus',$prev->id);
                    //......................................................
                }
               
                //.....................................................................
                $tr_received      = \App\Models\TransactionRecieved::where("transaction_id",$purchase_return->id)->first();
                if(empty($tr_received)){
                    $type                         =  'purchase_receive';
                    $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
                    $receipt_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                    $tr_received                  =  new TransactionRecieved;
                    $tr_received->store_id        =  $purchase_return->store;
                    $tr_received->transaction_id  =  $purchase_return->id;
                    $tr_received->business_id     =  $purchase_return->business_id ;
                    $tr_received->reciept_no      =  $receipt_no ;
                    $tr_received->ref_no          =  $purchase_return->ref_no;
                    $tr_received->is_returned     =  1;
                    $tr_received->status          = 'Return Purchase';
                    $tr_received->save();
                }
                $prev_s = \App\Models\RecievedPrevious::where("transaction_id",$purchase_return->id)->get();
                    foreach($prev_s as $pr){
                        $item_prv = \App\Models\RecievedPrevious::find($pr->id); 
                        $item_prv->transaction_deliveries_id = $tr_received->id;
                        $item_prv->update();
                    }
                
                \App\Models\ItemMove::return_recieve($purchase_return,$tr_received->id);

            } else {
                $sellLine = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
                $service_lines = [] ;
                foreach($sellLine as $it){
                    if($it->product->enable_stock == 0){   
                        $service_lines[]=$it;                             
                    }
                }
                if(count($service_lines)>0){
                        $type                              =  'trans_delivery';
                        $ref_count                         =  $this->productUtil->setAndGetReferenceCount($type);
                        $receipt_no                        =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                        $tr_received                       =  new TransactionRecieved;
                        $tr_received->store_id             =  $purchase_return->store;
                        $tr_received->transaction_id       =  $purchase_return->id;
                        $tr_received->business_id          =  $purchase_return->business_id ;
                        $tr_received->reciept_no           =  $receipt_no ;
                        $tr_received->invoice_no           =  $purchase_return->ref_no; 
                        $tr_received->status               = 'Service Item';
                        $tr_received->save();

                    foreach($service_lines as $it){

                        $prev                              =  new RecievedPrevious;
                        $prev->product_id                  =  $it->product_id;
                        $prev->store_id                    =  $it->store_id;
                        $prev->business_id                 =  $it->transaction->business_id ;
                        $prev->transaction_id              =  $it->transaction->id;
                        $prev->unit_id                     =  $it->product->unit->id;
                        $prev->total_qty                   =  $it->quantity;
                        $prev->current_qty                 =  $it->quantity;
                        $prev->remain_qty                  =  0;
                        $prev->transaction_deliveries_id   =  $tr_received->id;
                        $prev->product_name   =  $it->product->name;
                        $prev->line_id        =  $it->id;
                        $prev->save();
                       
                        \App\Models\WarehouseInfo::update_stoct($it->product->id,$it->store_id,$it->quantity*-1,$it->transaction->business_id);
                        \App\MovementWarehouse::movemnet_warehouse($purchase_return,$it->product,$it->quantity,$it->store_id,$it,"minus",$tr_received->id);
                         
                    }
                    \App\Models\ItemMove::return_recieve($purchase_return,$tr_received->id);
                    Transaction::update_status($id);
                }
            }
            $output = ['success' => 1,
                            'msg' => __('lang_v1.purchase_return_added_success')
                        ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            // 'msg' => __('messages.something_went_wrong')
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }

        return redirect('purchase-return')->with('status', $output);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('purchase_return.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $purchase_return = Transaction::where('business_id', $business_id)
                                    ->with(['contact'])
                                    ->find($id);
        $location_id    = $purchase_return->location_id;
        $purchase_lines = PurchaseLine::
                        join(
                            'products AS p',
                            'purchase_lines.product_id',
                            '=',
                            'p.id'
                        )
                        ->join(
                            'variations AS variations',
                            'purchase_lines.variation_id',
                            '=',
                            'variations.id'
                        )
                        ->join(
                            'product_variations AS pv',
                            'variations.product_variation_id',
                            '=',
                            'pv.id'
                        )
                        ->leftjoin('variation_location_details AS vld', function ($join) use ($location_id) {
                            $join->on('variations.id', '=', 'vld.variation_id')
                                ->where('vld.location_id', '=', $location_id);
                        })
                        ->leftjoin('units', 'units.id', '=', 'p.unit_id')
                        ->where('purchase_lines.transaction_id', $id)
                        ->select(
                            DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, 
                                    ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                            'p.id as product_id',
                            'p.enable_stock',
                            'pv.is_dummy as is_dummy',
                            'variations.sub_sku',
                            'vld.qty_available',
                            'variations.id as variation_id',
                            'units.id as unit_id',
                            'units.short_name as unit',
                            'units.allow_decimal as unit_allow_decimal',
                            'purchase_lines.purchase_price',
                            'purchase_lines.sub_unit_id',
                            'purchase_lines.list_price',
                            'purchase_lines.id as purchase_line_id',
                            'purchase_lines.quantity_returned as quantity_returned',
                            'purchase_lines.lot_number',
                            'purchase_lines.exp_date',
                            
                        )
                        // ->groupBy("p.id")
                        ->get();
       
        foreach ($purchase_lines as $key => $value) {
            $purchase_lines[$key]->qty_available += $value->quantity_returned;
            $purchase_lines[$key]->formatted_qty_available = $this->productUtil->num_f($purchase_lines[$key]->qty_available);
        }
        $product_list = [];
        $pur = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
        foreach($pur as $it){
            $product_list[] = $it->product_id;
        }
        $Purchaseline     = PurchaseLine::where("transaction_id",$purchase_return->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(quantity) as total"))->first()->total;
        $RecievedPrevious = RecievedPrevious::where("transaction_id",$purchase_return->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
        if($Purchaseline <= $RecievedPrevious){
            $state   = "received";
        }else{
            if($Purchaseline  != $RecievedPrevious){
                if($purchase_return->status  == "received"){
                    $state   = "final";
                }else{
                    $state   = $purchase_return->status;
                }        
            }
        }
        $currency     =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies   = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
         $taxes = TaxRate::where('business_id', $business_id)
                        ->ExcludeForTaxGroup()
                        ->get();
        $ship_from            = 0;
        $mainstore_categories = \App\Models\Warehouse::childs($business_id);
        $cost_centers  =  \App\Account::cost_centers();
        $orderStatuses = $this->productUtil->orderStatuses();
        $TranRecieved  = TransactionRecieved::where("business_id",$business_id)->where("transaction_id",$id)->first();
        $row            = 1;$line_prices  = [];#2024-8-6
        $list_of_prices         = \App\Product::getListPrices($row);
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
        $customer_groups    = CustomerGroup::forDropdown($business_id);
        return view('purchase_return.edit')
            ->with(compact('business_locations','list_of_prices','customer_groups','currencies','types','mainstore_categories' ,'orderStatuses' ,'TranRecieved' ,'state','ship_from' ,'cost_centers' , 'taxes', 'purchase_return', 'purchase_lines'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        if (!auth()->user()->can('purchase_return.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            DB::beginTransaction();
            $input_data     = $request->only([
                            'location_id', 
                            'transaction_date', 
                            'final_total',
                            'status',
                            'currency_id',
                            'currency_id_amount',
                            'ref_no',
                            'cost_center_id',
                            'store_id',
                            'discount_amount2',
                            'discount_amount',
                            'discount_type',
                            'tax_id', 
                            'tax_amount2', 
                            'contact_id',
                            'sup_ref_no',
                            'shipping_details',
                            'additional_notes'
                        ]);

            $business_id    = $request->session()->get('user.business_id');
            if (!empty($request->input('ref_no'))) {  $input_data['ref_no'] = $request->input('ref_no'); }
            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                    return $this->moduleUtil->expiredResponse();
                } 
            } 
            $company_name                       = request()->session()->get("user_main.domain");
            $input_data['transaction_date']     = $this->productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['total_before_tax']     = $this->productUtil->num_uf($input_data['final_total']) - $this->productUtil->num_uf($input_data['tax_amount2']);
            $input_data['currency_id']          = $input_data['currency_id'];
            $input_data['exchange_price']       = $input_data['currency_id_amount'];
            $input_data['amount_in_currency']   = ($input_data['currency_id_amount']!= 0)? $input_data['final_total'] / $input_data['currency_id_amount'] :0;
            $input_data['discount_amount']      = $input_data['discount_amount'] ;
            $products           = $request->input('products');
            $purchase_return_id = $request->input('purchase_return_id');
            $purchase_return    = Transaction::where('business_id', $business_id)->where('type', 'purchase_return')->find($purchase_return_id);
            $old_date           = $purchase_return->transaction_date;
            $edit_days          = request()->session()->get('business.transaction_edit_days');
            $edit_date          = request()->session()->get('business.transaction_edit_date');
           
            $dateFilter       = (\Carbon::parse($request->transaction_date)<\Carbon::parse($old_date))?$request->transaction_date:$old_date;
            if (!$this->transactionUtil->canBeEdited($purchase_return_id, $edit_date,$dateFilter)) {
                $output =  [
                            'success' => 0,
                            'msg'     => __('messages.transaction_edit_not_allowed', ['days' => $edit_date])];
                return redirect('purchase-return')->with('status', $output);
            }
            # upload document
            $document_purchase                  = [];
            if ($request->hasFile('document_purchase')) {
                $i = 1;
                $referencesNewStyle = str_replace('/', '-', $purchase_return->ref_no);
                foreach ($request->file('document_purchase') as $file) {
                    
                    #................   
                    if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                        if ($file->getSize() <= config('constants.document_size_limit')){ 
                            $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$i++.'_'.$file->getClientOriginalName();
                            $file->move('uploads/companies/'.$company_name.'/documents/purchase_return',$file_name_m);
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase_return/'. $file_name_m;
                        }
                    }else{
                        if ($file->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time().'_'.$referencesNewStyle.'_'.$i++.'_'.$file->getClientOriginalName();
                            $Data         = getimagesize($file);
                            $width         = $Data[0];
                            $height        = $Data[1];
                            $half_width    = $width/2;
                            $half_height   = $height/2; 
                            $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase_return/'. $new_file_name;
                            // if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\purchase_return\\$new_file_name"),20)) {
                            //     $uploaded_file_name = $new_file_name;
                            // }
                            $public_path = public_path('uploads/companies/'.$company_name.'/documents/purchase_return');
                            if (!file_exists($public_path)) {
                                mkdir($public_path, 0755, true);
                            }
                            if ($imgs->save($public_path ."/" . $new_file_name)) {
                                $uploaded_file_name = $new_file_name;
                            }     
                        }
                    }
                    #................
                    
                    array_push($document_purchase,$file_name);
                }
            }
            if(json_encode($document_purchase)!="[]"){  $input_data['document']         = json_encode($document_purchase); }
            $archive            = \App\Models\ArchiveTransaction::save_parent($purchase_return,"edit");
            $purchaseLines      = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
            foreach($purchaseLines as $it){ \App\Models\ArchivePurchaseLine::save_purchases( $archive , $it); }
            $old_return         = $purchase_return->replicate();
            $old_lines          = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
            $lines_purchase     = [];
            foreach($old_lines as $ele){  $lines_purchase[$ele->id] =  $ele->replicate(); }
            $sub_total_rt_purchase = 0;
            if (!empty($products)) {
                $product_data           = [];
                $new_id                 = [];
                $updated_purchase_lines = [];
                foreach ($products as $product) {
                    $unit_price                   = $this->productUtil->num_uf($product['unit_price_before_dis_exc']);
                    $unit_price_after_dis_exc     = $this->productUtil->num_uf($product['unit_price_after_dis_exc']);
                    $unit_price_after_dis_inc     = $this->productUtil->num_uf($product['unit_price_after_dis_inc']);
                    if (!empty($product['purchase_line_id'])) {
                        $return_line              = PurchaseLine::find($product['purchase_line_id']);
                        $updated_purchase_lines[] = $return_line->id;
                    } else {
                        $return_line         =  new PurchaseLine([
                            'product_id'     => $product['product_id'],
                            'variation_id'   => $product['variation_id'],
                            'quantity'       => $this->productUtil->num_uf($product['quantity']),
                            'transaction_id' => $purchase_return_id
                        ]);
                    }
                    $sub_total_rt_purchase              += ($product['quantity']*$unit_price_after_dis_exc) ;
                    $return_line->store_id               = $request->store_id;
                    $return_line->quantity               = $this->productUtil->num_uf($product['quantity']);
                    $return_line->purchase_price         = $unit_price_after_dis_exc;
                    $return_line->pp_without_discount    = $unit_price;
                    $return_line->purchase_price_inc_tax = $unit_price_after_dis_inc;
                    $return_line->discount_percent       = $product['discount_percent_return'];
                    $return_line->bill_return_price      =  $unit_price_after_dis_exc;
                    $return_line->quantity_returned      = $this->productUtil->num_uf($product['quantity']);
                    $return_line->lot_number             = !empty($product['lot_number']) ? $product['lot_number'] : null;
                    $return_line->exp_date               = !empty($product['exp_date']) ? $this->productUtil->uf_date($product['exp_date']) : null;
                    
                    if (!empty($product['purchase_line_id'])) {
                        $return_line->update();
                    } else {
                        $return_line->save();
                        $new_id[] = $return_line->id;
                    }
                    $product_data[] = $return_line;
                }
                $purchase_return->update($input_data);
                # If purchase line deleted add return quantity to stock
                $deleted_purchase_lines = PurchaseLine::where('transaction_id', $purchase_return_id)->whereNotIn('id', $updated_purchase_lines)->whereNotIn('id',$new_id)->get();
                PurchaseLine::where('transaction_id', $purchase_return_id)->whereNotIn('id', $updated_purchase_lines)->whereNotIn('id',$new_id)->delete();
                if($request->status == "received" && $old_return->status != "received" ){
                    $prc_lines = \App\PurchaseLine::where("transaction_id",$purchase_return->id)->get();
                    foreach($prc_lines as $it){
                        $price  = \App\PurchaseLine::orderby("id","desc")->where("product_id",$it->product_id)->first();
                        \App\Models\WarehouseInfo::update_stoct($it->product_id,$it->store_id,$it->quantity*-1,$it->transaction->business_id);
                        
                        $prev                  =  new RecievedPrevious;
                        $prev->product_id      =  $it->product_id;
                        $prev->store_id        =  $it->store_id;
                        $prev->business_id     =  $it->transaction->business_id ;
                        $prev->product_name    =  $it->product->name ;
                        $prev->transaction_id  =  $it->transaction->id;
                        $prev->unit_id         =  $it->product->unit->id;
                        $prev->total_qty       =  $it->quantity;
                        $prev->current_qty     =  $it->quantity;
                        $prev->remain_qty      =  0;
                        $prev->line_id         =  $it->id;  
                        $prev->is_returned     =  1;  
                        $prev->save();
                        //***  ........... eb
                        MovementWarehouse::movemnet_warehouse($it->transaction,$it->product,$it->quantity,$it->store_id,$price,'minus',$prev->id);
                        //......................................................
                    }
                   
                    //.....................................................................
                    $tr_received      = \App\Models\TransactionRecieved::where("transaction_id",$purchase_return->id)->first();
                    if(empty($tr_received)){
                        $type                         =  'purchase_receive';
                        $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
                        $receipt_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                        $tr_received                  =  new TransactionRecieved;
                        $tr_received->store_id        =  $purchase_return->store;
                        $tr_received->transaction_id  =  $purchase_return->id;
                        $tr_received->business_id     =  $purchase_return->business_id ;
                        $tr_received->reciept_no      =  $receipt_no ;
                        $tr_received->ref_no          =  $purchase_return->ref_no;
                        $tr_received->is_returned     =  1;
                        $tr_received->status          = 'Return Purchase';
                        $tr_received->save();
                    }
                    $prev_s = \App\Models\RecievedPrevious::where("transaction_id",$purchase_return->id)->get();
                    foreach($prev_s as $pr){
                        $item_prv = \App\Models\RecievedPrevious::find($pr->id); 
                        $item_prv->transaction_deliveries_id = $tr_received->id;
                        $item_prv->update();
                    }
                    \App\Models\ItemMove::return_recieve($purchase_return,$tr_received->id);


                } else if($request->status == "received" && $old_return->status == "received" ){
                    $tr_received = \App\Models\TransactionRecieved::where("transaction_id",$purchase_return->id)->first();
                    \App\Models\ItemMove::return_recieve_update($tr_received,$purchase_return,$tr_received->id);
                }
                if(($request->status != "received" && $request->status != "final") && ($old_return->status == "final"  )){
                    $all = \App\AccountTransaction::where('transaction_id',$purchase_return_id)->get();
                    foreach($all as $i){
                        $account_transaction  = \App\Account::find($i->account_id); 
                        $i->delete();
                        if($account_transaction->cost_center!=1){
                            \App\AccountTransaction::nextRecords($account_transaction->id,$purchase_return->business_id,$purchase_return->transaction_date);
                        }
                    }   
                    \App\Models\Entry::delete_entries($purchase_return_id);
                    \App\Models\ItemMove::delete_move($purchase_return_id);
                    \App\Models\TransactionRecieved::where('transaction_id',$purchase_return_id)->delete();
                    \App\MovementWarehouse::where('transaction_id',$purchase_return_id)->delete();
                    RecievedPrevious::where('transaction_id',$purchase_return_id)->delete();
                    $shipping_id = \App\Models\AdditionalShipping::where("transaction_id",$purchase_return_id)->first();
                    if(!empty($shipping_id)){ \App\Models\Entry::where("shipping_id",$shipping_id->id)->delete(); }
                    \App\Models\Entry::where("account_transaction",$purchase_return_id)->delete();
                    \App\Models\StatusLive::where("transaction_id",$purchase_return_id)->whereNotNull("shipping_item_id")->delete();
                    \App\Models\StatusLive::where("transaction_id",$purchase_return_id)->where("num_serial","!=",1)->delete();
                    \App\Models\StatusLive::insert_data_p($business_id,$purchase_return,$request->status);
                    $allTransaction = \App\AccountTransaction::where('transaction_id',$purchase_return_id)->whereNotNull('additional_shipping_item_id')->get();
                    foreach($allTransaction as $o){
                        $account_transaction = \App\Account::find($o->account_id); 
                        if($account_transaction->cost_center!=1){
                             \App\AccountTransaction::nextRecords($account_transaction->id,$purchase_return->business_id,$purchase_return->transaction_date);
                        }
                    }
                }   
                # update payment status
                $this->transactionUtil->updatePaymentStatus($purchase_return_id, $purchase_return->final_total);
            }
            $additional_inputs = $request->only([
                'contact_id','shipping_amount','shipping_vat','shipping_total','shipping_account_id','shiping_text',
                'shiping_date','additional_shipping_item_id','old_shipping_amount','old_shipping_vat','old_shipping_total','old_shipping_account_id',
                'old_shiping_text','old_shiping_date','old_shipping_contact_id','shipping_contact_id','old_shipping_cost_center_id','cost_center_id'
            ]);
            $document_expense = $request->old_document??[];
            if ($request->hasFile('document_expense')) {
                $id_ex = 1;
                $referencesNewStyle = str_replace('/', '-', $purchase_return->ref_no);
                foreach ($request->file('document_expense') as $file) {
                    #................
                    if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                        if ($file->getSize() <= config('constants.document_size_limit')){ 
                            $file_name_m    =   time().'_'.$referencesNewStyle.'_Expense_'.$id_ex++.'_'.$file->getClientOriginalName();
                            $file->move('uploads/companies/'.$company_name.'/documents/purchase_return/expense',$file_name_m);
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase_return/expense/'. $file_name_m;
                        }
                    }else{
                        if ($file->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time().'_'.$referencesNewStyle.'_Expense_'.$id_ex++.'_'.$file->getClientOriginalName();
                            $Data          = getimagesize($file);
                            $width         = $Data[0];
                            $height        = $Data[1];
                            $half_width    = $width/2;
                            $half_height   = $height/2; 
                            $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase_return/expense'. $new_file_name;
                            // if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\purchase_return\\expense\\$new_file_name"),20)) {
                            //     $uploaded_file_name = $new_file_name;
                            // }
                            $public_path = public_path('uploads/companies/'.$company_name.'/documents/purchase_return/expense');
                            if (!file_exists($public_path)) {
                                mkdir($public_path, 0755, true);
                            }
                            if ($imgs->save($public_path ."/" . $new_file_name)) {
                                $uploaded_file_name = $new_file_name;
                            }    
                        }
                    }
                    #................
                    array_push($document_expense,$file_name);
                }
            } 
            
            if(($request->status == "received" || $request->status == "final")){
                \App\Models\AdditionalShipping::update_purchase($purchase_return->id,$additional_inputs,$document_expense);
                \App\Models\AdditionalShipping::add_purchase_payment($purchase_return->id,null,null,1);
                \App\AccountTransaction::update_return_purchase($purchase_return,$input_data['discount_amount2'],$request->total_finals_,$sub_total_rt_purchase,$input_data['tax_amount2'],$old_return,null,$old_date);
            }
            if( ($request->status == "received" || $request->status == "final") && ($old_return->status != "received" && $old_return->status != "final" )){
                $type="PReturn";
                \App\Models\Entry::create_entries($purchase_return,$type,null,null,null,null,$purchase_return->id);
                $entry    = \App\Models\Entry::orderBy("id","desc")->where('account_transaction',$purchase_return->id)->first();
                if(!empty($entry)){
                    $accountTransaction = \App\AccountTransaction::where("transaction_id",$purchase_return->id)->get();
                    foreach($accountTransaction as $it){
                        $it->entry_id = ($entry)? $entry->id:null;
                        $account_transaction = \App\Account::find($it->account_id);
                        $it->update();
                        if($account_transaction->cost_center!=1){
                            \App\AccountTransaction::nextRecords($account_transaction->id,$purchase_return->business_id,$purchase_return->transaction_date);
                       }
                    }
                }
            }
            $output = [
                    'success' => 1,
                    'msg'     => __('lang_v1.purchase_return_updated_success')
            ];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg'     => __('messages.something_went_wrong')
            ];
        }

        return redirect('purchase-return')->with('status', $output);
    }

    /**
     * Return product rows
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getProductRow(Request $request)
    {
        if (request()->ajax()) {
          
            $row_index    = $request->input('row_index');
            $row_count    = $request->input('row_index');
            $variation_id = $request->input('variation_id');
            $location_id  = $request->input('location_id');
            $currency     = $request->input('currency');
            $list_of_prices_in_unit = [];
            $business_id = $request->session()->get('user.business_id');
            $buss        = \App\Business::find($business_id);
            if(app("request")->input("check")){ 
                $product       = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id,false);
                 
            }else{ 
                $product       = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id);
                
            }
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);
            $products     = \App\Variation::find($request->variation_id);  
            $pro_id       = ($products)?$products->product_id:null;
            if($pro_id != null){
                $quantity    = \App\Models\WarehouseInfo::where("business_id",$business_id)->where("store_id",$request->store_id)->where("product_id",$pro_id)->sum('product_qty');
            }else{
                $quantity    = 0;
            }
            $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
            $stores       =  \App\Models\Warehouse::childs($business_id);
            $allUnits     =  [];
            if($pro_id != null){
                $pro                    =  \App\Product::find($products->product_id);
                $var                    =  $pro->variations->first();
                $var                    =  ($var)?$var->default_sell_price:0;
                $UU                     = \App\Unit::find($pro->unit_id);
                $allUnits[$UU->id] = [
                                            'name'          => $UU->actual_name,
                                            'multiplier'    => $UU->base_unit_multiplier,
                                            'allow_decimal' => $UU->allow_decimal,
                                            'price'         => $var,
                                            'check_price'   => $buss->default_price_unit,
                                    ];
                if($pro->sub_unit_ids != null){
                    foreach($pro->sub_unit_ids  as $i){
                            $row_price    =  0;
                            $un           = \App\Unit::find($i);
                            $row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$product->product_id)->where("number_of_default",0)->first();
                            $row_price    = ($row_price)?$row_price->default_sell_price:0;
                            $allUnits[$i] = [
                                'name'          => $un->actual_name,
                                'multiplier'    => $un->base_unit_multiplier,
                                'allow_decimal' => $un->allow_decimal,
                                'price'         => $row_price,
                                'check_price'   => $buss->default_price_unit,
                            ] ;
                        }
                }
                $list_of_prices_in_unit = \App\Product::getProductPrices($pro_id);
            }
            $sub_units = $allUnits  ;

            if(app("request")->input("check")){
                return view('sell_return.partials.product_row')
                    ->with(compact('product','stores' ,'sub_units' ,'list_of_prices_in_unit' , 'currency' ,'row_index' , 'quantity' ,'row_count','tax_dropdown' ));
            }else{
                return view('purchase_return.partials.product_table_row')
                    ->with(compact('product','currency','sub_units' ,'list_of_prices_in_unit' ,'stores' ,'row_index' , 'quantity'));
            }
        }
    }
}
