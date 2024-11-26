<?php

namespace App\Http\Controllers;

use App\Account;
use Carbon\Carbon;
use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\CustomerGroup;
use App\Media;
use App\Product;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\Models\DeliveredPrevious;
use App\Models\TransactionDelivery;
use App\MovementWarehouse;
use App\TransactionSellLine;
use App\TypesOfService;
use App\Models\WarehouseInfo;
use App\Models\RecievedWrong;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\CashRegisterUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\Warranty;
use App\Models\Warehouse;

use App\Unit;
use App\InvoiceLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\InvoiceScheme;
use \Module;

class SellPosController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $contactUtil;
    protected $productUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $cashRegisterUtil;
    protected $moduleUtil;
    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(
        ContactUtil $contactUtil,
        ProductUtil $productUtil,
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        CashRegisterUtil $cashRegisterUtil,
        ModuleUtil $moduleUtil,
        NotificationUtil $notificationUtil
    ) {
        $this->contactUtil = $contactUtil;
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moduleUtil = $moduleUtil;
        $this->notificationUtil = $notificationUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => ''
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->can('sell.view') && auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $sales_representative = User::forDropdown($business_id, false, false, true);
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $is_cmsn_agent_enabled = request()->session()->get('business.sales_cmsn_agnt');
        $commission_agents = [];
        if (!empty($is_cmsn_agent_enabled)) {
            $commission_agents = User::forDropdown($business_id, false, true, true);
        }

        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');

        //Service staff filter
        $service_staffs = null;
        if ($is_service_staff_enabled) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        return view('sale_pos.index')->with(compact('business_locations', 'currency_details','customers', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'is_types_service_enabled', 'shipping_statuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || auth()->user()->can('sell.create') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair.create')))) {
            abort(403, 'Unauthorized action.');
        }

        //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse(action('HomeController@index'));
            }elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
            }
        } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
        }

        //like:repair

        $sub_type = request()->get('sub_type');

        //Check if there is a open register, if no then redirect to Create Register screen.
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
            return redirect()->action('CashRegisterController@create', ['sub_type' => $sub_type]);
        }

        
        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);
        
        $payment_lines[] = $this->dummyPaymentLine;
        
        $register_details = $this->cashRegisterUtil->getCurrentCashRegister(auth()->user()->id);
        $default_location = !empty($register_details->location_id) ? BusinessLocation::findOrFail($register_details->location_id) : null;

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        //set first location as default locaton
        if (empty($default_location)) {
            foreach ($business_locations as $id => $name) {
                $default_location = BusinessLocation::findOrFail($id);
                break;
            }
        }
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $payment_types = $this->productUtil->payment_types(null, true, $business_id);

        //Shortcuts
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id, false);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id, false);
        }

        //If brands, category are enabled then send else false.
        $categories = (request()->session()->get('business.enable_category') == 1) ? Category::catAndSubCategories($business_id) : false;


        $categories = Category::getCategorywithlocation($business_id, $default_location->id);
        //dd( Category::getCategorywithlocation(1,1));


        $brands = (request()->session()->get('business.enable_brand') == 1) ? Brands::forDropdown($business_id)
            ->prepend(__('lang_v1.all_brands'), 'all') : false;

        $change_return = $this->dummyPaymentLine;

        $types = Contact::getContactTypes();
        $customer_groups = CustomerGroup::forDropdown($business_id);

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false, true);
        }

        //Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);

        $default_price_group_id = !empty($default_location->selling_price_group_id) && array_key_exists($default_location->selling_price_group_id, $price_groups) ? $default_location->selling_price_group_id : null;

        //Types of service
        $types_of_service = [];
        if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
            $types_of_service = TypesOfService::forDropdown($business_id);
        }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $default_datetime = $this->businessUtil->format_date('now', true);

        $featured_products = !empty($default_location) ? $default_location->getFeaturedProducts() : [];

        //pos screen view from module
        $pos_module_data = $this->moduleUtil->getModuleData('get_pos_screen_view', ['sub_type' => $sub_type, 'job_sheet_id' => request()->get('job_sheet_id')]);
        $invoice_layouts = InvoiceLayout::forDropdown($business_id);

        $invoice_schemes = InvoiceScheme::forDropdown($business_id);
        $default_invoice_schemes = InvoiceScheme::getDefault($business_id);

        return view('sale_pos.create')
            ->with(compact(
                'business_locations',
                'bl_attributes',
                'business_details',
                'taxes',
                'payment_types',
                'walk_in_customer',
                'currency_details',
                'payment_lines',
                'default_location',
                'shortcuts',
                'commission_agent',
                'currency_details',
                'categories',
                'brands',
                'pos_settings',
                'change_return',
                'types',
                'customer_groups',
                'accounts',
                'price_groups',
                'types_of_service',
                'default_price_group_id',
                'shipping_statuses',
                'default_datetime',
                'featured_products',
                'sub_type',
                'pos_module_data',
                'invoice_schemes',
                'default_invoice_schemes',
                'invoice_layouts'
            ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        
        if (!auth()->user()->can('sell.create') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }
        $user_id               = $request->session()->get('user.id');
        $business_id           = $request->session()->get('user.business_id');
        # Check if subscribed or not, then check for users quota
        if(!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse(action('HomeController@index'));
            }elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
            }
        }elseif(!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
        }
        $sub_status_      = "";
        $final_status     = "";
        $final_sub_status = "";
        /* start save Pos */
        $is_direct_sale = false;
        if (!empty($request->input('is_direct_sale'))) {
            $is_direct_sale = true;
        }
        # Check if there is a open register, if no then redirect to Create Register screen.
        if (!$is_direct_sale && $this->cashRegisterUtil->countOpenedRegister() == 0) {
            return redirect()->action('CashRegisterController@create');
        }

        try {

            DB::beginTransaction();

            $input                 = $request->except('_token');
            $input['is_quotation'] = 0;
            if($input['status'] == 'quotation') { 
                $input['status']      = 'draft';
                $input['is_quotation']= 1;
                $input['sub_status']  = 'quotation';
                $sub_status_          = 'quotation';
            }elseif($input['status'] == 'ApprovedQuotation') { 
                $input['status']      = 'ApprovedQuotation';
                $input['sub_status']  = 'proforma';
                $sub_status_          = 'proforma';
            }elseif($input['status'] == 'proforma') { 
                $input['status']      = 'draft';
                $input['sub_status']  = 'proforma';
                $sub_status_          = 'proforma';
            }  

            # Check Customer credit limit 
            $is_credit_limit_exeeded       = $this->transactionUtil->isCustomerCreditLimitExeeded($input);
            if ($is_credit_limit_exeeded !== false) {
                $credit_limit_amount       = $this->transactionUtil->num_f($is_credit_limit_exeeded, true);
                $output = [
                    'success' => 0,
                    'msg'     => __('lang_v1.cutomer_credit_limit_exeeded', ['credit_limit' => $credit_limit_amount])
                ];
                if (!$is_direct_sale) {
                    return $output;
                } else {
                    return redirect()->action('SellController@index')->with('status', $output);
                }
            }
            
            if (!empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');
                
                //Check if subscribed or not, then check for users quota
                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                        return $this->moduleUtil->expiredResponse();
                    }elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                        return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
                    }
                } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                    return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
                }
               
                $user_id = $request->session()->get('user.id');
                $discount = [
                    'discount_type'   => (isset($input['discount_type']))?$input['discount_type']:null,
                    'discount_amount' => (isset($input['discount_type']))? (($input['discount_type'] != null)?$input['discount_amount']:0):0
                ]; 
                $invoice_total             = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_rate_id'], $discount);
                $invoice_total['tax']      = $request->input('tax_calculation_amount');
                $input['transaction_date'] =  (empty($request->input('transaction_date')))?(\Carbon::now()):$this->productUtil->uf_date($request->input('transaction_date'), true);
                if ($is_direct_sale) {
                    $input['is_direct_sale'] = 1; 
                }
                # Set commission agent
                $input['commission_agent'] = !empty($request->input('commission_agent')) ? $request->input('commission_agent') : null;
                $comm_agnt_setting         = $request->session()->get('business.sales_cmsn_agnt');
                if ($comm_agnt_setting == 'logged_in_user') {
                    $input['commission_agent'] = $user_id;
                }
                if (isset($input['exchange_rate']) && $this->transactionUtil->num_uf($input['exchange_rate']) == 0) {
                    $input['exchange_rate'] = 1;
                }
                # Customer group details
                $contact_id                 = $request->get('contact_id', null);
                $cg                         = $this->contactUtil->getCustomerGroup($business_id, $contact_id);
                $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;
                # set selling price group id
                $price_group_id             = $request->has('price_group') ? $request->input('price_group') : null;
                # If default price group for the location exists
                $price_group_id             = $price_group_id == 0 && $request->has('default_price_group') ? $request->input('default_price_group') : $price_group_id;
                $input['is_suspend']        = isset($input['is_suspend']) && 1 == $input['is_suspend']  ? 1 : 0;
                if($request->sale_note != null){
                    $it_terms = \App\Models\QuatationTerm::where("business_id",$business_id)->where("id",$request->sale_note)->first(); 
                    if ($input['is_suspend']) {
                         if (!empty($it_terms)) {
                            $input['sale_note'] = $it_terms->description;
                         }   
                    }   

                }
                # Generate reference number
                // if (!empty($input['is_recurring'])) {
                        # Update reference count
                //     $ref_count = $this->transactionUtil->setAndGetReferenceCount('subscription');
                //     $input['subscription_no'] = $this->transactionUtil->generateReferenceNumber('subscription', $ref_count);
                // }
                 
                # Update reference count
                $pat_id        = null;
                if ($request->pattern_id != null) {
                    $pat       = \App\Models\Pattern::find($request->pattern_id);
                    $pat_id    = ($pat)?$pat->id:null; 
                    if(!empty($pat)){
                        $input['invoice_scheme_id'] = $pat->invoice_scheme;
                    }
                }
                
                if (empty($request->input('project_no'))) {
                    $ref_count           = $this->transactionUtil->setAndGetReferenceCount('project_no',$business_id,$pat_id);
                    $number              = $this->transactionUtil->generateReferenceNumber('project_no', $ref_count, null, null,$pat_id);
                    $input['project_no'] = $number;
                }

                $type_ref           = "";
                if($input["status"] == "ApprovedQuotation"){  $type_ref = 'Approve';
                } else if(($input["status"] == "draft" && $sub_status_ != "quotation")   ){ $type_ref = 'draft';
                } else if($input["status"] == "quotation" || ($input["status"] == "draft" && $sub_status_ == "quotation")){ $type_ref = 'quotation';
                }
                $ref_count           = $this->transactionUtil->setAndGetReferenceCount($type_ref,$business_id,$pat_id);
                $number              = $this->transactionUtil->generateReferenceNumber($type_ref,$ref_count,null,null,$pat_id);
                $input['invoice_no'] = $number;
              
                # Types of service
                if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
                    $input['types_of_service_id'] = $request->input('types_of_service_id');
                    $price_group_id = !empty($request->input('types_of_service_price_group')) ? $request->input('types_of_service_price_group') : $price_group_id;
                    $input['packing_charge'] = !empty($request->input('packing_charge')) ?
                    $this->transactionUtil->num_uf($request->input('packing_charge')) : 0;
                    $input['packing_charge_type'] = $request->input('packing_charge_type');
                    $input['service_custom_field_1'] = !empty($request->input('service_custom_field_1')) ?
                    $request->input('service_custom_field_1') : null;
                    $input['service_custom_field_2'] = !empty($request->input('service_custom_field_2')) ?
                    $request->input('service_custom_field_2') : null;
                    $input['service_custom_field_3'] = !empty($request->input('service_custom_field_3')) ?
                    $request->input('service_custom_field_3') : null;
                    $input['service_custom_field_4'] = !empty($request->input('service_custom_field_4')) ?
                    $request->input('service_custom_field_4') : null;
                }
                
                $input['selling_price_group_id'] = $price_group_id;
                if ($this->transactionUtil->isModuleEnabled('tables')) {
                    $input['res_table_id'] = request()->get('res_table_id');
                }
                if ($this->transactionUtil->isModuleEnabled('service_staff')) {
                    $input['res_waiter_id'] = request()->get('res_waiter_id');
                }
                
                $document_sell = [];
                if ($request->hasFile('document_sell')) { $id_sf = 1;
                    foreach ($request->file('document_sell') as $file) {
                        $file_name =  'public/uploads/documents/'.time().'_'.$id_sf++.'.'.$file->getClientOriginalExtension();
                        $file->move('public/uploads/documents',$file_name);
                        array_push($document_sell,$file_name);
                    }
                } 
                
                $input['document']               = json_encode($document_sell);
                $input['agent_id']               = $request->input('agent_id');
                $input['cost_center_id']         = $request->input('cost_center_id');
                $input['tax_amount']             = $request->input('tax_calculation_amount');
                $input['pattern_id']             = $request->pattern_id;
                $input['currency_id']            = $request->currency_id;
                $input['exchange_price']         = ($request->currency_id != "" && $request->currency_id != null)?$request->currency_id_amount:null;
                $input['amount_in_currency']     = ($request->currency_id != "" && $request->currency_id != null)?$request->final_total / $request->currency_id_amount:null;
                $input['payment_status']         = 2;
                $input['ecommerce']              = 0;
               
                
                if ($request->status == 'final' ) {
                   $input['sub_status'] = "final";
                }
                if ($request->discount_type == null ) {
                   $input['discount_amount'] = 0;
                }

                $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id);
                $archive     =  \App\Models\ArchiveTransaction::save_parent($transaction,"create");
                if ($transaction->status == 'final' || $transaction->status == 'delivered') {
                    \App\AccountTransaction::add_sell_pos($transaction,$request->pattern_id);
                    \App\Models\StatusLive::insert_data_s($business_id,$transaction,"Sales Invoice");
                }
                # Upload Shipping documents
                Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');
                
                $arr           = [];
                $product_req   = $request->products;
                foreach($product_req as $key_index => $pro_variation){
                    $product   = Product::find($pro_variation['product_id']);
                    if (isset($request->products_variation[($key_index-1)])) {
                        $pro_variation['variation_id']   =  $request->products_variation[($key_index-1)];
                    }
                    $arr[$key_index] = $pro_variation;
                }

                $list_of_product = [];
                $final_status    = $input['status'] ;
                if( ( $final_status == 'draft' && $input['is_quotation'] == 1 && $sub_status_ == 'quotation'  )  || 
                    ( $final_status == 'draft' && $input['is_quotation'] == 0  ) ||
                    ( $final_status = 'ApprovedQuotation' && $sub_status_ = 'proforma' ) ){
                    $list_of_product = $arr;
                }else {
                    $list_of_product = $input['products'];
                }

                $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id'],false,null,[],true,null,$archive,$request);
                           
                if (!$is_direct_sale) {
                    //Add change return
                    $change_return              = $this->dummyPaymentLine;
                    $change_return['amount']    = $input['change_return'];
                    $change_return['is_return'] = 1;
                    $input['payment'][]         = $change_return;
                }
                
                $is_credit_sale = isset($input['is_credit_sale']) && $input['is_credit_sale'] == 1 ? true : false;
                /* $input['payment']=!empty($input['payment'])?$input['payment']:'0.0';*/
                if (!$transaction->is_suspend && !empty($input['payment']) && !$is_credit_sale) {
                    if ($input['payment'][0]['amount'] > 0) {
                        $pays =  $input['payment'];
                        $pay_account =  \App\Contact::add_account($transaction->contact_id);
                        // $pays[0]['account_id'] = (isset($pays[0]['account_id']))?$pays[0]['account_id']:$pay_account;  
                        if(isset($pays[0]['account_id'])){
                            $x =  \App\Services\Sells\Delivery::add_payment($transaction ,$pays);
                        }
                    }
                }
                if ($input['status'] == 'final' || $input["status"] == "ApprovedQuotation" || ($input["status"] == "draft" && $sub_status_ == "proforma")) {
                    $sellLine      = \App\TransactionSellLine::where("transaction_id",$transaction->id)->get();
                    $service_lines = [] ;
                    foreach($sellLine as $it){
                        if($it->product->enable_stock == 0){   
                            $service_lines[] = $it;                             
                        }
                    }
                    if(count($service_lines)>0){
                            $type                         = 'trans_delivery';
                            $ref_count                    =  $this->productUtil->setAndGetReferenceCount($type);
                            $receipt_no                   =  $this->productUtil->generateReferenceNumber($type, $ref_count);
                            $tr_received                  =  new TransactionDelivery;
                            $tr_received->store_id        =  $transaction->store;
                            $tr_received->transaction_id  =  $transaction->id;
                            $tr_received->business_id     =  $transaction->business_id ;
                            $tr_received->reciept_no      =  $receipt_no ;
                            $tr_received->invoice_no      =  $transaction->invoice_no;
                            //$tr_received->ref_no        =  $data->ref_no;
                            $tr_received->date            =  $transaction->transaction_date;
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
                            
                            $prev->save();
                        
                            WarehouseInfo::update_stoct($it->product->id,$it->store_id,$it->quantity*-1,$it->transaction->business_id);
                            MovementWarehouse::movemnet_warehouse_sell($transaction,$it->product,$it->quantity,$it->store_id,$it,$prev->id);
                        }
                        \App\Models\ItemMove::create_sell_itemMove($transaction,null,"service");
                    }
                }
                # Check for final and do some processing.Delivered
                if ($input['status'] == 'final') {
                    # Add payments to Cash Register
                        // if (!$is_direct_sale && !$transaction->is_suspend && !empty($input['payment']) && !$is_credit_sale) {
                        //     $this->cashRegisterUtil->addSellPayments($transaction, $input['payment'],'sale') ;
                        // }
                    # Update payment status
                        // $payment_status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
                        // $transaction->payment_status = $payment_status;
                    
                    if ($request->session()->get('business.enable_rp') == 1) {
                        $redeemed = !empty($input['rp_redeemed']) ? $input['rp_redeemed'] : 0;
                        $this->transactionUtil->updateCustomerRewardPoints($contact_id, $transaction->rp_earned, 0, $redeemed);
                    }
                    
                    # Allocate the quantity from purchase and add mapping of
                    # purchase & sell lines in
                    # transaction_sell_lines_purchase_lines table
                    $business_details = $this->businessUtil->getDetails($business_id);
                    $pos_settings     = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
                    
                    $business = [
                        'id'                => $business_id,
                        'accounting_method' => $request->session()->get('business.accounting_method'),
                        'location_id'       => $input['location_id'],
                        'pos_settings'      => $pos_settings
                    ];
                    
                    # map between purchase and sell
                    // if($input['status'] != 'ApprovedQuotation' || $input['status'] != 'proforma' || $input['status'] != 'quotation'  || $input['status'] != 'draft'){
                        // $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');
                    // }

                    # Auto send notification
                    $whatsapp_link = $this->notificationUtil->autoSendNotification($business_id,'new_sale',$transaction,$transaction->contact);
                }
            
                # Set Module fields
                if (!empty($input['has_module_data'])) {
                    $this->moduleUtil->getModuleData('after_sale_saved', ['transaction' => $transaction, 'input' => $input]);
                }
                
                Media::uploadMedia($business_id, $transaction, $request, 'documents');
                $this->transactionUtil->activityLog($transaction, 'added');
                
                DB::commit();
                
                if ($request->input('is_save_and_print') == 1) {
                    # show_quote   show_invoice
                    $url = $this->transactionUtil->getInvoiceUrl($transaction->id, $business_id);
                    return redirect()->to($url . '?print_on_load=true');
                }
                $receipt       = '';
                $print_invoice = false;
                $msg           = trans("sale.pos_sale_added");
                if ( $request->pattern_id != null) {
                    $pat               = \App\Models\Pattern::find($request->pattern_id);
                    $invoice_layout_id = $pat->invoice_layout;
                }
                if (!$is_direct_sale) {
                    if ($input['status'] == 'draft') {
                        $msg = trans("sale.draft_added");
                        if ($input['is_quotation'] == 1) {
                            $msg           = trans("lang_v1.quotation_added");
                            $print_invoice = true;
                        }
                    } elseif ($input['status'] == 'final' || $input['status'] == 'Delivered' ) {
                        $print_invoice = true;
                    }
                }
                if ($transaction->is_suspend == 1 && empty($pos_settings['print_on_suspend'])) {
                    $print_invoice = false;
                }
                if (!auth()->user()->can("sales.print_invoice")) {
                    $print_invoice = false;
                }
                if ($print_invoice) {
                    $receipt = $this->receiptContent($business_id, $input['location_id'], $transaction->id, null, false, true, $invoice_layout_id);
                }
                $output = ['success' => 1, 'msg' => $msg, 'receipt' => $receipt];
                if (!empty($whatsapp_link)) { $output['whatsapp_link'] = $whatsapp_link; }
            } else {
                $output = [
                    'success' => 0,
                    'msg'     => trans("messages.something_went_wrong")
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            // if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) { $msg = $e->getMessage(); }
            // if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) { $msg = $e->getMessage();  }
            $msg    = trans("messages.something_went_wrong");
            $output = [
                'success' => 0,
                'msg'     => $msg
            ];
        }

        $url = "/sells";            
        if (!$is_direct_sale) {
            return $output;
        } else {
            if ($input['status'] == 'quotation') { $url = '/sells/quotations'; 
            }  else if ($input['status'] == 'ApprovedQuotation') { $url = '/sells/QuatationApproved';
            }  else if ($input['status'] == 'final' || $final_status == 'Delivered') { $url = '/sells';
            }  else if ($input['status'] == 'draft') {
                if (isset($input['is_quotation']) && $input['is_quotation'] == 1) { $url = '/sells/quotations';
                } else { $url = '/sells/drafts'; }
            }  else {
                if (!empty($input['sub_type']) && $input['sub_type'] == 'repair') {
                    $redirect_url = $input['print_label'] == 1 ? action('\Modules\Repair\Http\Controllers\RepairController@printLabel', [$transaction->id]) : action('\Modules\Repair\Http\Controllers\RepairController@index');
                    $url          = $redirect_url;
                }
                
            }
        }
        return redirect($url)->with('status', $output);
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
        $printer_type = null,
        $is_package_slip = false,
        $from_pos_screen = true,
        $invoice_layout_id = null
        ) {
            
            
            $transaction = Transaction::find($transaction_id);
            $transaction_ticket = $transaction->walk_in_ticket_id;
            
            $output = [
                'is_enabled' => false,
                'print_type' => 'browser',
                'html_content' => null,
                'printer_config' => [],
                'data' => []
            ];
            

            $business_details = $this->businessUtil->getDetails($business_id);
            $location_details = BusinessLocation::find($location_id);
            
            if ($from_pos_screen && $location_details->print_receipt_on_invoice != 1) {
                return $output;
            }
        //Check if printing of invoice is enabled or not.
        //If enabled, get print type.
        $output['is_enabled'] = true;

        $invoice_layout_id = !empty($invoice_layout_id) ? $invoice_layout_id : $location_details->invoice_layout_id;
        $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $invoice_layout_id);
        //Check if printer setting is provided.
        $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

        $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);
        
        $currency_details = [
            'symbol' => $business_details->currency_symbol,
            'thousand_separator' => $business_details->thousand_separator,
            'decimal_separator' => $business_details->decimal_separator,
        ];
        $receipt_details->currency = $currency_details;

        $TransactionUtil = new TransactionUtil();

        $total_in_words = $TransactionUtil->numToWord($transaction->final_total);



        if ($is_package_slip) {
            $output['html_content'] = view('sale_pos.receipts.packing_slip', compact('receipt_details', 'transaction_ticket', 'transaction', 'total_in_words'))->render();
            return $output;
        }


        //If print type browser - return the content, printer - return printer config data, and invoice format config
        //browser
        if ($receipt_printer_type == 'printer') {
            $output['print_type'] = 'printer';
            $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
            $output['data'] = $receipt_details;
        } else {
    
    
            $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';
            $output['html_content'] = view($layout, compact('receipt_details', 'transaction_ticket', 'transaction', 'total_in_words'))->render();

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         
        $business_id = request()->session()->get('user.business_id');

        if ( (!(auth()->user()->can('superadmin') || auth()->user()->can('sell.update') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair.update'))))) {
            abort(403, 'Unauthorized action.');
        }

        //Check if the transaction can be edited or not.
        // $edit_days = request()->session()->get('business.transaction_edit_days');
        // if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
        //     return back()
        //         ->with('status', [
        //             'success' => 0,
        //             'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])
        //         ]);
        // }

        //Check if there is a open register, if no then redirect to Create Register screen.
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
            return redirect()->action('CashRegisterController@create');
        }

        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', [
                'success' => 0,
                'msg' => __('lang_v1.return_exist')
            ]);
        }

        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);

        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $transaction = Transaction::where('business_id', $business_id)
            ->where('type', 'sale')
            ->with(['price_group', 'types_of_service'])
            ->findorfail($id);
            $warehouse = $transaction->store;

        $location_id = $transaction->location_id;
        $business_location = BusinessLocation::find($location_id);
        $payment_types = $this->productUtil->payment_types($business_location, true);
        $location_printer_type = $business_location->receipt_printer_type;
        $sell_details = TransactionSellLine::join(
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
            ->leftjoin('variation_location_details AS vld', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'vld.variation_id')
                    ->where('vld.location_id', '=', $location_id);
            })
            ->leftjoin('units', 'units.id', '=', 'p.unit_id')
            ->where('transaction_sell_lines.transaction_id', $id)
            ->with(['warranties'])
            ->select(
                DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                'p.id as product_id',
                'p.enable_stock',
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
                'transaction_sell_lines.unit_price_before_discount as unit_price_before_discount',
                'transaction_sell_lines.unit_price_inc_tax as sell_price_inc_tax',
                'transaction_sell_lines.id as transaction_sell_lines_id',
                'transaction_sell_lines.id',
                'transaction_sell_lines.se_note',
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

                    //Add available lot numbers for dropdown to sell lines
                    $lot_numbers = [];
                    if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                        $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($value->variation_id, $business_id, $location_id);
                        foreach ($lot_number_obj as $lot_number) {
                            //If lot number is selected added ordered quantity to lot quantity available
                            if ($value->lot_no_line_id == $lot_number->purchase_line_id) {
                                $lot_number->qty_available += $value->quantity_ordered;
                            }

                            $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                            $lot_numbers[] = $lot_number;
                        }
                    }
                    $sell_details[$key]->lot_numbers = $lot_numbers;

                    if (!empty($value->sub_unit_id)) {
                        $value = $this->productUtil->changeSellLineUnit($business_id, $value);
                        $sell_details[$key] = $value;
                    }

                    $sell_details[$key]->formatted_qty_available = $this->productUtil->num_f($value->qty_available, false, null, true);

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

        $featured_products = $business_location->getFeaturedProducts();

        $payment_lines = $this->transactionUtil->getPaymentDetails($id);
        //If no payment lines found then add dummy payment line.
        if (empty($payment_lines)) {
            $payment_lines[] = $this->dummyPaymentLine;
        }

        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id, false);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id, false);
        }

        //If brands, category are enabled then send else false.
        $categories = (request()->session()->get('business.enable_category') == 1) ? Category::catAndSubCategories($business_id) : false;
        $brands = (request()->session()->get('business.enable_brand') == 1) ? Brands::forDropdown($business_id)
            ->prepend(__('lang_v1.all_brands'), 'all') : false;

        $change_return = $this->dummyPaymentLine;

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

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false, true);
        }

        $waiters = [];
        if ($this->productUtil->isModuleEnabled('service_staff') && !empty($pos_settings['inline_service_staff'])) {
            $waiters_enabled = true;
            $waiters = $this->productUtil->serviceStaffDropdown($business_id);
        }
        $redeem_details = [];
        if (request()->session()->get('business.enable_rp') == 1) {
            $redeem_details = $this->transactionUtil->getRewardRedeemDetails($business_id, $transaction->contact_id);

            $redeem_details['points'] += $transaction->rp_redeemed;
            $redeem_details['points'] -= $transaction->rp_earned;
        }

        $edit_discount = auth()->user()->can('edit_product_discount_from_pos_screen');
        $edit_price = auth()->user()->can('edit_product_price_from_pos_screen');
        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $warranties = $this->__getwarranties();
        $sub_type = request()->get('sub_type');

        //pos screen view from module
        $pos_module_data = $this->moduleUtil->getModuleData('get_pos_screen_view', ['sub_type' => $sub_type]);

        $invoice_schemes = [];
        $default_invoice_schemes = null;

        if ($transaction->status == 'draft') {
            $invoice_schemes = InvoiceScheme::forDropdown($business_id);
            $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
        }

        $invoice_layouts = InvoiceLayout::forDropdown($business_id);

        return view('sale_pos.edit')
            ->with(compact('business_details', 'taxes', 'warehouse' , 'payment_types', 'walk_in_customer', 'sell_details', 'transaction', 'payment_lines', 'location_printer_type', 'shortcuts', 'commission_agent', 'categories', 'pos_settings', 'change_return', 'types', 'customer_groups', 'brands', 'accounts', 'waiters', 'redeem_details', 'edit_price', 'edit_discount', 'shipping_statuses', 'warranties', 'sub_type', 'pos_module_data', 'invoice_schemes', 'default_invoice_schemes', 'invoice_layouts', 'featured_products'));
    }

    /**
     * Update the specified resource in storage.
     * TODO: Add edit log.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         if (!auth()->user()->can('sell.update') && !auth()->user()->can('direct_sell.access') && !auth()->user()->can('warehouse.views')  ) {
            abort(403, 'Unauthorized action.');
        }
        
        
        try {
            $input = $request->except('_token');
            // dd($request);    
            $input['is_quotation'] = 0;
            //status is send as quotation from edit sales screen.
            $sub_status_ = "";
            if ($input['status'] == 'quotation') {
                if($input['sub_status'] == "proforma"){
                    $input['status'] = 'draft';
                    $input['is_quotation'] = 1;
                    $input['sub_status'] = 'proforma';
                    $sub_status_ = 'proforma';
                }else{
                    $input['status'] = 'draft';
                    $input['is_quotation'] = 1;
                    $input['sub_status'] = 'quotation';
                    $sub_status_ = 'quotation';
                }
            } else if ($input['status'] == 'proforma') {
                
                $input['status'] = 'draft';
                $input['sub_status'] = 'proforma';
                $sub_status_ = 'proforma';
                $input['is_quotation'] = 0;
            } else if ($input['status'] == 'final' && $input['sub_status']   = "final") {
                
                $input['sub_status']   = "final";
                $sub_status_   = "final";
                $input['is_quotation'] = 0;
            } else if ($input['status'] == 'delivered' && $input['sub_status'] == 'proforma') {
                
                $input['sub_status'] = "proforma";
                $sub_status_ = "proforma";
                $input['is_quotation'] = 0;
                
            } else if ($input['status'] == 'delivered' && $input['sub_status'] == 'final') {
                
                $input['sub_status'] = "final";
                $sub_status_ = "final";
                $input['is_quotation'] = 0;
                
            } else if ($input['status'] == 'delivered' && $input['sub_status'] == 'f' || $input['status'] == 'final' && $input['sub_status'] == 'f'  ) {
                
                $input['sub_status'] = "f";
                $sub_status_ = "f";
                $input['is_quotation'] = 0;
            } else {
                
                $input['sub_status'] = null;
                $sub_status_ = null;
                $input['is_quotation'] = 0;
            }
            
            $is_direct_sale = false;
            if (!empty($input['products'])) {
                //Get transaction value before updating.
                $transaction_before = Transaction::find($id);
                $archive            = \App\Models\ArchiveTransaction::save_parent($transaction_before,"edit");
                $sell_lines         = \App\TransactionSellLine::where("transaction_id",$transaction_before->id)->get();
                foreach($sell_lines as $it){
                    \App\Models\ArchiveTransactionSellLine::save_sells_line( $archive , $it);
                }
                $old_status    = $transaction_before->status;
                $old_trans     = $transaction_before->cost_center_id;
                $old_pattern_id= $transaction_before->pattern_id;
                $old_account   = $transaction_before->contact_id;
                $old_discount  = $transaction_before->discount_amount;
                $old_tax       = $transaction_before->tax_amount;
                $old_document  = $transaction_before->document;
                
                // dd($request);

                $status_before      = $transaction_before->status;
                $rp_earned_before   = $transaction_before->rp_earned;
                $rp_redeemed_before = $transaction_before->rp_redeemed;
                
                if ($transaction_before->is_direct_sale == 1) {
                    $is_direct_sale = true;
                }
                
                //Check Customer credit limit
                $is_credit_limit_exeeded = $this->transactionUtil->isCustomerCreditLimitExeeded($input, $id);
                
                if ($is_credit_limit_exeeded !== false) {
                    $credit_limit_amount = $this->transactionUtil->num_f($is_credit_limit_exeeded, true);
                    $output = [
                        'success' => 0,
                        'msg' => __('lang_v1.cutomer_credit_limit_exeeded', ['credit_limit' => $credit_limit_amount])
                    ];
                    if (!$is_direct_sale) {
                        return $output;
                    } else {
                        return redirect()
                        ->action('SellController@index')
                        ->with('status', $output);
                    }
                }
                
                //Check if there is a open register, if no then redirect to Create Register screen.
                if (!$is_direct_sale && $this->cashRegisterUtil->countOpenedRegister() == 0) {
                    return redirect()->action('CashRegisterController@create');
                }
            
                $business_id         = $request->session()->get('user.business_id');
                $user_id             = $request->session()->get('user.id');
                $commsn_agnt_setting = $request->session()->get('business.sales_cmsn_agnt');
                
                $discount = [
                    'discount_type'   => (isset($input['discount_type']))?$input['discount_type']:null,
                    'discount_amount' => (isset($input['discount_type']))? (($input['discount_type'] != null)?$input['discount_amount']:0):0
                ];
                $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_rate_id'], $discount);
                // return dd($invoice_total);
                if (!empty($request->input('transaction_date'))) {
                    $input['transaction_date'] = $this->productUtil->uf_date($request->input('transaction_date'), true);
                }
                
                
                if (!empty($request->input('project_no'))) {
                    $input['project_no'] =  $request->input('project_no') ;
                }
                $input['commission_agent'] = !empty($request->input('commission_agent')) ? $request->input('commission_agent') : null;
                if ($commsn_agnt_setting == 'logged_in_user') {
                    $input['commission_agent'] = $user_id;
                }
                
                if (isset($input['exchange_rate']) && $this->transactionUtil->num_uf($input['exchange_rate']) == 0) {
                    $input['exchange_rate'] = 1;
                }
                 //Customer group details
                $contact_id                 = $request->get('contact_id', null);
                $cg                         = $this->contactUtil->getCustomerGroup($business_id, $contact_id);
                $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;
                
                //set selling price group id
                $price_group_id = $request->has('price_group') ? $request->input('price_group') : null;
                
                $input['is_suspend'] = isset($input['is_suspend']) && 1 == $input['is_suspend']  ? 1 : 0;
                // if ($input['is_suspend']) {
                //     $input['sale_note'] = !empty($input['additional_notes']) ? $input['additional_notes'] : null;
                // }
                if($request->sale_note != null){
                    $it_terms = \App\Models\QuatationTerm::where("business_id",$business_id)->where("id",$request->sale_note)->first(); 
                    if ($input['is_suspend']) {
                         if (!empty($it_terms)) {
                            $input['sale_note'] = $it_terms->description;
                         }   
                    }   
                }
                
                if ($status_before == 'draft' && $request->pattern_id !=null ) {
                    if (  $request->pattern_id !=null ) {
                         $it = \App\Models\Pattern::find($request->pattern_id);
                         if(!empty($it)){
                            $input['invoice_scheme_id'] = $it->invoice_scheme;
                         }
                    }
                }
                
                //Types of service
                if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
                    $input['types_of_service_id'] = $request->input('types_of_service_id');
                    $price_group_id = !empty($request->input('types_of_service_price_group')) ? $request->input('types_of_service_price_group') : $price_group_id;
                    $input['packing_charge'] = !empty($request->input('packing_charge')) ?
                    $this->transactionUtil->num_uf($request->input('packing_charge')) : 0;
                    $input['packing_charge_type'] = $request->input('packing_charge_type');
                    $input['service_custom_field_1'] = !empty($request->input('service_custom_field_1')) ?
                    $request->input('service_custom_field_1') : null;
                    $input['service_custom_field_2'] = !empty($request->input('service_custom_field_2')) ?
                    $request->input('service_custom_field_2') : null;
                    $input['service_custom_field_3'] = !empty($request->input('service_custom_field_3')) ?
                    $request->input('service_custom_field_3') : null;
                    $input['service_custom_field_4'] = !empty($request->input('service_custom_field_4')) ?
                    $request->input('service_custom_field_4') : null;
                }
                
                $input['selling_price_group_id'] = $price_group_id;
                
                // if ($this->transactionUtil->isModuleEnabled('tables')) {
                    //     $input['res_table_id'] = request()->get('res_table_id');
                    // }
                    // if ($this->transactionUtil->isModuleEnabled('service_staff')) {
                        //     $input['res_waiter_id'] = request()->get('res_waiter_id');
                        // }
                        
                  
                if ($request->hasFile('document_sell')) {
                    $id_ss = 1;
                    foreach ($request->file('document_sell') as $file) {
                        $file_name =  'public/uploads/documents/'.time().'_'.$id_ss++.'.'.$file->getClientOriginalExtension();
                        $file->move('public/uploads/documents',$file_name);
                        array_push($old_document,$file_name);
                    }
                } 

                
                if(json_encode($old_document)!="[]"){
                    $input['document'] = json_encode($old_document);
                }
                

                //Begin transaction
                DB::beginTransaction();
                $input['agent_id']               = $request->input('agent_id');
                $input['cost_center_id']         = $request->input('cost_center_id');
                $input['tax_amount']             = $request->input('tax_calculation_amount');
                $input['pattern_id']             = $request->pattern_id;
               
                if( $request->discount_type == null ){
                        $input["discount_amount"] = 0;
                }
                  
                $transaction   = $this->transactionUtil->updateSellTransaction($id, $business_id, $input, $invoice_total, $user_id);
                //Update Sell lines
                $deleted_lines = $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id'], true, $status_before,[],true,$input['tax_rate_id'],null,$request);
           
              
              

                //Update update lines
                $is_credit_sale = isset($input['is_credit_sale']) && $input['is_credit_sale'] == 1 ? true : false;

                if (!$is_direct_sale && !$transaction->is_suspend && !$is_credit_sale) {
                    //Add change return
                    $change_return = $this->dummyPaymentLine;
                    $change_return['amount'] = $input['change_return'];
                    $change_return['is_return'] = 1;
                    if (!empty($input['change_return_id'])) {
                        $change_return['id'] = $input['change_return_id'];
                    }
                    $input['payment'][] = $change_return;

                    $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment']);

                    //Update cash register
                    $this->cashRegisterUtil->updateSellPayments($status_before, $transaction, $input['payment']);
                }

                if ($request->session()->get('business.enable_rp') == 1) {
                    $this->transactionUtil->updateCustomerRewardPoints($contact_id, $transaction->rp_earned, $rp_earned_before, $transaction->rp_redeemed, $rp_redeemed_before);
                }

                Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');
                //Update payment status
                // $payment_status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
                // $transaction->payment_status = $payment_status;
              

                //Update product stock
                $this->productUtil->adjustProductStockForInvoice($status_before, $transaction, $input);
                
                
                // return  dd($transaction);
                // dd("stop");
                
                //Allocate the quantity from purchase and add mapping of
                //purchase & sell lines in
                //transaction_sell_lines_purchase_lines table
                $business_details = $this->businessUtil->getDetails($business_id);
                $pos_settings     = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
                
                $business = [
                    'id'                => $business_id,
                    'accounting_method' => $request->session()->get('business.accounting_method'),
                    'location_id'       => $input['location_id'],
                    'pos_settings'      => $pos_settings
                ];
                // $this->transactionUtil->adjustMappingPurchaseSell($status_before, $transaction, $business, $deleted_lines);
                
                $log_properties   = [];
                if (isset($input['repair_completed_on'])) {
                    $completed_on = !empty($input['repair_completed_on']) ? $this->transactionUtil->uf_date($input['repair_completed_on'], true) : null;
                    if ($transaction->repair_completed_on != $completed_on) {
                        $log_properties['completed_on_from'] = $transaction->repair_completed_on;
                        $log_properties['completed_on_to'] = $completed_on;
                    }
                }
                
                //Set Module fields
                if (!empty($input['has_module_data'])) {
                    $this->moduleUtil->getModuleData('after_sale_saved', ['transaction' => $transaction, 'input' => $input]);
                }

                Media::uploadMedia($business_id, $transaction, $request, 'documents');

                $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);
                //  
                if ($request->status == 'final' || $request->status == 'delivered') { 
                    if($transaction->sub_status != "proforma" && $transaction->sub_status != "ApprovedQuotation"){
                        \App\AccountTransaction::update_sell_pos_($transaction,null,$old_trans,$old_account,$old_discount,$old_tax,$request->pattern_id,$old_pattern_id);
                        \App\Models\StatusLive::update_data_s($business_id,$transaction,"Sales Invoice");
                    }
                }
               
                
                if ($request->input('is_save_and_print') == 1) {
                    $url = $this->transactionUtil->getInvoiceUrl($id, $business_id);
                    return redirect()->to($url . '?print_on_load=true');
                }

                $msg = '';
                $receipt = '';
                $can_print_invoice = auth()->user()->can("print_invoice");
                $invoice_layout_id = $request->input('invoice_layout_id');
                
                if ($input['status'] == 'draft' && $input['is_quotation'] == 0) {
                    $msg = trans("sale.draft_added");
                } elseif ($input['status'] == 'draft' && $input['is_quotation'] == 1) {
                    $msg = trans("lang_v1.quotation_updated");
                    if (!$is_direct_sale && $can_print_invoice) {
                        $receipt = $this->receiptContent($business_id, $input['location_id'], $transaction->id, null, false, true, $invoice_layout_id);
                    } else {
                        $receipt = '';
                    }
                } elseif ($input['status'] == 'final') {
                    $msg = trans("sale.pos_sale_updated");
                    if (!$is_direct_sale && $can_print_invoice) {
                        $receipt = $this->receiptContent($business_id, $input['location_id'], $transaction->id, null, false, true, $invoice_layout_id);
                    } else {
                        $receipt = '';
                    }
                }

                //.........................\\ 
                //...service item section 
                //.........................\\
              if ($input['status'] == 'final' || $input["status"] == "ApprovedQuotation" || ($input["status"] == "draft" && $sub_status_ == "proforma")) {
                $sellLine = \App\TransactionSellLine::where("transaction_id",$transaction->id)->get();
                $service_lines = [] ;
                foreach($sellLine as $it){
                    if($it->product->enable_stock == 0){   
                        $service_lines[]=$it;                             
                    }
                }
                
                if(count($service_lines)>0){
                    $tr_delivered = \App\Models\TransactionDelivery::where("transaction_id",$transaction->id)->where("status",'Service Item')->first();
                    $id_td = ($tr_delivered)?$tr_delivered->id:null;
                    if($id_td == null){
                        $type  = 'trans_delivery';
                        $ref_count                    = $this->productUtil->setAndGetReferenceCount($type);
                        $reciept_no                   = $this->productUtil->generateReferenceNumber($type, $ref_count);
                        $tr_recieved                  =  new TransactionDelivery;
                        $tr_recieved->store_id        =  $transaction->store;
                        $tr_recieved->transaction_id  =  $transaction->id;
                        $tr_recieved->business_id     =  $transaction->business_id ;
                        $tr_recieved->reciept_no      =  $reciept_no ;
                        $tr_recieved->invoice_no      =  $transaction->invoice_no;
                        //$tr_recieved->ref_no        =  $data->ref_no;
                        $tr_recieved->status          = 'Service Item';
                        $tr_recieved->save();
                        $id_td = $tr_recieved->id;
                    }
                    foreach($service_lines as $it){
                        
                        $previous = \App\Models\DeliveredPrevious::where("transaction_recieveds_id",$id_td)->where("line_id",$it->id)->first();
                        if(!empty($previous)){
                            $margin = $it->quantity -  $previous->current_qty;
                            $previous->store_id        =  $it->store_id;
                            $previous->total_qty       =  $it->quantity;
                            $previous->current_qty     =  $it->quantity;
                            if($margin == 0){
                                    $qty_margin = 0;
                            }elseif($margin < 0){
                                $qty_margin = $margin*-1;
                            }else{
                                $qty_margin = $margin;
                            }
                            $previous->update();
                            WarehouseInfo::update_stoct($it->product->id,$it->store_id,$qty_margin,$it->transaction->business_id);
                            MovementWarehouse::movemnet_warehouse($transaction,$it->product,$it->quantity,$it->store_id,$it,"minus",$id_td);
                        }else{

                            $prev                  =  new DeliveredPrevious;
                          
                            
                            $prev->product_id      =  $it->product_id;
                            $prev->store_id        =  $it->store_id;
                            $prev->business_id     =  $it->transaction->business_id ;
                            $prev->transaction_id  =  $it->transaction->id;
                            $prev->unit_id         =  $it->product->unit->id;
                            $prev->total_qty       =  $it->quantity;
                            $prev->current_qty     =  $it->quantity;
                            $prev->remain_qty      =  0;
                            $prev->transaction_recieveds_id   = $id_td;
                            $prev->product_name   =  $it->product->name;
                            $prev->line_id        =  $it->id;
                            
                            $prev->save();
                            WarehouseInfo::update_stoct($it->product->id,$it->store_id,$it->quantity*-1,$it->transaction->business_id);
                            MovementWarehouse::movemnet_warehouse_sell($transaction,$it->product,$it->quantity,$it->store_id,$it,$prev->id);
                        }
                       
                    }
                    \App\Models\ItemMove::create_sell_itemMove($transaction,null,"service");
                    
                }else{
                   
                    $tr_delivered = \App\Models\TransactionDelivery::where("transaction_id",$transaction->id)->where("status",'Service Item')->first();
                    if(!empty($tr_delivered)){
                        //.1//.. select all  child
                        $receive = \App\Models\DeliveredPrevious::where("transaction_recieveds_id",$tr_delivered->id)->where("transaction_id",$transaction->id)->get();
                        $move_id        = []; 
                        foreach($receive as $it){
                                $movewarehouse = \App\MovementWarehouse::where("delivered_previouse_id",$it->id)->first();
                                if(!empty($movewarehouse)){
                                    WarehouseInfo::update_stoct($it->product_id,$it->store_id,$it->current_qty,$transaction->business_id);
                                    $movewarehouse->delete();
                                }   
                                $itemMove = \App\Models\ItemMove::where("transaction_id",$transaction->id)->where("recieve_id",$it->id)->first();
                                if(!empty($itemMove)){
                                    $move_id[] = $itemMove->id; 
                                }
                                if(!empty($itemMove)){
                                    $itemMove->delete();
                                    \App\Models\ItemMove::refresh_item($itemMove->id,$it->product_id);
                                    $move_all  = \App\Models\ItemMove::where("product_id",$it->product_id)
                                                            ->whereNotIn("id",$move_id)
                                                            ->get(); 
                                    if(count($move_all)>0){
                                        foreach($move_all as $key =>  $it){
                                            \App\Models\ItemMove::refresh_item($it->id,$it->product_id );
                                        }
                                    }
                                }   
                                $it->delete();
                        }
                        $tr_delivered->delete(); 
                    }
                }
              }
                $output = ['success' => 1, 'msg' => $msg, 'receipt' => $receipt];
            } else {
                $output = [
                    'success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage()
            ];
        }
        if (!$is_direct_sale) {
            return $output;
        } else {
            if ($input['status'] == 'draft') {
                if($input['sub_status'] == 'proforma'){
                    return redirect()
                        ->action('SellController@getApproved')
                        ->with('status', $output);
                }else if (isset($input['is_quotation']) && $input['is_quotation'] == 1) {
                    return redirect()
                        ->action('SellController@getQuotations')
                        ->with('status', $output);
                } else {
                    return redirect()
                        ->action('SellController@getDrafts')
                        ->with('status', $output);
                }
            } else {
                if (!empty($transaction->sub_type) && $transaction->sub_type == 'repair') {
                    return redirect()
                        ->action('\Modules\Repair\Http\Controllers\RepairController@index')
                        ->with('status', $output);
                }

                return redirect()
                    ->action('SellController@index')
                    ->with('status', $output);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         
        if (!auth()->user()->can('sell.delete') && !auth()->user()->can('direct_sell.delete')) {
            $output['success'] = false;
            $output['msg']     = trans("messages.Unauthorized_action");
            return $output;
        }
        
        if (request()->ajax()) {
             try {
                    if (Module::has('Installment')) {
                        $installment = DB::table('installments')->where('transaction_id', $id)->count();
                        if ($installment > 0) {
                            $output['success'] = false;
                            $output['msg'] = trans("messages.tarnsaction_has_installments");
                            return $output;
                        }
                    }
                    $payment     =  \App\TransactionPayment::where('transaction_id',$id)->get();
                    if (count($payment)>0) {
                        $output['success'] = false;
                        $output['msg']     = trans("lang_v1.sorry_there_is_payment");
                        return $output;
                        
                    }
                    $business_id = request()->session()->get('user.business_id');
                    # Begin transaction
                    DB::beginTransaction();
                        $transaction           =  \App\Transaction::find($id);
                        $childS                =  \App\Transaction::where("separate_parent",$transaction->id)->get();
                        if(count($childS)>0){
                            $output['success'] = false;
                            $output['msg']     = trans("Sorry!! Can't Delete This Approve");
                            return $output;
                        }else{
                            #  Delete sales
                            $previous              =  \App\Models\DeliveredPrevious::where('transaction_id',$transaction->id)->get();
                            $payment               =  \App\TransactionPayment::where('transaction_id',$transaction->id)->get();
                            if(count($previous)>0){
                                    $output['success'] = false;
                                    $output['msg']     = trans("messages.sorry_there_is_delivery");
                                    return $output;
                            }else{
                                if(count($payment)>0){
                                    $output['success'] = false;
                                    $output['msg']     = trans("messages.sorry_there_is_payment");
                                    return $output;
                                }
                                # $transaction->status == "draft" && $transaction->sub_status == null
                                # $transaction->status == "draft" && $transaction->sub_status == "proforma"
                                # $transaction->status == "draft" && $transaction->sub_status == "quotation"
                                # $transaction->status == "ApprovedQuotation" && $transaction->sub_status == "proforma"
                                if (($transaction->status == "final" && $transaction->sub_status == "final" )|| ($transaction->status == "final" && $transaction->sub_status == "f" )) { // final
                                    $payment     =  \App\TransactionPayment::where('transaction_id',$transaction->id)->get();
                                    $previous    =  \App\Models\DeliveredPrevious::where('transaction_id',$transaction->id)->get();
                                    foreach($payment as $it)    { $it->delete(); }
                                    foreach($previous as $prev) { WarehouseInfo::update_stoct($prev->product_id,$prev->store_id,$prev->total_qty,$transaction->business_id); }
                                    $all_account = \App\AccountTransaction::where('transaction_id',$transaction->id)->get();
                                    foreach($all_account as $o){
                                        $account_transaction  = $o->account_id;
                                        $action_date          = $o->operation_date;
                                        $ac                   = \App\Account::find($account_transaction);
                                        $o->delete();
                                        if($ac->cost_center!=1){
                                            \App\AccountTransaction::nextRecords($ac->id,$business_id,$action_date);
                                        }
                                    }
                                    \App\MovementWarehouse::where('transaction_id',$transaction->id)->delete();
                                }
                                $output = $this->transactionUtil->deleteSale($business_id, $id);
                            }
                        }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                    $output['success'] = false;
                    $output['msg']     = trans("messages.something_went_wrong");
                }

                return $output;
            }
            
    }
    
    /**
     * Returns the HTML row for a product in POS
     *
     * @param  int  $variation_id
     * @param  int  $location_id
     * @return \Illuminate\Http\Response
     */
    public function getProductRow($variation_id, $location_id,$store_id,$status,$contact_id=null,$return=null,$sell_id=null)
    {
       
        $output = [];
        /*sells/pos/get_product_row/14/1?
        product_row=8&
        customer_id=1&
        is_direct_sell=true&
        price_group=0&purchase_line_id=&weighing_scale_barcode=&
        quantity=1
        */
        $id_contact = $contact_id;
        try {
            $list_of_prices = [];
            $currency       = request()->get('currency');
            $row_count      = request()->get('product_row');
            $id_product     = request()->get('id_product');
            $row_count      = $row_count + 1;
            $is_direct_sell = false;
            
            /* from sells/create  page */
            if (request()->get('is_direct_sell') == 'true') {
                $is_direct_sell = true;
            }
            
            $business_id      =  request()->session()->get('user.business_id');
            $business_details =  $this->businessUtil->getDetails($business_id);
            $quantity         =  request()->get('quantity', 1);
            $childs           =  Warehouse::childs($business_id);
          
            //Check for weighing scale barcode
            $weighing_barcode = request()->get('weighing_scale_barcode');
            if ($variation_id == 'null' && !empty($weighing_barcode)) {
                $product_details = $this->__parseWeighingBarcode($weighing_barcode);
                if ($product_details['success']) {
                    $variation_id = $product_details['variation_id'];
                    $quantity     = $product_details['qty'];
                } else {
                    $output['success'] = false;
                    $output['msg']     = $product_details['msg'];
                    return $output;
                }
            }
            
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
            
            $check_qty    = !empty($pos_settings['allow_overselling']) ? false : true;
            
            if($status == "quotation" || $status == "draft" || $status == "ApprovedQuotation"  || $status == "proforma" || $status == "delivered"){
                $product  = $this->productUtil->getDetailsFromVariationQ($variation_id, $business_id, $location_id, $check_qty,$id_product);
            }else{
                $product  = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id, $check_qty,$id_product);
            }
            if (!isset($product->quantity_ordered)) {
                $product->quantity_ordered = $quantity;
            }
          
            //............. eb last price_customer
            if(isset($id_contact) && $id_contact != null){
                $contacts_price = TransactionSellLine::orderby("id","desc")->where("product_id",$product->product_id)->whereHas("transaction",function($query) use($id_contact){
                    $query->where("contact_id" , $id_contact);
                    $query->whereIn("status",["final","delivered"]);
                    $query->whereIn("sub_status",["final","f"]);
                })->select()->first();
            }

            $buss = \App\Business::find($business_id);
            if(isset($buss) && $buss->source_sell_price != 0){
                if(isset($contacts_price) && $id_contact != null ){
                    if(auth()->user()->can("all_sales_prices")){
                        if($contacts_price != null){
                            switch($buss->source_sell_price) {
                            case 2  :
                                $price_contact    = $contacts_price->unit_price;
                                break;
                            case 3  :
                                $trasnaction      = \App\Transaction::find($contacts_price->transaction_id);   
                                $subtotal         = $trasnaction->total_before_tax;
                                if ($trasnaction->discount_type == "fixed_before_vat"){
                                    $dis = $trasnaction->discount_amount;
                                }else if ($trasnaction->discount_type == "fixed_after_vat"){
                                    $tax = \App\TaxRate::find($trasnaction->tax_id);
                                    $dis = ($trasnaction->discount_amount*100)/(100+$tax->amount) ;
                                }else if ($trasnaction->discount_type == "percentage"){
                                    $dis = ($trasnaction->total_before_tax *  $trasnaction->discount_amount)/100;
                                }else{
                                    $dis = 0;
                                }
                                $discount         = $dis;
                                $percentage       = ($subtotal != 0)?($discount / $subtotal):0;
                                $row_price        =  ($contacts_price->quantity != 0)?($contacts_price->unit_price / $contacts_price->quantity) :0;
                                $price_contact    = $row_price - ( $row_price * $percentage);
                                break;
                            default :
                                $price_contact    = $contacts_price->unit_price_before_discount;
                                break;
                            }
                            $product->default_purchase_price = $price_contact;      //... before vat & dis

                            if($product->tax!=null){
                                $tax_rates = TaxRate::where('id', $product->tax)
                                ->select(['amount'])->first();
                                
                                $tax_ = $tax_rates->amount ;
                            }else{
                                $tax_ = 0;
                            }
                            $product->dpp_inc_tax            = ($price_contact*$tax_/100) +($price_contact);  //... before vat & dis
                            // $product->default_purchase_price = $contacts_price->line_discount_amount;         //...  discount amount
                            $product->default_sell_price     = $price_contact;                      //... after dis & without vat 
                            $product->sell_price_inc_tax     = ($price_contact*$tax_/100) 
                            +($price_contact);              //... after dis &  vat 
                            
                                                        
                        }
                    }
                    //......... eb
                }
            }
            //......... eb

            // $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available, false, null, true);
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available, false, null, true);

            $pro                    =  \App\Product::find($product->product_id);
            $allUnits               =  [];
            $var                    =  $pro->variations->first();
            $var                    =  ($var)?$var->default_sell_price:0;
            $UU                     = \App\Unit::find($product->unit_id);
            $allUnits[$UU->id] = [
                                        'name'          => $UU->actual_name,
                                        'multiplier'    => $UU->base_unit_multiplier,
                                        'allow_decimal' => $UU->allow_decimal,
                                        'price'         => $var,
                                        'check_price'   => $buss->default_price_unit,
                                ];
            // $sub_units      = $this->productUtil->getSubUnits($business_id, $UU->id, false, $product->product_id);
            // foreach($sub_units as $k => $line){
            //     $allUnits[$k] =  $line; 
            // }
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
            $sub_units = $allUnits  ;

            //Get customer group and change the price accordingly
            $customer_id                 = request()->get('customer_id', null);
            $cg                          = $this->contactUtil->getCustomerGroup($business_id, $customer_id);
            $percent                     = (empty($cg) || empty($cg->amount) || $cg->price_calculation_type != 'percentage') ? 0 : $cg->amount;
            $product->default_sell_price = $product->default_sell_price + ($percent * $product->default_sell_price / 100);
            $product->sell_price_inc_tax = $product->sell_price_inc_tax + ($percent * $product->sell_price_inc_tax / 100);

            $warehouse    = WarehouseInfo::where("product_id",$product->product_id)
                            ->where("store_id",$store_id)
                            ->select(DB::raw("SUM(product_qty) as stock"))->first();
            
            $tax_dropdown    = TaxRate::forBusinessDropdown($business_id, true, true);

            $enabled_modules = $this->transactionUtil->allModulesEnabled();

            //Get lot number dropdown if enabled
            $lot_numbers = [];
            if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($variation_id, $business_id, $location_id, true);
                foreach ($lot_number_obj as $lot_number) {
                    $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                    $lot_numbers[]            = $lot_number;
                }
            }
            $product->lot_numbers = $lot_numbers;
            $purchase_line_id     = request()->get('purchase_line_id');
            $tr_id                = \App\Transaction::find(request()->get('tr_id'));
            $line_main            = \App\TransactionSellLine::where("transaction_id",request()->get('tr_id'))->where("product_id",$product->product_id)->get();
            $price_group          = request()->input('price_group');

            if (!empty($price_group)) {
                $variation_group_prices = $this->productUtil->getVariationGroupPrice($variation_id, $price_group, $product->tax_id);

                if (!empty($variation_group_prices['price_inc_tax'])) {
                    $product->sell_price_inc_tax = $variation_group_prices['price_inc_tax'];
                    $product->default_sell_price = $variation_group_prices['price_exc_tax'];
                }
            }
            $row                    = 1;$line_prices  = [];#2024-8-6
            $list_of_prices         = \App\Product::getListPrices($row);
            $list_of_prices_in_unit = \App\Product::getProductPrices($product->product_id);

            $warranties        = $this->__getwarranties();
            $output['success'] = true;
            $waiters           = [];

            if ($this->productUtil->isModuleEnabled('service_staff') && !empty($pos_settings['inline_service_staff'])) {
                $waiters_enabled = true;
                $waiters = $this->productUtil->serviceStaffDropdown($business_id, $location_id);
            }

            if (request()->get('type') == 'sell-return') {
                $enable_stock = $product->enable_stock;
                            
                $output['enable_stock'] = $enable_stock  ;
                $output['html_content'] =  view('sell_return.partials.product_row')
                    ->with(compact('product', 'row_count'  , "store_id", 'tax_dropdown','childs', 'enabled_modules', 'sub_units'))
                    ->render();
            } else {
                $is_cg    = !empty($cg->id) ? true : false;
                $is_pg    = !empty($price_group) ? true : false;
                $discount = $this->productUtil->getProductDiscount($product, $business_id, $location_id, $is_cg, $is_pg, $variation_id);

                if ($is_direct_sell) {
                    $edit_discount = auth()->user()->can('edit_product_discount_from_sale_screen');
                    $edit_price    = auth()->user()->can('edit_product_price_from_sale_screen');
                } else {
                    $edit_discount = auth()->user()->can('edit_product_discount_from_pos_screen');
                    $edit_price    = auth()->user()->can('edit_product_price_from_pos_screen');
                }
                 
                if(app('request')->input('return') == "return_sale"){
                    $transaction_id = app('request')->input('trans_id');
                    $childs         =  Warehouse::product_stores_return($product->product_id,$transaction_id);
                    foreach($childs as $it){
                        $available_qty  =  ($it["available_qty"])??9999999;
                        break;
                    }
                    $enable_stock           = $product->enable_stock;
                            
                    $output['enable_stock'] = $enable_stock  ;
                    $output['html_content'] =  view('delivery.partials.product_row')
                                                ->with(compact('available_qty','product','currency', "childs", "store_id", 'row_count' , "status" ,'warehouse' ,
                                                    'tax_dropdown', 'enabled_modules', 'pos_settings', 'sub_units', 'discount', 'waiters',
                                                    'edit_discount', 'edit_price', 'purchase_line_id', 'warranties', 'quantity',
                                                    'is_direct_sell'))
                                                ->render();
                }else{
                     
                    switch (app('request')->input('view_type')) {
                        case 'sell_add':
                            $enable_stock           = $product->enable_stock;
                            $currency               = app('request')->input('currency');
                            $output['enable_stock'] = $enable_stock  ;
                            $output['html_content'] = view('sale_pos.view_types.sell_add')
                                                        ->with(compact([
                                                                        'product', 
                                                                        'childs', 
                                                                        'store_id',
                                                                        'currency', 
                                                                        'row_count', 
                                                                        'status',
                                                                        'warehouse',
                                                                        'tax_dropdown', 
                                                                        'enabled_modules', 
                                                                        'pos_settings', 
                                                                        'sub_units', 
                                                                        'discount', 
                                                                        'waiters', 
                                                                        'edit_discount', 
                                                                        'edit_price', 
                                                                        'purchase_line_id', 
                                                                        'warranties', 
                                                                        'quantity', 
                                                                        'list_of_prices_in_unit', 
                                                                        'list_of_prices', 
                                                                        'is_direct_sell'
                                                                    ]))
                                                        ->render();
                            break;
                        case 'sell_edit':
                            $enable_stock           = $product->enable_stock;
                            $currency               = app('request')->input('currency');
                            $output['enable_stock'] = $enable_stock  ;
                            $output['html_content'] = view('sale_pos.view_types.sell_add')
                                                        ->with(compact([
                                                                'product', 
                                                                "childs", 
                                                                "store_id",
                                                                'currency', 
                                                                'row_count' , 
                                                                "status" ,
                                                                'warehouse' , 
                                                                'tax_dropdown', 
                                                                'enabled_modules', 
                                                                'pos_settings', 
                                                                'sub_units', 
                                                                'discount', 
                                                                'waiters', 
                                                                'edit_discount', 
                                                                'edit_price', 
                                                                'purchase_line_id', 
                                                                'warranties', 
                                                                'quantity', 
                                                                'list_of_prices_in_unit', 
                                                                'list_of_prices', 
                                                                'is_direct_sell'
                                                            ]))
                                                        ->render();
                            break;
                        case 'delivery_page':
                            
                            $childs    =  Warehouse::product_stores($product->product_id);
                            $available_qty  =  0;
                            if (count($childs)) {
                                $inf =  WarehouseInfo::where('store_id',$store_id)
                                ->where('product_id',$product->product_id)->first();
                                if (empty($inf)) {
                                    $store_k       =  array_keys($childs)[0];
                                    $available_qty =  WarehouseInfo::where('store_id',$store_k)
                                                            ->where('product_id',$product->product_id)->first()->product_qty;
                                }else{
                                    $available_qty = $inf->product_qty;
                                }
                                
                            }
                            if(count($line_main)>0){
                                foreach($line_main as $single_prc){
                                    
                                    $list_of_prices[$single_prc->id] = $single_prc->unit_price_before_discount; 
                                }
                            }
                            
                            $enable_stock = $product->enable_stock;
                            $tran      = app('request')->input('trans_id');
                            $should    = request()->get('should') ;
                            $reamain             = null;
                            if($should  != null){
                                $TransactionSellLine = TransactionSellLine::where("transaction_id",$tran)->where("product_id",$product->product_id)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                                $DeliveredPrevious   = \App\Models\DeliveredPrevious::where("transaction_id",$tran)->where("product_id",$product->product_id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                                $wrong               = \App\Models\DeliveredWrong::where("transaction_id",$tran)->where("product_id",$product->product_id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                                $margin              = $TransactionSellLine - $DeliveredPrevious ;
                                $reamain             = null;
                                if($margin > 0){
                                    $reamain = $margin ;
                                }elseif($margin < 0){
                                    $reamain = null;
                                }elseif($margin == 0){
                                    $reamain = null;
                                }
                                
                            }
                             
                            
                            $output['enable_stock'] =  $enable_stock  ;
                            $output['html_content'] =  view('delivery.partials.product_row')
                                            ->with(compact('available_qty','product','list_of_prices','currency' ,"childs", "store_id", 'row_count' , "status" ,'warehouse' ,
                                                'tax_dropdown', 'enabled_modules','reamain', 'pos_settings', 'sub_units', 'discount', 'waiters',
                                                'edit_discount', 'edit_price', 'purchase_line_id', 'warranties', 'quantity','enable_stock',
                                                'is_direct_sell'))
                                            ->render();
                            break;                    
                        default:
                                $enable_stock           = $product->enable_stock;
                                $output['enable_stock'] = $enable_stock  ;
                                $output['html_content'] =  view('sale_pos.product_row')
                                                    ->with(compact([
                                                                    'product',
                                                                    'currency', 
                                                                    "childs", 
                                                                    "store_id", 
                                                                    'row_count' , 
                                                                    "status" ,
                                                                    'warehouse' , 
                                                                    'tax_dropdown', 
                                                                    'enabled_modules', 
                                                                    'pos_settings', 
                                                                    'sub_units', 
                                                                    'discount', 
                                                                    'waiters', 
                                                                    'edit_discount', 
                                                                    'edit_price', 
                                                                    'purchase_line_id', 
                                                                    'warranties', 
                                                                    'quantity', 
                                                                    'is_direct_sell'
                                                                ]))
                                                    ->render();
                            break;
                    }
                }
            }

            $output['enable_sr_no'] = $product->enable_sr_no;
            if ($this->transactionUtil->isModuleEnabled('modifiers')  && !$is_direct_sell) {
                $this_product = Product::where('business_id', $business_id)->find($product->product_id);
                if (count($this_product->modifier_sets) > 0) {
                    $product_ms = $this_product->modifier_sets;
                    // for modifire
                    $output['html_modifier'] =  view('restaurant.product_modifier_set.modifier_for_product')
                        ->with(compact('product_ms','currency',  "store_id",'row_count' , "childs"))->render();
                }
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output['success'] = false;
            // $output['msg'] = __('lang_v1.item_out_of_stock');
            $output['msg'] = "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage();
        }

        return $output;
    }

    /**
     * Returns the HTML row for a payment in POS
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getPaymentRow(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $row_index = $request->input('row_index');
        $location_id = $request->input('location_id');
        $removable = true;
        $payment_types = $this->productUtil->payment_types($location_id, true);

        $payment_line = $this->dummyPaymentLine;

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false, true);
        }

        return view('sale_pos.partials.payment_row')
            ->with(compact('payment_types', 'row_index', 'removable', 'payment_line', 'accounts'));
    }

    /**
     * Returns recent transactions
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getRecentTransactions(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');
        $transaction_status = $request->get('status');

        $register = $this->cashRegisterUtil->getCurrentCashRegister($user_id);

        $query = Transaction::where('business_id', $business_id)
            ->where('transactions.created_by', $user_id)
            ->where('transactions.type', 'sale')
            ->where('is_direct_sale', 0);

        if ($transaction_status == 'final') {
            //Commented as credit sales not showing
            // if (!empty($register->id)) {
            //     $query->leftjoin('cash_register_transactions as crt', 'transactions.id', '=', 'crt.transaction_id')
            //     ->where('crt.cash_register_id', $register->id);
            // }
        }

        if ($transaction_status == 'quotation') {
            $query->where('transactions.status', 'draft')
                ->where('sub_status', 'quotation');
        } elseif ($transaction_status == 'draft') {
            $query->where('transactions.status', 'draft')
                ->whereNull('sub_status');
        } else {
            $query->where('transactions.status', $transaction_status);
        }

        $transaction_sub_type = $request->get('transaction_sub_type');
        if (!empty($transaction_sub_type)) {
            $query->where('transactions.sub_type', $transaction_sub_type);
        } else {
            $query->where('transactions.sub_type', null);
        }

        if (!empty($request->invoice_number)) {
            $query->where('transactions.invoice_no', $request->invoice_number);
        }

        $transactions = $query->orderBy('transactions.created_at', 'desc')
            ->groupBy('transactions.id')
            ->select('transactions.*')
            ->with(['contact', 'table'])
            ->limit(10)
            ->get();

        return view('sale_pos.partials.recent_transactions')
            ->with(compact('transactions', 'transaction_sub_type'));
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
                    'msg' => trans("messages.something_went_wrong")
                ];

                $business_id = $request->session()->get('user.business_id');
                $transaction = Transaction::where('business_id', $business_id)
                    ->where('id', $transaction_id)
                    ->with(['location'])
                    ->first();

                if (empty($transaction)) {
                    return $output;
                }

                $printer_type = 'browser';
                if (!empty(request()->input('check_location')) && request()->input('check_location') == true) {
                    $printer_type = $transaction->location->receipt_printer_type;
                }

                $is_package_slip = !empty($request->input('package_slip')) ? true : false;
                $invoice_layout_id = $transaction->is_direct_sale ? $transaction->location->sale_invoice_layout_id : null;
                $receipt = $this->receiptContent($business_id, $transaction->location_id, $transaction_id, $printer_type, $is_package_slip, false, $invoice_layout_id);

                if (!empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Gives suggetion for product based on category
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getProductSuggestion(Request $request)
    {
        if ($request->ajax()) {
            $category_id = $request->get('category_id');
            $brand_id = $request->get('brand_id');
            $location_id = $request->get('location_id');
            $term = $request->get('term');

            $check_qty = false;
            $business_id = $request->session()->get('user.business_id');
            $business = $request->session()->get('business');
            $pos_settings = empty($business->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business->pos_settings, true);

            $products = Variation::join('products as p', 'variations.product_id', '=', 'p.id')
                ->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                ->leftjoin(
                    'variation_location_details AS VLD',
                    function ($join) use ($location_id) {
                        $join->on('variations.id', '=', 'VLD.variation_id');

                        //Include Location
                        if (!empty($location_id)) {
                            $join->where(function ($query) use ($location_id) {
                                $query->where('VLD.location_id', '=', $location_id);
                                //Check null to show products even if no quantity is available in a location.
                                //TODO: Maybe add a settings to show product not available at a location or not.
                                $query->orWhereNull('VLD.location_id');
                            });;
                        }
                    }
                )
                ->where('p.business_id', $business_id)
                ->where('p.type', '!=', 'modifier')
                ->where('p.is_inactive', 0)
                ->where('p.not_for_selling', 0)
                //Hide products not available in the selected location
                ->where(function ($q) use ($location_id) {
                    $q->where('pl.location_id', $location_id);
                });

            //Include search
            if (!empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('p.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sku2', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                });
            }

            //Include check for quantity
            if ($check_qty) {
                $products->where('VLD.qty_available', '>', 0);
            }

            if (!empty($category_id) && ($category_id != 'all')) {
                $products->where(function ($query) use ($category_id) {
                    $query->where('p.category_id', $category_id);
                    $query->orWhere('p.sub_category_id', $category_id);
                });
            }
            if (!empty($brand_id) && ($brand_id != 'all')) {
                $products->where('p.brand_id', $brand_id);
            }

            if (!empty($request->get('is_enabled_stock'))) {
                $is_enabled_stock = 0;
                if ($request->get('is_enabled_stock') == 'product') {
                    $is_enabled_stock = 1;
                }

                $products->where('p.enable_stock', $is_enabled_stock);
            }

            if (!empty($request->get('repair_model_id'))) {
                $products->where('p.repair_model_id', $request->get('repair_model_id'));
            }

            $products = $products->select(
                'p.id as product_id',
                'p.name',
                'p.type',
                'p.enable_stock',
                'p.image as product_image',
                'variations.id',
                'variations.name as variation',
                'VLD.qty_available',
                'variations.default_sell_price as selling_price',
                'variations.sub_sku'
            )
                ->with(['media', 'group_prices'])
                ->orderBy('p.name', 'asc')
                ->paginate(50);

            $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');

            $allowed_group_prices = [];
            foreach ($price_groups as $key => $value) {
                if (auth()->user()->can('selling_price_group.' . $key)) {
                    $allowed_group_prices[$key] = $value;
                }
            }

            $show_prices = !empty($pos_settings['show_pricing_on_product_sugesstion']);

            return view('sale_pos.partials.product_list')
                ->with(compact('products', 'allowed_group_prices', 'show_prices'));
        }
    }

    /**
     * Shows invoice url.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showInvoiceUrl($id)
    {
        // if (!auth()->user()->can('sell.update')) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $transaction = Transaction::where('business_id', $business_id)
                ->findorfail($id);
            $url = $this->transactionUtil->getInvoiceUrl($id, $business_id);

            return view('sale_pos.partials.invoice_url_modal')
                ->with(compact('transaction', 'url'));
        }
    }

    /**
     * Shows invoice to guest user.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function showInvoice($token)
    {
        $transaction = Transaction::where('invoice_token', $token)->with(['business', 'location'])->first();

        // use invoice_layout_id from table business_location
        if (!empty($transaction)) {
            $invoice_layout_id = $transaction->is_direct_sale ? $transaction->location->sale_invoice_layout_id : null;

            $receipt = $this->receiptContent($transaction->business_id, $transaction->location_id, $transaction->id, 'browser', false, true, $invoice_layout_id);

            $title = $transaction->business->name . ' | ' . $transaction->invoice_no;
            return view('sale_pos.partials.show_invoice')
                ->with(compact('receipt', 'title'));
        } else {
            die(__("messages.something_went_wrong"));
        }
    }

    /**
     * Display a listing of the recurring invoices.
     *
     * @return \Illuminate\Http\Response
     */
    public function listSubscriptions()
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access') && !auth()->user()->can('ReadOnly.views')&& !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views')&& !auth()->user()->can('admin_supervisor.views')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sale')
                ->where('transactions.status', 'final')
                ->where('transactions.is_recurring', 1)
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.is_direct_sale',
                    'transactions.invoice_no',
                    'contacts.name',
                    'transactions.subscription_no',
                    'bl.name as business_location',
                    'transactions.recur_parent_id',
                    'transactions.recur_stopped_on',
                    'transactions.is_recurring',
                    'transactions.recur_interval',
                    'transactions.recur_interval_type',
                    'transactions.recur_repetitions',
                    'transactions.subscription_repeat_on'
                )->with(['subscription_invoices']);



            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }
            if (!empty(request()->contact_id)) {
                $sells->where('transactions.contact_id', request()->contact_id);
            }
            $datatable = Datatables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';

                        if ($row->is_recurring == 1 && auth()->user()->can("sell.update")) {
                            $link_text = !empty($row->recur_stopped_on) ? __('lang_v1.start_subscription') : __('lang_v1.stop_subscription');
                            $link_class = !empty($row->recur_stopped_on) ? 'btn-success' : 'btn-danger';

                            $html .= '<a href="' . action('SellPosController@toggleRecurringInvoices', [$row->id]) . '" class="toggle_recurring_invoice btn btn-xs ' . $link_class . '"><i class="fa fa-power-off"></i> ' . $link_text . '</a>';

                            if ($row->is_direct_sale == 0) {
                                $html .= '<a target="_blank" class="btn btn-xs btn-primary" href="' . action('SellPosController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a>';
                            } else {
                                $html .= '<a target="_blank" class="btn btn-xs btn-primary" href="' . action('SellController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a>';
                            }

                            if (auth()->user()->can("direct_sell.delete") || auth()->user()->can("sell.delete")) {
                                $html .= '&nbsp;<a href="' . action('SellPosController@destroy', [$row->id]) . '" class="delete-sale btn btn-xs btn-danger"><i class="fas fa-trash"></i> ' . __("messages.delete") . '</a>';
                            }
                        }

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('recur_interval', function ($row) {
                    $type = $row->recur_interval == 1 ? Str::singular(__('lang_v1.' . $row->recur_interval_type)) : __('lang_v1.' . $row->recur_interval_type);
                    $recur_interval = $row->recur_interval . $type;

                    if ($row->recur_interval_type == 'months' && !empty($row->subscription_repeat_on)) {
                        $recur_interval .= '<br><small class="text-muted">' .
                            __('lang_v1.repeat_on') . ': ' . str_ordinal($row->subscription_repeat_on);
                    }
                    return $recur_interval;
                })
                ->editColumn('recur_repetitions', function ($row) {
                    return !empty($row->recur_repetitions) ? $row->recur_repetitions : '-';
                })
                ->addColumn('subscription_invoices', function ($row) {
                    $invoices = [];
                    if (!empty($row->subscription_invoices)) {
                        $invoices = $row->subscription_invoices->pluck('invoice_no')->toArray();
                    }

                    $html = '';
                    $count = 0;
                    if (!empty($invoices)) {
                        $imploded_invoices = '<span class="label bg-info">' . implode('</span>, <span class="label bg-info">', $invoices) . '</span>';
                        $count = count($invoices);
                        $html .= '<small>' . $imploded_invoices . '</small>';
                    }
                    if ($count > 0) {
                        $html .= '<br><small class="text-muted">' .
                            __('sale.total') . ': ' . $count . '</small>';
                    }

                    return $html;
                })
                ->addColumn('last_generated', function ($row) {
                    if (!empty($row->subscription_invoices)) {
                        $last_generated_date = $row->subscription_invoices->max('created_at');
                    }
                    return !empty($last_generated_date) ? $last_generated_date->diffForHumans() : '';
                })
                ->addColumn('upcoming_invoice', function ($row) {
                    if (empty($row->recur_stopped_on)) {
                        $last_generated = !empty(count($row->subscription_invoices)) ? \Carbon::parse($row->subscription_invoices->max('transaction_date')) : \Carbon::parse($row->transaction_date);
                        $last_generated_string = $last_generated->format('Y-m-d');
                        $last_generated = \Carbon::parse($last_generated_string);

                        if ($row->recur_interval_type == 'days') {
                            $upcoming_invoice = $last_generated->addDays($row->recur_interval);
                        } elseif ($row->recur_interval_type == 'months') {
                            if (!empty($row->subscription_repeat_on)) {
                                $last_generated_string = $last_generated->format('Y-m');
                                $last_generated = \Carbon::parse($last_generated_string . '-' . $row->subscription_repeat_on);
                            }

                            $upcoming_invoice = $last_generated->addMonths($row->recur_interval);
                        } elseif ($row->recur_interval_type == 'years') {
                            $upcoming_invoice = $last_generated->addYears($row->recur_interval);
                        }
                    }
                    return !empty($upcoming_invoice) ? $this->transactionUtil->format_date($upcoming_invoice) : '';
                })
                ->rawColumns(['action', 'subscription_invoices', 'recur_interval'])
                ->make(true);

            return $datatable;
        }
        return view('sale_pos.subscriptions');
    }

    /**
     * Starts or stops a recurring invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleRecurringInvoices($id)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $transaction = Transaction::where('business_id', $business_id)
                ->where('type', 'sale')
                ->where('is_recurring', 1)
                ->findorfail($id);

            if (empty($transaction->recur_stopped_on)) {
                $transaction->recur_stopped_on = \Carbon::now();
            } else {
                $transaction->recur_stopped_on = null;
            }
            $transaction->save();

            $output = [
                'success' => 1,
                'msg' => trans("lang_v1.updated_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    public function getRewardDetails(Request $request)
    {
        if ($request->session()->get('business.enable_rp') != 1) {
            return '';
        }

        $business_id = request()->session()->get('user.business_id');

        $customer_id = $request->input('customer_id');

        $redeem_details = $this->transactionUtil->getRewardRedeemDetails($business_id, $customer_id);

        return json_encode($redeem_details);
    }

    public function placeOrdersApi(Request $request)
    {
        try {
            $api_token = $request->header('API-TOKEN');
            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $business_id = $api_settings->business_id;
            $location_id = $api_settings->location_id;

            $input = $request->only(['products', 'customer_id', 'addresses']);

            //check if all stocks are available
            $variation_ids = [];
            foreach ($input['products'] as $product_data) {
                $variation_ids[] = $product_data['variation_id'];
            }

            $variations_details = $this->getVariationsDetails($business_id, $location_id, $variation_ids);
            $is_valid = true;
            $error_messages = [];
            $sell_lines = [];
            $final_total = 0;
            foreach ($variations_details as $variation_details) {
                if ($variation_details->product->enable_stock == 1) {
                    if (empty($variation_details->variation_location_details[0]) || $variation_details->variation_location_details[0]->qty_available < $input['products'][$variation_details->id]['quantity']) {
                        $is_valid = false;
                        $error_messages[] = 'Only ' . $variation_details->variation_location_details[0]->qty_available . ' ' . $variation_details->product->unit->short_name . ' of ' . $input['products'][$variation_details->id]['product_name'] . ' available';
                    }
                }

                //Create product line array
                $sell_lines[] = [
                    'product_id' => $variation_details->product->id,
                    'unit_price_before_discount' => $variation_details->unit_price_inc_tax,
                    'unit_price' => $variation_details->unit_price_inc_tax,
                    'unit_price_inc_tax' => $variation_details->unit_price_inc_tax,
                    'variation_id' => $variation_details->id,
                    'quantity' => $input['products'][$variation_details->id]['quantity'],
                    'item_tax' => 0,
                    'enable_stock' => $variation_details->product->enable_stock,
                    'tax_id' => null,
                ];

                $final_total += ($input['products'][$variation_details->id]['quantity'] * $variation_details->unit_price_inc_tax);
            }

            if (!$is_valid) {
                return $this->respond([
                    'success' => false,
                    'error_messages' => $error_messages
                ]);
            }

            $business = Business::find($business_id);
            $user_id = $business->owner_id;

            $business_data = [
                'id' => $business_id,
                'accounting_method' => $business->accounting_method,
                'location_id' => $location_id
            ];

            $customer = Contact::where('business_id', $business_id)
                ->whereIn('type', ['customer', 'both'])
                ->find($input['customer_id']);

            $order_data = [
                'business_id' => $business_id,
                'location_id' => $location_id,
                'contact_id' => $input['customer_id'],
                'final_total' => $final_total,
                'created_by' => $user_id,
                'status' => 'final',
                'payment_status' => 'due',
                'additional_notes' => '',
                'transaction_date' => \Carbon::now(),
                'customer_group_id' => $customer->customer_group_id,
                'tax_rate_id' => null,
                'sale_note' => null,
                'commission_agent' => null,
                'order_addresses' => json_encode($input['addresses']),
                'products' => $sell_lines,
                'is_created_from_api' => 1,
                'discount_type' => 'fixed',
                'discount_amount' => 0
            ];

            $invoice_total = [
                'total_before_tax' => $final_total,
                'tax' => 0,
            ];

            DB::beginTransaction();

            $transaction = $this->transactionUtil->createSellTransaction($business_id, $order_data, $invoice_total, $user_id, false);

            //Create sell lines
            $this->transactionUtil->createOrUpdateSellLines($transaction, $order_data['products'], $order_data['location_id'], false, null, [], false);
            //update product stock
            foreach ($order_data['products'] as $product) {
                if ($product['enable_stock']) {
                    $this->productUtil->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $order_data['location_id'],
                        $product['quantity']
                    );
                }
            }

            $this->transactionUtil->mapPurchaseSell($business_data, $transaction->sell_lines, 'purchase');
            //Auto send notification
            $this->notificationUtil->autoSendNotification($business_id, 'new_sale', $transaction, $transaction->contact);

            DB::commit();

            $receipt = $this->receiptContent($business_id, $transaction->location_id, $transaction->id);

            $output = [
                'success' => 1,
                'transaction' => $transaction,
                'receipt' => $receipt
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $msg = trans("messages.something_went_wrong");

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            }

            if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
                $msg = $e->getMessage();
            }

            $output = [
                'success' => 0,
                'error_messages' => [$msg]
            ];
        }

        return $this->respond($output);
    }

    private function getVariationsDetails($business_id, $location_id, $variation_ids)
    {
        $variation_details = Variation::whereIn('id', $variation_ids)
            ->with([
                'product' => function ($q) use ($business_id) {
                    $q->where('business_id', $business_id);
                },
                'product.unit',
                'variation_location_details' => function ($q) use ($location_id) {
                    $q->where('location_id', $location_id);
                }
            ])->get();

        return $variation_details;
    }

    public function getTypesOfServiceDetails(Request $request)
    {
        $location_id = $request->input('location_id');
        $types_of_service_id = $request->input('types_of_service_id');

        $business_id = $request->session()->get('user.business_id');

        $types_of_service = TypesOfService::where('business_id', $business_id)
            ->where('id', $types_of_service_id)
            ->first();

        $price_group_id = !empty($types_of_service->location_price_group[$location_id])
            ? $types_of_service->location_price_group[$location_id] : '';
        $price_group_name = '';

        if (!empty($price_group_id)) {
            $price_group = SellingPriceGroup::find($price_group_id);
            $price_group_name = $price_group->name;
        }

        $modal_html = view('types_of_service.pos_form_modal')
            ->with(compact('types_of_service'))->render();

        return $this->respond([
            'price_group_id' => $price_group_id,
            'packing_charge' => $types_of_service->packing_charge,
            'packing_charge_type' => $types_of_service->packing_charge_type,
            'modal_html' => $modal_html,
            'price_group_name' => $price_group_name
        ]);
    }

    private function __getwarranties()
    {
        $business_id = session()->get('user.business_id');
        $common_settings = session()->get('business.common_settings');
        $is_warranty_enabled = !empty($common_settings['enable_product_warranty']) ? true : false;
        $warranties = $is_warranty_enabled ? Warranty::forDropdown($business_id) : [];
        return $warranties;
    }

    /**
     * Parse the weighing barcode.
     *
     * @return array
     */
    private function __parseWeighingBarcode($scale_barcode)
    {
        $business_id = session()->get('user.business_id');

        $scale_setting = session()->get('business.weighing_scale_setting');

        $error_msg = trans("messages.something_went_wrong");

        //Check for prefix.
        if ((strlen($scale_setting['label_prefix']) == 0) || Str::startsWith($scale_barcode, $scale_setting['label_prefix'])) {
            $scale_barcode = substr($scale_barcode, strlen($scale_setting['label_prefix']));

            //Get product sku, trim left side 0
            $sku = ltrim(substr($scale_barcode, 0, $scale_setting['product_sku_length'] + 1), '0');

            //Get quantity integer
            $qty_int = substr($scale_barcode, $scale_setting['product_sku_length'] + 1, $scale_setting['qty_length'] + 1);

            //Get quantity decimal
            $qty_decimal = '0.' . substr($scale_barcode, $scale_setting['product_sku_length'] + $scale_setting['qty_length'] + 2, $scale_setting['qty_length_decimal'] + 1);

            $qty = (float)$qty_int + (float)$qty_decimal;

            //Find the variation id
            $result = $this->productUtil->filterProduct($business_id, $sku, null, false, null, [], ['sub_sku'], false, 'exact')->first();

            if (!empty($result)) {
                return [
                    'variation_id' => $result->variation_id,
                    'qty' => $qty,
                    'success' => true
                ];
            } else {
                $error_msg = trans("lang_v1.sku_not_match", ['sku' => $sku]);
            }
        } else {
            $error_msg = trans("lang_v1.prefix_did_not_match");
        }

        return [
            'success' => false,
            'msg' => $error_msg
        ];
    }

    public function getFeaturedProducts($id)
    {
        $location = BusinessLocation::findOrFail($id);
        $featured_products = $location->getFeaturedProducts();

        if (!empty($featured_products)) {
            return view('sale_pos.partials.featured_products')->with(compact('featured_products'));
        } else {
            return '';
        }
    }

     /**
     * Converts drafts and quotations to invoice
     *
     */
    public function convertToInvoice($id)
    {
         
        if (!auth()->user()->can('sell.create') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $transaction = Transaction::with([
                    'sell_lines',
                    'sell_lines.product',
                    'sell_lines.variations',
                    'contact'
                ])
                ->where('business_id', $business_id)
                ->findOrFail($id);

                $transaction_before = $transaction->replicate();
                $is_direct_sale     = $transaction->is_direct_sale;
                # Check Customer credit limit
                $info_status        =  ($transaction->status == "delivered" )?'delivered':'final';
                $info_sub_status    =  'f';
                $data = [
                    'final_total'   => $transaction->final_total,
                    'contact_id'    => $transaction->contact_id,
                    'status'        => $info_status,
                    'sub_status'    => $info_sub_status
                ];
        
                $is_credit_limit_exeeded       = $this->transactionUtil->isCustomerCreditLimitExeeded($data, $id);
                if ($is_credit_limit_exeeded !== false) {
                    $credit_limit_amount = $this->transactionUtil->num_f($is_credit_limit_exeeded, true);
                    $output = [
                        'success' => 0,
                        'msg'     => __('lang_v1.cutomer_credit_limit_exeeded', ['credit_limit' => $credit_limit_amount])
                    ];
                    return redirect()
                        ->back()
                        ->with('status', $output);
                }
            
                DB::beginTransaction();
                # Check if there is a open register, if no then redirect to Create Register screen.
                if (!$is_direct_sale && $this->cashRegisterUtil->countOpenedRegister() == 0) {
                    return redirect()->action('CashRegisterController@create');
                }
                $transaction->previous         = $transaction->invoice_no;
                $pattern_id                    = ($transaction)?$transaction->pattern_id:null;
                $pat                           = \App\Models\Pattern::find($pattern_id);
                $scheme_id                     = (!empty($pat))?$pat->scheme->id:"";
                $invoice_no                    = $this->transactionUtil->getInvoiceNumber($business_id, 'final', $transaction->location_id, $scheme_id);
                $transaction->invoice_no       = $invoice_no;
                $transaction->transaction_date = \Carbon::now();
                $transaction->status           = ($transaction->status == "delivered" )?'delivered':'final';
                $transaction->sub_status       = "f";
                $transaction->is_quotation     = 0;
                $transaction->save();
                # update product stock
                foreach ($transaction->sell_lines as $sell_line) {
                    $decrease_qty = $sell_line->quantity;
                    if($transaction->status != "delivered" ){
                        if ($sell_line->product->enable_stock == 1) {
                            $this->productUtil->decreaseProductQuantity(
                                $sell_line->product_id,
                                $sell_line->variation_id,
                                $transaction->location_id,
                                $decrease_qty
                            );
                        }
                    }
                    if ($sell_line->product->type == 'combo') {
                        # Decrease quantity of combo as well.
                        $combo_variations = $sell_line->variations->combo_variations;
                        foreach ($combo_variations as $key => $value) {
                            $base_unit_multiplier      = 1;
                            if (!empty($value['unit_id'])) {
                                $unit = Unit::find($value['unit_id']);
                                $base_unit_multiplier = !empty($unit->base_unit_multiplier) ? $unit->base_unit_multiplier : $base_unit_multiplier;
                            }
                            $combo_variations[$key]['product_id'] = $sell_line->product_id;
                            $combo_variations[$key]['product_id'] = $sell_line->product_id;
                            $combo_variations[$key]['quantity']   = $value['quantity'] * $decrease_qty * $base_unit_multiplier;
                        }
                    }
                }
                \App\AccountTransaction::add_sell_pos($transaction);
                \App\Models\StatusLive::insert_data_s($business_id,$transaction,"Sales Invoice");

                DB::commit();

                $output = ['success' => 1, 'msg' => __('lang_v1.converted_to_invoice_successfully' )];
                
                return  $output ;

            } catch (Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $msg = trans("messages.something_went_wrong");
                if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                    $msg = $e->getMessage();
                }
                if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
                    $msg = $e->getMessage();
                }
                $output = [
                    'success' => 0,
                    'msg'     => $msg
                ];
                
                return  $output ;
            }
        }

    }

    /**
     * Converts drafts and quotations to invoice
     *
     */
    public function convertToProforma($id)
    {
        if (!auth()->user()->can('sell.create') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id    = request()->session()->get('user.business_id');
            $user_id        = request()->session()->get('user.id');
           
            $transaction = Transaction::where('business_id', $business_id)
                                ->where('status', 'draft')
                                ->findOrFail($id);
            $pattern_id     = ($transaction)?$transaction->pattern_id:null;

            $transaction_before = $transaction->replicate();
            $transaction->sub_status   = 'proforma';
            $transaction->first_ref_no = $transaction->invoice_no;
            $ref_count = $this->transactionUtil->setAndGetReferenceCount('Approve',$business_id,$pattern_id);
            $number = $this->transactionUtil->generateReferenceNumber('Approve', $ref_count , null, null,$pattern_id );
            $transaction->invoice_no = $number  ;
            $transaction->save();

            $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);

            $output = ['success' => 1, 'msg' => __('lang_v1.converted_to_proforma_successfully')];
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans("messages.something_went_wrong")
            ];
        }

        return $output;
    }
        /**
     * Converts drafts and quotations to invoice
     *
     */
    public function convertToQoutation($id)
    {
        if (!auth()->user()->can('sell.create') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id    = request()->session()->get('user.business_id');
            $user_id        = request()->session()->get('user.id');
            
            
            $transaction = Transaction::where('business_id', $business_id)
                ->where('status', 'draft')
                ->findOrFail($id);

            $pattern_id         = ($transaction)?$transaction->pattern_id:null;
            $transaction_before = $transaction->replicate();
            
            $transaction->sub_status   = 'quotation';
            $transaction->is_quotation = 1;
            $transaction->refe_no = $transaction->invoice_no;
            $ref_count = $this->transactionUtil->setAndGetReferenceCount('quotation',$business_id,$pattern_id);
            $number    = $this->transactionUtil->generateReferenceNumber('quotation', $ref_count , null, null,$pattern_id );
            $transaction->invoice_no = $number  ;
            $transaction->save();

            $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);

            $output = ['success' => 1, 'msg' => __('lang_v1.converted_to_quotation_successfully')];
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans("messages.something_went_wrong")
            ];
        }

        return $output;
    }
}
