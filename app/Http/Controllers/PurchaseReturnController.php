<?php

namespace App\Http\Controllers;

use App\AccountTransaction;
use App\PurchaseLine;
use App\Transaction;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\BusinessLocation;
use Spatie\Activitylog\Models\Activity;

class PurchaseReturnController extends Controller
{
    /** 
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('purchase_return.view') && !auth()->user()->can('purchase_return.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $is_admin    = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
        $business_id = request()->session()->get('user.business_id');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $orderStatuses = $this->productUtil->orderStatuses();
        if (request()->ajax()) {
            $purchases_returns = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS BS',
                        'transactions.location_id',
                        '=',
                        'BS.id'
                    )
                    ->leftJoin(
                        'transactions AS T',
                        'transactions.return_parent_id',
                        '=',
                        'T.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase_return')
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.business_id',
                        'transactions.ref_no',
                        'contacts.name',
                        'transactions.status',
                        'transactions.document',
                        'transactions.payment_status',
                        'transactions.final_total',
                        'transactions.return_parent_id',
                        'BS.name as location_name',
                        'T.ref_no as parent_purchase',
                        DB::raw('SUM(TP.amount) as amount_paid')
                    )
                    ->groupBy('transactions.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases_returns->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $purchases_returns->where('transactions.location_id', request()->location_id);
            }
            
            if (!empty(request()->supplier_id)) {
                $supplier_id = request()->supplier_id;
                $purchases_returns->where('contacts.id', $supplier_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end   =  request()->end_date;
                $purchases_returns->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }
            return Datatables::of($purchases_returns)
                ->addColumn('action', function ($row) use($is_admin){
                    $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                                        __("messages.actions") .
                                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    if (!empty($row->return_parent_id)) {
                        if($row->return_parent_id == $row->id){
                            if ($is_admin || auth()->user()->can('purchase_return.create') ) {
                            $html .= '<li><a href="' . action('CombinedPurchaseReturnController@edit', $row->id) . '" ><i class="glyphicon glyphicon-edit"></i>&nbsp;&nbsp;' .
                            __("messages.edit") .
                            '</a></li>';
                            }
                        }else{
                            $html .= '<li><a href="' . action('PurchaseReturnController@add', $row->return_parent_id) . '" ><i class="glyphicon glyphicon-edit"></i>&nbsp;&nbsp;' .
                                            __("messages.edit") .
                                            '</a></li>';
                        }
                    } else {
                        if ($is_admin || auth()->user()->can('purchase_return.create') ) {
                        $html .= '<li><a href="' . action('CombinedPurchaseReturnController@edit', $row->id) . '" ><i class="glyphicon glyphicon-edit"></i>&nbsp;&nbsp;' .
                                __("messages.edit") .
                                '</a></li>';
                        }
                    }
                    if (auth()->user()->can("purchase_return.view") ) {
                        $business_module = \App\Business::find($row->business_id);
                        $id_module       = (!empty($business_module))?(($business_module->return_purchase_print_module != null )? $business_module->return_purchase_print_module:null):null;
                        if(!empty($business_module)){
                            if($business_module->return_purchase_print_module != null && $business_module->return_purchase_print_module != "[]" ){
                                $all_pattern = json_decode($business_module->return_purchase_print_module);
                            }else{
                                $id_module = null;
                            }
                        }else{
                            $id_module = null ;
                        }
                        if($id_module != null){
                            $html .= '<li> <a href="'.\URL::to('reports/purchase/'.$row->id.'?return=1&ref_no='.$row->ref_no.'').'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>&nbsp;&nbsp;'. __("messages.print") .'</a>';
                            $html .= '<div style="border:3px solid #ee680e;background-color:#ee680e">';
                            foreach($all_pattern as $one_pattern){
                                $pat   = \App\Models\PrinterTemplate::find($one_pattern); 
                                if(!empty($pat)){
                                    $html .= '<a target="_blank" class="btn btn-info" style="width:100%;border-radius:0px;text-align:left;background-color:#474747 !important;color:#f7f7f7 !important;border:2px solid #ee680e !important" href="'. action("Report\PrinterSettingController@generatePdf",["id"=>$one_pattern,"sell_id"=>$row->id]) .'"> <i class="fas fa-print" style="color:#ee680e"  aria-hidden="true"></i> Print By <b style="color:#ee680e">'.$pat->name_template.'</b> </a>';
                                }
                            }
                            $html .= '</div>';
                            $html .= '</li>';
                        }else{
                            $html .= '<li><a href="'.\URL::to('reports/purchase/'.$row->id.'?return=1&ref_no='.$row->ref_no.'').'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>&nbsp;&nbsp;'. __("messages.print") .'</a></li>';
                        }
                    }
                    if ($row->payment_status != "paid") {
                        $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fas fa-money-bill-alt"></i>&nbsp;&nbsp;' . __("purchase.add_payment") . '</a></li>';
                    }
                    if($row->account_transactions->count() > 0){
                     $html .= '<li><a href="#" data-href="' .\URL::to('entry/transaction/'.$row->return_parent_id) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-align-justify" aria-hidden="true"></i>&nbsp;&nbsp;' . __("home.Entry") . '</a></li>';
                    }
                    $html  .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) . '" class="view_payment_modal"><i class="fas fa-money-bill-alt"></i>&nbsp;&nbsp;' . __("purchase.view_payments") . '</a></li>';
                    if($is_admin || auth()->user()->can('purchase.delete')){
                        if($row->id == $row->return_parent_id){
                            $html .= '<li><a href="' . action('PurchaseReturnController@destroy', [$row->id,"basic"=>"basic"]) . '" class="delete_purchase_return" ><i class="fa fa-trash"></i>&nbsp;&nbsp;' .
                                                __("messages.delete") .
                                                '</a></li>';
                        }else{
                            $html .= '<li><a href="' . action('PurchaseReturnController@destroy', $row->id) . '" class="delete_purchase_return" ><i class="fa fa-trash"></i>&nbsp;&nbsp;' .
                                                __("messages.delete") .
                                                '</a></li>';
                        }
                    }
                    $html .= '<li><a href="#" data-href="' . action('HomeController@formAttach', ["type" => "purchase_return","id" => $row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-paperclip" aria-hidden="true"></i>&nbsp;&nbsp;' . __("Add Attachment") . '</a></li>';
                    
                    $html .= '</ul></div>';
                    
                    return $html;
                })
                ->removeColumn('id')
                ->removeColumn('return_parent_id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
               
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="@if($payment_status != "paid"){{__(\'lang_v1.\' . $payment_status)}}@else{{__(\'lang_v1.\' . $payment_status)}}@endif"><span class="label @payment_status($payment_status)">@if($payment_status != "paid"){{__(\'lang_v1.\' . $payment_status)}} @else {{__(\'lang_v1.\' . $payment_status)}} @endif
                        </span></a>'
                )
                ->editColumn('parent_purchase', function ($row) {
                    $html = '';
                    if (!empty($row->parent_purchase)) {
                        $html = '<a href="#" data-href="' . action('PurchaseController@show', [$row->return_parent_id]) . '" class="btn-modal font_number" data-container=".view_modal">' . $row->parent_purchase . '</a>';
                    }
                    return $html;
                })
                ->editColumn('ref_no', function ($row) {
                    if(!empty($row->document) && $row->document != "[]"){
                         
                        $attach = "<br>
                                    <a class='btn-modal' data-href='".\URL::to('sells/attachment/'.$row->id)."' data-container='.view_modal'>
                                            <i class='fas fa-paperclip'></i>
                                                                                    
                                        </a>
                                    ";
                    }else{
                        $attach = "";
                    }
                    $invoice_no = $row->ref_no .  $attach;
                    return $invoice_no;
                })
                ->addColumn('status',function($row){
                    $product_list = [];
                    $purchase = \App\PurchaseLine::where("transaction_id",$row->id)->get();
                    foreach($purchase as $it){
                        $product_list[] = $it->product_id;
                    }
                    $Purchaseline     = \App\PurchaseLine::where("transaction_id",$row->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                    $RecievedPrevious = \App\Models\RecievedPrevious::where("transaction_id",$row->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                    $wrong            = \App\Models\RecievedWrong::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                    $type_return      = ($row->id == $row->return_parent_id)?"equal":"not_equal";
                    if($Purchaseline == $RecievedPrevious){
                        $state   = "received";
                    }else{
                        if($Purchaseline  != $RecievedPrevious){
                            if($row->status  == "received"){
                                $state   = "final";
                            }else{
                                $state   = $row->status;
                            }        
                        }
                        
                    }
                    return (string) view('sell.partials.bill_status', ['state' => $state, 'id' => $row->id , "RecievedPrevious"=>$RecievedPrevious, "wrong" => $wrong, "type_return" => $type_return  ]);
                })
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</sapn>';
                })
                ->addColumn('received_status', function ($row) {
                    $transaction_recieved = \App\Models\TransactionRecieved::where("transaction_id",$row->id)->where("is_returned",1)->first(); 
                    if($row->return_parent_id == null){
                        $tr                   = \App\Transaction::where("id",$row->id)->first();
                    }else{
                        $tr                   = \App\Transaction::where("id",$row->return_parent_id)->first();
                    }
                    $Purchaseline         = \App\PurchaseLine::where("transaction_id",$tr->id)->whereNotNull("quantity_returned")->select(DB::raw("SUM(quantity_returned) as total"))->first()->total;
                    $RecievedPrevious     = \App\Models\RecievedPrevious::whereHas("transaction",function($query) use($row){
                                                                                    $query->where("id",$row->id);
                                                                            })->whereHas("TrRecieved",function($query){
                                                                                $query->where("is_returned",1);
                                                                            })->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                    $wrong                = \App\Models\RecievedWrong::whereHas("transaction",function($query) use($row){
                                                                    $query->where("id",$row->id);
                                                            })->whereHas("TrRecieved",function($query){
                                                                $query->where("is_returned",1);
                                                            })->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                    if($RecievedPrevious == null){
                        $state = $row->status;
                        $payment_status = "not_delivered";
                        return (string) view('sell.partials.delivered_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong ,  "state" => $state , "type" => "p_return" ]);
                    }else if($Purchaseline == $RecievedPrevious){
                        $state =$row->status;
                        $payment_status = "delivered";
                        return (string) view('sell.partials.delivered_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong , "state" => $state , "type" => "p_return" ]);
                    }else if( $RecievedPrevious < $Purchaseline && $RecievedPrevious != 0){
                        $state = $row->status;
                        $payment_status = "separate";
                        return (string) view('sell.partials.delivered_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong , "state" => $state , "type" => "p_return" ]);
                    }else if( $RecievedPrevious > $Purchaseline && $RecievedPrevious != 0 ){
                        $state = $row->status;
                        $payment_status = "wrong";
                        return (string) view('sell.partials.delivered_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong , "state" => $state , "type" => "p_return" ]);
                    }
                    

                 })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("purchase.view")) {
                            $return_id = !empty($row->return_parent_id) ? $row->return_parent_id : $row->id;
                            return  action('PurchaseReturnController@show', [$return_id]) ;
                        } else {
                            return '';
                        }
                    }])
                ->rawColumns(['final_total','ref_no' ,'received_status', 'status', 'action', 'payment_status', 'parent_purchase', 'payment_due'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('purchase_return.index')->with(compact('business_locations', 'orderStatuses',"currency_details"));
    }

    /**
     * Show the form for purchase return.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        if (!auth()->user()->can('purchase_return.create') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $purchase    = Transaction::where('business_id', $business_id)
                        ->where('type', 'purchase')
                        ->with(['purchase_lines', 'contact', 'tax', 'return_parent', 'purchase_lines.sub_unit', 'purchase_lines.product', 'purchase_lines.product.unit'])
                        ->find($id);



        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }

        
        foreach ($purchase->purchase_lines as $key => $value) {
            $qty_available = $value->quantity - $value->quantity_sold - $value->quantity_adjusted;

            $purchase->purchase_lines[$key]->formatted_qty_available = $this->transactionUtil->num_f($qty_available);
        }
        $cost_centers  =  \App\Account::cost_centers();
        $return_transaction = Transaction::where('type', 'purchase_return')
                                            ->where('return_parent_id', $id)
                                            ->first();
        $currency     =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies   = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        return view('purchase_return.add')
                    ->with(compact('purchase','currencies','cost_centers','return_transaction'));
    }

    /**
     * Saves Purchase returns in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('purchase_return.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $business_id = request()->session()->get('user.business_id');

            $purchase = Transaction::where('business_id', $business_id)
                        ->where('type', 'purchase')
                        ->with(['purchase_lines', 'purchase_lines.sub_unit'])
                        ->findOrFail($request->input('transaction_id'));


            $return_quantities = $request->input('returns');

            $return_total = 0;
            
             
            
            DB::beginTransaction();
            
            $return_transaction = Transaction::where('business_id', $business_id)
                                            ->where('type', 'purchase_return')
                                            ->where('return_parent_id', $purchase->id)
                                            ->first();
            if (!empty($return_transaction)) {
                    $return_transaction_before = $return_transaction->replicate();
                    $archive        =  \App\Models\ArchiveTransaction::save_parent($return_transaction,"edit");
                    $purchase_lines = \App\PurchaseLine::where("transaction_id",$return_transaction->id)->get();
                
                    foreach($purchase_lines as $it){
                        \App\Models\ArchivePurchaseLine::save_purchases( $archive , $it);
                    }
                    $return_transaction_data['business_id'] = $business_id;
                    $return_transaction_data['location_id'] = $purchase->location_id;
                    $return_transaction_data['type'] = 'purchase_return';
                    $return_transaction_data['status'] = 'final';
                    $return_transaction_data['contact_id'] = $purchase->contact_id;
                    $return_transaction_data['transaction_date'] = \Carbon::now();
                    $return_transaction_data['created_by'] = request()->session()->get('user.id');
                    $return_transaction_data['discount_type'] = $request->discount_type;
                    $return_transaction_data['discount_amount'] = ($request->last_dis)?$request->last_dis:$request->last_discount;
                    $return_transaction_data['tax_id'] = $purchase->tax_id;
                    $return_transaction_data['store'] = $purchase->store;
                    $return_transaction_data['tax_amount'] = $request->tax_amount;
                    $return_transaction_data['final_total'] = $request->total_rt_purchase;
                    $return_transaction_data['currency_id'] = $request->currency_id;
                    $return_transaction_data['exchange_price'] = ($request->currency_id != null)?$request->currency_id_amount:null;
                    $return_transaction_data['amount_in_currency'] = ($request->currency_id != null)?(($request->currency_id_amount > 0)?round($request->total_rt_purchase/$request->currency_id_amount,2):0):null;
                    $return_transaction_data['return_parent_id'] = $purchase->id;
                    $return_transaction_data['cost_center_id'] =$request->cost_center_id;
                    $return_transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents'); 
                    $return_transaction->update($return_transaction_data);
                    
                    $this->transactionUtil->activityLog($return_transaction, 'edited', $return_transaction_before);
            } else {
                    $return_transaction_data['business_id'] = $business_id;
                    $return_transaction_data['location_id'] = $purchase->location_id;
                    $return_transaction_data['store'] = $purchase->store;
                    $return_transaction_data['type'] = 'purchase_return';
                    $return_transaction_data['status'] = 'final';
                    $type        = 'purchase_return';
                    $ref_count   =  $this->productUtil->setAndGetReferenceCount($type);
                    $reciept_no  = $this->productUtil->generateReferenceNumber($type, $ref_count);
                    $return_transaction_data['ref_no'] = $reciept_no;
                    $return_transaction_data['contact_id'] = $purchase->contact_id;
                    $return_transaction_data['transaction_date'] = \Carbon::now();
                    $return_transaction_data['created_by'] = request()->session()->get('user.id');
                    $return_transaction_data['discount_type'] = $request->discount_type;
                    $return_transaction_data['discount_amount'] = ($request->last_dis)?$request->last_dis:$request->last_discount;
                    $return_transaction_data['return_parent_id'] = $purchase->id;
                    $return_transaction_data['tax_id'] = $purchase->tax_id;
                    $return_transaction_data['tax_amount'] = $request->tax_amount;
                    $return_transaction_data['final_total'] = $request->total_rt_purchase;
                    $return_transaction_data['cost_center_id'] =$request->cost_center_id;
                    $return_transaction_data['currency_id'] = $request->currency_id;
                    $return_transaction_data['exchange_price'] = ($request->currency_id != null)?$request->currency_id_amount:null;
                    $return_transaction_data['amount_in_currency'] = ($request->currency_id != null)?(($request->currency_id_amount > 0)?round($request->total_rt_purchase/$request->currency_id_amount,2):0):null;
                    
                    $return_transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents'); 
                    $return_transaction = Transaction::create($return_transaction_data);
                    $this->transactionUtil->activityLog($return_transaction, 'added');
                    $archive = \App\Models\ArchiveTransaction::save_parent($return_transaction,"create");
            }
            $type=0;
            foreach ($purchase->purchase_lines as  $purchase_line) {
                
                $old_return_qty  = $purchase_line->quantity_returned;

                $return_quantity = !empty($return_quantities[$purchase_line->id]) ? $this->productUtil->num_uf($return_quantities[$purchase_line->id]) : 0;

                $multiplier = 1;
                if (!empty($purchase_line->sub_unit->base_unit_multiplier)) {
                    $multiplier = $purchase_line->sub_unit->base_unit_multiplier;
                    $return_quantity = $return_quantity * $multiplier;
                }
                $purchase_line->quantity_returned = $return_quantity;
                    
                /**
                 *.... here don't update returned quantity  
                 * ... just in recieved page ............
                 */

                $pr = \App\PurchaseLine::find($purchase_line->id);
                $pr->quantity_returned     = $return_quantity;
                $pr->bill_return_price     = $request->products[$purchase_line->id]["unit_price_"];
                $pr->save();

                $return_total += $request->products[$purchase_line->id]["unit_price_"] * $purchase_line->quantity_returned;

                /**
                 *.... don't return quantity to store ..  
                 * ..................................
                 */
                //Decrease quantity in variation location details
                // if ($old_return_qty != $purchase_line->quantity_returned) {
                //     $this->productUtil->decreaseProductQuantity(
                //         $purchase_line->product_id,
                //         $purchase_line->variation_id,
                //         $purchase->location_id,
                //         $purchase_line->quantity_returned,
                //         $old_return_qty
                //     );
                //     $diff = $purchase_line->quantity_returned - $old_return_qty;

                //     \App\Models\WarehouseInfo::update_stoct($purchase_line->product_id,$purchase_line->store_id,($diff*-1),$business_id);
                //     \App\MovementWarehouse::return_purchase($purchase_line->transaction,$purchase_line->product,($diff*-1),$purchase_line->store_id,$purchase_line);
                //     //effect account 
                //     //end_effect
                // }
 
            }
            $purchase_lines = \App\PurchaseLine::where("transaction_id",$return_transaction->id)->get();
            foreach($purchase_lines as $it){
                \App\Models\ArchivePurchaseLine::save_purchases($archive , $it);
            }
            \App\AccountTransaction::return_purchase($purchase,$request->last_discount,$request->total_rt_purchase,$request->sub_total_rt_purchase,$request->tax_amount);
            //.. expense .. 
            $type="PReturn";
            \App\Models\Entry::create_entries($return_transaction,$type,null,null,null,null,$return_transaction->id);
            $return_total_inc_tax = $return_total + $request->input('tax_amount');
        
            \App\Models\StatusLive::insert_data_rp($business_id,$purchase,"Return Purchase",$return_total_inc_tax );

            $return_transaction_data = [
                'total_before_tax' => $return_total,
                'final_total'      => $return_total_inc_tax,
                'tax_amount'       => $request->input('tax_amount'),
                'tax_id'           => $purchase->tax_id,
                'cost_center_id'   => $request->input('cost_center_id')
            ];

            if (empty($request->input('ref_no'))) {
                //Update reference count
                $ref_count = $this->transactionUtil->setAndGetReferenceCount('purchase_return');
                $return_transaction_data['ref_no'] = $this->transactionUtil->generateReferenceNumber('purchase_return', $ref_count);
            }
            
        //    \App\Models\ItemMove::return_purchase($return_transaction);
            //update payment status
            $this->transactionUtil->updatePaymentStatus($return_transaction->id, $return_transaction->final_total);
           
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('purchase_return.view')  ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $purchase    = Transaction::where('business_id', $business_id)
                        ->with(['return_parent', 'return_parent.tax', 'purchase_lines', 'contact', 'tax', 'purchase_lines.sub_unit', 'purchase_lines.product', 'purchase_lines.product.unit'])
                        ->find($id);
        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formatted_purchase_line        = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formatted_purchase_line;
            }
        }
        $purchase_taxes = [];
        if (!empty($purchase->return_parent->tax)) {
            if ($purchase->return_parent->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->return_parent->tax, $purchase->return_parent->tax_amount));
            } else {
                $purchase_taxes[$purchase->return_parent->tax->name] = $purchase->return_parent->tax_amount;
            }
        }
        //For combined purchase return return_parent is empty
        if (empty($purchase->return_parent) && !empty($purchase->tax)) {
            if ($purchase->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
            } else {
                $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
            }
        }
        $activities = Activity::forSubject($purchase->return_parent)->with(['causer', 'subject'])->latest()->get();
        return view('purchase_return.show')->with(compact('purchase', 'purchase_taxes', 'activities'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }
  
        try {
            if (request()->ajax()) {
                # BUSINESS INFO 
                $business_id = request()->session()->get('user.business_id');
                # SEARCH IF THERE IS TRANSACTION RETURNED 
                $purchase_return = Transaction::where('id', $id)->where('business_id', $business_id)->where('type', 'purchase_return')->with(['purchase_lines'])->first();
                # BEGIN 
                DB::beginTransaction();
                # Check if received exist then not allowed
                $receive = \App\Models\RecievedPrevious::where("transaction_id",$id)->where("is_returned",1)->first();
                if (!empty($receive)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.sorry_there_is_recieve')
                    ];
                    return $output;
                }
                $delete_purchase_lines = PurchaseLine::where('transaction_id', $purchase_return->id)->get();
                if(app('request')->input("basic")){
                    foreach ($delete_purchase_lines as $purchase_line) {
                        $delete_purchase_line_ids = [];
                        $delete_purchase_lines    = $purchase_return->purchase_lines;
                        PurchaseLine::where('transaction_id', $purchase_return->id)->whereIn('id', $delete_purchase_line_ids)->delete();
                    }
                    $shipping = \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$purchase_return){
                                            $query->where("transaction_id",$purchase_return->id);
                                    })->first();
                    if(!empty($shipping)){
                        $ship_main = \App\Models\AdditionalShipping::find($shipping->additional_shipping_id);
                        $ship_item = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$ship_main->id)->get();
                        foreach($ship_item as $it){
                            $it->delete();
                        }
                        $ship_main->delete();
                    }
                }
                # CHECK IF PURCHASE RETURN NOT EMPTY  
                if (empty($purchase_return->return_parent_id)) {
                    foreach ($delete_purchase_lines as $purchase_line) {
                        $delete_purchase_lines    = $purchase_return->purchase_lines;
                        $delete_purchase_line_ids = [];
                        PurchaseLine::where('transaction_id', $purchase_return->id)->whereIn('id', $delete_purchase_line_ids)->delete();
                    }
                } else {
                    if(app('request')->input("basic")){
                        $parent_purchase = Transaction::where('id', $purchase_return->id)->where('business_id', $business_id)->where('type', 'purchase_return')->with(['purchase_lines'])->first();
                    }else{
                        $parent_purchase = Transaction::where('id', $purchase_return->return_parent_id)->where('business_id', $business_id)->where('type', 'purchase')->with(['purchase_lines'])->first();
                    }
                    $updated_purchase_lines = $parent_purchase->purchase_lines;
                    foreach ($updated_purchase_lines as $purchase_line) {
                        $this->productUtil->updateProductQuantity($parent_purchase->location_id, $purchase_line->product_id, $purchase_line->variation_id, $purchase_line->quantity_returned, 0, null, false);
                        $purchase_line->quantity_returned = 0;
                        $purchase_line->save();
                    }
                }
                if(!empty($purchase_return)){
                    $all = \App\AccountTransaction::where("return_transaction_id",$purchase_return->id);
                    if(app('request')->input("basic")){
                        $all = $all->whereNotNull('return_transaction_id');
                    }
                    $all = $all->get();
                    foreach($all as $it){
                        $account_transactions = \App\Account::find($it->account_id);
                        $actions_date         = $it->operation_date;
                        $it->delete();
                        if($account_transactions->cost_center!=1){
                            \App\AccountTransaction::nextRecords($account_transactions->id,$purchase_return->business_id,$actions_date);
                        }
                    }
                }
                # Delete Transaction
                $purchase_return->delete();
                DB::commit();
                $output = [
                    'success' => true,
                    'msg'     => __('lang_v1.deleted_success')
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                    'success' => false,
                    'msg'     => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                    // 'msg' => __('messages.something_went_wrong'),
                ];  
        }

        return $output;
    }

    public function store_return(Request $request)
    {
        if (!auth()->user()->can('purchase_return.create')) {
            abort(403, 'Unauthorized action.');
        }
        try{
           $business_id       = request()->session()->get('user.business_id');
           $transaction       = Transaction::find($request->input('transaction_id'));
           //.... Array Of Lines_id and Returned Item
           $return_quantities = $request->input('returns');
           $return_total      = 0;
           if(!empty($transaction)){
                $return_transaction = Transaction::where('business_id', $business_id)
                                                ->where('return_parent_id', $transaction->id)
                                                ->first();
                if(!empty($return_transaction)){
                    $previous_transaction  = $return_transaction->replicate();
                    $tr                    = Transaction::find($return_transaction->id);
                    $tr->business_id       = $business_id ;
                    $tr->location_id       = $transaction->location_id;
                    $tr->type              = 'purchase_return';
                    $tr->status            = 'final';
                    $tr->contact_id        = $transaction->contact_id;
                    $tr->transaction_date  = \Carbon::now();
                    $tr->created_by        = request()->session()->get('user.id');
                    $tr->return_parent_id  = $transaction->id;
                    $tr->cost_center_id    = $request->cost_center_id;
                    $tr->update();
                    $this->transactionUtil->activityLog($tr, 'edited', $previous_transaction);
                }else{
                    $tr                    = new Transaction;
                    $tr->business_id       = $business_id ;
                    $tr->location_id       = $transaction->location_id;
                    $tr->type              = 'purchase_return';
                    $tr->status            = 'final';
                    $tr->contact_id        = $transaction->contact_id;
                    $tr->transaction_date  = \Carbon::now();
                    $tr->created_by        = request()->session()->get('user.id');
                    $tr->return_parent_id  = $transaction->id;
                    $tr->cost_center_id    = $request->cost_center_id;
                    $tr->save();
                    $this->transactionUtil->activityLog($tr, 'added');

                }
           }
           $purchase = \App\PurchaseLine::where("transaction_id",$transaction->id)->get();
           foreach ($purchase as $it) {
         
            $old_return_qty   = $it->quantity_returned;
            $return_quantity  = !empty($return_quantities[$it->id]) ? $this->productUtil->num_uf($return_quantities[$it->id]) : 0;
            
            $return_total += $request->products[$it->id]["unit_price_"] * $return_quantity;
            if ($old_return_qty != $pr->quantity_returned) {
                $this->productUtil->decreaseProductQuantity(
                    $it->product_id,
                    $it->variation_id,
                    $transaction->location_id,
                    $return_quantity,
                    $old_return_qty
                );
                $diff = $return_quantity - $old_return_qty;
                \App\Models\WarehouseInfo::update_stoct($it->product_id,$it->store_id,($diff*-1),$business_id);
                \App\MovementWarehouse::return_purchase($it->transaction,$it->product,($diff*-1),$it->store_id,$it);
            }                
            // \App\AccountTransaction::return_purchase($it->id);

           }
           $return_total_inc_tax = $return_total + $request->input('tax_amount');
           \App\Models\StatusLive::insert_data_rp($business_id,$transaction,"Return Purchase",$return_total_inc_tax );

           $output = [
                    "success" => true,
                    "msg" => __("messages.added_successfull")
           ];
        }catch(\Exception $e){
           $output = [
                    "success" => false,
                    "msg" => __("messages.something_went_wrong")
           ];
        }
        return  redirect("/purchase-return")->with("status",$output) ;
    }
}
