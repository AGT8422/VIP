<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\Util;

class ParentArchive extends Model
{
    use HasFactory;

    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }

    public function items()
    {
        return $this->hasMany('App\Models\ChildArchive','additional_shipping_id');
    }
    public static function ship_save_parent($id,$action_type)
    {
        $old_ship_parent   = \App\Models\AdditionalShipping::find($id); 
        $transactions_olds = \App\Transaction::find($old_ship_parent->transaction_id); 
        $business_id       = (!empty($transactions_olds))?$transactions_olds->business_id:request()->session()->get("user.business_id");
       
        if($old_ship_parent){
            $type                                    = 'log_file';
            $ref_count                               = Util::setAndGetReferenceCount($type,$business_id);
            $reciept_no                              = Util::generateReferenceNumber($type, $ref_count,$business_id,"LOG_");
            $new_ship_parent                         = new ParentArchive;
            $new_ship_parent->business_id            = $business_id ;
            $new_ship_parent->additional_shipping_id = $id ;
            $new_ship_parent->ship_transaction_id    = $old_ship_parent->transaction_id ;
            // $new_ship_parent->document               = $old_ship_parent->document ;
            $new_ship_parent->sub_total              = $old_ship_parent->sub_total ;
            $new_ship_parent->type                   = $old_ship_parent->type ;
            $new_ship_parent->total_purchase         = $old_ship_parent->total_purchase ;
            $new_ship_parent->total_ship             = $old_ship_parent->total_ship ;
            $new_ship_parent->t_recieved             = $old_ship_parent->t_recieved ;
            $new_ship_parent->updated_at             = $old_ship_parent->updated_at ;
            $new_ship_parent->created_at             = $old_ship_parent->created_at ;
            $new_ship_parent->ref_number             = "SHIP_" . $reciept_no; 
            $new_ship_parent->state_action           = ($action_type=="create")?"Add":"Edit"; 
            $new_ship_parent->parent_id              = $id ; 
            $new_ship_parent->save() ;
            
            return $new_ship_parent;
        }else{
            return null;
            
        }

    }
    public static function  save_payment_parent($id,$action_type,$old=null)
    {

        $old_payment_parent = \App\TransactionPayment::find($id); 
        $business_id = $old_payment_parent->business_id ;
        $archive     = \App\Models\ParentArchive::orderBy("id","desc")->where("line_id",$id)->first();
        if($old_payment_parent){
            $type                                        = 'log_file';
            $ref_count                                   = Util::setAndGetReferenceCount($type ,$old_payment_parent->business_id );
            $reciept_no                                  = Util::generateReferenceNumber($type, $ref_count,$old_payment_parent->business_id,"LOG_");
          
            $new_payment_parent                          = new ParentArchive;
            $new_payment_parent->business_id             = ($old!=null)?$old->business_id:$old_payment_parent->business_id ;
            $new_payment_parent->payment_ref_no          = ($old!=null)?$old->payment_ref_no:$old_payment_parent->payment_ref_no ;
            $new_payment_parent->tp_transaction_no       = ($old!=null)?$old->transaction_id:$old_payment_parent->transaction_id  ;
            $new_payment_parent->is_return               = ($old!=null)?$old->is_return:$old_payment_parent->is_return ;
            $new_payment_parent->amount                  = ($old!=null)?$old->amount:$old_payment_parent->amount ;
            $new_payment_parent->method                  = ($old!=null)?$old->method:$old_payment_parent->method ;
            $new_payment_parent->card_transaction_number = ($old!=null)?$old->card_transaction_number:$old_payment_parent->card_transaction_number ;
            $new_payment_parent->card_number             = ($old!=null)?$old->card_number:$old_payment_parent->card_number ;
            $new_payment_parent->card_type               = ($old!=null)?$old->card_type:$old_payment_parent->card_type; //.......
            $new_payment_parent->card_holder_name        = ($old!=null)?$old->card_holder_name:$old_payment_parent->card_holder_name ;
            $new_payment_parent->card_month              = ($old!=null)?$old->card_month:$old_payment_parent->card_month ;
            $new_payment_parent->card_year               = ($old!=null)?$old->card_year:$old_payment_parent->card_year ;
            $new_payment_parent->card_security           = ($old!=null)?$old->card_security:$old_payment_parent->card_security ;
            $new_payment_parent->cheque_number           = ($old!=null)?$old->cheque_number:$old_payment_parent->cheque_number ;
            $new_payment_parent->bank_account_number     = ($old!=null)?$old->bank_account_number:$old_payment_parent->bank_account_number ;
            $new_payment_parent->paid_on                 = ($old!=null)?$old->paid_on:$old_payment_parent->paid_on ;
            $new_payment_parent->created_by              = ($old!=null)?$old->created_by:$old_payment_parent->created_by  ;
            $new_payment_parent->is_advance              = ($old!=null)?$old->is_advance:$old_payment_parent->is_advance ;
            $new_payment_parent->payment_for             = ($old!=null)?$old->payment_for:$old_payment_parent->payment_for ;
            $new_payment_parent->parent_id               = ($old!=null)?$old->parent_id:$old_payment_parent->parent_id  ;
            $new_payment_parent->note                    = ($old!=null)?$old->note:$old_payment_parent->note ;
            $new_payment_parent->document                = ($old!=null)?$old->document:$old_payment_parent->document ;
            $new_payment_parent->account_id              = ($old!=null)?$old->account_id:$old_payment_parent->account_id ;
            $new_payment_parent->created_at              = ($old!=null)?$old->created_at:$old_payment_parent->created_at ;
            $new_payment_parent->updated_at              = ($old!=null)?$old->updated_at:$old_payment_parent->updated_at ;
             $new_payment_parent->contact_type           = ($old!=null)?$old->contact_type:$old_payment_parent->contact_type ;
            $new_payment_parent->prepaid                 = ($old!=null)?$old->prepaid:$old_payment_parent->prepaid ;
            $new_payment_parent->amount_second_curr      = ($old!=null)?$old->amount_second_curr:$old_payment_parent->amount_second_curr ;
            $new_payment_parent->check_id                = ($old!=null)?$old->check_id:$old_payment_parent->check_id ;
            $new_payment_parent->payment_voucher_id      = ($old!=null)?$old->payment_voucher_id:$old_payment_parent->payment_voucher_id ;
            $new_payment_parent->source                  = ($old!=null)?$old->source:$old_payment_parent->source ;
            $new_payment_parent->line_id                 = ($old!=null)?$id:$old_payment_parent->id ;
            $new_payment_parent->ref_number              = "PAYMENT_" . $reciept_no; 
            $new_payment_parent->state_action            = $action_type; 
            $new_payment_parent->log_parent_id           = ($archive)?$archive->id:null ; //.......
            $new_payment_parent->save() ;
            
            return $new_payment_parent;
        }else{
            return null;
            
        }

    }
}
