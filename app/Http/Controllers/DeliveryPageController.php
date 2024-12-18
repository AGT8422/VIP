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
use App\TransactionSellLine;
use App\TypesOfService;
use App\Models\TransactionDelivery;
use App\Models\WarehouseInfo;
use App\Models\RecievedPrevious;
use App\User;
use App\InvoiceScheme;
use App\Utils\BusinessUtil;
use App\Utils\CashRegisterUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\InvoiceLayout;
use App\Warranty;
use App\Models\Warehouse;

use Illuminate\Http\Request;
use DB;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;
use App\PurchaseLine;
use App\Models\DeliveredPrevious;
use App\MovementWarehouse;
use App\VariationLocationDetails;
use App\Unit;
use App\Models\DeliveredWrong;

class DeliveryPageController extends Controller
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
    
    /* Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create(Request $request)
   {
     
    $business_id = request()->session()->get('user.business_id');

    if (!(auth()->user()->can('superadmin') || auth()->user()->can('warehouse.views') || auth()->user()->can('sell.create') || auth()->user()->can('SalesMan.views') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('admin_supervisor.create')&& auth()->user()->can('manufuctoring.views')&& auth()->user()->can('warehouse.views')))) {
        abort(403, 'Unauthorized action.');
    }
 
    $sub_type                        = request()->get('sub_type');
    
    $walk_in_customer                = $this->contactUtil->getWalkInCustomer($business_id);
    $business_details                = $this->businessUtil->getDetails($business_id);
    
    $taxes                           = TaxRate::forBusinessDropdown($business_id, true, true);
   
    $business_locations              = BusinessLocation::forDropdown($business_id, false, true);
    $bl_attributes                   = $business_locations['attributes'];
    $business_locations              = $business_locations['locations'];

    $transaction                     = Transaction::find($request["id"]);

     
    if(!empty($transaction)){
        $transaction_child    = \App\Transaction::where("separate_parent",$transaction->id)->where("separate_type","partial")->get();
        $TransactionSellLine  = TransactionSellLine::where('transaction_id', $request["id"])->get();
        $TrSeLine_            = TransactionSellLine::where('transaction_id', $request["id"])->sum("quantity");
        $TrSeLine             = intval($TrSeLine_);
        if(count($transaction_child)>0){
            $transaction_delivery = [];$RecievedPrevious = [];$DelWrong=0;$DelPrevious=0;
            foreach($transaction_child as $one){
                $transaction_delivery[]  = TransactionDelivery::where('transaction_id', $one->id)->get();
                $DeliveredPrevious[]      = DeliveredPrevious::where('transaction_id', $one->id)->get();
                $DelPrevious             += DeliveredPrevious::where('transaction_id', $one->id)->sum("current_qty");
                $DeliveredWrong[]        = DeliveredWrong::where('transaction_id', $one->id)->get();
                $DelWrong                += DeliveredWrong::where('transaction_id', $one->id)->sum("current_qty");
            }
            $location_id                     = !empty($transaction->location_id) ? $transaction->location_id : null;
            $totals                          = $DelPrevious + $DelWrong ;
                    
            $product_list_all                = [];
            foreach($TransactionSellLine as $trl){
                $product_list_all[] = $trl->product_id;
            }
            $array     = [] ;
            $business  = \App\Business::find($business_id);
            $childs    = \App\Models\Warehouse::childs($business_id);
             
            return view('delivery.create')->with(compact('business_locations',
                        'bl_attributes',
                        'business_details',
                        'childs',
                        'taxes',
                        'TrSeLine',
                        'array',
                        'transaction',
                        'transaction_child',
                        'product_list_all',
                        'DeliveredPrevious',
                        'DelPrevious',
                        'DeliveredWrong',
                        'business_locations',
                        'DelWrong',
                        'business',
                        'TransactionSellLine',
                        'walk_in_customer',
                        'sub_type',
                        ));
             
             
             
        }else{

            
            $tr                              = \App\Transaction::where("id",$transaction->return_parent_id)->first();
            if(!empty($tr)){
                $TransactionSellLine_return  = TransactionSellLine::where('transaction_id', $tr->id)->get();   
                $TrSeLine_return             = TransactionSellLine::where('transaction_id', $tr->id)->sum("quantity_returned");
            }

            $DeliveredPrevious               = DeliveredPrevious::where('transaction_id', $request["id"])->get();
            $DelPrevious                     = DeliveredPrevious::where('transaction_id', $request["id"])->sum("current_qty");
                    
            $DeliveredWrong                  = DeliveredWrong::where('transaction_id', $request["id"])->get();
            $DelWrong                        = DeliveredWrong::where('transaction_id', $request["id"])->sum("current_qty");
                    
            $totals                          = $DelPrevious + $DelWrong ;
                    
            $product_list_all                = [];
            foreach($TransactionSellLine as $trl){
                $product_list_all[] = $trl->product_id;
            }
            $array     = [] ;
            $business  = \App\Business::find($business_id);
            $childs    = \App\Models\Warehouse::childs($business_id);
            if(!empty($tr)){
                $product_list_all_return     = [];
                foreach($TransactionSellLine_return as $trl){
                    $product_list_all_return[] = $trl->product_id;
                }
                return view('delivery.create')->with(compact('business_locations',
                                'bl_attributes',
                                'business_details',
                                'childs',
                                'taxes',
                                'TrSeLine',
                                'array',
                                'transaction',
                                'product_list_all',
                                'product_list_all_return',
                                'TrSeLine_return',
                                'DeliveredPrevious',
                                'DelPrevious',
                                'DeliveredWrong',
                                'business_locations',
                                'DelWrong',
                                'business',
                                'TransactionSellLine',
                                'walk_in_customer',
                                'sub_type',
                                ));
            }else{
                return view('delivery.create')->with(compact('business_locations',
                            'bl_attributes',
                            'business_details',
                            'childs',
                            'taxes',
                            'TrSeLine',
                            'array',
                            'transaction',
                            'product_list_all',
                            'DeliveredPrevious',
                            'DelPrevious',
                            'DeliveredWrong',
                            'business_locations',
                            'DelWrong',
                            'business',
                            'TransactionSellLine',
                            'walk_in_customer',
                            'sub_type',
                            ));
            }
        }
    }
    
   }
    /* Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function edit_delivery(Request $request,$id)
   {
     
    $business_id = request()->session()->get('user.business_id');

    if (!(auth()->user()->can('superadmin') || auth()->user()->can('warehouse.views') || auth()->user()->can('admin_supervisor.create') || auth()->user()->can('manufuctoring.views') ||   auth()->user()->can('warehouse.views') || auth()->user()->can('sell.create') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair.create')))) {
        abort(403, 'Unauthorized action.');
    }
 
    $sub_type = request()->get('sub_type');
    
    $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

    $business_details = $this->businessUtil->getDetails($business_id);
    
    $taxes = TaxRate::forBusinessDropdown($business_id, true, true);
   
    $business_locations   = BusinessLocation::forDropdown($business_id, false, true);
    $bl_attributes        = $business_locations['attributes'];
    $business_locations   = $business_locations['locations'];

    $TransactionDelivery  = TransactionDelivery::find($id);
    $transaction          = Transaction::find($TransactionDelivery->transaction_id);


    $TransactionSellLine  = TransactionSellLine::where('transaction_id', $transaction->id)->get();   
    $TrSeLine_            = TransactionSellLine::where('transaction_id', $transaction->id)->sum("quantity");
    
    $TrSeLine             = floatval($TrSeLine_);
    
    $tr                          = \App\Transaction::where("id",$transaction->return_parent_id)->first();
    if(!empty($tr)){
        $TransactionSellLine_return  = TransactionSellLine::where('transaction_id', $tr->id)->get();   
        $TrSeLine_             = TransactionSellLine::where('transaction_id', $tr->id)->sum("quantity_returned");
        $TrSeLine_return       = floatval($TrSeLine_);
    }
    
    
    $DeliveredPrevious    = DeliveredPrevious::where('transaction_id', $transaction->id)->where("transaction_recieveds_id",$id)->get();
    $DelPrevious          = DeliveredPrevious::where('transaction_id', $transaction->id)->sum("current_qty");
   
    $DeliveredWrong       = DeliveredWrong::where('transaction_id', $transaction->id)->where("transaction_recieveds_id",$id)->get();
    $DelWrong             = DeliveredWrong::where('transaction_id', $transaction->id)->sum("current_qty");

    $totals = $DelPrevious + $DelWrong ;

    $product_list_all     = [];
    foreach($TransactionSellLine as $trl){
        $product_list_all[] = $trl->product_id;
    }
    $array_remain = []; 
    $childs = \App\Models\Warehouse::childs($business_id);
    if(!empty($tr)){
        $product_list_all_return     = [];
        foreach($TransactionSellLine_return as $trl){
            $product_list_all_return[] = $trl->product_id;
        }
            return view('delivery.edit_delivery')->with(compact('business_locations',
                                    'bl_attributes',
                                    'business_details',
                                    'childs',
                                    'taxes',
                                    'array_remain',
                                    'TrSeLine',
                                    'transaction',
                                    'product_list_all',
                                    'product_list_all_return',
                                    'TrSeLine_return',
                                    'DeliveredPrevious',
                                    'DelPrevious',
                                    'DeliveredWrong',
                                    'business_locations',
                                    'DelWrong',
                                    'TransactionSellLine',
                                    'walk_in_customer',
                                    'sub_type',
                                    ));   
        }else{
                return view('delivery.edit_delivery')->with(compact('business_locations',
                                    'bl_attributes',
                                    'business_details',
                                    'childs',
                                    'taxes',
                                    'array_remain',
                                    'TrSeLine',
                                    'transaction',
                                    'product_list_all',
                                    'DeliveredPrevious',
                                    'DelPrevious',
                                    'DeliveredWrong',
                                    'business_locations',
                                    'DelWrong',
                                    'TransactionSellLine',
                                    'walk_in_customer',
                                    'sub_type',
                                    ));
        }
    }
                /* Show the form for creating a new resource.
                *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
       if (!auth()->user()->can('warehouse.create') &&  !auth()->user()->can('SalesMan.views') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('admin_without.views') && !auth()->user()->can('manufuctoring.views')  ) {
           abort(403, 'Unauthorized action.');
       }

       $business_id = request()->session()->get('user.business_id');

       $business_locations = BusinessLocation::forDropdown($business_id);

       $mainstore = Warehouse::where('business_id', $business_id)->select(['business_id','mainStore','name','id'])->get();
       // $mainstoreall = Warehouse::select('name')->where('business_id', $business_id)->get();
       $Product = Product::where('business_id', $business_id)->get();

       // if(!empty($mainstore[0]->name)){
           //     dd($mainstore[0]->name);
           // }else{
               //     dd("no");
               // }
        $mainstore_categories = [""];
       if (!empty($mainstore)) {
           foreach ($mainstore as $mainstor) {
               // dd($mainstor->mainStore);
               if($mainstor->mainStore == 0){
                   $mainstore_categories[$mainstor->id] = $mainstor->name;
               }
           }
                  
       }
       $Product_list = [""];
       if (!empty($Product)) {
           foreach ($Product as $Pro) {
                   $Product_list[$Pro->name] = $Pro->name;
             
           }
                  
       }
       return view('delivery.index')->with(compact('business_locations','Product_list','business_id','mainstore_categories'));
   }
    /* Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function edit()
   {
       if (!auth()->user()->can('warehouse.create')  &&  !auth()->user()->can('SalesMan.views') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('admin_without.views') && !auth()->user()->can('manufuctoring.views') ) {
           abort(403, 'Unauthorized action.');
       }

       $business_id = request()->session()->get('user.business_id');

       $business_locations = BusinessLocation::forDropdown($business_id);

       $mainstore = Warehouse::where('business_id', $business_id)->select(['business_id','mainStore','name','id'])->get();
       // $mainstoreall = Warehouse::select('name')->where('business_id', $business_id)->get();

       // if(!empty($mainstore[0]->name)){
           //     dd($mainstore[0]->name);
           // }else{
               //     dd("no");
               // }
               
       $mainstore_categories = [];
       if (!empty($mainstore)) {
           foreach ($mainstore as $mainstor) {
               // dd($mainstor->mainStore);
               if($mainstor->mainStore == 0){
                   $mainstore_categories[$mainstor->id] = $mainstor->name;
               }
           }
                  
       }
       return view('delivery.edit')->with(compact('business_locations','business_id','mainstore_categories'));
   }
    /* Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function show()
   {
       if (!auth()->user()->can('warehouse.create')  &&  !auth()->user()->can('SalesMan.views')  && !auth()->user()->can('warehouse.views')  && !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('admin_without.views') && !auth()->user()->can('manufuctoring.views') ) {
           abort(403, 'Unauthorized action.');
       }

       $business_id = request()->session()->get('user.business_id');

       $business_locations = BusinessLocation::forDropdown($business_id);

       $mainstore = Warehouse::where('business_id', $business_id)->select(['business_id','mainStore','name','id'])->get();
       // $mainstoreall = Warehouse::select('name')->where('business_id', $business_id)->get();

       // if(!empty($mainstore[0]->name)){
           //     dd($mainstore[0]->name);
           // }else{
               //     dd("no");
               // }
               
       $mainstore_categories = [];
       if (!empty($mainstore)) {
           foreach ($mainstore as $mainstor) {
               // dd($mainstor->mainStore);
               if($mainstor->mainStore == 0){
                   $mainstore_categories[$mainstor->id] = $mainstor->name;
               }
           }
                  
       }
       return view('delivery.show')->with(compact('business_locations','business_id','mainstore_categories'));
   }

   /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allStores(Request $request)
    {
        if (!auth()->user()->can('allStores')   &&  !auth()->user()->can('SalesMan.views') && !auth()->user()->can('warehouse.views')  && !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('admin_without.views') && !auth()->user()->can('manufuctoring.views') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id           = request()->session()->get('user.business_id');
        $name                  = (!empty($_GET["name"])) ? ($_GET["name"]) : ('');
        $type                  = (!empty($_GET["type"])) ? ($_GET["type"]) : ('');
        $DeliveredPrevious     = DeliveredPrevious::where("business_id",$business_id);
        if($name != "") { $DeliveredPrevious->where("store_id",$name); }
        if($type != "") { $DeliveredPrevious->where("product_name",$type); }
        $DeliveredPrevious->get();
        
        return Datatables::of($DeliveredPrevious)->addColumn('action',
                function ($row) {
                $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                        </button> 
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                  if($row->T_delivered == null){
                      dd($row);
                  }
                if (auth()->user()->can("purchase.view") ||  auth()->user()->can('warehouse.views') ||  auth()->user()->can('admin_without.views') ||  auth()->user()->can('admin_supervisor.views') &&  !auth()->user()->can('manufuctoring.views')) {
                    $html .= '<li><a href="#" data-href="' . action('TransactionPaymentController@viewDelivered', [($row->T_delivered)?$row->T_delivered->id:null]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                }
                if (auth()->user()->can("purchase.update") ||  auth()->user()->can('warehouse.Edit')||  auth()->user()->can('admin_without.Edit') ||  auth()->user()->can('admin_supervisor.Edit') &&  !auth()->user()->can('manufuctoring.Edit')) {
                    $html .= '<li><a href="' . action('DeliveryPageController@edit_delivery', ["id"  => ($row->T_delivered)?$row->T_delivered->id:null]) . '"><i class="fas fa-edit"></i>' . __("messages.edit") . '</a></li>';
                }
                $html .=  '</ul></div>';
                return $html;
            })->editColumn('store_id',    function ($row) {
                  
                 
                return $row->store->name;
            })->editColumn('unit_id',     function ($row) {
                return ($row->unit)?$row->unit->actual_name:"";
            })->editColumn('created_at',  function ($row) {
                $date  = date_format($row->created_at,"Y-m-d h:i:s a");
                return $date;
            })->editColumn('business_id', function ($row) {
                $name   = \App\BusinessLocation::where("business_id",$row->business_id)->first();
                if(!empty($name)){
                   $nam =  $name->name;
                }else{
                   $nam = " -- ";
                }
                return $nam;
            })->rawColumns(['action','created_at'])->make(true);
    }
   

}
