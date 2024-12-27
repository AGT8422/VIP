<?php

namespace App\Http\Controllers;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\CustomerGroup;
use App\InvoiceScheme;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TransactionSellLine;
use App\TypesOfService;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Warranty;
use App\Models\WarehouseInfo;
use App\Models\Agent;
use App\Models\Warehouse;
use App\Models\DeliveredPrevious;
use App\Models\DeliveredWrong;

use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Product;
use App\Media;
use Spatie\Activitylog\Models\Activity;

class SellController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;


    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;

        $this->dummyPaymentLine = ['method' => '', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
        'is_return' => 0, 'transaction_no' => ''];

        $this->shipping_status_colors = [
            'ordered' => 'bg-yellow',
            'packed' => 'bg-info',
            'shipped' => 'bg-navy',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
        ];
    }
    public function Project_no(Request $request)
    {
        $movies = [];
        // DD($request);
        if($request->has('q')){
            $search = $request->q;
            $trans =Transaction::select("id", "project_no")
            		->where('project_no', 'LIKE', "%$search%")
            		->get();
        }
        return response()->json($trans);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view' , 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');
        $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $users  = [] ;
        $us               = User::where('business_id', $business_id)
                                        ->where('is_cmmsn_agnt', 1)->get();

        $patterns  = [];
        $patterns_ = \App\Models\Pattern::select()->get();
        foreach($patterns_ as $it){
                $patterns[$it->id] = $it->name;
        }
        foreach($us as $it){
            $users[$it->id] = $it->first_name;
        }

        if (request()->ajax()) {
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $sale_type = !empty(request()->input('sale_type')) ? request()->input('sale_type') : 'sale';

            $styl = request()->input('types');
            $sells = $this->transactionUtil->getListSells($business_id, $sale_type,$styl);

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

            

            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (!auth()->user()->can('direct_sell.access')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }

                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }



            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $sells->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $sells->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }
            $sells->where('transactions.ecommerce', 0);
            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (!empty(request()->input('rewards_only')) && request()->input('rewards_only') == true) {
                $sells->where(function ($q) {
                    $q->whereNotNull('transactions.rp_earned')
                        ->orWhere('transactions.rp_redeemed', '>', 0);
                });
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            //Check is_direct sell
            if (request()->has('is_direct_sale')) {
                $is_direct_sale = request()->is_direct_sale;
                if ($is_direct_sale == 0) {
                    $sells->where('transactions.is_direct_sale', 0);
                    $sells->whereNull('transactions.sub_type');
                }
            }

            //Add condition for commission_agent,used in sales representative sales with commission report
            if (request()->has('commission_agent')) {
                $commission_agent = request()->get('commission_agent');
                if (!empty($commission_agent)) {
                    $sells->where('transactions.commission_agent', $commission_agent);
                }
            }

            if ($is_woocommerce) {
                $sells->addSelect('transactions.woocommerce_order_id');
                if (request()->only_woocommerce_sells) {
                    $sells->whereNotNull('transactions.woocommerce_order_id');
                }
            }
            if(!auth()->user()->can("warehouse.views")){
                $os_check =  \App\User::where('id',auth()->id())->whereHas('roles',function($query){
                    $query->where('id',1);
                })->first();
                if(empty($os_check)) {
                        $users =  \App\User::where('id',auth()->id())->first();
                        if(!empty($users)) {
                            $patterns = json_decode($users->pattern_id);
                            $sells->whereIn('transactions.pattern_id',$patterns);
                        }
                 }
            }
            if (request()->only_subscriptions) {
                $sells->where(function ($q) {
                    $q->whereNotNull('transactions.recur_parent_id')
                        ->orWhere('transactions.is_recurring', 1);
                });
            }

            if (!empty(request()->list_for) && request()->list_for == 'service_staff_report') {
                $sells->whereNotNull('transactions.res_waiter_id');
            }
            if (!empty(request()->project_no)) {
                
                $sells->where('transactions.project_no', request()->project_no);
            }

            if (!empty(request()->res_waiter_id)) {
                $sells->where('transactions.res_waiter_id', request()->res_waiter_id);
            }
             if (!empty(request()->agent_id)) {
                $sells->where('transactions.agent_id', request()->agent_id);
            }
            if (!empty(request()->cost_center_id)) {
                $sells->where('transactions.cost_center_id', request()->cost_center_id);
            }
            if (!empty(request()->input('sub_type'))) {
                $sells->where('transactions.sub_type', request()->input('sub_type'));
            }

            if (!empty(request()->input('created_by'))) {
                $sells->where('transactions.created_by', request()->input('created_by'));
            }
            //.........

            if (!empty(request()->input('status'))) {
                $sells->where('transactions.status', request()->input('status'));
            }

            if (!empty(request()->input('sales_cmsn_agnt'))) {
                $sells->where('transactions.commission_agent', request()->input('sales_cmsn_agnt'));
            }

            if (!empty(request()->input('service_staffs'))) {
                $sells->where('transactions.res_waiter_id', request()->input('service_staffs'));
            }
            $only_shipments = request()->only_shipments == 'true' ? true : false;
            if ($only_shipments) {
                $sells->whereNotNull('transactions.shipping_status');
            }

            if (!empty(request()->input('shipping_status'))) {
                $sells->where('transactions.shipping_status', request()->input('shipping_status'));
            }
            if (!empty(request()->input('pattern_id'))) {
                $sells->where('transactions.pattern_id', request()->input('pattern_id'));
            }

            if (!empty(request()->input('for_dashboard_sales_order'))) {
                $sells->whereIn('transactions.status', ['partial', 'ordered'])
                    ->orHavingRaw('so_qty_remaining > 0');
            }
            $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;

            if ($sale_type == 'sales_order') {
                if (!auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    if($is_admin){
                        
                    }else{
                        $sells->where('transactions.created_by', request()->session()->get('user.id'));
                    }
                }
            }
            if(!$is_admin){
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
            }
            $sells->groupBy('transactions.id');

            if (!empty(request()->suspended)) {
                $transaction_sub_type = request()->get('transaction_sub_type');
                if (!empty($transaction_sub_type)) {
                    $sells->where('transactions.sub_type', $transaction_sub_type);
                } else {
                    $sells->where('transactions.sub_type', null);
                }

                $with = ['sell_lines'];
                
                if ($is_tables_enabled) {
                    $with[] = 'table';
                    
                }

                if ($is_service_staff_enabled) {
                    $with[] = 'service_staff';
                }
                

                $sales = $sells->where('transactions.is_suspend', 1)
                ->with($with)
                ->addSelect('transactions.is_suspend', 'transactions.project_no','transactions.business_id', 'transactions.pattern_id', 'transactions.separate_parent','transactions.separate_type','transactions.store','transactions.refe_no','transactions.res_table_id', 'transactions.res_waiter_id', 'transactions.additional_notes')
                 
                ->get();

                return view('sale_pos.partials.suspended_sales_modal')->with(compact('sales','users', 'currency_details','is_tables_enabled', 'is_service_staff_enabled', 'transaction_sub_type'));
            }

            $with[] = 'payment_lines';
            if (!empty($with)) {
                $sells->with($with);
            }

            // //$business_details = $this->businessUtil->getDetails($business_id);
            // if ($this->businessUtil->isModuleEnabled('subscription')) {
            // }
            $sells->addSelect('transactions.is_recurring', 'transactions.created_by','transactions.business_id','transactions.recur_parent_id',  'transactions.separate_parent','transactions.separate_type','transactions.store','transactions.pattern_id',"transactions.project_no","transactions.refe_no", 'transactions.agent_id','transactions.contact_id as co', 'transactions.cost_center_id' );

            $sales_order_statuses = Transaction::sales_order_statuses();
            $datatable = Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) use ($only_shipments, $is_admin, $sale_type,$business_id) {
                        $delivery = \App\Models\DeliveredPrevious::where("transaction_id",$row->id)->sum("current_qty");
                        $wrong    = \App\Models\DeliveredWrong::where("transaction_id",$row->id)->sum("current_qty");
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        if (auth()->user()->can("sell.view")) {
                            $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                        }
                        if (!$only_shipments) {
                            if ($is_admin || auth()->user()->can("sell.can_edit")) {
                                if($row->separate_parent == null || $row->separate_parent == 0){
                                    $html .= '<li><a target="_blank" href="' . action('SellController@edit', [$row->id]) . '"><i class="fas fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                                }else{
                                        if($row->separate_type == "partial"){
                                            // $html .= '<li><a target="_blank" href="' . action('SellController@edit', [$row->id]) . '"><i class="fas fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                                            $html .= '<li><a target="_blank" class="btn-modal" data-container=".change_warehouse" href="" data-href="' . action('SellController@changeStore', [$row->id]) . '"><i class="fas fa-edit"></i> ' . __("Change Warehouse") . '</a></li>';
                                        }
                                } 
                            }else{
                                if ($row->is_direct_sale == 0) {
                                    if (auth()->user()->can("sell.update")) {
                                        $html .= '<li><a target="_blank" href="' . action('SellPosController@edit', [$row->id]) . '"><i class="fas fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                                    }
                                } elseif ($row->type == 'sales_order') {
                                    if ( $is_admin || auth()->user()->can("so.update")) {
                                        $html .= '<li><a target="_blank" href="' . action('SellController@edit', [$row->id]) . '"><i class="fas fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                                    }
                                } else {
                                    if ($is_admin || auth()->user()->can("direct_sell.update")) {
                                        $html .= '<li><a target="_blank" href="' . action('SellController@edit', [$row->id]) . '"><i class="fas fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                                    }
                                }
                            }
                            if ( $is_admin ||  auth()->user()->can("sell.delete")){
                                $delete_link = '<li><a href="' . \URL::to('/sells/destroy/'.$row->id) . '" class="delete-sale"><i class="fas fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                                if($row->separate_parent == null || $row->separate_parent == 0){
                                    if ($row->is_direct_sale == 0) {
                                        if (auth()->user()->can("sell.delete")) {
                                            $html .= $delete_link;
                                        }
                                    } elseif ($row->type == 'sales_order') {
                                        if (auth()->user()->can("so.delete")) {
                                            $html .= $delete_link;
                                        }
                                    } else {
                                        if (auth()->user()->can("direct_sell.delete")) {
                                            $html .= $delete_link;
                                        }
                                    }
                                } 
                            }
                        }
                        if (config('constants.enable_download_pdf') && auth()->user()->can("print_invoice") && $sale_type != 'sales_order') {
                            $html .= '<li><a href="' . route('sell.downloadPdf', [$row->id]) . '" target="_blank"><i class="fas fa-print" aria-hidden="true"></i> ' . __("lang_v1.download_pdf") . '</a></li>';
                            if (!empty($row->shipping_status)) {
                                $html .= '<li><a href="' . route('packing.downloadPdf', [$row->id]) . '" target="_blank"><i class="fas fa-print" aria-hidden="true"></i> ' . __("lang_v1.download_paking_pdf") . '</a></li>';
                            }
                        }
                        if (auth()->user()->can("sell.view")) {
                            $html .= '<li><a class="btn-modal" data-container=".view_modal" href="" data-href="' . action('StatusLiveController@show', [$row->id]) . '"><i class="fas fa-eye"></i>' . __("home.Status Live") . '</a></li>';
                        }
                        // if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access")) {
                        //     if (!empty($row->document)) {
                        //         $document_name = !empty(explode("_", $row->document, 2)[1]) ? explode("_", $row->document, 2)[1] : $row->document;
                        //         $html .= '<li><a href="' . $row->document_path . '" download="' . $document_name . '"><i class="fas fa-download" aria-hidden="true"></i>' . __("purchase.download_document") . '</a></li>';
                        //         if (isFileImage($document_name)) {
                        //             $html .= '<li><a href="#" data-href="' .  $row->document_path  . '" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true"></i>' . __("lang_v1.view_document") . '</a></li>';
                        //         }
                        //     }
                        // }

                        // if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
                        //     $html .= '<li><a href="#" data-href="' . action('SellController@editShipping', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-truck" aria-hidden="true"></i>' . __("lang_v1.edit_shipping") . '</a></li>';
                        // }
                        
                        if($row->account_transactions->count() > 0){
                            $html .= '<li><a href="#" data-href="' .\URL::to('entry/transaction/'.$row->id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-align-justify" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                        }
                        
                        if ($row->type == 'sell' || $row->type == 'sale' ) { 
                            if (auth()->user()->can("print_invoice")) {
                                
                               $business_module = \App\Business::find($row->business_id);

                                $id_module       = (!empty($business_module))?(($business_module->sale_print_module != null )? $business_module->sale_print_module:null):null;

                                if(!empty($business_module)){
                                    if($business_module->sale_print_module != null && $business_module->sale_print_module != "[]" ){
                                        $all_pattern = json_decode($business_module->sale_print_module);
                                    }else{
                                        $id_module = null;
                                    }
                                }else{
                                    $id_module = null ;
                                }
        
                                
                                 if (request()->session()->get("user.id") == 1){
                                        if($id_module != null){
                                            $html .= '<li> <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no).'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>';
                                            $html .= '<div style="border:3px solid #ee680e;background-color:#ee680e">';
                                            foreach($all_pattern as $one_pattern){
                                                $pat   = \App\Models\PrinterTemplate::find($one_pattern); 
                                                if(!empty($pat)){
                                                    $html .= '<a target="_blank" class="btn btn-info" style="width:100%;border-radius:0px;text-align:left;background-color:#474747 !important;color:#f7f7f7 !important;border:2px solid #ee680e !important" href="'. action("Report\PrinterSettingController@generatePdf",["id"=>$one_pattern,"sell_id"=>$row->id]) .'"> <i class="fas fa-print" style="color:#ee680e"  aria-hidden="true"></i> Print By <b style="color:#ee680e">'.$pat->name_template.'</b> </a>';
                                                }
                                            }
                                            $html .= '</div>';
                                            
                                            
                                            $html .= '</li> <li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?package_slip=true"><i class="fas fa-file-alt" aria-hidden="true"></i> ' . __("lang_v1.packing_slip") . '</a></li>';
                                        }else{
                                            $html .= '<li>
                                                            <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no).'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>
                                                             
                                                            </li> <li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?package_slip=true"><i class="fas fa-file-alt" aria-hidden="true"></i> ' . __("lang_v1.packing_slip") . '</a></li> ';
                                        }
                                      
                                 }else{
                                     if($id_module != null){
                                            $html .= '<li> <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no).'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>';
                                            $html .= '<div style="border:3px solid #ee680e;background-color:#ee680e">';
                                            foreach($all_pattern as $one_pattern){
                                                $pat   = \App\Models\PrinterTemplate::find($one_pattern); 
                                                if(!empty($pat)){
                                                    $html .= '<a target="_blank" class="btn btn-info" style="width:100%;border-radius:0px;text-align:left;background-color:#474747 !important;color:#f7f7f7 !important;border:2px solid #ee680e !important" href="'. action("Report\PrinterSettingController@generatePdf",["id"=>$one_pattern,"sell_id"=>$row->id]) .'"> <i class="fas fa-print" style="color:#ee680e"  aria-hidden="true"></i> Print By <b style="color:#ee680e">'.$pat->name_template.'</b> </a>';
                                                }
                                            }
                                            $html .= '</div>';
                                            
                                            
                                            $html .= '</li> <li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?package_slip=true"><i class="fas fa-file-alt" aria-hidden="true"></i> ' . __("lang_v1.packing_slip") . '</a></li>';
                                        }else{
                                            $html .= '<li><a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no).'" target="_blank"><i class="fas fa-print" aria-hidden="true"></i> ' . __("lang_v1.print_invoice") . '</a> </li>
                                            <li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?package_slip=true"><i class="fas fa-file-alt" aria-hidden="true"></i> ' . __("lang_v1.packing_slip") . '</a></li>';
                                    
                                  }
                                     
                                 }
                                
                            }
                            $html .= '<li class="divider"></li>';
                            if (!$only_shipments) {
                                if ($row->payment_status != "paid" && auth()->user()->can("sell.payments")) {
                                    $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fas fa-money-bill-alt"></i> ' . __("purchase.add_payment") . '</a></li>';
                                }

                                $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) . '" class="view_payment_modal"><i class="fas fa-money-bill-alt"></i> ' . __("purchase.view_payments") . '</a></li>';

                                if (auth()->user()->can("sell.create")) {

                                    if($row->separate_parent == null || $row->separate_parent == 0){
                                        $html .= '<li><a href="' . action('SellController@duplicateSell', [$row->id]) . '"><i class="fas fa-copy"></i> ' . __("lang_v1.duplicate_sell") . '</a></li>';
                                        $html .= '<li><a href="' . action('SellReturnController@add', [$row->id]) . '"><i class="fas fa-undo"></i> ' . __("lang_v1.sell_return") . '</a></li>';
                                    }else{
                                            if($row->separate_type == "partial"){
                                                // $html .= '<li><a href="' . action('SellReturnController@add', [$row->id]) . '"><i class="fas fa-undo"></i> ' . __("lang_v1.sell_return") . '</a></li>';
                                            }
                                    } 
                                    
                                    $html .= '<li><a href="' . action('SellPosController@showInvoiceUrl', [$row->id]) . '" class="view_invoice_url"><i class="fas fa-eye"></i> ' . __("lang_v1.view_invoice_url") . '</a></li>';
                                }
                            }

                            // $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "new_sale"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i>' . __("lang_v1.new_sale_notification") . '</a></li>';
                        } else {
                            $html .= '<li><a href="#" data-href="' . action('SellController@viewMedia', ["model_id" => $row->id, "model_type" => "App\Transaction", 'model_media_type' => 'shipping_document']) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-paperclip" aria-hidden="true"></i>' . __("lang_v1.shipping_documents") . '</a></li>';
                        }
                        $html .= '<li><a href="#" data-href="' . action('HomeController@formAttach', ["type" => "sale","id" => $row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-paperclip" aria-hidden="true"></i> ' . __("Add Attachment") . '</a></li>';
                    
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="final-total" data-orig-value="{{$final_total}}">{{number_format($final_total, config("constants.currency_precision"))}}</span>'
                )
                
                ->editColumn(
                    'tax_amount',
                    '<span class="total-tax" data-orig-value="{{$tax_amount}}">{{number_format($tax_amount, config("constants.currency_precision"))}}</span>'
                )
                ->editColumn(
                    'total_paid',
                    '<span class="total-paid" data-orig-value="{{$total_paid}}">{{number_format($total_paid, config("constants.currency_precision"))}}</span>'
                )
                ->editColumn(
                    'total_before_tax',
                    '<span class="total_before_tax" data-orig-value="{{$total_before_tax}}">{{number_format($total_before_tax, config("constants.currency_precision"))}}</span>'
                )->addColumn('agents', function ($row) {
                    $id  =  $row->agent_id ;
                    if($id != null){
                        $AGENT = \App\User::find($id);  
                        $html  = $AGENT->first_name;
                    }else{
                        $html = "";
                    }
                    return $html;
                })->addColumn('cost_center_id', function ($row) {
                    $id  =  $row->cost_center_id ;
             
                    if($id != null){
                        $cost_centers =  \App\Account::cost_centers();
                        $cost = "";
                        foreach($cost_centers as $key => $ct){
                            if($id == $key ){
                                $cost =  $ct;
                                break; 
                            }  
                        }
                    }else{
                        $cost = "";
                    }
                    return $cost;
                })
                ->editColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = !empty($row->discount_amount) ? $row->discount_amount : 0;

                        if (!empty($discount) && $row->discount_type == 'percentage') {
                            $discount = $row->total_before_tax * ($discount / 100);
                        }
                        return '<span class="total-discount" data-orig-value="' . $discount . '">' .  number_format($discount, config("constants.currency_precision"))   . '</span>';
                    }
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                        function ($row) {
                            $payment_status      = Transaction::getPaymentStatus($row);
                            $transaction         = \App\Transaction::where("separate_parent",$row->id)->where("separate_type","partial")->get();
                                $lock                = 0;
                                if(count($transaction)>0){
                                    $lock                = 1;
                                }
                            if($payment_status == null){
                                $payment_status = "due" ; 
                            }
                            $cheques        = \App\Models\Check::where("transaction_id",$row->id)->whereIn("status",[0,3,4])->get();
                            return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id,'cheques' => $cheques , "lock" => $lock]);
                        }
                )
                ->editColumn(
                    'types_of_service_name',
                    '<span class="service-type-label" data-orig-value="{{$types_of_service_name}}" data-status-name="{{$types_of_service_name}}">{{$types_of_service_name}}</span>'
                )
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining =  $row->final_total - $row->total_paid;
                    $total_remaining_html = '<span class="payment_due" data-orig-value="' . $total_remaining . '">' . number_format($total_remaining, config("constants.currency_precision")) . '</span>';


                    return $total_remaining_html;
                })
                ->addColumn('return_due', function ($row) {
                    $return_due_html = '';
                    if (!empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $return_due_html .= '<a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="sell_return_due" data-orig-value="' . $return_due . '">' . number_format($return_due, config("constants.currency_precision")) . '</span></a>';
                    }
 
                    return $return_due_html;
                })
                ->editColumn('invoice_no', function ($row) {
                    if(!empty($row->document) && $row->document != "[]"){
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
                    $invoice_no = $row->invoice_no .  $attach;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                    if (!empty($row->return_exists)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned_from_sell') . '"><i class="fas fa-undo"></i></small>';
                    }
                    if (!empty($row->is_recurring)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.subscribed_invoice') . '"><i class="fas fa-recycle"></i></small>';
                    }

                    if (!empty($row->recur_parent_id)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-info label-round no-print" title="' . __('lang_v1.subscription_invoice') . '"><i class="fas fa-recycle"></i></small>';
                    }

                    if (!empty($row->is_export)) {
                        $invoice_no .= '</br><small class="label label-default no-print" title="' . __('lang_v1.export') . '">' . __('lang_v1.export') . '</small>';
                    }
                    $transaction = \App\Transaction::find($row->separate_parent);
                    $parent = ""; 
                    if(!empty($transaction)){
                        $parent .=  '<div class="btn-group">
                                    <button  type="button" class="btn btn-note dropdown-toggle btn-xs" 
                                        style="border:1px solid black;margin-top:10px" data-toggle="dropdown" aria-expanded="false">' .
                                        __("messages.related_bill") .
                                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                       
                        $parent  .= "\n<a href='#' class='btn btn-info btn-modal' data-href='/sells/".$transaction->id."'data-container='.view_modal'>".$transaction->invoice_no."</a>";
                        
                        $parent .='</ul>
                        </div>
                        '; 
                    }
                    return $invoice_no . $parent;
                })
                ->addColumn("project_no",function($row){
                    $Transactoin = Transaction::find($row->id);
                    return $Transactoin->project_no;
                })
                ->editColumn('shipping_status', function ($row) use ($shipping_statuses) {
                    $status_color = !empty($this->shipping_status_colors[$row->shipping_status]) ? $this->shipping_status_colors[$row->shipping_status] : 'bg-gray';
                    $status = !empty($row->shipping_status) ? '<a href="#" class="btn-modal" data-href="' . action('SellController@editShipping', [$row->id]) . '" data-container=".view_modal"><span class="label ' . $status_color . '">' . $shipping_statuses[$row->shipping_status] . '</span></a>' : '';

                    return $status;
                })
                // '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}'
                ->addColumn('conatct_name', function($row){
                    
                    $account = \App\Account::where("contact_id",$row->co)->first();
                    return view("account.action_parents.show_ledger_purchase",['account' => $account]);
                })
                ->editColumn('total_items', '{{@format_quantity($total_items)}}')
                ->filterColumn('conatct_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        if($methods[0] == "cash_visa"){
                            $payment_method = $methods[0];
                        }else{
                            $payment_method = $payment_types[$methods[0]];
                        }
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }
                    
                    $html = !empty($payment_method) ? '<span class="payment-method" data-orig-value="' . $payment_method . '" data-status-name="' . $payment_method . '">' . $payment_method . '</span>' : '';

                    return $html;
                })->editColumn("pattern_id",function($row){
                    $name = ""; 
                    $row->pattern_id;
                   
                    $pattern = \App\Models\Pattern::find($row->pattern_id);
                    if(!empty($pattern)){
                        $name = $pattern->name; 
                    }
                    return $name;
                })
                ->addColumn(
                    'delivery_status', function ($row)  {
                   
                        $product_list = [];
                        $sell = \App\TransactionSellLine::where("transaction_id",$row->id)->get();
                        foreach($sell as $it){
                            $product_list[] = $it->product_id;
                        }
                        $TransactionSellLine = TransactionSellLine::where("transaction_id",$row->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                        $DeliveredPrevious   = DeliveredPrevious::where("transaction_id",$row->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                        $wrong               = DeliveredWrong::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                         
                       
                        if($DeliveredPrevious == null){
                            $payment_status = "not_delivereds";
                            return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong ,  "type" => "normal"  ,"approved"=> false]);
                        }else if($TransactionSellLine <= $DeliveredPrevious){
                            $payment_status = "delivereds";
                            return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id , "wrong" => $wrong , "type" => "normal"  ,"approved"=> false]);
                        
                        } else if( $DeliveredPrevious < $TransactionSellLine ){
    
                            $payment_status = "separates";
                            return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong ,  "type" => "normal"  ,"approved"=> false]);
    
    
                        }
                })
                ->editColumn('status', function ($row) use ($sales_order_statuses, $is_admin) {
                    $status = '';

                    if ($row->type == 'sales_order') {
                        if ($is_admin && $row->status != 'completed') {
                            $status = '<span class="edit-so-status label ' . $sales_order_statuses[$row->status]['class'] . '" data-href="' . action("SalesOrderController@getEditSalesOrderStatus", ['id' => $row->id]) . '">' . $sales_order_statuses[$row->status]['label'] . '</span>';
                        } else {
                            $status = '<span class="label ' . $sales_order_statuses[$row->status]['class'] . '" >' . $sales_order_statuses[$row->status]['label'] . '</span>';
                        }
                    }

                    return $status;
                })
                ->editColumn('so_qty_remaining', '{{@format_quantity($so_qty_remaining)}}')
                ->editColumn('additional_notes',function($row){
                        return substr($row->additional_notes,0,30);
                    })
                ->editColumn('shipping_details',function($row){
                    return substr($row->shipping_details,0,30);
                })
                ->editColumn('store',function($row){
                    $warehouse = "";
                    if($row->warehouse){
                        $warehouse = $row->warehouse->name;
                    }
                    return  $warehouse;
                })
                ->editColumn('created_by',function($row){
                    $AGENT = \App\User::find($row->created_by);  
                    $html  = ($AGENT)?$AGENT->first_name:"";
                    return  $html;
                })
                ->addColumn('cost',function($row){
                    $total_cost     = 0;
                    $qty     = [];
                    $product = [];
                    foreach($row->sell_lines as $it){
                        if(!in_array($it->product_id,$product)){
                            $cost = \App\Models\ItemMove::orderBy("id","desc")->where("product_id",$it->product_id)->first();
                            $product[$it->product_id] = (!empty($cost))?$cost->unit_cost:0;
                            $qty[$it->product_id]     = $it->quantity;
                        }else{
                            $cost = \App\Models\ItemMove::orderBy("id","desc")->where("product_id",$it->product_id)->first();
                            $product[$it->product_id] += (!empty($cost))?$cost->unit_cost:0;
                            $qty[$it->product_id]     += $it->quantity;
                        }
                    }
                    foreach($product as $key => $it){
                        foreach($qty as $keys => $i){
                            if($key == $keys){
                                $total_cost += ($i*$it);
                            }
                        }
                    }
                   
                    return   $total_cost;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return  action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);

            $rawColumns = ['final_total','project_no','store','cost','cost_center_id','created_by','pattern_id', 'shipping_details','agents' ,'action', 'total_paid', 'delivery_status','total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due', 'conatct_name', 'status'];

            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $sales_representative = User::forDropdown($business_id, false, false, true);

        //Commission agent filter
        $is_cmsn_agent_enabled = request()->session()->get('business.sales_cmsn_agnt');
        $commission_agents = [];
        if (!empty($is_cmsn_agent_enabled)) {
            $commission_agents = User::forDropdown($business_id, false, true, true);
        }

        //Service staff filter
        $service_staffs = null;
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $project_numbers = [];

        $project_no_ = Transaction::whereNotNull("project_no")->get();
        foreach($project_no_ as  $value){
            $project_numbers[ $value->project_no] = $value->project_no;
        }
        $cost_centers =  \App\Account::cost_centers();
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $agents     =  \App\Models\Agent::items();
        return view('sell.index')
            ->with(compact('agents' , 'patterns' ,'users','cost_centers','currency_details','project_numbers' ,'business_locations', 'customers', 'is_woocommerce', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'is_types_service_enabled', 'shipping_statuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('direct_sell.access') || !auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        
        $business_id      = request()->session()->get('user.business_id');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $currency_data    = session('currency');
        /*
         * save currency symbol for recover
         *
         * */
       /* $currency_data =session('currency');
        $currency_data['old_symbol']=$currency_data['symbol'];
        $currency_data['symbol']='دولار';
        session()->put('currency', $currency_data);*/
        $users  = [] ;
        $us               = User::where('business_id', $business_id)
                                        ->where('is_cmmsn_agnt', 1)->get();
                                        
        $terms = [] ;
        $it_terms = \App\Models\QuatationTerm::where("business_id",$business_id)->get(); 
        foreach($it_terms as $it){
        $terms[$it->id] =  $it->name  ;
        }
        $patterns  = [];
        if(request()->session()->get("user.id") == 1){
            $patterns_ = \App\Models\Pattern::select()->get();
        }else{
            $userd     = \App\Models\User::find(request()->session()->get("user.id"));
            $patterns_ = \App\Models\Pattern::whereIn("id",json_decode($userd->pattern_id))->select()->get();
        } 
        foreach($patterns_ as $it){
                $patterns[$it->id] = $it->name;
        }

        foreach($us as $it){
            $users[$it->id] = $it->first_name;
        }

         //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse();
            }elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellController@index'));
            }
        } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellController@index'));
        }

        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        
        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);
        
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $mainstore = Warehouse::where('business_id', $business_id)->select(['name','status','id'])->get();
        $mainstore_categories = [];
        if (!empty($mainstore)) {
            foreach ($mainstore as $mainstor) {
                if($mainstor->status == 1){
                    $mainstore_categories[$mainstor->id] = $mainstor->name;
                
                }
            }
                   
        }

        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $default_location = null;
        foreach ($business_locations as $id => $name) {
            $default_location = BusinessLocation::findOrFail($id);
            break;
        }
        /*dd($business_details->sales_cmsn_agnt); is NULL*/
        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
        }
        // dd($business_details);
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
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $payment_line  = $this->dummyPaymentLine;
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        //Selling Price Group Dropdown
        $price_groups     = SellingPriceGroup::forDropdown($business_id);
        $default_datetime = $this->businessUtil->format_date('now', true);
        $pos_settings     = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $invoice_schemes  = InvoiceScheme::forDropdown($business_id);
        $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        //Types of service
        $types_of_service = [];
        if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
            $types_of_service = TypesOfService::forDropdown($business_id);
        }

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
            $keys =  array_keys($accounts);
            if (isset($keys[0])) {
                unset($accounts[$keys[0]]);
            } 
            
        }
        $accounts = Account::main('cash');
        $status = request()->get('status', '');
        $currency     =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies   = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $business_id   =  request()->session()->get('user.business_id');       
        $layout        =  \App\InvoiceLayout::where('business_id',$business_id)->where('is_default',1)->first();
        $agents        =  \App\Models\Agent::items();
        $cost_centers  =  \App\Account::cost_centers();
        $list_of_prices         = \App\Product::getListPrices();
        return view('sell.create')
                ->with(compact(
                    'layout','cost_centers',
                    'business_details',
                    'list_of_prices',
                    'taxes',
                    'agents',
                    'currencies',
                    'patterns',
                    'users',
                    'walk_in_customer',
                    'business_locations',
                    'bl_attributes',
                    'default_location',
                    'currency_details',
                    'commission_agent',
                    'terms',
                    'types',
                    'customer_groups',
                    'payment_line',
                    'payment_types',
                    'price_groups',
                    'default_datetime',
                    'pos_settings',
                    'invoice_schemes',
                    'default_invoice_schemes',
                    'types_of_service',
                    'accounts',
                    'mainstore_categories',
                    'shipping_statuses',
                    'status',
                    'currency_data'
                ));
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delivered_page(Request $request){
         
         
        
        if (!auth()->user()->can('sell.delivered')) {
            abort(403, 'Unauthorized action.');
        }
    
        if(request()->ajax()){
    
            $business_id = request()->session()->get('user.business_id');
            $currency_data =session('currency');
            /*
             * save currency symbol for recover
             *
             * */
           /* $currency_data =session('currency');
            $currency_data['old_symbol']=$currency_data['symbol'];
            $currency_data['symbol']='دولار';
            session()->put('currency', $currency_data);*/
    
    
             //Check if subscribed or not, then check for users quota
             if (!$this->moduleUtil->isSubscribed($business_id)) {
                if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                    return $this->moduleUtil->expiredResponse();
                }elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                    return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellController@index'));
                }
            } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellController@index'));
            }
    
            $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
            
            $business_details = $this->businessUtil->getDetails($business_id);
            $taxes = TaxRate::forBusinessDropdown($business_id, true, true);
    
            $business_locations = BusinessLocation::forDropdown($business_id, false, true);
            $mainstore = Warehouse::where('business_id', $business_id)->select(['name','status','id'])->get();
            $mainstore_categories = [];
            if (!empty($mainstore)) {
                foreach ($mainstore as $mainstor) {
                    if($mainstor->status == 1){
                        $mainstore_categories[$mainstor->id] = $mainstor->name;
                    
                    }
                }
                       
            }
    
            $bl_attributes = $business_locations['attributes'];
            $business_locations = $business_locations['locations'];
    
            $default_location = null;
            foreach ($business_locations as $id => $name) {
                $default_location = BusinessLocation::findOrFail($id);
                break;
            }
            /*dd($business_details->sales_cmsn_agnt); is NULL*/
            $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
            $commission_agent = [];
            if ($commsn_agnt_setting == 'user') {
                $commission_agent = User::forDropdown($business_id);
            } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
                $commission_agent = User::saleCommissionAgentsDropdown($business_id);
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
            $customer_groups = CustomerGroup::forDropdown($business_id);
    
            $payment_line = $this->dummyPaymentLine;
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
    
            //Selling Price Group Dropdown
            $price_groups = SellingPriceGroup::forDropdown($business_id);
    
            $default_datetime = $this->businessUtil->format_date('now', true);
    
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
    
            $invoice_schemes = InvoiceScheme::forDropdown($business_id);
            $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
    
            //Types of service
            $types_of_service = [];
            if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
                $types_of_service = TypesOfService::forDropdown($business_id);
            }
    
            //Accounts
            $accounts = [];
            if ($this->moduleUtil->isModuleEnabled('account')) {
                $accounts = Account::forDropdown($business_id, true, false);
            }
    
            $status = request()->get('status', '');
    
    
    
            return view('sell.delivered')
                ->with(compact(
                    'business_details',
                    'taxes',
                    'walk_in_customer',
                    'business_locations',
                    'bl_attributes',
                    'default_location',
                    'commission_agent',
                    'types',
                    'customer_groups',
                    'payment_line',
                    'payment_types',
                    'price_groups',
                    'default_datetime',
                    'pos_settings',
                    'invoice_schemes',
                    'default_invoice_schemes',
                    'types_of_service',
                    'accounts',
                    'mainstore_categories',
                    'shipping_statuses',
                    'status',
                    'currency_data'
                ));
            }
        }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }


    public function attach($id)
    {
        $data        = Transaction::find($id);
        return view("sell.attach")->with(compact("data"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $query = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(['contact', 'sell_lines' => function ($q) {
                        $q->whereNull('parent_sell_line_id');
                        $q->orderBy('order_id',"asc");
                    },'sell_lines.product', 'sell_lines.product.unit','sell_lines.variations', 'sell_lines.variations.product_variation', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'table', 'service_staff'
                    , 'sell_lines.service_staff', 'types_of_service', 'sell_lines.warranties', 'media']);

        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
            $query->where('transactions.created_by', request()->session()->get('user.id'));
        }

        $sell = $query->firstOrFail();

        $activities = Activity::forSubject($sell)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

           foreach ($sell->sell_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                $sell->sell_lines[$key] = $formated_sell_line;
            }
        }
        $payment_types = $this->transactionUtil->payment_types($sell->location_id, true);
        $order_taxes = [];
        if (!empty($sell->tax)) {
            if ($sell->tax->is_tax_group) {
                $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
            } else {
                $order_taxes[$sell->tax->name] = $sell->tax_amount;
            }
        }
        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $shipping_status_colors = $this->shipping_status_colors;
        $common_settings = session()->get('business.common_settings');
        $is_warranty_enabled = !empty($common_settings['enable_product_warranty']) ? true : false;
        $statuses = Transaction::getSellStatuses();
        $transactions = Transaction::where("id" , $sell->id)->get();
        $store  = "";
        if(!empty($transactions)){
            foreach ($transactions as $transaction) {
                $business_id = request()->session()->get('user.business_id');
                $warehouses = Warehouse::where("business_id",$business_id)->select(["description","status","mainStore","id","name"])->get();
                if (!empty($warehouses)) {
                    foreach ($warehouses as $warehouse) {
                        if($transaction->store == $warehouse->id){
                            $store = $warehouse->name;
                        }
                    }
                }
            }
        }
        $all_separate = Transaction::where('business_id', $business_id)->where('separate_parent', $id)->where('separate_type', "payment")->get();
        $separate     = [] ;
        foreach($all_separate as $trans){
            $payment    = \App\TransactionPayment::where("transaction_id",$trans->id)->first();
            $separate[] = $payment; 

        }
        $all_separate_del      = Transaction::where('business_id', $business_id)->where('separate_parent', $id)->where('separate_type', "partial")->get();

        $separate_delivery     = [] ;
        foreach($all_separate_del as $trans){
            $payments    = \App\TransactionPayment::where("transaction_id",$trans->id)->first();
            $separate_delivery[] = $payments; 
        }
      
 
        return view('sale_pos.show')
                ->with(compact([
                    'taxes',
                    'sell',
                    'payment_types',
                    'order_taxes',
                    'separate',
                    'separate_delivery',
                    'pos_settings',
                    'shipping_statuses',
                    'shipping_status_colors',
                    'is_warranty_enabled',
                    'activities',
                    'statuses',
                    'store',
                ]));
    }

    /**
     * change warehouse.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeStore(Request $request,$id) {
        $stores    = [];
        $store     = \App\Transaction::find($id)->store;
        $allStores = \App\Models\Warehouse::get();
        foreach($allStores as $i){$stores[$i->id]=$i->name;}
        return view("sale_pos.warehouse.change_store")->with(compact("id","store","stores"));
    }
    /**
     * change by warehouse.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeByStore(Request $request) {
       
        $transaction_id = request()->input("transaction");
        $new_store_id   = request()->input("store");
        try{
            \DB::beginTransaction();
            $transaction    = \App\Transaction::find($transaction_id);
            $sellLines      = \App\TransactionSellLine::where("transaction_id",$transaction_id)->get();
            foreach($sellLines as $i){
                
                $line_id            =  $i->id;
                $store_id           =  $i->store_id;
                $product_id         =  $i->product_id;

                $previous           =  \App\Models\DeliveredPrevious::where("line_id",$line_id)
                                                                    ->where("transaction_id",$transaction_id)
                                                                    ->where("product_id",$product_id)
                                                                    ->where("store_id",$store_id)
                                                                    ->first();

                $tr_delivery        =  $previous->transaction_recieveds_id ;                                                 
                $previous_id        =  $previous->id;
                $previous->store_id =  $new_store_id;
                $previous->update();

                $movementWarehouse  =  \App\MovementWarehouse::where("transaction_sell_line_id",$line_id)
                                                                    ->where("transaction_id",$transaction_id)
                                                                    ->where("product_id",$product_id)
                                                                    ->where("store_id",$store_id)
                                                                    ->first();
                
                $movementWarehouse->store_id =  $new_store_id;
                $movementWarehouse->update();

                $item_movement      =  \App\Models\ItemMove::where("recieve_id",$previous_id)
                                                            ->where("transaction_id",$transaction_id)
                                                            ->where("product_id",$product_id)
                                                            ->where("store_id",$store_id)
                                                            ->first();   
                
                $item_movement->store_id = $new_store_id;
                $item_movement->update();

                $qty                     =  $i->quantity;
                $warehouseInfo           =  \App\Models\WarehouseInfo::where("store_id",$store_id)
                                                                    ->where("product_id",$product_id)
                                                                    ->first();

                $warehouseInfo->product_qty  =  $warehouseInfo->product_qty + $qty;
                $warehouseInfo->update();

                $newWarehouseInfo        =  \App\Models\WarehouseInfo::where("store_id",$new_store_id)
                                                                    ->where("product_id",$product_id)
                                                                    ->first();

                if(!empty($newWarehouseInfo)){
                    if($newWarehouseInfo->product_qty == 0){
                        $output = [
                            "success" => 0,  
                            "msg"     => __("Out Of Stock"),  
                        ];
                        
                        return $output;
                    }
                    $newWarehouseInfo->product_qty   =  $newWarehouseInfo->product_qty - $qty;
                    $newWarehouseInfo->update();
                }else{
                    $output = [
                        "success" => 0,  
                        "msg"     => __("Out Of Stock"),  
                    ];
                    
                    return $output;
                }

                $i->store_id  = $new_store_id;
                $i->update(); 
            }
            $transactionDelivery             = \App\Models\TransactionDelivery::find($tr_delivery);
            $transactionDelivery->store_id   = $new_store_id;
            $transactionDelivery->update();

            $transaction->store   = $new_store_id;
            $transaction->update();
            \DB::commit();
            $output = [
                "success" => 1,  
                "msg"     => __("Successfully Changed"),  
            ];
        }catch(Exception $e){
            $output = [
                "success" => 0,  
                "msg"     => __("Failed Changed"),  
            ];
            
        } 
        return $output;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('sell.update') || !auth()->user()->can('sell.can_edit')) {
            abort(403, 'Unauthorized action.');
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
        
        $business_id = request()->session()->get('user.business_id');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $users  = [] ;
        $us               = User::where('business_id', $business_id)->where('is_cmmsn_agnt', 1)->get();
        $currency         =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies       = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $patterns  = [];
        $patterns_ = \App\Models\Pattern::select()->get();
        foreach($patterns_ as $it){
                $patterns[$it->id] = $it->name;
        }
        $terms = [] ;
        $it_terms = \App\Models\QuatationTerm::where("business_id",$business_id)->get(); 
        foreach($it_terms as $it){
             $terms[$it->id] =  $it->name  ;
        }
        foreach($us as $it){
            $users[$it->id] = $it->first_name;
        }
        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes            = TaxRate::forBusinessDropdown($business_id, true, true);
        $transaction      = Transaction::where('business_id', $business_id)
                                        ->with(['price_group', 'types_of_service', 'media', 'media.uploaded_by_user'])
                                        ->whereIn('type', ['sell',"sale"])
                                        ->findorfail($id);

        $warehouse         = $transaction->store;
        $transaction_stock = WarehouseInfo::where('business_id', $business_id)
                                            ->where('store_id', $warehouse)
                                            ->get();
        
                            

        $location_id           = $transaction->location_id;
        $location_printer_type = BusinessLocation::find($location_id)->receipt_printer_type;

        $sell_details = TransactionSellLine::
                        join(
                            'products AS p',
                            'transaction_sell_lines.product_id',
                            '=',
                            'p.id'
                        )
                        ->join(
                            'variations AS variations',
                            'transaction_sell_lines.variation_id',
                            '=',
                            'variations.id'
                        )
                        ->join(
                            'product_variations AS pv',
                            'variations.product_variation_id',
                            '=',
                            'pv.id'
                        )
                        ->leftJoin('variation_location_details AS vld', function ($join) use ($location_id) {
                            $join->on('variations.id', '=', 'vld.variation_id')
                                ->where('vld.location_id', '=', $location_id);
                        })
                        ->leftJoin('units', 'units.id', '=', 'p.unit_id')
                        ->where('transaction_sell_lines.transaction_id', $id)
                        ->with(['warranties'])
                        ->select(
                            DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                            'p.id as product_id',
                            'p.enable_stock',
                            'transaction_sell_lines.order_id',
                            'p.name as product_actual_name',
                            'p.type as product_type',
                            'pv.name as product_variation_name',
                            'pv.is_dummy as is_dummy',
                            'variations.name as variation_name',
                            'variations.sub_sku',
                            'p.barcode_type',
                            'p.enable_sr_no',
                            'variations.id as variation_id',
                            'units.short_name as unit',
                            'units.allow_decimal as unit_allow_decimal',
                            'transaction_sell_lines.tax_id as tax_id',
                            'transaction_sell_lines.item_tax as item_tax',
                            'transaction_sell_lines.unit_price as default_sell_price',
                            'transaction_sell_lines.unit_price_inc_tax as sell_price_inc_tax',
                            'transaction_sell_lines.unit_price_before_discount as unit_price_before_discount',
                            'transaction_sell_lines.id as transaction_sell_lines_id',
                            'transaction_sell_lines.id',
                            'transaction_sell_lines.se_note',
                            'transaction_sell_lines.transaction_id',
                            'transaction_sell_lines.quantity as quantity_ordered',
                            'transaction_sell_lines.sell_line_note as sell_line_note',
                            'transaction_sell_lines.parent_sell_line_id',
                            'transaction_sell_lines.lot_no_line_id',
                            'transaction_sell_lines.line_discount_type',
                            'transaction_sell_lines.line_discount_amount',
                            'transaction_sell_lines.res_service_staff_id',
                            'units.id as unit_id',
                            'transaction_sell_lines.sub_unit_id',
                             
                            DB::raw('vld.qty_available + transaction_sell_lines.quantity AS qty_available')
                        )->orderBy("transaction_sell_lines.order_id","asc")
                        ->get();
                        
        if (!empty($sell_details)) {
            foreach ($sell_details as $key => $value) {
                //If modifier or combo sell line then unset
                if (!empty($sell_details[$key]->parent_sell_line_id)) {
                    unset($sell_details[$key]);
                } else {
                    if ($transaction->status != 'final') {
                        $actual_qty_avlbl = $value->qty_available - $value->quantity_ordered;
                        $sell_details[$key]->qty_available = $actual_qty_avlbl;
                        $value->qty_available = $actual_qty_avlbl;
                    }
                        
                    $sell_details[$key]->formatted_qty_available = $this->productUtil->num_f($value->qty_available, false, null, true);
                    $lot_numbers = [];
                    if (request()->session()->get('business.enable_lot_number') == 1) {
                        $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($value->variation_id, $business_id, $location_id);
                        foreach ($lot_number_obj as $lot_number) {
                            //If lot number is selected added ordered quantity to lot quantity available
                            if ($value->lot_no_line_id == $lot_number->purchase_line_id) {
                                $lot_number->qty_available += $value->quantity_ordered;
                            }

                            $lot_number->qty_formated = $this->transactionUtil->num_f($lot_number->qty_available);
                            $lot_numbers[] = $lot_number;
                        }
                    }
                    $sell_details[$key]->lot_numbers = $lot_numbers;

                    if (!empty($value->sub_unit_id)) {
                        $value = $this->productUtil->changeSellLineUnit($business_id, $value);
                        $sell_details[$key] = $value;
                    }

                    if ($this->transactionUtil->isModuleEnabled('modifiers')) {
                        //Add modifier details to sel line details
                        $sell_line_modifiers = TransactionSellLine::where('parent_sell_line_id', $sell_details[$key]->transaction_sell_lines_id)
                            ->where('children_type', 'modifier')
                            ->get();
                        $modifiers_ids = [];
                        if (count($sell_line_modifiers) > 0) {
                            $sell_details[$key]->modifiers = $sell_line_modifiers;
                            foreach ($sell_line_modifiers as $sell_line_modifier) {
                                $modifiers_ids[] = $sell_line_modifier->variation_id;
                            }
                        }
                        $sell_details[$key]->modifiers_ids = $modifiers_ids;

                        //add product modifier sets for edit
                        $this_product = Product::find($sell_details[$key]->product_id);
                        if (count($this_product->modifier_sets) > 0) {
                            $sell_details[$key]->product_ms = $this_product->modifier_sets;
                        }
                    }

                    //Get details of combo items
                    if ($sell_details[$key]->product_type == 'combo') {
                        $sell_line_combos = TransactionSellLine::where('parent_sell_line_id', $sell_details[$key]->transaction_sell_lines_id)
                            ->where('children_type', 'combo')
                            ->get()
                            ->toArray();
                        if (!empty($sell_line_combos)) {
                            $sell_details[$key]->combo_products = $sell_line_combos;
                        }

                        //calculate quantity available if combo product
                        $combo_variations = [];
                        foreach ($sell_line_combos as $combo_line) {
                            $combo_variations[] = [
                                'variation_id' => $combo_line['variation_id'],
                                'quantity' => $combo_line['quantity'] / $sell_details[$key]->quantity_ordered,
                                'unit_id' => null
                            ];
                        }
                        $sell_details[$key]->qty_available =
                        $this->productUtil->calculateComboQuantity($location_id, $combo_variations);
                        
                        if ($transaction->status == 'final') {
                            $sell_details[$key]->qty_available = $sell_details[$key]->qty_available + $sell_details[$key]->quantity_ordered;
                        }
                        
                        $sell_details[$key]->formatted_qty_available = $this->productUtil->num_f($sell_details[$key]->qty_available, false, null, true);
                    }
                }
            }
        }
        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
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
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $transaction->transaction_date = $this->transactionUtil->format_date($transaction->transaction_date, true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $waiters = null;
        if ($this->productUtil->isModuleEnabled('service_staff') && !empty($pos_settings['inline_service_staff'])) {
            $waiters = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $invoice_schemes = [];
        $default_invoice_schemes = null;

        if ($transaction->status == 'draft') {
            $invoice_schemes = InvoiceScheme::forDropdown($business_id);
            $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
        }

        $redeem_details = [];
        if (request()->session()->get('business.enable_rp') == 1) {
            $redeem_details = $this->transactionUtil->getRewardRedeemDetails($business_id, $transaction->contact_id);

            $redeem_details['points'] += $transaction->rp_redeemed;
            $redeem_details['points'] -= $transaction->rp_earned;
        }

        $edit_discount = auth()->user()->can('edit_product_discount_from_sale_screen');
        $edit_price = auth()->user()->can('edit_product_price_from_sale_screen');

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
        }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];

        $common_settings = session()->get('business.common_settings');
        $is_warranty_enabled = !empty($common_settings['enable_product_warranty']) ? true : false;
        $warranties = $is_warranty_enabled ? Warranty::forDropdown($business_id) : [];
        $warehouses = Warehouse::where("business_id",$business_id)->get();
        $stores = [];
        if (!empty($warehouses)) {
            foreach ($warehouses as $mainstor) {
                if($mainstor->status == 1){
                    $stores[$mainstor->id] = $mainstor->name;
                
                }
            }
                   
        }
        
        $warehouse              = $transaction->store;
        $agents                 =  \App\Models\Agent::items();
        $cost_centers           =  \App\Account::cost_centers();
        $list_of_prices         = \App\Product::getListPrices();
        return view('sell.edit')
            ->with(compact('business_details','users', 'currency_details',  'patterns','cost_centers',"stores", 'bl_attributes' ,'agents' , 'warehouse' ,'transaction_stock', 'taxes', 'sell_details',
             'transaction', 'list_of_prices' ,'commission_agent','currencies', 'types', 'customer_groups', 'pos_settings', 'waiters', 'invoice_schemes', 'default_invoice_schemes', 'redeem_details', 
             'edit_discount', 'edit_price','terms' ,'accounts', 'shipping_statuses', 'warranties'));
    }
    

    /**
     * Display a listing sell drafts.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDrafts()
    {
        if (!auth()->user()->can('list_drafts')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
      
        $sales_representative = User::forDropdown($business_id, false, false, true);
        $project_no_ = Transaction::whereNotNull("project_no")->get();
        $project_numbers = [ ];
        foreach($project_no_ as  $value){
            $project_numbers[ $value->project_no] = $value->project_no;
        }
        $users  = [] ;
        $us               = User::where('business_id', $business_id)
                                        ->where('is_cmmsn_agnt', 1)->get();

        foreach($us as $it){
            $users[$it->id] = $it->first_name;
        }
        return view('sale_pos.draft')
            ->with(compact('business_locations','users','project_numbers', 'customers', 'sales_representative'));
    }
    /**
     * Display a listing sell drafts.
     *
     * @return \Illuminate\Http\Response
     */
    public function getApproved()
    {
        if (!auth()->user()->can('list_quotations')) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $users  = [] ;
        $us               = User::where('business_id', $business_id)
                                        ->where('is_cmmsn_agnt', 1)->get();

        foreach($us as $it){
            $users[$it->id] = $it->first_name;
        }
        $sales_representative = User::forDropdown($business_id, false, false, true);
    
        $project_numbers = [];

        $project_no_ = Transaction::whereNotNull("project_no")->get();
        foreach($project_no_ as  $value){
            $project_numbers[ $value->project_no] = $value->project_no;
        }
        $refe_nos = [];
        $first_nos = [];
        $previouses = [];
        $agents = [];
        $agents_ = Agent::where("business_id",$business_id)->get();
        foreach($agents_ as  $value){
            $agents[ $value->id] = $value->name;
        }
        $first_no_ = Transaction::whereNotNull("first_ref_no")->where("ecommerce",0)->get();
        foreach($first_no_ as  $value){
            $first_nos[ $value->first_ref_no] = $value->first_ref_no;
        }
        $previous_ = Transaction::whereNotNull("previous")->where("ecommerce",0)->get();
        foreach($previous_ as  $value){
            $previouses[ $value->previous] = $value->previous;
        }
        $refe_no_ = Transaction::whereNotNull("refe_no")->where("ecommerce",0)->get();
        foreach($refe_no_ as  $value){
            $refe_nos[ $value->refe_no] = $value->refe_no;
        }
        $cost_centers =  \App\Account::cost_centers();
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        return view('sale_pos.approved_Quatation')
            ->with(compact('business_locations','refe_nos','currency_details','first_nos','previouses','users','agents','cost_centers',"project_numbers",'project_numbers', 'customers', 'sales_representative'));
    }

    /**
     * Display a listing sell quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getQuotations()
    {
        if (!auth()->user()->can('list_quotations')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $users  = [] ;
        $us               = User::where('business_id', $business_id)
                                        ->where('is_cmmsn_agnt', 1)->get();

        foreach($us as $it){
            $users[$it->id] = $it->first_name;
        }
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
      
        $sales_representative = User::forDropdown($business_id, false, false, true);
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $project_numbers = [];
        $refe_nos = [];

        $project_no_ = Transaction::whereNotNull("project_no")->get();
        foreach($project_no_ as  $value){
            $project_numbers[ $value->project_no] = $value->project_no;
        }
        $refe_no_ = Transaction::whereNotNull("refe_no")->get();
        foreach($refe_no_ as  $value){
            $refe_nos[ $value->refe_no] = $value->refe_no;
        }
        $agents = [];
        $agents_ = Agent::where("business_id",$business_id)->get();
        foreach($agents_ as  $value){
            $agents[ $value->id] = $value->name;
        }
        $cost_centers =  \App\Account::cost_centers();

        return view('sale_pos.quotations')
                ->with(compact('currency_details' , 'refe_nos','users','agents','cost_centers',"project_numbers",'business_locations', 'customers', 'sales_representative'));
    }
    /**
     * Display a listing sell quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getApprovedQuotations()
    {
        if (!auth()->user()->can('list_quotations')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
      
        $sales_representative = User::forDropdown($business_id, false, false, true);
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $project_numbers = [];

        $project_no_ = Transaction::whereNotNull("project_no")->where("ecommerce",0)->get();
        foreach($project_no_ as  $value){
            $project_numbers[ $value->project_no] = $value->project_no;
        }
        $agents = [];
        $agents_ = Agent::where("business_id",$business_id)->get();
        foreach($project_no_ as  $value){
            $agents[ $value->id] = $value->name;
        }
        $cost_centers =  \App\Account::cost_centers();
        $users  = [] ;
        $us               = User::where('business_id', $business_id)
                                        ->where('is_cmmsn_agnt', 1)->get();

        foreach($us as $it){
            $users[$it->id] = $it->first_name;
        }
        return view('sale_pos.quotations')
                ->with(compact('currency_details' ,'users' ,'project_numbers','cost_centers','business_locations', 'customers', 'sales_representative'));
    }

    /**
     * Send the datatable response for draft or quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDraftDatables()
    {
        
        if (request()->ajax()) {
            $is_admin = $this->businessUtil->is_admin(auth()->user());
            $business_id = request()->session()->get('user.business_id');
            $is_quotation = request()->input('is_quotation', 0);
            
            $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
            
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin('transaction_sell_lines as tsl', function($join) {
                    $join->on('transactions.id', '=', 'tsl.transaction_id')
                        ->whereNull('tsl.parent_sell_line_id');
                })
                ->where('transactions.business_id', $business_id)
                ->whereIn('transactions.type', ['sale','sell'])
                ->where('transactions.status', 'draft')
                ->where("ecommerce",0)
                ->where('transactions.sub_status', null)
                ->select(
                    'transactions.id',
                    'transactions.agent_id',
                    'transactions.business_id',
                    'transaction_date',
                    'transactions.document',
                    'invoice_no',
                    'contacts.name',
                    'contacts.mobile',
                    'contacts.supplier_business_name',
                    'bl.name as business_location',
                    'is_direct_sale',
                    'sub_status',
                    DB::raw('COUNT( DISTINCT tsl.id) as total_items'),
                    DB::raw('SUM(tsl.quantity) as total_quantity'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as added_by")
                );
                
            if ($is_quotation == 1) {
                $sells->where('transactions.sub_status', 'quotation');
            }
            $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;

            if(!$is_admin){
                        
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
                     
            }
            
            $os_check =  \App\User::where('id',auth()->id())->whereHas('roles',function($query){
                                $query->where('id',1);
                                })->first();
            if(empty($os_check)) {
                $users =  \App\User::where('id',auth()->id())->first();
                if(!empty($users)) {
                    $patterns = json_decode($users->pattern_id);
                    $sells->whereIn('transactions.pattern_id',$patterns);
                }
            }
            

             
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
            if(!empty(request()->agent_id)){
                $agent_id = request()->agent_id;
                if ($agent_id != 'all') {
                    $sells->where('transactions.agent_id', $agent_id);
                }
            }
                
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transaction_date', '>=', $start)
                            ->whereDate('transaction_date', '<=', $end);
            }

            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }
             
            if (!empty(request()->project_no)) {
               
                $sells->where('transactions.project_no',request()->project_no);
            }
            
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }

            if ($is_woocommerce) {
                $sells->addSelect('transactions.woocommerce_order_id');
            }

            $sells->groupBy('transactions.id');
            // dd($sells);
            return Datatables::of($sells)
                ->addColumn(
                    'action',function($row) use($is_admin){
                        
                        $text ='<div class="btn-group">
                                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                            data-toggle="dropdown" aria-expanded="false">' .
                                            __("messages.actions") .
                                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                            <li>
                                            <a href="#" data-href="'.action('SellController@show', [$row->id]).'" 
                                            class="btn-modal" data-container=".view_modal"><i class="fas fa-eye"
                                             aria-hidden="true"></i>'.trans("messages.view").'</a>
                                            </li>';
                        if ($is_admin || auth()->user()->can('sell.can_edit')) {    
                            if($row->is_direct_sale == 1){
                                $text .='<li>
                                        <a target="_blank" href="'.action('SellController@edit', [$row->id,'status' => 'draft']).'">
                                        <i class="fas fa-edit"></i>'.trans("messages.edit").'</a>
                                        </li>';
                            }else{
                                $text .='<li>
                                            <a target="_blank" href="'.action("SellPosController@edit", [$row->id]).'">
                                            <i class="fas fa-edit"></i>'.trans("messages.edit").'</a>
                                            </li>';
                            }
                        }
                        $business_module = \App\Business::find($row->business_id);

                        $id_module       = (!empty($business_module))?(($business_module->draft_print_module != null )? $business_module->draft_print_module:null):null;

                        if(!empty($business_module)){
                            if($business_module->draft_print_module != null && $business_module->draft_print_module != "[]" ){
                                $all_pattern = json_decode($business_module->draft_print_module);
                            }else{
                                $id_module = null;
                            }
                        }else{
                            $id_module = null ;
                        }

                        if($id_module != null){
                            $text .= '<li> <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no).'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>';
                            $text .= '<div style="border:3px solid #ee680e;background-color:#ee680e">';
                            foreach($all_pattern as $one_pattern){
                                $pat   = \App\Models\PrinterTemplate::find($one_pattern); 
                                if(!empty($pat)){
                                    $text .= '<a target="_blank" class="btn btn-info" style="width:100%;border-radius:0px;text-align:left;background-color:#474747 !important;color:#f7f7f7 !important;border:2px solid #ee680e !important" href="'. action("Report\PrinterSettingController@generatePdf",["id"=>$one_pattern,"sell_id"=>$row->id]) .'"> <i class="fas fa-print" style="color:#ee680e"  aria-hidden="true"></i> Print By <b style="color:#ee680e">'.$pat->name_template.'</b> </a>';
                                }
                            }
                            $text .= '</div>';
                            
                            
                            $text .= '</li>';
                        }else{
                            $text .= '<li>
                                            <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no).'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>
                                             
                                            </li> ';
                        }
                        
                         
                        if ( (auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access")) && config("constants.enable_convert_draft_to_invoice")) {
                            $text .= '<li>
                            <a href="'.\URL::to("reports/sell/".$id."?invoice_no=".$invoice_no).'" target="_blank"><i class="fas fa-print" aria-hidden="true"></i>'.trans("lang_v1.convert_to_invoice").'</a>
                            </li>';
                        }
                        if ($row->sub_status != "proforma") {
                           $text .=' <li>
                                    <a href="'.action("SellPosController@convertToQoutation", [$row->id]).'" class="convert-to-proforma">
                                    <i class="fas fa-sync-alt"></i>'.trans("lang_v1.convert_to_quotation").'</a>
                                    </li> ';
                        }
                         if ($is_admin || auth()->user()->can('sell.delete')) {
                            $text  .='<li>
                                        <a href="'.\URL::to('/sells/destroy/'.$row->id).'" class="delete-draft">
                                        <i class="fas fa-trash"></i>'.trans("messages.delete").'</a>
                                        </li>';
                         }
                        if($row->sub_status == "quotation") {
                            $text .= '<li><a href="'.action("SellPosController@showInvoiceUrl", [$row->id]).'" 
                                  class="view_invoice_url"><i class="fas fa-eye"></i>'.trans("lang_v1.view_quote_url").'</a></li>
                            <li><a href="#" data-href="'.action("NotificationController@getTemplate",
                             ["transaction_id" => $row->id,"template_for" => "new_quotation"]).'" class="btn-modal" data-container=".view_modal">
                             <i class="fa fa-envelope" aria-hidden="true"></i>' . __("lang_v1.new_quotation_notification") .
                              '</a></li>';
                        }
                        $text .= '<li><a href="#" data-href="' . action('HomeController@formAttach', ["type" => "sale","id" => $row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-paperclip" aria-hidden="true"></i> ' . __("Add Attachment") . '</a></li>';
                    
                        $text .= '
                                    </ul>
                                    </div>
                                    ';
                        return $text;
                    } 
                    
                    
                )->removeColumn('id')
                ->addColumn("project_no",function($row)
                {
                    $Transactoin = Transaction::find($row->id);
                    return $Transactoin->project_no;
                })->addColumn('agents', function ($row) {
                    $id  =  $row->agent_id ;
                    if($id != null){
                        $AGENT = \App\User::find($id);  
                        $html = $AGENT->first_name;
                    }else{
                        $html = "";
                    }
                    return $html;
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
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }

                    if ($row->sub_status == 'proforma') {
                        $invoice_no .= '<br><span class="label bg-gray">' . __('lang_v1.proforma_invoice') . '</span>';
                    }
                    if ($row->sub_status == 'quotation') {
                        $invoice_no .= '<br><span class="label bg-gray">' . __('lang_v1.quotation') . '</span>';
                    }

                    return $invoice_no . $attach;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('total_items', '{{@format_quantity($total_items)}}')
                ->editColumn('total_quantity', '{{@format_quantity($total_quantity)}}')
                ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br>@endif {{$name}}')
                ->filterColumn('conatct_name', function ($query, $keyword) {
                    $query->where( function($q) use($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                        ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('added_by', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }])->addColumn(
                        'store',function ($row) {
                            $transactions = Transaction::where("id" , $row->id)->get();
                            // dd($transactions);
                            $store  = "";
                            if(!empty($transactions)){
                                foreach ($transactions as $transaction) {
                                    // dd($transaction->store);
                                    $business_id = request()->session()->get('user.business_id');

                                    $warehouses = Warehouse::where("business_id",$business_id)->select(["description","status","mainStore","id","name"])->get();

                                    if (!empty($warehouses)) {
                                        foreach ($warehouses as $warehouse) {
                                            if($transaction->store == $warehouse->id){
                                                // dd($warehouse->id . " - - " . $transaction->store);
                                                $store = $warehouse->name;
                                            }
                                        }
                                            
                                    }




                                }
                            }
                        return $store;
                        }
                    )
                ->rawColumns(['action', 'deliver_status','agents' , 'project_no',  'invoice_no', 'transaction_date', 'conatct_name'])
                ->make(true);
        }
    }
    
    public function getQuatationApproved()
    {
        
        if (request()->ajax()) {
            $is_admin = $this->businessUtil->is_admin(auth()->user());
            $business_id = request()->session()->get('user.business_id');
            $is_quotation = request()->input('is_quotation', 0);
            
            $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin('transaction_sell_lines as tsl', function($join) {
                    $join->on('transactions.id', '=', 'tsl.transaction_id')
                        ->whereNull('tsl.parent_sell_line_id');
                })
                ->where('transactions.business_id', $business_id)
                ->whereIn('transactions.type', ['sale',"sell"])
                ->where("ecommerce",0)
                ->whereIn('transactions.status', ['ApprovedQuotation' ,"draft" , "delivered", "final"])
                ->whereIn('transactions.sub_status', ['proforma',"f"])
                ->select(
                    'transactions.id',
                    'transactions.agent_id',
                    'transactions.business_id',
                    'transactions.first_ref_no',
                    'transactions.previous',
                    'transactions.refe_no',
                    'transactions.payment_status',
                    'transactions.document',
                    'transactions.separate_type',
                    'transactions.separate_parent',
                    'transactions.final_total',
                    'transactions.ecommerce',
                    'transactions.tax_amount',
                    'transactions.cost_center_id',
                    'transactions.total_before_tax',
                    'transaction_date',
                    'invoice_no',
                    'transactions.status',
                    'contacts.name',
                    'contacts.mobile',
                    'contacts.supplier_business_name',
                    'bl.name as business_location',
                    'is_direct_sale',
                    'sub_status',
                    DB::raw('COUNT( DISTINCT tsl.id) as total_items'),
                    DB::raw('SUM(tsl.quantity) as total_quantity'),
                    DB::raw('(SELECT SUM(IF(TP.is_return = 1,-1*TP.amount,TP.amount)) FROM transaction_payments AS TP WHERE
                        TP.transaction_id=transactions.id AND TP.return_payment=0) as total_paid'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as added_by")
                );
            
            // if ($is_quotation == 1) {
            //     $sells->where('transactions.sub_status', 'quotation');
            // }
            
           $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;

            if(!$is_admin){
                        
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
                     
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
                
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transaction_date', '>=', $start)
                            ->whereDate('transaction_date', '<=', $end);
            }

            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }
            $os_check =  \App\User::where('id',auth()->id())->whereHas('roles',function($query){
                            $query->where('id',1);
                            })->first();
                if(empty($os_check)) {
                $users =  \App\User::where('id',auth()->id())->first();
                if(!empty($users)) {
                    $patterns = json_decode($users->pattern_id);
                    $sells->whereIn('transactions.pattern_id',$patterns);
                }
            }
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }
            if (!empty(request()->project_no)) {
                 
                $sells->where('transactions.project_no',request()->project_no);
            }
            if (!empty(request()->draft_no)) {
                 
                $sells->where('transactions.refe_no',request()->draft_no);
            }
            if (!empty(request()->quotation_no)) {
                 
                $sells->where('transactions.first_ref_no',request()->quotation_no);
            }
            if (!empty(request()->previous_no)) {
                 
                $sells->where('transactions.previous',request()->previous_no);
            }
            if (request()->has('agent_id')) {
                
                $agent_id = request()->get('agent_id');
                if (!empty($agent_id)) {
                    $sells->where('transactions.agent_id', $agent_id);
                }
            }
            if (!empty(request()->converted)) {
                $type = request()->converted;
                  
                 if($type == 1){
                     $sells->where('transactions.sub_status',"f");
                 }else if($type == 2){
                     $sells->where('transactions.sub_status',"!=","f");
                 }
             }
            if (request()->has('cost_center_id')) {
                $cost_center_id = request()->get('cost_center_id');
                if (!empty($cost_center_id)) {
                    $sells->where('transactions.cost_center_id', $cost_center_id);
                }
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }

            if ($is_woocommerce) {
                $sells->addSelect('transactions.woocommerce_order_id');
            }

            $sells->where('transactions.ecommerce', 0);
            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    function($row) use($is_admin){
                        $text =  '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        $text .= '<li>
                            <a href="#" data-href="/sells/'.$row->id.'" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>'.trans("messages.view").'</a>
                            </li>';

                        if ($is_admin|| auth()->user()->can("sell.can_edit")  ){
                            if ($row->is_direct_sale == 1) {
                                $text .= '<li>
                                <a target="_blank" href="/sells/'.$row->id.'/edit"><i class="fas fa-edit"></i> '.trans("messages.edit").'</a>
                                </li>';
                            }else{
                                $text .='<li>
                                        <a target="_blank" href="/sells/'.$id.'/edit"><i class="fas fa-edit"></i>'.trans("messages.edit").'</a>
                                        </li>'; 
                            }
                        } 
                        
                    if($row->sub_status == "f" || $row->sub_status == "final"){
                            $business_module = \App\Business::find($row->business_id);

                            $id_module       = (!empty($business_module))?(($business_module->approve_quotation_print_module != null )? $business_module->approve_quotation_print_module:null):null;

                            if(!empty($business_module)){
                                if($business_module->approve_quotation_print_module != null && $business_module->approve_quotation_print_module != "[]" ){
                                    $all_pattern = json_decode($business_module->approve_quotation_print_module);
                                }else{
                                    $id_module = null;
                                }
                            }else{
                                $id_module = null ;
                            }

                            if($id_module != null){
                                $text .= '<li> <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no.'&old=1').'" target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>';
                                $text .= '<div style="border:3px solid #ee680e;background-color:#ee680e">';
                                foreach($all_pattern as $one_pattern){
                                    $pat   = \App\Models\PrinterTemplate::find($one_pattern); 
                                    if(!empty($pat)){
                                        $text .= '<a target="_blank" class="btn btn-info" style="width:100%;border-radius:0px;text-align:left;background-color:#474747 !important;color:#f7f7f7 !important;border:2px solid #ee680e !important" href="'. action("Report\PrinterSettingController@generatePdf",["id"=>$one_pattern,"sell_id"=>$row->id]) .'"> <i class="fas fa-print" style="color:#ee680e"  aria-hidden="true"></i> Print By <b style="color:#ee680e">'.$pat->name_template.'</b> </a>';
                                    }
                                }
                                $text .= '</div>';
                                
                                
                                $text .= '</li>';
                            }else{
                                $text .='<li>
                                    <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no.'&old=1').'" target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>
                                </li>
                                ';
                            }
                    }
                    if($row->sub_status != "f" && $row->sub_status != "final"){ 
                            $business_module = \App\Business::find($row->business_id);

                            $id_module       = (!empty($business_module))?(($business_module->approve_quotation_print_module != null )? $business_module->approve_quotation_print_module:null):null;

                            if(!empty($business_module)){
                                if($business_module->approve_quotation_print_module != null && $business_module->approve_quotation_print_module != "[]" ){
                                    $all_pattern = json_decode($business_module->approve_quotation_print_module);
                                }else{
                                    $id_module = null;
                                }
                            }else{
                                $id_module = null ;
                            }

                            if($id_module != null){
                                $text .= '<li> <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no).'" target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>';
                                $text .= '<div style="border:3px solid #ee680e;background-color:#ee680e">';
                                foreach($all_pattern as $one_pattern){
                                    $pat   = \App\Models\PrinterTemplate::find($one_pattern); 
                                    if(!empty($pat)){
                                        $text .= '<a target="_blank" class="btn btn-info" style="width:100%;border-radius:0px;text-align:left;background-color:#474747 !important;color:#f7f7f7 !important;border:2px solid #ee680e !important" href="'. action("Report\PrinterSettingController@generatePdf",["id"=>$one_pattern,"sell_id"=>$row->id]) .'"> <i class="fas fa-print" style="color:#ee680e"  aria-hidden="true"></i> Print By <b style="color:#ee680e">'.$pat->name_template.'</b> </a>';
                                    }
                                }
                                $text .= '</div>';
                                
                                
                                $text .= '</li>';
                            }else{
                                $text .='<li>
                                <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no).'" target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>
                                 
                                </li>';
                            }
                            $find = \App\Transaction::where("separate_parent",$row->id)->first();
                            if($find == null){
                                $text .= '<li>
                                <a data-href="/sells/convert-to-invoice/'.$row->id.'" class="convert-to-invoice"><i class="fas fa-sync-alt"></i>'.trans("lang_v1.convert_to_invoice").'</a>
                                </li>';
                            }
                    }
                    if($row->sub_status != "proforma"){
                        if($row->sub_status != "f" && $row->sub_status != "final"){
                        
                            $text .= '
                            
                            <li>
                            <a href="{{action(\'SellPosController@convertToProforma\', [$row->id])}}" class="convert-to-proforma"><i class="fas fa-sync-alt"></i> '.trans("lang_v1.convert_to_proforma").'</a>
                            </li> ';
                        }
                    }
                    if ($is_admin || auth()->user()->can('sell.delete')) {
                        $text .= '<li>
                                <a  data-href="'.\URL::to("/sells/destroy/".$row->id).'" class="delete-sale-Q"><i class="fas fa-trash"></i>'.trans("messages.delete").'</a>
                                </li>';
                    }
                        
                                    
                       if($row->sub_status == "quotation"){
                           $text .=' <li><a href="'.action("SellPosController@showInvoiceUrl", [$row->id]).'" class="view_invoice_url"><i class="fas fa-eye"></i>'.trans("lang_v1.view_quote_url").'</a></li>
                                    <li><a href="#" data-href="'.action("NotificationController@getTemplate", ["transaction_id" => $row->id,"template_for" => "new_quotation"]).'" class="btn-modal"
                                    data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i>' . __("lang_v1.new_quotation_notification") . '</a></li>
                                   ';
                       }
                       $text .= '<li><a href="#" data-href="' . action('HomeController@formAttach', ["type" => "sale","id" => $row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-paperclip" aria-hidden="true"></i> ' . __("Add Attachment") . '</a></li>';
                       $text .='</ul>
                                    </div>
                                    ';
                        return $text;
                    }
                    
                )->addColumn(
                    'deliver_status', function ($row)  {
                        $TransactionSellLine = TransactionSellLine::where("transaction_id",$row->id)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                        $transaction         = \App\Transaction::where("separate_parent",$row->id)->where("separate_type","partial")->get();
                        if(count($transaction)>0){
                            $DeliveredPrevious = 0;
                            $wrong             = 0;
                            foreach($transaction as $k => $item){
                                $DeliveredPrevious   += DeliveredPrevious::where("transaction_id",$item->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                                $wrong               += DeliveredWrong::where("transaction_id",$item->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                            }

                            if($DeliveredPrevious == null){
                                $payment_status = "not_delivereds";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong,  "type" => "normal" ,"approved"=> true]);
                            }else if($TransactionSellLine <= $DeliveredPrevious){
                                $payment_status = "delivereds";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id , "wrong" => $wrong,  "type" => "normal" ,"approved"=> true ]);
                            } else if( $DeliveredPrevious < $TransactionSellLine ){
                                $payment_status = "separates";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong,  "type" => "normal" ,"approved"=> true]);
        
        
                            }
                        }else{
                        
                            $TransactionSellLine = TransactionSellLine::where("transaction_id",$row->id)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                            $DeliveredPrevious   = DeliveredPrevious::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                            $wrong               = DeliveredWrong::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;

                            if($DeliveredPrevious == null){
                                $payment_status = "not_delivereds";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong,  "type" => "normal" ,"approved"=> true]);
                            }else if($TransactionSellLine <= $DeliveredPrevious){
                                $payment_status = "delivereds";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id , "wrong" => $wrong,  "type" => "normal" ,"approved"=> true ]);
                            } else if( $DeliveredPrevious < $TransactionSellLine ){
                                $payment_status = "separates";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong,  "type" => "normal" ,"approved"=> true]);
        
        
                            }
                        }
                })->removeColumn('id')
               ->addColumn(
                    'payment_status',
                        function ($row) {
                            $payment_status      = Transaction::getPaymentStatus($row);
                            $transaction         = \App\Transaction::where("separate_parent",$row->id)->where("separate_type","partial")->get();
                            $lock                = 0;
                            if(count($transaction)>0){
                                $lock                = 1;
                            }
                            if($payment_status == null){
                                $payment_status = "due" ; 
                            }
                            $cheques        = \App\Models\Check::where("transaction_id",$row->id)->whereIn("status",[0,3,4])->get();
                            return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id ,"cheques" => $cheques , "lock"  => $lock]);
                      
                        }
                            
                )->addColumn("converted",function($row){
                    if($row->sub_status == "f" || $row->sub_status == "final"){
                        $html = "converted";
                    }else{
                        $html = "";
                    }
                    return $html;
                })
                ->addColumn("converted_date",function($row){
                    if($row->sub_status == "f" || $row->sub_status == "final"){
                        $html = $row->transaction_date;
                    }else{
                        $html = "";
                    }
                    return $html;
                     
                })
                ->addColumn("refe_no",function($row){
                    $html = $row->refe_no;
                    return $html;
                     
                })
                ->addColumn("first_ref_no",function($row){
                    $html = $row->first_ref_no;
                    return $html;
                })
                ->addColumn("previous",function($row){
                    $html = $row->previous;
                    return $html;
                })
                ->editColumn(
                    'final_total',
                    '<span class="final-total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
                )
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining =  $row->final_total - $row->total_paid;
                    $total_remaining_html = '<span class="payment_due" data-orig-value="' . $total_remaining . '">' . $this->transactionUtil->num_f($total_remaining, true) . '</span>';


                    return $total_remaining_html;
                })
                ->editColumn(
                    'tax_amount',
                    '<span class="total-tax" data-orig-value="{{$tax_amount}}">@format_currency($tax_amount)</span>'
                )
                ->editColumn(
                    'total_paid',
                    '<span class="total-paid" data-orig-value="{{$total_paid}}">@format_currency($total_paid)</span>'
                )
                ->editColumn(
                    'total_before_tax',
                    '<span class="total_before_tax" data-orig-value="{{$total_before_tax}}">@format_currency($total_before_tax)</span>'
                )
                ->editColumn('invoice_no', function ($row) {
                     
                    $parent = "";
                    if(!empty($row->document)){
                         if($row->document != "[]"){
                            $attach = "<br>
                                        <a class='btn-modal' data-href='".\URL::to('sells/attachment/'.$row->id)."' data-container='.view_modal'>
                                                <i class='fas fa-paperclip'></i>
                                                                                        
                                            </a>
                                        ";
                         }
                    }else{
                        $attach = "";
                    }$parent='';
                    $transaction = \App\Transaction::where("separate_parent",$row->id)->get();
                    if(count($transaction)>0){
                        $parent =  '<div class="btn-group">
                                    <button  type="button" class="btn btn-note dropdown-toggle btn-xs" 
                                        style="border:1px solid black;margin-top:10px" data-toggle="dropdown" aria-expanded="false">' .
                                        __("messages.related_bill") .
                                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        foreach($transaction as $k => $item){
                            // $parent .= "\n <".($k+1).">".$item->invoice_no;

                            $parent  .= "\n<a href='#' class='btn btn-info btn-modal' data-href='/sells/".$item->id."'data-container='.view_modal'>".$item->invoice_no."</a>";
                        }
                        $parent .='</ul>
                        </div>
                        '; 
                    }
                    $invoice_no = $row->invoice_no ;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                  
                    if ($row->status == "final" && $row->sub_status == 'proforma'  ) {
                        $invoice_no .= '<br><span class="label bg-gray">' . __('lang_v1.invoice') . '</span>';
                    }else if ($row->status == "draft"  && $row->sub_status == 'proforma') {
                        $invoice_no .= '<br><span class="label bg-gray">' . __('lang_v1.proforma_invoice') . '</span>';
                    }else if ($row->sub_status == 'quotation') {
                        $invoice_no .= '<br><span class="label bg-gray">' . __('lang_v1.quotation') . '</span>';
                    }

                    return $invoice_no . $attach .$parent;
                })
                ->addColumn('agents', function ($row) {
                    $id  =  $row->agent_id ;
                    if($id != null){
                        $AGENT = \App\User::find($id);  
                        $html = $AGENT->first_name;
                    }else{
                        $html = "";
                    }
                    return $html;
                })
                ->addColumn('cost_center_id', function ($row) {
                     $id  =  $row->cost_center_id ;
                   
                    $cost_centers =  \App\Account::cost_centers();
                    $cost = "";
                    foreach($cost_centers as $key => $ct){
                        if($id == $key ){
                            $cost =  $ct;
                            break; 
                        }  
                    }
                    return $cost;
                })
                ->addColumn("project_no",function($row)
                {
                    $Transactoin = Transaction::find($row->id);
                    return $Transactoin->project_no;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('total_items', '{{@format_quantity($total_items)}}')
                ->editColumn('total_quantity', '{{@format_quantity($total_quantity)}}')
                ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br>@endif {{$name}}')
                ->filterColumn('conatct_name', function ($query, $keyword) {
                    $query->where( function($q) use($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                        ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('added_by', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }])->addColumn(
                        'store',function ($row) {
                            $transactions = Transaction::where("id" , $row->id)->get();
                            // dd($transactions);
                            $store  = "";
                            if(!empty($transactions)){
                                foreach ($transactions as $transaction) {
                                    // dd($transaction->store);
                                    $business_id = request()->session()->get('user.business_id');

                                    $warehouses = Warehouse::where("business_id",$business_id)->select(["description","status","mainStore","id","name"])->get();

                                    if (!empty($warehouses)) {
                                        foreach ($warehouses as $warehouse) {
                                            if($transaction->store == $warehouse->id){
                                                // dd($warehouse->id . " - - " . $transaction->store);
                                                $store = $warehouse->name;
                                            }
                                        }
                                            
                                    }




                                }
                            }
                        return $store;
                        }
                    )
                ->rawColumns(['action', 'converted','total_remaining','tax_amount','total_paid','final_total','first_ref_no','previous', 'refe_no', 'converted_date' ,'deliver_status','payment_status' ,'agents','cost_center_id', 'project_no',  'invoice_no', 'transaction_date', 'conatct_name'])
                ->make(true);
        }
    }

  
    public function getQuatationList()
    {

        if (request()->ajax()) {
            $is_admin = $this->businessUtil->is_admin(auth()->user());
            $business_id = request()->session()->get('user.business_id');
            $is_quotation = request()->input('is_quotation', 1);
            
            $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin('transaction_sell_lines as tsl', function($join) {
                    $join->on('transactions.id', '=', 'tsl.transaction_id')
                        ->whereNull('tsl.parent_sell_line_id');
                })
                ->where('transactions.business_id', $business_id)
                ->whereIn('transactions.type', ['sell',"sale"])
                ->where('transactions.status', 'draft')
                ->where("ecommerce",0)
                ->where('transactions.sub_status', 'quotation')
                ->select(
                    'transactions.id',
                    'transactions.agent_id',
                    'transactions.business_id',
                    'transactions.cost_center_id',
                    'transactions.refe_no',
                    'transaction_date',
                    'transactions.document',
                    'invoice_no',
                    'contacts.name',
                    'contacts.mobile',
                    'contacts.supplier_business_name',
                    'bl.name as business_location',
                    'is_direct_sale',
                    'sub_status',
                    DB::raw('COUNT( DISTINCT tsl.id) as total_items'),
                    DB::raw('SUM(tsl.quantity) as total_quantity'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as added_by")
                );
            
            if ($is_quotation == 1) {
                $sells->where('transactions.sub_status', 'quotation');
            }
            
             $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;

            if(!$is_admin){
                        
                $sells->where('transactions.created_by', request()->session()->get('user.id'));
                     
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
                
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transaction_date', '>=', $start)
                            ->whereDate('transaction_date', '<=', $end);
            }
            if (!empty(request()->project_no)) {
                $sells->where('transactions.project_no',request()->project_no);
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }
            $os_check =  \App\User::where('id',auth()->id())->whereHas('roles',function($query){
                            $query->where('id',1);
                            })->first();
            if(empty($os_check)) {
            $users =  \App\User::where('id',auth()->id())->first();
            if(!empty($users)) {
                $patterns           = json_decode($users->pattern_id);
                $sells->whereIn('transactions.pattern_id',$patterns);
            }
            }
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }
            if (request()->has('agents_id')) {
                
                $agent_id = request()->get('agents_id');
                if (!empty($agent_id)) {
                    $sells->where('transactions.agent_id', $agent_id);
                }
            }
            if (request()->has('refe_no')) {
                $refe_no = request()->get('refe_no');
                if (!empty($refe_no)) {
                    $sells->where('transactions.refe_no', $refe_no);
                }
            }
            if (request()->has('cost_center_id')) {
                $cost_center_id = request()->get('cost_center_id');
                if (!empty($cost_center_id)) {
                    $sells->where('transactions.cost_center_id', $cost_center_id);
                }
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }

            if ($is_woocommerce) {
                $sells->addSelect('transactions.woocommerce_order_id');
            }

            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',function($row) use($is_admin){
                        $text = '<div class="btn-group">
                                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                            data-toggle="dropdown" aria-expanded="false">' .
                                            __("messages.actions") .
                                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                            </span>
                                        </button>';
                        $text .=' <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                            <li>
                                            <a href="#" data-href="'.action('SellController@show', [$row->id]).'" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i> '.trans("messages.view").'</a>
                                            </li>';
                                    if($is_admin || auth()->user()->can('sell.can_edit')){

                                        if($row->is_direct_sale == 1){
                                            $text .= '<li>
                                            <a target="_blank" href="'.action('SellController@edit', [$row->id, "status"=>"quotation"]).'"><i class="fas fa-edit"></i>'.trans("messages.edit").'</a>
                                            </li>';
                                        }else{
                                            $text .='<li>
                                            <a target="_blank" href="'.action('SellPosController@edit', [$row->id , "status"=>"quotation"]).'"><i class="fas fa-edit"></i> '.trans("messages.edit").'</a>
                                            </li>' ;
                                        }
                                    }

                                    $business_module = \App\Business::find($row->business_id);

                                    $id_module       = (!empty($business_module))?(($business_module->quotation_print_module != null )? $business_module->quotation_print_module:null):null;
        
                                    if(!empty($business_module)){
                                        if($business_module->quotation_print_module != null && $business_module->quotation_print_module != "[]" ){
                                            $all_pattern = json_decode($business_module->quotation_print_module);
                                        }else{
                                            $id_module = null;
                                        }
                                    }else{
                                        $id_module = null ;
                                    }
        
                                    if($id_module != null){
                                        $text .= '<li> <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no).'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>';
                                        $text .= '<div style="border:3px solid #ee680e;background-color:#ee680e">';
                                        foreach($all_pattern as $one_pattern){
                                            $pat   = \App\Models\PrinterTemplate::find($one_pattern); 
                                            if(!empty($pat)){
                                                $text .= '<a target="_blank" class="btn btn-info" style="width:100%;border-radius:0px;text-align:left;background-color:#474747 !important;color:#f7f7f7 !important;border:2px solid #ee680e !important" href="'. action("Report\PrinterSettingController@generatePdf",["id"=>$one_pattern,"sell_id"=>$row->id]) .'"> <i class="fas fa-print" style="color:#ee680e"  aria-hidden="true"></i> Print By <b style="color:#ee680e">'.$pat->name_template.'</b> </a>';
                                            }
                                        }
                                        $text .= '</div>';
                                        $text .= '</li>';
                                    }else{
                                        $text .= '<li>
                                                     <a href="'.\URL::to('reports/sell/'.$row->id.'?invoice_no='.$row->invoice_no).'"  target="_blank" ><i class="fas fa-print" aria-hidden="true"></i>'.trans("messages.print").'</a>
                                                 </li> ';
                                    }

                                    
                                    if( (auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access")) && config("constants.enable_convert_draft_to_invoice")){
                                        $text .='<li>
                                                    <a href="'.action('SellPosController@convertToInvoice', [$row->id]).'" class="convert-draft"><i class="fas fa-sync-alt"></i>'.trans("lang_v1.convert_to_invoice").'</a>
                                                    </li> ' ;  
                                    }
                                    if($row->sub_status != "proforma"){
                                        $text .='<li>
                                                <a href="'.action('SellPosController@convertToProforma', [$row->id]).'" class="convert-to-proforma"><i class="fas fa-sync-alt"></i>'.trans("lang_v1.convert_to_proforma").'</a>
                                                </li>';
                                    }
                                    if (request()->session()->get("user.id") == 1){
                                        $text .='<li>
                                                <a href="'.\URL::to('/sells/destroy/'.$row->id).'" class="delete-sale"><i class="fas fa-trash"></i>'.trans("messages.delete").'</a>
                                                </li>';
                                    }
                                    
                                    
                                    if($row->sub_status == "quotation"){
                                        $text.='<li><a href="'.action("SellPosController@showInvoiceUrl", [$row->id]).'" class="view_invoice_url"><i class="fas fa-eye"></i>'.trans("lang_v1.view_quote_url").'</a></li>
                                                        <li><a href="#" data-href="'.action("NotificationController@getTemplate", ["transaction_id" => $row->id,"template_for" => "new_quotation"]).'" class="btn-modal"
                                                        data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i>' . __("lang_v1.new_quotation_notification") . '</a></li>
                                                        ';
                                    }
                                    $text .= '<li><a href="#" data-href="' . action('HomeController@formAttach', ["type" => "sale","id" => $row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-paperclip" aria-hidden="true"></i> ' . __("Add Attachment") . '</a></li>';
                    
                                    $text .='</ul>
                                    </div>
                                    ';
                        return $text;
                    }
                    
                )
                ->removeColumn('id')
                ->addColumn('agents', function ($row) {
                    $id  =  $row->agent_id ;
                    if($id != null){
                        $AGENT = \App\User::find($id);  
                        $html = $AGENT->first_name;
                    }else{
                        $html = "";
                    }
                    return $html;
                })
                ->addColumn('refe_no', function ($row) {
                    $html  =  $row->refe_no ;
                    return $html;
                })
                ->addColumn('cost_center_id', function ($row) {
                 
                    $id  =  $row->cost_center_id ;
                   
                    $cost_centers =  \App\Account::cost_centers();
                    $cost = "";
                    foreach($cost_centers as $key => $ct){
                        if($id == $key ){
                            $cost =  $ct;
                            break; 
                        }  
                    }
                    return $cost;
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
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }

                    if ($row->sub_status == 'proforma') {
                        $invoice_no .= '<br><span class="label bg-gray">' . __('lang_v1.proforma_invoice') . '</span>';
                    }
                    if ($row->sub_status == 'quotation') {
                        $invoice_no .= '<br><span class="label bg-gray">' . __('lang_v1.quotation') . '</span>';
                    }

                    return $invoice_no . $attach;
                })
                ->addColumn("project_no",function($row)
                {
                    $Transactoin = Transaction::find($row->id);
                    return $Transactoin->project_no;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('total_items', '{{@format_quantity($total_items)}}')
                ->editColumn('total_quantity', '{{@format_quantity($total_quantity)}}')
                ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br>@endif {{$name}}')
                ->filterColumn('conatct_name', function ($query, $keyword) {
                    $query->where( function($q) use($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                        ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('added_by', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }])->addColumn(
                        'store',function ($row) {
                            $transactions = Transaction::where("id" , $row->id)->get();
                            // dd($transactions);
                            $store  = "";
                            if(!empty($transactions)){
                                foreach ($transactions as $transaction) {
                                    // dd($transaction->store);
                                    $business_id = request()->session()->get('user.business_id');

                                    $warehouses = Warehouse::where("business_id",$business_id)->select(["description","status","mainStore","id","name"])->get();

                                    if (!empty($warehouses)) {
                                        foreach ($warehouses as $warehouse) {
                                            if($transaction->store == $warehouse->id){
                                                // dd($warehouse->id . " - - " . $transaction->store);
                                                $store = $warehouse->name;
                                            }
                                        }
                                            
                                    }




                                }
                            }
                        return $store;
                        }
                    )
                ->rawColumns(['action', 'deliver_status' ,'refe_no',  'invoice_no', 'transaction_date', 'conatct_name'])
                ->make(true);
        }
    }
    /**
     * Creates copy of the requested sale.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicateSell($id)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $transaction = Transaction::where('business_id', $business_id)
                            ->whereIn('type', ['sell',"sale"])
                            ->findorfail($id);
            $duplicate_transaction_data = [];
            foreach ($transaction->toArray() as $key => $value) {
                if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                    $duplicate_transaction_data[$key] = $value;
                }
            }
            $duplicate_transaction_data['status'] = 'draft';
            $duplicate_transaction_data['payment_status'] = null;
            $duplicate_transaction_data['transaction_date'] =  \Carbon::now();
            $duplicate_transaction_data['created_by'] = $user_id;
            $duplicate_transaction_data['invoice_token'] = null;

            DB::beginTransaction();
            $duplicate_transaction_data['invoice_no'] = $this->transactionUtil->getInvoiceNumber($business_id, 'draft', $duplicate_transaction_data['location_id']);

            //Create duplicate transaction
            $duplicate_transaction = Transaction::create($duplicate_transaction_data);

            //Create duplicate transaction sell lines
            $duplicate_sell_lines_data = [];

            foreach ($transaction->sell_lines as $sell_line) {
                $new_sell_line = [];
                foreach ($sell_line->toArray() as $key => $value) {
                    if (!in_array($key, ['id', 'transaction_id', 'created_at', 'updated_at', 'lot_no_line_id'])) {
                        $new_sell_line[$key] = $value;
                    }
                }

                $duplicate_sell_lines_data[] = $new_sell_line;
            }

            $duplicate_transaction->sell_lines()->createMany($duplicate_sell_lines_data);

            DB::commit();

            $output = ['success' => 0,
                            'msg' => trans("lang_v1.duplicate_sell_created_successfully")
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
        }

        if (!empty($duplicate_transaction)) {
            if ($duplicate_transaction->is_direct_sale == 1) {
                return redirect()->action('SellController@edit', [$duplicate_transaction->id])->with(['status', $output]);
            } else {
                return redirect()->action('SellPosController@edit', [$duplicate_transaction->id])->with(['status', $output]);
            }
        } else {
            abort(404, 'Not Found.');
        }
    }

    /**
     * Shows modal to edit shipping details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editShipping($id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if ( !$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']) ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $transaction = Transaction::where('business_id', $business_id)
                                ->with(['media', 'media.uploaded_by_user'])
                                ->findorfail($id);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        return view('sell.partials.edit_shipping')
               ->with(compact('transaction', 'shipping_statuses'));
    }

    /**
     * Update shipping.
     *
     * @param  Request $request, int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateShipping(Request $request, $id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if ( !$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']) ) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only([
                    'shipping_details', 'shipping_address',
                    'shipping_status', 'delivered_to', 'shipping_custom_field_1', 'shipping_custom_field_2', 'shipping_custom_field_3', 'shipping_custom_field_4', 'shipping_custom_field_5'
                ]);
            $business_id = $request->session()->get('user.business_id');

            
            $transaction = Transaction::where('business_id', $business_id)
                                ->findOrFail($id);

            $transaction_before = $transaction->replicate();

            $transaction->update($input);

            $this->transactionUtil->activityLog($transaction, 'shipping_edited', $transaction_before);

            $output = ['success' => 1,
                            'msg' => trans("lang_v1.updated_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display list of shipments.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipments()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if ( !$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']) ) {
            abort(403, 'Unauthorized action.');
        }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
      
        $sales_representative = User::forDropdown($business_id, false, false, true);

        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');

        //Service staff filter
        $service_staffs = null;
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }

        return view('sell.shipments')->with(compact('shipping_statuses'))
                ->with(compact('business_locations', 'customers', 'sales_representative', 'is_service_staff_enabled', 'service_staffs'));
    }

    public function viewMedia($model_id)
    {
        if (request()->ajax()) {
            $model_type = request()->input('model_type');
            $business_id = request()->session()->get('user.business_id');

            $query = Media::where('business_id', $business_id)
                        ->where('model_id', $model_id)
                        ->where('model_type', $model_type);

            $title = __('lang_v1.attachments');
            if (!empty(request()->input('model_media_type'))) {
                $query->where('model_media_type', request()->input('model_media_type'));
                $title = __('lang_v1.shipping_documents');
            }

            $medias = $query->get();

            return view('sell.view_media')->with(compact('medias', 'title'));
        }
    }

    public function max_qty()
    {
       if(request()->ajax()){
            $store_id    = request()->input("store");
            $product_id  = request()->input("product");
            $max_qty     = \App\Models\WarehouseInfo::where("store_id",$store_id)->where("product_id",$product_id)->sum("product_qty");  
            $output = [
                        "success"=>1,
                        "val"=>$max_qty
                    ];
            return $output;
       }
    }
    // check_msg  
    public function check_msg(Request $request  )
    {
        $pattern      = request()->input("pattern");
        $complete     = request()->input("complete");
        $type         = request()->input("type");
        $pattern_name = \App\Models\Pattern::find($pattern); 
        return view("alerts.check_attention")->with(compact(["pattern_name","complete","type"]));
    }

}
