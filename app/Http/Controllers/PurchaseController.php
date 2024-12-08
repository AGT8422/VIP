<?php
 
namespace App\Http\Controllers;

use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\Product;
use App\PurchaseLine;
use App\TaxRate;
use App\Transaction;
use App\User; 
use App\Unit;
use App\Utils\BusinessUtil;

use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;

use App\Variation;
use App\TransactionSellLine;
use App\Models\Warehouse;
use App\Models\WarehouseInfo;
use App\Models\RecievedPrevious;
use App\Models\RecievedWrong;
use App\Models\TransactionRecieved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;

class PurchaseController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /** *F
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

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
        'is_return' => 0, 'transaction_no' => ''];
    }

    /** *F
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('ReadOnly.views')&& !auth()->user()->can('SalesMan.views')&& !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views') && !auth()->user()->can('purchase.create') && !auth()->user()->can('view_own_purchase')) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id      = request()->session()->get('user.business_id');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
       
        if (request()->ajax()) {
            $purchases           = $this->transactionUtil->getListPurchases($business_id);
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }
            if (!empty(request()->sup_refe)) {
                $purchases->where('transactions.sup_refe', request()->sup_refe);
            }
            if (!empty(request()->supplier_id)) {
                $purchases->where('contacts.id', request()->supplier_id);
            }
            if (!empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }
            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $purchases->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $purchases->whereIn('transactions.payment_status', ['due', 'partial'])
                ->whereNotNull('transactions.pay_term_number')
                ->whereNotNull('transactions.pay_term_type')
                ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }
            
            if (!empty(request()->status)) {
                $purchases->where('transactions.status', request()->status);
            }
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start =  request()->start_date;
                $end   =  request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                ->whereDate('transactions.transaction_date', '<=', $end);
            }
            
            if (!auth()->user()->can('purchase.view') && auth()->user()->can('view_own_purchase')) {
                $purchases->where('transactions.created_by', request()->session()->get('user.id'));
            }
            
            // dd($purchases->whereIn('transactions.location_id', $permitted_locations));
            return Datatables::of($purchases)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                    data-toggle="dropdown" aria-expanded="false">' .
                    __("messages.actions") .
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                    </span>
                    </button> 
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    if (auth()->user()->can("purchase.view") || auth()->user()->can("warehouse.views")|| auth()->user()->can('SalesMan.views')||auth()->user()->can('admin_supervisor.views') || auth()->user()->can("manufuctoring.views") || auth()->user()->can("admin_without.views") || auth()->user()->can("admin_supervisor.views")) {
                        $html .= '<li><a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                    }
                    if (auth()->user()->can("purchase.view")|| auth()->user()->can("warehouse.views")|| auth()->user()->can('SalesMan.views')||auth()->user()->can('admin_supervisor.views') || auth()->user()->can("manufuctoring.views") || auth()->user()->can("admin_supervisor.views") || auth()->user()->can("admin_without.views") ) {
                        if ( auth()->user()->can("product.avarage_cost") || auth()->user()->hasRole("Admin#" . session("business.id") ) ){
                            $business_module = \App\Business::find($row->business_id);

                            $id_module       = (!empty($business_module))?(($business_module->purchase_print_module != null )? $business_module->purchase_print_module:null):null;

                            if(!empty($business_module)){
                                if($business_module->purchase_print_module != null && $business_module->purchase_print_module != "[]" ){
                                    $all_pattern = json_decode($business_module->purchase_print_module);
                                }else{
                                    $id_module = null;
                                }
                            }else{
                                $id_module = null ;
                            }

                            if($id_module != null){
                                $html .= '<li> <a href="'.\URL::to('reports/purchase/'.$row->id.'?ref_no='.$row->ref_no).'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'. __("messages.print") .'</a>';
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
                                $html .= '<li><a href="'.\URL::to('reports/purchase/'.$row->id.'?ref_no='.$row->ref_no).'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'. __("messages.print") .'</a></li>';
                            }
                            
                        }
                    }
                    if( $row->status == "final" || $row->status == "received"){
                        $html .= '<li><a href="#" data-href="' .\URL::to('entry/transaction/'.$row->id) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-align-justify" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                    }
                    if (auth()->user()->can("purchase.update") || auth()->user()->can('SalesMan.views')||auth()->user()->can('admin_supervisor.views') ) {
                        if (request()->session()->get("user.id") == 1 || auth()->user()->can('product.avarage_cost')) {
                        $html .= '<li><a href="' . action('PurchaseController@edit', [$row->id]) . '"><i class="fas fa-edit"></i>' . __("messages.edit") . '</a></li>';
                        }
                    }
                    if (auth()->user()->can("purchase.update") || auth()->user()->can('SalesMan.views')||auth()->user()->can('admin_supervisor.views') ) {
                        $html .= '<li><a class="btn-modal" data-container=".view_modal" href="" data-href="' . action('StatusLiveController@show', [$row->id]) . '"><i class="fas fa-eye"></i>' . __("home.Status Live") . '</a></li>';
                    }
                    if ( request()->session()->get("user.id") == 1) {
                        $html .= '<li><a href="' . action('PurchaseController@destroy', [$row->id]) . '" class="delete-purchase"><i class="fas fa-trash"></i>' . __("messages.delete") . '</a></li>';
                    }

                    // $html .= '<li><a href="' . action('LabelsController@show') . '?purchase_id=' . $row->id . '" data-toggle="tooltip" title="' . __('lang_v1.label_help') . '"><i class="fas fa-barcode"></i>' . __('barcode.labels') . '</a></li>';

                    // if (auth()->user()->can("purchase.view") && !empty($row->document)) {
                    //     $document_name = !empty(explode("_", $row->document, 2)[1]) ? explode("_", $row->document, 2)[1] : $row->document ;
                    //     $html .= '<li><a href="' . url('public/uploads/documents/' . $row->document) .'" download="' . $document_name . '"><i class="fas fa-download" aria-hidden="true"></i>' . __("purchase.download_document") . '</a></li>';
                    //     if (isFileImage($document_name)) {
                    //         $html .= '<li><a href="#" data-href="' . url('public/uploads/documents/' . $row->document) .'" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true"></i>' . __("lang_v1.view_document") . '</a></li>';
                    //     }
                    // }
                                        
                    if (auth()->user()->can("purchase.create") || auth()->user()->can('SalesMan.views')||auth()->user()->can('admin_supervisor.views') ) {
                        $html .= '<li class="divider"></li>';
                        if ($row->payment_status != 'paid') {
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>' . __("purchase.add_payment") . '</a></li>';
                        }
                        $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) .
                        '" class="view_payment_modal"><i class="fas fa-money-bill-alt" aria-hidden="true" ></i>' . __("purchase.view_payments") . '</a></li>';
                    }

                    if (auth()->user()->can("purchase.update")) {
                        $html .= '<li><a href="' . action('PurchaseReturnController@add', [$row->id]) .
                        '"><i class="fas fa-undo" aria-hidden="true" ></i>' . __("lang_v1.purchase_return") . '</a></li>';
                    }

                    if (auth()->user()->can("purchase.update") || auth()->user()->can("purchase.update_status") || auth()->user()->can('SalesMan.views')||auth()->user()->can('admin_supervisor.views') ) {
                        $RecievedPrevious = RecievedPrevious::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                                
                        if($RecievedPrevious == null){ $class = "update_status" ;}else{$class = "";}
                        $html .= '<li><a href="#" data-purchase_id="' . $row->id .
                        '" data-status="' . $row->status . '" class="'.$class.'"><i class="fas fa-edit" aria-hidden="true" ></i>' . __("lang_v1.update_status") . '</a></li>';
                    }

                    if ($row->status == 'ordered') {
                        $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id,"template_for" => "new_order"]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.new_order_notification") . '</a></li>';
                    } elseif ($row->status == 'received') {
                        $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id,"template_for" => "items_received"]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_received_notification") . '</a></li>';
                    } elseif ($row->status == 'pending') {
                        $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id,"template_for" => "items_pending"]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_pending_notification") . '</a></li>';
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->removeColumn('id')
                ->editColumn('ref_no', function ($row) {
                     if(    (!empty($row->document) && $row->document != "[]" )  ){
                         if ( auth()->user()->can("product.avarage_cost") || auth()->user()->hasRole("Admin#" . session("business.id") ) ){
                            $attach = "<br>
                            <a class='btn-modal' data-href='".\URL::to('sells/attachment/'.$row->id)."' data-container='.view_modal'>
                            <i class='fas fa-paperclip'></i>
                            
                            </a>
                            ";
                         }else{
                            $attach = "";
                         }
                    }else{
                        $attach = "";
                    }
                    return !empty($row->return_exists) ? $row->ref_no . ' <small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned') .'"><i class="fas fa-undo"></i></small>' . $attach : $row->ref_no .  $attach;
                })
                ->editColumn(
                    'final_total',function($row)    {
                        $id          = $row->id;
                        $tr          = Transaction::find($id) ;
                        $final_total = $tr->final_total;
                        $cost_shipp  =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                                $query->where("type",1);
                                                                                $query->where("transaction_id",$id);
                                                                                $query->where("contact_id",$tr->contact_id);
                                                                            })->sum("total");
                        $final_pr = $final_total + $cost_shipp;
                        // $business = \App\Business::find($tr->business_id); $currency = \App\Currency::find($business->currency_id); 
                        // $currency_symbol =  isset($currency)?$currency->symbol:"";
                        // return  '<span class="final-total" data-orig-value="'.$final_pr.'">'. number_format($final_pr,config('constants.currency_precision')) . $currency_symbol.'</span>';
                        return  $final_pr ;
                    }
                 )
                ->addColumn('sup_refe', function ($row) {
                    $transactions = Transaction::where("ref_no" , $row->ref_no)->first();
                    return  $transactions->sup_refe;
                })
                ->editColumn(
                    'tax_amount',
                    '{{round($tax_amount,2)}}'
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->addColumn('name',function($row){
                    $account = \App\Account::where("contact_id",$row->contact->id)->first();
                    return view("account.action_parents.show_ledger_purchase",['account' => $account]);
                })
                ->editColumn('status',function($row){
                                $product_list       = [];
                                $purchase           = \App\PurchaseLine::where("transaction_id",$row->id)->get();
                                foreach($purchase as $it){
                                    $product_list[] = $it->product_id;
                                }
                                $Purchaseline       = PurchaseLine::where("transaction_id",$row->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                                $RecievedPrevious   = RecievedPrevious::where("transaction_id",$row->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                                $wrong              = RecievedWrong::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                                if($Purchaseline <= $RecievedPrevious){
                                    // $state   = "received";
                                    $state   =  $row->status;
                                }else{
                                    if($Purchaseline  != $RecievedPrevious){
                                        if($row->status  == "received"){
                                            $state   = "final";
                                        }else{
                                            $state   = $row->status;
                                        }        
                                    }
                                    
                                }
                                return (string) view('sell.partials.bill_status', ['state' => $state, 'id' => $row->id , "RecievedPrevious"=>$RecievedPrevious, "wrong" => $wrong ,"Purchaseline" => $Purchaseline]);
                                 
                })->editColumn(
                'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);
                        $cheques        = \App\Models\Check::where("transaction_id",$row->id)->whereIn("status",[0,3,4])->get();
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true,'cheques' => $cheques]);
                    }
                )
                ->addColumn('warehouse', function ($row) {
                    $transactions                 = Transaction::where("ref_no" , $row->ref_no)->get();
                    $store                        = "";
                    if(!empty($transactions)){
                        foreach ($transactions as $transaction) {
                            $business_id          = request()->session()->get('user.business_id');
                            $warehouses           = Warehouse::where("business_id",$business_id)->select(["description","status","mainStore","id","name"])->get();
                            if (!empty($warehouses)) {
                                foreach ($warehouses as $warehouse) {
                                    if($transaction->store == $warehouse->id){
                                         $store = $warehouse->name;
                                    }
                                }
                            }   
                        }
                    }
                    return $store;
                })
                ->addColumn('payment_due', function ($row) {
                    $id          = $row->id;
                    $tr          = Transaction::find($id);
                    $cost_shipp  = \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                                $query->where("type",1);
                                                                                $query->where("transaction_id",$id);
                                                                                $query->where("contact_id",$tr->contact_id);
                                                                            })->sum("total");
                    
                    $due      = $row->final_total + $cost_shipp - $row->amount_paid;
                    $due_html = '<strong>' . __('lang_v1.purchase') .':</strong> <span class="payment_due" data-orig-value="' . $due . '">' . $this->transactionUtil->num_f($due, true) . '</span>';
                    
                    if (!empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $due_html .= '<br><strong>' . __('lang_v1.purchase_return') .':</strong> <a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="purchase_return" data-orig-value="' . $return_due . '">' . $this->transactionUtil->num_f($return_due, true) . '</span></a>';
                    }
                    return $due_html;
                })
                ->addColumn('recieved_status', function ($row) {
                    
                   
                        $Purchaseline     = PurchaseLine::where("transaction_id",$row->id)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                        $RecievedPrevious = RecievedPrevious::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                        $wrong            = RecievedWrong::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                         
                        if($RecievedPrevious == null){
                            $state = $row->status;
                            $payment_status = "not_delivered";
                            return (string) view('sell.partials.delivered_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong ,  "state" => $state,"type"=>"normal"]);
                             
                        }else if($Purchaseline == $RecievedPrevious){
                            $state =$row->status;
                            $payment_status = "delivered";
                            return (string) view('sell.partials.delivered_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong , "state" => $state ,"type"=>"normal"]);
                        
                        }else if( $RecievedPrevious < $Purchaseline && $RecievedPrevious != 0){
                             
                            $state = $row->status;
                            $payment_status = "separate";
                            return (string) view('sell.partials.delivered_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong , "state" => $state,"type"=>"normal"]);
    
                        }else if( $RecievedPrevious > $Purchaseline && $RecievedPrevious != 0 ){
                            $state = $row->status;
                            $payment_status = "wrong";
                            return (string) view('sell.partials.delivered_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong , "state" => $state,"type"=>"normal"]);
    
                        }
                    })
                 
                    ->rawColumns(['final_total', 'action','tax_amount', 'payment_due','warehouse',"recieved_status", 'payment_status', 'status', 'ref_no', 'name'])
                    ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers          = Contact::suppliersDropdown($business_id, false);
        $orderStatuses      = $this->productUtil->orderStatuses();
        $mainstore          = Warehouse::where('business_id', $business_id)->select(['name','id'])->get();
        $mainstore_categories = [];
        if (!empty($mainstore)) {
            foreach ($mainstore as $mainstor) {
                $mainstore_categories[$mainstor->id] = $mainstor->name;
            }
                   
        }
        $sup_refe  = [];
        $sup_refe_ = Transaction::whereNotNull("sup_refe")->get();
        
        foreach($sup_refe_ as  $value){
            $sup_refe[ $value->sup_refe] = $value->sup_refe;
        }
        return view('purchase.index')
            ->with(compact('business_locations', 'sup_refe', 'suppliers','mainstore_categories', 'orderStatuses'));
    }
    /** *F
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('purchase.create')&&!auth()->user()->can('SalesMan.views')&&!auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $ship_from   = 1 ; 
        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            } 
        } 

        $taxes = TaxRate::where('business_id', $business_id)
                        ->ExcludeForTaxGroup()
                        ->get();

        $orderStatuses        = $this->productUtil->orderStatuses();
        $business_locations   = BusinessLocation::forDropdown($business_id, false, true);
        $mainstore            = Warehouse::where('business_id', $business_id)->select(['name','id','status','mainStore','description'])->get();
        $mainstore_categories = [];
        if (!empty($mainstore)) {
            foreach ($mainstore as $mainstor) {
                if($mainstor->status != 0){
                    $mainstore_categories[$mainstor->id] = $mainstor->name;
                }
            }
        }
        $bl_attributes           = $business_locations['attributes'];
        $business_locations      = $business_locations['locations'];
        $currency_details        = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $default_purchase_status = null;

        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
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

        $customer_groups    = CustomerGroup::forDropdown($business_id);
        $business_details   = $this->businessUtil->getDetails($business_id);
        $shortcuts          = json_decode($business_details->keyboard_shortcuts, true);
        $payment_line       = $this->dummyPaymentLine;
        $payment_types      = $this->productUtil->payment_types(null, true);

        //Accounts
        $cost_centers       = \App\Account::cost_centers();
        $accounts           = $this->moduleUtil->accountsDropdown($business_id, true);
        $accounts           = \App\Account::main();
        $currency           = \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies         = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }$row = 1;
        $list_of_prices         = \App\Product::getListPrices($row);
        return view('purchase.create')
            ->with(compact('taxes','ship_from' , 'list_of_prices','currencies','orderStatuses',"cost_centers", 'business_locations','mainstore_categories', 'currency_details', 'default_purchase_status', 'customer_groups', 'types', 'shortcuts', 'payment_line', 'payment_types', 'accounts', 'bl_attributes'));
    }
 
    /** *F
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         if (!auth()->user()->can('purchase.create') &&!auth()->user()->can('SalesMan.views')&&!auth()->user()->can('admin_supervisor.views')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = $request->session()->get('user.business_id');
            //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
                if (!$this->moduleUtil->isSubscribedPermitted($business_id)) {
                    return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
                }
            }
             
            $taxx_id = $request->tax_id;
            
            //shipping_charges
            $transaction_data = $request->only([  'ref_no'
                                                , 'contact_id'
                                                , "purchase_note"   
                                                , "cost_center_id"
                                                , 'status'
                                                , "sup_ref_no"
                                                , "ADD_SHIP" 
                                                , 'store_id' 
                                                , 'transaction_date'
                                                , 'total_before_tax'
                                                , 'location_id'
                                                , 'dis_currency'
                                                , 'discount_type'
                                                , 'discount_amount'
                                                , 'tax_id'
                                                , 'tax_amount'
                                                , 'shipping_details'
                                                , 'project_no'
                                                , 'final_total'
                                                , 'additional_notes'
                                                , 'exchange_rate'
                                                , 'pay_term_number'
                                                , 'pay_term_type']);
            $exchange_rate    = $transaction_data['exchange_rate'];
            //TODO: Check for "Undefined index: total_before_tax" issue
            //Adding temporary fix by validating
            $request->validate([
                'status'           => 'required',
                'contact_id'       => 'required',
                'transaction_date' => 'required',
                'total_before_tax' => 'required',
                'location_id'      => 'required',
                'store_id'         => 'required',
                'final_total'      => 'required',
                'document'         => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);
         
            $user_id                = $request->session()->get('user.id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');
            //Update business exchange rate.
            Business::update_business($business_id, ['p_exchange_rate' => ($transaction_data['exchange_rate'])]);
            $currency_details                        =  $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $actual_discount  = 0;
            //unformat input values
            $transaction_data['total_before_tax']    =  $this->productUtil->num_uf($transaction_data['total_before_tax'], $currency_details)*$exchange_rate;
            // If discount type is fixed them multiply by exchange rate, else don't
            if ($transaction_data['discount_type']  == 'fixed') {
             
                $transaction_data['discount_amount'] =  $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details)*$exchange_rate;
            } else if ($transaction_data['discount_type'] == 'percentage') {
                $transaction_data['discount_amount'] =  $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details);
                $actual_discount                     =  $transaction_data['discount_amount'];
                if($request->currency_id != null){  
                    $actual_discount = ($transaction_data['total_before_tax']*($transaction_data['discount_amount'])/100)  ;
                }
            } else if($transaction_data['discount_type'] == null){
                $transaction_data['discount_amount'] = 0;
            } else {
                if($transaction_data['discount_type']  == 'fixed_before_vat'){
                    $transaction_data['discount_amount'] = $request->discount_amount;
                    if($request->currency_id != null){  
                        $actual_discount = $request->discount_amount * $request->currency_id_amount ;
                    }
                }elseif($transaction_data['discount_type']  == 'fixed_after_vat'){
                    $VT                                  = \App\TaxRate::find($request->tax_id);
                    $vat_amount                          = ($VT != NULL)?$VT->amount:0; 
                    $transaction_data['discount_amount'] = $request->discount_amount ;
                    if($request->currency_id != null){  
                        $actual_discount = ($request->discount_amount*100/(100+$vat_amount)) * $request->currency_id_amount ;
                    }
                }else{
                    $transaction_data['discount_amount'] = $request->discount_amount;
                }
            }
           
            
            $transaction_data['tax_amount']               = $this->productUtil->num_uf($transaction_data['tax_amount'], $currency_details)*$exchange_rate;
            $transaction_data['shipping_charges']         = $this->productUtil->num_uf(0, $currency_details)*$exchange_rate;
            $transaction_data["ship_amount"]              = 0;
            $transaction_data["currency_id"]              = ($request->currency_id!=null)?$request->currency_id:null;
            $transaction_data["exchange_price"]           = ($request->currency_id!=null)?$request->currency_id_amount:1;
            $transaction_data["amount_in_currency"]       = ($request->currency_id_amount != "" && $request->currency_id_amount != 0 && $request->currency_id != null)? $request->final_total / $request->currency_id_amount:null;
            $transaction_data['final_total']              = $this->productUtil->num_uf($transaction_data['final_total']+floatVal($transaction_data['ADD_SHIP']), $currency_details)*$exchange_rate;
            $transaction_data['store']                    = $transaction_data['store_id'];
            $transaction_data['business_id']              = $business_id;
            $transaction_data['created_by']               = $user_id;
            $transaction_data['type']                     = 'purchase';
            $transaction_data['payment_status']           = 'due'; 
            $transaction_data['dis_type']                 = $request->dis_type; 
            $transaction_data['discount_type']            = $request->discount_type;
            $transaction_data['transaction_date']         = $this->productUtil->uf_date($transaction_data['transaction_date'], true);

            DB::beginTransaction();

            //Update reference count
            $ref_count                            = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no']       = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count);
            }
            //upload document
            $company_name      = request()->session()->get("user_main.domain");
            $document_purchase = [];
            if ($request->hasFile('document_purchase')) {
                $id_sf = 1;
                $referencesNewStyle = str_replace('/', '-', $transaction_data['ref_no']);
                foreach ($request->file('document_purchase') as $file) {
                    #................
                    if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                        if ($file->getSize() <= config('constants.document_size_limit')){ 
                            $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$id_sf++.'_'.$file->getClientOriginalName();
                            $file->move('uploads/companies/'.$company_name.'/documents/purchase',$file_name_m);
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase/'. $file_name_m;
                        }
                    }else{
                        if ($file->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time().'_'.$referencesNewStyle.'_'.$id_sf++.'_'.$file->getClientOriginalName();
                            $Data         = getimagesize($file);
                            $width         = $Data[0];
                            $height        = $Data[1];
                            $half_width    = $width/2;
                            $half_height   = $height/2; 
                            $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase/'. $new_file_name;
                            if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\purchase\\$new_file_name"),20)) {
                                $uploaded_file_name = $new_file_name;
                            }
                                
                        }
                    }
                    #................
                    array_push($document_purchase,$file_name);
                }
            }
            $transaction_data['document']         = json_encode($document_purchase);
            // $transaction_data['document']         = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            
            $transaction_data['cost_center_id']   = $request->cost_center_id;
            $transaction_data['list_price']       = $request->list_price;
            $transaction_data['sup_refe']         = $transaction_data['sup_ref_no'];
            
            //...............................................................................................................................
            //...............................................................................................................................
            $transaction                          = Transaction::create($transaction_data);
            //...............................................................................................................................
            //...............................................................................................................................
            if(\App\Account::find($transaction->contact_id)){
                $listOfAccountForUpdate[]=\App\Account::find($transaction->contact_id)->id;                                                                   
            }
            $archive = \App\Models\ArchiveTransaction::save_parent($transaction,"create");
            // **  Save Copy Of This Purchase Bill in Archive Table 
                // \App\ArchiveTransaction::save_old_bill($transaction);
            //.. ** 
            
            $additional_inputs = $request->only([
                'contact_id','shipping_amount','shipping_vat','shipping_total','shipping_account_id','shiping_text','add_currency_id','add_currency_id_amount',
                'shiping_date','shipping_contact_id','shipping_cost_center_id','cost_center_id','line_currency_id','line_currency_id_amount','currency_id','currency_id_amount',
            ]);

            $document_expense = [];
            if ($request->hasFile('document_expense')) {
                $oo_id = 1;
                $referencesNewStyle = str_replace('/', '-', $transaction_data['ref_no']);
                foreach ($request->file('document_expense') as $file) {
                    #................
                    if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                        if ($file->getSize() <= config('constants.document_size_limit')){ 
                            $file_name_m    =   time().'_'.$referencesNewStyle.'_Expense_'.$oo_id++.'_'.$file->getClientOriginalName();
                            $file->move('uploads/companies/'.$company_name.'/documents/purchase/expense',$file_name_m);
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase/expense/'. $file_name_m;
                        }
                    }else{
                        if ($file->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time().'_'.$referencesNewStyle.'_Expense_'.$oo_id++.'_'.$file->getClientOriginalName();
                            $Data         = getimagesize($file);
                            $width         = $Data[0];
                            $height        = $Data[1];
                            $half_width    = $width/2;
                            $half_height   = $height/2; 
                            $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase/expense/'. $new_file_name;
                            if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\purchase\\expense\\$new_file_name"),20)) {
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
                \App\Models\AdditionalShipping::add_purchase($transaction->id,$additional_inputs,$document_expense);
            }
            //..........................................................................
            //..........................................................................

            $purchase_lines = [];
            $purchases      = $request->input('purchases'); //this is array of product
            //......................................... create purchase ................................................................
            //............................................................................................................................
          
            $type_update    = "create";
            $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing,$type_update,null,$archive,$request);
            //............................................................................................................................
            //............................................................................................................................
            
            foreach($request["payment"] as $rq_payment){ 
                if($rq_payment["amount"] > 0){
                    $xx =   $request->input('payment');
                    if ($xx[0]['method'] == 'cheque') {
                        $xx[0]['account_id'] =  $xx[0]['cheque_account'];
                    }
                    // $this->transactionUtil->createOrUpdatePaymentLines($transaction, $xx);
                    \App\Services\Purchase\Recieve::add_payment($transaction,$xx);
                    $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
                    // update payment status
                    if ($rq_payment['cheque_number']) {
                        $pay_trans =  \App\TransactionPayment::where('transaction_id',$transaction->id)->first();
                        $rq_payment['transaction_payment_id'] = $pay_trans->id;
                        \App\Models\Check::add_cheque($transaction,$rq_payment);
                    } 
                }
            }

            // Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);
            $this->transactionUtil->activityLog($transaction, 'added');
            $before = \App\Models\WarehouseInfo::qty_before($transaction);
            if ($request->status ==  'received' || $request->status ==  'final') {
                if($request->shipping_amount != null){
                    \App\Models\AdditionalShipping::add_purchase_payment($transaction->id);
                }
               \App\Models\StatusLive::insert_data_p($business_id,$transaction,$request->status);
               $cost=0;$without_contact=0;
               if($request->shipping_amount != null){
                    $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                    $ids = $data_ship->items->pluck("id");
                    foreach($ids as $i){
                        $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                        if($data_shippment->contact_id == $request->contact_id){ 
                            $cost += $data_shippment->amount;
                        }else{
                            $without_contact += $data_shippment->amount;
                        }
                        \App\Models\StatusLive::insert_data_sh($business_id,$transaction,$data_shippment,"Add Expense");
                    }
                }
                
            }
            if($request->status ==  'received'){
                $total_expense = $cost + $without_contact;
                if ($request->discount_type == "fixed_before_vat"){
                    $dis = $request->discount_amount;
                }else if ($request->discount_type == "fixed_after_vat"){
                    $tax = \App\TaxRate::find($request->tax_id);
                     if(!empty($tax)){
                        $dis = ($request->discount_amount*100)/(100+$tax->amount) ;
                    }else{
                        $dis = ($request->discount_amount*100)/(100) ;
                    }
                 }else if ($request->discount_type == "percentage"){
                    $dis = ($request->total_before_tax *  $request->discount_amount)/100;
                }else{
                    $dis = 0;
                }
               
                \App\Models\ItemMove::create_itemMove($transaction,$total_expense,$before,null,$dis);

            }
            // $allTransaction =  \App\AccountTransaction::where('transaction_id',$transaction->id)->whereNull("check_id")->whereNull("payment_voucher_id")->pluck("account_id");
            // foreach($allTransaction as $account_for_refresh){
            //     $ac = \App\Account::find($account_for_refresh);
            //     if($ac->cost_center!=1){
            //         if(!in_array($ac->id,$listOfAccountForUpdate)){
            //             $listOfAccountForUpdate[]=$ac->id; 
            //         }
            //         // \App\AccountTransaction::oldBalance(0,$ac->id,$business_id,date('Y-m-d'));
            //     }
            // }
            DB::commit();
                $output = ['success' => 1,
                'msg' => __('purchase.purchase_add_success')
                    ];
            // $data =     json_encode($listOfAccountForUpdate) ;
            // $data =     substr($data,1,strlen($data)-2) ;
           
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            $output = ['success' => 0,
                            // 'msg' => __("messages.something_went_wrong")
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }
        return redirect('purchases')->with('status', $output);
    }

    /** *F
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('purchase.view') &&!auth()->user()->can('SalesMan.views')&&!auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('admin_without.views')&& !auth()->user()->can('manufuctoring.views')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $taxes       = TaxRate::where('business_id', $business_id)
                                ->pluck('name', 'id');
        $taxess      = TaxRate::where('business_id', $business_id)->get();
        $array       = [];
        foreach($taxess as $tvo){
            $tx =  $tvo->toArray();
            $array[$tx["id"]] = $tx["amount"] ;
        }
        $arrays        = array_keys($taxess->toArray());
        $purchcaseline = PurchaseLine::where('transaction_id', $id)->orderBy("order_id","asc")->get();
        $purchase      = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with([
                                    'contact',
                                    'purchase_lines'=> function($q){
                                        $q->orderBy("order_id","asc");
                                    },
                                    'purchase_lines.product',
                                    'purchase_lines.product.unit',
                                    'purchase_lines.variations',
                                    'purchase_lines.variations.product_variation',
                                    'purchase_lines.sub_unit',
                                    'location',
                                    'payment_lines',
                                    'tax'
                                ])
                                ->firstOrFail();
        $purchase1 = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->select("store")
                                ->with([
                                    'contact',
                                    'purchase_lines'=> function($q){
                                        $q->orderBy("order_id","asc");
                                    },
                                    'purchase_lines.product',
                                    'purchase_lines.product.unit',
                                    'purchase_lines.variations',
                                    'purchase_lines.variations.product_variation',
                                    'purchase_lines.sub_unit',
                                    'location',
                                    'payment_lines',
                                    'tax'
                                ])
                                ->firstOrFail();
                                
        if (!empty($purchase1)) {
            $store = "";
            $business_id = request()->session()->get('user.business_id');
            $warehouses = Warehouse::where("business_id",$business_id)->select(["description","status","mainStore","id","name"])->get();
            if (!empty($warehouses)) {
                foreach ($warehouses as $warehouse) {
                    if($purchase1->store == $warehouse->id){
                        $store = $warehouse->name;
                    }
                }
            }
        }
        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line         = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
        
        $payment_methods = $this->productUtil->payment_types($purchase->location_id, true);
        $purchase_taxes  = [];
        if (!empty($purchase->tax)) {
            if ($purchase->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
            } else {
                $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
            }
        }
        $activities          = Activity::forSubject($purchase)
                                        ->with(['causer', 'subject'])
                                        ->latest()
                                        ->get();
        $statuses            = $this->productUtil->orderStatuses();
        $RecievedWrong       = RecievedWrong::where('transaction_id', $id)->get();
        $RecievedPrevious    = RecievedPrevious::where('transaction_id', $id)->get();
        $product             = Product::where('business_id', $business_id)->get();
        $currency_details    = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $unit                = Unit::where('business_id', $business_id)->get();
        $business_locations  = BusinessLocation::forDropdown($business_id);
        $Warehouse           = Warehouse::where('business_id', $business_id)->get();
        $product_list        = [];
        foreach($product as $prd){
            foreach($purchcaseline as $pruche){
                if($pruche->product_id == $prd->id ){
                    $product_list[$prd->id] = $prd->name;
                }
            }
        }
        $product_list_all = [];
        foreach($product as $prd){
            $product_list_all[$prd->id] = $prd->name;
        }
        $Warehouse_list = [];
        foreach($Warehouse as $Ware){
            $Warehouse_list[$Ware->id] = $Ware->name;
        }
        return view('purchase.show')
                ->with(compact('taxes','array','RecievedWrong', 'purchase','RecievedPrevious','currency_details' ,'product_list_all', 'Warehouse_list' ,'product_list','unit','store' ,'payment_methods', 'purchase_taxes', 'activities', 'statuses'));
    }
    /** *F
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('purchase.update')  &&!auth()->user()->can('SalesMan.views')&&!auth()->user()->can('admin_supervisor.views')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $ship_from   =  0;
        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if (!$this->moduleUtil->isSubscribedPermitted($business_id)) {
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }
        }
        //Check if the transaction can be edited or not.
        // $edit_days = request()->session()->get('business.transaction_edit_days');
        // if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
        //     return back()
        //         ->with('status', ['success' => 0,
        //             'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])]);
        // }
        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', ['success' => 0,
                    'msg' => __('lang_v1.return_exist')]);
        }
        $business         = Business::find($business_id);
        $store_id         = "" ;
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $currency         =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies       = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $tax_amount       = [] ;
        $taxes            = TaxRate::where('business_id', $business_id)
                                    ->ExcludeForTaxGroup()
                                    ->get();
         
        foreach($taxes as $key => $value){
            $tax_amount[$value->id] = $value->amount;
        }
        
        $purchase = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with([
                                        'contact',
                                        'purchase_lines' => function ($q) {
                                            $q->orderBy('order_id',"asc");
                                        }, 
                                        'purchase_lines.product',
                                        'purchase_lines.product.unit',
                                        'purchase_lines.product.unit.sub_units',
                                        'purchase_lines.variations',
                                        'purchase_lines.variations.product_variation',
                                        'location',
                                        'purchase_lines.sub_unit'
                                        ])
                                        ->first();
        $status            = $purchase->status;
        $TranRecieved      = TransactionRecieved::where("business_id",$business_id)->where("transaction_id",$id)->first();
        $Transaction_store = Transaction::where('business_id', $business_id)
                                            ->where("id",$id)
                                            ->select(["store"])
                                            ->get();
        foreach ($Transaction_store  as  $store) {
            $store_id  = $store["store"];
        }

        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line         = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
        $orderStatuses        = $this->productUtil->orderStatuses();
        $business_locations   = BusinessLocation::forDropdown($business_id, false, true);
        $mainstore            = Warehouse::where('business_id', $business_id)->select(['name','id','status','mainStore','description'])->get();
        $mainstore_categories = [];
        if (!empty($mainstore)) {
            foreach ($mainstore as  $key => $mainstor) {
                if($mainstor->status != 0){
                    $mainstore_categories[$mainstor->id] = $mainstor->name;
                }
                if($mainstor->id == $id){
                }
            }
        }
        
        $bl_attributes           = $business_locations['attributes'];
        $default_purchase_status = null;
        
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
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
        $transaction_recieved = null;
        $product_list         = [];
        $pur                  = \App\PurchaseLine::where("transaction_id",$purchase->id)->get();
        foreach($pur as $it){
            $product_list[] = $it->product_id;
        }
        $Purchaseline         = PurchaseLine::where("transaction_id",$purchase->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(quantity) as total"))->first()->total;
        $RecievedPrevious     = RecievedPrevious::where("transaction_id",$purchase->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
        if($Purchaseline <= $RecievedPrevious){
            $state   = "received";
        }else{
            if($Purchaseline  != $RecievedPrevious){
                if($purchase->status  == "received"){
                    $state   = "final";
                }else{
                    $state   = $purchase->status;
                }        
            }
        }
        $customer_groups  = CustomerGroup::forDropdown($business_id);
        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts        = json_decode($business_details->keyboard_shortcuts, true);
        $cost_centers     =  \App\Account::cost_centers();
        $row = 1;
        $list_of_prices   = \App\Product::getListPrices($row);
        return view('purchase.edit')
            ->with(compact(
                'taxes',
                'list_of_prices',
                'ship_from',
                'cost_centers',
                'purchase',
                'state',
                'currencies',
                'orderStatuses',
                'transaction_recieved',
                'tax_amount',
                'business_locations',
                'business',
                'status',
                'store_id',
                'mainstore_categories',
                'bl_attributes',
                'currency_details',
                'default_purchase_status',
                'TranRecieved',
                'customer_groups',
                'types',
                'shortcuts'
            ));
    }

    /** *F
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('purchase.update') &&!auth()->user()->can('SalesMan.views')&&!auth()->user()->can('admin_supervisor.views')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            //Validate document size
            $request->validate([
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);
            $listOfAccountForUpdate = [];
            $should_update          = 0;
            // ...................................................
            $transaction    = Transaction::findOrFail($id);
            $archive        =  \App\Models\ArchiveTransaction::save_parent($transaction,"edit");
            $purchase_lines = \App\PurchaseLine::where("transaction_id",$transaction->id)->get();
          
            foreach($purchase_lines as $it){
                \App\Models\ArchivePurchaseLine::save_purchases( $archive , $it);
            }
            $old_status             = $transaction->status;
            $old_trans              = $transaction->cost_center_id;
            $old_account            = $transaction->contact_id;
            $old_discount           = $transaction->discount_amount;
            $old_tax                = $transaction->tax_amount;
            $old_document           = $transaction->document; 
            $before_status          = $transaction->status;
            $business_id            = request()->session()->get('user.business_id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');
            $transaction_before     = $transaction->replicate();
            $currency_details       = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            
            
            $update_data            = $request->only([ 'ref_no', 'status', 'contact_id','old_sts',
                                            'transaction_date', 'total_before_tax','store','document','project_no',
                                            'discount_type', 'discount_amount', 'tax_id','supplier_id','sup_id',
                                            'tax_amount', 'ADD_SHIP',"purchase_note",'cost_center_id',
                                            'shipping_charges', 'final_total','sup_ref_no',
                                            'currency_id','currency_id_amount' ,'amount_in_currency',
                                            'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type']
                                        );

            $sup                        = $update_data['supplier_id'];
            $sup_id                     = $update_data['sup_id'];
            $update_data['contact_id']  = $update_data['supplier_id'] ;
            if(\App\Account::find($sup)){
                 $listOfAccountForUpdate[] = \App\Account::find($sup)->id;                                                                   
            }
            $update_data['status']      = ($request->status)?$request->status:$request->old_sts;
            $exchange_rate              = $update_data['exchange_rate'];

            //Reverse exchange rate and save
            //$update_data['exchange_rate'] = number_format(1 / $update_data['exchange_rate'], 2);
            $update_data['transaction_date']    = $this->productUtil->uf_date($update_data['transaction_date'], true);
            //unFormat input values   
            $update_data['total_before_tax']    = $this->productUtil->num_uf($update_data['total_before_tax'], $currency_details) * $exchange_rate;
            
            // If discount type is fixed them multiply by exchange rate, else don't
            if ($update_data['discount_type'] == 'fixed') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details) * $exchange_rate;
            } elseif ($update_data['discount_type'] == 'percentage') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details);
            } else if($update_data['discount_type'] == null){
                $update_data['discount_amount'] = 0;
            } else {
                $update_data['discount_amount'] = $request->discount_amount;
            }
            
    
            $update_data['tax_amount']               = $this->productUtil->num_uf($update_data['tax_amount'], $currency_details) * $exchange_rate;
            $update_data['final_total']              = $this->productUtil->num_uf($update_data['final_total'] + $update_data['ADD_SHIP'], $currency_details) * $exchange_rate;
            $update_data['dis_type']                 = $request->dis_type;
            $update_data["ship_amount"]              = 0;
            $update_data["currency_id"]              = ($request->currency_id!=null)?$request->currency_id:null;
            $update_data["exchange_price"]           = ($request->currency_id!=null)?$request->currency_id_amount:null;
            $update_data["amount_in_currency"]       = ($request->currency_id_amount != "" && $request->currency_id_amount != 0 && $request->currency_id != null)? $request->final_total / $request->currency_id_amount:null;
            // $update_data["exchange_price"]  = $request->currency_id_amount;
            $update_data['discount_type']            = $request->discount_type;
            $update_data['sup_refe']                 = $request->sup_ref_no;
            $update_data["store"]                    = $request->store;
            $update_data['list_price']               = $request->list_price;
            //unFormat input values ends 
            //upload document
            $company_name      = request()->session()->get("user_main.domain");
            if ($request->hasFile('document_purchase')) {
                $id_cv = 1;
                $referencesNewStyle = str_replace('/', '-', $transaction->ref_no);
                foreach ($request->file('document_purchase') as $file) { 
                    #................
                    if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                        if ($file->getSize() <= config('constants.document_size_limit')){ 
                            $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$id_cv++.'_'.$file->getClientOriginalName();
                            $file->move('uploads/companies/'.$company_name.'/documents/purchase',$file_name_m);
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase/'. $file_name_m;
                        }
                    }else{
                        if ($file->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time().'_'.$referencesNewStyle.'_'.$id_cv++.'_'.$file->getClientOriginalName();
                            $Data         = getimagesize($file);
                            $width         = $Data[0];
                            $height        = $Data[1];
                            $half_width    = $width/2;
                            $half_height   = $height/2; 
                            $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase/'. $new_file_name;
                            if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\purchase\\$new_file_name"),20)) {
                                $uploaded_file_name = $new_file_name;
                            }
                                
                        }
                    }
                    #................
                    array_push($old_document,$file_name);
                }
            }

            if(json_encode($old_document)!="[]"){
                $update_data['document']         = json_encode($old_document);
            }
           
            $additional_inputs = $request->only([
                'contact_id','supplier_id','shipping_amount','shipping_vat','shipping_total','shipping_account_id','shiping_text','old_line_currency_id','old_line_currency_id_amount',
                'shiping_date','additional_shipping_item_id','old_shipping_amount','old_shipping_vat','old_shipping_total','old_shipping_account_id','line_currency_id',
                'old_shiping_text','old_shiping_date','old_shipping_contact_id','shipping_contact_id','old_shipping_cost_center_id','cost_center_id','line_currency_id_amount',
                'currency_id','currency_id_amount','add_currency_id','add_currency_id_amount',
            ]);
            $additional_inputs['contact_id'] = $request->supplier_id;
            $document_expense = $request->old_document??[];
            if ($request->hasFile('document_expense')) {
                $id_cc = 1;
                $referencesNewStyle = str_replace('/', '-', $transaction->ref_no);
                foreach ($request->file('document_expense') as $file) {
                    #................
                    if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                        if ($file->getSize() <= config('constants.document_size_limit')){ 
                            $file_name_m    =   time().'_'.$referencesNewStyle.'_Expense_'.$id_cc++.'_'.$file->getClientOriginalName();
                            $file->move('uploads/companies/'.$company_name.'/documents/purchase/expense',$file_name_m);
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase/expense/'. $file_name_m;
                        }
                    }else{
                        if ($file->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time().'_'.$referencesNewStyle.'_Expense_'.$id_cc++.'_'.$file->getClientOriginalName();
                            $Data         = getimagesize($file);
                            $width         = $Data[0];
                            $height        = $Data[1];
                            $half_width    = $width/2;
                            $half_height   = $height/2; 
                            $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase/expense/'. $new_file_name;
                            if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\purchase\\expense\\$new_file_name"),20)) {
                                $uploaded_file_name = $new_file_name;
                            }
                                
                        }
                    }
                    #................
                    array_push($document_expense,$file_name);
                }
            } 
             
            //.. 1   ..........  update expenses 
            \App\Models\AdditionalShipping::update_purchase($transaction->id,$additional_inputs,$document_expense);
            
            DB::beginTransaction();
           
            //....................................  update transaction  ........................
            //..................................................................................
            $transaction->update($update_data);
            $transaction->exchange_price     = ($request->currency_id!=null)?$request->currency_id_amount:null;
            $transaction->amount_in_currency = ($request->currency_id_amount != "" && $request->currency_id_amount != 0 && $request->currency_id != null)? ($update_data['final_total'] / $request->currency_id_amount):null ;
            $transaction->update();
            //..................................................................................
            //..................................................................................
            

            //Update transaction payment status
            $payment_status              = $this->transactionUtil->updatePaymentStatus($transaction->id);
            $transaction->payment_status = $payment_status;
            $purchases                   = $request->input('purchases');
            

            //................................. update  purchase ...................... 
            //......................................................................... 
            $type_update           = "update";
            $delete_purchase_lines = $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing, $before_status,$type_update,null,$archive,$request);
            //......................................................................... 
            //......................................................................... 
          

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines);


            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);


            $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);

            // update accounts 
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            ///................. get children
            $purchase_lines  = PurchaseLine::where('transaction_id', $transaction->id)->get();
            /// search in child's
            foreach ($purchase_lines as $purchase_line) {
                if ( $transaction->status == 'received'  && $old_status != 'received' ) {
                    // .................................................................
                    $id_tr = 0;
                    $trp   = TransactionRecieved::where("transaction_id",$transaction->id)->first();
                    if ( empty($trp) ) {
                        $currency_details             =  $this->transactionUtil->purchaseCurrencyDetails($business_id);
                        $type                         =  'purchase_receive';
                        $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
                        $receipt_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                        $tr_received                  =  new TransactionRecieved;
                        $tr_received->store_id        =  $transaction->store;
                        $tr_received->transaction_id  =  $transaction->id;
                        $tr_received->business_id     =  $transaction->business_id ;
                        $tr_received->reciept_no      =  $receipt_no ;
                        // $tr_received->invoice_no      =  $data->ref_no;
                        $tr_received->ref_no          =  $transaction->ref_no;
                        $tr_received->status          = 'purchase';
                        $tr_received->save();
                        $id_tr                        = $tr_received->id;
                    } else{
                        $id_tr                        = $trp->id;
                    }
                    // ...............................................................*.
                    $prev =  RecievedPrevious::where('transaction_id',$transaction->id)->where('product_id',$purchase_line->product_id)->where("line_id",$purchase_line->id)->first();
                    if (empty($prev)) {
                        $prev                              =  new RecievedPrevious;
                        $prev->product_id                  =  $purchase_line->product_id;
                        $prev->store_id                    =  $transaction->store;
                        $prev->business_id                 =  $transaction->business_id ;
                        $prev->transaction_id              =  $transaction->id;
                        $prev->unit_id                     =  $purchase_line->product->unit_id;
                        $prev->total_qty                   =  $purchase_line->quantity;
                        $prev->current_qty                 =  $purchase_line->quantity;
                        $prev->remain_qty                  =  0;
                        $prev->transaction_deliveries_id   =  $id_tr;
                        $prev->product_name                =  $purchase_line->product->name;  
                        $prev->save();
                    }else{
                        $prev->total_qty                  +=  $purchase_line->quantity;
                        $prev->current_qty                +=  $purchase_line->quantity;
                        $prev->remain_qty                  =  0;
                        $prev->transaction_deliveries_id   =  $id_tr;
                        $prev->store_id                    =  $transaction->store;
                        $prev->product_name                =  $purchase_line->product->name;  
                        $prev->save();
                    }
                    // ...............................................................*. 
                    WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$purchase_line->quantity,$transaction->business_id);
                    \App\MovementWarehouse::movemnet_warehouse($transaction,$purchase_line->product,$purchase_line->quantity,$transaction->store,$purchase_line,'plus',$prev->id);
                    // ................................................................. 
                }elseif ( $transaction->status != 'received'  && $old_status == 'received' ) {
                    WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,($purchase_line->quantity*-1),$transaction->business_id);
                    \App\MovementWarehouse::where('transaction_id',$transaction->id)->delete();
                    RecievedPrevious::where('transaction_id',$transaction->id)->delete();
                }elseif ( $transaction->status == 'received'  && $old_status == 'received' ){
                    // ...................................................................
                    $id_tr = 0; 
                    $trp   = TransactionRecieved::where("transaction_id",$transaction->id)->first();
                    if ( empty($trp) ) {
                        $currency_details             =  $this->transactionUtil->purchaseCurrencyDetails($business_id);
                        $type                         =  'purchase_receive';
                        $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
                        $receipt_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                        $tr_received                  =  new TransactionRecieved;
                        $tr_received->store_id        =  $transaction->store;
                        $tr_received->transaction_id  =  $transaction->id;
                        $tr_received->business_id     =  $transaction->business_id ;
                        $tr_received->reciept_no      =  $receipt_no ;
                        $tr_received->ref_no          =  $transaction->ref_no;
                        $tr_received->status          = 'purchase';
                        $tr_received->save();
                        $id_tr                        = $tr_received->id;
                    } else{
                        $id_tr                        = $trp->id;
                    }
                    // .................................................................*.
                    $prev =  RecievedPrevious::where('transaction_id',$transaction->id)->where('product_id',$purchase_line->product_id)->where("line_id",$purchase_line->id)->first();
                    if (empty($prev)) {
                        $prev                              =  new RecievedPrevious;
                        $prev->product_id                  =  $purchase_line->product_id;
                        $prev->store_id                    =  $transaction->store;
                        $prev->business_id                 =  $transaction->business_id ;
                        $prev->transaction_id              =  $transaction->id;
                        $prev->unit_id                     =  $purchase_line->product->unit_id;
                        $prev->total_qty                   =  $purchase_line->quantity;
                        $prev->current_qty                 =  $purchase_line->quantity;
                        $prev->remain_qty                  =  0;
                        $prev->line_id                     =  $purchase_line->id;/** UPDATE FOR MISTAKE  #$%*/
                        $prev->transaction_deliveries_id   =  $id_tr;
                        $prev->product_name                =  $purchase_line->product->name;  
                        $prev->save();
                        WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$purchase_line->quantity,$transaction->business_id);
                        \App\MovementWarehouse::movemnet_warehouse($transaction,$purchase_line->product,$purchase_line->quantity,$transaction->store,$purchase_line,'plus',$prev->id);
                    }else{
                        $margin     = $purchase_line->quantity - $prev->current_qty;
                        $move_store = \App\MovementWarehouse::where("transaction_id",$transaction->id)->where("purchase_line_id",$purchase_line->id)->where("product_id",$purchase_line->product->id)->first();
                        if($margin != 0){
                           // *** CHECK CHANGE STORE ?!!
                            if($prev->store_id !=  $transaction->store){
                                WarehouseInfo::update_stoct($purchase_line->product_id,$prev->store_id,$prev->current_qty*-1,$transaction->business_id);
                                WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$purchase_line->quantity,$transaction->business_id);
                            }else{
                                WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$margin,$transaction->business_id);
                            } 
                            $prev->total_qty                     +=  $margin;
                            $prev->current_qty                   +=  $margin;
                            $prev->remain_qty                     =  0;
                            $prev->transaction_deliveries_id      =  $id_tr;
                            $prev->store_id                       =  $transaction->store;
                            $prev->product_name                   =  $purchase_line->product->name;  
                            $prev->update(); 
                            if(!empty($move_store)){
                                $before_qty                       =  0;
                                $before                           =  \App\MovementWarehouse::orderBy("id","desc")->where("transaction_id",$transaction->id)->where("product_id",$purchase_line->product->id)->where("store_id",$transaction->store)->where("id","<",$move_store->id)->first();
                                if(!empty($before)){ $before_qty  =  $before->current_qty ; } 
                                $current_qty                      =  $before_qty + $purchase_line->quantity ;
                                $move_store->store_id             =  $transaction->store;
                                $move_store->plus_qty             =  $purchase_line->quantity;
                                $move_store->current_qty          =  $current_qty;
                                $move_store->update();
                            }
                        }else{
                            // *** CHECK CHANGE STORE ?!!
                            if($prev->store_id !=  $transaction->store){
                                WarehouseInfo::update_stoct($purchase_line->product_id,$prev->store_id,$purchase_line->quantity*-1,$transaction->business_id);
                                WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$purchase_line->quantity,$transaction->business_id);
                            } 
                            $prev->store_id            =  $transaction->store;
                            $prev->update();
                            if(!empty($move_store)){
                                $move_store->plus_qty  =  $purchase_line->quantity;
                                $move_store->store_id  =  $transaction->store;
                                $move_store->update();
                            }
                        }
                    }
                }
            }

            //......... for mistakes
            $total_ship       = \App\Models\Purchase::supplier_shipp($transaction->id);

            if (($old_status !=  'final' && $old_status != 'received' ) && ($transaction->status == 'final' ||  $transaction->status == 'received' ) ) {
                ///.... add purchase action 
                \App\AccountTransaction::add_purchase($transaction,$total_ship);
                //.... for map purchase 
                $cost            = 0;
                $without_contact = 0;
                if($request->shipping_amount != null){
                    $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                    $ids       = $data_ship->items->pluck("id");
                    foreach($ids as $i){
                        $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                        if($data_shippment->contact_id == $request->contact_id){ 
                            $cost += $data_shippment->amount;
                        }else{
                            $without_contact += $data_shippment->amount;
                        }
                        \App\Models\StatusLive::insert_data_sh($business_id,$transaction,$data_shippment,"Add Expense");
                    }
                }

            }else if (($old_status ==  'final' || $old_status == 'received' ) && ($transaction->status != 'final' && $transaction->status != 'received' ) ){
                /// ...... Delete  All Actions
                $allCheckAndVoucher = \App\AccountTransaction::where('transaction_id',$transaction->id)->whereNull("check_id")->whereNull("payment_voucher_id")->get();
                foreach($allCheckAndVoucher as $li){
                    $account_check_voucher = $li->account_id;
                    $li->delete();
                    \App\AccountTransaction::nextRecords($account_check_voucher,$transaction->business_id,$transaction->transaction_date);
                }
                /// ...... Delete  All Entries
                \App\Models\Entry::delete_entries($transaction->id);
            } 

            if ($transaction->status ==  'received' || $transaction->status ==  'final' || ($transaction->status == null && $request->old_sts == "final")) {
                //.. 2  ..........  update expenses 
                \App\Models\AdditionalShipping::add_purchase_payment($transaction->id);
              
                if (!(($old_status !=  'final' && $old_status != 'received' ) && ($transaction->status == 'final' ||  $transaction->status == 'received' )) ) {
                    //.. 3 ..........  update expenses 
                    \App\AccountTransaction::update_purchase($transaction,$total_ship,$old_trans,$old_account,$old_discount,$old_tax);
                }
                \App\Models\StatusLive::update_data_p($business_id,$transaction,$request->status);

                $cost=0;$without_contact=0;
                 if($request->old_shipping_amount != null){
                    $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                    $ids = $data_ship->items->pluck("id");
                    foreach($ids as $i){
                        $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                        if($data_shippment->contact_id == $request->contact_id){ 
                            $cost += $data_shippment->amount;
                        }else{
                            $without_contact += $data_shippment->amount;
                        }
                        \App\Models\StatusLive::insert_data_sh($business_id,$transaction,$data_shippment,"Add Expense");
                    }
                }



            }else{ 
                $allShippingLines = \App\AccountTransaction::where('transaction_id',$transaction->id)->whereNotNull('additional_shipping_item_id')->get();
                foreach($allShippingLines as $spl){
                    $account_shipping = $spl->account_id;
                    $spl->delete();
                    \App\AccountTransaction::nextRecords($account_shipping,$transaction->business_id,$transaction->transaction_date);
                }
                \App\Models\Entry::where("account_transaction",$transaction->id)->whereNull("check_id")->whereNull("voucher_id")->delete();
                $shipping_id = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                if(!empty($shipping_id)){
                    \App\Models\Entry::where("shipping_id",$shipping_id->id)->delete();
                }
                \App\Models\StatusLive::where("transaction_id",$transaction->id)->whereNotNull("shipping_item_id")->delete();
                \App\Models\StatusLive::where("transaction_id",$transaction->id)->where("num_serial","!=",1)->delete();
                \App\Models\StatusLive::where("transaction_id",$transaction->id)->update(["state"=>"Purchase ".$transaction->status ]);
            }
            // ** FOR DO THIS CONDITION EVERY TIME UPDATE PURCHASE WITH STATUS "received" FOR UPDATE ITEM MOVEMENT 
            // *** AGT8422 
                if($transaction->status ==  'received' ){
                    $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                    $ids       = $data_ship->items->pluck("id");
                    foreach($ids as $i){
                        $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                        if($data_shippment->contact_id == $request->contact_id){ 
                            $cost            += $data_shippment->amount;
                        }else{
                            $without_contact += $data_shippment->amount;
                        } 
                    }
                    $total_expense = $cost + $without_contact;
                    if ($request->discount_type == "fixed_before_vat"){
                        $dis = $request->discount_amount;
                    }else if ($request->discount_type == "fixed_after_vat"){
                        $tax = \App\TaxRate::find($request->tax_id);
                        $dis = ($request->discount_amount*100)/(100+$tax->amount) ;
                    }else if ($request->discount_type == "percentage"){
                        $dis = ($request->total_before_tax *  $request->discount_amount)/100;
                    }else{
                        $dis = 0;
                    }
                    $before = \App\Models\WarehouseInfo::qty_before($transaction);
                    \App\Models\ItemMove::update_itemMove($transaction,$total_expense,$before,null,$dis);    
                }
            // *************
            // *1* FOR IF CHANGE SUPPLIER 
            // *** AGT8422
                if(app("request")->input("dialog")){
                    if(app("request")->input("check")){
                        if(app("request")->input("check") == 1){
                                $add_ship  = \App\Models\AdditionalShipping::where("transaction_id",$id)->get();
                                foreach($add_ship as $is){
                                    $items = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$is->id)->get();
                                    foreach($items  as $i){
                                        if($i->contact_id == $sup_id ){
                                            $acct = \App\Account::where("contact_id",$sup_id)->first();
                                            $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("additional_shipping_item_id",$i->id)->get();
                                            foreach($account_transaction as $ie){
                                                $account = \App\Account::where("contact_id",$sup)->first();
                                                $ie->account_id = ($account)?$account->id:null; 
                                                $ie->update();
                                                \App\AccountTransaction::nextRecords($acct->id,$business_id,$transaction->transaction_date);
                                                \App\AccountTransaction::nextRecords($account->id,$business_id,$transaction->transaction_date);
                                            }
                                            $i->contact_id = $sup;   
                                            $i->update();
                                        }
                                    }
                                }
                                $payment   = \App\TransactionPayment::where("transaction_id",$id)->get();
                                $array_voucher = [];
                                $array_check   = [];
                                foreach($payment as $it){
                                    if($it->payment_voucher_id != null ){
                                        $array_voucher[]  = $it->payment_voucher_id;
                                    }
                                    if($it->check_id != null ){
                                        $array_check[]  = $it->check_id;
                                    }
                                    $it->payment_for = $sup;
                                    $it->update();
                                }
                                foreach($array_voucher as $it){
                                    $voucher = \App\Models\PaymentVoucher::where("id",$it)->first();
                                    if($voucher){
                                        $acct = \App\Account::where("contact_id",$sup_id)->first();
                                        $voucher->contact_id = $sup; 
                                        $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("payment_voucher_id",$it)->get();
                                    
                                        foreach($account_transaction as $ie){
                                            $account = \App\Account::where("contact_id",$sup)->first();
                                            $ie->account_id = ($account)?$account->id:null; 
                                            $ie->update();
                                            \App\AccountTransaction::nextRecords($acct->id,$business_id,$transaction->transaction_date);
                                            \App\AccountTransaction::nextRecords($account->id,$business_id,$transaction->transaction_date);
                                        }
                                        $voucher->update();
                                    }
                                }
                                foreach($array_check as $i){
                                    $check   = \App\Models\Check::where("id",$i)->first();
                                    if($check){
                                        $acct = \App\Account::where("contact_id",$sup_id)->first();
                                        $new_account = \App\Account::where("contact_id",$sup)->first();
                                        $check->contact_id = ($new_account)?$new_account->id:null; 
                                        $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("check_id",$i)->get();
                                        foreach($account_transaction as $ie){
                                            $account = \App\Account::where("contact_id",$sup)->first();
                                            $ie->account_id = ($account)?$account->id:null; 
                                            $ie->update();
                                            \App\AccountTransaction::nextRecords($acct->id,$business_id,$transaction->transaction_date);
                                            \App\AccountTransaction::nextRecords($account->id,$business_id,$transaction->transaction_date);
                                        }
                                        $check->update();
                                    }
                                }
                        }else {  // *** without payment
                                
                            $add_ship  = \App\Models\AdditionalShipping::where("transaction_id",$id)->get();
                            foreach($add_ship as $is){
                                $items = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$is->id)->get();
                                foreach($items  as $i){
                                    if($i->contact_id == $sup_id ){
                                        $acct = \App\Account::where("contact_id",$sup_id)->first();
                                        $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("additional_shipping_item_id",$i->id)->get();
                                        foreach($account_transaction as $ie){
                                            $account = \App\Account::where("contact_id",$sup)->first();
                                            $ie->account_id = ($account)?$account->id:null; 
                                            $ie->update();
                                            \App\AccountTransaction::nextRecords($acct->id,$business_id,$transaction->transaction_date);
                                            \App\AccountTransaction::nextRecords($account->id,$business_id,$transaction->transaction_date);
                                        }
                                        $i->contact_id = $sup;   
                                        $i->update();
                                    }
                                }
                            }
                            $payment   = \App\TransactionPayment::where("transaction_id",$id)->get();
                            $array_voucher = [];
                            $array_check   = [];
                            foreach($payment as $it){
                                if($it->payment_voucher_id != null ){
                                    $array_voucher[]  = $it->payment_voucher_id;
                                }
                                if($it->check_id != null ){
                                    $array_check[]  = $it->check_id;
                                }
                                $it->payment_for = $sup;
                                $it->update();
                            }
                            foreach($array_voucher as $it){
                                $voucher = \App\Models\PaymentVoucher::where("id",$it)->first();
                                if($voucher){
                                    $acct = \App\Account::where("contact_id",$sup_id)->first();
                                    $voucher->contact_id = $sup; 
                                    $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("payment_voucher_id",$it)->get();
                                
                                    foreach($account_transaction as $ie){
                                        $account = \App\Account::where("contact_id",$sup)->first();
                                        $ie->account_id = ($account)?$account->id:null; 
                                        $ie->update();
                                        \App\AccountTransaction::nextRecords($acct->id,$business_id,$transaction->transaction_date);
                                        \App\AccountTransaction::nextRecords($account->id,$business_id,$transaction->transaction_date);
                                    }
                                    $voucher->update();
                                }
                            }
                            foreach($array_check as $i){
                                $check   = \App\Models\Check::where("id",$i)->first();
                                if($check){
                                    $acct = \App\Account::where("contact_id",$sup_id)->first();
                                    $new_account = \App\Account::where("contact_id",$sup)->first();
                                    $check->contact_id = ($new_account)?$new_account->id:null; 
                                    $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("check_id",$i)->get();
                                    foreach($account_transaction as $ie){
                                        $account = \App\Account::where("contact_id",$sup)->first();
                                        $ie->account_id = ($account)?$account->id:null; 
                                        $ie->update();
                                        \App\AccountTransaction::nextRecords($acct->id,$business_id,$transaction->transaction_date);
                                        \App\AccountTransaction::nextRecords($ie->account_id,$business_id,$transaction->transaction_date);
                                    }
                                    $check->update();
                                }
                            }
                        }
                    } else {
                            
                        $add_ship  = \App\Models\AdditionalShipping::where("transaction_id",$id)->get();
                        foreach($add_ship as $is){
                            $items = \App\Models\AdditionalShippingItem::where("additional_shipping_id",$is->id)->get();
                            foreach($items  as $i){
                                if($i->contact_id == $sup_id ){
                                    $acct = \App\Account::where("contact_id",$sup_id)->first();
                                    $account_transaction = \App\AccountTransaction::where("account_id",($acct)?$acct->id:null)->where("additional_shipping_item_id",$i->id)->get();
                                    foreach($account_transaction as $ie){
                                        $account = \App\Account::where("contact_id",$sup)->first();
                                        $ie->account_id = ($account)?$account->id:null; 
                                        $ie->update();
                                        \App\AccountTransaction::nextRecords($acct->id,$business_id,$transaction->transaction_date);
                                        \App\AccountTransaction::nextRecords($account->id,$business_id,$transaction->transaction_date);
                                    }
                                    $i->contact_id = $sup;   
                                    $i->update();
                                }
                            }
                        }
                        $payment   = \App\TransactionPayment::where("transaction_id",$id)->get();
                        $array_voucher = [];
                        $array_check   = [];
                        foreach($payment as $it){
                            if($it->payment_voucher_id != null ){
                                $array_voucher[]  = $it->payment_voucher_id;
                            }
                            if($it->check_id != null ){
                                $array_check[]  = $it->check_id;
                            }
                        }
                        foreach($array_voucher as $it){
                            $voucher = \App\Models\PaymentVoucher::where("id",$it)->first();
                            if($voucher){
                                $account_transaction = \App\AccountTransaction::where("payment_voucher_id",$it)->get();
                                foreach($account_transaction as $ie){
                                    $ie->transaction_id =  null; 
                                    $ie->update();
                                    \App\AccountTransaction::nextRecords($ie->account_id,$business_id,$transaction->transaction_date);
                                }
                            }
                        }
                        foreach($array_check as $i){
                            $check   = \App\Models\Check::where("id",$i)->first();
                            if($check){
                                $account_transaction = \App\AccountTransaction::where("check_id",$i)->get();
                                foreach($account_transaction as $ie){
                                    $ie->transaction_id = null; 
                                    $ie->update();
                                    \App\AccountTransaction::nextRecords($ie->account_id,$business_id,$transaction->transaction_date);
                                }
                            }
                        }
                        foreach($payment as $it){
                            $it->delete();
                        }
                        $transaction = \App\Transaction::find($id);
                        $total_paid = \App\TransactionPayment::where('transaction_id', $id)
                                                                    ->select(DB::raw('SUM(IF( is_return = 0, amount, amount*-1))as total_paid'))
                                                                    ->first()
                                                                    ->total_paid;
                        $final_amount =  $transaction->final_total;
                        if (is_null($final_amount)) {
                            $final_amount = \App\Transaction::find($id)->final_total;
                        }   
                        $status = 'due';
                        if ($final_amount <= $total_paid) {
                            $status = 'paid';
                        } elseif ($total_paid > 0 && $final_amount > $total_paid) {
                            $status = 'partial';
                        }
                        \App\Transaction::where('id',$id)->update([
                                'payment_status'=>$status
                        ]);


                    }
                }
            // ***********
            DB::commit();

            $output = ['success' => 1,
                            'msg' => __('purchase.purchase_update_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                            // 'msg' => __("messages.something_went_wrong")
                        ];
            return back()->with('status', $output);
        }

        return redirect('purchases')->with('status', $output);
    }

    /** *F
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
                $business_id = request()->session()->get('user.business_id');

                // 1 Check if return exist then not allowed
                if ($this->transactionUtil->isReturnExist($id)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.return_exist')
                    ];
                    return $output;
                }

                // 2 Check if check exist then not allowed
                $checks = \App\Models\Check::where("transaction_id",$id)->first();
                if (!empty($checks)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.sorry_there_is_payment')
                    ];
                    return $output;
                }

                // 3 Check if voucher exist then not allowed
                $payment = \App\TransactionPayment::where("transaction_id",$id)->first();
                if (!empty($payment)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.sorry_there_is_payment')
                    ];
                    return $output;
                }
                // 4 Check if recieved exist then not allowed
                $recieve = \App\Models\RecievedPrevious::where("transaction_id",$id)->first();
                if (!empty($recieve)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.sorry_there_is_recieve')
                    ];
                    return $output;
                }
                // 5 Check if expenses exist then not allowed
                $shipping = \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id){
                                                                        $query->where("transaction_id",$id);
                                                                })->first();
                if (!empty($shipping)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.sorry_there_is_expenses')
                    ];
                    return $output;
                }

                $transaction = Transaction::where('id', $id)
                                ->where('business_id', $business_id)
                                ->with(['purchase_lines'])
                                ->first();
                // 6 Check if lot numbers from the purchase is selected in sale
                if (request()->session()->get('business.enable_lot_number') == 1 && $this->transactionUtil->isLotUsed($transaction)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.lot_numbers_are_used_in_sale')
                    ];
                    return $output;
                }
                
                DB::beginTransaction();
                
                $delete_purchase_lines            = $transaction->purchase_lines;
                $delete_purchase_line_ids         = [];
                foreach ($delete_purchase_lines as $purchase_line) {
                    $delete_purchase_line_ids[]   = $purchase_line->id;
                }
                
                // 1 Delete item in purchase bill
                PurchaseLine::where('transaction_id', $id)
                                    ->whereIn('id', $delete_purchase_line_ids)
                                    ->delete();

                // 2 Delete account transactions
                $all_trs = AccountTransaction::where('transaction_id', $id)->get();
                foreach($all_trs as $line_one){ 
                    $line_one->delete();
                    \App\AccountTransaction::nextRecords($line_one->account_id,$business_id,$transaction->transaction_date);
                }
                // 3 Delete warehouse movement
                \App\MovementWarehouse::where('transaction_id',$id)->delete();
                
                // 4 Delete Item movement
                \App\Models\ItemMove::where('transaction_id',$id)->delete();
                
                // 5 Delete Entry 
                \App\Models\Entry::where('account_transaction',$id)->delete();
                
                // 6 Delete Map status
                \App\Models\StatusLive::where('transaction_id',$id)->delete();
                
                // 7 Delete Shipping expenses
                \App\Models\AdditionalShipping::where('transaction_id',$id)->delete();
                
                // 8 Delete purchase bill
                $transaction->delete();
                
                
                //Delete Entry Not Connection
                // $all_entry = \App\Models\Entry::where("business_id",$business_id)->get();
                // foreach($all_entry as $it){
                //     $tr =  \App\Transaction::find($it->account_transaction);
                //     if(empty($tr)){
                //         $it->delete();
                //     }
                // }

                //Update mapping of purchase & Sell.
                // $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($transaction_status, $transaction, $delete_purchase_lines);


                // //Delete Transaction
                // $previous =  RecievedPrevious::where('transaction_id',$transaction->id)->get();
                // foreach ($previous as $prev) {
                //     WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,($prev->total_qty*-1),$transaction->business_id);
                // }

                DB::commit();

                $output = ['success' => true,
                            'msg' => __('lang_v1.purchase_delete_success')
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }
    

     // ************************************************** ADDITIONAL FINCTIONS *************************************************** \\  
    // *1* FOR SECTION ONE *F
        //  *F
        public function Sup_refe(Request $request){
            $movies = [];
            if($request->has('q')){
                $search = $request->q;
                $trans =Transaction::select("id", "sup_refe")
                		->where('sup_refe', 'LIKE', "%$search%")
                		->get();
            }
            return response()->json($trans);
        }
        //  *F
        public function recieved_page(Request $request){
            
            if (!auth()->user()->can('purchase.recieved') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('SalesMan.views')&& !auth()->user()->can('admin_supervisor.views')  ) {
                abort(403, 'Unauthorized action.');    
            }

            $business_id            = request()->session()->get('user.business_id');
            $business_locations     = BusinessLocation::forDropdown($business_id, false, true);
            $currency_details       = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $quantity_all           = 0;
            $product_list_all       = [];
            $transaction            = Transaction::find($request["id"]);
            $tr                     = \App\Transaction::where("id",$transaction->return_parent_id)->first();
            
            if(!empty($tr)){
                $purchcaseline_return    = PurchaseLine::where('transaction_id', $tr->id)->get();
                $purline_return          = PurchaseLine::where('transaction_id', $tr->id)->select(DB::raw("SUM(quantity_returned) as qty"),DB::raw("COUNT(id) as count"))->first();
            } 

            $purchcaseline           = PurchaseLine::where('transaction_id', $request["id"])->get();
            $purline                 = PurchaseLine::where('transaction_id', $request["id"])->select(DB::raw("SUM(quantity) as qty"),DB::raw("COUNT(id) as count"))->first();
            
            $quantity_all            = $purline->qty;
            foreach($purchcaseline as $pr){
                $product_list_all[]  = $pr->product_id;
            }
            
            $RecievedPrevious        = RecievedPrevious::where('transaction_id', $request["id"])->get();
            $RPrevious               = RecievedPrevious::where('transaction_id', $request["id"])->sum("current_qty");
            $RecievedWrong           = RecievedWrong::where('transaction_id', $request["id"])->get();
            $Warehouse               = Warehouse::where('business_id', $business_id)->get();
            $taxes                   = TaxRate::where('business_id', $business_id) ->ExcludeForTaxGroup()->get();
    

            $count                   = $purline->count;
            $hide                    = (count($RecievedPrevious))? "false" : "true";  

            $bl_attributes           = $business_locations['attributes'];
            $business_locations      = $business_locations['locations'];

            //Accounts
            $accounts                = $this->moduleUtil->accountsDropdown($business_id, true);
            $childs                  =  Warehouse::childs($business_id);
            $array_remain            = [];
            
            $currency           =  \App\Models\ExchangeRate::where("source","!=",1)->get();
            $currencies         = [];
            foreach($currency as $i){
                $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
            }
            if(!empty($tr)){
                $product_list_all_return  =  [] ;
                foreach($purchcaseline_return as $pr){
                    $product_list_all_return[] = $pr->product_id;
                }
                return view('purchase.recieved')
                ->with(compact('childs',
                                'taxes',
                                'purchcaseline' ,
                                'RecievedWrong',
                                'quantity_all',
                                'currencies',
                                'array_remain',
                                'product_list_all_return' ,
                                'RPrevious' ,
                                'purline_return' ,
                                'product_list_all' ,
                                'RecievedPrevious',
                                'hide', 
                                "transaction", 
                                'business_locations', 
                                'currency_details',
                                'accounts', 
                                'bl_attributes'));
            }else{
                return view('purchase.recieved')
                ->with(compact('childs',
                                'taxes',
                                'purchcaseline' ,
                                'RecievedWrong',
                                'RPrevious',
                                'array_remain',
                                'quantity_all',
                                'currencies',
                                'product_list_all' ,
                                'RecievedPrevious',
                                'hide', 
                                "transaction", 
                                'business_locations', 
                                'currency_details',
                                'accounts', 
                                'bl_attributes'));
            }
        }
        // *F
        public function update_recieved(Request $request){
    
            if (!auth()->user()->can('purchase.recieved')  && !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('SalesMan.views') && !auth()->user()->can('admin_without.views') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('manufuctoring.views')  ) {
                abort(403, 'Unauthorized action.');    
            }
            $currency           =  \App\Models\ExchangeRate::where("source","!=",1)->get();
            $currencies         = [];
            foreach($currency as $i){
                $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
            }
            $business_id           = request()->session()->get('user.business_id');
            $business_locations    = BusinessLocation::forDropdown($business_id, false, true);
            $currency_details      = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $quantity_all          = 0;
            $product_list_all      = [];
            $transaction           = Transaction::find($request["id"]);
            $purchcaseline         = PurchaseLine::where('transaction_id', $request["id"])->get();
            $tr                    = \App\Transaction::where("id",$transaction->return_parent_id)->first();
            
            if(!empty($tr)){
                $purchcaseline_return    = PurchaseLine::where('transaction_id', $tr->id)->get();
                $purline_return          = PurchaseLine::where('transaction_id', $tr->id)->sum("quantity_returned");
            }
            
            $purchase              = $transaction ;
            $purline               = PurchaseLine::where('transaction_id', $request["id"])->select(DB::raw("SUM(quantity) as qty"),DB::raw("COUNT(id) as count"))->first();
            $RecievedPrevious      = RecievedPrevious::where('transaction_id', $request["id"])->where("transaction_deliveries_id",$request["trn"])->get();
            $RecPre                = RecievedPrevious::where('transaction_id', $request["id"])->sum("current_qty");
            $RecievedWrong         = RecievedWrong::where('transaction_id', $request["id"])->where("transaction_deliveries_id",$request["trn"])->get();
            $RecWrong              = RecievedWrong::where('transaction_id', $request["id"])->sum("current_qty");
            $Warehouse             = Warehouse::where('business_id', $business_id)->get();
            $taxes                 = TaxRate::where('business_id', $business_id) ->ExcludeForTaxGroup()->get();
    
            foreach($purchcaseline as $pr){
                $product_list_all[] = $pr->product_id;
            }
            
            $quantity_all         = intval($purline->qty); 
            $count                = $purline->count;
            $RecPr                = $RecPre;
            $RecWr                = $RecWrong;
            $hide                 = (count($RecievedPrevious))? "false" : "true";  
            
            $bl_attributes        = $business_locations['attributes'];
            $business_locations   = $business_locations['locations'];
            $ship_from            = 1;
            $transaction_recieved = $request["trn"];
            //Accounts
            $accounts      = $this->moduleUtil->accountsDropdown($business_id, true);
            $childs        =  Warehouse::childs($business_id);
            $array_remain  = [];
            if(!empty($tr)){
                $product_list_all_return = [];
                foreach($purchcaseline_return as $pr){
                $product_list_all_return[] = $pr->product_id;
            }
            return view('purchase.update_recieved')
                ->with(compact('childs',
                                    'taxes',
                                    'purchcaseline' ,
                                    'purchase' ,
                                    'ship_from' ,
                                    'transaction_recieved' ,
                                    'RecievedWrong',
                                    'quantity_all',
                                    'currencies',
                                    'product_list_all' ,
                                    'purchcaseline_return' ,
                                    'product_list_all_return' ,
                                    'purline_return' ,
                                    'array_remain',
                                    'RecievedPrevious',
                                    'hide', 
                                    'RecPr', 
                                    'RecWr', 
                                    "transaction", 
                                    'business_locations', 
                                    'currency_details',
                                    'accounts', 
                                    'bl_attributes'));
                                }else{
                return view('purchase.update_recieved')
                ->with(compact('childs',
                                    'taxes',
                                    'purchcaseline' ,
                                    'purchase' ,
                                    'ship_from' ,
                                    'transaction_recieved' ,
                                    'RecievedWrong',
                                    'quantity_all',
                                    'currencies',
                                    'product_list_all' ,
                                    'array_remain',
                                    'RecievedPrevious',
                                    'hide', 
                                    'RecPr', 
                                    'RecWr', 
                                    "transaction", 
                                    'business_locations', 
                                    'currency_details',
                                    'accounts', 
                                    'bl_attributes'));
            }
        }
        // *F
        public function delivered_page(Request $request)
        {
    
            if (!auth()->user()->can('sell.delivered')  && !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('SalesMan.views') && !auth()->user()->can('admin_without.views') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('manufuctoring.views')   ) {
                abort(403, 'Unauthorized action.');
                
            }
            
            $business_id         = request()->session()->get('user.business_id');
            $currency_data       = session('currency');
            $business_locations  = BusinessLocation::forDropdown($business_id, false, true);
            $quantity_all        = 0;
            $transaction         = Transaction::where('business_id', $business_id)
                                            ->where('id',$request["id"])
                                            ->with(['contact', 'location'])
                                            ->findOrFail($request["id"]);
            $purchcaseline       = PurchaseLine::where('transaction_id', $request["id"])->get();
            $TransactionSellLine = TransactionSellLine::where('transaction_id', $request["id"])->get();
            $RecievedPrevious    = RecievedPrevious::where('transaction_id', $request["id"])->get();
            $product             = Product::where('business_id', $business_id)->get();
            $unit                = Unit::where('business_id', $business_id)->get();

            // $business_locations = BusinessLocation::forDropdown($business_id);
            $Warehouse    = Warehouse::where('business_id', $business_id)->get();
            $product_list = [];
            foreach($product as $prd){
                foreach($TransactionSellLine as $pruche){
                    if($pruche->product_id == $prd->id ){
                        $product_list[$prd->id] = $prd->name;
                        $quantity_all = $pruche->quantity; 
                    }
                } 
            }
            $product_list_all = [];
            foreach($product as $prd){
                    $product_list_all[$prd->id] = $prd->name;
            }
            $Warehouse_list = [];
            foreach($Warehouse as $Ware){
                if($Ware->status != 0){
                    $Warehouse_list[$Ware->id] = $Ware->name;
                }
            }
            $count = 0;
            foreach($purchcaseline as $line){
                $count = $count + 1;
            }
            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                if (!$this->moduleUtil->isSubscribedPermitted($business_id)) {
                    return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
                }
            }
            $taxes                  = TaxRate::where('business_id', $business_id)
                                            ->ExcludeForTaxGroup()
                                            ->get();
            $orderStatuses          = $this->productUtil->orderStatuses();
            $mainstore              = Warehouse::where('business_id', $business_id)->select(['name','id','status','mainStore','description'])->get();
            // $transaction = Transaction::where('id', $request["id"])->get();
            $mainstore_categories   = [];
            if (!empty($mainstore)) {
                foreach ($mainstore as $mainstor) {
                    if($mainstor->status != 0){
                        $mainstore_categories[$mainstor->id] = $mainstor->name;
                    }
                }
            }
            $bl_attributes           = $business_locations['attributes'];
            $business_locations      = $business_locations['locations'];
            $currency_details        = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $default_purchase_status = null;
            
            if (request()->session()->get('business.enable_purchase_status') != 1) {
                $default_purchase_status = 'received';
            }
            
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
            $customer_groups    = CustomerGroup::forDropdown($business_id);
            $business_details   = $this->businessUtil->getDetails($business_id);
            $shortcuts          = json_decode($business_details->keyboard_shortcuts, true);
            $payment_line       = $this->dummyPaymentLine;
            $payment_types      = $this->productUtil->payment_types(null, true);
            //Accounts
            $accounts           = $this->moduleUtil->accountsDropdown($business_id, true);
            return view('sell.delivered')
                ->with(compact('taxes','purchcaseline', 'business_details', 'currency_data', 'TransactionSellLine', 'quantity_all','product_list_all' ,'RecievedPrevious','unit', 'product',"product_list" , "transaction", 'orderStatuses','Warehouse_list', 'business_locations','mainstore_categories', 'currency_details', 'default_purchase_status', 'customer_groups', 'types', 'shortcuts', 'payment_line', 'payment_types', 'accounts', 'bl_attributes'));
        
        }
    // *2* FOR SECTION TWO *F
        // *F
        public function updateStatus(Request $request)
        {
            
            if (!auth()->user()->can('purchase.update') && !auth()->user()->can('purchase.update_status')) {
                abort(403, 'Unauthorized action.');
            }
            
            //Check if the transaction can be edited or not.
            // $edit_days = request()->session()->get('business.transaction_edit_days');
            // if (!$this->transactionUtil->canBeEdited($request->input('purchase_id'), $edit_days)) {
            //     return ['success' => 0,
            //             'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])];
            // }
        
            try {
                $business_id              = request()->session()->get('user.business_id');
                $type_transaction         = (app("request")->input("type"))?"purchase_return":"purchase";
                $transaction              = Transaction::where('business_id', $business_id)
                                                        ->where('type', $type_transaction)
                                                        ->with(['purchase_lines'])
                                                        ->findOrFail($request->input('purchase_id'));
                $transaction_before       =  $transaction->replicate();
                $old_status               =  $transaction->status;
                $before_status            =  $transaction->status;
                $update_data['status']    =  $request->input('status');
                
                DB::beginTransaction();
                $total_ship = \App\Models\Purchase::supplier_shipp($transaction->id);
                
                //update transaction
                $transaction->update($update_data);
                $currency_details          = $this->transactionUtil->purchaseCurrencyDetails($business_id);

                if ( $transaction->status == 'received'  && $old_status != 'received' ) {
                        $currency_details             =  $this->transactionUtil->purchaseCurrencyDetails($business_id);
                        $type                         =  'purchase_receive';
                        $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
                        $receipt_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                        $tr_recieved                  =  new TransactionRecieved;
                        $tr_recieved->store_id        =  $transaction->store;
                        $tr_recieved->transaction_id  =  $transaction->id;
                        $tr_recieved->business_id     =  $transaction->business_id ;
                        $tr_recieved->reciept_no      =  $receipt_no ;
                        $tr_recieved->ref_no          =  $transaction->ref_no;
                        $tr_recieved->status          = (app("request")->input("type")) ? "Return Purchase":"purchase"; 
                        $tr_recieved->is_returned     = (app("request")->input("type")) ? 1:0; 
                        $tr_recieved->save();
                        \App\Models\StatusLive::insert_data_p($business_id,$transaction,$request->status,$tr_recieved);
                } 

                foreach ($transaction->purchase_lines as $purchase_line) {
                    $this->productUtil->updateProductStock($before_status, $transaction, $purchase_line->product_id, $purchase_line->variation_id, $purchase_line->quantity, $purchase_line->quantity, $currency_details);
                    if ( $transaction->status == 'received'  && $old_status != 'received' ) {
                        $prev                      =  RecievedPrevious::where('transaction_id',$transaction->id)
                                                                        ->where('product_id',$purchase_line->product_id)
                                                                        ->where("line_id",$purchase_line->id)
                                                                        ->first();
                        if (empty($prev)) {
                            $prev                              =  new RecievedPrevious;
                            $prev->product_id                  =  $purchase_line->product_id;
                            $prev->store_id                    =  $transaction->store;
                            $prev->business_id                 =  $transaction->business_id ;
                            $prev->transaction_id              =  $transaction->id;
                            $prev->unit_id                     =  $purchase_line->product->unit_id;
                            $prev->total_qty                   =  $purchase_line->quantity;
                            $prev->current_qty                 =  $purchase_line->quantity;
                            $prev->remain_qty                  =  0;
                            $prev->transaction_deliveries_id   =  $tr_recieved->id;
                            $prev->product_name                =  $purchase_line->product->name;  
                            $prev->line_id                     =  $purchase_line->id;  
                            $prev->is_returned                 =  (app("request")->input("type")) ? 1:0; 
                            $prev->save();
                        } 
                        $type_move                             = (app("request")->input("type")) ? "minus":"plus";
                        $qty                                   = (app("request")->input("type")) ? ($purchase_line->quantity*-1):($purchase_line->quantity);
                        WarehouseInfo::update_stoct($purchase_line->product_id,$transaction->store,$qty,$transaction->business_id);
                        \App\MovementWarehouse::movemnet_warehouse($transaction,$purchase_line->product,$purchase_line->quantity,$transaction->store,$purchase_line,$type_move,$prev->id);
                        
                    }elseif ( $transaction->status != 'received'  && $old_status == 'received' ) {
                        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
                        \App\Models\TransactionRecieved::where('transaction_id',$transaction->id)->delete();
                        \App\MovementWarehouse::where('transaction_id',$transaction->id)->delete();
                        RecievedPrevious::where('transaction_id',$transaction->id)->delete();
                    }
                }
                
                if (($old_status !=  'final' && $old_status != 'received' ) &&  ($request->status == 'final' ||  $request->status == 'received' ) ) {
                    if(app("request")->input("type")){
                        $transaction_return = \App\Transaction::find($transaction->id);
                        $purchase_lines     = \App\PurchaseLine::where("transaction_id",$transaction->id)->select(DB::raw("SUM(quantity*purchase_price) as sub_total"))->first();
                        $additional_expense = \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($transaction){
                                                                                        $query->where("transaction_id",$transaction->id);
                                                                                })->where("contact_id",$transaction->contact_id)->sum("total");
                        $fianl_total        = $transaction->final_total - $additional_expense  ;
                        \App\AccountTransaction::return_purchase($transaction_return,$transaction_return->discount_amount, $fianl_total , $purchase_lines->sub_total  ,$transaction_return->tax_amount );
                    }else{
                        \App\AccountTransaction::add_purchase($transaction,$total_ship);
                    }
                }elseif (($old_status ==  'final' || $old_status == 'received' ) &&  ($request->status != 'final' && $request->status != 'received' ) ){
                    $allTransactionRecords = \App\AccountTransaction::where('transaction_id',$transaction->id)->whereNull("check_id")->whereNull("payment_voucher_id")->get(); 
                    foreach($allTransactionRecords as $li){
                        $account_transaction_record = $li->account_id;
                        $action_date                = $li->operation_date;
                        $account_transaction        = \App\Account::find($account_transaction_record); 
                        $li->delete();
                        if($account_transaction->cost_center!=1){
                            \App\AccountTransaction::nextRecords($account_transaction_record,$transaction->business_id,$action_date);
                        }
                    }
                    \App\Models\Entry::delete_entries($transaction->id);
                    \App\Models\ItemMove::delete_move($transaction->id);
                }

                //Update mapping of purchase & Sell.
                $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, null);
                //Adjust stock over selling if found
                $this->productUtil->adjustStockOverSelling($transaction);

                if ($request->status ==  'received' || $request->status ==  'final') {
                    if(app("request")->input("type")){
                        \App\Models\AdditionalShipping::add_purchase_payment($transaction->id,null,null,1);
                    }else{
                        \App\Models\AdditionalShipping::add_purchase_payment($transaction->id);
                    }
                    $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                    $cost=0;$without_contact =0;
                    if(!empty($data_ship)){
                        $ids = $data_ship->items->pluck("id");
                        foreach($ids as $i){
                            $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                            if($data_shippment->contact_id == $request->contact_id){ 
                                $cost += $data_shippment->total;
                            }else{
                                $without_contact += $data_shippment->total;
                            }
                            \App\Models\StatusLive::insert_data_sh($business_id,$transaction,$data_shippment,"Add Expense");
                        }
                    }
                }else{
                    \App\Models\Entry::where("account_transaction",$transaction->id)->whereNull("check_id")->whereNull("voucher_id")->delete();
                    $shipping_id = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                    if(!empty($shipping_id)){
                        \App\Models\Entry::where("shipping_id",$shipping_id->id)->delete();
                    }
                    \App\Models\StatusLive::where("transaction_id",$transaction->id)->whereNotNull("shipping_item_id")->delete();
                    \App\Models\StatusLive::where("transaction_id",$transaction->id)->where("num_serial","!=",1)->delete();
                    \App\Models\StatusLive::insert_data_p($business_id,$transaction,$request->status);
                    $allTransactionRecords  = \App\AccountTransaction::where('transaction_id',$transaction->id)->whereNotNull('additional_shipping_item_id')->get();
                    foreach($allTransactionRecords as $li){
                        $account_transaction_record = $li->account_id;
                        $action_date                = $li->operation_date;
                        $account_transaction        = \App\Account::find($account_transaction_record); 
                        $li->delete();
                        if($account_transaction->cost_center!=1){
                            \App\AccountTransaction::nextRecords($account_transaction_record,$transaction->business_id,$action_date);
                        }
                    }
                }

                if (( $old_status != 'received' ) &&  ( $request->status == 'received' ) ) {
                    $tr             = \App\Transaction::find($transaction->id);
                    $cost=0;$without_contact=0;
                    $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                    if(!empty($data_ship)){
                        $ids = $data_ship->items->pluck("id");
                        foreach($ids as $i){
                            $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                            if($data_shippment->contact_id == $request->contact_id){ 
                                $cost += $data_shippment->amount;
                            }else{
                                $without_contact += $data_shippment->amount;
                            }
                        }
                    }
                    $total_expense = $cost + $without_contact; 
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
                    if(app("request")->input("type")){
                        \App\Models\ItemMove::return_recieve($transaction,$tr_recieved->id);
                    }else{
                        $before = \App\Models\WarehouseInfo::qty_before($transaction);
                        \App\Models\ItemMove::update_itemMove($transaction,$total_expense,$before,null,$dis);
                    }
                }
                if (($old_status !=  'final' && $old_status != 'received' ) &&  ($request->status == 'final' ||  $request->status == 'received' ) ) {
                    if(app("request")->input("type")){
                        $type="PReturn";
                        \App\Models\Entry::create_entries($transaction,$type,null,null,null,null,$transaction->id);
                        $entry    = \App\Models\Entry::orderBy("id","desc")->where('account_transaction',$transaction->id)->first();
                        if(!empty($entry)){
                            $accountTransaction = \App\AccountTransaction::where("transaction_id",$transaction->id)->get();
                            foreach($accountTransaction as $it){
                                $account_transaction_record = $it->account_id;
                                $action_date                = $it->operation_date;
                                $account_transaction        = \App\Account::find($it->account_id);
                                $it->entry_id               = ($entry)? $entry->id:null;
                                $it->update();
                                if($account_transaction->cost_center!=1){
                                    \App\AccountTransaction::nextRecords($account_transaction_record,$transaction->business_id,$action_date);
                                }
                            }
                        }
                    }
                }
                
                $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);
                $archive         =  \App\Models\ArchiveTransaction::save_parent($transaction,"edit");
                $purchase_lines  =  \App\PurchaseLine::where('transaction_id', $transaction->id)->get();
                foreach($purchase_lines as $it){
                    \App\Models\ArchivePurchaseLine::save_purchases($archive , $it);
                }
            DB::commit();
                $output = ['success' => 1,
                                'msg' => __('purchase.purchase_update_success')
                            ];
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = ['success' => 0,
                        // 'msg' =>  __("messages.something_went_wrong")
                        'msg' =>  "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                    ];
            }
            if(app("request")->input("type")){
                return redirect()->back()->with("staus",$output);
            }else{
                return $output;
            }
        }
        // *F
        public function check_curr_account(Request $request){
            $id=$request->id;
        
            $data= \DB::table('accounts')->where('id',$id)->get();
        
            return json_encode($data[0]);
        }
        
    // *3* FOR SECTION THREE *F
        /** *F
         * Retrieves products list.
         *
         * @return \Illuminate\Http\Response
         */
        public function getPurchaseEntryRow(Request $request)
        {
            if (request()->ajax()) {
                // #6-8-2024
                $send            = (app('request')->input('type_send'))?app('request')->input('type_send'):null;
                $contact_id      = (app('request')->input('contact_id'))?app('request')->input('contact_id'):null;
                $product_id      = $request->input('product_id');
                $variation_id    = $request->input('variation_id');
                $global_price    = $request->input('global_price');
                $currency        = $request->input('currency');
                $transaction_id  = (app('request')->input('transaction_id'))?app('request')->input('transaction_id'):null;
                $business_id     = request()->session()->get('user.business_id');
                $location_id     = $request->input('location_id');
                $return_type     = $request->input('return_type');
                $total_remain    = $request->input('total_remain');
                $type_received   = null;
                
                
                $hide_tax = 'hide';
                if ($request->session()->get('business.enable_inline_tax') == 1) {
                    $hide_tax = '';
                }
            
                $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
                $business   = \App\Business::find($business_id);
                if (!empty($product_id)) {
                    
                    $row_count = $request->input('row_count');
                    $product   = Product::where('id', $product_id)
                                            ->with(['unit'])
                                            ->first();
                    $unit_main = $product->unit_id ;
                    $query     = Variation::where('product_id', $product_id)
                                    ->with([
                                        'product_variation', 
                                        'variation_location_details' => function ($q) use ($location_id) {
                                            $q->where('location_id', $location_id);
                                        }
                                    ]);
                    if ($variation_id !== '0' ) {

                        $price_from_table = \App\Variation::find($variation_id);
                        if($price_from_table == null){ $price_from_table = \App\Variation::where('product_id',$variation_id)->first(); }else{
                            $query->where('id', $variation_id);
                        }
                        $row_price        = \App\Models\ProductPrice::where("unit_id",$unit_main)   
                                                                    ->where("product_id",$product->id)
                                                                    ->where("number_of_default",0)
                                                                    ->where("variations_value_id",$price_from_table->variation_value_id)
                                                                    ->where("variations_template_id",$price_from_table->product_variation->variation_template_id)
                                                                    ->first();
                        
                        $var              =  ($row_price)?$row_price->default_purchase_price:0;
                    }else{
                        $var              =  $product->variations->first();
                        $var              =  ($var)?$var->default_purchase_price:0;
                    }

                    $allUnits       = [];
                    $allUnits[$product->unit_id] = [
                        'name'          => $product->unit->actual_name,
                        'multiplier'    => $product->unit->base_unit_multiplier,
                        'allow_decimal' => $product->unit->allow_decimal,
                        'price'         => $var,
                        'check_price'   => $business->default_price_unit,
                        ];
                    // $sub_units      = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);
                    // foreach($sub_units as $k => $line){
                    //     $allUnits[$k] =  $line; 
                    // }
                
                    if($product->sub_unit_ids != null){
                        foreach($product->sub_unit_ids  as $i){
                                $row_price    =  0;
                                $un           = \App\Unit::find($i);
                                $row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$product->id)->where("number_of_default",0)->first();
                                $row_price    = ($row_price)?$row_price->default_purchase_price:0;
                                $allUnits[$i] = [
                                    'name'          => $un->actual_name,
                                    'multiplier'    => $un->base_unit_multiplier,
                                    'allow_decimal' => $un->allow_decimal,
                                    'price'         => $row_price,
                                    'check_price'   => $business->default_price_unit,
                                ] ;
                            }
                    }
                    $sub_units = $allUnits  ;
                    

                    if($contact_id != null){
                        $item_price  =  \App\PurchaseLine::orderBy("id","desc")->whereHas("transaction",function($query) use($contact_id){
                                                                            $query->where("contact_id",$contact_id);
                                                                        })->where("product_id",$product_id)->select(["purchase_price","purchase_price_inc_tax"])->first(); 
                    }else{
                        $item_price = null ;
                    }
                    $variations  = $query->get();
                    $taxes       = TaxRate::where('business_id', $business_id)
                                                ->ExcludeForTaxGroup()
                                                ->get();
                    $childs      =  Warehouse::childs($business_id);
                    $page_type   = 'add_page';
                    if($transaction_id != null){
                        if($return_type == "return_type"){
                            $tr          = \App\Transaction::where("id",$transaction_id)->first();
                            $return_id   = \App\Transaction::where("id",$tr->return_parent_id)->first(); 
                            $cost        = \App\Product::product_cost_purchase($product_id,$return_id->id,"return");
                        }else{
                            $cost        = \App\Product::product_cost_purchase($product_id,$transaction_id);
                        }
                    
                    }else{
                        $cost        = \App\Product::product_cost($product_id);
                    }
                    $check_cost = 0;
                    if($cost == 0){
                        $move_row = \App\Models\ItemMove::where("product_id",$product_id)->first();
                        if(empty($move_row)){
                            $check_cost = 1;
                        }
                    }
                    $reamain = null; 
                    if($total_remain != null){
                        $Purchaseline     = PurchaseLine::where("transaction_id",$transaction_id)->where("product_id",$product_id)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                        $RecievedPrevious = RecievedPrevious::where("transaction_id",$transaction_id)->where("product_id",$product_id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                        $wrong            = RecievedWrong::where("transaction_id",$transaction_id)->where("product_id",$product_id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                        $margin           = $Purchaseline - $RecievedPrevious ;
                        if($margin > 0){
                            $reamain = $margin ;
                        }elseif($margin < 0){
                            $reamain = null;
                        }elseif($margin == 0){
                            $reamain = null;
                        }
                    }
                    if($send != null){
                        $type_received = $send;
                    }
                    $row            = 1;$line_prices  = [];#2024-8-6
                    $list_of_prices         = \App\Product::getListPrices($row);
                    $list_of_prices_in_unit = \App\Product::getProductPrices($product_id);
                    return view('purchase.partials.purchase_entry_row')
                        ->with(compact(
                            'product',
                            'list_of_prices',
                            'list_of_prices_in_unit',
                            'variations',
                            'global_price',
                            'row_count',
                            'reamain',
                            'type_received',
                            'item_price',
                            'variation_id',
                            'currency',
                            'taxes',
                            'cost',
                            'send',
                            'check_cost',
                            'currency_details',
                            'hide_tax',
                            'sub_units',
                            'childs',
                            'page_type'
                        ));
                }
            }
        }
        //.................................... 19-12-2022
        //*** ....... eb */
        /** *F
         * Retrieves products list.
         *
         * @return \Illuminate\Http\Response
         */
        public function getPurchaseEntryRow_open(Request $request , $open, $edit=null)
        {
            if (request()->ajax()) {
                $send             = null;
                $global_price     = $request->input('global_price');
                $product_id       = $request->input('product_id');
                $variation_id     = $request->input('variation_id');
                $business_id      = request()->session()->get('user.business_id');
                $location_id      = $request->input('location_id');
              
                $hide_tax         = 'hide';
                if ($request-> session()->get('business.enable_inline_tax') == 1) {
                    $hide_tax = '';
                }

                $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
                $business   = \App\Business::find($business_id);
                if (!empty($product_id)) {
                    $var            = 0;
                    $row_count      = $request->input('row_count');
                    $product        = Product::where('id', $product_id)
                                        ->with(['unit'])
                                        ->first();
                    $product_type   = $product->type;
                    
                    // $sub_units      = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);
                    // foreach($sub_units as $k => $line){
                    //     $allUnits[$k] =  $line; 
                    // }
                   
                    $query          = Variation::where('product_id', $product_id)
                                        ->with([
                                            'product_variation', 
                                            'variation_location_details' => function ($q) use ($location_id) {
                                                $q->where('location_id', $location_id);
                                            }
                                        ]);
                    if ($variation_id !== '0') {
                        $query->where('id', $variation_id);
                        
                    } 
                     
                    $var            = $product->variations->first();
                    $var            = ($var)?$var->default_purchase_price:0;
                    

                    
                    $allUnits       = [];
                    $allUnits[$product->unit_id] = [
                        'name' => $product->unit->actual_name,
                        'multiplier' => $product->unit->base_unit_multiplier,
                        'allow_decimal' => $product->unit->allow_decimal,
                        'price' => $var,
                        'check_price' => $business->default_price_unit,
                        ];
                  
                   
                    if($product->sub_unit_ids != null){
                        foreach($product->sub_unit_ids  as $i){
                                $row_price    =  0;
                                $un           = \App\Unit::find($i);
                                $row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$product->id)->where("number_of_default",0)->first();
                                $row_price    = ($row_price)?$row_price->default_purchase_price:0;
                                $allUnits[$i] = [
                                    'name'          => $un->actual_name,
                                    'multiplier'    => $un->base_unit_multiplier,
                                    'allow_decimal' => $un->allow_decimal,
                                    'price'         => $row_price,
                                    'check_price'   => $business->default_price_unit,
                                ] ;
                            }
                    } 
                    
                    $sub_units        = $allUnits  ;


                    $currency       = $request->input('currency');
                    $variations     =  $query->get();
                    $cost           = \App\Product::product_cost($product_id);
                    $check_cost = 0;
                    if($cost == 0){
                        $move_row = \App\Models\ItemMove::where("product_id",$product_id)->first();
                        if(empty($move_row)){
                            $check_cost = 1;
                        }
                    }
                    
                    $taxes          = TaxRate::where('business_id', $business_id)
                                                ->ExcludeForTaxGroup()
                                                ->get();
                    $childs         =  Warehouse::childs($business_id);
                    $row            = 1;$line_prices  = [];#2024-8-6
                    $list_of_prices         = \App\Product::getListPrices($row);
                    $list_of_prices_in_unit = \App\Product::getProductPrices($product_id);
                
                    $page_type      = 'add_page';
                    return view('purchase.partials.purchase_entry_row')
                        ->with(compact(
                            'product',
                            'open',
                            'list_of_prices',
                            'check_cost',
                            'global_price',
                            'cost',
                            'variations',
                            'list_of_prices_in_unit',
                            'row_count',
                            'variation_id',
                            'taxes',
                            'currency',
                            'send',
                            'currency_details',
                            'hide_tax',
                            'sub_units',
                            'childs',
                            'page_type'
                        ));
                    
                
                }
            }
        }
        //**................................ ******** 
        //.................................... 19-12-2022
        //*** ....... eb */
        /** *F
        * Retrieves products list.
        *
        * @return \Illuminate\Http\Response
        */
        public function getProductsOpen($open,$edit_open = null)
        {
            if (request()->ajax()) {
                $term                  = request()->term;
                $check_enable_stock    = true;

                if (isset(request()->check_enable_stock)) {
                    $check_enable_stock = filter_var(request()->check_enable_stock, FILTER_VALIDATE_BOOLEAN);
                }
                $only_variations = false;
                if (isset(request()->only_variations)) {
                    $only_variations = filter_var(request()->only_variations, FILTER_VALIDATE_BOOLEAN);
                }
                if (empty($term)) {
                    return json_encode([]);
                }

                

                $business_id = request()->session()->get('user.business_id');
                $q = Product::leftJoin('variations','products.id','=','variations.product_id')
                    ->where(function ($query) use ($term) {
                        $query->where('products.name', 'like', '%' . $term .'%');
                        $query->orWhere('sku', 'like', '%' . $term .'%');
                        $query->orWhere('sku2', 'like', '%' . $term .'%');
                        $query->orWhere('sub_sku', 'like', '%' . $term .'%');
                    })
                    ->active()
                    ->where('business_id', $business_id)
                    ->whereNull('variations.deleted_at')
                    ->select(
                        'products.id as product_id',
                        'products.name',
                        'products.type',
                        'products.business_id as business_id',
                        'products.expiry_period_type',#2024-8-6
                        // 'products.sku as sku',
                        'variations.id as variation_id',
                        'variations.name as variation',
                        'variations.sub_sku as sub_sku'
                    )
                    ->groupBy('variation_id');
                    
                if ($check_enable_stock) {
                    $q->where('enable_stock', 1);
                }
                
                if (!empty(request()->location_id)) {
                    $q->ForLocation(request()->location_id);
                }
            
                $products       = $q->get();
              
                $products_array = [];
                foreach ($products as $product) {
                    $products_array[$product->product_id]['expiry_period_type'] = $product->expiry_period_type;
                    $products_array[$product->product_id]['name'] = $product->name;
                    $products_array[$product->product_id]['sku'] = $product->sub_sku;
                    $products_array[$product->product_id]['type'] = $product->type;
                    $products_array[$product->product_id]['variations'][]
                    = [
                        'variation_id' => $product->variation_id,
                        'variation_name' => $product->variation,
                        'sub_sku' => $product->sub_sku
                        ];
                }

                $result        = [];
                $i             = 1;
                $no_of_records = $products->count();
                if (!empty($products_array)) {
                    foreach ($products_array as $key => $value) {
                        if ($no_of_records > 1 && $value['type'] != 'single' && !$only_variations) {
                            $result[] = [ 'id' => $i,
                                            "open" => $open,    
                                        'text' => $value['name'] . ' - ' . $value['sku'],
                                        'variation_id' => 0,
                                        'product_id' => $key,
                                        'expiry_period_type' => $value['expiry_period_type'],
                                        'session' => session('business.enable_product_expiry')
                                    ];
                        }
                        $name = $value['name'];
                        foreach ($value['variations'] as $variation) {
                            $text = $name;
                            if ($value['type'] == 'variable') {
                                $text = $text . ' (' . $variation['variation_name'] . ')';
                            }
                            $i++;
                            $result[] = [ 'id' => $i,
                                            "open" => $open,
                                            "edit" => $edit_open,
                                                'text' => $text . ' - ' . $variation['sub_sku'],
                                                'product_id' => $key ,
                                                'variation_id' => $variation['variation_id'],
                                                'expiry_period_type' => $value['expiry_period_type'],
                                                'session' => session('business.enable_product_expiry')
                                            ];
                        }
                        $i++;
                    }
                }
                if($edit_open != null){
                    return json_encode($result)  ;
                }else{
                    return json_encode($result);
                }
            }
        }
        //**................................ ********

    // *4* FOR SECTION FOURTH *F
        // payment_msg *F
        public function payment_msg()
        {
            return view("alerts.transfer_payment_attention");
        }
    // *5* FOR SECTION FIVTH *F
        //  *F
        public function get_purchase_pay_default(Request $request){
            $business_id = request()->session()->get('user.business_id');
        
            $location=DB::table('business_locations')->where('business_id',$business_id)->where('id',$request->location_id)->first();
            
            $data=json_decode($location->default_payment_accounts,true);
            
            if(isset($data['purchase']['account']) && $data['purchase']['account']>1){
            
                $account_default=\DB::table('accounts')->where('id',$data['purchase']['account'])->first();
                $html='
                    <option selected value='.$account_default->id.'>'.$account_default->name.'</option>
                ';
                return $html;
            }else{
                return 0;
            }
            return 0; 
        }  
    // *6* FOR SECTION SIXTH *F
        /** *F
         * Retrieves supliers list.
         *
         * @return \Illuminate\Http\Response
         */
        public function getSuppliers()
        {
            if (request()->ajax()) {
                
                $term = request()->q;
                
                if (empty($term)) {
                    return json_encode([]);
                }

                $business_id = request()->session()->get('user.business_id');
                $user_id     = request()->session()->get('user.id');
                $query       = Contact::where('business_id', $business_id)
                                    ->active();

                $selected_contacts = User::isSelectedContacts($user_id);
                if ($selected_contacts) {
                    $query->join('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')
                    ->where('uca.user_id', $user_id);
                }
                $suppliers = $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . $term .'%')
                                    ->orWhere('supplier_business_name', 'like', '%' . $term .'%')
                                    ->orWhere('contacts.contact_id', 'like', '%' . $term .'%');
                })
                            ->select(
                                'contacts.id', 
                                'name as text', 
                                'supplier_business_name as business_name', 
                                'contacts.mobile',
                                'contacts.prefix',
                                'contacts.supplier_business_name',
                                'contacts.first_name',
                                'contacts.middle_name',
                                'contacts.last_name',
                                'contacts.tax_number',
                                'contacts.address_line_1',
                                'contacts.address_line_2',
                                'contacts.city',
                                'contacts.state',
                                'contacts.country',
                                'contacts.zip_code',
                                'contact_id', 
                                'contacts.pay_term_type', 
                                'contacts.pay_term_number', 
                                'contacts.balance'
                            )
                            ->onlySuppliers()
                            ->get();
                            
                foreach($suppliers as $key => $row){
                    $start_date='1970-01-01';
                    $end_date=date("Y-m-d");
                    $ledger_details = $this->transactionUtil->getLedgerDetails($row->id, $start_date, $end_date);
                    $suppliers[$key]->balance_due =  $ledger_details['balance_due'];
                }            
                            
                return json_encode($suppliers);
            }
        }
        /** *F
         * Retrieves products list.
         *
         * @return \Illuminate\Http\Response
         */
        public function getProducts()
        {
            if (request()->ajax()) {
                
                $term = request()->term;

                $check_enable_stock = true;
                if (isset(request()->check_enable_stock)) {
                    $check_enable_stock = filter_var(request()->check_enable_stock, FILTER_VALIDATE_BOOLEAN);
                }
                $only_variations = false;
                if (isset(request()->only_variations)) {
                    $only_variations = filter_var(request()->only_variations, FILTER_VALIDATE_BOOLEAN);
                }

                if (empty($term)) {
                    return json_encode([]);
                }

                $business_id = request()->session()->get('user.business_id');
                $q = Product::leftJoin('variations','products.id','=','variations.product_id')
                    ->where(function ($query) use ($term) {
                        $query->where('products.name', 'like', '%' . $term .'%');
                        $query->orWhere('sku', 'like', '%' . $term .'%');
                        $query->orWhere('sku2', 'like', '%' . $term .'%');
                        $query->orWhere('sub_sku', 'like', '%' . $term .'%');
                    })
                    ->active()
                    ->where('business_id', $business_id)
                    ->whereNull('variations.deleted_at')
                    ->select(
                        'products.id as product_id',
                        'products.name',
                        'products.type',
                        // 'products.sku as sku',
                        'variations.id as variation_id',
                        'variations.name as variation',
                        'variations.sub_sku as sub_sku'
                    )
                    ->groupBy('variation_id');
                if ($check_enable_stock) {
                    $q->where('enable_stock', 1);
                }
                if (!empty(request()->location_id)) {
                    $q->ForLocation(request()->location_id);
                }
                $products = $q->get();
                    
                $products_array = [];
                foreach ($products as $product) {
                    $products_array[$product->product_id]['name'] = $product->name;
                    $products_array[$product->product_id]['sku'] = $product->sub_sku;
                    $products_array[$product->product_id]['type'] = $product->type;
                    $products_array[$product->product_id]['variations'][]
                    = [
                            'variation_id' => $product->variation_id,
                            'variation_name' => $product->variation,
                            'sub_sku' => $product->sub_sku
                            ];
                }
                $result        = [];
                $i             = 1;
                $no_of_records = $products->count();
                if (!empty($products_array)) {
                    foreach ($products_array as $key => $value) {
                        if ($no_of_records > 1 && $value['type'] != 'single' && !$only_variations) {
                            $result[] = [ 'id' => $i,
                                        'text' => $value['name'] . ' - ' . $value['sku'],
                                        'variation_id' => 0,
                                        'product_id' => $key
                                    ];
                        }
                        $name = $value['name'];
                        foreach ($value['variations'] as $variation) {
                            $text = $name;
                            if ($value['type'] == 'variable') {
                                $text = $text . ' (' . $variation['variation_name'] . ')';
                            }
                            $i++;
                            $result[] = [ 'id' => $i,
                                                'text' => $text . ' - ' . $variation['sub_sku'],
                                                'product_id' => $key ,
                                                'variation_id' => $variation['variation_id'],
                                            ];
                        }
                        $i++;
                    }
                }
                
                return json_encode($result);
            }
        }
        /** *F
         * Checks if ref_number and supplier combination already exists.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function checkRefNumber(Request $request)
        {
            $business_id     = $request->session()->get('user.business_id');
            $contact_id      = $request->input('contact_id');
            $ref_no          = $request->input('ref_no');
            $purchase_id     = $request->input('purchase_id');

            $count = 0;
            if (!empty($contact_id) && !empty($ref_no)) {
                //check in transactions table
                $query = Transaction::where('business_id', $business_id)
                                ->where('ref_no', $ref_no)
                                ->where('contact_id', $contact_id);
                if (!empty($purchase_id)) {
                    $query->where('id', '!=', $purchase_id);
                }
                $count = $query->count();
            }
            if ($count == 0) {
                echo "true";
                exit;
            } else {
                echo "false";
                exit;
            }
        }
        /** *F
         * Checks if ref_number and supplier combination already exists.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function printInvoice($id)
        {
            try {
                $business_id = request()->session()->get('user.business_id');
                $taxes       = TaxRate::where('business_id', $business_id)
                                    ->pluck('name', 'id');
                $purchase    = Transaction::where('business_id', $business_id)
                                        ->where('id', $id)
                                        ->with(
                                            'contact',
                                            'purchase_lines',
                                            'purchase_lines.product',
                                            'purchase_lines.variations',
                                            'purchase_lines.variations.product_variation',
                                            'location',
                                            'payment_lines'
                                        )
                                        ->first();
                $payment_methods = $this->productUtil->payment_types(null, false, $business_id);
                $output = ['success' => 1, 'receipt' => []];
                $output['receipt']['html_content'] = view('purchase.partials.show_details', compact('taxes', 'purchase', 'payment_methods'))->render();
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = ['success' => 0,
                                'msg' => __('messages.something_went_wrong')
                            ];
            }

            return $output;
        }
        
        /**
         * Get Balance For all Account 
         * @return null
         */
        public function getAccountBalance(Request $request) {
            try{
                
                    $id            = request()->input('id');
                    $business_id   = request()->input('business_id');
                    // dd($list);
                    // foreach($list as $i){
                        \App\AccountTransaction::oldBalance(0,$id,$business_id,date('Y-m-d'));
                    // }
                    return true;
                
            }catch(\Exception $e){
                return false;
            }
        }

        public function changeAccountExpense(Request $request) {
            try{ 
                 if(request()->ajax()){
                     $old = request()->input('old_account');
                     $new = request()->input('new_account');
                     $all_list = []; 
                     $old_accounts = \App\AccountTransaction::whereHas("transaction",function($query){
                                                 $query->whereIn("type",["purchase","purchase_return"]);
                                         })
                                         ->whereNotIn("note",["Add Purchase","Return purchase"])
                                         ->where("account_id",$old)
                                         ->get();
                      
                     foreach($old_accounts as $item){
                         $item->account_id = $new ;
                         $item->update();
                     }
                     
                     $output = [
                         "success" => true,
                         "msg" => __("Change Account Successfullty"),
                     ];
                 }
             }catch(Exception $e){
                 $output = [
                     "success" => false,
                     "msg" => __("Change Account Faild"),
                 ];
             }
                 return $output;
        }
}