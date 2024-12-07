<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Transaction;
use App\Contact;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;

use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\TransactionSellLine;
use App\Events\TransactionPaymentDeleted;
use App\Models\TransactionDelivery;
use App\Models\WarehouseInfo;
use App\MovementWarehouse;
use App\Models\DeliveredPrevious;
use Spatie\Activitylog\Models\Activity;

class SellReturnController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $contactUtil;
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ContactUtil $contactUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil     = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->contactUtil     = $contactUtil;
        $this->businessUtil    = $businessUtil;
        $this->moduleUtil      = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('access_sell_return') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

                    ->join(
                        'business_locations AS bl',
                        'transactions.location_id',
                        '=',
                        'bl.id'
                    )
                    ->join(
                        'transactions as T1',
                        'transactions.return_parent_id',
                        '=',
                        'T1.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_return')
                    ->whereIn('transactions.status', ['final','delivered'])
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.business_id',
                        'transactions.status',
                        'transactions.invoice_no',
                        'transactions.document',
                        'transactions.ref_no',
                        'transactions.return_parent_id',
                        'contacts.name',
                        'transactions.final_total',
                        'transactions.payment_status',
                        'bl.name as business_location',
                        'T1.invoice_no as parent_sale',
                        'T1.id as parent_sale_id',
                        DB::raw('SUM(TP.amount) as amount_paid')
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end   =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                        ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $sells->groupBy('transactions.id');
           
            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    function($row){
                        $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                        $html .= '<li><a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'SellReturnController@show\', [$row->parent_sale_id])}}"><i class="fas fa-eye" aria-hidden="true"></i> '. __("messages.view") .'</a></li>';
                        
                        if( $row->return_parent_id == $row->id){
                            $html .=  '<li><a href="'. action("SellReturnController@edit", [$row->id]) .'" ><i class="fa fa-edit" aria-hidden="true"></i>'. __("messages.edit") .'</a></li>';
                        }else{
                            $html .= '<li><a href="'. action("SellReturnController@add", [$row->parent_sale_id]).'" ><i class="fa fa-edit" aria-hidden="true"></i>'. __("messages.edit") .'</a></li>';
                        }
                        if(request()->session()->get("user.id") == 1){
                            $html .= '<li><a href="'. action("SellReturnController@destroy", [$row->id]).'" class="delete_sell_return" ><i class="fa fa-trash" aria-hidden="true"></i>'. __("messages.delete") .'</a></li>';
                        }
                        
                          
                            $business_module = \App\Business::find($row->business_id);
                            $id_module       = (!empty($business_module))?(($business_module->return_sale_print_module != null )? $business_module->return_sale_print_module:null):null;
                             
                            if(!empty($business_module)){
                                if($business_module->return_sale_print_module != null && $business_module->return_sale_print_module != "[]" ){ 
                                    $all_pattern = json_decode($business_module->return_sale_print_module); 
                                }else{
                                    $id_module   = null ; $all_pattern = []; 
                                    
                                }
                            }else{
                                $id_module = null ; $all_pattern = []; 
                            } 

                            if($id_module != null){
                                $html .= '<li><a href="#" class="print-invoice" data-href="{{action(\'SellReturnController@printInvoice\', [$row->id])}}"><i class="fa fa-print" aria-hidden="true"></i>'. __("messages.print") .'</a></li>';
                             
                                $html .= '<li><div style="border:3px solid #ee680e;background-color:#ee680e">';
                                    foreach($all_pattern as $one_pattern){ 
                                        $pat   = \App\Models\PrinterTemplate::find($one_pattern); 
                                        if(!empty($pat)){
                                            $html .= ' <a target="_blank" class="btn btn-info" style="width:100%;border-radius:0px;text-align:left;background-color:#474747 !important;color:#f7f7f7 !important;border:2px solid #ee680e !important" href="'.action("Report\PrinterSettingController@generatePdf",["id" => $one_pattern,"sell_id" => $row->id]).'"> <i class="fas fa-print" style="color:#ee680e"  aria-hidden="true"></i> Print By '; 
                                            $html .= '<b style="color:#ee680e"> ' . $pat->name_template . '</b> </a>';
                                        } 
                                    }  
                                $html .= '</div></li>';
                                
                            }else{
                                $html .= '<li><a href="'. action("Report\SellController@index",[$row->id ,"return"=>"1" , "ref_no" => $row->invoice_no ]  ) .'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i> '. __("messages.print").'</a></li>';  
                            }

                            
                            $html .= '<li><a href="#" class="btn-modal" data-container=".view_modal"  data-href="'. action("General\AccountController@transaction", [$row->parent_sale_id]). '"><i class="fa fa-align-justify" aria-hidden="true"></i> '. __("home.Entry") .'</a></li>';
                            
                            
                            
                            
                        if( $row->payment_status != "paid"){
                            $html .= '<li><a href="'. action("TransactionPaymentController@addPayment", [$row->id]).'" class="add_payment_modal"><i class="fas fa-money-bill-alt"></i>  '. __("purchase.add_payment").'</a></li>';
                        }

                        $html .= '<li><a href="'. action("TransactionPaymentController@show", [$row->id]).'" class="view_payment_modal"><i class="fas fa-money-bill-alt"></i> '. __("purchase.view_payments") . '</a></li>';
                        $html .= '</ul>';
                        $html .= '</div>';
                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('parent_sale', function ($row) {
                    return '<button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="' . action('SellController@show', [$row->parent_sale_id]) . '">' . $row->parent_sale . '</button>';
                })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}</span></a>'
                )
                ->editColumn(
                   'status',function($row){
                    $sell_list       = [];
                    $transaction = \App\Transaction::find($row->id);
                    if($transaction->return_parent_id != null){
                        $sell          = \App\TransactionSellLine::where("transaction_id",$transaction->return_parent_id)->get();
                        
                    }else{
                        
                        $sell          = \App\TransactionSellLine::where("transaction_id",$row->id)->get();
                    }
                    foreach($sell as $it){
                        $sell_list[] = $it->product_id;
                    }
                    if($transaction->return_parent_id != null){
                        $Purchaseline     = \App\TransactionSellLine::where("transaction_id",$transaction->return_parent_id)->whereIn("product_id",$sell_list)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                        
                    }else{
                        
                        $Purchaseline     = \App\TransactionSellLine::where("transaction_id",$row->id)->whereIn("product_id",$sell_list)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                     }
                 
                    $RecievedPrevious = \App\Models\DeliveredPrevious::where("transaction_id",$row->id)->whereIn("product_id",$sell_list)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                    $wrong            = \App\Models\DeliveredWrong::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                    $type_return      = ($row->id == $row->return_parent_id)?"equal":"not_equal";
                    
                    if($RecievedPrevious == null){
                        $status           = "not_delivereds" ;
                    }elseif($Purchaseline <= $RecievedPrevious){
                        $status           =  "delivereds";
                    }elseif($Purchaseline > $RecievedPrevious){
                        $status           =  "separates";
                    }else{
                        $status           =  "final";
                    }
                    
                    return (string) view('sell.partials.sell_bill_status', ['state' => $status, 'id' => $row->id , "RecievedPrevious"=>$RecievedPrevious, "wrong" => $wrong, "type_return" => $type_return ]);
                
                })
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</sapn>';
                })
                ->editColumn('invoice_no', function ($row) {
                    if(!empty($row->document) && $row->document != "[]"){
                         
                        $attach = "<br>
                                    <a class='btn-modal' data-href='".\URL::to('sells/attachment/'.$row->id)."' data-container='.view_modal'>
                                            <i class='fas fa-paperclip'></i>
                                                                                    
                                        </a>
                                    ";
                    }else{
                        $attach = "";
                    }
                    $invoice_no = $row->invoice_no ;
                    return $invoice_no . $attach;
                })
                ->addColumn(
                    'delivery_status', function ($row)  {
                        $tr = \App\Transaction::where("id",$row->parent_sale_id)->first(); 
                        $product_list = [];
                        $sell = \App\TransactionSellLine::where("transaction_id",$tr->id)->get();
                        foreach($sell as $it){
                            $product_list[] = $it->product_id;
                        }
                        $TransactionSellLine   = \App\TransactionSellLine::where("transaction_id",$tr->id)->whereIn("product_id",$product_list)->whereNotNull("quantity_returned")->select(DB::raw("SUM(quantity_returned) as total"))->first()->total;
                        
                        $DeliveredPrevious     = \App\Models\DeliveredPrevious::whereHas("transaction",function($query) use($row,$product_list){
                                                                        $query->where("id",$row->parent_sale_id);
                                                                        $query->orWhere("id",$row->id);
                                                                        $query->whereIn("product_id",$product_list);
                                                                        })->whereHas("T_delivered",function($query){
                                                                            $query->where("is_returned",1);
                                                                        })
                                                                        ->select(DB::raw("SUM(current_qty) as total"))
                                                                        ->first()->total;
                        $wrong                 = \App\Models\DeliveredWrong::whereHas("transaction",function($query) use($row){
                                                                    $query->where("id",$row->parent_sale_id);
                                                                    $query->orWhere("id",$row->id);
                                                                    })->whereHas("T_delivered",function($query){
                                                                        $query->where("is_returned",1);
                                                                    })
                                                                    ->select(DB::raw("SUM(current_qty) as total"))
                                                                    ->first()->total;
                        
                          
                        if($DeliveredPrevious == null){
                            $payment_status = "not_delivereds";
                            return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong , "type" => "s_return" ,"approved"=> false]);
                             
                        }else if($TransactionSellLine <= $DeliveredPrevious){
                            $payment_status = "delivereds";
                             
                            return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id , "wrong" => $wrong , "type" => "s_return" ,"approved"=> false ]);
                        
                        } else if( $DeliveredPrevious < $TransactionSellLine ){
    
                            $payment_status = "separates";
                            
                            return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong , "type" => "s_return" ,"approved"=> false]);
    
                            
                        }
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellReturnController@show', [$row->parent_sale_id]) ;
                        } else {
                            return '';
                        }
                    }])
                ->rawColumns(['final_total','delivery_status','invoice_no', 'status', 'action', 'parent_sale', 'payment_status', 'payment_due','action'])
                ->make(true);
        }
        $business_locations   = BusinessLocation::forDropdown($business_id, false);
        $customers            = Contact::customersDropdown($business_id, false);
        $sales_representative = User::forDropdown($business_id, false, false, true);

        return view('sell_return.index')->with(compact("currency_details",'business_locations', 'customers', 'sales_representative'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
                if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                    return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
                } 
            }

        $business_locations   = BusinessLocation::forDropdown($business_id);
        //$walk_in_customer   = $this->contactUtil->getWalkInCustomer($business_id);
        $currency_details     = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $mainstore_categories = \App\Models\Warehouse::childs($business_id);
        $cost_centers         = \App\Account::cost_centers();
        $taxes                = \App\TaxRate::where('business_id', $business_id)
                                ->ExcludeForTaxGroup()
                                ->get();
        $users                = [] ;
        $us                   = User::where('business_id', $business_id)
                                  ->where('is_cmmsn_agnt', 1)->get();
        foreach($us as $it){
            $users[$it->id] = $it->first_name;
        }
        $patterns         = [];
        $patterns_        = \App\Models\Pattern::select()->get();
        foreach($patterns_ as $it){
                $patterns[$it->id] = $it->name;
        }
        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        $currency         =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies       = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
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
            $types['both']     = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = \App\CustomerGroup::forDropdown($business_id);
        return view('sell_return.create')
            ->with(compact('business_locations','types','customer_groups','currencies','currency_details','list_of_prices','cost_centers','patterns','mainstore_categories','taxes','walk_in_customer','users'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('sell.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
                if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                    return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
                } 
            }

        $business_locations = BusinessLocation::forDropdown($business_id);
        //$walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $mainstore_categories = \App\Models\Warehouse::childs($business_id);
        $cost_centers =  \App\Account::cost_centers();
        $transaction_return =  \App\Transaction::find($id);
        $transactionSell = \App\TransactionSellLine::where("transaction_id",$id)->get();
        $taxes = \App\TaxRate::where('business_id', $business_id)
                                ->ExcludeForTaxGroup()
                                ->get();
        $users  = [] ;
        $us     = User::where('business_id', $business_id)
                    ->where('is_cmmsn_agnt', 1)->get();
        foreach($us as $it){
            $users[$it->id] = $it->first_name;
        }
        $patterns  = [];
        $patterns_ = \App\Models\Pattern::select()->get();
        foreach($patterns_ as $it){
                $patterns[$it->id] = $it->name;
        }
        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        $Purchaseline     = \App\TransactionSellLine::where("transaction_id",$id)->select(DB::raw("SUM(quantity) as total"))->first()->total;
        $RecievedPrevious = \App\Models\DeliveredPrevious::where("transaction_id",$id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
        $wrong            = \App\Models\DeliveredWrong::where("transaction_id",$id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
        
        if($Purchaseline == $RecievedPrevious){
            $statuss           =  "delivered";
        }else{
            $statuss           =  "final";
        }
   
        $currency     =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies   = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $row                  = 1;$line_prices  = [];#2024-8-6
        $list_of_prices       = \App\Product::getListPrices($row);
        return view('sell_return.edit')
            ->with(compact('business_locations','currencies','RecievedPrevious','list_of_prices' ,'currency_details','statuss','transactionSell','transaction_return','cost_centers','patterns','mainstore_categories','taxes','walk_in_customer','users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        if (!auth()->user()->can('access_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
            } 
        }
        $sell = Transaction::where('business_id', $business_id)
                            ->with(['sell_lines', 'location', 'return_parent', 'contact', 'tax', 'sell_lines.sub_unit', 'sell_lines.product', 'sell_lines.product.unit'])
                            ->find($id);

        foreach ($sell->sell_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
            $sell->sell_lines[$key]->formatted_qty = $this->transactionUtil->num_f($value->quantity, false, null, true);
        }
        $currency     =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies   = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
         return view('sell_return.add')
            ->with(compact('sell','currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('access_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        # Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
             if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                 return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
             } 
        } 
        try {
            $input       = $request->except('_token');
            $user_id     = $request->session()->get('user.id');
            $business_id = $request->session()->get('user.business_id');
 
            DB::beginTransaction();
            $company_name      = request()->session()->get("user_main.domain");
            if (!empty($input['products'])) {
                $document_expense   = [];$idxx = 1;
                $document_sell      = [];$idx  = 1;
                $company_name      = request()->session()->get("user_main.domain");
                #........................................
                if ($request->hasFile('document_sell')) {
                    foreach ($request->file('document_sell') as $file) {
                        $file_name =  'uploads/companies/'.$company_name.'/documents/sale_return/'.time().'_'.$idx++.'.'.$file->getClientOriginalExtension();
                        $file->move('uploads/companies/'.$company_name.'/documents/sale_return',$file_name);
                        array_push($document_sell,$file_name);
                    }
                } 
                $input['line_type'] = 'return_sell';
                $input['document']  =  json_encode($document_sell);
                $sell_return        =  $this->transactionUtil->addSellReturn($input, $business_id, $user_id);
                $receipt            =  $this->receiptContent($business_id, $sell_return->location_id, $sell_return->id);
                # .................. service item
                    $sellLine    = \App\TransactionSellLine::where("transaction_id",$sell_return->return_parent_id)->get();
                    $service_lines = [] ;
                    foreach($sellLine as $it){
                        if($it->product->enable_stock == 0){   
                            $service_lines[]=$it;                             
                        }
                    }
                    if(count($service_lines)>0){
                            $previous_tr  = \App\Models\TransactionDelivery::where("transaction_id",$sell_return->id)->where("status",'Service Item')->first();
                            $id_td = ($previous_tr)?$previous_tr->id:null;
                            if(!empty($previous_tr)){
                                foreach($service_lines as $it){
                                        
                                        $prev                  =  \App\Models\DeliveredPrevious::where("transaction_recieveds_id",$id_td)->where("line_id",$it->id)->first();
                                        $margin                =  $it->quantity - $prev->current_qty ;
                                        $prev->store_id        =  $it->store_id;
                                        $prev->total_qty       =  $it->quantity;
                                        $prev->current_qty     =  $it->quantity;
                                        if($margin == 0){
                                            $qty = 0;
                                        }elseif($margin <  0){
                                            $qty = $margin;
                                        }else{
                                            $qty = $margin*-1;
                                            
                                        }
                                        $prev->update();
                                        \App\Models\WarehouseInfo::update_stoct($it->product->id,$it->store_id,$qty,$it->transaction->business_id);
                                        \App\MovementWarehouse::movemnet_warehouse($sell_return,$it->product,$it->quantity,$it->store_id,$it,"plus",$id_td);
        
                                }
                            }else{
                                $type                         =  'trans_delivery';
                                $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
                                $receipt_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                                $tr_received                  =  new TransactionDelivery;
                                $tr_received->store_id        =  $sell_return->store;
                                $tr_received->transaction_id  =  $sell_return->id;
                                $tr_received->business_id     =  $sell_return->business_id ;
                                $tr_received->reciept_no      =  $receipt_no ;
                                $tr_received->invoice_no      =  $sell_return->invoice_no;
                                //$tr_received->ref_no        =  $data->ref_no;
                                $tr_received->date            =  $sell_return->transaction_date;
                                $tr_received->is_returned     =  1;
                                $tr_received->status          = 'Service Item';
                                $tr_received->save();
                            
                                foreach($service_lines as $it){
                                    
                                    $prev                             =  new DeliveredPrevious;
                                    $prev->product_id                 =  $it->product_id;
                                    $prev->store_id                   =  $it->store_id;
                                    $prev->business_id                =  $it->transaction->business_id ;
                                    $prev->transaction_id             =  $it->transaction->id;
                                    $prev->unit_id                    =  $it->product->unit->id;
                                    $prev->total_qty                  =  $it->quantity;
                                    $prev->current_qty                =  $it->quantity;
                                    $prev->remain_qty                 =  0;
                                    $prev->transaction_recieveds_id   =  $tr_received->id;
                                    $prev->product_name               =  $it->product->name;
                                    $prev->is_returned                =  1;
                                    $prev->line_id                    =  $it->id;
                                    
                                    $prev->save();
                                
                                    \App\Models\WarehouseInfo::update_stoct($it->product->id,$it->store_id,$it->quantity,$it->transaction->business_id);
                                    \App\MovementWarehouse::movemnet_warehouse($sell_return,$it->product,$it->quantity,$it->store_id,$it,"plus",$tr_received->id);
                                    
                                }
                            }
                            
                    }
                //  .................
                \App\Models\ItemMove::return_sale_delivery($sell_return,1);
                // \App\Models\ItemMove::return_sale($sell_return);
                DB::commit();

                $output = [
                            'success' => 1,
                            'msg'     => __('lang_v1.success'),
                            'receipt' => $receipt
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // $msg = "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage();
            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $msg = __('messages.something_went_wrong');
            }

            $output = [
                            'success' => 0,
                            'msg'     => $msg
                        ];
        }

        return $output;
     }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('access_sell_return') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $sell = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'contact',
                                    'return_parent',
                                    'tax',
                                    'sell_lines',
                                    'sell_lines.product',
                                    'sell_lines.variations',
                                    'sell_lines.sub_unit',
                                    'sell_lines.product',
                                    'sell_lines.product.unit',
                                    'location'
                                )
                                ->first();

        foreach ($sell->sell_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
        }

        $sell_taxes = [];
        if (!empty($sell->return_parent->tax)) {
            if ($sell->return_parent->tax->is_tax_group) {
                $sell_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->return_parent->tax, $sell->return_parent->tax_amount));
            } else {
                $sell_taxes[$sell->return_parent->tax->name] = $sell->return_parent->tax_amount;
            }
        }
        $total_discount = 0;
        if ($sell->return_parent->discount_type == 'fixed_before_vat') {
            $total_discount   = $sell->return_parent->discount_amount;
        } elseif ($sell->return_parent->discount_type == 'fixed_after_vat') {
            $total_discount   = ($sell->return_parent->discount_amount * 100 )/( 100 + (($sell->return_parent->tax)?$sell->return_parent->tax->amount:0)) ;
        }elseif ($sell->return_parent->discount_type == 'percentage') {
            $discount_percent = $sell->return_parent->discount_amount;
            if ($discount_percent == 100) {
                $total_discount = $sell->return_parent->total_before_tax;
            } else {
                $total_after_discount  = $sell->return_parent->final_total - $sell->return_parent->tax_amount;
                $total_before_discount = $total_after_discount * 100 / (100 - $discount_percent);
                $total_discount        = $total_before_discount - $total_after_discount;
            }
        }

        $activities = Activity::forSubject($sell->return_parent)
                                ->with(['causer', 'subject'])
                                ->latest()
                                ->get();



        return view('sell_return.show')
            ->with(compact('sell', 'sell_taxes', 'total_discount', 'activities'));
    }

    /**
     * 
     * 
     */
    public function save_return(Request $request)
    {
       
        try {
            DB::beginTransaction();
        
            $input_data = $request->only([ 
                'location_id', 'transaction_date', 'total_final_input','status','total_return_input','currency_id','currency_id_amount',
                'ref_no','cost_center_id','store_id','discount_amount2','discount_amount','discount_type',
                'tax_id', 'tax_amount2', 'contact_id','sup_ref_no',  'agent_id','project_no','pattern_id'
                ]);
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                    return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
                } 
            } 
            
            $user_id                        = $request->session()->get('user.id');
            $input_data['store']            = $input_data['store_id'];
            $input_data['type']             = 'sell_return';
            $input_data['tax_amount']       = $input_data['tax_amount2'];
            $input_data['business_id']      = $business_id;
            $input_data['created_by']       = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['total_before_tax'] = $input_data['total_final_input'] - $input_data['tax_amount2'];
            $input_data['sup_refe']         = $input_data['sup_ref_no'];
            $input_data['final_total']      = $input_data['total_final_input'];
            $input_data['ship_amount']      = 0;
            $input_data['payment_status']   = 2;
            $input_data['currency_id']      = $input_data['currency_id'];
            $input_data['exchange_price']   = ($input_data['currency_id']!= null)?$input_data['currency_id_amount']:1;
            $input_data['amount_in_currency']   = ($input_data['currency_id']!= null)?($input_data['total_final_input'] / $input_data['currency_id_amount']):null;
            // $input_data['discount_amount']  = $input_data['discount_amount2'];

            $input_data['discount_amount']  = $input_data['discount_amount'];
           
            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('sell_return');
            //Generate reference number
            if (empty($input_data['ref_no'])) {
                $PATTERN = \App\Models\Pattern::where("id",$request->pattern_id)->first();
                if(!empty($PATTERN)){
                        $invoice = $PATTERN->invoice_scheme;
                        $PATTERN_INVOICE = \App\InvoiceScheme::find($invoice)->prefix;
                }else{
                        $PATTERN_INVOICE = "";
                }
                $input_data['invoice_no'] = $PATTERN_INVOICE . $this->productUtil->generateReferenceNumber('sell_return', $ref_count);
            }
            $document_sell = [];
            $company_name      = request()->session()->get("user_main.domain");
                if ($request->hasFile('document_sell')) {
                    $id_return = 1;
                    foreach ($request->file('document_sell') as $file) {
                        $file_name =  'uploads/companies/'.$company_name.'/documents/sale_return/'.time()."_".$id_return++.'.'.$file->getClientOriginalExtension();
                        $file->move('uploads/companies/'.$company_name.'/documents/sale_return',$file_name);
                        array_push($document_sell,$file_name);
                    }
                } 

                
                
            $input_data['document'] = json_encode($document_sell);
             
            $sub_total_rt_purchase = 0;

            if(!empty($request->products)){
                $product_data = [];
                $sell_return = Transaction::create($input_data);
                $sell_return->return_parent_id = $sell_return->id;
                $sell_return->save();

                foreach ($request->products as $key=>$product) {
                        $unit_price                      = $this->productUtil->num_uf($product['unit_price_before_dis_exc']);
                        $unit_price_after_dis_exc        = $this->productUtil->num_uf($product['unit_price_after_dis_exc']);
                        $unit_price_after_dis_inc        = $this->productUtil->num_uf($product['unit_price_after_dis_inc']);
                        $return_line = [
                            'product_id'                 => $product['product_id'],
                            'store_id'                   => $input_data['store_id'],
                            'variation_id'               => $product['variation_id'],
                            'quantity'                   => $product['quantity'],
                            'unit_price_before_discount' => $unit_price,
                            'line_discount_type'         => 'percentage',
                            'line_discount_amount'       => $product['discount_percent_return'],
                            'unit_price'                 => $unit_price_after_dis_exc,
                            'unit_price_inc_tax'         => $unit_price_after_dis_inc,
                            'bill_return_price'          => $unit_price_after_dis_exc,
                            'quantity_returned'          => $this->productUtil->num_uf($product['quantity']),
                            'lot_number'                 => !empty($product['lot_number']) ? $product['lot_number'] : null,
                            'exp_date'                   => !empty($product['exp_date']) ? $this->productUtil->uf_date($product['exp_date']) : null
                        ];
                        $sub_total_rt_purchase   += ($product['quantity']*$unit_price_after_dis_exc) ;
                        $product_data[]           = $return_line;
                }
                    $sell_return->sell_lines()->createMany($product_data);
             }
             

            \App\AccountTransaction::return_sales($sell_return,$request->discount_amount2,$request->total_final_input,$sub_total_rt_purchase,$request->tax_amount2);

            if($request->status == "delivered"){

                $sells_lines = \App\TransactionSellLine::where("transaction_id",$sell_return->id)->get();
                foreach($sells_lines as $it){
                    $price  = \App\TransactionSellLine::orderby("id","desc")->where("product_id",$it->product_id)->first();
                    \App\Models\WarehouseInfo::update_stoct($it->product_id,$it->store_id,$it->quantity,$it->transaction->business_id);
                    
                    $prev                  =  new DeliveredPrevious;
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
                    // WarehouseInfo::update_stoct($data['product_id'],$transaction->store,$new_quantity_f,$transaction->business_id);
                    MovementWarehouse::movemnet_warehouse($it->transaction,$it->product,$it->quantity,$it->store_id,$price,'plus',$prev->id);
                }
                $type                         =  'trans_delivery';
                $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
                $receipt_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                $tr_received                  =  new TransactionDelivery;
                $tr_received->store_id        =  $sell_return->store;
                $tr_received->transaction_id  =  $sell_return->id;
                $tr_received->business_id     =  $sell_return->business_id ;
                $tr_received->reciept_no      =  $receipt_no ;
                $tr_received->invoice_no      =  $sell_return->invoice_no;
                $tr_received->is_returned     =  1;
                $tr_received->date            = ($request->date)?$request->date:\Carbon\Carbon::now();
                $tr_received->status          = 'Return  Sale';
            
                $tr_received->save();

                $prev_s = \App\Models\DeliveredPrevious::where("transaction_id",$sell_return->id)->get();
                foreach($prev_s as $pr){
                    $item_prv = \App\Models\DeliveredPrevious::find($pr->id); 
                    $item_prv->transaction_recieveds_id  = $tr_received->id;
                    $item_prv->update();
                }
 
            } else {
                $sellLine = \App\TransactionSellLine::where("transaction_id",$sell_return->id)->get();
                $service_lines = [] ;
                foreach($sellLine as $it){
                    if($it->product->enable_stock == 0){   
                        $service_lines[]=$it;                             
                    }
                }
                if(count($service_lines)>0){
                        $type                         =  'trans_delivery';
                        $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
                        $receipt_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                        $tr_received                  =  new TransactionDelivery;
                        $tr_received->store_id        =  $sell_return->store;
                        $tr_received->transaction_id  =  $sell_return->id;
                        $tr_received->business_id     =  $sell_return->business_id ;
                        $tr_received->reciept_no      =  $receipt_no ;
                        $tr_received->invoice_no      =  $sell_return->invoice_no;
                        //$tr_received->ref_no        =  $data->ref_no;
                        $tr_received->date            =  $sell_return->transaction_date;
                        $tr_received->is_returned     =  1; 
                        $tr_received->status          = 'Service Item';
                        $tr_received->save();

                    foreach($service_lines as $it){

                        $prev                             =  new DeliveredPrevious;
                        $prev->product_id                 =  $it->product_id;
                        $prev->store_id                   =  $it->store_id;
                        $prev->business_id                =  $it->transaction->business_id ;
                        $prev->transaction_id             =  $it->transaction->id;
                        $prev->unit_id                    =  $it->product->unit->id;
                        $prev->total_qty                  =  $it->quantity;
                        $prev->current_qty                =  $it->quantity;
                        $prev->remain_qty                 =  0;
                        $prev->transaction_recieveds_id   =  $tr_received->id;
                        $prev->product_name               =  $it->product->name;
                        $prev->line_id                    =  $it->id;
                        $prev->is_returned                =  1; 
                        $prev->save();
                       
                        \App\Models\WarehouseInfo::update_stoct($it->product->id,$it->store_id,$it->quantity,$it->transaction->business_id);
                        \App\MovementWarehouse::movemnet_warehouse($sell_return,$it->product,$it->quantity,$it->store_id,$it,"plus",$tr_received->id);
                        Transaction::update_status($tr_received->id,'deliver');

                    }
                    
                }
            }
            \App\Models\ItemMove::return_sale_delivery($sell_return,1);
            $output = ['success' => 1,
                        'msg' => __('lang_v1.sell_return_added_success')
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

        return redirect('sell-return')->with('status', $output);
    }
    /**
     * 
     * 
     */
    public function update_return(Request $request)
    {
        
        try {
            $business_id = $request->session()->get('user.business_id');
            $user_id     = $request->session()->get('user.id');
            
            DB::beginTransaction();
           
            $input_data = $request->only([ 
                'location_id', 'transaction_date', 'final_total','total_final_input','status','total_return_input',
                'ref_no','cost_center_id','store_id','discount_amount2','discount_amount','discount_type','currency_id_amount','currency_id',
                'tax_id', 'tax_amount2', 'contact_id','sup_ref_no',  'agent_id','project_no','pattern_id'
                ]);

            # Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                    return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
                } 
            } 
        
            # Check if delete rows after edit 
            $line_id_delete  = []; 
            foreach($request->products as $it){
                if($it["sell_line_id"] != null){
                    $line_id_delete[]   = $it["sell_line_id"];
                }
            }
            if(count($line_id_delete)>0){
                $lines_sell = \App\TransactionSellLine::where("transaction_id",$request->transaction_id)->whereNotIn("id",$line_id_delete)->get();
                $move       = [];
                foreach($lines_sell as $it){
                    $pre = \App\Models\DeliveredPrevious::where("line_id",$it->id)->first();
                    if(!empty($pre)){
                        $tr_delivery   = \App\Models\TransactionDelivery::where("id",$pre->transaction_recieveds_id)->first();
                        $pre_id        = ($pre)?$pre->id:null;
                        $moveWarehouse = \App\MovementWarehouse::where("delivered_previouse_id",$pre_id)->first();
                        $ItemMove      = \App\Models\ItemMove::where("recieve_id",$pre_id)->first();
                        if(!empty($ItemMove)){
                            $move[] = $ItemMove->id;
                        }
                        if(!empty($moveWarehouse)){
                            \App\Models\WarehouseInfo::update_stoct($it->product_id,$it->store_id,$it->quantity*-1,$it->transaction->business_id);
                            $moveWarehouse->delete();
                            if(!empty($ItemMove)){
                                $_id          = $ItemMove->id;
                                $product_id   = $ItemMove->product_id;
                                $ItemMove->delete();
                                \App\Models\ItemMove::refresh_item($_id,$product_id);
                                $move_all  = \App\Models\ItemMove::where("product_id",$product_id)->whereNotIn("id",[])->get(); 
                                if(count($move_all)>0){
                                    foreach($move_all as $key =>  $it){
                                        \App\Models\ItemMove::refresh_item($it->id,$it->product_id );
                                    }
                                }
                                $pre->delete();
                            }   
                        }
                        
                    }
                    $it->delete(); 
                    if(!empty($pre)){ 
                        $child_s = \App\Models\TransactionDelivery::childs($tr_delivery->id);
                        if(count($child_s)==0){
                            $tr_delivery->delete();
                        } 
                    } 
                }
            }
            $company_name      = request()->session()->get("user_main.domain");
            $document_sell =  \App\Transaction::find($request->transaction_id)->document;
                if ($request->hasFile('document_sell')) { $ids=1;
                    foreach ($request->file('document_sell') as $file) {
                        $file_name =  'uploads/companies/'.$company_name.'/documents/sale_return/'.time().'_'.$ids++.'.'.$file->getClientOriginalExtension();
                        $file->move('uploads/companies/'.$company_name.'/documents/sale_return',$file_name);
                        array_push($document_sell,$file_name);
                    }
                } 
            if(json_encode($document_sell)!="[]"){
                $input_data['document'] = json_encode($document_sell);
            }
            # get info 
            $trans                        = \App\Transaction::where("id",$request->transaction_id)->first();
            $old_return                   = $trans->replicate();

            # update sell return transaction  
            $trans->store                 = $input_data['store_id'];
            $trans->type                  = 'sell_return';
            $trans->contact_id            = $input_data['contact_id'];
            $trans->status                = $input_data['status'];
            $trans->tax_amount            = $input_data['tax_amount2'];
            $trans->business_id           = $business_id;
            $trans->created_by            = $user_id;
            $trans->transaction_date      = $this->productUtil->uf_date($input_data['transaction_date'], true);
            $trans->total_before_tax      = $this->productUtil->num_uf($input_data['total_final_input']) - $this->productUtil->num_uf($input_data['tax_amount2']);
            if(json_encode($document_sell)!="[]"){
                $trans->document          = $input_data['document'];
            }
            $trans->sup_refe              = $input_data['sup_ref_no'];
            $trans->ship_amount           = 0;
            $trans->discount_amount       = $input_data['discount_amount'];
            $trans->agent_id              = $request->input('agent_id');
            $trans->final_total           = $request->input('total_final_input');
            $trans->cost_center_id        = $request->input('cost_center_id');
            $trans->currency_id           = $input_data['currency_id'];
            $trans->exchange_price        = ($input_data['currency_id']!= null)?$input_data['currency_id_amount']:1;
            $trans->amount_in_currency    = ($input_data['currency_id']!= null)?($request->input('total_final_input') / $input_data['currency_id_amount']):null;
            # $trans->tax_amount         = $request->input('tax_calculation_amount');
            $trans->pattern_id            = $request->pattern_id;
            
            if( $request->discount_type == null ){
                $trans->discount_amount  = 0;
            }
            $trans->update();

         
            # update items in bill
            $sub_total_rt_purchase = 0;
            if(!empty($request->products)){
                $product_data = [];
                foreach ($request->products as $key=>$product) {
                            $unit_price               = $this->productUtil->num_uf($product['unit_price_before_dis_exc']);
                            $unit_price_after_dis_exc = $this->productUtil->num_uf($product['unit_price_after_dis_exc']);
                            $unit_price_after_dis_inc = $this->productUtil->num_uf($product['unit_price_after_dis_inc']);
                            if (!empty($product['sell_line_id'])) {
                                $return_line              = \App\TransactionSellLine::find($product['sell_line_id']);
                                $updated_purchase_lines[] = $return_line->id;
                                $return_line->quantity    = $this->productUtil->num_uf($product['quantity']) ;
                            } else {
                                $return_line         = new TransactionSellLine([
                                    'product_id'     => $product['product_id'],
                                    'transaction_id' => $trans->id,
                                    'variation_id'   => $product['variation_id'],
                                    'quantity'       => $this->productUtil->num_uf($product['quantity']) 
                                ]);
                            }
                            $sub_total_rt_purchase                  += ($this->productUtil->num_uf($product['quantity'])*$unit_price_after_dis_exc) ;
                            $return_line->store_id                   = $request->store_id;
                            $return_line->unit_price                 = $unit_price_after_dis_exc;
                            $return_line->unit_price_before_discount = $unit_price;
                            $return_line->unit_price_inc_tax         = $unit_price_after_dis_inc;
                            $return_line->line_discount_type         = 'percentage';
                            $return_line->line_discount_amount       = $product['discount_percent_return'];
                            $return_line->bill_return_price          =  $unit_price_after_dis_exc;
                            $return_line->quantity_returned          = $this->productUtil->num_uf($product['quantity']);
                           
                            if (!empty($product['sell_line_id'])) {
                                $return_line->update();
                            } else {
                                $return_line->save();
                            }
                            $product_data[] = $return_line;
                }
            }
            # entries
            \App\AccountTransaction::update_return_sales($trans,$request->discount_amount2,$request->total_final_input,$sub_total_rt_purchase,$request->tax_amount2,$old_return);
           
            #  compare status 
            if($old_return->status == "final" && $trans->status == "delivered"){
                $sells_lines       = \App\TransactionSellLine::where("transaction_id",$trans->id)->get();
                foreach($sells_lines as $it){
                    $price                 = \App\TransactionSellLine::orderby("id","desc")->where("product_id",$it->product_id)->first();
                    \App\Models\WarehouseInfo::update_stoct($it->product_id,$it->store_id,$it->quantity,$it->transaction->business_id);
                    $prev                  =  new DeliveredPrevious;
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
                    MovementWarehouse::movemnet_warehouse_sell($it->transaction,$it->product,$it->quantity,$it->store_id,$it,$prev->id,NULL,1);
                }
                $type                         =  'trans_delivery';
                $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
                $receipt_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                $tr_received                  =  new TransactionDelivery;
                $tr_received->store_id        =  $trans->store;
                $tr_received->transaction_id  =  $trans->id;
                $tr_received->business_id     =  $trans->business_id ;
                $tr_received->reciept_no      =  $receipt_no ;
                $tr_received->invoice_no      =  $trans->invoice_no;
                $tr_received->is_returned     =  1;
                $tr_received->date            = ($request->date)?$request->date:\Carbon\Carbon::now();
                $tr_received->status          = 'Return  Sale';
                $tr_received->save();

                $prev_s                                  = \App\Models\DeliveredPrevious::where("transaction_id",$trans->id)->get();
                foreach($prev_s as $pr){
                    $item_prv                            = \App\Models\DeliveredPrevious::find($pr->id); 
                    $item_prv->transaction_recieveds_id  = $tr_received->id;
                    $item_prv->update();
                }
                \App\Models\ItemMove::return_sale_delivery($trans);
            }else if($old_return->status == "delivered" && $trans->status == "delivered"){
                $sells_lines = \App\TransactionSellLine::where("transaction_id",$trans->id)->get();
                 
                foreach($sells_lines as $it){
                    $item_move = \App\Models\ItemMove::where("line_id",$it->id)->first();
                    if(empty($item_move)){
                        \App\Models\WarehouseInfo::update_stoct($it->product_id,$it->store_id,$it->quantity,$it->transaction->business_id);
                            
                        $prev                  =  new DeliveredPrevious;
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
                        MovementWarehouse::movemnet_warehouse_sell($it->transaction,$it->product,$it->quantity,$it->store_id,$it,$prev->id,NULL,1);
                    } 
                }
            }else {
                $sellLine                         = \App\TransactionSellLine::where("transaction_id",$trans->id)->get();
                $service_lines                    = [] ;
                foreach($sellLine as $it){
                    if($it->product->enable_stock == 0){   
                        $service_lines[]=$it;                             
                    }
                }
                if(count($service_lines)>0){
                    $tr_delivered = \App\Models\TransactionDelivery::where("transaction_id",$trans->id)->where("status",'Service Item')->first();
                    $id_td        = ($tr_delivered)?$tr_delivered->id:null;
                    foreach($service_lines as $it){
                        $previous = \App\Models\DeliveredPrevious::where("transaction_recieveds_id",$id_td)->where("line_id",$it->id)->first();
                        if(!empty($previous)){
                            $margin                    =  $it->quantity -  $previous->current_qty;
                            $previous->store_id        =  $it->store_id;
                            $previous->total_qty       =  $it->quantity;
                            $previous->current_qty     =  $it->quantity;
                            if($margin == 0){
                                $qty_margin = 0;
                            }elseif($margin < 0){
                                $qty_margin = $margin;
                            }else{
                                $qty_margin = $margin*-1;
                            } 
                            $previous->update();
                            \App\Models\WarehouseInfo::update_stoct($it->product->id,$it->store_id,$qty_margin,$it->transaction->business_id);
                            \App\MovementWarehouse::movemnet_warehouse($trans,$it->product,$it->quantity,$it->store_id,$it,"plus",$id_td);
                        }else{
                            $prev                             =  new DeliveredPrevious;
                            $prev->product_id                 =  $it->product_id;
                            $prev->store_id                   =  $it->store_id;
                            $prev->business_id                =  $it->transaction->business_id ;
                            $prev->transaction_id             =  $it->transaction->id;
                            $prev->unit_id                    =  $it->product->unit->id;
                            $prev->total_qty                  =  $it->quantity;
                            $prev->current_qty                =  $it->quantity;
                            $prev->remain_qty                 =  0;
                            $prev->transaction_recieveds_id   =  $tr_delivered->id;
                            $prev->product_name               =  $it->product->name;
                            $prev->line_id                    =  $it->id;
                            $prev->save();

                            \App\Models\WarehouseInfo::update_stoct($it->product->id,$it->store_id,$it->quantity,$it->transaction->business_id);
                            \App\MovementWarehouse::movemnet_warehouse($trans,$it->product,$it->quantity,$it->store_id,$it,"plus",$id_td);
                        }
                        
                    }
                    \App\Models\ItemMove::return_sale_delivery($trans,1);
                } 
            }

            DB::commit();
            $output = [
                        'success' => 1,
                        'msg'     => __('lang_v1.sell_return_added_success')
                ];
        } catch (\Exception $e) {
                // 'msg'     => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                            'success' => 0,
                            'msg'     => __('messages.something_went_wrong')
                        ];
        }

        return redirect('sell-return')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('access_sell_return')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                $business_id = request()->session()->get('user.business_id');
                $sell_return = Transaction::where('id', $id)
                                          ->where('business_id', $business_id)
                                          ->where('type', 'sell_return')
                                          ->with(['sell_lines', 'payment_lines'])
                                          ->first();

                $sell_lines  = TransactionSellLine::where('transaction_id', $sell_return->return_parent_id)->get();
                # Check if received exist then not allowed
                $receive     = \App\Models\DeliveredPrevious::where("transaction_id",$id)->where("is_returned",1)->first();
                if (!empty($receive)) {
                   $output = [
                       'success' => false,
                       'msg'     => __('lang_v1.sorry_there_is_delivery')
                   ];
                   return $output;
                }
                if (!empty($sell_return)) {
                    $transaction_payments = $sell_return->payment_lines;

                    foreach ($sell_lines as $sell_line) {
                        if ($sell_line->quantity_returned > 0) {
                            $quantity                     = 0;
                            $quantity_before              = $this->transactionUtil->num_f($sell_line->quantity_returned);
                            $sell_line->quantity_returned = 0;
                            $sell_line->save();
                            # Update quantity sold in corresponding purchase lines
                            $this->transactionUtil->updateQuantitySoldFromSellLine($sell_line, 0, $quantity_before);
                            # Update quantity in variation location details
                            $this->productUtil->updateProductQuantity($sell_return->location_id, $sell_line->product_id, $sell_line->variation_id, 0, $quantity_before);
                        }
                    }

                    $sell_return->delete();
                    $move_remove    = \App\Models\ItemMove::where('transaction_id', $sell_return->return_parent_id)->whereIn("line_id",$sell_lines->pluck("id"))->where("is_returned",1)->get();
                    foreach($move_remove as $it){
                        $id         = $it->id;
                        $product_id = $it->product_id;  
                        $it->delete();
                        \App\Models\ItemMove::refresh_item($it->id,$product_id);
                        $move_all  = \App\Models\ItemMove::where("product_id",$product_id)->whereNotIn("id",[])->get(); 
                        if(count($move_all)>0){
                            foreach($move_all as $key =>  $it){
                                \App\Models\ItemMove::refresh_item($it->id,$it->product_id );
                            }
                        }
                    }
                    foreach ($transaction_payments as $payment) {
                        event(new TransactionPaymentDeleted($payment));
                    }
                }
                $acc_trans  = \App\AccountTransaction::where('transaction_id',$sell_return->return_parent_id)->whereNotNull("return_transaction_id")->get();
                foreach($acc_trans as $it){
                    $account_transaction = $it->account_id;
                    $action_date         = $it->operation_date;
                    $ac                  = \App\Account::find($account_transaction);
                    $it->delete();
                    if($ac->cost_center!=1){
                        \App\AccountTransaction::nextRecords($ac->id,$sell_return->business_id,$action_date);
                    }
                }
                DB::commit();
                $output = [
                            'success' => 1,
                            'msg'     => __('lang_v1.success'),
                        ];
            } catch (\Exception $e) {
                DB::rollBack();
                if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                    $msg = $e->getMessage();
                } else {
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    $msg = __('messages.something_went_wrong');
                }

                $output = [
                                'success' => 0,
                                'msg'     => $msg
                            ];
            }

            return $output;
        }
    }

    /**
     * Returns the content for the receipt
     *
     * @param  int  $business_id
     * @param  int  $location_id
     * @param  int  $transaction_id
     * @param string $printer_type = null
     *
     * @return array
     */
    private function receiptContent(
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null
    ) {
        $output = [
                    'is_enabled'      => false,
                    'print_type'      => 'browser',
                    'html_content'    => null,
                    'printer_config'  => [],
                    'data'            => []
                ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        //Check if printing of invoice is enabled or not.
        if ($location_details->print_receipt_on_invoice == 1) {
            # If enabled, get print type.
            $output['is_enabled'] = true;
            $invoice_layout       = $this->businessUtil->invoiceLayout($business_id, $location_id, $location_details->invoice_layout_id);
            # Check if printer setting is provided.
            $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;
            $receipt_details      = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);
            # If print type browser - return the content, printer - return printer config data, and invoice format config
            if ($receipt_printer_type == 'printer') {
                $output['print_type']     = 'printer';
                $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
                $output['data']           = $receipt_details;
            } else {
                /*dd($receipt_details);*/
                $output['html_content'] = view('sell_return.receipt', compact('receipt_details'))->render();
            }
        }

        return $output;
    }

    /**
     * Prints invoice for sell
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice(Request $request, $transaction_id)
    {
        if (request()->ajax()) {
            try {
                $output = [
                    'success' => 0,
                    'msg'     => trans("messages.something_went_wrong")
                ];
                $business_id = $request->session()->get('user.business_id');
                $transaction = Transaction::where('business_id', $business_id)->where('id', $transaction_id)->first();
                if (empty($transaction)) {
                    return $output;
                }
                $receipt     = $this->receiptContent($business_id, $transaction->location_id, $transaction_id, 'browser');
                if (!empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                $output = [
                    'success' => 0,
                    'msg'     => trans("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
}
