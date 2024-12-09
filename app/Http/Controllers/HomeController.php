<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Currency;
use App\ExpenseCategory;
use App\Transaction;
use App\Utils\BusinessUtil;
use App\CustomerGroup;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\VariationLocationDetails;
use Datatables;
use DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Utils\Util;
use App\Utils\RestaurantUtil;
use App\User;
use Illuminate\Notifications\DatabaseNotification;
use App\Media; 
use App\Charts\SampleChart;


class HomeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $commonUtil;
    protected $restUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil,
        Util $commonUtil,
        RestaurantUtil $restUtil
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
        // dd(auth()->user());
        $business_id = request()->session()->get('user.business_id');
       
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end']   = date('Y-m-t');
        $date_filters['this_week']['start']  = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end']    = date('Y-m-d', strtotime('sunday this week'));
        
        //.1.//.... warehouse permissions 
         
         
        //.1.//.... end 
        if (!auth()->user()->can('dashboard.data')) {

            return view('home.index', compact('date_filters'));
        }

        $currency_detail = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }

        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();
        
        //Chart for sells last 30 days
        $sells_last_30_days = $this->transactionUtil->getSellsLast30Days($business_id);
        $labels = [];
        $all_sell_values = [];
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            $labels[] = date('j M Y', strtotime($date));

            if (!empty($sells_last_30_days[$date])) {
                $all_sell_values[] = (float) $sells_last_30_days[$date];
            } else {
                $all_sell_values[] = 0;
            }
        }

        //Get sell for indivisual locations
        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();
        
        $location_sells = [];
        $sells_by_location = $this->transactionUtil->getSellsLast30Days($business_id, true);
        foreach ($all_locations as $loc_id => $loc_name) {
            if(User::can_access_this_location($loc_id)){
            $values = [];
            foreach ($dates as $date) {
                $sell = $sells_by_location->first(function ($item) use ($loc_id, $date) {
                    return $item->date == $date &&
                        $item->location_id == $loc_id;
                });
                
                if (!empty($sell)) {
                    $values[] = (float) $sell->total_sells;
                } else {
                    $values[] = 0;
                }
            }
            $location_sells[$loc_id]['loc_label'] = $loc_name;
            $location_sells[$loc_id]['values'] = $values;
        }
        }





        //dd($location_sells);
        //Chart for sells this financial year
        $sells_this_fy = $this->transactionUtil->getSellsCurrentFy($business_id, $fy['start'], $fy['end']);

        $labels = [];
        $values = [];

        $months = [];
        $date   = strtotime($fy['start']);
        $last   = date('m-Y', strtotime($fy['end']));
        
        $fy_months = [];
        do {
            $month_year = date('m-Y', $date);
            $fy_months[] = $month_year;

            $month_number = date('m', $date);

            $labels[] = \Carbon::createFromFormat('m-Y', $month_year)
                            ->format('M-Y');
            $date = strtotime('+1 month', $date);

            if (!empty($sells_this_fy[$month_year])) {
                $values[] = (float) $sells_this_fy[$month_year];
            } else {
                $values[] = 0;
            }
        } while ($month_year != $last);

        $fy_sells_by_location = $this->transactionUtil->getSellsCurrentFy($business_id, $fy['start'], $fy['end'], true);
        $fy_sells_by_location_data = [];

        foreach ($all_locations as $loc_id => $loc_name) {
            $values_data = [];
            foreach ($fy_months as $month) {
                $sell = $fy_sells_by_location->first(function ($item) use ($loc_id, $month) {
                    return $item->yearmonth == $month &&
                        $item->location_id == $loc_id;
                });
                
                if (!empty($sell)) {
                    $values_data[] = (float) $sell->total_sells;
                } else {
                    $values_data[] = 0;
                }
            }
            $fy_sells_by_location_data[$loc_id]['loc_label'] = $loc_name;
            $fy_sells_by_location_data[$loc_id]['values'] = $values_data;
        }

        //Get Dashboard widgets from module
        $module_widgets = $this->moduleUtil->getModuleData('dashboard_widget');

        $widgets = [];

        foreach ($module_widgets as $widget_array) {
            if (!empty($widget_array['position'])) {
                $widgets[$widget_array['position']][] = $widget_array['widget'];
            }
        }


        $date_range =[]; //$request->input('date_range');
        
        if (!empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date']   = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        } else {
            $filters['start_date'] = \Carbon::now()->startOfYear()->format('Y-m-d');
            $filters['end_date']   = \Carbon::now()->endOfYear()->format('Y-m-d');
        }
      
        // $expenses = $this->transactionUtil->getExpenseReport($business_id, $filters);

        // $values = [];
        // $labels = [];
        // foreach ($expenses as $expense) {
        //     $values[] = (float) $expense->total_expense;
        //     $labels[] = !empty($expense->category) ? $expense->category : __('report.others');
        // }

        // $categories         = ExpenseCategory::where('business_id', $business_id)->pluck('name', 'id');
        // $business_locations = BusinessLocation::forDropdown($business_id, true);

       //recent transaction==========================================================
        // $transaction_status ='final'; //$request->get('status');

       // $register = $this->cashRegisterUtil->getCurrentCashRegister($user_id);

        // $query = Transaction::where('business_id', $business_id)
        //                // ->where('transactions.created_by', $user_id)
        //                 ->where('transactions.type', 'sale')
        //                 ->where('is_direct_sale', 0);

        // if ($transaction_status == 'final') {
        //     //Commented as credit sales not showing
        //     // if (!empty($register->id)) {
        //     //     $query->leftjoin('cash_register_transactions as crt', 'transactions.id', '=', 'crt.transaction_id')
        //     //     ->where('crt.cash_register_id', $register->id);
        //     // }
        // }

        // if ($transaction_status == 'quotation') {
        //     $query->where('transactions.status', 'draft')
        //         ->where('is_quotation', 1);
        // } elseif ($transaction_status == 'draft') {
        //     $query->where('transactions.status', 'draft')
        //         ->where('is_quotation', 0);
        // } else {
        //     $query->where('transactions.status', $transaction_status);
        // }

        // $transaction_sub_type =''; //$request->get('transaction_sub_type');
        // if (!empty($transaction_sub_type)) {
        //     $query->where('transactions.sub_type', $transaction_sub_type);
        // } else {
        //     $query->where('transactions.sub_type', null);
        // }

        // $transactions = $query->orderBy('transactions.created_at', 'desc')
        //                     ->groupBy('transactions.id')
        //                     ->select('transactions.*')
        //                     ->with(['contact', 'table'])
        //                     ->limit(10)
        //                     ->get();

        #.........................
        #.........................
        #.........................
        $months                  =  [""];
        $values                  =  [""];           
        $values2                 =  [""];           
        $numberOfReceipt         =  200; 
        $business                = \App\Business::find($business_id);
        $customerParent          = $business->customer_type_id;
        $supplierParent          = $business->supplier_type_id;
        #...................balance..
        $totalCustomer           = \App\Contact::whereIn("type",["customer","both"])->count();
        $totalBalanceCustomer    = \App\Account::whereHas("account_type",function($query) use($customerParent){ $query->where("id",$customerParent); })->sum('balance');
        $totalBalanceSupplier    = \App\Account::whereHas("account_type",function($query) use($supplierParent){  $query->where("id",$supplierParent);  })->sum('balance');
        #.........................
        $contactOfInvoice        = \App\Transaction::where('business_id', $business_id)
                                                      ->where('type', 'sale')
                                                      ->whereIn('status', ['final','delivered'])
                                                      ->whereIn('sub_status', ['final','f'])
                                                      ->whereDate("transaction_date",">=",$filters['start_date'])                  
                                                      ->whereDate("transaction_date","<=",$filters['end_date']  )                  
                                                      ->groupBy('contact_id')->pluck('contact_id')->count();
        $idsOfInvoice            = \App\Transaction::where('business_id', $business_id)
                                                      ->where('type', 'sale')
                                                      ->whereIn('status', ['final','delivered'])
                                                      ->whereDate("transaction_date",">=",$filters['start_date'])                  
                                                      ->whereDate("transaction_date","<=",$filters['end_date']  )  
                                                      ->whereIn('sub_status', ['final','f'])->pluck('id');
        $numberOfInvoice         = \App\Transaction::where('business_id', $business_id)
                                                      ->where('type', 'sale')
                                                      ->whereIn('status', ['final','delivered'])
                                                      ->whereDate("transaction_date",">=",$filters['start_date'])                  
                                                      ->whereDate("transaction_date","<=",$filters['end_date']  )  
                                                      ->whereIn('sub_status', ['final','f'])->count();
              
        $partialInvoices         = \App\TransactionPayment::whereIn("transaction_id", $idsOfInvoice)
                                                   ->whereHas('transaction',function($query) use($filters){
                                                         $query->where("payment_status","partial");;
                                                         $query->whereDate("transaction_date",">=",$filters['start_date']);                  
                                                         $query->whereDate("transaction_date","<=",$filters['end_date']  );  
                                                    })
                                                   ->sum('amount');
        #.........................
        
        #...........delivered
        $idSales                 = \App\Transaction::where('business_id', $business_id)
                                            ->where('type', 'sale')
                                            ->whereIn('status', ['final','delivered'])
                                            ->whereDate("transaction_date",">=",$filters['start_date'])                  
                                            ->whereDate("transaction_date","<=",$filters['end_date']  )  
                                            ->whereIn('sub_status', ['final','f'])->pluck('id');

        $trSales                 = \App\TransactionSellLine::whereIn('transaction_id',$idSales)->pluck("product_id","id");
        $productId               = \App\TransactionSellLine::whereIn('transaction_id',$idSales)->groupBy('product_id')->pluck("product_id");
        #..................................1.
        $productIDNotDelivered   = [];
        $productIDDelivered      = [];
        $costOfDeliveredSale     = 0;$AmOfUnDeliveredSale = 0;
        $amountOfDeliveredSale   = 0;$CoOfUnDeliveredSale = 0;
        foreach($productId as $iD){
            $moves = \App\Models\ItemMove::whereIn('state',["sale","sell_return"])->whereIn('transaction_id',$idSales)->where("product_id",$iD)->get();
            foreach($moves as $move){
                $delivered             = \App\Models\DeliveredPrevious::find($move->recieve_id);
                $lineSel               = \App\TransactionSellLine::find($move->line_id);
                $margin                = $lineSel->quantity-$delivered->current_qty;
                $amountOfDeliveredSale = $amountOfDeliveredSale + ($move->qty * $move->row_price_inc_exp);   
                $costOfDeliveredSale   = $costOfDeliveredSale   + ($move->qty * $move->out_price);   
                if($margin!=0){
                    $AmOfUnDeliveredSale = $AmOfUnDeliveredSale + (abs($margin) * $move->row_price_inc_exp); 
                    $CoOfUnDeliveredSale = $CoOfUnDeliveredSale + (abs($margin) * $move->out_price);
                }
            }
            if(count($moves)==0){
                $productIDNotDelivered[] = $iD;
            }else{
                 
            }
        }
        #..................................1.
        #........................Not delivered yet.....2.
        $amountOfUnDeliveredSale = floatVal(\App\TransactionSellLine::whereIn('transaction_id',$idSales)->whereIn("product_id",$productIDNotDelivered)->select(\DB::raw('SUM(quantity*unit_price) as total'))->first()->total);
        $productCost             = \App\Models\ItemMove::whereIn("product_id",$productIDNotDelivered)
                                                    ->orderByRaw('ISNULL(date), date desc, created_at desc')
                                                    ->orderBy("id","desc")
                                                    ->orderBy("order_id","desc")
                                                    ->groupBy("product_id")
                                                    ->pluck('unit_cost','product_id');
        $listOfQuantityProduct   = \App\TransactionSellLine::whereIn('transaction_id',$idSales)->whereIn("product_id",$productIDNotDelivered)->select('quantity','product_id')->get();
        $array_of_product_qty    = [];  $array_of_product  = [];
        foreach($listOfQuantityProduct as $key => $value){
            if(!in_array($value->product_id,$array_of_product)){
                $array_of_product[]                       = $value->product_id;
                $array_of_product_qty[$value->product_id] = $value->quantity;
            }else{     
                $array_of_product_qty[$value->product_id] = $array_of_product_qty[$value->product_id] + $value->quantity;
            }
        }
        $costOfUnDeliveredSale = 0;
        foreach($productCost as $ky => $val){
            $costOfUnDeliveredSale = $costOfUnDeliveredSale + ($array_of_product_qty[$ky]*$val) ;
        }
        $amountOfUnDeliveredSale = $amountOfUnDeliveredSale + $AmOfUnDeliveredSale ; 
        $costOfUnDeliveredSale   = $costOfUnDeliveredSale   + $CoOfUnDeliveredSale ; 
        #..................................2.
        #......................
        #.......................                             
        #.......................
        $patterns           = [];
        $pattern            = \App\Models\Pattern::where("business_id", $business_id)->get();
        foreach($pattern as $pat){
            $patterns[$pat->id] = $pat->name;
        }
        #.......................
        #.......................
        $cash              = \App\Account::main('cash',null,null,1);
        $bank              = \App\Account::main('bank',null,null,1);
        $cashId            = \App\Account::main('cash',null,null,2);
        $bankId            = \App\Account::main('bank',null,null,2);
        #....................... 
        #.......................
        $totalPaidSales    = \App\TransactionPayment::whereIn("transaction_id", $idsOfInvoice)->whereDate('paid_on',">=",$filters['start_date'])->whereDate('paid_on',"<=", $filters['end_date'])->sum('amount');
        
        #.......................
        $totalSales        = \App\Transaction::where('business_id', $business_id)
                                        ->where('type', 'sale')
                                        ->whereIn('status', ['final','delivered'])
                                        ->whereDate("transaction_date",">=",$filters['start_date'])                  
                                        ->whereDate("transaction_date","<=",$filters['end_date']  )  
                                        ->whereIn('sub_status', ['final','f'])->sum('final_total');

        $totalSalesExclude = \App\Transaction::where('business_id', $business_id)
                                        ->where('type', 'sale')
                                        ->whereIn('status', ['final','delivered'])
                                        ->whereDate("transaction_date",">=",$filters['start_date'])                  
                                        ->whereDate("transaction_date","<=",$filters['end_date']  )  
                                        ->whereIn('sub_status', ['final','f'])->sum('total_before_tax');

        $totalSalesTax     = \App\Transaction::where('business_id', $business_id)
                                        ->where('type', 'sale')
                                        ->whereIn('status', ['final','delivered'])
                                        ->whereDate("transaction_date",">=",$filters['start_date'])                  
                                        ->whereDate("transaction_date","<=",$filters['end_date']  )  
                                        ->whereIn('sub_status', ['final','f'])->sum('tax_amount');
        
        $totalUnPaidSales  = (($totalSales - $totalPaidSales)<0)?abs($totalSales - $totalPaidSales):$totalSales - $totalPaidSales ;
        $grossProfit       = $totalSalesExclude   - ($costOfDeliveredSale + $costOfUnDeliveredSale);
        $costOfSales       = $costOfDeliveredSale + $costOfUnDeliveredSale ;
        
        #.......................
        $chart             = new SampleChart(); 
        // // Parse the start and end dates using Carbon
        $start   = Carbon::parse($filters['start_date'])->startOfMonth();
        $end     = Carbon::parse($filters['end_date'])->endOfMonth();
 
        // Generate the period between the start    and end dates
        $period  = CarbonPeriod::create($start, '1 month', $end);
        foreach ($period as $date) { 
            #......................................date section
            $months[]               = $date->format('M Y');
            $new_format             = $date->format('Y-m-d'); 
            $firstDate              = $date;
            $secondDate             = \Carbon::parse($new_format)->startOfMonth()->subMonth(-1);
            #.................................................end
           
            $idsOfInvoiceChart      = \App\Transaction::where('business_id', $business_id)
                                                        ->where('type', 'sale')
                                                        ->whereDate('transaction_date',">=", $firstDate)
                                                        ->whereDate('transaction_date',"<=", $secondDate)
                                                        ->whereIn('status', ['final','delivered'])
                                                        ->whereIn('sub_status', ['final','f'])
                                                        ->pluck('id');
             

            $totalSalesChart        = \App\Transaction::where('business_id', $business_id)
                                                        ->where('type', 'sale')
                                                        ->whereDate('transaction_date',">=", $firstDate)
                                                        ->whereDate('transaction_date',"<=", $secondDate)
                                                        ->whereIn('status', ['final','delivered'])
                                                        ->whereIn('sub_status', ['final','f'])
                                                        ->sum('final_total');
             
           
            $totalPaid              = \App\TransactionPayment::whereIn("transaction_id", $idsOfInvoiceChart)->whereDate('paid_on',">=", $firstDate)->whereDate('paid_on',"<=", $secondDate)->sum('amount');
            $totalUnPaid            = (($totalSalesChart - $totalPaid)<0)?abs($totalSalesChart - $totalPaid):$totalSalesChart - $totalPaid ;
                
            $values[]               = $totalPaid;
            $values2[]              = $totalUnPaid;

            $totalPaidSales         = $totalPaidSales + $totalPaid;
            $totalUnPaidSales       = $totalUnPaidSales + $totalUnPaid;
             
        }
        if(request()->ajax()){
            $startDate        =  request()->input('start_date');
            $endDate          =  request()->input('end_date');  
            if($startDate != null){
                $yearStartDate            =  \Carbon::createFromFormat('m/d/Y', $startDate)->subYear(0)->year ;
                $monthStartDate           =  \Carbon::createFromFormat('m/d/Y', $startDate)->month;
                $monthEndDate             =  \Carbon::createFromFormat('m/d/Y', $endDate)->month;
                // // Parse the start and end dates using Carbon
                $start   = Carbon::parse($startDate)->startOfMonth();
                $end     = Carbon::parse($endDate)->endOfMonth();
                // Generate the period between the start    and end dates
                $period  = CarbonPeriod::create($start, '1 month', $end);
                // Initialize an array to hold the month names
                $months  = [""];
                $values  = [""];
                $values2 = [""];
                #.................................
             
                #...........delivered 
                $idSales                 = \App\Transaction::where('business_id', $business_id)
                                                        ->where('type', 'sale')
                                                        ->whereDate("transaction_date",">=",$start)                  
                                                        ->whereDate("transaction_date","<=",$end  )  
                                                        ->whereIn('status', ['final','delivered'])
                                                        ->whereIn('sub_status', ['final','f'])->pluck('id');
               
                $trSales                 = \App\TransactionSellLine::whereIn('transaction_id',$idSales)->pluck("product_id","id");
                $productId               = \App\TransactionSellLine::whereIn('transaction_id',$idSales)->groupBy('product_id')->pluck("product_id");
                #..................................1.
                $productIDNotDelivered   = [];
                $productIDDelivered      = [];
                $costOfDeliveredSale     = 0;$AmOfUnDeliveredSale = 0;
                $amountOfDeliveredSale   = 0;$CoOfUnDeliveredSale = 0;
                foreach($productId as $iD){
                $moves = \App\Models\ItemMove::whereIn('state',["sale","sell_return"])->whereIn('transaction_id',$idSales)->where("product_id",$iD)->get();
                foreach($moves as $move){
                $delivered             = \App\Models\DeliveredPrevious::find($move->recieve_id);
                $lineSel               = \App\TransactionSellLine::find($move->line_id);
                $margin                = $lineSel->quantity-$delivered->current_qty;
                $amountOfDeliveredSale = $amountOfDeliveredSale + ($move->qty * $move->row_price_inc_exp);   
                $costOfDeliveredSale   = $costOfDeliveredSale   + ($move->qty * $move->out_price);   
                if($margin!=0){
                $AmOfUnDeliveredSale = $AmOfUnDeliveredSale + (abs($margin) * $move->row_price_inc_exp); 
                $CoOfUnDeliveredSale = $CoOfUnDeliveredSale + (abs($margin) * $move->out_price);
                }
                }
                if(count($moves)==0){
                    $productIDNotDelivered[] = $iD;
                }else{

                }
                }

                #..................................1.
                #........................Not delivered yet.....2.
                $amountOfUnDeliveredSale = floatVal(\App\TransactionSellLine::whereIn('transaction_id',$idSales)->whereIn("product_id",$productIDNotDelivered)->select(\DB::raw('SUM(quantity*unit_price) as total'))->first()->total);
                $productCost             = \App\Models\ItemMove::whereIn("product_id",$productIDNotDelivered)
                                                                ->orderByRaw('ISNULL(date), date desc, created_at desc')
                                                                ->orderBy("id","desc")
                                                                ->orderBy("order_id","desc")
                                                                ->groupBy("product_id")
                                                                ->pluck('unit_cost','product_id');
                $listOfQuantityProduct   = \App\TransactionSellLine::whereIn('transaction_id',$idSales)->whereIn("product_id",$productIDNotDelivered)->select('quantity','product_id')->get();
                $array_of_product_qty    = [];  $array_of_product  = [];
                foreach($listOfQuantityProduct as $key => $value){
                if(!in_array($value->product_id,$array_of_product)){
                $array_of_product[]                       = $value->product_id;
                $array_of_product_qty[$value->product_id] = $value->quantity;
                }else{     
                $array_of_product_qty[$value->product_id] = $array_of_product_qty[$value->product_id] + $value->quantity;
                }
                }
                $costOfUnDeliveredSale = 0;
                foreach($productCost as $ky => $val){
                $costOfUnDeliveredSale = $costOfUnDeliveredSale + ($array_of_product_qty[$ky]*$val) ;
                }
                $amountOfUnDeliveredSale = $amountOfUnDeliveredSale + $AmOfUnDeliveredSale ; 
                $costOfUnDeliveredSale   = $costOfUnDeliveredSale   + $CoOfUnDeliveredSale ; 
                #.................................
              
                $contactOfInvoice  = \App\Transaction::where('business_id', $business_id)
                                                    ->where('type', 'sale')
                                                    ->whereDate('transaction_date',">=", $start)
                                                    ->whereDate('transaction_date',"<=", $end)
                                                    ->whereIn('status', ['final','delivered'])
                                                    ->whereIn('sub_status', ['final','f'])->groupBy('contact_id')->pluck('contact_id')->count();

                $idsOfInvoice      = \App\Transaction::where('business_id', $business_id)
                                                    ->where('type', 'sale')
                                                    ->whereDate('transaction_date',">=", $start)
                                                    ->whereDate('transaction_date',"<=", $end)
                                                    ->whereIn('status', ['final','delivered'])
                                                    ->whereIn('sub_status', ['final','f'])->pluck('id');

                $numberOfInvoice   = \App\Transaction::where('business_id', $business_id)
                                                    ->where('type', 'sale')
                                                    ->whereIn('status', ['final','delivered'])
                                                    ->whereDate("transaction_date",">=",$start)                  
                                                    ->whereDate("transaction_date","<=",$end  )  
                                                    ->whereIn('sub_status', ['final','f'])->count();

                $totalSales        = \App\Transaction::where('business_id', $business_id)
                                                    ->where('type', 'sale')
                                                    ->whereDate('transaction_date',">=", $start)
                                                    ->whereDate('transaction_date',"<=", $end)
                                                    ->whereIn('status', ['final','delivered'])
                                                    ->whereIn('sub_status', ['final','f'])
                                                    ->sum('final_total');

                $totalSalesExclude = \App\Transaction::where('business_id', $business_id)
                                                    ->where('type', 'sale')
                                                    ->whereIn('status', ['final','delivered'])
                                                    ->whereDate("transaction_date",">=",$start)                  
                                                    ->whereDate("transaction_date","<=",$end  )  
                                                    ->whereIn('sub_status', ['final','f'])->sum('total_before_tax');

                $totalSalesTax     = \App\Transaction::where('business_id', $business_id)
                                                    ->where('type', 'sale')
                                                    ->whereIn('status', ['final','delivered'])
                                                    ->whereDate("transaction_date",">=",$start)                  
                                                    ->whereDate("transaction_date","<=",$end  )  
                                                    ->whereIn('sub_status', ['final','f'])->sum('tax_amount');
                $grossProfit            = $totalSalesExclude   - ($costOfDeliveredSale + $costOfUnDeliveredSale);
                $costOfSales            = $costOfDeliveredSale + $costOfUnDeliveredSale ;
                $totalPaidSales         = 0;
                $totalUnPaidSales       = 0;
                // Iterate over the period and format each month
                foreach ($period as $date) { 
                    #......................................date section
                    $months[]               = $date->format('M Y');
                    $new_format             = $date->format('Y-m-d'); 
                    $firstDate              = $date;
                    $secondDate             = \Carbon::parse($new_format)->startOfMonth()->subMonth(-1);
                    #.................................................end
                   
                    $idsOfInvoiceChart      = \App\Transaction::where('business_id', $business_id)
                                                                ->where('type', 'sale')
                                                                ->whereDate('transaction_date',">=", $firstDate)
                                                                ->whereDate('transaction_date',"<=", $secondDate)
                                                                ->whereIn('status', ['final','delivered'])
                                                                ->whereIn('sub_status', ['final','f'])
                                                                ->pluck('id');
                     
 
                    $totalSalesChart        = \App\Transaction::where('business_id', $business_id)
                                                                ->where('type', 'sale')
                                                                ->whereDate('transaction_date',">=", $firstDate)
                                                                ->whereDate('transaction_date',"<=", $secondDate)
                                                                ->whereIn('status', ['final','delivered'])
                                                                ->whereIn('sub_status', ['final','f'])
                                                                ->sum('final_total');
                     
                   
                    $totalPaid              = \App\TransactionPayment::whereIn("transaction_id", $idsOfInvoiceChart)->whereDate('paid_on',">=", $firstDate)->whereDate('paid_on',"<=", $secondDate)->sum('amount');
                    $totalUnPaid            = (($totalSalesChart - $totalPaid)<0)?abs($totalSalesChart - $totalPaid):$totalSalesChart - $totalPaid ;
                        
                    $values[]               = $totalPaid;
                    $values2[]              = $totalUnPaid;
    
                    $totalPaidSales         = $totalPaidSales + $totalPaid;
                    $totalUnPaidSales       = $totalUnPaidSales + $totalUnPaid;
                     
                }
                 
            }      
            
            return  response()->json(compact('costOfSales','grossProfit','costOfUnDeliveredSale','amountOfUnDeliveredSale','costOfDeliveredSale','amountOfDeliveredSale','totalSales','totalSalesExclude','totalSalesTax','numberOfInvoice','months', 'values', 'values2','totalPaidSales','totalUnPaidSales','contactOfInvoice')); 
            
        }   
        $closing          = \App\Product::closing_stock($business_id);
        return view('home.index', compact('closing','cashId','bankId','amountOfUnDeliveredSale','costOfUnDeliveredSale','amountOfDeliveredSale','costOfDeliveredSale','trSales','totalBalanceCustomer','totalBalanceSupplier','cash','bank','patterns', 'chart','values2','months','values','contactOfInvoice','totalSalesTax','totalCustomer','totalSales','totalSalesExclude','totalPaidSales','totalUnPaidSales','numberOfInvoice','partialInvoices','numberOfReceipt'     ));
    }

    /**
     * Retrieves purchase and sell details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotals()
    {
        if (request()->ajax()) {
            
            if(app("request")->input("month")){
                $start = \Carbon::now()->subMonth(0)->day(0);
                $end   =  \Carbon::now()->subMonth(0);
            }elseif(app("request")->input("year")){
                $start = \Carbon::now()->subYear(1);
                $end   = \Carbon::now()->subYear(0);
             }else{
                $start = \Carbon::now();
                $end   = \Carbon::now()->addHours(24);
            }
            
            $reportSetting  =  \App\Models\ReportSetting::select("*")->first();
             
            $location_id = request()->location_id;
            $business_id = request()->session()->get('user.business_id');
            $user_id     = request()->session()->get('user.id');
            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start, $end, $location_id,null,$reportSetting ,1);
            // dd($purchase_details);
            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start, $end, $location_id,null,$reportSetting ,1);

            $transaction_types = [
                'purchase_return', 'sell_return', 'expense'
            ];

            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start,
                $end,
                $location_id,
                null,
                null
            );

            $total_purchase_inc_tax = !empty($purchase_details['total_purchase_inc_tax']) ? $purchase_details['total_purchase_inc_tax'] : 0;
            $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'];

            $total_purchase = $total_purchase_inc_tax - $total_purchase_return_inc_tax;
            $output = $purchase_details;
            $output['total_purchase']  = $total_purchase;

            $total_sell_inc_tax = !empty($sell_details['total_sell_inc_tax']) ? $sell_details['total_sell_inc_tax'] : 0;
            $total_sell_return_inc_tax = !empty($transaction_totals['total_sell_return_inc_tax']) ? $transaction_totals['total_sell_return_inc_tax'] : 0;

            $output['total_sell']      = $total_sell_inc_tax - $total_sell_return_inc_tax;

            $output['invoice_due']     = $sell_details['invoice_due'];
            $output['total_expense']   = $transaction_totals['total_expense'];
            
            return $output;
        }
    }

    public function getExpense()
    {
        if(request()->ajax()){
           
            $reportSetting  =  \App\Models\ReportSetting::select("*")->first();

            if(app("request")->input("month")){
                $start = \Carbon::now()->subMonth(0)->day(0);
                $end   =  \Carbon::now()->subMonth(0);
            }elseif(app("request")->input("year")){
                $start = \Carbon::now()->subYear(1);
                $end   = \Carbon::now()->subYear(0);
             }else{
                $start = \Carbon::now();
                $end   = \Carbon::now()->addHours(24);
            }

            $location_id = request()->location_id;
            $business_id = request()->session()->get('user.business_id');
            $expenses    = $this->transactionUtil->getTotalByType("expense",$reportSetting,$start,$end);
            // $expenses = \App\Models\AdditionalShipping::whereHas("transaction",function($query) use($business_id){
            //                                     $query->where("business_id",$business_id);
            //                                 });
            
            // if (!empty($start_date) && !empty($end_date)) {
            //     $expenses->whereHas("transaction",function($query) use($start_date,$end_date){
            //                     $query->whereDate('transaction_date', '>=', $start_date)
            //                           ->whereDate('transaction_date', '<=', $end_date);
            //     });
            // }
            // $transaction = $expenses->get();

            $total_expense = ($expenses["total"]["total_amounts"] != null )?$expenses["total"]["total_amounts"]:0;

            // foreach($transaction as $it){
            //        $ids = $it->items->pluck("id");
            //        $expense = \App\Models\AdditionalShippingItem::whereIn("id",$ids)->sum("amount");
            //        $total_expense += $expense; 
            // }
            return $total_expense;
        }
    }

    public function saveAttach(Request $request){
        try{
            $type              = $request->type_of_attachment;
            $source_id         = $request->source_id;
            
            switch ($type){
                case 'purchase'://
                        $source     = \App\Transaction::find($source_id); 
                        $ref_no     = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url        = '/purchases';
                        break;
                case 'purchase_return'://
                        $source     = \App\Transaction::find($source_id); 
                        $ref_no     = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url        = '/purchase-return';
                        break;
                case 'sale'://
                        $source     = \App\Transaction::find($source_id);
                        $ref_no     = $source->invoice_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url        = '/sells';
                        if ($source->status == 'quotation') { $url = '/sells/quotations'; 
                        }  else if ($source->status == 'ApprovedQuotation') { $url = '/sells/QuatationApproved';
                        }  else if ($source->status == 'final' || $source->status == 'Delivered') { $url = '/sells';
                        }  else if ($source->status == 'draft') {
                            if ( $source->is_quotation == 1) { $url = '/sells/quotations';
                            } else { $url = '/sells/drafts'; }
                        }else{
                            $url        = '/sells';
                        }
                        break;
                case 'sell_return'://
                        $source     = \App\Transaction::find($source_id);
                        $ref_no     = $source->invoice_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url        = '/sell-return';
                        break;                    
                case 'payment_voucher':// 
                        $source     = \App\Models\PaymentVoucher::find($source_id);
                        $ref_no     = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url        = '/payment-voucher';
                        break;
                case 'daily_payment'://
                        $source     = \App\Models\DailyPayment::find($source_id);
                        $ref_no     = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url        = '/daily-payment';
                        break;
                case 'expense_voucher'://
                        $source     = \App\Models\GournalVoucher::find($source_id);
                        $ref_no     = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url        = '/gournal-voucher';
                        break;  
                case 'check'://
                        $source     = \App\Models\Check::find($source_id);
                        $ref_no     = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url        = '/cheque'; 
                        break;                    
                default:
                    $source            = null;
                    $ref_no            = ""; $document_attach   = [] ; 
                    $url               = "/";
                    break;

            }
            $company_name      = request()->session()->get("user_main.domain");
            if ($request->hasFile('attach')) {
                $count_doc1 = 1;
                $referencesNewStyle = str_replace('/', '-', $ref_no);
                foreach ($request->file('attach') as $file) {
                    #................
                    if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                        if ($file->getSize() <= config('constants.document_size_limit')){ 
                            $file_name_m    =  time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                            $file->move('uploads/companies/'.$company_name.'/documents/purchase',$file_name_m);
                            $file_name      =  'uploads/companies/'.$company_name.'/documents/purchase/'. $file_name_m;
                        }
                    }else{
                        if ($file->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                            $Data          = getimagesize($file);
                            $width         = $Data[0];
                            $height        = $Data[1];
                            $half_width    = $width/2;
                            $half_height   = $height/2; 
                            $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                            $file_name =  'uploads/companies/'.$company_name.'/documents/purchase/'. $new_file_name;
                            if ($imgs->save(public_path("uploads\companies\\$company_name\documents\purchase\\$new_file_name"),20)) {
                                $uploaded_file_name = $new_file_name;
                            }
                        }
                    }
                    #................
                    array_push($document_attach,$file_name);
                }
            }
            if(!empty($source)){

                $source->document           = json_encode($document_attach) ;
                $source->update();
                $output = [
                    "success" => 1,
                    "msg" => __('Added successfully'),
                ]; 
                return redirect($url)->with("status",$output);
            }else{
                $output = [
                    "success" => 0,
                    "msg" => __('Failed Action'),
                ];
                return redirect($url)->with("status",$output);
            }
        }catch(Exception $e){
            $output = [
                "success" => 0,
                "msg" => __('Failed Action'),
            ];
            return redirect($url)->with("status",$output);
        }
    }

    public function formAttach(Request $request){
             
            return view('attachment_outside.attach_outside');
       
    }
    /**
     * Retrieves sell products whose available quntity is less than alert quntity.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductStockAlert()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $query = VariationLocationDetails::join('product_variations as pv','variation_location_details.product_variation_id',
                '=',
                'pv.id'
            )
                    ->join(
                        'variations as v',
                        'variation_location_details.variation_id',
                        '=',
                        'v.id'
                    )
                    ->join(
                        'products as p',
                        'variation_location_details.product_id',
                        '=',
                        'p.id'
                    )
                    ->leftjoin(
                        'business_locations as l',
                        'variation_location_details.location_id',
                        '=',
                        'l.id'
                    )
                    ->leftjoin(
                        'warehouse_infos as wif',
                        'variation_location_details.product_id',
                        '=',
                        'wif.id'
                    )
                    ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                    ->where('p.business_id', $business_id)
                    ->where('p.enable_stock', 1)
                    // ->where('wif.product_qty',"<=","p.alert_quantity")
                    ->where('p.is_inactive', 0)
                    ->whereNull('v.deleted_at')
                    ->whereRaw('variation_location_details.qty_available <= p.alert_quantity');

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('variation_location_details.location_id', $permitted_locations);
            }

            $products = $query->select(
                'p.id as product_id',
                'p.name as product',
                'p.type',
                'pv.name as product_variation',
                'v.name as variation',
                'l.name as location',
                'variation_location_details.qty_available as stock',
                'u.short_name as unit'
            )
                    ->groupBy('variation_location_details.id')
                    ->orderBy('stock', 'asc');

            return Datatables::of($products)
                ->addColumn('product', function ($row) {
                    if ($row->type == 'single') {
                        return $row->product;
                    } else {
                        return $row->product . ' - ' . $row->product_variation . ' - ' . $row->variation;
                    }
                })
                ->addColumn('stock', function ($row) {
                     $stock = \App\Models\WarehouseInfo::where("product_id",$row->product_id)->select(DB::raw("SUM('product_qty') as stock"))->first()->stock;
                 
                      return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false>'. (float)$stock . '</span> ' . $row->unit;
                  
                  
                })
                ->addColumn('location', function ($row) {
                        return $row->location;
                    
                   
                })
                ->removeColumn('unit')
                ->removeColumn('type')
                ->removeColumn('product_variation')
                ->removeColumn('variation')
                ->rawColumns(["product","location","stock"])
                ->make(true);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchasePaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format("Y-m-d H:i:s");

            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(c.pay_term_type = 'days', c.pay_term_number, 30 * c.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            $dues =  $query->select(
                'transactions.id as id',
                'c.name as supplier',
                'c.supplier_business_name',
                'ref_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = !empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;
                    return '<span class="display_currency" data-currency_symbol="true">' .
                    $due . '</span>';
                })
                ->addColumn('action', '@can("purchase.create") <a href="{{action("TransactionPaymentController@addPayment", [$id])}}" class="btn btn-xs btn-success add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endcan')
                ->removeColumn('supplier_business_name')
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$supplier}}')
                ->editColumn('ref_no', function ($row) {
                    if (auth()->user()->can('purchase.view')) {
                        return  '<a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '"
                                    class="btn-modal" data-container=".view_modal">' . $row->ref_no . '</a>';
                    }
                    return $row->ref_no;
                })
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesPaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format("Y-m-d H:i:s");

            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            $dues =  $query->select(
                'transactions.id as id',
                'c.name as customer',
                'c.supplier_business_name',
                'transactions.invoice_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = !empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;
                    return '<span class="display_currency" data-currency_symbol="true">' .
                    $due . '</span>';
                })
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view')) {
                        return  '<a href="#" data-href="' . action('SellController@show', [$row->id]) . '"
                                    class="btn-modal" data-container=".view_modal">' . $row->invoice_no . '</a>';
                    }
                    return $row->invoice_no;
                })
                ->addColumn('action', '@if(auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access")) <a href="{{action("TransactionPaymentController@addPayment", [$id])}}" class="btn btn-xs btn-success add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endif')
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$customer}}')
                ->removeColumn('supplier_business_name')
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    public function loadMoreNotifications()
    {
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'DESC')->paginate(10);

        if (request()->input('page') == 1) {
            auth()->user()->unreadNotifications->markAsRead();
        }
        $notifications_data = $this->commonUtil->parseNotifications($notifications);

        return view('layouts.partials.notification_list', compact('notifications_data'));
    }

    /**
     * Function to count total number of unread notifications
     *
     * @return json
     */
    public function getTotalUnreadNotifications()
    {
        $unread_notifications = auth()->user()->unreadNotifications;
        $total_unread = $unread_notifications->count();

        $notification_html = '';
        $modal_notifications = [];
        foreach ($unread_notifications as $unread_notification) {
            if (isset($data['show_popup'])) {
                $modal_notifications[] = $unread_notification;
                $unread_notification->markAsRead();
            }
        }
        if (!empty($modal_notifications)) {
            $notification_html = view('home.notification_modal')->with(['notifications' => $modal_notifications])->render();
        }

        return [
            'total_unread' => $total_unread,
            'notification_html' => $notification_html
        ];
    }

    private function __chartOptions($title)
    {
        return [
            'yAxis' => [
                    'title' => [
                        'text' => $title
                    ]
                ],
            'legend' => [
                'align' => 'right',
                'verticalAlign' => 'top',
                'floating' => true,
                'layout' => 'vertical'
            ],
        ];
    }

    public function getCalendar()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->restUtil->is_admin(auth()->user(), $business_id);
        $is_superadmin = auth()->user()->can('superadmin');
        if (request()->ajax()) {
            $data = [
                'start_date' => request()->start,
                'end_date' => request()->end,
                'user_id' => ($is_admin || $is_superadmin) && !empty(request()->user_id) ? request()->user_id : auth()->user()->id,
                'location_id' => !empty(request()->location_id) ? request()->location_id : null,
                'business_id' => $business_id,
                'events' => request()->events ?? [],
                'color' => '#007FFF'
            ];
            $events = [];

            if (in_array('bookings', $data['events'])) {
                $events = $this->restUtil->getBookingsForCalendar($data);
            }
            
            $module_events = $this->moduleUtil->getModuleData('calendarEvents', $data);

            foreach ($module_events as $module_event) {
                $events = array_merge($events, $module_event);
            }  
            return $events;
        }

        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();
        $users = [];
        if ($is_admin) {
            $users = User::forDropdown($business_id, false);
        }

        $event_types = [
            'bookings' => [
                'label' => __('restaurant.bookings'),
                'color' => '#007FFF'
            ]
        ];
        $module_event_types = $this->moduleUtil->getModuleData('eventTypes');
        foreach ($module_event_types as $module_event_type) {
            $event_types = array_merge($event_types, $module_event_type);
        }
        
        return view('home.calendar')->with(compact('all_locations', 'users', 'event_types'));
    }

    public function showNotification($id)
    {
        $notification = DatabaseNotification::find($id);

        $data = $notification->data;

        $notification->markAsRead();

        return view('home.notification_modal')->with([
                'notifications' => [$notification]
            ]);
    }

    public function attachMediasToGivenModel(Request $request)
    {   
        if ($request->ajax()) {
            try {
                
                $business_id = request()->session()->get('user.business_id');

                $model_id = $request->input('model_id');
                $model = $request->input('model_type');
                $model_media_type = $request->input('model_media_type');

                DB::beginTransaction();

                //find model to which medias are to be attached
                $model_to_be_attached = $model::where('business_id', $business_id)
                                        ->findOrFail($model_id);

                Media::uploadMedia($business_id, $model_to_be_attached, $request, 'file', false, $model_media_type);

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success')
                ];
            } catch (Exception $e) {

                DB::rollBack();

                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    public function changeLanguage(Request $request){
        try{

            DB::beginTransaction();

            $business_id          = request()->session()->get('user.business_id');
            $user_id              = request()->session()->get('user.id');
            $i                    = request()->input('lang');
            $input['id']          = $user_id;
            $input['business_id'] = $business_id;
            $input                = ["language"=>$i];
            $user                 = \App\User::find($user_id);
            $user->update($input);

            if (in_array($i, ['en', 'ar'])) {
                session(['locale' => $i]);
                // Determine the direction for the new locale
                $direction = ($i === 'ar') ? 'rtl' : 'ltr';
                session(['direction' => $direction]);
                 
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        }catch(Exception $e){
            \DB::rollback();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];

        }
        return redirect()->back();
    }
}
