<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\TransactionPaymentDeleted;
use App\Events\TransactionPaymentUpdated;

class TransactionPayment extends Model
{
    use SoftDeletes;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the phone record associated with the user.
     */
    public function payment_account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id');
    }

    /**
     * Get the transaction related to this payment.
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }

    /**
     * Get the user.
     */
    public function created_user()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get child payments
     */
    public function child_payments()
    {
        return $this->hasMany(\App\TransactionPayment::class, 'parent_id');
    }
    /**
     * Get child payments
     */
    public function check()
    {
        return $this->belongsTo(\App\Models\Check::class, 'check_id');;
    }

    public function voucher()
    {
        return $this->belongsTo(\App\Models\PaymentVoucher::class, 'payment_voucher_id');
    }
    public function account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id');
    }
    /**
     * Retrieves documents path if exists
     */
    public function getDocumentPathAttribute()
    {
        $path = !empty($this->document) ? asset('public/uploads/documents/' . $this->document) : null;
        
        return $path;
    }

    /**
     * Removes timestamp from document name
     */
    public function getDocumentNameAttribute()
    {
        $document_name = !empty(explode("_", $this->document, 2)[1]) ? explode("_", $this->document, 2)[1] : $this->document ;
        return $document_name;
    }

    public static function deletePayment($payment)
    {
        //Update parent payment if exists
        if (!empty($payment->parent_id)) {
            $parent_payment = TransactionPayment::find($payment->parent_id);
            $parent_payment->amount -= $payment->amount;

            if ($parent_payment->amount <= 0) {
                $parent_payment->delete();
                event(new TransactionPaymentDeleted($parent_payment));
            } else {
                $parent_payment->save();
                //Add event to update parent payment account transaction
                event(new TransactionPaymentUpdated($parent_payment, null));
            }
        }

        $payment->delete();

        if(!empty($payment->transaction_id)) {
            $transactionUtil = new \App\Utils\TransactionUtil();
            //update payment status
            $transaction = $payment->load('transaction')->transaction;
            $transaction_before = $transaction->replicate();

            $payment_status = $transactionUtil->updatePaymentStatus($payment->transaction_id);

            $transaction->payment_status = $payment_status;
            
            $transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);
        }

        //Add event to delete account transaction
        event(new TransactionPaymentDeleted($payment));
        
    }
    // public function FunctionName(Type $var = null)
    // {
    //     # code...
    // }
}
