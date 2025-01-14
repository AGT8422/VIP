<?php

namespace App\Http\Controllers;

use App\Account;
use App\User;
use App\AccountTransaction;
use App\Utils\TransactionUtil;
use App\AccountType;
use App\TransactionPayment;
use App\Utils\Util;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Media;

class AccountController extends Controller
{
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil , TransactionUtil $transactionUtil)
    {
      
        $this->transactionUtil = $transactionUtil;
        $this->commonUtil = $commonUtil;
    }

   /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        // 
        // <button data-href="{{action(\'AccountController@getFundTransfer\',[$id])}}" class="btn btn-xs btn-info btn-modal" data-container=".view_modal"><i class="fa fa-exchange"></i> @lang("account.fund_transfer")</button>
        // <button data-href="{{action(\'AccountController@getDeposit\',[$id])}}" class="btn btn-xs btn-success btn-modal" data-container=".view_modal"><i class="fas fa-money-bill-alt"></i> @lang("account.deposit")</button>
     
        if ( !auth()->user()->can('account.access') ) {
            abort(403, 'Unauthorized action.');
        }
        $databaseName     =  request()->session()->get("user_main.database");
        $business_id      = session()->get('user.business_id');
        $user_id          = request()->session()->get('user.id');
        $account_         = Account::Where("business_id",$business_id)->where("cost_center",0)->pluck('name','id');
        $account_number   = Account::Where("business_id",$business_id)->where("cost_center",0)->pluck('account_number','account_number');
        $user             = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        
        if (request()->ajax()) {
            // leftJoin('account_transactions as AT','accounts.id', '=', 'AT.account_id')
            $start_date          = request()->input('start_date');
            $end_date            = request()->input('end_date');

            $accounts = Account::leftJoin( 'account_types as ats', 'accounts.account_type_id', '=', 'ats.id' )
                                ->leftJoin( 'account_types as pat', 'ats.parent_account_type_id', '=', 'pat.id' )
                                ->leftJoin( 'account_types as pat_sub', 'ats.sub_parent_id', '=', 'pat_sub.id' )
                                ->leftJoin('users AS u', 'accounts.created_by', '=', 'u.id')
                                ->where('accounts.business_id', $business_id)
                                ->where('accounts.cost_center', 0)
                                // ->whereNull('AT.for_repeat')
                                // ->whereNull('AT.deleted_at')
                                ->select([
                                    'accounts.name', 
                                    'accounts.account_number', 
                                    'accounts.balance', 
                                    'accounts.note', 
                                    'accounts.id', 
                                    'accounts.account_type_id',
                                    'ats.name as account_type_name',
                                    'ats.id as account_type_id_',
                                    'pat_sub.name as sub_parent_name',
                                    'pat.name as parent_account_type_name',
                                    'is_closed', 
                                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                                    // DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance"),
                                ])
                                ->groupBy('accounts.id');

                $is_closed = request()->input('account_status') == 'closed' ? 1 : 0;
                $accounts->where('is_closed', $is_closed);
                // if (!empty($start_date) && !empty($end_date)) {
                //     // $accounts->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date]);
                // } 
                
                if( request()->input('account_name') != null){
                    $accounts->where('accounts.id', request()->input('account_name'));
                }

                if( request()->input('account_number') != null ){
                    $accounts->where('accounts.account_number', request()->input('account_number'));
                }
                
                 

                
                if( request()->input('main_account') != null ){
                    // $databaseName     =  request()->session()->get("user_main.database"); if("izo26102024_esai" == Config::get('database.connections.mysql.database')){
                        $idMain   = request()->input('main_account');
                        $allChild = \App\AccountType::where('parent_account_type_id',$idMain)->pluck("id");
                        $accounts->whereHas('account_type',function($query) use($idMain,$allChild){
                            $query->where("id",$idMain);
                            $query->orWhere("parent_account_type_id",$idMain);
                            $query->orWhereIn("parent_account_type_id",$allChild);
                        });
                    // }   
                }

                if( request()->input('account_type') != null ){
                    $idSubMain   = request()->input('account_type');
                    $allChildSub = \App\AccountType::where('parent_account_type_id',$idSubMain)->pluck("id");
                    $accounts->whereHas('account_type',function($query) use($idSubMain,$allChildSub){
                        $query->where("id",$idSubMain);
                        $query->orWhere("parent_account_type_id",$idSubMain);
                        $query->orWhereIn("parent_account_type_id",$allChildSub);
                    });
                }
                
                // if( request()->input('account_sub_type') != null  ){
                //     $idSubMain   = request()->input('account_sub_type');
                //     $allChildSub = \App\AccountType::where('parent_account_type_id',$idSubMain)->pluck("id");
                //     $accounts->whereHas('account_type',function($query) use($idSubMain,$allChildSub){
                //         $query->where("id",$idSubMain);
                //         $query->orWhere("parent_account_type_id",$idSubMain);
                //         $query->orWhereIn("parent_account_type_id",$allChildSub);
                //     });
                // }
                 
                return DataTables::of($accounts)
                        ->addColumn(
                            'action',
                            '@can("account.update")
                                <button data-href="{{action(\'AccountController@edit\',[$id])}}" data-container=".account_model" class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                            @endcan
                            @can("account.create")
                                
                                <a href="{{action(\'AccountController@show\',[$id])}}" class="  btn btn-warning btn-xs"><i class="fa fa-book"></i> @lang("account.account_book")</a>
                                &nbsp;
                            @endcan
                            @can("account.create")
                              
                                @if($is_closed == 0)
                                    <button data-url="{{action(\'AccountController@close\',[$id])}}" class="btn btn-xs btn-danger close_account"><i class="fa fa-power-off"></i> @lang("messages.close")</button>
                                    <button data-href="{{action(\'AccountController@getOneAccountBalance\',[$id])}}" data-id="{{$id}}"  data-container="#account_type_modal" class="hide btn btn-modal btn-xs btn-info one_account_balance"><i class="fa fa-money"></i> @lang("lang_v1.balance")</button>
                                @elseif($is_closed == 1)
                                    <button data-url="{{action(\'AccountController@activate\',[$id])}}" class="btn btn-xs btn-success activate_account"><i class="fa fa-power-off"></i> @lang("messages.activate")</button>
                                @endif
                            @endcan'
                        )
                        ->editColumn('name', function ($row) {
                            
                            $name =  ($row->contact)?$row->contact:$row->name;
                            if ($row->is_closed == 1) {
                                return $name . ' <small class="label pull-right bg-red no-print">' . __("account.closed") . '</small><span class="print_section">(' . __("account.closed") . ')</span>';
                            } else {
                                return $name;
                            }
                        })
                        ->addColumn('balance_final', function ($row) {
                            $blc_account = $row->balance; 
                            return  '<span class="display_currency" data-id="'.$row->id.'"   data-currency_symbol="true" >'.abs($blc_account).'</span> '    ;
                        })
                        ->addColumn('type', function ($row) { 
                            $blc_account = $row->balance; 
                            $type        = ($blc_account>0)?"Debit":(($blc_account!=0)?"Credit":"");
                            return  $type   ;
                        })  
                        ->editColumn('balance', function ($row) {
                            return '<span class="balance_rows" data-id="'.$row->id.'"  ><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>'  ;
                        }) 
                        ->editColumn('account_type', function ($row) {
                            $account_type = '';
                            if (!empty($row->account_type->parent_account)) {
                                $account_type .= $row->account_type->parent_account->name . ' - ';
                            }
                            if (!empty($row->account_type)) {
                                $account_type .= $row->account_type->name;
                            }
                            return $account_type;
                        })
                        ->editColumn('parent_account_type_name', function ($row) {
                            $parent_account_type_name = empty($row->parent_account_type_name) ? $row->account_type_name : $row->parent_account_type_name;
                            return $parent_account_type_name;
                        })
                        ->editColumn('sub_parent_name', function ($row) {
                            $parent_account_type_name = empty($row->sub_parent_name) ? $row->account_type_name : $row->sub_parent_name;
                            return $parent_account_type_name;
                        })
                        ->editColumn('account_type_name', function ($row) {
                            $account_type_name = empty($row->parent_account_type_name) ? '' : $row->account_type_name;
                            return $account_type_name;
                        })
                        ->addColumn('balance_with_date', function ($row) {
                        })
                        ->removeColumn('id')
                        ->removeColumn('is_closed')
                        ->rawColumns(['action','balance_final' ,  'balance', 'name','type',"sub_parent_name","balance_with_date"])
                        ->make(true);
        }

        $not_linked_payments = TransactionPayment::leftjoin(
                                        'transactions as T',
                                        'transaction_payments.transaction_id',
                                        '=',
                                        'T.id'
                                    )
                                    ->whereNull('transaction_payments.parent_id')
                                    ->where('method', '!=', 'advance')
                                    ->where('transaction_payments.business_id', $business_id)
                                    ->whereNull('account_id')
                                    ->count();

        // $capital_account_count = Account::where('business_id', $business_id)
        //                             ->NotClosed()
        //                             ->where('account_type', 'capital')
        //                             ->count();

        $account_types = AccountType::where('business_id', $business_id)
                                     ->whereNull('parent_account_type_id')
                                     ->with(['sub_types'])
                                     ->get();
        //.............. eb 
        $array_of_type             = [];
        $array_of_type_            = [];
        $array_of_type_sub         = [];
        $array_of_main_            = [];
        $array_of_sub_type_        = [];
        $array_of_sub_type_account = [];
        $account_types_sub         = AccountType::where('business_id', $business_id)
                                                ->whereNotNull('parent_account_type_id')
                                                ->whereHas('sub_types',function($query){
                                                        $query->whereNotNull('parent_account_type_id');
                                                })
                                              ->with(['sub_types_id'])
                                              ->get();
        $main_account              =  AccountType::where('business_id', $business_id)
                                              ->whereNull('parent_account_type_id')
                                              ->whereNull('sub_parent_id')
                                              ->get();
        $account_types_sub_all     = AccountType::where('business_id', $business_id)
                                              ->whereNotNull('parent_account_type_id')
                                              ->whereHas('sub_types',function($query){
                                                        $query->whereNotNull('parent_account_type_id');
                                                })
                                              ->get();
        foreach ($account_types_sub as $key => $value) {
            $array_of_type[$value->id] = $value->sub_parent_id."&".$value->name." /".$value->code;
        }                    

        foreach ($account_types_sub as $key => $value) {
            $array_of_type_sub[$value->id] =  $value->code . " || " . $value->name;
        }                    
        
        foreach ($account_types_sub_all as $key => $value) {
            $array_of_type_[$value->id] =   $value->name;
        }   

        foreach ($main_account as $key => $value) {
            $array_of_main_[$value->id] =   $value->code . " || " . $value->name;
        }          
        // foreach ($account_types_sub_all as $key => $value) {
        //     $array_of_type_[$value->id] =  $value->name;
        //     if($value->sub_parent_id != null){
        //         foreach ($array_of_type_ as $key => $values) {
        //             if($value->sub_parent_id == $key){
        //                     $array_of_sub_type_[$key]  = $values;
        //             }
        //         }
        //     }
        // }  
        foreach ($account_types_sub_all as $key => $value) {
            $array_of_type_[$value->id] = $value->code . " || " . $value->name;
            if($value->parent_account_type_id != null){
                $array_of_sub_type_account[$value->id] = $value->name;
            }
        }  
        
        $array_ids          = [];
        $allType            = \App\AccountType::where("business_id",$business_id)->orderBy("code","asc")->get();
        $allTypeParent      = \App\AccountType::where("business_id",$business_id)->whereNull("parent_account_type_id")->orderBy("code","asc")->get();
        $allTypeSubP        = \App\AccountType::where("business_id",$business_id)->whereNotNull("parent_account_type_id")->whereNull("sub_parent_id")->get();
        $allTypeSub         = \App\AccountType::where("business_id",$business_id)->whereNotNull("sub_parent_id")->get();
        
        foreach($allType as $item){
            $array_ids[$item->id]       =  $item ;
            $check                      =  $item->parent_account_type_id;
            while($check != null){
                $account                =  \App\AccountType::find($check);
                $array_ids[$item->id]   =  $item ;
                $check                  =  $account->parent_account_type_id;
               
            } 
         }
           
        //.......................eb 27-12-2022

        return view('account.index')
                ->with(compact('languages' ,"account_",'allType' ,'account_number' ,'array_of_sub_type_account', 'array_of_main_','array_of_type' ,'array_of_type_sub', 'array_of_type_' ,'currency_details'  ,'not_linked_payments', 'account_types','array_ids'));
    }

    public function Parent($item,$array) {
        $account              = \App\AccountType::find($item->sub_parent_id);
        $array[$account->id]  = $account;
        $check = $this->checkP($account);
        while( $check["status"] ){
            $act   = \App\AccountType::find($check["id"]->id);
            $array[$act->id]  = $act;
            $check = $this->checkP($act);
        }
        return $array ;
    }

    public function checkP($account){
        if($account->sub_parent_id != null){
            $accounts = \App\AccountType::find($account->sub_parent_id);
            $data = [
                    "status"=>true,
                    "id"=>$accounts
            ];
            return $data;
        }elseif($account->parent_account_type_id != null){
            $accounts = \App\AccountType::find($account->parent_account_type_id);
            $data = [
                    "status"=>true,
                    "id"=>$accounts
            ];
            return $data;
        
        }else{
            $data = [
                    "status"=>false,
                    "id"=>null
            ];
            return $data;
        }
    }
    
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        if (!auth()->user()->can('account.access') ) {
            abort(403, 'Unauthorized action.');
        }
        
        

        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }

        $business_id = session()->get('user.business_id');
        $account_types = AccountType::where('business_id', $business_id)
                                     ->whereNull('parent_account_type_id')
                                     ->with(['sub_types'])
                                     ->get();
        //.............. eb 
        $array_of_type = [];
        $array_of_type_ = [];
        $array_of_type_last = [];
        $array_of_type_id = [];
        $account_types_sub = AccountType::where('business_id', $business_id)
                                     ->whereNotNull('parent_account_type_id')
                                     ->whereNotNull('sub_parent_id')
                                     ->with(['sub_types_id'])
                                     ->get();
        
         foreach ($account_types_sub as $key => $value) {
            $array_of_type[$key] = $value->name;
            $array_of_type_[$key] = $value->sub_parent_id;
            $array_of_type_id[$key] = $value->id;
         }   
         
         $account_types_all = AccountType::where('business_id', $business_id)
                                     ->with(['sub_types'])
                                     ->get();
        $account_loop =  $this->loop($account_types_all);  
        
        $type = [];                     
        foreach($account_types_all as $i){
            $type[$i->id] = $i->name;                     
        }
        $list_a  = [];
        foreach($account_loop as  $key => $i ){
            $account = \App\AccountType::find($key);
            $list_a[$key] =  $account->name;
            if(!is_string($i) && $i!=null){
                $array  =  $this->list($i,$list_a);
                $list_a =  $array;
            } 
        }
        // $account_types_i = AccountType::where('business_id', $business_id)->get();

        // $array_level_one = [];
        // foreach($account_types_i as $it){
        //         if($it->parent_account_type_id == null){
        //             $array_level_one[]  =  $it->id;
        //         }
        // }

        // $array_level_two = [];
        // foreach($account_types_i as $it){
        //         if($it->parent_account_type_id != null && $it->sub_parent_id == null){
        //             $array_level_two[]  =  $it->id;
        //         }
        // }

        // $array_level_tree = [];
        // foreach($account_types_i as $it){
        //         if(  $it->sub_parent_id != null){
        //             $array_level_tree[]  =  $it->id;
        //         }
        // }

       
        //.......................eb 27-12-2022

         
        return view('account.create')
                ->with(compact('languages','array_of_type','account_types_all','type','list_a','array_of_type_id','array_of_type_','account_types'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('account.create') ) {
            abort(403, 'Unauthorized action.');
        }
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
        if (request()->ajax()) {
            try {
                $input                = $request->only(['name', 'account_number', 'note', 'account_type_id']);
                $business_id          = $request->session()->get('user.business_id');
                $user_id              = $request->session()->get('user.id');
                $input['business_id'] = $business_id;
                $input['created_by']  = $user_id;
               
                $account = Account::create($input);

                //Opening Balance
                $opening_bal = $request->input('opening_balance');

                if (!empty($opening_bal)) {
                    $ob_transaction_data = [
                        'amount'         => $this->commonUtil->num_uf($opening_bal),
                        'account_id'     => $account->id,
                        'type'           => 'credit',
                        'sub_type'       => 'opening_balance',
                        'operation_date' => \Carbon::now(),
                        'created_by'     => $user_id
                    ];

                    AccountTransaction::createAccountTransaction($ob_transaction_data);
                }
                AccountType::where("id",$input["account_type_id"])->update([
                   "active" => "1", 
                ]);
                $output = ['success' => true,
                            'msg' => __("account.account_created_success")
                        ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                            ];
            }
            return $output;
        }else{
            try {
                $input = $request->only(['name', 'account_number', 'note', 'account_type_id']);
                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $input['business_id'] = $business_id;
                $input['created_by']  = $user_id;
               
                $account = Account::create($input);

                //Opening Balance
                $opening_bal = $request->input('opening_balance');

                if (!empty($opening_bal)) {
                    $ob_transaction_data = [
                        'amount' =>$this->commonUtil->num_uf($opening_bal),
                        'account_id' => $account->id,
                        'type' => 'credit',
                        'sub_type' => 'opening_balance',
                        'operation_date' => \Carbon::now(),
                        'created_by' => $user_id
                    ];

                    AccountTransaction::createAccountTransaction($ob_transaction_data);
                }
                 AccountType::where("id",$input["account_type_id"])->update([
                   "active" => "1", 
                ]);
                $output = ['success' => true,
                            'msg' => __("account.account_created_success")
                        ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                            ];
            }
            return redirect()->back()->with("status",$output);
        }   
    }

   /**
     * Show the specified resource.
     * @return Response
     */
     
    // public function show($id){
      
    //     $user_id = request()->session()->get('user.id');
    //     $user = User::where('id', $user_id)->with(['media'])->first();
    //     $config_languages = config('constants.langs');
    //     $languages = [];
    //     foreach ($config_languages as $key => $value) {
    //         $languages[$key] = $value['full_name'];
    //     }
    //     if (!auth()->user()->can('account.access') &&   !auth()->user()->can('SalesMan.views')&& !auth()->user()->can('ReadOnly.views')&& !auth()->user()->can('admin_supervisor.views')&& !auth()->user()->can('manufuctoring.views') && !auth()->user()->can('warehouse.views')) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     $business_id = request()->session()->get('user.business_id');
    //     $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

       
    //     if (request()->ajax()) {
    //         $balance_amount = 0;

    //         $accounts = AccountTransaction::join(
    //             'accounts as A',
    //             'account_transactions.account_id',
    //             '=',
    //             'A.id'
    //         )
    //         ->leftJoin('transaction_payments AS tp', 'account_transactions.transaction_payment_id', '=', 'tp.id')
    //         ->leftJoin('users AS u', 'account_transactions.created_by', '=', 'u.id')
    //         ->leftJoin('contacts AS c', 'tp.payment_for', '=', 'c.id')
    //         ->where('A.business_id', $business_id)
    //         ->where('A.id', $id)
    //         ->with(['transaction', 'transaction.contact', 'transfer_transaction', 'media', 'transfer_transaction.media'])
    //         ->select(['account_transactions.type','account_transactions.account_id' ,'account_transactions.payment_voucher_id','account_transactions.created_by','account_transactions.check_id','account_transactions.gournal_voucher_item_id','account_transactions.daily_payment_item_id', 'account_transactions.amount','account_transactions.for_repeat', 'operation_date',
    //             'sub_type', 'transfer_transaction_id',
    //             DB::raw('(SELECT SUM(IF(AT.type="credit", AT.amount, -1 * AT.amount)) from account_transactions as AT WHERE 
    //             AT.operation_date <= account_transactions.operation_date AND AT.account_id  =account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id
    //                 <= account_transactions.id) as balance'),
    //             'account_transactions.transaction_id',
    //             'account_transactions.id',
    //             'account_transactions.note',
    //             'tp.is_advance',
    //             'tp.payment_ref_no',
    //             'c.name as payment_for',
    //             DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
    //             ])
    //             ->orderBy('account_transactions.operation_date', 'asc')
    //             ->orderBy('account_transactions.id', 'asc')
    //             ->groupBy('account_transactions.id') 
    //             ;
    //         if (!empty(request()->input('type'))) {
    //             $accounts->where('account_transactions.type', request()->input('type'));
    //         }

    //         $start_date = request()->input('start_date');
    //         $end_date = request()->input('end_date');
            
    //         if (!empty($start_date) && !empty($end_date)) {
    //             $accounts->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date]);
    //         }
    //         if(!empty(request()->input('check_box'))){
    //             $check_box = request()->input('check_box');
    //         }else{
    //             $check_box = null;
    //         }
    //         $accounts->where("for_repeat","=",null);
            
            
    //         $accounts->where("account_transactions.amount",">",0);
    //         $accounts->orderBy("account_transactions.operation_date","asc");
    //         $id_first = null;
           
    //         foreach($accounts->get() as $it){
    //             $id_first = $it->id;
    //             break;
    //         }
    //         //...... cost center section 
    //         $cost_center =\App\AccountTransaction::whereHas("account",function($query) {
    //                                             $query->where("cost_center",1);
    //                                         })->select("*")->get();
    //         $Ccenter          = null;                                          
    //         $id_account       = [];                                          
    //         $allDataFilter    = [];                                          
    //         foreach($accounts->get() as $i){
    //             foreach($cost_center as $it){
    //                 // dd(request());
    //                 if($it->transaction_id == $i->transaction_id && $i->transaction_id != null ){
    //                     $id_account[$i->id] = $it->id;
    //                      if(!empty(request()->input('cost_center'))){
    //                         $cost_center_id = request()->input('cost_center');
    //                         if($it->account_id == $cost_center_id){
    //                             $allDataFilter[] = $i->id;
    //                         }
    //                     } 
    //                     break;
    //                 }elseif($it->payment_voucher_id  == $i->payment_voucher_id && $i->payment_voucher_id != null){
    //                     $id_account[$i->id] = $it->id;
    //                      if(!empty(request()->input('cost_center'))){
    //                         $cost_center_id = request()->input('cost_center');
    //                         if($it->account_id == $cost_center_id){
    //                             $allDataFilter[] = $i->id;
    //                         }
    //                     } 
    //                     break;
    //                 }elseif($it->daily_payment_item_id  == $i->daily_payment_item_id && $i->daily_payment_item_id != null){
    //                     $id_account[$i->id] = $it->id;
    //                      if(!empty(request()->input('cost_center'))){
    //                         $cost_center_id = request()->input('cost_center');
    //                         if($it->account_id == $cost_center_id){
    //                             $allDataFilter[] = $i->id;
    //                         }
    //                     } 
    //                     break;
    //                 }elseif($it->gournal_voucher_item_id  == $i->gournal_voucher_item_id && $i->gournal_voucher_item_id != null){
    //                     $id_account[$i->id] = $it->id;
    //                      if(!empty(request()->input('cost_center'))){
    //                         $cost_center_id = request()->input('cost_center');
    //                         if($it->account_id == $cost_center_id){
    //                             $allDataFilter[] = $i->id;
    //                         }
    //                     } 
    //                     break;
    //                 }elseif($it->purchase_line_id  == $i->purchase_line_id && $i->purchase_line_id != null){
    //                     $id_account[$i->id] = $it->id;
    //                      if(!empty(request()->input('cost_center'))){
    //                         $cost_center_id = request()->input('cost_center');
    //                         if($it->account_id == $cost_center_id){
    //                             $allDataFilter[] = $i->id;
    //                         }
    //                     } 
    //                     break;
    //                 }elseif($it->additional_shipping_item_id  == $i->additional_shipping_item_id && $i->additional_shipping_item_id != null){
    //                     $id_account[$i->id] = $it->id;
    //                      if(!empty(request()->input('cost_center'))){
    //                         $cost_center_id = request()->input('cost_center');
    //                         if($it->account_id == $cost_center_id){
    //                             $allDataFilter[] = $i->id;
    //                         }
    //                     } 
    //                     break;
    //                 }elseif($it->transaction_sell_line_id  == $i->transaction_sell_line_id && $i->transaction_sell_line_id != null){
    //                     $id_account[$i->id] = $it->id;
    //                      if(!empty(request()->input('cost_center'))){
    //                         $cost_center_id = request()->input('cost_center');
    //                         if($it->account_id == $cost_center_id){
    //                             $allDataFilter[] = $i->id;
    //                         }
    //                     } 
    //                     break;
    //                 }elseif($it->return_transaction_id  == $i->return_transaction_id && $i->return_transaction_id != null){
    //                     $id_account[$i->id] = $it->id;
    //                      if(!empty(request()->input('cost_center'))){
    //                         $cost_center_id = request()->input('cost_center');
    //                         if($it->account_id == $cost_center_id){
    //                             $allDataFilter[] = $i->id;
    //                         }
    //                     } 
    //                     break;
    //                 }
    //             }
    //         }
               
    //         if(!empty(request()->input('cost_center'))){
    //             $accounts->whereIn("account_transactions.id",$allDataFilter)->get();
    //         }
    //         $rows_count = 1;
    //         $os_credit  = 0;  
    //         $os_debit   = 0;
            
    //         return DataTables::of($accounts)
    //                         ->addColumn('ref_no', function ($row) use($os_credit,$os_debit){
    //                             $ref_no =  ($row->transaction)?$row->transaction->ref_no.' '.$row->transaction->invoice_no:$row->transaction_id;
    //                             if ($row->transaction) {
    //                                 if ($row->transaction->ref_no) {
    //                                   return '<a href="#" data-href="'.url('purchases/'.$row->transaction_id).'" class="btn-modal" data-container=".view_modal">'.$ref_no.'</a>';
    //                               }else{
    //                                   return '<a href="#" data-href="'.url('sells/'.$row->transaction_id).'" class="btn-modal" data-container=".view_modal">'.$ref_no.'</a>';
    //                               }
    //                             }else{
    //                                 return '<a href="#" data-href="'.url('account/account-ref/'.$row->id).'" class="btn-modal" data-container=".view_modal">'.$row->parent_ref.'</a>';
    //                             }
                                
    //                         })
    //                         ->addColumn('debit', function ($row) {
    //                             if ($row->type == 'debit' ) {
    //                                 return '<span class="display_currency" data-currency_symbol="true">' . $row->amount . '</span>';
    //                             }
    //                             return '';
    //                         })
    //                         ->addColumn('credit', function ($row) {
    //                             if ($row->type == 'credit' ) {
    //                                 // class="display_currency" data-currency_symbol="true"
    //                                 return '<span class="display_currency" data-currency_symbol="true">' . $row->amount . '</span>';
    //                             }
    //                             return '';
    //                         })
    //                         ->addColumn('cost_center',function($row) use($id_account) {
                           
    //                             if( isset($id_account[$row->id]) ){
    //                                 $id = $id_account[$row->id];
    //                                 $cost_center     = \App\AccountTransaction::find($id);
    //                                 $name_of_account = $cost_center->account->name;
    //                             }else{
    //                                 $name_of_account = "";
    //                             }
    //                             return $name_of_account;
    //                         })
    //                         ->editColumn('balance', function ($row) use($accounts,$check_box,$rows_count,$id_first,$balance_amount) {

    //                                 $os_debit   = \App\AccountTransaction::where(function($q) use($row,$check_box,$rows_count,$id_first){
    //                                                     $q->where('account_id',$row->account_id);
    //                                                     if($check_box!=null){
    //                                                         if($id_first == $row->id){
    //                                                             $q->where('id','=',$row->id); 
    //                                                         }else{
    //                                                             $q->where('id','<=',$row->id); 
    //                                                             $q->where('id','>=',$id_first); 

    //                                                         }
    //                                                     }else{
    //                                                         $q->where('id','<=',$row->id); 
    //                                                     }
    //                                                     $q->where("for_repeat","=",null); 
    //                                                     $q->where('operation_date','<=',$row->operation_date);
    //                                                     $q->where('type','debit');
    //                                                 })->orWhere(function($q) use($row){
    //                                                         $q->where('account_id',$row->account_id);
    //                                                         $q->where("for_repeat","=",null); 
    //                                                         $q->where('id','>',$row->id); 
    //                                                         $q->where('operation_date','<',$row->operation_date);
    //                                                         $q->where('type','debit');
    //                                                 })->sum('amount');
              
    //                                 $os_credit  = \App\AccountTransaction::where(function($q) use($row,$check_box,$rows_count,$id_first){
    //                                                 $q->where('account_id',$row->account_id);
    //                                                 if($check_box!=null){
    //                                                     if($id_first == $row->id){
    //                                                         $q->where('id','=',$row->id); 
    //                                                     }else{
    //                                                         $q->where('id','<=',$row->id); 
    //                                                         $q->where('id','>=',$id_first); 
    //                                                     }
    //                                                 }else{
    //                                                     $q->where('id','<=',$row->id); 
    //                                                 }
    //                                                 $q->where("for_repeat","=",null); 
    //                                                 $q->where('operation_date','<=',$row->operation_date);
    //                                                 $q->where('type','credit');
    //                                             })->orWhere(function($q) use($row){
    //                                                     $q->where('account_id',$row->account_id);
    //                                                     $q->where('id','>',$row->id); 
    //                                                     $q->where("for_repeat","=",null); 
    //                                                     $q->where('operation_date','<',$row->operation_date);
    //                                                     $q->where('type','credit');
    //                                             })->sum('amount');
                               
                                    
    //                                 $balance    =  $os_debit - $os_credit;
    //                                 $bal_text   =  round($balance,4);
    //                                 $bl  =  '   DR';
    //                                 if ($balance < 0 ) {
    //                                     $bl = '   CR';
    //                                     $bal_text  =  abs($balance);
    //                                 }
    //                              return '<span class="display_currency" data-currency_symbol="true" >' .$bal_text . '</span>' . $bl   ;
                                
    //                         })
    //                          ->editColumn('description', function ($row) {
                          
    //                             return $html = $row->note;
    //                         })
    //                         ->editColumn('note', function ($row) {
    //                                 if($row->note == "refund Collect"){
    //                                     $html = "Refund Cheque";
    //                                 }else{
    //                                     if($row->note == "Add Purchase"){
    //                                         if($row->transaction){
    //                                             $html = $row->transaction->additional_notes;
    //                                         }else{
    //                                             $html =  $row->note;
    //                                         }
    //                                     }elseif($row->note == "Add Sale"){
    //                                         if($row->transaction){
    //                                             $html = $row->transaction->sell_line_note;
    //                                         }else{
    //                                             $html =  $row->note;
    //                                         }
                                          
    //                                     }elseif($row->note == "Add Cheque"){
    //                                         if($row->check){
    //                                             $html = $row->check->note;
    //                                         } else{
    //                                             $html = "";
                                                
    //                                         }
    //                                     }else{
    //                                         $html = $row->note;
    //                                     }
    //                                 }
    //                                 return $html;
    //                         })
    //                         ->editColumn('operation_date', function ($row) {
    //                             $date_i = \Carbon\Carbon::parse($row->operation_date);
    //                             return $date_i->format("Y-m-d");
    //                         })
    //                         ->editColumn('sub_type', function ($row) {
    //                             return $row->note . "<br>" . $this->__getPaymentDetails($row);
    //                         })
    //                         ->removeColumn('id')
    //                         ->addColumn('balance_amount',function($row)  use($accounts,$check_box,$rows_count,$id_first,$balance_amount,$start_date) {
                                    
    //                                 $os_debit   = \App\AccountTransaction::where(function($q) use($row,$check_box,$rows_count,$id_first,$start_date){
    //                                                     $q->where('account_id',$row->account_id);
    //                                                     if($check_box!=null){
    //                                                         if($id_first == $row->id){
    //                                                             $q->where('id','=',$row->id); 
    //                                                         }else{
    //                                                             $q->where('id','<=',$row->id); 
    //                                                             $q->where('id','>=',$id_first); 
    //                                                         }
    //                                                     }else{
    //                                                         $q->where('id','<=',$row->id); 
    //                                                     }
    //                                                     $q->where("for_repeat","=",null); 
    //                                                     $q->where('operation_date','<',$start_date);
    //                                                     $q->where('type','debit');
    //                                                 })->orWhere(function($q) use($row,$start_date){
    //                                                         $q->where('account_id',$row->account_id);
    //                                                         $q->where("for_repeat","=",null); 
    //                                                         $q->where('id','>',$row->id); 
    //                                                         $q->where('operation_date','<',$start_date);
    //                                                         $q->where('type','debit');
    //                                                 })->sum('amount');
              
    //                                 $os_credit  = \App\AccountTransaction::where(function($q) use($row,$check_box,$rows_count,$id_first,$start_date){
    //                                                 $q->where('account_id',$row->account_id);
    //                                                 if($check_box!=null){
    //                                                     if($id_first == $row->id){
    //                                                         $q->where('id','=',$row->id); 
    //                                                     }else{
    //                                                         $q->where('id','<=',$row->id); 
    //                                                         $q->where('id','>=',$id_first); 
    //                                                     }
    //                                                 }else{
    //                                                     $q->where('id','<=',$row->id); 
    //                                                 }
    //                                                 $q->where("for_repeat","=",null); 
    //                                                 $q->where('operation_date','<',$start_date);
    //                                                 $q->where('type','credit');
    //                                             })->orWhere(function($q) use($row,$start_date){
    //                                                     $q->where('account_id',$row->account_id);
    //                                                     $q->where('id','>',$row->id); 
    //                                                     $q->where("for_repeat","=",null); 
    //                                                     $q->where('operation_date','<',$start_date);
    //                                                     $q->where('type','credit');
    //                                             })->sum('amount');
    //                                 $balance         =  $os_debit - $os_credit;
    //                                 $balance_amount += $balance;
                                               
    //                             return $balance_amount;
    //                         })
    //                         ->removeColumn('is_closed')
                             
    //                         ->rawColumns(['ref_no','credit','description','cost_center', 'debit',  'balance' ,  'sub_type','balance_amount'])
    //                         ->make(true);
             
    //     }
    //     $account = Account::where('business_id', $business_id)
    //                     ->with(['account_type', 'account_type.parent_account'])
    //                     ->findOrFail($id);
    //     $account_cost = \App\Account::where("cost_center",1)->get();
    //     $costcenter = [];
    //     foreach($account_cost as $i){
    //         $costcenter[$i->id]= $i->name . " || " . $i->account_number;
    //     }
    //     return view('account.show')
    //             ->with(compact('languages','currency_details','costcenter','account'));
    // }
    
    public function show($id)
    {
        
        if (!auth()->user()->can('account.access') &&   !auth()->user()->can('account.view') ) {
            abort(403, 'Unauthorized action.');
        }
        // ................................................................................
            $user_id          = request()->session()->get('user.id');
            $business_id      = request()->session()->get('user.business_id');
            $user             = User::where('id', $user_id)->with(['media'])->first();
            $languages        = \App\Account::getLang();
            $account          = \App\Account::getAccount($id,$business_id);  
            $costcenter       = \App\Account::Cost_center();
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            if (request()->ajax()) {
                $balance_amount   = 0;
                $balance_added    = request()->input('balance_added');
                $transaction_type = request()->input('type');
                $start_date       = request()->input('start_date');
                $end_date         = request()->input('end_date');
                $t_check_box      = request()->input('check_box');
                $t_cost_center    = request()->input('cost_center');
                $balance_amount   = 0;
                // ..............................................................................
                $list             = \App\Account::getALeger($business_id,$id,$transaction_type,$t_check_box,$start_date, $end_date,$t_cost_center);
                // ..............................................................................
                $counter = 0; 
                 // return view('account.show')->with(compact('languages','currency_details','costcenter','os_credit','os_debit','rows_count','account','check_box','start_date','end_date','id_first','accounts','allDataFilter','id_account','Ccenter')); 
                return DataTables::of($list)
                                    ->addColumn('ref_no', function ($row) {
                                        $ref_no =  ($row->transaction)?$row->transaction->ref_no.' '.$row->transaction->invoice_no:$row->transaction_id;
                                        if ($row->transaction) {
                                            if ($row->transaction->ref_no) {
                                                return '<a href="#"  data-href="'.url('purchases/'.$row->transaction_id).'" class="btn-modal font_number" data-container=".view_modal">'.$ref_no.'</a>';
                                            }else{
                                                return '<a href="#"  data-href="'.url('sells/'.$row->transaction_id).'" class="btn-modal font_number" data-container=".view_modal">'.$ref_no.'</a>';
                                            }
                                        }else{
                                            return '<a href="#" data-href="'.url('account/account-ref/'.$row->id).'" class="btn-modal font_number" data-container=".view_modal">'.$row->parent_ref.'</a>';
                                        }
                                        
                                    })
                                    ->addColumn('debit', function ($row) {
                                        if ($row->type == 'debit' ) {
                                            return '<span class="display_currency" data-currency_symbol="true">' . $row->amount . '</span>';
                                        }
                                        return '';
                                    })
                                    ->addColumn('credit', function ($row) {
                                        if ($row->type == 'credit' ) {
                                            return '<span class="display_currency" data-currency_symbol="true">' . $row->amount . '</span>';
                                        }
                                        return '';
                                    })
                                    ->addColumn('cost_center',function($row)   {
                                        $cost    = $row->cs_related_id;
                                        if($cost!=null){
                                            $account         = \App\Account::find($cost);
                                            $name_of_account = $account->name;
                                        }else{
                                            $name_of_account = "";
                                        }
                                        return $name_of_account;
                                    })
                                    ->editColumn('balance', function ($row) use($transaction_type,$counter,$balance_added,$t_check_box,$t_cost_center,$start_date, $end_date,$business_id)  {
                                        $balance = 0;
                                        $date    = $row->operation_date;
                                        if($t_cost_center!=null){   
                                            $row_balance = \App\AccountTransaction::join(
                                                        'accounts as A',
                                                        'account_transactions.account_id',
                                                        '=',
                                                        'A.id'
                                                    )->where('A.business_id', $business_id)
                                                    ->where('A.id', $row->account_id)
                                                    ->select(DB::raw('(SELECT SUM(IF(AT.type="credit", AT.amount, -1 * AT.amount)) from account_transactions as AT WHERE 
                                                        AT.operation_date <=  \''.$date.'\' AND AT.account_id  = account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id
                                                            <  \''.$row->id.'\' AND AT.cs_related_id = \''.$t_cost_center.'\') as balance'),
                                                        DB::raw('(SELECT SUM(IF(AT.type="credit", AT.amount, -1 * AT.amount)) from account_transactions as AT WHERE 
                                                        AT.operation_date <  \''.$date.'\'   AND AT.account_id  = account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id
                                                            > \''.$row->id.'\' AND AT.cs_related_id = \''.$t_cost_center.'\' ) as balance_more'),
                                                        DB::raw('(SELECT SUM(IF(AT.type="credit", AT.amount, -1 * AT.amount)) from account_transactions as AT WHERE 
                                                        AT.operation_date =  \''.$date.'\'  AND AT.account_id  = account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id
                                                            = \''.$row->id.'\' AND AT.cs_related_id = \''.$t_cost_center.'\') as balance_more_id'))
                                                    ->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date])
                                                    ->whereNull("account_transactions.deleted_at") 
                                                    ->whereNull("account_transactions.for_repeat") 
                                                    ->where("account_transactions.amount",">",0) 
                                                    ->orderBy('account_transactions.operation_date', 'asc')
                                                    ->orderBy('account_transactions.id', 'asc')
                                                    ->groupBy('account_transactions.id')
                                                    ->first();

                                            $main = round( $row_balance->balance,2 );

                                            if($main < 0){
                                                $balance          = $balance - abs($main) ;
                                            }else{
                                                $balance          = $balance + abs($main) ;
                                            }
                                            $second = round( $row_balance->balance_more,2 );
                                            
                                            if($second < 0){
                                                $balance          = $balance - abs($second) ;
                                            }else{
                                                $balance          = $balance + abs($second) ;
                                            }
                                            $third         =  round( $row_balance->balance_more_id ,2);
                                            
                                            if($third < 0){
                                                $balance          = $balance - abs($third) ;
                                            }else{
                                                $balance          = $balance + abs($third) ;
                                            }
                                            $epsilon = 0.000001; 
                                            if($t_check_box != null){ 
                                                if($balance_added != null && $balance_added != 0){
                                                    $balance        +=  $balance_added;
                                                }
                                            }
                                            if( abs($balance) < $epsilon ){
                                                $balance = 0;
                                            }   
                                            // return   $balance      ;             
                                            return  '<span class="display_currency" data-currency_symbol="true">' . abs($balance) . '</span><b></b>'  ;                   
                                        }elseif($transaction_type!=null){
                                          
                                            $balance      =  ($transaction_type == "debit")?0:1;
                                            $balance_type =  ($transaction_type == "debit")?$row->amount:$row->amount*-1 ;
                                                              
                                            return  '<span class="balance_type">' . $balance_type . '</span>'  ;
                                        }else{
                                            $balance = $row->current_balance;
                                            
                                            $epsilon = 0.000001;
                                            if($t_check_box != null){ 
                                                if($balance_added != null && $balance_added != 0){
                                                    
                                                    $balance        -=  $balance_added;
                                                }
                                            }
                                            if( abs($balance) < $epsilon ){
                                                $balance = 0;
                                            }   
                                            return  '<span class="display_currency" data-currency_symbol="true">' . abs($balance) . '</span><b> </b>'  ;
                                        }
                                        
                                        // else{
                                       
                                        //     $row_balance = \App\AccountTransaction::join(
                                        //                     'accounts as A',
                                        //                     'account_transactions.account_id',
                                        //                     '=',
                                        //                     'A.id'
                                        //                 )
                                        //                 ->leftJoin('transaction_payments AS tp', 'account_transactions.transaction_payment_id', '=', 'tp.id')
                                        //                 ->leftJoin('users AS u', 'account_transactions.created_by', '=', 'u.id')
                                        //                 ->leftJoin('contacts AS c', 'tp.payment_for', '=', 'c.id')
                                        //                 ->where('A.business_id', $business_id)
                                        //                 ->where('A.id', $row->account_id)
                                        //                 ->with(['transaction', 'transaction.contact', 'transfer_transaction', 'media', 'transfer_transaction.media'])
                                        //                     ->select(DB::raw('(SELECT SUM(IF(AT.type="credit", AT.amount, -1 * AT.amount)) from account_transactions as AT WHERE 
                                        //                     AT.operation_date <=  \''.$date.'\' AND AT.account_id  = account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id
                                        //                         <  \''.$row->id.'\' ) as balance'),
                                        //                     DB::raw('(SELECT SUM(IF(AT.type="credit", AT.amount, -1 * AT.amount)) from account_transactions as AT WHERE 
                                        //                     AT.operation_date <  \''.$date.'\'   AND AT.account_id  = account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id
                                        //                         > \''.$row->id.'\'  ) as balance_more'),
                                        //                     DB::raw('(SELECT SUM(IF(AT.type="credit", AT.amount, -1 * AT.amount)) from account_transactions as AT WHERE 
                                        //                     AT.operation_date =  \''.$date.'\'  AND AT.account_id  = account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id
                                        //                         = \''.$row->id.'\' ) as balance_more_id'))
                                        //                     ->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date])
                                        //                     ->whereNull("account_transactions.for_repeat") 
                                        //                     ->where("account_transactions.amount",">",0) 
                                        //                     ->orderBy('account_transactions.operation_date', 'asc')
                                        //                     ->orderBy('account_transactions.id', 'asc')
                                        //                     ->groupBy('account_transactions.id')
                                        //                     ->first();
                                                                    
                                        // }
                                        
                                        // $main = round( $row_balance->balance,2 );
                                        // if($main < 0){
                                        //     $balance          = $balance - abs($main) ;
                                        // }else{
                                        //     $balance          = $balance + abs($main) ;
                                        // }
                                        // $second = round( $row_balance->balance_more,2 );
                                        
                                        // if($second < 0){
                                        //     $balance          = $balance - abs($second) ;
                                        // }else{
                                        //     $balance          = $balance + abs($second) ;
                                        // }
                                        // $third         =  round( $row_balance->balance_more_id ,2);
                                        
                                        // if($third < 0){
                                        //     $balance          = $balance - abs($third) ;
                                        // }else{
                                        //     $balance          = $balance + abs($third) ;
                                        // }
                                        // $epsilon = 0.000001;
                                   
                                        //  if($t_check_box != null){ 
                                        //      if($balance_added != null && $balance_added != 0){
                                        //          $balance        +=  $balance_added;
                                        //      }
                                        //  }
                                        //  if( abs($balance) < $epsilon ){
                                        //      $balance = 0;
                                        //  }
                                        //  if($balance < 0){ $bl_text = " Debit" ;}elseif($balance == 0){ $bl_text = "" ;}else{ $bl_text = " Credit" ;}
                                        // return  '<span class="display_currency" data-currency_symbol="true">' . abs($balance) . '</span><b>' . $bl_text .'</b>'  ;
                                        
                                    })
                                    ->editColumn('note', function ($row) {
                                        if($row->note == "refund Collect"){
                                            $html = "Refund Cheque";
                                        }else{
                                            if ($row->note == "Add Purchase"){
                                                if($row->transaction){
                                                    $html = $row->transaction->additional_notes;
                                                }else{
                                                    $html =  $row->note;
                                                }
                                            }else if ($row->note == "Add Sale"){
                                                if($row->transaction){
                                                    $html = $row->transaction->sell_line_note;
                                                }else{
                                                    $html =  $row->note;
                                                }
                                            }else if ($row->note == "Add Cheque"){
                                                if($row->check){
                                                    $html = $row->check->note;
                                                } else{
                                                    $html = "";
                                                }
                                            }else {
                                                $html = $row->note;
                                            }
                                        }
                                        return $html;
                                    })
                                    ->editColumn('operation_date', function ($row) {
                                        $date_i   = \Carbon\Carbon::parse($row->operation_date);
                                        return $date_i->format("Y-m-d");
                                    })
                                    ->editColumn('sub_type', function ($row) {
                                        return $this->__getPaymentDetails($row);
                                    })
                                    // ->removeColumn('id')
                                    ->removeColumn('is_closed')
                                    ->rawColumns(['ref_no'
                                                ,'credit'
                                                ,'cost_center'
                                                ,'debit'
                                                ,'balance' 
                                                ,'sub_type'
                                                ,'balance_amount'])
                                    ->make(true);
                
            }
        // .................................................................................
        return view('account.show')
                ->with(compact('languages','currency_details','costcenter','account'));
    }
    /**
     * Show the specified resource.
     * @return Response
     */

    public function ledgerShow($id=null)
    {
        if (!auth()->user()->can('account.access') &&   !auth()->user()->can('account.view') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id      = request()->session()->get("user.business_id");
        $account          = Account::where("business_id",$business_id)->get();
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $account_list     = [];
        foreach($account as $act){
            $account_list[$act->id] = $act->name . " || " . $act->account_number;
        }
        $config_languages = config('constants.langs');
        $languages        = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
         if($id != null){
            $account = Account::where('business_id', $business_id)
                ->with(['account_type', 'account_type.parent_account'])
                ->find($id);
        }else{
            $account = Account::where('business_id', $business_id)
                ->with(['account_type', 'account_type.parent_account'])
                ->find(1);
        }
        
    
            
        return view('account.ledger_show')->with(compact('account',"account_list",'languages','currency_details'));
    }
    public function getAccount()
    {
        if(!auth()->user()->can("account.view") ){
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id        = request()->session()->get("user.business_id");
            $account            = Account::where("business_id",$business_id)->get();
            $currency_details   = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $config_languages   = config('constants.langs');
            $languages          = [];
            foreach ($config_languages as $key => $value) {
                $languages[$key] = $value['full_name'];
            }
            $account = Account::where('business_id', $business_id)
                        ->with(['account_type', 'account_type.parent_account'])
                        ->findOrFail(request()->input("id_account"));
            return view("account.action_parents.ldg_show")->with(compact('account','languages','currency_details'));
        }
    }

    public function show_contact($id){
        $account = \App\Account::find($id);
        $debit   = \App\AccountTransaction::where("account_id",$account->id)->where("for_repeat",null)->where("type","debit")->sum("amount");
        $credit  = \App\AccountTransaction::where("account_id",$account->id)->where("for_repeat",null)->where("type","credit")->sum("amount");
        $total   = $debit - $credit;
        $total   = ($total==0)?0:(($total<0)?(($total)*-1 . " / Credit"):(($total) . " / Debit"));
        $contact = (!empty($account))?$account->contact_id:null;
        return view("account.action_parents.show_contact")->with(compact("account","total","contact"));
    }

    public function account_ref($id)
    {
        $data          =  AccountTransaction::find($id);
        if ($data->payment_voucher) {
            $payment   =  TransactionPayment::where("payment_voucher_id",$data->payment_voucher->id)->get();
             $types    =  \App\Models\PaymentVoucher::types();
            return view('account.action_parents.payment_voucher')
                        ->with('types',$types)
                        ->with('payment',$payment)
                        ->with('data',$data->payment_voucher)
                    ;
        }elseif ($data->daily_payment_item) {
            return view('account.action_parents.daily_payment')
                        ->with('data',$data->daily_payment_item->daily_payment)
                    ;
        }elseif ($data->gournal_voucher_item) {
            return view('account.action_parents.gournal_voucher')
                        ->with('data',$data->gournal_voucher_item->gournal_voucher)
                    ;
        }elseif ($data->cheque){
            $types    =  \App\Models\Check::types();
            return view('account.action_parents.cheque')
                      ->with('data',$data->cheque)
                      ->with('types',$types)
                        ;
        }
    }

    public function check(Request $request,$name=null)
    {
        $result            = $name;
        $products_         = null;
        $business_id       = $request->session()->get('user.business_id');
        $products          = \App\Account::where("business_id",$business_id)->get();
        foreach($products as $it){
            if(trim($it->name) == trim($name) ){
                $products_ = 1;
                break;
            }
        }
        if(($products_)!= null ){$status = true;}else{$status = false;}
        $output = ["success" => 1,"msg" => "successfull","status" => $status];
        return  $output;
    }

    public function check_number(Request $request,$number=null)
    {
         
        $result           = $number;
        $accounts_        = null;
        $business_id      = $request->session()->get('user.business_id');
        $accounts         = \App\Account::where("business_id",$business_id)->get();
        foreach($accounts as $it){
            if(trim($it->account_number) == trim($number) ){
                $accounts_ = 1;
                break;
            }
        }
        if(($accounts_)!= null ){$status = true;}else{$status = false;}
        $output = ["success" => 1,"msg" => "successfull","status" => $status];
        return  $output;
    }


    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can("account.update") ){
            abort(403, 'Unauthorized action.');
        }
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

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $account = Account::where('business_id', $business_id)
                                ->find($id);
            $account_types = AccountType::where('business_id', $business_id)
                                     ->whereNull('parent_account_type_id')
                                     ->with(['sub_types'])
                                     ->get();
           //.............. eb 
            $array_of_type = [];
            $account_types_sub = AccountType::where('business_id', $business_id)
                                        ->whereNotNull('parent_account_type_id')
                                        ->whereNotNull('sub_parent_id')
                                        ->with(['sub_types_id'])
                                        ->get();
            foreach ($account_types_sub as $key => $value) {
                $array_of_type[$value->id] = $value->sub_parent_id."&".$value->name." /".$value->code;
            }                            
            //.......................eb 27-12-2022

            $account_types_all = AccountType::where('business_id', $business_id)
                                            ->whereNull("parent_account_type_id")
                                            ->with(['sub_types'])
                                            ->get();
            $account_loop =  $this->loop($account_types_all);  
        
            $type = [];                     
            foreach($account_types_all as $i){
                $type[$i->id] = $i->name;                     
            }
            $list_a  = [];
            foreach($account_loop as  $key => $i ){
                $s = \App\AccountType::find($key);
                $list_a[$key] =  $s->name;
                if(!is_string($i) && $i!=null){
                    $array  =  $this->list($i,$list_a);
                    $list_a =  $array;
                } 
            }
            return view('account.edit')
                ->with(compact('languages','account' ,'type','list_a', 'array_of_type' , 'account_types'));
        }
    }

    /**
     * Show the cash infomation
     * ...  @  _... 
     * 
     */
    public function showCash()
    {
        if(!auth()->user()->can("sidBar.Cash_And_Bank") ){
            abort(403, 'Unauthorized action.');
        }
        $id        = request()->session()->get("user.business_id"); 
        $business  = \App\Business::find($id);
        $type      = $business->cash; 
        $account_s = \App\Account::where("account_type_id",$business->cash);
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($id);

        $accounts_type = [];
        foreach($account_s->get() as $it ){
            $accounts_type[$it->id] = $it->name . " || " . $it->account_number ;
        }
       
        if(request()->ajax()){
            if(!empty(request()->accounts)){
                $account = request()->accounts;
                $account_s->where("id",$account);
             }
            $account_s->get();
                return DataTables::of($account_s)
                ->addColumn( 'action',
                        '<button  data-href="{{action(\'AccountController@edit\',[$id])}}" data-container=".account_model" class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        <a  href="{{action(\'AccountController@show\',[$id])}}" class="btn btn-warning btn-xs  "><i class="fa fa-book"></i> @lang("account.account_book")</a>&nbsp;
                        @can("account.create")
                        @if($is_closed == 0)
                        <button  data-url="{{action(\'AccountController@close\',[$id])}}" class="btn btn-xs btn-danger close_account  "><i class="fa fa-power-off"></i> @lang("messages.close")</button>
                        @elseif($is_closed == 1)
                            <button data-url="{{action(\'AccountController@activate\',[$id])}}" class="btn btn-xs btn-success activate_account  "><i class="fa fa-power-off"></i> @lang("messages.activate")</button>
                        @endif@endcan'
                 )
                ->addColumn("number",function($row) {
                    $html = '<a    href="' . \URL::to('account/account/'.$row->id)   . '"    >' . $row->account_number . '</a>';
                    return $html;
                })
                ->addColumn("name",function($row) {
                    $html = '<a    href="' . \URL::to('account/account/'.$row->id)   . '"    >' . $row->name . '</a>';
                    return $html;
                })
                ->addColumn("debit",function($row){
                    $debit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","debit")->sum("amount");
                    $html = '<span class="display_currency" data-currency_symbol=true >'
                                        . $debit. '</span>';
                    return  $debit;
                 })
                ->addColumn("credit",function($row){
                    $credit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","credit")->sum("amount");
                    $html = '<span class="display_currency" data-currency_symbol=true >'
                                        . $credit. '</span>';
                    return   $credit;
                    
                })
                ->addColumn("status",function($row){
                    $debit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","debit")->sum("amount");
                    $credit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","credit")->sum("amount");
                    if(($debit - $credit)== 0){
                        $status = " --- ";
                    }else if(($debit - $credit)< 0){
                        $status = " Credit ";
                    }else{
                        $status = " Debit ";
                    }
                    return $status;
                })
                ->addColumn("balance",function($row){
                    $debit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","debit")->sum("amount");
                    $credit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","credit")->sum("amount");
                    if(($debit - $credit)== 0){
                        $balance = 0 ;
                    }else if(($debit - $credit)< 0){
                        $balance = ($debit - $credit) ;
                    }else{
                        $balance = ($debit - $credit) ;
                    
                    }
                    $html = '<span class="display_currency" data-currency_symbol=true >'
                                        . $balance. '</span>';
                    return  $balance;
                
                })
                ->rawColumns(["number","action","name","debit","credit","status","balance"])
                ->make(true);
        }
        return view("cash_and_bank.cash_list")->with(compact("currency_details","type","accounts_type"));
    }

    /**
     * Show the bank infomation
     * ...  @  _... 
     * 
     */
    public function showBank()
    {
        
        if(!auth()->user()->can("sidBar.Cash_And_Bank") ){
            abort(403, 'Unauthorized action.');
        }
        $id               = request()->session()->get("user.business_id"); 
        $business         = \App\Business::find($id);
        $type             = $business->bank; 
        $account_s        = \App\Account::where("account_type_id",$business->bank);
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($id);

        $accounts_type    = [];
        foreach($account_s->get() as $it ){
            $accounts_type[$it->id] = $it->name . " || " . $it->account_number ;
        }
       
        if(request()->ajax()){
            if(!empty(request()->accounts)){
                $account = request()->accounts;
                $account_s->where("id",$account);
             }
            $account_s->get();
            return DataTables::of($account_s)
                        ->addColumn( 'action',
                                    '<button data-href="{{action(\'AccountController@edit\',[$id])}}" data-container=".account_model" class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                                    <a href="{{action(\'AccountController@show\',[$id])}}" class="btn btn-warning btn-xs"><i class="fa fa-book"></i> @lang("account.account_book")</a>&nbsp;
                                    @can("account.create")
                                    @if($is_closed == 0)


                                    <button data-url="{{action(\'AccountController@close\',[$id])}}" class="btn btn-xs btn-danger close_account "><i class="fa fa-power-off"></i> @lang("messages.close")</button>
                                    @elseif($is_closed == 1)
                                        <button data-url="{{action(\'AccountController@activate\',[$id])}}" class="btn btn-xs btn-success activate_account "><i class="fa fa-power-off"></i> @lang("messages.activate")</button>
                                    @endif@endcan'
                            )
                        ->addColumn("number",function($row) {
                            $html = '<a    href="' . \URL::to('account/account/'.$row->id)   . '"    >' . $row->account_number . '</a>';
                            return $html;
                        })
                        ->addColumn("name",function($row) {
                            $html = '<a    href="' . \URL::to('account/account/'.$row->id)   . '"    >' . $row->name . '</a>';
                            return $html;
                        })
                        ->addColumn("debit",function($row){
                            $debit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","debit")->sum("amount");
                            $html = '<span class="display_currency" data-currency_symbol=true >'
                                        . $debit . '</span>';
                            return  $debit;
                        })
                        ->addColumn("credit",function($row){
                            $credit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","credit")->sum("amount");
                            $html = '<span class="display_currency" data-currency_symbol=true >'
                                        . $credit . '</span>';
                            return $credit ;
                        })
                        ->addColumn("status",function($row){
                            $debit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","debit")->sum("amount");
                            $credit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","credit")->sum("amount");
                            if(($debit - $credit)== 0){
                                $status = " --- ";
                            }else if(($debit - $credit)< 0){
                                $status =  " Credit ";
                            }else{
                                $status = " Debit ";
                            }
                            return $status;
                        })
                        ->addColumn("balance",function($row){
                            $debit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","debit")->sum("amount");
                            $credit = \App\AccountTransaction::where("account_id",$row->id)->whereNull("for_repeat")->where("type","credit")->sum("amount");
                            if(($debit - $credit)== 0){
                                $balance = 0;
                            }else if(($debit - $credit)< 0){
                                $balance = ($debit - $credit);
                            }else{
                                $balance = ($debit - $credit) ;
                            
                            }
                            // $html = '<span class="display_currency" data-currency_symbol=true >'
                            //             . $balance . '</span>';
                            return  $balance ;
                        })
                        ->rawColumns(["number","action","name","debit","credit","status","balance"])
                        ->make(true);
        }


        return view("cash_and_bank.bank_list")->with(compact("accounts_type" , "type" ,"currency_details"));
    }


    public function filterSubAccountType($id)
    {   

        if(request()->ajax()){
            $account = \App\AccountType::where("sub_parent_id",$id)->get();
            $array   = [] ;
            foreach($account as $it){
                $array[$it->id] = $it->name  . " || " . $it->code;
            }
            $output = [
                'success' => true,
                'array' => $array,
                
                
            ];
            
            return  $output;
        }
    }
    
    public function getSubAccountType($id)
    {   
        if(request()->ajax()){
            
            $business = \App\Business::find(request()->session()->get("user.business_id"));
           
            if($id == "cash"){
              $id_account = $business->cash;
            }elseif($id == "card"){
              $id_account =  $business->bank;
            }elseif($id == "bank_transfer"){
                $id_account =  $business->bank;
            }else{
                $id_account = null;
            }
            $array   = [] ;
            if( $id_account != null){
                $account = \App\Account::where("account_type_id",$id_account)->get();
                foreach($account as $it){
                    $array[$it->id] = $it->name  . " || " . $it->account_number;
                }
            }
            $output = [
                'success' => true,
                'array' => $array,
            ];
           
            return  $output;
        }
    }
    
    public function filterAccountType($id)
    {  
        if(request()->ajax()){
            $account = \App\AccountType::where("parent_account_type_id",$id)->get();
            $array   = [] ; 
            foreach($account as $it){
                $array[$it->id] = $it->code  . " || " . $it->name;
                $idd   =  $it->id ;
                $acc   =  \App\AccountType::where("parent_account_type_id", $it->id)->get(); 
                $array =  $this->takeParent($array,$acc);
            }
            $output = [
                        'success' => true,
                        'array' => $array,
                    ]; 
            return  $output;
        }
    }

    public function takeParent($list,$acc){
        foreach($acc as $it){
            $list[$it->id]  =  $it->code . " || " . $it->name;
            $idd            =  $it->id ;
            $acc            =  \App\AccountType::where("parent_account_type_id", $it->id)->get();
            if(count($acc)>0){
                $this->takeParent($list,$acc);
            }
        }
        return $list;
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('account.access') && !auth()->user()->can('account.update')) {
            abort(403, 'Unauthorized action.');
        }
        
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'account_number', 'note', 'account_type_id']);

                $business_id = request()->session()->get('user.business_id');
                $account = Account::where('business_id', $business_id)
                                                    ->findOrFail($id);
                $account->name = $input['name'];
                $account->account_number = $input['account_number'];
                $account->note = $input['note'];
                $account->account_type_id = $input['account_type_id'];
                $account->save();

                $output = ['success' => true,
                                'msg' => __("account.account_updated_success")
                                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }
            
            return $output;
        }else{
            try {
                $input = $request->only(['name', 'account_number', 'note', 'account_type_id']);

                $business_id = request()->session()->get('user.business_id');
                $account = Account::where('business_id', $business_id)
                                                    ->findOrFail($id);
                $account->name = $input['name'];
                $account->account_number = $input['account_number'];
                $account->note = $input['note'];
                $account->account_type_id = $input['account_type_id'];
                $account->save();

                $output = ['success' => true,
                                'msg' => __("account.account_updated_success")
                                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }
            
            return redirect()->back()->with("status",$output);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroyAccountTransaction($id)
    {
        $user_id = request()->session()->get('user.id');
        $user    = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        if (!auth()->user()->can('account.access') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('SalesMan.views')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $account_transaction = AccountTransaction::findOrFail($id);
                
                if (in_array($account_transaction->sub_type, ['fund_transfer', 'deposit'])) {
                    //Delete transfer transaction for fund transfer
                    if (!empty($account_transaction->transfer_transaction_id)) {
                        $transfer_transaction = AccountTransaction::findOrFail($account_transaction->transfer_transaction_id);
                        $transfer_transaction->delete();
                    }
                    $account_transaction->delete();
                }

                $output = ['success' => true,
                            'msg' => __("lang_v1.deleted_success")
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

    /**
     * Closes the specified account.
     * @return Response
     */
    public function close($id)
    {
       
        if (!auth()->user()->can('account.create') ) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {
            try {
                $business_id = session()->get('user.business_id');
            
                $account = Account::where('business_id', $business_id)
                                                    ->findOrFail($id);
                $account->is_closed = 1;
                $account->save(); 
                if($account->contact_id != null){
                   $contact                 = \App\Contact::where('business_id', $business_id)->find($account->contact_id );
                   $contact->contact_status = 'inactive';
                   $contact->save();
               }
                $output = ['success' => true,
                                    'msg' => __("account.account_closed_success")
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

    /**
     * Shows form to transfer fund.
     * @param  int $id
     * @return Response
     */
    public function getFundTransfer($id)
    {
        if (!auth()->user()->can('account.access') ) {
            abort(403, 'Unauthorized action.');
        }
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        
        if (request()->ajax()) {
            $business_id = session()->get('user.business_id');
            
            $from_account = Account::where('business_id', $business_id)
                            ->NotClosed()
                            ->find($id);

            $to_accounts = Account::where('business_id', $business_id)
                            ->where('id', '!=', $id)
                            ->NotClosed()
                            ->pluck('name', 'id');

            return view('account.transfer')
                ->with(compact('languages','from_account', 'to_accounts'));
        }
    }

    /**
     * Transfers fund from one account to another.
     * @return Response
     */
    public function postFundTransfer(Request $request)
    {
        if (!auth()->user()->can('account.access') ) {
            abort(403, 'Unauthorized action.');
        }
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        
        try {
            $business_id = session()->get('user.business_id');

            $amount = $this->commonUtil->num_uf($request->input('amount'));
            $from = $request->input('from_account');
            $to = $request->input('to_account');
            $note = $request->input('note');
            if (!empty($amount)) {
                $debit_data = [
                    'amount' => $amount,
                    'account_id' => $from,
                    'type' => 'debit',
                    'sub_type' => 'fund_transfer',
                    'created_by' => session()->get('user.id'),
                    'note' => $note,
                    'transfer_account_id' => $to,
                    'operation_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                ];

                DB::beginTransaction();
                $debit = AccountTransaction::createAccountTransaction($debit_data);

                $credit_data = [
                        'amount' => $amount,
                        'account_id' => $to,
                        'type' => 'credit',
                        'sub_type' => 'fund_transfer',
                        'created_by' => session()->get('user.id'),
                        'note' => $note,
                        'transfer_account_id' => $from,
                        'transfer_transaction_id' => $debit->id,
                        'operation_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                    ];

                $credit = AccountTransaction::createAccountTransaction($credit_data);

                $debit->transfer_transaction_id = $credit->id;
                $debit->save();

                Media::uploadMedia($business_id, $debit, $request, 'document');

                DB::commit();
            }
            
            $output = ['success' => true,
                                'msg' => __("account.fund_transfered_success")
                                ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        
            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];
        }

        return redirect()->action('AccountController@index')->with('status', $output);
    }

    /**
     * Shows deposit form.
     * @param  int $id
     * @return Response
     */
    public function getDeposit($id)
    {
        if (!auth()->user()->can('account.access') ) {
            abort(403, 'Unauthorized action.');
        }
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        
        if (request()->ajax()) {
            $business_id = session()->get('user.business_id');
            
            $account = Account::where('business_id', $business_id)
                            ->NotClosed()
                            ->find($id);

            $from_accounts = Account::where('business_id', $business_id)
                            ->where('id', '!=', $id)
                            // ->where('account_type', 'capital')
                            ->NotClosed()
                            ->pluck('name', 'id');

            return view('account.deposit')
                ->with(compact('languages','account', 'account', 'from_accounts'));
        }
    }

    /**
     * Deposits amount.
     * @param  Request $request
     * @return json
     */
    public function postDeposit(Request $request)
    {
        if (!auth()->user()->can('account.access')  ) {
            abort(403, 'Unauthorized action.');
        }
       
        try {
            $business_id = session()->get('user.business_id');

            $amount = $this->commonUtil->num_uf($request->input('amount'));
            $account_id = $request->input('account_id');
            $note = $request->input('note');

            $account = Account::where('business_id', $business_id)
                            ->findOrFail($account_id);

            if (!empty($amount)) {
                $credit_data = [
                    'amount' => $amount,
                    'account_id' => $account_id,
                    'type' => 'credit',
                    'sub_type' => 'deposit',
                    'operation_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                    'created_by' => session()->get('user.id'),
                    'note' => $note
                ];
                $credit = AccountTransaction::createAccountTransaction($credit_data);

                $from_account = $request->input('from_account');
                if (!empty($from_account)) {
                    $debit_data = $credit_data;
                    $debit_data['type'] = 'debit';
                    $debit_data['account_id'] = $from_account;
                    $debit_data['transfer_transaction_id'] = $credit->id;

                    $debit = AccountTransaction::createAccountTransaction($debit_data);

                    $credit->transfer_transaction_id = $debit->id;

                    $credit->save();
                }
            }
            
            $output = ['success' => true,
                                'msg' => __("account.deposited_successfully")
                                ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        
            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];
        }

        return $output;
    }

    /**
     * Calculates account current balance.
     * @param  int $id
     * @return json
     */
    public function getAccountBalance($id)
    {
         
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');
        $account = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
            ->whereNull('AT.deleted_at')
            ->where('accounts.business_id', $business_id)
            ->where('accounts.id', $id)
            ->where('AT.for_repeat',"=", null)
            ->select('accounts.*', DB::raw("SUM( IF(AT.type='credit', amount, -1 * amount) ) as balance"))
            ->first();

        return $account;
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function cashFlow()
    {
        if (!auth()->user()->can('account.cash_flow') ) {
            abort(403, 'Unauthorized action.');
        }
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }

        $business_id = request()->session()->get('user.business_id');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        if (request()->ajax()) {
            $accounts = AccountTransaction::join(
                'accounts as A',
                'account_transactions.account_id',
                '=',
                'A.id'
                )
                ->leftjoin(
                    'transaction_payments as TP',
                    'account_transactions.transaction_payment_id',
                    '=',
                    'TP.id'
                )
                ->where('A.business_id', $business_id)
                ->with(['transaction', 'transaction.contact', 'transfer_transaction'])
                ->select(['type', 'account_transactions.amount', 'operation_date',
                    'sub_type', 'transfer_transaction_id',
                    DB::raw("(SELECT SUM(IF(AT.type='credit', AT.amount, -1 * AT.amount)) from account_transactions as AT JOIN accounts as ac ON ac.id=AT.account_id WHERE ac.business_id= $business_id AND AT.operation_date <= account_transactions.operation_date AND AT.deleted_at IS NULL) as balance"),
                    'account_transactions.transaction_id',
                    'account_transactions.id',
                    'A.name as account_name',
                    'TP.payment_ref_no as payment_ref_no'
                    ])
                 ->groupBy('account_transactions.id')
                 ->orderBy('account_transactions.operation_date', 'desc');
            if (!empty(request()->input('type'))) {
                $accounts->where('type', request()->input('type'));
            }

            if (!empty(request()->input('account_id'))) {
                $accounts->where('A.id', request()->input('account_id'));
            }

            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            
            if (!empty($start_date) && !empty($end_date)) {
                $accounts->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date]);
            }

            return DataTables::of($accounts)
                ->addColumn('debit', function ($row) {
                    if ($row->type == 'debit') {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->amount . '</span>';
                    }
                    return '';
                })
                ->addColumn('credit', function ($row) {
                    if ($row->type == 'credit') {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->amount . '</span>';
                    }
                    return '';
                })
                ->editColumn('balance', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">' . $row->balance . '</span>';
                })
                ->editColumn('operation_date', function ($row) {
                    return $this->commonUtil->format_date($row->operation_date, true);
                })
                ->editColumn('sub_type', function ($row) {
                    return $this->__getPaymentDetails($row);
                })
                ->removeColumn('id')
                ->rawColumns(['credit', 'debit', 'balance', 'sub_type'])
                ->make(true);
        }
        $accounts = Account::forDropdown($business_id, false);
                            
        return view('account.cash_flow')
                 ->with(compact('languages' , 'currency_details' ,'accounts'));
    }

    public function __getPaymentDetails($row)
    {
       
        $details = '';
        if (!empty($row->sub_type)) {
            if($row->check_id != null){
                if($row->check->type == 0){
                    $details = 'Cheque In';
                }else{
                    $details = 'Cheque Out';
                }
            }elseif($row->daily_payment_item_id != null){
                $details = 'Journal Voucher';
            }elseif($row->gournal_voucher_item_id != null){
                $details = 'Expense Voucher';
            }elseif($row->payment_voucher_id != null){
                if($row->payment_voucher->type == 0){
                    $details = 'Payment Voucher';
                }else{
                    $details = 'Receipt Voucher';
                }
            }else{
                // $details = __('account.' . $row->sub_type);
                if (!empty($row->transaction->type)) {
                    if ($row->transaction->type == 'purchase') {
                        $details = __('lang_v1.purchase') . '<br><b>' . __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>'.
                        __('purchase.ref_no') . ':</b> <a href="#" data-href="' . action("PurchaseController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->ref_no . '</a>';
                    }elseif ($row->transaction->type == 'expense') {
                        $details = __('lang_v1.expense') . '<br><b>' . __('purchase.ref_no') . ':</b>' . $row->transaction->ref_no;
                    } elseif ($row->transaction->type == 'sale') {
                        $details = __('sale.sale') . '<br><b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>'.
                        __('sale.invoice_no') . ':</b> <a href="#" data-href="' . action("SellController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->invoice_no . '</a>';
                    }
                }
               
            }
            if (in_array($row->sub_type, ['fund_transfer', 'deposit']) && !empty($row->transfer_transaction)) {
                if ($row->type == 'credit') {
                    $details .= ' ( ' . __('account.from') .': ' . $row->transfer_transaction->account->name . ')';
                } else {
                    $details .= ' ( ' . __('account.to') .': ' . $row->transfer_transaction->account->name . ')';
                }
            }
        } else {
            if (!empty($row->transaction->type)) {
                if ($row->transaction->type == 'purchase') {
                    $details = __('lang_v1.purchase') . '<br><b>' . __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>'.
                    __('purchase.ref_no') . ':</b> <a href="#" data-href="' . action("PurchaseController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->ref_no . '</a>';
                }elseif ($row->transaction->type == 'expense') {
                    $details = __('lang_v1.expense') . '<br><b>' . __('purchase.ref_no') . ':</b>' . $row->transaction->ref_no;
                } elseif ($row->transaction->type == 'sale') {
                    $details = __('sale.sale') . '<br><b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>'.
                    __('sale.invoice_no') . ':</b> <a href="#" data-href="' . action("SellController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->invoice_no . '</a>';
                }
            }
        }

        if (!empty($row->payment_ref_no)) {
            if (!empty($details)) {
                $details .= '<br/>';
            }
            if($row->check_id != null){
                
            
                $details .= '<b>' . __('lang_v1.pay_reference_no') . ':</b> <button class="btn btn-modal btn-link" data-container=".view_modal" data-href="'.action("General\CheckController@show",[$row->check->id]).'">' . $row->check->ref_no.'</button>';
            }else if($row->payment_voucher_id != null){
                 $voucher =\App\Models\PaymentVoucher::find($row->payment_voucher_id);
                $details .= '<b>' . __('lang_v1.pay_reference_no') . ':</b> <button class="btn btn-modal btn-link" data-container=".view_modal" data-href="'.action("General\PaymentVoucherController@show",[$voucher->id]).'">' . $voucher->ref_no.'</button>';
            }else{
                
                $details .= '<b>' . __('lang_v1.pay_reference_no') . ':</b> ' . $row->payment_ref_no;
            }
        }
        if (!empty($row->payment_for)) {
            if (!empty($details)) {
                $details .= '<br/>';
            }

            $details .= '<b>' . __('account.payment_for') . ':</b> ' . $row->payment_for;
        }

        if ($row->is_advance == 1) {
            $details .= '<br>(' . __('lang_v1.advance_payment') . ')';
        }

        return $details;
    }

    /**
     * activate the specified account.
     * @return Response
     */
    public function activate($id)
    {
        
        if (!auth()->user()->can('account.access')  ) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {
            try {
                $business_id = session()->get('user.business_id');
            
                $account = Account::where('business_id', $business_id)
                                ->findOrFail($id);

                $account->is_closed = 0;
                $account->save();
                 if($account->contact_id != null){
                    $contact                 = \App\Contact::where('business_id', $business_id)->find($account->contact_id );
                    $contact->contact_status = 'active';
                    $contact->save();
                }
                $output = ['success' => true,
                        'msg' => __("lang_v1.success")
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

    public function typeAccount($name)
    {   
        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");

            $business = \App\Business::where("id",$business_id)->first(); 
            if($name == "cash"){
                $account  = \App\Account::where("account_type_id",$business->cash)->get();
            }elseif($name == "card" || $name == "bank_transfer"){
                $account  = \App\Account::where("account_type_id",$business->bank)->get();
            }else{
                $account  = \App\Account::select()->get();
            }
            $array_ = [];
            foreach($account as  $it){
                $array_[$it->id] = $it->account_number . " || " . $it->name; 
            }
            $output = [
                        "success" => true,
                        "array"   => $array_
            ];
            return $output;
        }
    }

    public function idTypeAccount($id)
    {
        $accountType = \App\AccountType::find($id);
        $array_ = "";
        $account_number = \App\Account::orderBy("id","desc")->where("account_type_id",$id)->first();
        if($account_number){
            $letter = substr($account_number->account_number,0,1);
           
            if($letter != "S" && $letter != "C"){
                $array_ = $account_number->account_number;
                
            }else{
                 
                $array_ = substr($account_number->account_number,1);
            }
                
             
        }else{
            $array_ =  $accountType->code . "0";
        }
        
        $output = [
            "success" => true,
            "array"   => $array_
        ];
        
        return $output;
    }
  
    // *1* FOR LOOP SECTION
    // *** AGT8422
        public function loop($accounts){
            foreach ($accounts as $key => $value) {
                $list_of_child      =  $this->childOfType($value->id);
                $list[$value->id]   =  (count($list_of_child)>0)?$list_of_child:null;
            } 
            return $list ;
        }
    // ************

    // *2* FOR CHILD OF ACCOUNT TYPE
    // *** AGT8422
        public function childOfType($id){
            $list     = []; 
            $accounts = AccountType::where("parent_account_type_id",$id)->get();
            foreach($accounts as $i){
                $list[$i->id]    = $i->name;
                $list_of_child   = $this->childOfType($i->id);
                if(count($list_of_child)>0){
                    $list[$i->id]  =  $list_of_child ;
                }else{
                    $list[$i->id]  = $i->name;
                }
                
            }
            return  $list;
        }                 
    // ************
    
    // ** FOR LIST 
    // *** AGT8422
        public function list($i,$list){
            foreach($i as  $key => $e ){
                $account    = \App\AccountType::find($key);
                $list[$key] = $account->name;
                if(!is_string($e) && $e!=null){
                    $array  =  $this->list($e,$list);
                    $list   =  $array;
                } 
            }
            return $list;
        }
    // ************

    // ** FOR LIST  Child
    // *** AGT8422
        public function ChildType($i,$list){
            
        }
    // ************

    // ** FOR Ledger Page
    // *** AGT8422
        public function getBalance($id){
            try{
                if(request()->ajax()){
                    $accountTransaction  = \App\AccountTransaction::find($id);
                     if(empty($accountTransaction)){
                        $output              = [
                            "success" => true,
                            "value"   => 0,
                        ];
                        return $output;
                    }
                    $check_box           = request()->input("check_box"); 
                    $start_date          = request()->input("start_date");
                    $cost_center         = request()->input("cost_center");
                    $account_id          = request()->input("account_id");
                //   dd($account_id);
                   if($accountTransaction == null){
                    $accountTransaction  = \App\AccountTransaction::where("account_id",$account_id)->whereDate("operation_date","<",$start_date)->first();
                    $id = $accountTransaction->id;
                       
                   }
                       
                   
                    if($cost_center != null){
                        $first_id            = (\App\AccountTransaction::where("account_id",$accountTransaction->account_id)
                                                                        ->where("id","!=",$id) 
                                                                        ->where("cs_related_id",$cost_center) 
                                                                        ->where("for_repeat","=",null) 
                                                                        ->whereDate("operation_date","<",$start_date)->orderBy("operation_date","asc")->first())?
                                                                        \App\AccountTransaction::where("account_id",$accountTransaction->account_id)
                                                                        ->where("id","!=",$id)->where("cs_related_id",$cost_center)->where("for_repeat","=",null) 
                                                                        ->whereDate("operation_date","<",$start_date)->orderBy("operation_date","asc")->first()->id:null;
                     
                    }else{
                    
                        $first_id            = (\App\AccountTransaction::where("account_id",$accountTransaction->account_id)
                                                                        ->where("id","!=",$id) 
                                                                        ->where("for_repeat","=",null) 
                                                                        ->whereDate("operation_date","<",$start_date)->orderBy("operation_date","asc")->first())?
                                                                        \App\AccountTransaction::where("account_id",$accountTransaction->account_id)
                                                                        ->where("id","!=",$id)->where("for_repeat","=",null) 
                                                                        ->whereDate("operation_date","<",$start_date)->orderBy("operation_date","asc")->first()->id:null;
                     
                    } 
                    $debit               = \App\Account::amount_balance($accountTransaction,[],$check_box,1,$first_id,$start_date,"debit",$cost_center);
                    $credit              = \App\Account::amount_balance($accountTransaction,[],$check_box,1,$first_id,$start_date,"credit",$cost_center);
                    $output              = [
                        "success" => true,
                        "value"   => $debit - $credit,
                    ];
                    return $output;
                }
            }catch(Exception $e){
                $output              = [
                        "success" => false,
                        "value"   => $e->getMessage(),
                ];
                    return $output; 
            }
        }
    // ************
  
    public function getOneAccountBalance($id){
        try{
            $business_id = session()->get('user.business_id');
            $accounts    = Account::leftJoin('account_transactions as AT','accounts.id', '=', 'AT.account_id')
                                    ->leftJoin( 'account_types as ats', 'accounts.account_type_id', '=', 'ats.id' )
                                    ->leftJoin( 'account_types as pat', 'ats.parent_account_type_id', '=', 'pat.id' )
                                    ->leftJoin( 'account_types as pat_sub', 'ats.sub_parent_id', '=', 'pat_sub.id' )
                                    ->leftJoin('users AS u', 'accounts.created_by', '=', 'u.id')
                                    ->where('accounts.id', $id)
                                    ->where('accounts.business_id', $business_id)
                                    ->where('accounts.cost_center', 0)
                                    ->whereNull('AT.for_repeat') 
                                    ->whereNull('AT.deleted_at')
                                    ->select([
                                        'accounts.name', 
                                        'accounts.account_number', 
                                        'accounts.note', 
                                        'accounts.id', 
                                        'accounts.account_type_id',
                                        'ats.name as account_type_name',
                                        'ats.id as account_type_id_',
                                        'pat_sub.name as sub_parent_name',
                                        'ats.name as parent_account_type_name',
                                        'is_closed',
                                        DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance"),
                                        DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                                    ])
                                    ->groupBy('accounts.id')->first();

            $balance =  ($accounts)?$accounts->balance:0;
            $output  = [
                "value"       => true,
                "msg"         => "Success",
                "balance"     => $balance
            ];
            if(request()->input('in_row')){
                return $output;
            }
            return view('account.one_account_balance')->with($output);
        }catch(Exception $e){
            $output = [
                "value"       => false,
                "msg"         => "failed",
            ];
            if(request()->input('in_row')){
                return $output;
            }
            return view('account.one_account_balance')->with($output);
        }   
    }
    // ## repair accounts balance
    public function repairBalance(){
        try{
            if(request()->ajax()){
                ini_set('max_execution_time', 0);
                ini_set('memory_limit', -1);
                $business_id  = session()->get('user.business_id');
                $all_accounts = \App\AccountTransaction::groupBy('account_id')->pluck('account_id');
                \DB::beginTransaction();
                foreach($all_accounts as $id){
                    \App\AccountTransaction::oldBalance($id,$business_id,date('Y-m-d'));
                }
                \DB::commit();
                $output       = [
                    "success" => 1,
                    "value"   => __('Repaired Successfully'),
                ];
                return $output;
            }
        }catch(\Exception $e){
            \DB::rollback();
            $output       = [
                "success" => 0,
                "value"   => $e->getMessage(),
            ];
            return $output;
        } 
    }
}
