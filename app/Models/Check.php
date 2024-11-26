<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\ProductUtil;
use App\Utils\Util;


class Check extends Model
{
    use HasFactory;
    protected $appends =  ['type_name','status_name','margin'];
    public static function types()
    {
        return [
            0=>trans('home.Cheque for collection'),
            1=>trans('home.Cheque for debit')
        ];
    } 
    public static function status_types()
    {
        return [
            0=>trans('home.write'),
            1=>trans('home.collected'),
            2=>trans('home.Refund'),
            3=>trans('home.Delete Collect'),
            4=>trans('home.Un Collect'),
           
        ];
    }
    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }
    public function getTypeNameAttribute()
    {
        return ($this->attributes['type'] == 1)?trans('home.Cheque for debit')
                    :trans('home.Cheque for collection');
    }
    public function getStatusNameAttribute()
    {
       if (array_key_exists($this->attributes['status'],Check::status_types())) {
           return Check::status_types()[$this->attributes['status']];
       }
    }
    public function contact()
    {
        return $this->belongsTo('App\Contact','contact_id');
    }
    public function contacts()
    {
        return $this->belongsTo('App\Account','contact_id');
    }
    public function account()
    {
        return $this->belongsTo('App\Account','account_id');
    }
    public function business()
    {
        return $this->belongsTo('App\Business','business_id');
    }
    public function bank()
    {
        return $this->belongsTo('App\Models\ContactBank','contact_bank_id');
    }
    public function location()
    {
        return $this->belongsTo('App\BusinessLocation','location_id');
    }
    public function collecting_account()
    {
        return $this->belongsTo('App\Account','collect_account_id');
    }
     
    public function check()
    {
      return $this->belongsTo("\App\Models\Entry","ref_no");
    }

    public function actions()
    {
        return $this->hasMany('App\Models\ChequeAction','check_id');
    }
   
    public function transaction()
    {
        return $this->belongsTo('App\Transaction','transaction_id');
    }
    public static function add_action($id,$action="add")
    {
         $data =  ChequeAction::where('check_id',$id)->where('type',$action)->first();
         if(empty($data)){
            $data           =  new ChequeAction;
            $data->type     = $action;
            $data->check_id =  $id;
            $data->save();
        }
    }
    public static function add_cheque($transaction,$inputs)
    {
        if ($inputs['cheque_number']  &&  $inputs['write_date'] && $inputs['due_date']) {
            $ref_count                    =  ProductUtil::setAndGetReferenceCount("Cheque");
            $ref_no                       =  ProductUtil::generateReferenceNumber("Cheque" , $ref_count);
            $business_id                  =  request()->session()->get('user.business_id');
            $setting                      =  \App\Models\SystemAccount::where('business_id',$business_id)->first();
            $type                         =  ($transaction->type == 'purchase')?1:0;
            $id                           =  ($type == 0)?$setting->cheque_collection:$setting->cheque_debit;
            # .........................................
            $data                         =  new Check;
            $data->cheque_no              =  $inputs['cheque_number'];
            $data->account_id             =  $id;
            $data->location_id            =  $transaction->location->id;
            $data->write_date             =  $inputs['write_date'];
            $data->due_date               =  $inputs['due_date'];
            $data->contact_bank_id        =  (isset( $inputs['cheque_bank']))?$inputs['cheque_bank']:NULL;
            $data->transaction_payment_id = (isset( $inputs['transaction_payment_id']))?$inputs['transaction_payment_id']:NULL;
            $data->contact_id             =  $transaction ->contact_id;
            $data->amount                 =  $inputs['amount'];
            $data->business_id            =  $business_id;
            $data->transaction_id         =  $transaction->id;
            $data->ref_no                 =  $ref_no;
            $data->account_type           =  0;
            $data->type                   = ($transaction->type == 'purchase')?1:0;
            $data->save();
            # ...........................................
            \App\Models\StatusLive::insert_data_c($business_id,$transaction,$data,"Add Cheque");
            $type        = ($data->type == 0)?'debit':'credit';
            $credit_data = [
                'amount'                 => $inputs['amount'],
                'account_id'             => $id,
                'type'                   => $type,
                'sub_type'               => 'deposit',
                'operation_date'         => $data->write_date,
                'created_by'             => session()->get('user.id'),
                'note'                   => 'added cheque',
                'check_id'               => $data->id,
                'for_repeat'             => 1,
                'transaction_payment_id' => $data->transaction_payment_id,
                // 'transaction_id'=>$data->transaction_id,
            ];
 
            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            $accountCheck = \App\Account::find($id);
            if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$data->write_date); }
    
            Check::contact_effect($data->id,null,$transaction->id);
            
            
            
        }
    }
    public function add_sell($transaction,$inputs)
    { 
        
        if ($inputs['cheque_number'] && $inputs['cheque_account'] &&  $inputs['write_date'] && $inputs['due_date']) {
            $ref_count               = ProductUtil::setAndGetReferenceCount("Cheque");
            $ref_no                  = ProductUtil::generateReferenceNumber("Cheque" , $ref_count);
            $business_id             = request()->session()->get('user.business_id');
            $setting                 =  \App\Models\SystemAccount::where('business_id',$business_id)->first();
            $type                    =  ($transaction->type == 'purchase')?1:0;
            $id                      =   ($type == 0)?$setting->cheque_collection:$setting->cheque_debit;
            # ...................................................
            $data                    =  new Check;
            $data->cheque_no         =  $inputs['cheque_number'];
            $data->account_id        =  $id;
            $data->write_date        =  $inputs['write_date'];
            $data->due_date          =  $inputs['due_date'];
            $data->contact_bank_id   =  (isset( $inputs['cheque_bank']))?$inputs['cheque_bank']:NULL;
            $data->transaction_payment_id   = (isset( $inputs['transaction_payment_id']))?$inputs['transaction_payment_id']:NULL;
            $data->contact_id        =  $transaction ->contact_id;
            $data->amount            =  $inputs['amount'];
            $data->business_id       =  $business_id;
            $data->transaction_id    =  $transaction->id;
            $data->ref_no            =  $ref_no;
            $data->type              = ($transaction->type == 'purchase')?1:0;
            $data->save();
            # ...................................................
            \App\Models\StatusLive::insert_data_c($business_id,$transaction,$data,"Add Cheque");
            $type        = ($data->type == 0)?'debit':'credit';
            $credit_data = [
                'amount'         => $data->amount,
                'account_id'     => $id,
                'type'           => $type,
                'sub_type'       => 'deposit',
                'operation_date' => $data->write_date,
                'created_by'     => session()->get('user.id'),
                'note'           => 'added cheque',
                'check_id'       => $data->id,
                'for_repeat'     => 1,
            ];
            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            $accountCheck = \App\Account::find($id);
            if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$data->write_date); }
    
            Check::contact_effect($data->id);
        }
    }
    public static function contact_effect($id,$transaction=null,$id_trans=null,$all=null,$id_account=null,$user=null)
    {
        $data  =  Check::find($id);

        if ($data) {
            $state =  ($data->type ==  0)?'credit':'debit';
            if($all != null){
                $account  =  \App\Account::where('id',$id_account)->first();
            }else{
                $account  =  \App\Account::where('contact_id',$data->contact_id)->first();
            }
            if ($account) {
                $credit_data = [
                    'amount'                 => $data->amount,
                    'account_id'             => $account->id,
                    'transaction_id'         => $id_trans,
                    'type'                   => $state,
                    'sub_type'               => 'deposit',
                    'operation_date'         => \Carbon::createFromFormat('Y-m-d', $data->write_date)->toDateString(),
                    'created_by'             => ($transaction != null)?(($user!=null)?$user:$transaction->created_by):(($user!=null)?$user:session()->get('user.id')),
                    'note'                   => 'Add Cheque',
                    'check_id'               => $id,
                    'for_repeat'             => null,
                    'transaction_array'      => $transaction,
                    'transaction_payment_id' => $data->transaction_payment_id,
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                $accountCheck = \App\Account::find($account->id);
                if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,\Carbon::createFromFormat('Y-m-d', $data->write_date)->toDateString()); }
    
            }
        }
        
        
    }
    public static function refund($id,$type=null,$user=null)
    {
        $data             =  Check::find($id);
        $state            =  ($data->type ==  0)?'debit':'credit';
        $re_state         =  ($data->type ==  0)?'credit':'debit';
        if ($data) {
                if($type != null){
                    $account  =  \App\Account::where('id',$data->contact_id)->first();
                }else{
                    $account  =  \App\Account::where('contact_id',$data->contact_id)->first();
                }
                if ($account) {
                    $credit_data = [
                        'amount'         => $data->amount,
                        'account_id'     => $account->id,
                        'type'           => $state,
                        'sub_type'       => 'deposit',
                        'operation_date' => date('Y-m-d'),
                        'created_by'     => ($user==null)?session()->get('user.id'):$user->id,
                        'note'           => 'refund Collect',
                        'check_id'       => $id
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    $accountCheck = \App\Account::find($account->id);
                    if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,date('Y-m-d')); }
        
                }
        }
        $credit_data = [
            'amount'         => $data->amount,
            'account_id'     => $data->account_id,
            'type'           => $re_state,
            'sub_type'       => 'deposit',
            'operation_date' => date('Y-m-d'),
            'created_by'     => ($user==null)?session()->get('user.id'):$user->id,
            'note'           => 'refund Collect',
            'check_id'       => $id
        ];
        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
        $accountCheck = \App\Account::find($data->account_id);
        if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,date('Y-m-d')); }
        \App\AccountTransaction::where("check_id",$id)->update(["id_delete"=>1]);
    }
    public static function un_collect($id,$user=null)
    {
        $data        = Check::find($id);
        $re_type     = ($data->type == 0) ?'credit':'debit';
        $type        = ($data->type == 0) ?'debit':'credit';
        $credit_data = [
            'amount'         => $data->amount,
            'account_id'     => $data->collect_account_id,
            'type'           => $re_type,
            'sub_type'       => 'deposit',
            'operation_date' => date('Y-m-d'),
            'created_by'     => ($user==null)?session()->get('user.id'):$user->id,
            'note'           => 'un collecting cheque',
            'check_id'       => $data->id,
            'transaction_id' => $data->transaction_id
        ];
        \App\AccountTransaction::createAccountTransaction($credit_data);
        $accountCheck = \App\Account::find($data->collect_account_id);
        if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,date('Y-m-d')); }
        $credit_data = [
            'amount'         => $data->amount,
            'account_id'     => $data->account_id,
            'type'           => $type,
            'sub_type'       => 'deposit',
            'operation_date' => date('Y-m-d'),
            'created_by'     => ($user==null)?session()->get('user.id'):$user->id,
            'note'           => 'un collecting cheque',
            'check_id'       => $data->id,
            'transaction_id' => $data->transaction_id
        ];
        \App\AccountTransaction::createAccountTransaction($credit_data);
        $accountCheck = \App\Account::find($data->account_id);
        if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,date('Y-m-d')); }
    }
    public function payments()
    {
        return $this->hasMany('\App\TransactionPayment','check_id');
    }
    public function getMarginAttribute()
    {
        return ($this->attributes['amount'] - $this->payments->sum('amount') );
    }
}
