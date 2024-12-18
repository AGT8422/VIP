<?php

namespace App\Http\Controllers;

use App\Account;
use App\User;

use App\AccountTransaction;
use App\TransactionPayment;
use App\Utils\TransactionUtil;
use DB;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AccountReportsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil)
    { 
        $this->transactionUtil = $transactionUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    
    public function balanceSheet()
    {
        $user_id             = request()->session()->get('user.id');
        $user                = User::where('id', $user_id)->with(['media'])->first();
        $config_languages    = config('constants.langs');
        $languages           = []; 
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        $reportSetting       = \App\Models\ReportSetting::select("*")->first();
        if (!auth()->user()->can('account.access') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('SalesMan.views') ) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = session()->get('user.business_id');
        
        
        if (request()->ajax()) {
            
            $filters     = [];
            $business_details          = \App\Business::find($business_id);
            $start_date                = null;
            $location                  = \App\BusinessLocation::where("business_id",$business_id)->first();
            $s_date                    = \Carbon::createFromFormat($business_details->date_format, request()->input('end_date'))->year;
            $_date                     = \Carbon::createFromFormat($business_details->date_format, request()->input('end_date'));
            $end_date                  = !empty(request()->input('end_date')) ? $this->transactionUtil->uf_date(request()->input('end_date')) : \Carbon::now()->format('Y-m-d');
            $date_range                = request()->input('date_range');
          
            if (!empty($date_range)) {
                $date_range_array = explode('~', $date_range);
                $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
                $filters['end_date']   = $this->transactionUtil->uf_date(trim($date_range_array[1]));
                $start_date = $filters['start_date'];
                $end_date   = $filters['end_date'];
               
            } else {
                $filters['start_date'] = \Carbon::now()->startOfMonth()->format('Y-m-d');
                $filters['end_date']   = \Carbon::now()->endOfMonth()->format('Y-m-d');
                $start_date = $filters['start_date'];
                $start_date = ($s_date-1)."-1-1"; 
                $end_date   = $filters['end_date'];

            }
            $purchase_details = $this->transactionUtil->getPurchaseTotals(
                $business_id,
                 $start_date,
                $end_date,
                $user_id,
                $reportSetting
            );
            $sell_details = $this->transactionUtil->getSellTotals(
                $business_id,
                $start_date,
                $end_date,
                $user_id,
                $reportSetting
            );

            $transaction_types = ['sell_return'];

            $sell_return_details = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start_date,
                $end_date,
                $user_id,
                $reportSetting
            );
            
            $account_details_assets    = $this->getAccountBalanceAssets($business_id, $start_date,$end_date);
            $account_details_liability = $this->getAccountBalanceLiability($business_id, $start_date,$end_date);
            $account_name_assets       = $this->getAccountNameAssets($business_id, $start_date,$end_date);
            $account_name_liability    = $this->getAccountNameLiability($business_id, $start_date,$end_date);
            $account_number_assets     = $this->getAccountNumberAssets($business_id, $start_date,$end_date);
            $account_number_liability  = $this->getAccountNumberLiability($business_id, $start_date,$end_date);
            // $capital_account_details = $this->getAccountBalance($business_id, $end_date, 'capital');
            
       
             //Get Closing stock
            $closing_stock = $this->transactionUtil->getOpeningClosingStock(
                $business_id,
                $end_date,
                null
            );
            if (!empty($date_range)) {
                $start_date                = $start_date; 
                $end_date                  = $end_date;
            } else {
                $start_date                = ($s_date-1)."-1-1"; 
                $end_date                  = $_date->format("Y-m-d");
            }
            $closing                   = \App\Product::closing_stock($business_id);
            $currency_details          = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $data                      = $this->transactionUtil->getProfitLossDetails($business_id, $location->id, $start_date, $end_date,null,$reportSetting);

            $output = [
                'supplier_due'                    => $purchase_details['purchase_due'],
                'customer_due'                    => $sell_details['invoice_due'] - $sell_return_details['total_sell_return_inc_tax'],
                'account_details_assets'          => $account_details_assets   ,
                'account_details_liability'       => $account_details_liability,
                'account_name_assets'             => $account_name_assets      ,
                'account_name_liability'          => $account_name_liability   ,
                'account_number_assets'           => $account_number_assets    ,
                'account_number_liability'        => $account_number_liability ,
                'closing_stock'                   => $closing                  ,
                'capital_account_details'         => null                      ,
                'data'                            => $data                     ,
                'closing'                         => $closing
            ];

            return $output;
        }

        return view('account_reports.balance_sheet')->with(compact('languages'));
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function trialBalance()
    {
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        if (!auth()->user()->can('account.access') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('SalesMan.views')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id             = session()->get('user.business_id');
        $currency_details        = $this->transactionUtil->purchaseCurrencyDetails($business_id);
      
        if (request()->ajax()) {
            $business_details        = \App\Business::find($business_id);
            $s_date                  = \Carbon::createFromFormat($business_details->date_format, request()->input('end_date'))->year;
            if(!empty(request()->date)){
                $end_date = !empty(request()->input('end_date')) ? $this->transactionUtil->uf_date(request()->date) : \Carbon::now()->format('Y-m-d');
            }else{
                $end_date = !empty(request()->input('end_date')) ? $this->transactionUtil->uf_date(request()->input('end_date')) : \Carbon::now()->format('Y-m-d');
            }

            $date_range   = request()->input('date_range');
          
            if (!empty($date_range)) {
                $date_range_array = explode('~', $date_range);
                $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
                $filters['end_date']   = $this->transactionUtil->uf_date(trim($date_range_array[1]));
                $start_date            = $filters['start_date'];
                $end_date              = $filters['end_date'];
               
            } else {
                $filters['start_date'] = \Carbon::now()->startOfMonth()->format('Y-m-d');
                $filters['end_date']   = \Carbon::now()->endOfMonth()->format('Y-m-d');
                $start_date            = $filters['start_date'];
                $start_date            = ($s_date-1)."-1-1"; 
                $end_date              = $filters['end_date'];

            }
            $reportSetting  =  \App\Models\ReportSetting::select("*")->first();
            
            $purchase_details = $this->transactionUtil->getPurchaseTotals(
                $business_id,
                 $start_date,
                $end_date,
                NULL,
                $user_id,
                $reportSetting

            );
            //Get Purchase details
            $sell_details = $this->transactionUtil->getSellTotals(
                $business_id,
                $start_date,
                $end_date,
                NULL,
                $user_id,
                $reportSetting
            );  

            $account_details = $this->getAccountBalance($business_id,$start_date, $end_date);
            $account_name    = $this->getAccountName($business_id,$start_date, $end_date);
            $account_number  = $this->getAccountNumber($business_id,$start_date, $end_date);
            // $capital_account_details = $this->getAccountBalance($business_id, $end_date, 'capital');
        
            $output = [
                'supplier_due'     => $purchase_details['purchase_due'],
                'customer_due'     => $sell_details['invoice_due'],
                'account_balances' => $account_details,
                'account_name'     => $account_name,
                'account_number'   => $account_number,

                'capital_account_details' => null
            ];
             
            
            return DataTables::of($account_details)
                            ->addColumn("Main_name",function($row) use($account_number){
                                $account = \App\Account::where("id",$row->id)->first();
                                $account_sub = \App\AccountType::where("id",$account->account_type_id)->first();
                                if($account_sub != null){
                                    $account_main = \App\AccountType::where("id",$account_sub->sub_parent_id)->first();
                                }else{
                                    $account_main = null;
                                }
                                if(!empty($account_main)){
                                    $name = $account_main->name;
                                    $id   = $account_main->code;
                                    $html = "<div>$name   ( $id )</div>";
                                }else{
                                    if($account_sub != null){
                                         $account_main = \App\AccountType::where("id",$account_sub->parent_account_type_id)->first();
                                    
                                    }else{
                                        $account_main = null;
                                     }
                                    if(!empty($account_main)){
                                        $name = $account_main->name;
                                        $id   = $account_main->code;
                                        $html = "<div>$name  ( $id )</div>";
                                    }else{
                                        $name = "";
                                        $id   = "";
                                        $html = "";
                                    }
                                }
                                // $html = "<a href='".action('AccountController@show',[$id])."' target='_blank' >$name |||| $account_number[$id]</a>";
                               
                                return $html;    
                            })
                            ->addColumn("sub_name",function($row) use($account_number){
                                $account = \App\Account::where("id",$row->id)->first();
                                $account_main = \App\AccountType::where("id",$account->account_type_id)->first();
                                if(!empty($account_main)){
                                    $name = $account_main->name;
                                    $id   = $account_main->code;
                                    $html = "<div>$name   ( $id )</div>";
                                }else{
                                    $name = "";
                                    $id   = "";
                                    $html = "";
                                }
                                // $html = "<a href='".action('AccountController@show',[$id])."' target='_blank' >$name   $account_number[$id]</a>";
                                
                               
                                return $html;    
                            })
                            ->addColumn("name",function($row) use($account_number){
                                $name = $row->name;
                                $id   = $row->id;
                                $html = "<a href='".action('AccountController@show',[$id])."' target='_blank' >$name   ( $account_number[$id] )</a>";
                               
                                return $html;    
                            })
                            ->addColumn("credit",function($row){
                                
                                if($row->balance < 0){
                                    $balance = "";
                                }else{
                                    $balance = $row->balance;
                                }
                                return ($balance!="" && $balance != 0)?round($balance,4):"";    
                            })
                            ->addColumn("debit",function($row){
                                if($row->balance < 0){
                                    $balance = $row->balance*-1;
                                }else{
                                    $balance = "";
                                }
                                return ($balance!="" && $balance != 0)?round($balance,4):"";    
                            })
                            ->rawColumns(['id','name','debit','credit','Main_name','sub_name'])
                            ->make(true);


            // return $output;
        }
      
        return view('account_reports.trial_balance')->with(compact(["currency_details"]));
    }

 
    /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountBalance($business_id, $start_date,$end_date, $account_type = 'others')
    {
        $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
                // ->NotClosed()
                ->whereNull('AT.deleted_at')
                ->where('business_id', $business_id)
                ->where("accounts.cost_center",0)
                // ->where("AT.amount",">",0)
                ->whereNull("AT.for_repeat")
                ->whereDate('AT.operation_date', '<=', $end_date)
                ->whereDate('AT.operation_date', '>=', $start_date);
            
        // if ($account_type == 'others') {
        //    $query->NotCapital();
        // } elseif ($account_type == 'capital') {
        //     $query->where('account_type', 'capital');
        // }

        $account_details = $query->select(['name','accounts.id',
                                        DB::raw("SUM( IF(AT.type='credit' AND amount > 0,  amount ,  0 ) ) as balance_credit"),
                                        DB::raw("SUM( IF(AT.type='debit' AND amount > 0,  amount ,  0 ) ) as balance_debit"),
                                        DB::raw("SUM( IF(AT.type='credit' AND amount > 0, round(amount,4), round((-1*amount),4)) ) as balance")])
                                ->groupBy('accounts.id')
                                ->get();
                                // ->pluck('balance', 'id' );
         
        return $account_details;
    }
    /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountBalanceAssets($business_id,$start_date, $end_date, $account_type = 'others')
    {
        $id_liability = null ;
        $id_assets    = null ;
        $liability    = \App\Business::find($business_id);
        if(!empty($liability)){
            $id_liability = $liability->liability;
            $id_assets    = $liability->assets;
        }  
        $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
                // ->NotClosed()
                ->whereNull('AT.deleted_at')
                ->where('business_id', $business_id)
                ->whereNull("AT.for_repeat")
                // ->where("AT.amount",">",0)
                ->where("accounts.cost_center",0)
                ->whereHas('account_type',function($query) use($id_assets){
                    $query->where("parent_account_type_id",$id_assets);
                    $query->orWhereHas("parent_account",function($query) use($id_assets){
                        $query->where("parent_account_type_id",$id_assets);
                        $query->orWhere("sub_parent_id",$id_assets);
                    });
                })
                ->whereDate('AT.operation_date', '<=', $end_date)
                ->whereDate('AT.operation_date', '>=', $start_date);

        // if ($account_type == 'others') {
        //    $query->NotCapital();
        // } elseif ($account_type == 'capital') {
        //     $query->where('account_type', 'capital');
        // }

        $account_details = $query->select(['name','accounts.id',
                                        DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")])
                                ->groupBy('accounts.id')
                                ->get()
                                ->pluck('balance', 'id' );
                                
     
        return $account_details;
    }
    /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountBalanceLiability($business_id,$start_date, $end_date, $account_type = 'others')
    {
        $id_liability = null ;
        $id_assets    = null ;
        $liability    = \App\Business::find($business_id);
        if(!empty($liability)){
            $id_liability = $liability->liability;
            $id_assets    = $liability->assets;
        }  
        $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
                // ->NotClosed()
                ->whereNull('AT.deleted_at')
                ->where('business_id', $business_id)
                ->whereNull("AT.for_repeat")
                // ->where("AT.amount",">",0)
                ->where("accounts.cost_center",0)
                ->whereHas('account_type',function($query) use($id_liability){
                    $query->where("parent_account_type_id",$id_liability);
                     $query->orWhereHas("parent_account",function($query) use($id_liability){
                        $query->where("parent_account_type_id",$id_liability);
                         $query->orWhere("sub_parent_id",$id_liability);
                    });
                })
                ->whereDate('AT.operation_date', '<=', $end_date)
                ->whereDate('AT.operation_date', '>=', $start_date);

        // if ($account_type == 'others') {
        //    $query->NotCapital();
        // } elseif ($account_type == 'capital') {
        //     $query->where('account_type', 'capital');
        // }

        $account_details = $query->select(['name','accounts.id',
                                        DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")])
                                ->groupBy('accounts.id')
                                ->get()
                                ->pluck('balance', 'id' );
     
        return $account_details;
    }
    /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountNumber($business_id, $start_date,$end_date, $account_type = 'others')
    {
        $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
                // ->NotClosed()
                ->whereNull('AT.deleted_at')
                // ->where("AT.amount",">",0)
                ->where('business_id', $business_id)
                ->where("accounts.cost_center",0)
                ->whereNull("AT.for_repeat")
                ->whereDate('AT.operation_date', '<=', $end_date)
                ->whereDate('AT.operation_date', '>=', $start_date);

        // if ($account_type == 'others') {
        //    $query->NotCapital();
        // } elseif ($account_type == 'capital') {
        //     $query->where('account_type', 'capital');
        // }

        $account_details = $query->select(['name','accounts.account_number','accounts.id',
                                        DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")])
                                ->groupBy('accounts.id')
                                ->get()
                                ->pluck( 'account_number', 'id');
     
        return $account_details;
    }
    /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountNumberAssets($business_id, $start_date,$end_date, $account_type = 'others')
    {
        $id_liability = null ;
        $id_assets    = null ;
        $liability    = \App\Business::find($business_id);
        if(!empty($liability)){
            $id_liability = $liability->liability;
            $id_assets    = $liability->assets;
        }  
        $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
                // ->NotClosed()
                ->whereNull('AT.deleted_at')
                // ->where("AT.amount",">",0)
                ->whereNull("AT.for_repeat")
                ->where("accounts.cost_center",0)
                ->where('business_id', $business_id)
                ->whereHas('account_type',function($query) use($id_assets){
                    $query->where("parent_account_type_id",$id_assets);
                     $query->orWhereHas("parent_account",function($query) use($id_assets){
                        $query->where("parent_account_type_id",$id_assets);
                        $query->orWhere("sub_parent_id",$id_assets);
                    });
                })
                ->whereDate('AT.operation_date', '<=', $end_date)
                ->whereDate('AT.operation_date', '>=', $start_date);

        // if ($account_type == 'others') {
        //    $query->NotCapital();
        // } elseif ($account_type == 'capital') {
        //     $query->where('account_type', 'capital');
        // }

        $account_details = $query->select(['name','accounts.account_number','accounts.id',
                                        DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")])
                                ->groupBy('accounts.id')
                                ->get()
                                ->pluck( 'account_number', 'id');
     
        return $account_details;
    }
    /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountNumberLiability($business_id, $start_date,$end_date, $account_type = 'others')
    {
        $id_liability = null ;
        $id_assets    = null ;
        $liability    = \App\Business::find($business_id);
        if(!empty($liability)){
            $id_liability = $liability->liability;
            $id_assets    = $liability->assets;
        }  
        $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
                // ->NotClosed()
                ->whereNull('AT.deleted_at')
                // ->where("AT.amount",">",0)
                ->whereNull("AT.for_repeat")
                ->where("accounts.cost_center",0)
                ->where('business_id', $business_id)
                ->whereHas('account_type',function($query) use($id_liability){
                    $query->where("parent_account_type_id",$id_liability);
                     $query->orWhereHas("parent_account",function($query) use($id_liability){
                        $query->where("parent_account_type_id",$id_liability);
                        $query->orWhere("sub_parent_id",$id_liability);
                    });
                })
                ->whereDate('AT.operation_date', '<=', $end_date)
                ->whereDate('AT.operation_date', '>=', $start_date);

        // if ($account_type == 'others') {
        //    $query->NotCapital();
        // } elseif ($account_type == 'capital') {
        //     $query->where('account_type', 'capital');
        // }

        $account_details = $query->select(['name','accounts.account_number','accounts.id',
                                        DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")])
                                ->groupBy('accounts.id')
                                ->get()
                                ->pluck( 'account_number', 'id');
     
        return $account_details;
    }
    /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountName($business_id, $start_date,$end_date, $account_type = 'others')
    {
        $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
                // ->NotClosed()
                ->whereNull('AT.deleted_at')
                // ->where("AT.amount",">",0)
                ->where("accounts.cost_center",0)
                ->whereNull("AT.for_repeat")
                ->where('business_id', $business_id)
                ->whereDate('AT.operation_date', '<=', $end_date)
                ->whereDate('AT.operation_date', '>=', $start_date);
                
        // if ($account_type == 'others') {
        //    $query->NotCapital();
        // } elseif ($account_type == 'capital') {
        //     $query->where('account_type', 'capital');
        // }

        $account_details = $query->select(['name','accounts.id',  
                                        DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")])
                                ->groupBy('accounts.id')
                                ->get()
                                ->pluck('name', 'id' );
     
        return $account_details;
    }


        /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountNameLiability($business_id, $start_date,$end_date, $account_type = 'others')
    {
        $id_liability = null ;
        $id_assets    = null ;
        $liability    = \App\Business::find($business_id);
        if(!empty($liability)){
            $id_liability = $liability->liability;
            $id_assets    = $liability->assets;
        }  
         $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
                // ->NotClosed()
                ->whereNull('AT.deleted_at')
                // ->where("AT.amount",">",0)
                ->where("accounts.cost_center",0)
                ->whereNull("AT.for_repeat")
                ->where('business_id', $business_id)
                ->whereHas('account_type',function($query) use($id_liability){
                        $query->where("parent_account_type_id",$id_liability);
                        $query->orWhereHas("parent_account",function($query) use($id_liability){
                        $query->where("parent_account_type_id",$id_liability);
                        $query->orWhere("sub_parent_id",$id_liability);
                    });
                        
                })
                ->whereDate('AT.operation_date', '<=', $end_date)
                ->whereDate('AT.operation_date', '>=', $start_date);

        // if ($account_type == 'others') {
        //    $query->NotCapital();
        // } elseif ($account_type == 'capital') {
        //     $query->where('account_type', 'capital');
        // }

        $account_details = $query->select(['name','accounts.id',  
                                        DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")])
                                ->groupBy('accounts.id')
                                ->get()
                                ->pluck('name', 'id' );
     
        return $account_details;
    }
    /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountNameAssets($business_id, $start_date,$end_date, $account_type = 'others')
    {
        $id_liability = null ;
        $id_assets    = null ;
        $liability    = \App\Business::find($business_id);
        if(!empty($liability)){
            $id_liability = $liability->liability;
            $id_assets    = $liability->assets;
        }  
         $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
                // ->NotClosed()
                ->whereNull('AT.deleted_at')
                // ->where("AT.amount",">",0)
                ->where("accounts.cost_center",0)
                ->whereNull("AT.for_repeat")
                ->where('business_id', $business_id)
                ->whereHas('account_type',function($query) use($id_assets){
                        $query->where("parent_account_type_id",$id_assets);
                        $query->orWhereHas("parent_account",function($query) use($id_assets){
                        $query->where("parent_account_type_id",$id_assets);
                        $query->orWhere("sub_parent_id",$id_assets);
                    });
                })
                ->whereDate('AT.operation_date', '<=', $end_date)
                ->whereDate('AT.operation_date', '>=', $start_date);

        // if ($account_type == 'others') {
        //    $query->NotCapital();
        // } elseif ($account_type == 'capital') {
        //     $query->where('account_type', 'capital');
        // }

        $account_details = $query->select(['name','accounts.id',  
                                        DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")])
                                ->groupBy('accounts.id')
                                ->get()
                                ->pluck('name', 'id' );
     
        return $account_details;
    }

    /**
     * Displays payment account report.
     * @return Response
     */
    public function paymentAccountReport()
    {
         $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        if (!auth()->user()->can('account.access')  && !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('SalesMan.views')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');

        if (request()->ajax()) {
            $query = TransactionPayment::leftjoin(
                'transactions as T',
                'transaction_payments.transaction_id',
                '=',
                'T.id'
            )
                                    ->leftjoin('accounts as A', 'transaction_payments.account_id', '=', 'A.id')
                                    ->where('transaction_payments.business_id', $business_id)
                                    ->whereNull('transaction_payments.parent_id')
                                    ->where('transaction_payments.method', '!=', 'advance')
                                    ->select([
                                        'paid_on',
                                        'payment_ref_no',
                                        'T.ref_no',
                                        'T.invoice_no',
                                        'T.type',
                                        'T.id as transaction_id',
                                        'A.name as account_name',
                                        'A.account_number',
                                        'transaction_payments.id as payment_id',
                                        'transaction_payments.account_id'
                                    ]);

            $start_date = !empty(request()->input('start_date')) ? request()->input('start_date') : '';
            $end_date = !empty(request()->input('end_date')) ? request()->input('end_date') : '';

            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $account_id = !empty(request()->input('account_id')) ? request()->input('account_id') : '';

            if ($account_id == 'none') {
                $query->whereNull('account_id');
            } elseif (!empty($account_id)) {
                $query->where('account_id', $account_id);
            }

            return DataTables::of($query)
                    ->editColumn('paid_on', function ($row) {
                        return $this->transactionUtil->format_date($row->paid_on, true);
                    })
                    ->addColumn('action', function ($row) {
                        $action = '<button type="button" class="btn btn-info 
                        btn-xs btn-modal"
                        data-container=".view_modal" 
                        data-href="' . action('AccountReportsController@getLinkAccount', [$row->payment_id]). '">' . __('account.link_account') .'</button>';
                        
                        return $action;
                    })
                    ->addColumn('account', function ($row) {
                        $account = '';
                        if (!empty($row->account_id)) {
                            $account = $row->account_name . ' - ' . $row->account_number;
                        }
                        return $account;
                    })
                    ->addColumn('transaction_number', function ($row) {
                        $html = $row->ref_no;
                        if ($row->type == 'sell') {
                            $html = '<button type="button" class="btn btn-link btn-modal"
                                    data-href="' . action('SellController@show', [$row->transaction_id]) .'" data-container=".view_modal">' . $row->invoice_no . '</button>';
                        } elseif ($row->type == 'purchase') {
                            $html = '<button type="button" class="btn btn-link btn-modal"
                                    data-href="' . action('PurchaseController@show', [$row->transaction_id]) .'" data-container=".view_modal">' . $row->ref_no . '</button>';
                        }
                        return $html;
                    })
                    ->editColumn('type', function ($row) {
                        $type = $row->type;
                        if ($row->type == 'sell') {
                            $type = __('sale.sale');
                        } elseif ($row->type == 'purchase') {
                            $type = __('lang_v1.purchase');
                        } elseif ($row->type == 'expense') {
                            $type = __('lang_v1.expense');
                        }
                        return $type;
                    })
                    ->filterColumn('account', function ($query, $keyword) {
                        $query->where('A.name', 'like', ["%{$keyword}%"])
                            ->orWhere('account_number', 'like', ["%{$keyword}%"]);
                    })
                    ->filterColumn('transaction_number', function ($query, $keyword) {
                        $query->where('T.invoice_no', 'like', ["%{$keyword}%"])
                            ->orWhere('T.ref_no', 'like', ["%{$keyword}%"]);
                    })
                    ->rawColumns(['action', 'transaction_number'])
                    ->make(true);
        }

        $accounts = Account::forDropdown($business_id, false);
        $accounts = ['' => __('messages.all'), 'none' => __('lang_v1.none')] + $accounts;
        
        return view('account_reports.payment_account_report')
                ->with(compact('languages','accounts'));
    }

    /**
     * Shows form to link account with a payment.
     * @return Response
     */
    public function getLinkAccount($id)
    {
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            $payment = TransactionPayment::where('business_id', $business_id)->findOrFail($id);
            $accounts = Account::forDropdown($business_id, false);

            return view('account_reports.link_account_modal')
                ->with(compact('languages','accounts', 'payment'));
        }
    }

    /**
     * Links account with a payment.
     * @param  Request $request
     * @return Response
     */
    public function postLinkAccount(Request $request)
    {
         
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = session()->get('user.business_id');
            if (request()->ajax()) {
                $payment_id = $request->input('transaction_payment_id');
                $account_id = $request->input('account_id');

                $payment = TransactionPayment::with(['transaction'])->where('business_id', $business_id)->findOrFail($payment_id);
                $payment->account_id = $account_id;
                $payment->save();

                $payment_type = !empty($payment->transaction->type) ? $payment->transaction->type : null;
                if (empty($payment_type)) {
                    $child_payment = TransactionPayment::where('parent_id', $payment->id)->first();
                    $payment_type = !empty($child_payment->transaction->type) ? $child_payment->transaction->type : null;
                }

                AccountTransaction::updateAccountTransaction($payment, $payment_type);
            }
            $output = ['success' => true,
                            'msg' => __("account.account_linked_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }
}
