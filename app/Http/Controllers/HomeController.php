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

use Artisan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Utils\Util;
use App\Utils\RestaurantUtil;
use App\User;
use Illuminate\Notifications\DatabaseNotification;
use App\Media; 
use App\Charts\SampleChart;

require '../vendor/autoload.php';

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfReader\PdfReader;
use TCPDF;


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
        
        $business_id = request()->session()->get('user.business_id');
        $date_format = request()->session()->get('business.date_format'); 
        $date_range =[]; //$request->input('date_range');
        
        if (!empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date']   = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        } else {
            $filters['start_date'] = \Carbon::now()->startOfYear()->format($date_format);
            $filters['end_date']   = \Carbon::now()->endOfYear()->format($date_format);
        }
      
        $user_id              = request()->session()->get('user.id');
        $i                    = request()->input('lang');
        $input['id']          = $user_id;
        $input['business_id'] = $business_id;
        $input                = ["language"=>$i];
        $user                 = \App\User::find($user_id);
        
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
                                                      ->whereDate("transaction_date",">=",$this->transactionUtil->uf_date(trim($filters['start_date'])))                  
                                                      ->whereDate("transaction_date","<=",$this->transactionUtil->uf_date(trim($filters['end_date'])))                  
                                                      ->groupBy('contact_id')->pluck('contact_id')->count();
        $idsOfInvoice            = \App\Transaction::where('business_id', $business_id)
                                                      ->where('type', 'sale')
                                                      ->whereIn('status', ['final','delivered'])
                                                      ->whereDate("transaction_date",">=",$this->transactionUtil->uf_date(trim($filters['start_date'])))                  
                                                      ->whereDate("transaction_date","<=",$this->transactionUtil->uf_date(trim($filters['end_date']))  )  
                                                      ->whereIn('sub_status', ['final','f'])->pluck('id');
        $numberOfInvoice         = \App\Transaction::where('business_id', $business_id)
                                                      ->where('type', 'sale')
                                                      ->whereIn('status', ['final','delivered'])
                                                      ->whereDate("transaction_date",">=",$this->transactionUtil->uf_date(trim($filters['start_date'])))                  
                                                      ->whereDate("transaction_date","<=",$this->transactionUtil->uf_date(trim($filters['end_date']))  )  
                                                      ->whereIn('sub_status', ['final','f'])->count();
              
        $partialInvoices         = \App\TransactionPayment::whereIn("transaction_id", $idsOfInvoice)
                                                   ->whereHas('transaction',function($query) use($filters){
                                                         $query->where("payment_status","partial");;
                                                         $query->whereDate("transaction_date",">=",$this->transactionUtil->uf_date(trim($filters['start_date'])));                  
                                                         $query->whereDate("transaction_date","<=",$this->transactionUtil->uf_date(trim($filters['end_date']))  );  
                                                    })
                                                   ->sum('amount');
        #.........................
       
        #...........delivered
        $idSales                 = \App\Transaction::where('business_id', $business_id)
                                            ->where('type', 'sale')
                                            ->whereIn('status', ['final','delivered'])
                                            ->whereDate("transaction_date",">=",$this->transactionUtil->uf_date(trim($filters['start_date'])))                  
                                            ->whereDate("transaction_date","<=",$this->transactionUtil->uf_date(trim($filters['end_date'] )) )  
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
                $margin                = (($lineSel)?$lineSel->quantity:0)-(($delivered)?$delivered->current_qty:0);
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
        $totalPaidSales    = \App\TransactionPayment::whereIn("transaction_id", $idsOfInvoice)->whereDate('paid_on',">=",$this->transactionUtil->uf_date(trim($filters['start_date'])))->whereDate('paid_on',"<=", $this->transactionUtil->uf_date(trim($filters['end_date'])))->sum('amount');
        
        #.......................
        $totalSales        = \App\Transaction::where('business_id', $business_id)
                                        ->where('type', 'sale')
                                        ->whereIn('status', ['final','delivered'])
                                        ->whereDate("transaction_date",">=",$this->transactionUtil->uf_date(trim($filters['start_date'])))                  
                                        ->whereDate("transaction_date","<=",$this->transactionUtil->uf_date(trim($filters['end_date']))  )  
                                        ->whereIn('sub_status', ['final','f'])->sum('final_total');

        $totalSalesExclude = \App\Transaction::where('business_id', $business_id)
                                        ->where('type', 'sale')
                                        ->whereIn('status', ['final','delivered'])
                                        ->whereDate("transaction_date",">=",$this->transactionUtil->uf_date(trim($filters['start_date'])))                  
                                        ->whereDate("transaction_date","<=",$this->transactionUtil->uf_date(trim($filters['end_date'] )) )  
                                        ->whereIn('sub_status', ['final','f'])->sum('total_before_tax');

        $totalSalesTax     = \App\Transaction::where('business_id', $business_id)
                                        ->where('type', 'sale')
                                        ->whereIn('status', ['final','delivered'])
                                        ->whereDate("transaction_date",">=",$this->transactionUtil->uf_date(trim($filters['start_date'])))                  
                                        ->whereDate("transaction_date","<=",$this->transactionUtil->uf_date(trim($filters['end_date'] )) )  
                                        ->whereIn('sub_status', ['final','f'])->sum('tax_amount');
        
        $totalUnPaidSales  = (($totalSales - $totalPaidSales)<0)?abs($totalSales - $totalPaidSales):$totalSales - $totalPaidSales ;
        $grossProfit       = $totalSalesExclude   - ($costOfDeliveredSale + $costOfUnDeliveredSale);
        $costOfSales       = $costOfDeliveredSale + $costOfUnDeliveredSale ;
        
        #.......................
        $chart             = new SampleChart(); 
        // // Parse the start and end dates using Carbon
        $start   = Carbon::parse($this->transactionUtil->uf_date(trim($filters['start_date'])))->startOfMonth();
        $end     = Carbon::parse($this->transactionUtil->uf_date(trim($filters['end_date'])))->endOfMonth();
        
        // dd($start,$end,$filters['start_date'],$filters['end_date']);

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
                $yearStartDate            =  \Carbon::createFromFormat($date_format, $startDate)->subYear(0)->year ;
                $monthStartDate           =  \Carbon::createFromFormat($date_format, $startDate)->month;
                $monthEndDate             =  \Carbon::createFromFormat($date_format, $endDate)->month;
                // // Parse the start and end dates using Carbon
                $start   = Carbon::parse($this->transactionUtil->uf_date(trim($startDate)))->startOfMonth();
                $end     = Carbon::parse($this->transactionUtil->uf_date(trim($endDate)))->endOfMonth();
               
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
                $margin                = (($lineSel)?$lineSel->quantity:0)-(($delivered)?$delivered->current_qty:0);
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

            // $request->validate([
            //     'attach' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            // ]);

            $type              = $request->type_of_attachment;
            $source_id         = $request->source_id;
            
            switch ($type){
                case 'purchase'://
                        $source          = \App\Transaction::find($source_id); 
                        $ref_no          = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url             = '/purchases';
                        $dir             = 'purchase/';
                        $dir_back_right  = '/purchase';
                        $dir_back_left   = '\purchase';
                        break;
                case 'purchase_return'://
                        $source          = \App\Transaction::find($source_id); 
                        $ref_no          = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url             = '/purchase-return';
                        $dir             = 'purchase_return/';
                        $dir_back_right  = '/purchase_return';
                        $dir_back_left   = '\purchase_return';
                        break;
                case 'sale'://
                        $source          = \App\Transaction::find($source_id);
                        $ref_no          = $source->invoice_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url             = '/sells';
                        $dir             = 'sale/';
                        $dir_back_right  = '/sale';
                        $dir_back_left   = '\sale' ;
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
                        $source          = \App\Transaction::find($source_id);
                        $ref_no          = $source->invoice_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url             = '/sell-return';
                        $dir             = 'sale_return/';
                        $dir_back_right  = '/sale_return';
                        $dir_back_left   = '\sale_return';
                        break;                    
                case 'payment_voucher':// 
                        $source          = \App\Models\PaymentVoucher::find($source_id);
                        $ref_no          = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url             = '/payment-voucher';
                        $dir             = 'voucher/';
                        $dir_back_right  = '/voucher';
                        $dir_back_left   = '\voucher';
                        break;
                case 'daily_payment'://
                        $source          = \App\Models\DailyPayment::find($source_id);
                        $ref_no          = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url             = '/daily-payment';
                        $dir             = 'journal-voucher/';
                        $dir_back_right  = '/journal-voucher';
                        $dir_back_left   = '\journal-voucher';
                        break;
                case 'expense_voucher'://
                        $source          = \App\Models\GournalVoucher::find($source_id);
                        $ref_no          = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url             = '/gournal-voucher';
                        $dir             = 'expense-voucher/';
                        $dir_back_right  = '/expense-voucher';
                        $dir_back_left   = '\expense-voucher';
                        break; 
                case 'check'://
                        $source          = \App\Models\Check::find($source_id);
                        $ref_no          = $source->ref_no; $document_attach   = ($source->document != "[]" && $source->document != null)?$source->document:[] ; 
                        $url             = '/cheque'; 
                        $dir             = 'check/';
                        $dir_back_right  = '/check';
                        $dir_back_left   = '\check';
                        break;                    
                default:
                    $source            = null;
                    $ref_no            = ""; $document_attach   = [] ; 
                    $url               = "/";
                    $dir               = '';
                    break;

            }
            $company_name      = request()->session()->get("user_main.domain");
            if ($request->hasFile('attach')) {
                $count_doc1 = 1;
                $referencesNewStyle = str_replace('/', '-', $ref_no);
                foreach ($request->file('attach') as $k =>  $file) {
                    #................
                    if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                        if ($file->getSize() <= config('constants.document_size_limit')){ 
                            // if($file->getClientOriginalExtension() != "pdf"){
                                $file_name_m    =  time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                                $destinationPath = public_path('uploads/companies/'.$company_name.'/documents'.$dir_back_right);
                                if (!file_exists($destinationPath)) {
                                    mkdir($destinationPath, 0755, true);
                                }
                                $file->move('uploads/companies/'.$company_name.'/documents'.$dir_back_right,$file_name_m);
                                $file_name      =  'uploads/companies/'.$company_name.'/documents/'.$dir. $file_name_m;
                            // }else{

                            //     // Usage example
                            //     #............................................... Zero
                            //     // $file_name_m    =  time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                            //     // $inputFile      =  $file->getClientOriginalName();
                            //     // $outputFile     =  'uploads/companies/'.$company_name.'/documents/'.$dir. $file_name_m;
                            //     // $file_name      =  'uploads/companies/'.$company_name.'/documents/'.$dir. $file_name_m;
                                
                            //     // if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['attach'])) {
                            //     //     // Save the uploaded file to a temporary location
                            //     //     $uploadedFile    = $_FILES['attach']['tmp_name'][$k];
                            //     //     $destinationFile = $outputFile; // Path where you want to save the compressed PDF
                                
                            //     //     // Call the compress function
                            //     //     $this->compressPdf($uploadedFile, $destinationFile);
                                
                            //     //     // echo "PDF successfully compressed.";
                            //     // }
                            //     // $sourceFile      = $file;
                            //     // $destinationFile = $outputFile;
                            //     // $quality         = 75; // Compression quality (0-100)

                            //     // $this->compressPdf($sourceFile, $destinationFile, $quality);


                            //     #............................................... End Zero
                                
                                
                                
                            //     #............................................... First    
                            //     // return phpinfo();
                            //     // $sourceFile      = $file;
                            //     // $destinationFile = $outputFile;
                            //     // $quality         = 75; // Compression quality (0-100)
                            //     // $resolution      = 150; // DPI resolution
                            //     // $this->compressPdfWithImagick($sourceFile, $destinationFile, $quality, $resolution);
                            //     #............................................... End First    
                                
                            //     #............................................... Second    
                            //     // Ghostscript command to compress PDF
                            //     // $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/printer -dNOPAUSE -dQUIET -dBATCH -sOutputFile={$outputFile} {$inputFile}";
                            //     // $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . escapeshellarg($outputFile) . " " . escapeshellarg($inputFile);
                                
                            //     // $command = "gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -sOutputFile={$outputFile} {$inputFile}";
                            //     // $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen -dNOPAUSE -dQUIET -dBATCH "
                            //     //         . "-dColorImageDownsampleType=/Bicubic -dColorImageResolution=72 "
                            //     //         . "-dGrayImageDownsampleType=/Bicubic -dGrayImageResolution=72 "
                            //     //         . "-dMonoImageDownsampleType=/Subsample -dMonoImageResolution=72 "
                            //     //         . "-sOutputFile={$outputFile} {$inputFile}";
                            //     // Execute the command
                            //     // exec($command, $output, $return_var);
                            //     // if ($return_var === 0) {
                            //     //     echo "PDF successfully compressed.";
                            //     // } else {
                            //         //     echo "Error compressing PDF.";
                            //     // }
                            //     #............................................... End Second    
                            // }

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
                            $file_name =  'uploads/companies/'.$company_name.'/documents/'.$dir. $new_file_name;
                            // if ($imgs->save(public_path("uploads\companies\\$company_name\documents$dir_back_left\\$new_file_name"),20)) {
                            //     $uploaded_file_name = $new_file_name;
                            // }
                            $public_path = public_path('uploads/companies/'.$company_name.'/documents/'.$dir);
                            if (!file_exists($public_path)) {
                                mkdir($public_path, 0755, true);
                            }
                            // if ($imgs->save($public_path . $new_file_name)) {
                            //     $uploaded_file_name = $new_file_name;
                            // }
                            #............... new way 
                            // Usage example
                            $sources      = $file;
                            $destination  = $file_name;
                            $quality      = 99; // 0 (worst quality) to 100 (best quality)

                            if($Data[0] > $Data[1] ){
                                $maxWidth    = ($Data[0]>1024)?1024:$Data[0];
                                $maxHeight   = ($Data[1]>768)?768:$Data[1];
                            }else if( $Data[0] < $Data[1] ){
                                $maxHeight   = ($Data[1]>1024)?1024:$Data[1];
                                $maxWidth    = ($Data[0]>768)?768:$Data[0];
                            }else{
                                $maxHeight   = ($Data[1]>800)?800:$Data[1];
                                $maxWidth    = ($Data[0]>800)?800:$Data[0];
                            }
 

                            $this->commonUtil->compressImage($sources, $destination, $quality, $maxWidth, $maxHeight);

                        }else{
                            $output = [
                                "success" => 0,
                                "msg" => __('The Maximum Size Should Be ') . (floatVal(config('constants.document_size_limit'))/1000000) . " " . __('MB'),
                            ];
                            return redirect($url)->with("status",$output);
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
    # pdf    1
    public function compressPdfWithImagick($sourceFile, $destinationFile, $quality = 75, $resolution = 150) {
        // Create Imagick object
        $imagick = new \Imagick();
        $imagick->setResolution($resolution, $resolution);
        $imagick->readImage($sourceFile);
    
        // Compress each page
        foreach ($imagick as $page) {
            $page->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $page->setImageCompressionQuality($quality);
            $page->stripImage();  // Remove metadata
        }
    
        // Write the compressed PDF
        $imagick->setImageFormat('pdf');
        $imagick->writeImages($destinationFile, true);
    
        // Clean up
        $imagick->clear();
        $imagick->destroy();
    }

    # pdf    2
    // public function compressPdf($sourceFile, $destinationFile, $quality = 75) {
    //     $pdf = new FPDI();
    
    //     // Set compression to 1 (highest)
    //     $pdf->SetCompression(true);
    
    //     // Add a page for each page in the source PDF
    //     $pageCount = $pdf->setSourceFile($sourceFile);
    //     for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    //         $pdf->AddPage();
    //         $templateId = $pdf->importPage($pageNo);
    //         $pdf->useTemplate($templateId);
    
    //         // Reduce the quality of images
    //         $pdf->SetJPEGQuality($quality);
    //     }
    
    //     // Output the new PDF to the destination file
    //     $pdf->Output($destinationFile, 'F');
    // }
    public function compressPdf($sourceFile, $destinationFile, $quality = 75) {
        // $pdf = new FPDI();
    
        // // Set compression
        // $pdf->SetCompression(true);
    
        // // Set JPEG quality
        
        // // Read the source file
        // $pageCount = $pdf->setSourceFile($sourceFile);
        
        // // Import each page
        // for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        //     $pdf->AddPage();
        //     $templateId = $pdf->importPage($pageNo);
        //     $pdf->useTemplate($templateId);
        //     $pdf->setJPEGQuality($quality);
        // }
    
        // // Output the new PDF to the destination file
        // $pdf->Output($destinationFile, 'F');
            // $pdf = new TCPDF();
        
            // // Set document information
            // $pdf->SetCreator(PDF_CREATOR);
            // $pdf->SetAuthor('Author');
            // $pdf->SetTitle('Compressed PDF');
            // $pdf->SetSubject('PDF Compression');
            // $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        
            // // Set JPEG quality
            // $pdf->setJPEGQuality($quality);
        
            // // Add a page
            // $pdf->AddPage();
        
            // // Import the PDF document
            // $pageCount = $pdf->setSourceFile($sourceFile);
            // for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            //     $tplIdx = $pdf->importPage($pageNo);
            //     $pdf->useTemplate($tplIdx);
            // }
        
            // // Output the new PDF to the destination file
            // $pdf->Output($destinationFile, 'F');
    }

    # ..... image compress
    public function compressImage($source, $destination, $quality, $maxWidth, $maxHeight) {
        // Get image info
        $imageInfo = getimagesize($source);
        $mime = $imageInfo['mime'];
    
        // Create a new image from file
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            default:
                throw new \Exception('Unsupported image type.');
        }
    
        // Get original dimensions
        $width = imagesx($image);
        $height = imagesy($image);
    
        // Calculate new dimensions while maintaining aspect ratio
        $aspectRatio = $width / $height;
        if ($width > $height) {
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $aspectRatio;
        } else {
            $newWidth = $maxHeight * $aspectRatio;
            $newHeight = $maxHeight;
        }
    
        // Create a new true color image with the new dimensions
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
        // Copy and resize the old image into the new image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
        // Save the new image to the destination with the specified quality
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($newImage, $destination, $quality);
                break;
            case 'image/png':
                // PNG quality is 0 (no compression) to 9
                imagepng($newImage, $destination, floor($quality / 10));
                break;
            case 'image/gif':
                imagegif($newImage, $destination);
                break;
        }
    
        // Free up memory
        imagedestroy($image);
        imagedestroy($newImage);
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
                session(['lang' => $i]);
                // Determine the direction for the new locale
                $direction = ($i === 'ar') ? 'rtl' : 'ltr';
                session(['direction' => $direction]);
                session()->put('user.language', $i);
                \App::setLocale($i);
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
    public function changeLanguageApp(Request $request){
        try{

            if(request()->ajax()){
                $i    = request()->input('lang'); 
                DB::beginTransaction();
                session(["lang"=>$i]);
                \App::getLocale($i);
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
                Artisan::call('route:clear');
                Artisan::call('config:clear');
                DB::commit();
                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success')
                ];
                return $output;
            }
            DB::beginTransaction();
            $i                    = request()->input('lang'); 
            session(["lang"=>$i]);
            \App::getLocale($i);
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
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
    public function updateSessionHome(Request $request){
        try{

            if(request()->ajax()){ 
                DB::beginTransaction(); 

                $user              = \App\Models\User::find(\Auth::user()->id);
                $session           = \App\Models\SessionTable::where("user_id",\Auth::user()->id)->select()->first();
                if($session){
                    session(['device_id' => (string) \Str::uuid()]); 
                    $session->ip_address   = request()->ip();
                    $session->user_agent   = request()->header('user-agent') ;
                    $session->user_actives = request()->header('user-agent')."_".request()->ip()."_".$user->username."_".$user->id."_". session('device_id');
                    $session->update();
                    $user->login_token     = request()->header('user-agent')."_".request()->ip()."_".$user->username."_".$user->id."_". session('device_id');
                    $user->update();
                    request()->session()->forget('create_ses');
                    session()->forget('create_ses');
                }
                DB::commit();
                $output = [
                    'success' => true,
                    'msg'     => __('lang_v1.success')
                ];
                return [];
            }
            
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
