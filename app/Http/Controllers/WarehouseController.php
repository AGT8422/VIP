<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLocation;
use DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;
use App\Models\Warehouse;
use App\Models\WarehouseInfo;
use App\Transaction;
use App\Product;
use App\Category;
use App\Brands;
use App\Account;
use App\InvoiceLayout;
use App\InvoiceScheme;
use App\SellingPriceGroup;
use App\TypesOfService;
use App\Contact;
use App\CustomerGroup;
use App\TaxRate;
use App\Utils\BusinessUtil;
use App\Utils\CashRegisterUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
 
use App\PurchaseLine;
use App\MovementWarehouse;
use App\VariationLocationDetails;
use App\TransactionSellLine;
use App\Unit;

class WarehouseController extends Controller
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
        $this->contactUtil      = $contactUtil;
        $this->productUtil      = $productUtil;
        $this->businessUtil     = $businessUtil;
        $this->transactionUtil  = $transactionUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moduleUtil       = $moduleUtil;
        $this->notificationUtil = $notificationUtil;

        $this->dummyPaymentLine = [
              'method'              => 'cash'
            , 'amount'              => 0
            , 'note'                => '', 'card_transaction_number' => ''
            , 'card_number'         => ''
            , 'card_type'           => ''
            , 'card_holder_name'    => ''
            , 'card_month'          => ''
            , 'card_year'           => ''
            , 'card_security'       => ''
            , 'cheque_number'       => ''
            , 'bank_account_number' => ''
            , 'is_return'            => 0
            , 'transaction_no'      => ''
        ];
    }
 
    /**
     * Open Stores Page.
     *
     * @return View Page
     */
    public function index() 
    {
        if (!auth()->user()->can('warehouse.view') && !auth()->user()->can('SalesMan.views') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('manufuctoring.views')&& !auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }
        $Warehouse_list       = []; 
        $mainstore_categories = [];
        $product_list         = [];
        $business_id          = request()->session()->get('user.business_id');
        $business_locations   = BusinessLocation::forDropdown($business_id);
        $warehouse_info       = WarehouseInfo::where('business_id', $business_id)
                                            ->where(function($query){
                                                if(app('request')->input('warehouse_id')){
                                                    $query->where('store_id',app('request')->input('warehouse_id'));
                                                }
                                            })->get();
        $mainstore             = Warehouse::where('business_id', $business_id)->select(['business_id','mainStore','name','id','parent_id'])->get();
        $Warehouse_ist         = Warehouse::where('business_id', $business_id)->get();
        $product_ist           = Product::where('business_id', $business_id)->get();

      
        foreach ($mainstore as $main_stor) {  
            $mainstore_categories[$main_stor->id] = $main_stor->name;
        }
        foreach ($Warehouse_ist as $Warehouse) {
            $Warehouse_list[$Warehouse->id] = $Warehouse->name;
        }
        foreach ($product_ist as $pro) {
            $product_list[$pro->id] = $pro->name;
        }
        return view('warehouse.index')->with(compact('mainstore','Warehouse_list' ,'product_list','warehouse_info','business_locations','business_id','mainstore_categories'));
    }

    /**
     * All Stores In Database.
     * ( Main & Sub ) Stores
     * @return  DataTable Rows
     */
    public function allStores()
    {
        if (!auth()->user()->can('allStores')  && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views') && !auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }
        $name          = request()->input("name");
        $type          = request()->input("type");
        $business_id   = request()->session()->get('user.business_id');
        $warehouses    = Warehouse::where("business_id",$business_id);
        if($name != null) { $warehouses->where("id",$name); }
        if($type != null) { $value = $type  - 1; $warehouses->where("status",$value); } 
        $warehouses->get();
        return Datatables::of($warehouses)->addColumn(
                    'action', '@can("warehouse.Edit")
                        <a href="{{URL::to(\'/warehouse/edit\',[$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
                        &nbsp; @endcan  @if(request()->session()->get("user.id") == 1)
                        <button data-href="{{action(\'WarehouseController@delete\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button> @endif'
                )->editColumn("name",function($row){
                    return "<a href='".\URL::to("warehouse/index?warehouse_id=".$row->id)."'>".$row->name."</a>";
                })
                ->rawColumns(['action','name'])
                ->make(true);
    }

    /** 
     * View All Movements For Stores.
     *
     * @return DataTable Rows
     */
    public function allMovement(Request $request)
    {
        if (!auth()->user()->can('warehouse.create') && !auth()->user()->can('admin_without.views') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('manufuctoring.views')&& !auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id                 = request()->session()->get('user.business_id');
        $MovementWarehouse           = MovementWarehouse::where('business_id', $business_id); 
        // ................................ filters
            $name       = request()->input('name') ;
            if(!empty($name)){
               $Whouse = Warehouse::find($name);
               if($Whouse->status == 0){
                    $warehouse_ids  = Warehouse::where("mainStore",$Whouse->name)->get();
                    $warehouse_ids_ = [];
                    foreach($warehouse_ids as $it){
                        $warehouse_ids_[] = $it->id;
                    } 
                    $MovementWarehouse->whereIn("store_id",$warehouse_ids_);
                }else{
                    $MovementWarehouse->where("store_id",$name);
                }
            }
            
            $movement   = request()->input('movement') ;
            if(!empty($movement)){
               $MovementWarehouse->where("movement",$movement);
            }
            
            $product_id = request()->input('product_id') ;
            if(!empty($product_id)){
               $MovementWarehouse->where("product_id",$product_id);
            }
            
            $date       = request()->input('date') ;
            if(!empty($date)){
                $MovementWarehouse->where("date","<=",$date);
            }
        // ....................................... end filter
        $MovementWarehouse->orderBy("date","desc")->orderBy("id","desc");
         
      return Datatables::of($MovementWarehouse)
                ->editColumn('store_id',function($row){
                    return   ( $row->store)?$row->store->name:'---';
                })->editColumn('product_name',function($row){
                    $html = '<button type="button" data-href="' . action('ProductController@view', [$row->product_id]) . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->product_name . '</button>';
                    return $html;
                })->editColumn("business_id",function($row){
                        $html = "business  name";
                        foreach($row->business->locations as $location){ if($location->business_id == $row->business_id){ $html = $location->name ; } }
                        return $html ;
                })->editColumn("created_at",function($row){
                    if($row->date != null){
                        $date = date_format(\Carbon\Carbon::parse($row->date),"Y-m-d");
                    }else{
                        $date = date_format($row->created_at,"Y-m-d");
                    }
                    return $date;
                })->editColumn("unit_id",function($row){
                    return $row->product->unit->actual_name;
                })->editColumn("current_qty",function($row) use($MovementWarehouse){
                        \App\Models\ItemMove::refreshWarehouse($row->product_id);
                        return $row->current_qty;  
                })->editColumn("movement",function($row)  {
                        if($row->transaction){
                            $html  = $row->transaction->type;
                            if($row->transaction->type == "opening_stock"){
                                $open = \App\Models\OpeningQuantity::where("transaction_id",$row->transaction->id)->first();
                                $html .= "<br>" ."<button class='btn btn-modal btn-link' data-href='" .  action("ProductController@ViewOpeningProduct" , [trim($open->id)]) . "' data-container='.view_modal'>" . $row->transaction->ref_no . "</button>";; 
                            }else if ($row->transaction->type == "sale" || $row->transaction->type == "sell_return"){
                                $html .= "<br>" . "<button class='btn btn-modal btn-link' data-href='" .  action("SellController@show" , [trim($row->transaction->id)]) . "' data-container='.view_modal'>" . $row->transaction->invoice_no . "</button>"; 
                            }else if ($row->transaction->type == "purchase" || $row->transaction->type == "purchase_return" ){
                                $html .= "<br>" . "<button class='btn btn-modal btn-link' data-href='" .  action("PurchaseController@show" , [trim($row->transaction->id)]) . "' data-container='.view_modal'>" . $row->transaction->ref_no . "</button>";  
                            }else if ($row->transaction->type == "Stock_In"){
                                $html .=  "<br>" . "<button class='btn btn-modal btn-link' data-href='" .  action('StockTransferController@show', [trim($row->transaction->id)]) . "' data-container='.view_modal'>" . $row->transaction->ref_no . "</button>"; 
                            }else if ($row->transaction->type == "Stock_Out"){
                                $transaction = \App\Transaction::where("type","Stock_In")->where("ref_no",$row->transaction->ref_no)->first();
                                $html .= "<br>" .  "<button class='btn btn-modal btn-link' data-href='" .  action('StockTransferController@show', [trim($transaction->id)]) . "' data-container='.view_modal'>" . $row->transaction->ref_no . "</button>";; 
                            }else if ($row->transaction->type == "production_sell"){
                                $html .= "<br>" .  "<button class='btn btn-modal btn-link' data-href='" .  action('\Modules\Manufacturing\Http\Controllers\ProductionController@show', [trim($row->transaction->mfg_parent_production_purchase_id)]) . "' data-container='.view_modal'>" .__('messages.view') . "</button>";; 
                            }else{
                                $html .= "<br>" . "<button class='btn btn-modal btn-link' data-href='" .  action("PurchaseController@show" , [trim($row->transaction->id)]) . "' data-container='.view_modal'>" . $row->transaction->ref_no . "</button>";; 
                            }
                        }else{
                            $html = "--" ;
                        }
                        return $html;
                })
                ->rawColumns(['created_at','current_qty','movement','product_name'])
                ->make(true);
    }

   /**
     * Open Create Page Of Store.
     *
     * @return View
     */
    public function create()
    {
        if (!auth()->user()->can('warehouse.create') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('manufuctoring.views')&& !auth()->user()->can('admin_supervisor.views')) {
            abort(403, 'Unauthorized action.');
        }

        $mainstore_categories = [];
        $business_id          = request()->session()->get('user.business_id');
        $business_locations   = BusinessLocation::forDropdown($business_id);
        $parents              = Warehouse::parents($business_id);
        $mainstore            = Warehouse::where('status',"=",0)
                                         ->where('business_id', $business_id)
                                         ->select(['business_id',"status",'mainStore','name','id'])
                                         ->get();
        foreach($mainstore as $mainstor) { 
            $mainstore_categories[$mainstor->id] = $mainstor->name;
        }
       
        return view('warehouse.create')->with(compact('parents','business_locations','business_id','mainstore_categories'));
    }

    /**
     * Open Edit Page Of Store.
     *  
     * @return View
     */
    public function edit($id)
    {
        if (!auth()->user()->can('warehouse.create')  && !auth()->user()->can('warehouse.Edit')) {
            abort(403, 'Unauthorized action.');
        }
        $data                    =  Warehouse::find($id);
        $id_warehouse            = $id;
        $business_id             = request()->session()->get('user.business_id');
        $business_locations      = BusinessLocation::forDropdown($business_id);
        $mainstore               = Warehouse::where('business_id', $business_id)->select(['business_id','mainStore','name','id'])->get();
        $source                  = "";        
        $sourceType              = "";        
        $mainstore_categories    = [];
        if (!empty($mainstore)) {
            foreach ($mainstore as $mainStor) {
                if($mainStor->id == $id){
                    $source      = $mainStor->name;
                    $sourceType  = $mainStor->mainStore;        
                }
                if($mainStor->mainStore == 0){
                    $mainstore_categories[$mainStor->id] = $mainStor->name;
                }
            }
                   
        }
        $parents                 =  WareHouse::parents($business_id);
        return view('warehouse.edit')->with(compact('data',"id", 'mainstore','parents','business_locations','id_warehouse','business_id','mainstore_categories','sourceType', 'source'));
    }

    /**
     * Add New Store ( Main Or Sub Store).
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 

        if (!auth()->user()->can('warehouse.create') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('manufuctoring.views')&& !auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }

        try{
            $business_id = request()->session()->get('user.business_id');
            $parent      = "";
            \DB::beginTransaction();
            if($request->parent_id != null && $request->parent_id != 0){
                $old_warehouse      = Warehouse::find($request->parent_id);
                if(!empty($old_warehouse)){
                    $parent                = $old_warehouse->name;
                    $old_warehouse->status = 0;
                    $old_warehouse->update();
                }
            }
            
            $store              = new Warehouse();
            $store->name        = $request->store_name;
            $store->status      = ($request->parent_id)?1:0;
            $store->description = $request->descript ;
            $store->mainStore   = $parent;
            $store->business_id = $request->location_id;
            $store->parent_id   = $request->parent_id;
            $store->save();
            \DB::commit();
            $output = [         
                'success' =>  1,
                'msg'     => __('Store Added Successfully')
            ];

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [     
                    'success' => 0,
                    'msg'     => __('messages.something_went_wrong')
            ];
        }
      

        return redirect('warehouse/index')->with('status', $output);
    }

    /**
     * Update Information Store.
     *  
     * @return Response
     */ 
    public function update_id(Request $request,$id)
    {
        
        if (!auth()->user()->can('warehouse.create')   && !auth()->user()->can('warehouse.Edit')) {
            abort(403, 'Unauthorized action.');
        }
        try{
            \DB::beginTransaction();

            $store              = Warehouse::find($id);
            $store->name        = $request->store_name;
            $store->description = $request->descript;
            $store->status      = ($request->parent_id)?1:0;
            $store->parent_id   = $request->parent_id;
            $store->update();
            
            \DB::commit();
            $output = [
                    'success' => true,
                    'msg'     => __('Store Updated Successfully')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [     
                'success' => false,
                'msg'     => __('messages.something_went_wrong')
            ];
        }
     
        return redirect('warehouse/index')->with('status', $output);
        
    }
    /**
     * Update Information Store.
     *  
     * @return Response
     */ 
    public function update(Request $request,$id)
    {
        
        if (!auth()->user()->can('warehouse.create')   && !auth()->user()->can('warehouse.Edit')) {
            abort(403, 'Unauthorized action.');
        }
        try{
            \DB::beginTransaction();

            $store              = Warehouse::find($id);
            $store->name        = $request->store_name;
            $store->description = $request->descript;
            $store->status      = ($request->parent_id)?1:0;
            $store->parent_id   = $request->parent_id;
            $store->update();
            
            \DB::commit();
            $output = [
                    'success' => true,
                    'msg'     => __('Store Updated Successfully')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [     
                'success' => false,
                'msg'     => __('messages.something_went_wrong')
            ];
        }
     
        return redirect('warehouse/index')->with('status', $output);
        
    }

    /**
     * Delete The Store.
     *  
     * @return Response
     */ 
    public function delete($id)
    {
         
        if (!auth()->user()->can('purchase.delete')  && !auth()->user()->can('warehouse.Delete')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id        = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        if(request()->ajax()){
            try {
                    $warehouse  = \App\Models\Warehouse::find($id);
                    if(  $warehouse->status  == 0){
                        $output = [
                            'success' => false,
                            'msg' => __('lang_v1.sorry_is_main_store')
                        ];
                        return $output;
                    }
                    //.... if there is movement on this store
                    $warehouseMove = \App\MovementWarehouse::where("business_id",$business_id)->where("store_id",$id)->first();
                    if(!empty($warehouseMove)){
                        $output = [
                            'success' => false,
                            'msg' => __('lang_v1.sorry_there_is_movement')
                        ];
                        return $output;
                    }
                    //.... if this is store have product
                    $warehouseinfo = \App\Models\WarehouseInfo::where("business_id",$business_id)->where("store_id",$id)->sum("product_qty");
                    if($warehouseinfo != 0){
                        $output = [
                            'success' => false,
                            'msg' => __('lang_v1.sorry_there_is_product')
                        ];
                        return $output;
                    }
                    DB::table("warehouses")->where("business_id",$business_id)->where("id",$id)->delete();
                    $output = ['success' => true,
                    'msg' => __('lang_v1.warehouse_delete_success')
                ];
                return  $output ;
                
            } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    $output = ['success' => false,
                    'msg'   => $e->getMessage()
                ];
            }
            return  $output ;
        }
        
    }

   /**
     * Open Show Page Of Store.
     *  
     * @return View
     */
    public function show()
    {
        
    }

    /**
     * Show All Quantity Of Product iN Stores.
     *
     * @return Table Rows
     */
    public function movement()
    {
        if (!auth()->user()->can('product.view') && !auth()->user()->can('product.create') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('manufuctoring.views')&& !auth()->user()->can('admin_supervisor.views')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id        = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $business_locations->prepend(__('lang_v1.none'), 'none');
        $mainstore          = Warehouse::where('business_id', $business_id)->select(['business_id','status','mainStore','name','id'])->get();
        $movementWarehouse  = MovementWarehouse::where('business_id', $business_id)->get();
        $product            = product::where('business_id', $business_id)->get();
        $mainstore_categories = ["All"];
        if (!empty($mainstore)) {
            foreach ($mainstore as $mainStor) {
                    $mainstore_categories[$mainStor->id] = $mainStor->name;
            }
            $movement_categories = ["All"];
        }
        $product_list = [];
        $product_list[] = "All";
        if (!empty($product)) {
            foreach ($product as $pro) {
                $product_list[$pro->id] = $pro->name;
            }
        }
        if (!empty($movementWarehouse)) {
            foreach ($movementWarehouse as $movementW) {
                $status = $movementW->movement;
                if(!in_array($status,$movement_categories)){
                    $movement_categories[$movementW->movement] = $movementW->movement;
                }
            }
        }

        return view('warehouse.movement')->with(compact( 'product_list'
                                                        ,'mainstore_categories'
                                                        ,'movement_categories'
                                                        ,'business_locations'
                                                        ,'business_locations'));
    }

    /**
     * Get Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
        return view('warehouse.report');
    }
    /**
     * Get List Of old Movements.
     *
     * @return  View
     */
    public function conveyor()
    {

        if (!auth()->user()->can('warehouse.conveyor') && !auth()->user()->can('warehouse.views') ) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id              = request()->session()->get('user.business_id');
        //Check if subscribed or not, then check for users quota
        //like:repair
        $sub_type                 = request()->get('sub_type');
        $walk_in_customer         = $this->contactUtil->getWalkInCustomer($business_id);
        $business_details         = $this->businessUtil->getDetails($business_id);
        $taxes                    = TaxRate::forBusinessDropdown($business_id, true, true);
        $payment_lines[]          = $this->dummyPaymentLine;
        $register_details         = $this->cashRegisterUtil->getCurrentCashRegister(auth()->user()->id);
        $default_location         = !empty($register_details->location_id) ? BusinessLocation::findOrFail($register_details->location_id) : null;
        $business_locations       = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes            = $business_locations['attributes'];
        $business_locations       = $business_locations['locations'];
        //set first location as default locaton
        if (empty($default_location)) {
            foreach ($business_locations as $id => $name) {
                $default_location = BusinessLocation::findOrFail($id);
                break;
            }
        }
        $currency_details         = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $payment_types            = $this->productUtil->payment_types(null, true, $business_id);
        $shortcuts                = json_decode($business_details->keyboard_shortcuts, true);
        $pos_settings             = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
        $commission_agent_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commission_agent_setting == 'user') {
            $commission_agent = User::forDropdown($business_id, false);
        } elseif ($commission_agent_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id, false);
        }

        //If brands, category are enabled then send else false.
        $categories               = (request()->session()->get('business.enable_category') == 1) ? Category::catAndSubCategories($business_id) : false;
        $categories               = Category::getCategorywithlocation($business_id, $default_location->id);


        $brands                   = (request()->session()->get('business.enable_brand') == 1) ? Brands::forDropdown($business_id)
                                                                            ->prepend(__('lang_v1.all_brands'), 'all') : false;
        $change_return            = $this->dummyPaymentLine;
        $types                    = Contact::getContactTypes();
        $customer_groups          = CustomerGroup::forDropdown($business_id);
        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false, true);
        }
        //Selling Price Group Dropdown
        $price_groups            = SellingPriceGroup::forDropdown($business_id);
        $default_price_group_id  = !empty($default_location->selling_price_group_id) && array_key_exists($default_location->selling_price_group_id, $price_groups) ? $default_location->selling_price_group_id : null;
        //Types of service
        $types_of_service        = [];
        if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
            $types_of_service    = TypesOfService::forDropdown($business_id);
        }
        $shipping_statuses       = $this->transactionUtil->shipping_statuses();
        $default_datetime        = $this->businessUtil->format_date('now', true);
        $featured_products       = !empty($default_location) ? $default_location->getFeaturedProducts() : [];
        //pos screen view from module
        $pos_module_data         = $this->moduleUtil->getModuleData('get_pos_screen_view', ['sub_type' => $sub_type, 'job_sheet_id' => request()->get('job_sheet_id')]);
        $invoice_layouts         = InvoiceLayout::forDropdown($business_id);
        $invoice_schemes         = InvoiceScheme::forDropdown($business_id);
        $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
        $mainstore               = Warehouse::where('business_id', $business_id)->select(['business_id','status','name','id'])->get();

        $mainstore_categories = [];
        if (!empty($mainstore)) {
            foreach ($mainstore as $mainStor) {
                if($mainStor->status == 1){
                    $mainstore_categories[$mainStor->id] = $mainStor->name;
                }
            }
        }
        
        return view('warehouse.conveyor')
            ->with(compact(
                'business_locations',
                'bl_attributes',
                'business_details',
                'taxes',
                'mainstore_categories',
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
     * Check If There Are Quantity in Store.
     * input store id & product id 
     * @return CurrentStock
     */
    public function check($store,$product)
    {   
        $warehouseInfo_id    = \App\Models\WarehouseInfo::where("product_id",$product)->where("store_id",intVal($store))->sum("product_qty");
        $product_list["qty"] =  $warehouseInfo_id;
        return   $product_list ;
    }
    
    /**
     * Reset All Stores Stock.
     * From all Transactions 
     * @return Response
     */
    public function zero_qty()
    {
        //.......... check business id
        $business_id = request()->session()->get("user.business_id");
        // ....... empty every thing
        $result = \App\Models\WarehouseInfo::zero_qty($business_id);
        //....... finish
        return $result;

    }


}
