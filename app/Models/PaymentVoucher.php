<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Contact;

class PaymentVoucher extends Model
{
    use HasFactory,SoftDeletes;
    protected  $appends = ['margin'];
    public static function types()
    {
        return [
            0 => trans('home.Payment Voucher'),
            1 => trans('home.Receipt voucher')
        ];
    }
    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }
    public function contact()
    {
        return $this->belongsTo('\App\Contact','contact_id');
    }
    public function account()
    {
        return $this->belongsTo('\App\Account','account_id');
    }
    public function account_tansaction()
    {
        return $this->belongsTo('\App\AccountTransaction','payment_voucher_id');
    }
   
    public function business()
    {
        return $this->belongsTo('\App\Business','business_id');
    }
    public function payments()
    {
        return $this->hasMany('\App\TransactionPayment','payment_voucher_id');
    }
    public function getMarginAttribute()
    {
        return ($this->attributes['amount'] - $this->payments->sum('amount') );
    }
    public static function add_voucher_payment($data_old,$type=null,$transaction=null,$is_invoice=null)
    {
        DB::beginTransaction();
        
        # Generate reference number
        $ref_count          = ProductUtil::setAndGetReferenceCount("voucher");
        $ref_no             = ProductUtil::generateReferenceNumber("voucher" , $ref_count);
        # ..........................................
        $data               =  new PaymentVoucher;
        $data->business_id  =  $data_old->business_id;
        $data->ref_no       =  $ref_no;
        $data->amount       =  $data_old->amount;
        $data->account_id   =  $data_old->account_id;
        $data->contact_id   =  $data_old->transaction->contact_id;
        $data->type         =  $type;
        $data->is_invoice   =  $is_invoice;
        $data->text         =  $data_old->note;
        $data->date         =  $data_old->paid_on;
        $data->save();
        
        PaymentVoucher::effect_account_payment($data->id,$data->type,$data_old);
        \App\AccountTransaction::where("transaction_payment_id",$data_old->id)->update([
            "payment_voucher_id"=>$data->id
        ]);
        \App\AccountTransaction::where("payment_voucher_id",$data->id)->whereNull("transaction_id")->update([
            "for_repeat"=>null
        ]);
        \App\AccountTransaction::where("payment_voucher_id",$data->id)->whereNotNull("transaction_id")->update([
            "for_repeat"=>1
        ]);
        \App\AccountTransaction::where("payment_voucher_id",$data->id)->where("for_repeat",1)->update([
            "transaction_id"=>$data_old->transaction->id
        ]);
        \App\AccountTransaction::where("payment_voucher_id",$data->id)->where("for_repeat",null)->update([
            "transaction_id"=>null
        ]);
        $type="voucher";
        \App\Models\Entry::create_entries($data,$type);
        DB::commit();
         
    }
    public static function effect_account_payment($id,$type,$data_pay=null)
    {
        # supplier debit  => bank  credit
        # customer credit => debit  
        $payment_id = null;
        if($data_pay != null){
            $payment_id = $data_pay->id;
        }
        $data      =  PaymentVoucher::find($id);
        $state     =  'debit';
        $re_state  =  'credit';
        if ($type == 1 ) {
            $state     =  'credit';
            $re_state  =  'debit';
        }
        # effect cash  account 
        $credit_data = [
            'amount'                 => $data->amount,
            'account_id'             => $data->account_id,
            'type'                   => $re_state,
            'sub_type'               => 'deposit',
            'operation_date'         => $data->date,
            'created_by'             => session()->get('user.id'),
            'note'                   => $data->text,
            'for_repeat'             => null,
            'payment_voucher_id'     => $id,
            'transaction_payment_id' => $payment_id
        ];
        $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
        
        $account = \App\Account::find($data->account_id);
        if($account->cost_center!=1){
             \App\AccountTransaction::nextRecords($account->id,$account->business_id,$data->date);
        }
        # effect contact account 
        $account_id  =  Contact::add_account($data->contact_id);
        $credit_data = [
            'amount'                 => $data->amount,
            'account_id'             => $account_id,
            'type'                   => $state,
            'sub_type'               => 'deposit',
            'operation_date'         => $data->date,
            'created_by'             => session()->get('user.id'),
            'note'                   => $data->text,
            'for_repeat'             => null,
            'payment_voucher_id'     => $data->id,
            'transaction_payment_id' => $payment_id 
        ];
        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
        $account = \App\Account::find($account_id);
        if($account->cost_center!=1){ 
            \App\AccountTransaction::nextRecords($account->id,$account->business_id,$data->date);
        }
    } 
   
}
