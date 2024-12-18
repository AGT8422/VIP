<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Warranty;
use Yajra\DataTables\Facades\DataTables;

use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Models\ArchiveTransaction;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ArchiveTransactionController extends Controller
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
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil,   ModuleUtil $moduleUtil,   BusinessUtil $businessUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
         $this->moduleUtil = $moduleUtil;
         $this->businessUtil = $businessUtil;
         $this->shipping_status_colors = [
            'ordered' => 'bg-yellow',
            'packed' => 'bg-info',
            'shipped' => 'bg-navy',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
        ];
        
    }
     public function warranties(Request $request)
     {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $warranties = Warranty::where('business_id', $business_id)  
                         ->select(['id' , 'user_id', 'name', 'description', 'duration', 'duration_type','parent_id','state_action','ref_number']);

            return Datatables::of($warranties)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'ArchiveTransactionController@warranties_view\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal"><i class="fa fas fa-eye"></i> @lang("messages.view")</button>'
                 )
                ->editColumn('duration', function ($row) {
                     return $row->duration . ' ' . __('lang_v1.' .$row->duration_type);
                 })
                ->editColumn('user_id', function ($row) {
                    $users  = "";
             
                    $us        = \App\User::find($row->user_id);
                    if(!empty($us)){
                        $users = $us->first_name;
                    }
                    return  $users;
                  })
                 ->rawColumns(['action'])
                 ->make(true);
        }
        $type = "archive";
        
        return view('warranties.index')->with(compact("type"));
     }

     public function warranties_view($id)
     {  
        $main_source = Warranty::find($id);
        $allData     = Warranty::get();
         return view("warranties.log_file.view")->with(compact("main_source","allData"));
     }

     public function users_activations(Request $request)
     {
        $business_id = request()->session()->get('user.business_id');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        if(request()->ajax()){
            $users = Activity::select();
            if(request()->user_id){
               
                $id =  request()->user_id;
                $users->where("subject_id",$id);
            }
             
            if(request()->state){
                $desc =  request()->state;
                $users->where("description",$desc);
            }
            $users->get();
            return DataTables::of($users)
                    ->addColumn("id",function($row){
                        return $row->id;    
                    }) 
                    ->addColumn("name",function($row){
                        $users  = "";
             
                        $us        = \App\User::find($row->subject_id);
                        if(!empty($us)){
                             $users = $us->first_name;
                        }
                    return  $users;
                     }) 
                    ->addColumn("state",function($row){
                        return $row->description;    
                    }) 
                    ->addColumn("ref_no",function($row){
                        return $row->ref_number; 
                    }) 
                    ->addColumn("date",function($row){
                        return $row->created_at;   
                    }) 
                    ->rawColumns(['id','name','state','ref_no','date'])
                    ->make(true)  ;          
        }
        $uselist = \App\User::where("business_id",$business_id)->get();
        $users = [] ;
        foreach($uselist as $it){
            $users [$it->id] = $it->first_name  ;
        }
         return view("log_file.users_log")->with(compact("currency_details","users"));
     }

     public function transaction_activations(Type $var = null)
     {
        $business_id = request()->session()->get('user.business_id');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        if(request()->ajax()){
            $transaction = \App\Models\ArchiveTransaction::orderBy("id","desc")->select();
            if(request()->status){
               $type = request()->status;
               $transaction->where("type",$type);
            }
 
            $transaction->get();
            return DataTables::of($transaction)
                ->addColumn(
                    'action',
                    function ($row)  { 
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        
                        $html .= '<li><a   href="' . action("ArchiveTransactionController@show_transaction", [$row->id,"last"=>"last"]) . '"   ><i class="fas fa-eye" aria-hidden="true"></i> ' . __("home.last bill") . '</a></li>';
                        $html .= '<li><a   href="' . action("ArchiveTransactionController@show_transaction", [$row->id]) . '"   ><i class="fas fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                        $html .= '<li><a   href="' . action("ArchiveTransactionController@show_main", [$row->new_id,"bill_id"=>$row->id]) . '"   ><i class="fas fa-file" aria-hidden="true"></i> ' . __("home.main bill") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    })
                    ->addColumn("last_transaction",function($row){
                        $tr = \App\Transaction::find($row->new_id);
                        if($tr->type == "sale"){
                            $reference = $tr->invoice_no;
                        }else{
                            $reference = $tr->ref_no;
                        }
                        return $reference;    
                    }) 
                    ->addColumn("store",function($row){
                        
                        return $row->storex->name;    
                    }) 
                    ->addColumn("type",function($row){
                        return $row->type;    
                    }) 
                    ->addColumn("project_no",function($row){
                        return ($row->project_no)?$row->project_no:"";    
                    }) 
                    ->addColumn("status",function($row){
                        return $row->status;    
                    }) 
                    ->addColumn("payment_status",function($row){
                        return $row->payment_status;    
                    }) 
                    ->addColumn("contact_id",function($row){
                         return ($row->contact)?$row->contact->first_name:"";    
                    }) 
                    ->addColumn("invoice_no",function($row){
                         if($row->type == "sale"){
                            $reference = $row->invoice_no;
                        }else{
                            $reference = $row->ref_no;
                        }
                        return $reference;    
                    }) 
                    ->addColumn("transaction_date",function($row){
                        return $row->transaction_date;    
                    }) 
                    ->addColumn("total_before_tax",function($row){
                        return $row->total_before_tax;    
                    }) 
                    ->addColumn("final_total",function($row){
                        return $row->final_total;    
                    }) 
                    ->addColumn("agent_id",function($row){
                        $users  = "";
             
                        $us        = \App\User::find($row->agent_id);
                        if(!empty($us)){
                             $users = $us->first_name;
                        }
                        return  $users;  
                    }) 
                    ->addColumn("user",function($row){
                        $users  = "";
             
                        $us        = \App\User::find($row->created_by);
                        if(!empty($us)){
                             $users = $us->first_name;
                        }
                        return  $users;  
                    }) 
                    ->addColumn("sup_refe",function($row){
                        return $row->sup_refe;    
                    }) 
                    ->addColumn("cost_center_id",function($row){
                        return ($row->cost_center)?$row->cost_center->name:"";    
                    }) 
                    ->addColumn("pattern_id",function($row){
                      
                        return ($row->pattern)?$row->pattern->name:"";    
                    }) 
                    ->addColumn("ref_number",function($row){
                        return $row->ref_number; 
                    }) 
                    ->addColumn("date",function($row){
                        return $row->created_at;   
                    }) 
                    ->rawColumns(['action','project_no','user' ,'last_transaction','store','type','status','payment_status','contact_id','invoice_no','transaction_date','total_before_tax','final_total','agent_id','sup_refe','cost_center_id','pattern_id','ref_number','date'])
                    ->make(true)  ;          
        }
        $uselist = \App\User::where("business_id",$business_id)->get();
        $users = [] ;
        foreach($uselist as $it){
            $users [$it->id] = $it->first_name  ;
        }

        $status_filter = [
            "purchase" => "Purchase",
            "sale" => "Sale",
        ];



        return view("log_file.transactions")->with(compact("status_filter"));
     }

     public function show_transaction($id)
     {
                $business_id = request()->session()->get('user.business_id');
                $tr = \App\Models\ArchiveTransaction::find($id);
                $bill_id = $id;
                if($tr){
                    if($tr->type == "purchase"){
                            $last  = null;
                            $taxes = \App\TaxRate::where('business_id', $business_id)
                                    ->pluck('name', 'id');
                            $taxess = \App\TaxRate::where('business_id', $business_id)->get();
                            $array = [];
                            foreach($taxess as $tvo){
                                $tx =  $tvo->toArray();
                                $array[$tx["id"]] = $tx["amount"] ;
                                
                            }
                            $arrays = array_keys($taxess->toArray());
                            $purchcaseline = \App\Models\ArchivePurchaseLine::where('transaction_id', $id)
                                    ->get();
                            $purchase = \App\Models\ArchiveTransaction::where('business_id', $business_id)
                                    ->where('id', $id)
                                    ->with(
                                        'contact',
                                        'purchase_lines',
                                        'purchase_lines.product',
                                        'purchase_lines.product.unit',
                                        'purchase_lines.variations',
                                        'purchase_lines.variations.product_variation',
                                        'purchase_lines.sub_unit',
                                        'location',
                                        'payment_lines',
                                        'tax'
                                        )
                                        ->firstOrFail();
                            
                            $purchase_act = \App\Models\ArchiveTransaction::where('business_id', $business_id)
                                    ->where('id', $tr->parent_id)
                                    ->with(
                                        'contact',
                                        'purchase_lines',
                                        'purchase_lines.product',
                                        'purchase_lines.product.unit',
                                        'purchase_lines.variations',
                                        'purchase_lines.variations.product_variation',
                                        'purchase_lines.sub_unit',
                                        'location',
                                        'payment_lines',
                                        'tax'
                                        )
                                        ->firstOrFail();
                                        
                            $purchase1 = \App\Models\ArchiveTransaction::where('business_id', $business_id)
                                        ->where('id', $id)
                                        ->select("store")
                                        ->with(
                                            'contact',
                                            'purchase_lines',
                                            'purchase_lines.product',
                                            'purchase_lines.product.unit',
                                            'purchase_lines.variations',
                                            'purchase_lines.variations.product_variation',
                                            'purchase_lines.sub_unit',
                                            'location',
                                            'payment_lines',
                                            'tax'
                                            )
                                            ->firstOrFail();
                                            
                                            if (!empty($purchase1)) {

                                                $store = "";
                                                $business_id = request()->session()->get('user.business_id');
                                                
                                                $warehouses = \App\Models\Warehouse::where("business_id",$business_id)->select(["description","status","mainStore","id","name"])->get();
                                                
                                                if (!empty($warehouses)) {
                                                    foreach ($warehouses as $warehouse) {
                                                        if($purchase1->store == $warehouse->id){
                                                            // dd($warehouse->id . " - - " . $transaction->store);
                                                            $store = $warehouse->name;
                                                        }
                                                    }
                                                    
                                                }
                                            }
                            foreach ($purchase->purchase_lines as $key => $value) {
                                if (!empty($value->sub_unit_id)) {
                                    $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                                    $purchase->purchase_lines[$key] = $formated_purchase_line;
                                }
                            }

                            if(app("request")->input("last")){
                                $purchase     =   \App\Transaction::find($tr->new_id);
                                $new_purchase =   \App\Models\ArchiveTransaction::orderby("id","desc")->where("new_id",$tr->new_id)->where('business_id', $business_id)
                                                                                                ->with(
                                                                                                    'contact',
                                                                                                    'purchase_lines',
                                                                                                    'purchase_lines.product',
                                                                                                    'purchase_lines.product.unit',
                                                                                                    'purchase_lines.variations',
                                                                                                    'purchase_lines.variations.product_variation',
                                                                                                    'purchase_lines.sub_unit',
                                                                                                    'location',
                                                                                                    'payment_lines',
                                                                                                    'tax'
                                                                                                    )
                                                                                                    ->firstOrFail();
                              $last = "last";
                            }else{
                                $purchase     = $purchase;
                                $new_purchase = $purchase_act;
                            }

                            $payment_methods = $this->productUtil->payment_types($purchase->location_id, true);
                            $purchase_taxes = [];
                            if (!empty($purchase->tax)) {
                                if ($purchase->tax->is_tax_group) {
                                    $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
                                } else {
                                    $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
                                }
                            }
                            $new_purchase_taxes = [];
                            if (!empty($new_purchase->tax)) {
                                if ($new_purchase->tax->is_tax_group) {
                                    $new_purchase = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase_act->tax, $purchase_act->tax_amount));
                                } else {
                                    $new_purchase_taxes[$new_purchase->tax->name] = $new_purchase->tax_amount;
                                }
                            }
                            $activities = Activity::forSubject($new_purchase)
                                    ->with(['causer', 'subject'])
                                    ->latest()
                                    ->get();
                        
                            $statuses = $this->productUtil->orderStatuses();
                            
                            $RecievedWrong    = \App\Models\RecievedWrong::where('transaction_id', $tr->new_id)->get();
                            $RecievedPrevious = \App\Models\RecievedPrevious::where('transaction_id',$tr->new_id)->get();
                            $product = \App\Product::where('business_id', $business_id)->get();
                            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
                            $unit = \App\Unit::where('business_id', $business_id)->get();
                            $business_locations = \App\BusinessLocation::forDropdown($business_id);
                            $Warehouse = \App\Models\Warehouse::where('business_id', $business_id) ->get();
                            $product_list = [];
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
                            
                            $additional   = \App\Models\AdditionalShipping::where("transaction_id",$tr->new_id)->first();
                            $new_payment  = \App\TransactionPayment::where("transaction_id",$tr->new_id)->get();
                            $payment      = \App\Models\ParentArchive::where("tp_transaction_no",$tr->new_id)->where("log_parent_id")->get();
                            $all_payment  = [ ]; 
                            foreach($payment as $it){
                                    $all_payment[$it->line_id] = $it; 
                            }
                            $shipp        = \App\Models\ParentArchive::orderby("id","desc")->where("ship_transaction_id",$tr->new_id)->where("type","purchase")->first();
                              
                            
                            return view("log_file.show_details")
                                ->with(compact('taxes','array',"bill_id",'new_purchase','last','shipp','new_payment','all_payment','RecievedWrong', 'additional','purchase','RecievedPrevious','currency_details' ,'product_list_all', 'Warehouse_list' ,'product_list','unit','store' ,'payment_methods','new_purchase_taxes', 'purchase_taxes', 'activities', 'statuses'));
                    

                    }elseif($tr->type == "sale"){
                        $last  = null;
                     
                        $business_id = request()->session()->get('user.business_id');
                        $taxes = \App\TaxRate::where('business_id', $business_id)
                                            ->pluck('name', 'id');
                        $query = \App\Models\ArchiveTransaction::where('business_id', $business_id)
                                    ->where('id', $id)
                                    ->with(['contact', 'sell_lines' => function ($q) {
                                        $q->whereNull('parent_sell_line_id');
                                    },'sell_lines.product', 'sell_lines.product.unit', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'table', 'service_staff'
                                    , 'sell_lines.service_staff', 'types_of_service', 'sell_lines.warranties', 'media']);
                        $new_query = \App\Models\ArchiveTransaction::where('business_id', $business_id)
                                    ->where('id', $tr->parent_id)
                                    ->with(['contact', 'sell_lines' => function ($q) {
                                        $q->whereNull('parent_sell_line_id');
                                    },'sell_lines.product', 'sell_lines.product.unit', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'table', 'service_staff'
                                    , 'sell_lines.service_staff', 'types_of_service', 'sell_lines.warranties', 'media']);
                        if(app("request")->input("last") || $tr->parent_id == null){
                            $last_query = \App\Transaction::where('business_id', $business_id)
                            ->where('id', $tr->new_id)
                            ->with(['contact', 'sell_lines' => function ($q) {
                                $q->whereNull('parent_sell_line_id');
                            },'sell_lines.product', 'sell_lines.product.unit', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'table', 'service_staff'
                            , 'sell_lines.service_staff', 'types_of_service', 'sell_lines.warranties', 'media']);
                            $new_query = $last_query ;
                        }else{
                            
                        }
                           
                        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access') && auth()->user()->can('view_own_sell_only')) {
                            $query->where('transactions.created_by', request()->session()->get('user.id'));
                        }

                        $sell = $query->firstOrFail();
                        $new_sell = $new_query->firstOrFail();
                        
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
                       
                        $new_order_taxes = [];
                        if (!empty($new_sell->tax)) {
                            if ($new_sell->tax->is_tax_group) {
                                $new_order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($new_sell->tax, $new_sell->tax_amount));
                            } else {
                                $new_order_taxes[$new_sell->tax->name] = $new_sell->tax_amount;
                            }
                        }

                        $business_details = $this->businessUtil->getDetails($business_id);
                        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
                        $shipping_statuses = $this->transactionUtil->shipping_statuses();
                        $shipping_status_colors = $this->shipping_status_colors;
                        $common_settings = session()->get('business.common_settings');
                        $is_warranty_enabled = !empty($common_settings['enable_product_warranty']) ? true : false;

                        $statuses = \App\Transaction::getSellStatuses();
                
                        $transactions = \App\Models\ArchiveTransaction::where("id" , $tr->id)->get();
                        $store  = "";
                        if(!empty($transactions)){
                            foreach ($transactions as $transaction) {
                                $business_id = request()->session()->get('user.business_id');
                                $warehouses = \App\Models\Warehouse::where("business_id",$business_id)->select(["description","status","mainStore","id","name"])->get();
                                if (!empty($warehouses)) {
                                    foreach ($warehouses as $warehouse) {
                                        if($transaction->store == $warehouse->id){ 
                                            $store = $warehouse->name;
                                        }
                                    }
                                }
                            }
                        }
                        

                        return view('log_file.show_sale')
                            ->with(compact([
                                'taxes',
                                'sell',
                                'new_sell',
                                'payment_types', 
                                'order_taxes',
                                'new_order_taxes',
                                'pos_settings',
                                'shipping_statuses',
                                'shipping_status_colors',
                                'bill_id',
                                'is_warranty_enabled',
                                'activities',
                                'statuses',
                                'store',
                            ])) ;

                        
                    
                    } 
                }else{
                    return null;
                }
     }

     public function show_main($id)
     {
        $main    = \App\Transaction::find($id);
        $child   = \App\Models\ArchiveTransaction::where("new_id",$id)->orderby("id","desc")->get();
        $bill_id = app("request")->input("bill_id");

        $payment_main  = \App\TransactionPayment::where("transaction_id",$id)->orderby("id","desc")->get();
        $payment_child = \App\Models\ParentArchive::where("tp_transaction_no",$id)->orderby("id","desc")->get();

        $business_id = request()->session()->get('user.business_id');

        $uselist = \App\User::where("business_id",$business_id)->get();
        $users = [] ;
        foreach($uselist as $it){
            $users [$it->id] = $it->first_name  ;
        }
        return view("log_file.main_bill")->with(compact("main",'bill_id',"payment_main","payment_child","child","users"));
     }

}
