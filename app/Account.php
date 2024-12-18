<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\Util;
use DB;

class Account extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    public static function forDropdown($business_id, $prepend_none, $closed = false, $show_balance = false)
    {
        $query = Account::where('business_id', $business_id);

        $can_access_account = auth()->user()->can('account.access');
        if ($can_access_account && $show_balance) {
            $query->leftjoin('account_transactions as AT', function ($join) {
                $join->on('AT.account_id', '=', 'accounts.id');
                $join->whereNull('AT.deleted_at');
            })
           ->where("for_repeat",null)
            ->select('accounts.name', 
                    'accounts.id', 
                    DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")
                )->groupBy('accounts.id');
        }

        if (!$closed) {
            $query->where('is_closed', 0);
        }

        $accounts = $query->get();

        $dropdown = [];
        if ($prepend_none) {
            $dropdown[''] = __('lang_v1.none');
        }

        $commonUtil = new Util;
        foreach ($accounts as $account) {
            $name = $account->name;

            if ($can_access_account && $show_balance) {
                $name .= ' (' . __('lang_v1.balance') . ': ' . $commonUtil->num_f($account->balance) . ')';
            }

            $dropdown[$account->id] = $name;
        }

        return $dropdown;
    }
    
    public function contact()
    {
       return $this->belongsTo('App\Contact','contact_id');
    }
    /**
     * Scope a query to only include not closed accounts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotClosed($query)
    {
        return $query->where('is_closed', 0);
    }

    /**
     * Scope a query to only include non capital accounts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public function scopeNotCapital($query)
    // {
    //     return $query->where(function ($q) {
    //         $q->where('account_type', '!=', 'capital');
    //         $q->orWhereNull('account_type');
    //     });
    // }

    public static function accountTypes()
    {
        return [
            '' => __('account.not_applicable'),
            'saving_current' => __('account.saving_current'),
            'capital' => __('account.capital')
        ];
    }

    public function account_type()
    {
        return $this->belongsTo(\App\AccountType::class, 'account_type_id');
    }
    public static function items($type=NULL,$user=null)
    {
        $business_id = ($user==null)?session()->get('user.id'):$user->business_id;
        $allData     = Account::where('business_id',$business_id)
                                ->OrderBy('id','desc')
                                ->where('cost_center',0) 
                                ->where(function($query) use($type,$business_id){
                                    if ($type > 0) {
                                        $busi   = \App\Business::find($business_id);  
                                        $query->whereIn('account_type_id',[$busi->bank,$busi->cash]);
                                      }
                                })->get();
        $arr  = [];
        foreach ($allData as $data) {
           $arr[$data->id] =  $data->name . " || " . $data->account_number;
        }
        
        return $arr;
    }
    public static function main($type='cash',$business_id=null,$bank=null,$balance=null)
    {
        if($business_id == null){
            $business_id = request()->session()->get('user.business_id');
        }
        $business        = \App\Business::find($business_id);
        if($bank === null){
            if($type == 'cash'){
                $types       =  [$business->cash] ;
            }else{
                $types       = AccountType::where('business_id',$business_id)
                                    ->where('name','LIKE','%'.$type.'%')->pluck('id');
            }
        }else{
            if($type == 'cash' && $bank != null){
                $types       =  [$business->cash,$business->bank] ;
            }else{
                $types       = AccountType::where('business_id',$business_id)
                                    ->where('name','LIKE','%'.$type.'%')->pluck('id');
                
            }
        
        }
        $setting     = \App\Models\SystemAccount::where("business_id",$business_id)->first();
        if($setting){
            $allData     = Account::whereIn('account_type_id',$types)->where("id","!=",$setting->journal_expense_tax)->get();
        }else{
            $allData     = Account::whereIn('account_type_id',$types)->get();
        }
        $arr         = [];
        foreach ($allData as $data) {
            if($balance == null){
                $arr[$data->id] =  $data->account_number . " | " .$data->name;
            }else{
                if($balance == 1){
                    $arr[$data->account_number . " | " .$data->name] = $data->balance ;
                }else{
                    $arr[$data->account_number . " | " .$data->name] = $data->id ;
                }
            }
        }
        return $arr;
    }

    public static function accounts($business_id)
    {
        $business    = \App\Business::find($business_id);
        $bank        = $business->bank;
        $cash        = $business->cash;
        $ids         = [];
        $ids[]       = $bank;
        $ids[]       = $cash;
        $types       = AccountType::whereIn("id",$ids)->pluck("id");
        $allData     = Account::whereIn('account_type_id',$types)->get();
        $arr         = [];
        foreach ($allData as $data) {
            $arr[$data->id] =  $data->name;
        }
        return $arr;
    }

    public static function cost_center_list()
    {
        $business_id  = request()->session()->get('user.business_id');
        $cost_center  = Account::where("business_id",$business_id)->where("cost_center",1)->get();
        $cost_centers = [];
        foreach($cost_center as $key => $value){$cost_centers[$value->id]=$value->name;}
        return $cost_centers;
    }
    public static function add_main($type)
    {
        $business_id = request()->session()->get('user.business_id');
        $account     = \App\Account::where('name',$type)->where('business_id',$business_id)->first();
        if (empty($account)) {
            $account =  new \App\Account;
            $account->name =  $type;
            $account->business_id =  $business_id;
            $account->name        =  $type;
            $account->account_number  =  '00000'.$type;
            $account->save();
        } 
        return $account->id;
    }
    public static function cost_centers()
    {
        $business_id = request()->session()->get('user.business_id');
        $allData     = \App\Account::where('cost_center',1)
                            ->where('business_id',$business_id)->get();
        $arr  =  [];
        foreach ($allData as $data) {
            $arr[$data->id] =  $data->name;
        }
        return $arr;
    }
    // ** FOR GET LANG
    public static function getLang() {
        $config_languages = config('constants.langs');
        $languages        = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }
        return $languages;
    }
    
    // ** FOR GET ACCOUNT
    public static function getAccount($id,$business_id) {
        $account = Account::where('business_id', $business_id)
                        ->with(['account_type', 'account_type.parent_account'])
                        ->findOrFail($id); 
        return $account;
    }
    // ** FOR COST CENTER DROPDOWN
    public static function Cost_center() {
        $account_cost =  Account::where("cost_center",1)->get();
        $CostCenter   =  [];
        foreach($account_cost as $i){
            $CostCenter[$i->id]= $i->name . " || " . $i->account_number;
        }
        return $CostCenter;
    }
    
    // ** FOR ACCOUNT LEDGER 
    public static function getALeger($business_id,$id,$transaction_type,$t_check_box,$start_date, $end_date,$t_cost_center) {
       
        $accounts = AccountTransaction::join(
            'accounts as A',
            'account_transactions.account_id',
            '=',
            'A.id'
        )
        ->leftJoin('transaction_payments AS tp', 'account_transactions.transaction_payment_id', '=', 'tp.id')
        ->leftJoin('users AS u', 'account_transactions.created_by', '=', 'u.id')
        ->leftJoin('contacts AS c', 'tp.payment_for', '=', 'c.id')
        ->where('A.business_id', $business_id)
        ->where('A.id', $id)
        ->with(['transaction', 'transaction.contact', 'transfer_transaction', 'media', 'transfer_transaction.media']);
        if (!empty($transaction_type)) {
            $accounts->where('account_transactions.type',$transaction_type);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $accounts->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date]);
        }
        $accounts->where("for_repeat","=",null);
        $accounts->where("account_transactions.amount",">",0);
        $accounts->orderBy("account_transactions.operation_date","asc");
        $accounts->orderByRaw('ISNULL(account_transactions.daily_payment_item_id), account_transactions.operation_date asc, account_transactions.daily_payment_item_id asc');
        //...... cost center section 
   
        if($t_cost_center != null){
            $accounts->Where("account_transactions.cs_related_id",$t_cost_center);
            $accounts->select(['account_transactions.type','account_transactions.account_id' ,'account_transactions.payment_voucher_id','account_transactions.created_by','account_transactions.check_id','account_transactions.gournal_voucher_item_id','account_transactions.daily_payment_item_id', 'account_transactions.amount','account_transactions.for_repeat', 'account_transactions.operation_date',
                    'account_transactions.sub_type', 'account_transactions.transfer_transaction_id',
                    'account_transactions.transaction_id',
                    'account_transactions.id',
                    'account_transactions.note',
                    'account_transactions.cs_related_id',
                    'account_transactions.id_delete',
                    'tp.is_advance',
                    'tp.payment_ref_no',
                    'c.name as payment_for',
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                    ])
                    ->orderBy('account_transactions.operation_date', 'asc')
                    ->orderBy('account_transactions.id', 'asc')
                    ->groupBy('account_transactions.id') 
                    ;        
        }else{
            $accounts->select(['account_transactions.type','account_transactions.account_id' ,'account_transactions.payment_voucher_id','account_transactions.created_by','account_transactions.check_id','account_transactions.gournal_voucher_item_id','account_transactions.daily_payment_item_id', 'account_transactions.amount','account_transactions.for_repeat', 'operation_date',
                    'sub_type', 'transfer_transaction_id',
                    'account_transactions.transaction_id',
                    'account_transactions.id',
                    'account_transactions.note',
                    'account_transactions.current_balance',
                    'account_transactions.cs_related_id',
                    'account_transactions.id_delete',
                    'tp.is_advance',
                    'tp.payment_ref_no',
                    'c.name as payment_for',
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                    ])
                    ->orderBy('account_transactions.operation_date', 'asc')
                    ->orderBy('account_transactions.id', 'asc')
                    ->groupBy('account_transactions.id') 
                    ;
        
        
        } 
        return $accounts;
    }

    // *** AGT8422 FOR CALCULATE BALANCE
    public static function row_balance($row,$accounts,$check_box,$rows_count,$id_first,$type) {
        $os_debit   = \App\AccountTransaction::where(function($q) use($row,$check_box,$rows_count,$id_first,$type){
                                                    $q->where('account_id',$row->account_id);
                                                    if($check_box!=null){
                                                        if($id_first == $row->id){
                                                            $q->where('id','=',$row->id); 
                                                        }else{
                                                            // $q->where('id','<=',$row->id); 
                                                            // $q->where('id','>=',$id_first); 
                                                            if($id_first < $row->id){
                                                                $q->where('id','<=',$row->id); 
                                                                // $q->where('id','>=',$id_first); 
                                                                // $q->orWhere('id','<',$id_first); 
                                                                $acccountTransaction = \App\AccountTransaction::find($id_first);
                                                                $q->where('operation_date','>=',$acccountTransaction->operation_date);
                                                                $q->where('operation_date','<=',$row->operation_date);
                                                            }else{
                                                                $q->where('id','<=',$row->id); 
                                                                $q->where('id','<',$id_first);
                                                                $acccountTransaction = \App\AccountTransaction::find($id_first);
                                                                $q->where('operation_date','>=',$acccountTransaction->operation_date);
                                                            }
                                                        }
                                                    }else{
                                                        $q->where('id','<=',$row->id); 
                                                    }
                                                    $q->where("for_repeat","=",null); 
                                                    $q->where('operation_date','<=',$row->operation_date);
                                                    $q->where('type',$type);
                                                })->orWhere(function($q) use($row,$type){
                                                        $q->where('account_id',$row->account_id);
                                                        $q->where("for_repeat","=",null); 
                                                        $q->where('id','>',$row->id); 
                                                        $q->where('operation_date','<',$row->operation_date);
                                                        $q->where('type',$type);
                                                })->sum('amount');
        return   $os_debit ;                                     

    }

    // *** AGT8422 FOR CALCULATE AMOUNT BALANCE
    public static function amount_balance($row,$accounts,$check_box,$rows_count,$id_first,$start_date,$type,$cost_center = null) {
        $os_debit        = \App\AccountTransaction::where(function($q) use($row,$check_box,$rows_count,$id_first,$start_date,$type,$cost_center) {
                                $q->where('account_id',$row->account_id);
                                 if($check_box!=null){
                                    if($id_first == $row->id){
                                        $q->where('id','=',$row->id); 
                                    }else{
                                        $q->where('id','<=',$row->id);
                                        if($id_first!=null){ 
                                             
                                            $q->where('id','>=',$id_first); 
                                        }
                                    }
                                }else{
                                    $q->where('id','<=',$row->id); 
                                }
                                if($cost_center!=null){
                                    $q->where('cs_related_id', $cost_center);
                                }
                                $q->where("for_repeat","=",null); 
                                $q->where('operation_date','<',$start_date);
                                $q->where('type', $type);
                            })->orWhere(function($q) use($row,$start_date,$type,$cost_center){
                                    $q->where('account_id',$row->account_id);
                                    $q->where("for_repeat","=",null); 
                                    $q->where('id','>',$row->id); 
                                    if($cost_center!=null){
                                        $q->where('cs_related_id', $cost_center);
                                    }
                                    $q->where('operation_date','<',$start_date);
                                    $q->where('type',$type);
                            })->sum('amount');
        return   $os_debit ; 
    }
    // *** AGT FOR COST 
    public static function getCost($accounts,$t_cost_center,$allDataFilter) {
        //...... cost center section 
        $cost_center =\App\AccountTransaction::whereHas("account",function($query) use($t_cost_center) {
                                    $query->where("cost_center",1);
                                })->select("*")->get();
        $id_account            = [];
        $tr                    = [];                                          
        $daily                 = [];                                          
        $gournal               = [];                                          
        $additional            = [];                                          
        $return                = [];
        $list_tr               = [];                                          
        $list_daily            = [];                                          
        $list_gournal          = [];                                          
        $list_additional       = [];                                          
        $list_return           = [];
        $filter                = [];                                
        foreach($cost_center as $it){
            if ($it->transaction_id  != null ){
                if($t_cost_center != null && $it->account_id == $t_cost_center){
                    $list_tr[]              = $it->transaction_id;
                    $filter["transaction"]  = $list_tr;
                }
                $tr[]                       = $it->transaction_id; 
                $id_account["transaction"]  = $tr;
            }else if ($it->daily_payment_item_id != null ){
                if($t_cost_center != null && $it->account_id == $t_cost_center){
                    $list_daily[]              = $it->daily_payment_item_id;
                    $filter["daily"]           = $list_daily;
                }
                $daily[]                    = $it->daily_payment_item_id;
                $id_account["daily"]        = $daily;
            }else if ($it->gournal_voucher_item_id  != null){
                if($t_cost_center != null && $it->account_id == $t_cost_center){
                    $list_gournal[]              = $it->gournal_voucher_item_id;
                    $filter["gournal"]           = $list_gournal;
                }
                $gournal[]                  = $it->gournal_voucher_item_id;
                $id_account["gournal"]      = $gournal;
            }else if ($it->additional_shipping_item_id    != null){
                if($t_cost_center != null && $it->account_id == $t_cost_center){
                    $list_additional[]              = $it->additional_shipping_item_id;
                    $filter["additional"]           = $list_additional;
                }
                $additional[]               = $it->additional_shipping_item_id;
                $id_account["additional"]   = $additional;
            }else if ($it->return_transaction_id   != null){
                if($t_cost_center != null && $it->account_id == $t_cost_center){
                    $list_return[]              = $it->return_transaction_id;
                    $filter["return"]           = $list_return;
                }
                $return[]                   = $it->return_transaction_id ;
                $id_account["return"]       = $return;
            }
        }
        $filterList ["filter"]     = $filter;
        $filterList ["id_account"] = $id_account;
        return $filterList; 
    }

    // #### Cancellation ##### *** AGT8422 FOR CALCULATE ROW BALANCE
    public static function row_balance_one($id,$type) {
        $row        = \App\AccountTransaction::find($id);
        $os_debit   = \App\AccountTransaction::where(function($q) use($id,$row,$type){
                                                    $q->where('account_id',$row->account_id);
                                                    // if($check_box!=null){
                                                    //     if($id_first == $row->id){
                                                    //         $q->where('id','=',$row->id); 
                                                    //     }else{
                                                    //         $q->where('id','<=',$row->id); 
                                                    //         $q->where('id','>=',$id_first); 
                                                    //     }
                                                    // }else{
                                                    //     $q->where('id','<=',$row->id); 
                                                    // }
                                                    $q->where("for_repeat","=",null); 
                                                    $q->where('operation_date','<=',$row->operation_date);
                                                    $q->where('type',$type);
                                                })->orWhere(function($q) use($id,$type,$row){
                                                        $q->where('account_id',$row->account_id);
                                                        $q->where("for_repeat","=",null); 
                                                        $q->where('id','>',$row->id); 
                                                        $q->where('operation_date','<',$row->operation_date);
                                                        $q->where('type',$type);
                                                })->sum('amount');
        return   $os_debit ;                                     

    }
}
