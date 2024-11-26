<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLocation;
use DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;
use App\Models\Warehouse;
use App\Transaction;
use App\Product;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\PurchaseLine;
use App\TaxRate;
use App\CustomerGroup;
use App\MovementWarehouse;
use App\Models\RecievedPrevious;
use App\VariationLocationDetails;
use App\TransactionSellLine;
use App\Unit;

class RecievedPageController extends Controller
{


     /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil,  ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
        'is_return' => 0, 'transaction_no' => ''];
    }

   /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('SalesMan.views') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('admin_without.views') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('manufuctoring.views')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $taxes = TaxRate::where('business_id', $business_id)
                        ->ExcludeForTaxGroup()
                        ->get();

        $Transactoin = Transaction::select()->get();
        $Product_names = Product::select()->get();

        $Purchaseline = PurchaseLine::select()->get();
        $recieved_previous = RecievedPrevious::select()->get();
        $notRecieved = [];
        $counter = 1;
        $status = "notRecieved";
        foreach($Transactoin as $Trans){
            $remain = 0;
            foreach($Purchaseline as $purchase){
                if($purchase->transaction_id == $Trans->id){
                    if($Trans->status != "recieved"){
                        foreach($recieved_previous as $RecievedP){
                            if($RecievedP->transaction_id == $Trans->id){
                                foreach($Product_names as $Product_name){
                                    if($purchase->product_id == $Product_name->id){
                                        $row_material = [] ;
                                        $row_material["transaction"] = $RecievedP->transaction_id ;
                                        $row_material["pro_name"] =  $Product_name->id;
                                        $row_material["remain"] = $RecievedP->remain_qty ;
                                        $remain = $row_material["remain"] ;
                                    
                                        if($remain <= 0  ){
                                            $status  =  "recieved";
                                        }else{
                                            $status  =  "notRecieved";
                                        }
                                        
                                    }
                                    
                                }
                            }
                            $notRecieved[$Trans->id] = $status;}  
                            $notRecieved["remain".$Trans->id] = $remain;  
                            // dd($remain);
                            
                            
                    }
                }
                
            }
            
        }
            
        
        
        $Purchaseline_list = PurchaseLine::whereIn("id",$notRecieved)->select()->get();

        // dd($notRecieved);

        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
       
        $mainstore = Warehouse::where('business_id', $business_id)->select(['name','id','status','mainStore','description'])->get();
        $Warehouse = Warehouse::where('business_id', $business_id)->get();

        $mainstore_categories = [];

        if (!empty($mainstore)) {
            foreach ($mainstore as $mainstor) {
                // $mainstore_categories[$mainstor->id] = $mainstor->name;
                // dd($mainstor);
                if($mainstor->status != 0){
                    $mainstore_categories[$mainstor->id] = $mainstor->name;

                }
            }
                   
        }
        $bl_attributes = $business_locations['attributes'];

        $business_locations = $business_locations['locations'];

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

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

        $Warehouse_list = [];
        foreach($Warehouse as $Ware){
            $Warehouse_list[$Ware->id] = $Ware->name;
            
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;

        $payment_types = $this->productUtil->payment_types(null, true);

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        return view('recieved.create')
            ->with(compact('taxes', 'orderStatuses', 'Purchaseline_list' , 'Warehouse_list','business_locations','mainstore_categories', 'currency_details', 'default_purchase_status', 'customer_groups', 'types', 'shortcuts', 'payment_line', 'payment_types', 'accounts', 'bl_attributes'));
    }
    /* Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
       if (!auth()->user()->can('warehouse.create') && !auth()->user()->can('SalesMan.views') && !auth()->user()->can('warehouse.views') && !auth()->user()->can('admin_without.views')&& !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('manufuctoring.views') ) {
           abort(403, 'Unauthorized action.');
       }

       $business_id = request()->session()->get('user.business_id');

       $business_locations = BusinessLocation::forDropdown($business_id);

       $mainstore = Warehouse::where('business_id', $business_id)->select(['business_id','status','name','id'])->get();
       $Product = Product::where('business_id', $business_id)->get();
       // $mainstoreall = Warehouse::select('name')->where('business_id', $business_id)->get();

       // if(!empty($mainstore[0]->name)){
           //     dd($mainstore[0]->name);
           // }else{
               //     dd("no");
               // }
               
       $mainstore_categories = [""];
       if (!empty($mainstore)) {
           foreach ($mainstore as $mainstor) {
                if($mainstor->status != 0){
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
       return view('recieved.index')->with(compact('business_locations','Product_list','business_id','mainstore_categories'));
   }
    /* Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function edit()
   {
       if (!auth()->user()->can('warehouse.create') && !auth()->user()->can('SalesMan.views')  && !auth()->user()->can('warehouse.views')  && !auth()->user()->can('admin_without.views')&& !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('manufuctoring.views')) {
           abort(403, 'Unauthorized action.');
       }

       $business_id = request()->session()->get('user.business_id');

       $business_locations = BusinessLocation::forDropdown($business_id);

       $mainstore = Warehouse::where('business_id', $business_id)->select(['business_id','status','name','id'])->get();
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
               if($mainstor->status != 0){
                   $mainstore_categories[$mainstor->id] = $mainstor->name;
               }
           }
                  
       }
       return view('recieved.edit')->with(compact('business_locations','business_id','mainstore_categories'));
   }
    /* Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function show()
   {
       if (!auth()->user()->can('warehouse.create') && !auth()->user()->can('SalesMan.views') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('admin_without.views')&& !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('manufuctoring.views') ) {
           abort(403, 'Unauthorized action.');
       }

       $business_id = request()->session()->get('user.business_id');

       $business_locations = BusinessLocation::forDropdown($business_id);

       $mainstore = Warehouse::where('business_id', $business_id)->select(['business_id','status','name','id'])->get();
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
               if($mainstor->status != 0){
                   $mainstore_categories[$mainstor->id] = $mainstor->name;
               }
           }
                  
       }
       return view('recieved.show')->with(compact('business_locations','business_id','mainstore_categories'));
   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allStores(Request $request)
    {
        if (!auth()->user()->can('allStores')  && !auth()->user()->can('SalesMan.views') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views')&& !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('admin_without.views') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id           = request()->session()->get('user.business_id');
        $name                  = (!empty($_GET["name"])) ? ($_GET["name"]) : ('');
        $type                  = (!empty($_GET["type"])) ? ($_GET["type"]) : ('');
        $recieved_previous     = RecievedPrevious::where("business_id",$business_id);
        if($name != "") { $recieved_previous->where("store_id",$name); }
        if($type != "") { $recieved_previous->where("product_name",$type); }
        $recieved_previous->get();
        return Datatables::of($recieved_previous)->addColumn('action', 
                function ($row) {
                $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                        </button> 
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                if (auth()->user()->can("purchase.view") || auth()->user()->can("warehouse.views")  ||  auth()->user()->can('admin_without.views')  || auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('manufuctoring.views')) {
                    $html .= '<li><a href="#" data-href="' . action('TransactionPaymentController@viewRecieve', [$row->TrRecieved->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                }
                if (auth()->user()->can("purchase.update") || auth()->user()->can("warehouse.Edit")  ||    auth()->user()->can('admin_without.Edit')  || auth()->user()->can('admin_supervisor.Edit') && !auth()->user()->can('manufuctoring.Edit') ) {
                    $html .= '<li><a href="' . action('PurchaseController@update_recieved', ["id" => $row->transaction_id,"trn"=>$row->TrRecieved->id]) . '"><i class="fas fa-edit"></i>' . __("messages.edit") . '</a></li>';
                }
                $html .=  '</ul></div>';
                return $html;
            })->editColumn('store_id',    function ($row) {
                return $row->store->name;
            })->editColumn('unit_id',     function ($row) {
                return $row->unit->actual_name;
            })->editColumn('business_id', function ($row) {
                $name   = \App\BusinessLocation::where("business_id",$row->business_id)->first();
                if(!empty($name)){
                   $nam =  $name->name;
                }else{
                   $nam = " -- ";
                }
                return $nam;
            })->editColumn('remain_qty',  function ($row) {
                $remain = $row->total_qty - $row->current_qty;
                return $remain;
             })->addColumn('reference_po', function ($row) {
                $html = '<button type="button" data-href="' . action('PurchaseController@show', [$row->transaction->id])
                        . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->transaction->ref_no . '</button>';
                return $html;
             })->editColumn('created_at', function ($row) {
                $remain = date_format($row->created_at,"Y-m-d h:i:s a")  ;
                return $remain;
            })
            ->rawColumns(['action','reference_po',  'remain_qty','business_id','created_at'])->make(true);
    }

   
}
