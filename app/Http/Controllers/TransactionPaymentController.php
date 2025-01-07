<?php

namespace App\Http\Controllers;

use App\Contact;

use App\Events\TransactionPaymentAdded;

use App\Events\TransactionPaymentUpdated;
use App\Transaction;
use App\PurchaseLine;
use App\TransactionPayment;
use App\BusinessLocation;
use App\Product;    
use App\MovementWarehouse;    
use App\VariationLocationDetails;
use Carbon\Carbon;
use App\Utils\ProductUtil;

use App\Models\RecievedWrong;
use App\Models\TransactionDelivery;
use App\Models\TransactionRecieved;
use App\Models\RecievedPrevious;
use App\Models\DeliveredPrevious;
use App\Models\DeliveredWrong;
use App\Models\Warehouse;
use App\Models\WarehouseInfo;
use App\Unit;
use App\Business;
use App\TransactionSellLine;

use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;

use Datatables;
use DB;
use Illuminate\Http\Request;
use App\Exceptions\AdvanceBalanceNotAvailable;

class TransactionPaymentController extends Controller
{
    protected $transactionUtil;
    protected $moduleUtil;
    protected $productUtil;

    /**
     * Constructor
    * * @param ProductUtils $product

     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil,ModuleUtil $moduleUtil)
    {        
        $this->productUtil = $productUtil;

        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
        'is_return' => 0, 'transaction_no' => ''];
    }

   


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        
        try {
            if (!auth()->user()->can('purchase.payments') || !auth()->user()->can('sell.payments')) {
                abort(403, 'Unauthorized action.');
            }
            $business_id        = $request->session()->get('user.business_id');
            $transaction_id     = $request->input('transaction_id');
            $transaction        = Transaction::where('business_id', $business_id)->with(['contact'])->findOrFail($transaction_id);
            
            if(isset($request->separate_pay_sell)){
                \DB::beginTransaction();
                $inputs = $request->only(['amount', 'method', 'note', 'card_number', 'card_holder_name',
                                        'card_transaction_number', 'card_type', 'card_month',
                                        'card_year', 'card_security','cheque_number', 'bank_account_number']);
              
                $new_separate                     =  $transaction->replicate();
                $sale_type                        =  'sale';
                $invoice_scheme_id                =  ($transaction->pattern)?$transaction->pattern->invoice_scheme:null;
                $invoice_no                       =  $this->transactionUtil->getInvoiceNumber($transaction->business_id, "final", $transaction->location_id, $invoice_scheme_id, $sale_type);
                $tax_amount                       =  \App\TaxRate::find($transaction->tax_id);
                $value                            =  ($tax_amount)?$tax_amount->amount:0;
                $new_separate->tax_amount         =  round((($this->transactionUtil->num_uf($inputs['amount'])) * (($value))/(100+$value)),2);
                $new_separate->invoice_no         =  $invoice_no;
                $new_separate->transaction_date   =  \Carbon::now();
                $new_separate->status             =  "final";
                $new_separate->sub_status         =  "final";
                $new_separate->separate_type      =  "payment" ;
                $new_separate->payment_status     =  "paid" ;
                $new_separate->discount_amount    =  round(0,2) ;
                $new_separate->total_before_tax   =  $this->transactionUtil->num_uf($inputs['amount']) - round((($this->transactionUtil->num_uf($inputs['amount'])) * (($value))/(100+$value)),2) ;
                $new_separate->final_total        =  round($this->transactionUtil->num_uf($inputs['amount']),2);
                $new_separate->separate_parent    =  $transaction->id ;
                $new_separate->created_by         =  auth()->user()->id ;
                $new_separate->save();
                \App\AccountTransaction::add_sell_pos($new_separate,$new_separate->pattern_id);
                
                if($request->method == 'cheque'){
                    $inputs['paid_on'] =  $request->write_date ;
                }else{
                    $inputs['paid_on'] =  $this->transactionUtil->uf_date($request->input('paid_on'), true);
                }
                $inputs['transaction_id'] = $new_separate->id;
                $inputs['amount']         = $this->transactionUtil->num_uf($inputs['amount']);
                $inputs['created_by']     = auth()->user()->id;
                $inputs['payment_for']    = $new_separate->contact_id;

                if ($inputs['method'] == 'custom_pay_1') {
                    $inputs['transaction_no'] = $request->input('transaction_no_1');
                } elseif ($inputs['method'] == 'custom_pay_2') {
                    $inputs['transaction_no'] = $request->input('transaction_no_2');
                } elseif ($inputs['method'] == 'custom_pay_3') {
                    $inputs['transaction_no'] = $request->input('transaction_no_3');
                }

                # cheque_account
                if ($inputs['method'] == 'cheque' && $request->input('cheque_account')) {
                    $inputs['account_id'] =  $request->input('cheque_account');
                }else{
                    if (!empty($request->input('account_id')) && $inputs['method'] != 'advance') {
                        $inputs['account_id'] = $request->input('account_id');
                    }
                }
                
                $prefix_type     = 'purchase_payment';
                if (in_array($new_separate->type, ['sale', 'sell_return'])) {
                    $prefix_type = 'sell_payment';
                } elseif (in_array($new_separate->type, ['expense', 'expense_refund'])) {
                    $prefix_type = 'expense_payment';
                }

                

                $ref_count                = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                # Generate reference number
                $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);
                $inputs['business_id']    = $request->session()->get('business.id');
                $inputs['source']         = $request->input('amount');
                $inputs['document']       = $this->transactionUtil->uploadFile($request, 'document', 'documents');
                $inputs['is_invoice']     = 1;
              
                # Pay from advance balance
                $payment_amount           = $inputs['amount'];
                $contact_balance          = !empty($new_separate->contact) ? $new_separate->contact->balance : 0;
                if ($inputs['method'] == 'advance' && $inputs['amount'] > $contact_balance) {
                    throw new AdvanceBalanceNotAvailable(__('lang_v1.required_advance_balance_not_available'));
                }
                if (!empty($inputs['amount'])) {
                    $tp                         = TransactionPayment::create($inputs);
                    $inputs['transaction_type'] = $new_separate->type;
                    event(new TransactionPaymentAdded($tp, $inputs));
                    
                    
                    if ($request->method == 'cheque') {
                        $business_id                          = request()->session()->get('user.business_id');
                        $setting                              = \App\Models\SystemAccount::where('business_id',$business_id)->where("pattern_id",$new_separate->pattern_id)->first();
                        $type                                 = ($new_separate->type == 'purchase' || $new_separate->type == 'sell_return')?1:0;
                        $account_id                           = ($type == 0)?$setting->cheque_collection:$setting->cheque_debit;
                        $rq_payment                           = [];
                        $rq_payment['amount']                 = $request->amount;
                        $rq_payment['cheque_number']          = $request->cheque_number;
                        $rq_payment['write_date']             = $request->write_date;
                        $rq_payment['due_date']               = $request->due_date;
                        $rq_payment['cheque_account']         = $account_id;
                        $rq_payment['cheque_bank']            = $request->bank_id;
                        $rq_payment['note']                   = $request->note;
                        $rq_payment['transaction_payment_id'] = $tp->id;
                        \App\Models\Check::add_cheque($new_separate,$rq_payment);
                        $check_id      = \App\Models\Check::where("transaction_payment_id",$tp->id)->first()->id;
                        $pay           = TransactionPayment::find($tp->id);
                        $pay->check_id = $check_id;
                        $entries       = \App\Models\Entry::where("payment_id",$tp->id)->first();
                        $pay->save();
                    }
                    # effecting account
                    if ($new_separate->type == 'purchase' || $new_separate->type == 'sell_return') {
                        $check_id = \App\Models\Check::where("transaction_payment_id",$tp->id)->first();
                        if($check_id != null){
                            $check_id->id;
                            $allChecks = \App\AccountTransaction::where('account_id',$tp->account_id)->where('transaction_payment_id',$tp->id)->get();
                            foreach($allChecks as $ck){
                                $ck->transaction_id = $new_separate->id;
                                $ck->type           = 'credit';
                                $ck->check_id       = $check_id->id ;
                                $ck->update();
                                $accounts_transaction = \App\Account::find($ck->account_id);
                                if($accounts_transaction->cost_center!=1){
                                    \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$ck->operation_date);
                                }
                            }
                        }else{
                            $Checks = \App\AccountTransaction::where('account_id',$tp->account_id)->where('transaction_payment_id',$tp->id)->get();
                            foreach($Checks as $ck){
                                $ck->type = "credit";
                                $ck->update();
                                $accounts_transaction = \App\Account::find($ck->account_id);
                                if($accounts_transaction->cost_center!=1){
                                   \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$ck->operation_date);
                                }
                            } 
                        }    
                    }else{
                        $check_id = \App\Models\Check::where("transaction_payment_id",$tp->id)->first();
                        if($check_id != null){
                            $check_id->id;
                            $allTransactionPayment =  \App\AccountTransaction::where('account_id',$tp->account_id)->where('transaction_payment_id',$tp->id)->get();
                            foreach($allTransactionPayment as $tpItem){
                                $tpItem->transaction_id = $new_separate->id;
                                $tpItem->type           = 'debit' ;
                                $tpItem->check_id       = $check_id->id;
                                $tpItem->update();
                                $accounts_transaction = \App\Account::find($tpItem->account_id);
                                if($accounts_transaction->cost_center!=1){
                                   \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$tpItem->operation_date);
                                }
                            } 
                        }else{
                            $allTransactionPayment =  \App\AccountTransaction::where('account_id',$tp->account_id)->where('transaction_payment_id',$tp->id)->get();
                            foreach($allTransactionPayment as $tpItem ){
                                $tpItem->type = 'debit';
                                $tpItem->update();
                                $accounts_transaction = \App\Account::find($tpItem->account_id);
                                if($accounts_transaction->cost_center!=1){
                                    \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$tpItem->operation_date);
                                }
                            }
                        } 
                    }

                    $acc         =  \App\Account::where('contact_id',$new_separate->contact_id)->first();
                    if ($acc) {
                        $type         =  ($new_separate->type == 'purchase' || $new_separate->type == 'sell_return')?'debit':'credit';
                        $check_id     =  \App\Models\Check::where("transaction_payment_id",$tp->id)->first();
                        if($check_id != null){
                            $pCheck_id = $check_id->id;
                        }else{
                            $pCheck_id = null;
                        }
                        $credit_data = [
                            'amount'                 => $request->amount,
                            'account_id'             => $acc->id,
                            'type'                   => $type,
                            'sub_type'               => 'deposit',
                            'operation_date'         => $this->transactionUtil->uf_date($request->input('paid_on'), true),
                            'created_by'             => session()->get('user.id'),
                            'note'                   => 'Add Payment',
                            'transaction_id'         => $new_separate->id,
                            'transaction_payment_id' => $tp->id,
                            'for_repeat'             => 1,
                            'check_id'               => $pCheck_id,
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                        if($acc->cost_center!=1){
                            \App\AccountTransaction::nextRecords($acc->id,$acc->business_id,$this->transactionUtil->uf_date($request->input('paid_on'), true));
                        }
                        if($check_id != null){
                            $types="PCheck";
                            \App\Models\Entry::create_entries($check_id,$types);
                        } 
                        if($request->method != 'cheque'){
                            $types = ( $type == "debit")?0:1;
                            \App\Models\PaymentVoucher::add_voucher_payment($tp,$types,$new_separate,$tp->id);
                            $voucher_id                     = \App\Models\PaymentVoucher::orderBy("id","desc")->where("business_id",$business_id)->first();
                            $payment_id                     = \App\TransactionPayment::find($tp->id);
                            $payment_id->payment_voucher_id =  $voucher_id->id;
                            $payment_id->save();    

                            $document_expense = [];
                            if ($request->hasFile('document')) {
                                $file   = $request->file('document');
                                $file_name =  'public/uploads/documents/'.time().'.'.$file->getClientOriginalExtension();
                                $file->move('public/uploads/documents',$file_name);
                                array_push($document_expense,$file_name);
                            }
                             
                            $voucher_id->document        =  json_encode($document_expense) ;
                            $voucher_id->update();    
                            $type_voucher = ($new_separate->type == 'purchase'|| $new_separate->type == 'sell_return')?'Payment Voucher':'Reciept Voucher';                                                           
                            \App\Models\StatusLive::insert_data_v($business_id,$new_separate,$voucher_id,$type_voucher);
                                                                        
                        }
                    }
                    
                }
                $payment_status              = $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                $transaction->payment_status = $payment_status;
                $transaction->update();
                // $parent                      = \App\Models\ParentArchive::save_payment_parent($tp->id,"Add");
                // $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);
                \DB::commit();
                $output = ['success' => true,
                    'msg' => __('purchase.payment_added_success')
                ];
            }else{
                
                
                $transaction_before = $transaction->replicate();
 
                if ($transaction->payment_status != 'paid') {
                    $inputs = $request->only(['amount', 'method', 'note', 'card_number', 'card_holder_name',
                                            'card_transaction_number', 'card_type', 'card_month',
                                            'card_year', 'card_security','cheque_number', 'bank_account_number']);
                    if($request->method == 'cheque'){
                        $inputs['paid_on'] =  $request->write_date ;
                    }else{
                        $inputs['paid_on'] =  $this->transactionUtil->uf_date($request->input('paid_on'), true);
                    }
                    $inputs['transaction_id'] = $transaction->id;
                    $inputs['amount']         = $this->transactionUtil->num_uf($inputs['amount']);
                    $inputs['created_by']     = auth()->user()->id;
                    $inputs['payment_for']    = $transaction->contact_id;
                    if ($inputs['method'] == 'custom_pay_1') {
                        $inputs['transaction_no'] = $request->input('transaction_no_1');
                    } elseif ($inputs['method'] == 'custom_pay_2') {
                        $inputs['transaction_no'] = $request->input('transaction_no_2');
                    } elseif ($inputs['method'] == 'custom_pay_3') {
                        $inputs['transaction_no'] = $request->input('transaction_no_3');
                    }

                    # cheque_account
                    if ($inputs['method'] == 'cheque' && $request->input('cheque_account')) {
                        $inputs['account_id'] =  $request->input('cheque_account');
                    }else{
                        if (!empty($request->input('account_id')) && $inputs['method'] != 'advance') {
                            $inputs['account_id'] = $request->input('account_id');
                        }
                    }
                    
                    $prefix_type     = 'purchase_payment';
                    if (in_array($transaction->type, ['sale', 'sell_return'])) {
                        $prefix_type = 'sell_payment';
                    } elseif (in_array($transaction->type, ['expense', 'expense_refund'])) {
                        $prefix_type = 'expense_payment';
                    }

                    DB::beginTransaction();

                    $ref_count                = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                    # Generate reference number
                    $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);
                    $inputs['business_id']    = $request->session()->get('business.id');
                    $inputs['source']         = $request->input('amount');
                    $inputs['document']       = $this->transactionUtil->uploadFile($request, 'document', 'documents');

                    # Pay from advance balance
                    $payment_amount           = $inputs['amount'];
                    $contact_balance          = !empty($transaction->contact) ? $transaction->contact->balance : 0;
                    if ($inputs['method'] == 'advance' && $inputs['amount'] > $contact_balance) {
                        throw new AdvanceBalanceNotAvailable(__('lang_v1.required_advance_balance_not_available'));
                    }
                    if (!empty($inputs['amount'])) {
                        $tp                         = TransactionPayment::create($inputs);
                        $inputs['transaction_type'] = $transaction->type;
                        event(new TransactionPaymentAdded($tp, $inputs));
                        
                        
                        if ($request->method == 'cheque') {
                            $business_id                          = request()->session()->get('user.business_id');
                            $setting                              = \App\Models\SystemAccount::where('business_id',$business_id)->first();
                            $type                                 = ($transaction->type == 'purchase' || $transaction->type == 'sell_return')?1:0;
                            $account_id                           = ($type == 0)?$setting->cheque_collection:$setting->cheque_debit;
                            $rq_payment                           = [];
                            $rq_payment['amount']                 = $request->amount;
                            $rq_payment['cheque_number']          = $request->cheque_number;
                            $rq_payment['write_date']             = $request->write_date;
                            $rq_payment['due_date']               = $request->due_date;
                            $rq_payment['cheque_account']         = $account_id;
                            $rq_payment['cheque_bank']            = $request->bank_id;
                            $rq_payment['note']                   = $request->note;
                            $rq_payment['transaction_payment_id'] = $tp->id;
                            \App\Models\Check::add_cheque($transaction,$rq_payment);
                            $check_id                             = \App\Models\Check::where("transaction_payment_id",$tp->id)->first()->id;
                            $pay                                  = TransactionPayment::find($tp->id);
                            $pay->check_id                        = $check_id;
                            $entries                              = \App\Models\Entry::where("payment_id",$tp->id)->first();
                            $pay->save();
                        }
                        # effecting account
                        if ($transaction->type == 'purchase' || $transaction->type == 'sell_return') {
                            $check_id = \App\Models\Check::where("transaction_payment_id",$tp->id)->first();
                            if($check_id != null){
                                $check_id->id;
                                $allChecks = \App\AccountTransaction::where('account_id',$tp->account_id)->where('transaction_payment_id',$tp->id)->get();
                                foreach($allChecks as $ck){
                                    $ck->transaction_id = $transaction->id;
                                    $ck->type           = 'credit';
                                    $ck->check_id       = $check_id->id ;
                                    $ck->update();
                                    $accounts_transaction = \App\Account::find($ck->account_id);
                                    if($accounts_transaction->cost_center!=1){
                                        \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$ck->operation_date);
                                    }
                                }
                            }else{
                                $Checks = \App\AccountTransaction::where('account_id',$tp->account_id)->where('transaction_payment_id',$tp->id)->get();
                                foreach($Checks as $ck){
                                    $ck->type = "credit";
                                    $ck->update();
                                    $accounts_transaction = \App\Account::find($ck->account_id);
                                    if($accounts_transaction->cost_center!=1){
                                        \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$ck->operation_date);
                                    }
                                } 
                            }
                                
                        }else{
                            $check_id = \App\Models\Check::where("transaction_payment_id",$tp->id)->first();
                            if($check_id != null){
                                $check_id->id;
                                $allTransactionPayment =  \App\AccountTransaction::where('account_id',$tp->account_id)->where('transaction_payment_id',$tp->id)->get();
                                foreach($allTransactionPayment as $tpItem){
                                    $tpItem->transaction_id = $transaction->id;
                                    $tpItem->type           = 'debit' ;
                                    $tpItem->check_id       = $check_id->id;
                                    $tpItem->update();
                                    $accounts_transaction = \App\Account::find($tpItem->account_id);
                                    if($accounts_transaction->cost_center!=1){
                                    \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$tpItem->operation_date);
                                    }
                                } 
                            }else{
                                $allTransactionPayment =  \App\AccountTransaction::where('account_id',$tp->account_id)->where('transaction_payment_id',$tp->id)->get();
                                foreach($allTransactionPayment as $tpItem ){
                                    $tpItem->type = 'debit';
                                    $tpItem->update();
                                    $accounts_transaction = \App\Account::find($tpItem->account_id);
                                    if($accounts_transaction->cost_center!=1){
                                        \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$tpItem->operation_date);
                                    }
                                } 
                            }
                        }

                        $acc         =  \App\Account::where('contact_id',$transaction->contact_id)->first();
                        if ($acc) {
                            $type        =  ($transaction->type == 'purchase' || $transaction->type == 'sell_return')?'debit':'credit';
                            $check_id    =  \App\Models\Check::where("transaction_payment_id",$tp->id)->first();
                            if($check_id != null){
                                $pCheck_id = $check_id->id;
                            }else{
                                $pCheck_id = null;
                            }
                            $credit_data = [
                                'amount'                 => $request->amount,
                                'account_id'             => $acc->id,
                                'type'                   => $type,
                                'sub_type'               => 'deposit',
                                'operation_date'         => date('Y-m-d'),
                                'created_by'             => session()->get('user.id'),
                                'note'                   => 'Add Payment',
                                'transaction_id'         => $transaction->id,
                                'transaction_payment_id' => $tp->id,
                                'for_repeat'             => 1,
                                'check_id'               => $pCheck_id,
                            ];
                            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                            if($acc->cost_center!=1){
                                \App\AccountTransaction::nextRecords($acc->id,$acc->business_id,$credit->operation_date);
                            }
                            if($check_id != null){
                                $types="PCheck";
                                \App\Models\Entry::create_entries($check_id,$types);
                            } 
                            if($request->method != 'cheque'){
                                $types = ( $type == "debit")?0:1;
                                \App\Models\PaymentVoucher::add_voucher_payment($tp,$types,$transaction);
                                $voucher_id                     = \App\Models\PaymentVoucher::orderBy("id","desc")->where("business_id",$business_id)->first();
                                $payment_id                     = \App\TransactionPayment::find($tp->id);
                                $payment_id->payment_voucher_id =  $voucher_id->id;
                                $payment_id->save();    
                                $type_voucher                   = ($transaction->type == 'purchase'|| $transaction->type == 'sell_return')?'Payment Voucher':'Reciept Voucher';                                                           
                                \App\Models\StatusLive::insert_data_v($business_id,$transaction,$voucher_id,$type_voucher);
                                                                            
                            }
                        }
                        
                    }
                    $payment_status              = $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);
                    $transaction->payment_status = $payment_status;
                    $parent                      = \App\Models\ParentArchive::save_payment_parent($tp->id,"Add");
                    $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);
                    DB::commit();
                }

                $output = ['success' => true,
                    'msg' => __('purchase.payment_added_success')
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = $e;
            // $msg = __('messages.something_went_wrong');
            if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
                $msg = __('messages.something_went_wrong');
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            }
            $output = ['success' => false,
                          'msg' => $msg
                      ];
        }
        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create') && !auth()->user()->can('product.avarage_cost')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
        
            $transaction = Transaction::where('id', $id)
                                        ->with(['contact', 'business', 'transaction_for'])
                                        ->first();
            $payments_query     = TransactionPayment::where('transaction_id', $id);
            $all_payments_bill  = Transaction::where("separate_parent",$id)->where("separate_type","payment")->get();
            $payments_bill      = [];
            
            foreach($all_payments_bill as $all){
                $payments_row        = TransactionPayment::where('transaction_id', $all->id);
                $payments_bill[]     = $payments_row->first();
            }
  
            $accounts_enabled = false;
            if ($this->moduleUtil->isModuleEnabled('account')) {
                $accounts_enabled = true;
                $payments_query->with(['payment_account']);
            }
            $payments      = $payments_query->get();
            $location_id   = !empty($transaction->location_id) ? $transaction->location_id : null;
            $payment_types = $this->transactionUtil->payment_types($location_id, true);
             
            return view('transaction_payment.show_payments')
                    ->with(compact('transaction', 'payments','payments_bill','payment_types',  'accounts_enabled'));
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showw(Request $request)
    {
         
        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')  ) {
            abort(403, 'Unauthorized action.');
        }

        foreach($request->request as $key => $value){
            if($key != "status"){
                $id = $key;
            }else{
                $state = $value; 
            }
        }
        
        if (request()->ajax()) {
            
            $business_id = request()->session()->get('user.business_id');
            $business_locations = BusinessLocation::forDropdown($business_id);
            $transaction = Transaction::where('id', $id)
                                            ->with(['contact', 'business', 'transaction_for'])
                                            ->first();
            
            $transaction_deliveries      = TransactionRecieved::where('transaction_id', $transaction->id)->get();
            
            $RecievedPrevious            = RecievedPrevious::where('transaction_id', $transaction->id)->get();
            $RecPres                     = RecievedPrevious::where('transaction_id', $transaction->id)->sum("current_qty");
            
            $RecievedWrong               = RecievedWrong::where('transaction_id', $transaction->id)->get();
            $RecWrong                    = RecievedWrong::where('transaction_id', $transaction->id)->sum("current_qty");
            
            $purchcaseline               = PurchaseLine::where('transaction_id', $transaction->id)->get();
            $purline                     = PurchaseLine::where('transaction_id', $transaction->id)->sum("quantity");
            $location_id                 = !empty($transaction->location_id) ? $transaction->location_id : null;
           
            $tr                          = \App\Transaction::where("id",$transaction->return_parent_id)->first();
            
            if(!empty($tr)){
                $purchcaseline_return    = PurchaseLine::where('transaction_id', $tr->id)->get();
                $purline_return          = PurchaseLine::where('transaction_id', $tr->id)->sum("quantity_returned");
            }
            
            $quantity_all = 0;
            $totals       = $RecPres + $RecWrong   ;
            //..........**
            $product_list_all = [];
            foreach($purchcaseline as $prd){
                    $product_list_all[] = $prd->product_id;
            }
            if(!empty($tr)){
                $product_list_all_return = [];
                foreach($purchcaseline_return as $prd){
                        $product_list_all_return[] = $prd->product_id;
                }
                return view('transaction_payment.show_delivered')
                        ->with(compact('transaction',
                                        "state"  ,
                                        'totals',
                                        "RecPres",
                                        'purline',
                                        'product_list_all_return',
                                        'purline_return',
                                        'RecWrong',
                                        'quantity_all' ,
                                        'purchcaseline',
                                        'product_list_all',
                                        'RecievedPrevious' ,
                                        'transaction_deliveries',
                                        'RecievedWrong', ));
            }else{
                
            return view('transaction_payment.show_delivered')
                                        ->with(compact('transaction',
                                                        "state"  ,
                                                        'totals',
                                                        "RecPres",
                                                        'purline',
                                                        'RecWrong',
                                                        'quantity_all' ,
                                                        'purchcaseline',
                                                        'product_list_all',
                                                        'RecievedPrevious' ,
                                                        'transaction_deliveries',
                                                        'RecievedWrong', ));
            }
           
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //......... purchase  ... sells 
    public function showww(Request $request)
    {
        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create') ) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
                $business_id          = request()->session()->get('user.business_id');
                foreach($request->request as $key => $value){
                    if($key != "status"){
                        $id    = $key;
                    }else{
                        $state = $value; 
                        
                    }
                }
                $business_locations   = BusinessLocation::forDropdown($business_id);
                $transaction          = Transaction::where('id', $id)->with(['contact', 'business', 'transaction_for'])->first();
                if(!empty($transaction)){
                    $transaction_child    = \App\Transaction::where("separate_parent",$transaction->id)->where("separate_type","partial")->get();
                    $TransactionSellLine  = TransactionSellLine::where('transaction_id', $transaction->id)->get();
                    $TraSeLine            = TransactionSellLine::where('transaction_id', $transaction->id)->sum("quantity");
                    if(count($transaction_child)>0){
                        $transaction_delivery = [];$RecievedPrevious = [];$DelPrevious=0;$DelWrong=0;
                        foreach($transaction_child as $one){
                            $transaction_delivery[]  = TransactionDelivery::where('transaction_id', $one->id)->get();
                            $RecievedPrevious[]      = DeliveredPrevious::where('transaction_id', $one->id)->get();
                            $DelPrevious             += DeliveredPrevious::where('transaction_id', $one->id)->sum("current_qty");
                            $DeliveredWrong[]        = DeliveredWrong::where('transaction_id', $one->id)->get();
                            $DelWrong                += DeliveredWrong::where('transaction_id', $one->id)->sum("current_qty");
                        }
                        $location_id          = !empty($transaction->location_id) ? $transaction->location_id : null;
                        $totals               = $DelPrevious + $DelWrong;
                        $quantity_all         = 0;
                        $product_list_all     = [];
                        foreach($TransactionSellLine as $prd){
                                $product_list_all[] = $prd->product_id;
                        }
                        

                        return view('transaction_payment.show_delivered_sell')
                        ->with(compact('transaction',
                                'TraSeLine',
                                'TransactionSellLine', 
                                "totals",  
                                "state",  
                                "DelWrong",  
                                "transaction_child",  
                                "DeliveredWrong",  
                                'product_list_all', 
                                'RecievedPrevious',
                                'DelPrevious',
                                'transaction_delivery',
                                ));
                         
                    }else{
                        
                        $location_id            = !empty($transaction->location_id) ? $transaction->location_id : null;
                        
                        $transaction_delivery   = TransactionDelivery::where('transaction_id', $transaction->id)->get();
                        $RecievedPrevious       = DeliveredPrevious::where('transaction_id', $transaction->id)->get();
                        $DelPrevious            = DeliveredPrevious::where('transaction_id', $transaction->id)->sum("current_qty");
                        
                        //****START */ .. RETURN SECTION ..
                            $tr                   = \App\Transaction::where("id",$transaction->return_parent_id)->first();
                            if(!empty($tr)){
                                $TransactionSellLine_return  = TransactionSellLine::where('transaction_id', $tr->id)->get();
                                $TraSeLine_return            = TransactionSellLine::where('transaction_id', $tr->id)->sum("quantity_returned");
                            }
                        //****END */   .. RETURN SECTION ..
                            
                        $DeliveredWrong       = DeliveredWrong::where('transaction_id', $transaction->id)->get();
                        $DelWrong             = DeliveredWrong::where('transaction_id', $transaction->id)->sum("current_qty");
                
                        $totals               = $DelPrevious + $DelWrong;
                        $quantity_all         = 0;
                        $product_list_all     = [];
                        foreach($TransactionSellLine as $prd){
                                $product_list_all[] = $prd->product_id;
                        }
                        /*** RETURN SECTION */
                        if(!empty($tr)){
                            $product_list_all_return = [];
                            foreach($TransactionSellLine_return as $prd){
                                    $product_list_all_return[] = $prd->product_id;
                            }
                            return view('transaction_payment.show_delivered_sell')
                            ->with(compact('transaction',
                                    'TraSeLine',
                                    'TransactionSellLine', 
                                    "totals",  
                                    "state",  
                                    "DelWrong",  
                                    "DeliveredWrong",  
                                    'product_list_all', 
                                    'TransactionSellLine_return', 
                                    'TraSeLine_return', 
                                    'product_list_all_return', 
                                    'RecievedPrevious',
                                    'DelPrevious',
                                    'transaction_delivery',
                                    ));

                        }else{

                            return view('transaction_payment.show_delivered_sell')
                            ->with(compact('transaction',
                                    'TraSeLine',
                                    'TransactionSellLine', 
                                    "totals",  
                                    "state",  
                                    "DelWrong",  
                                    "DeliveredWrong",  
                                    'product_list_all', 
                                    'RecievedPrevious',
                                    'DelPrevious',
                                    'transaction_delivery',
                                    ));
                        }
                    }
                }
                

                
                
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create') ) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $payment_line = TransactionPayment::where('method', '!=', 'advance')->findOrFail($id);

            $transaction = Transaction::where('id', $payment_line->transaction_id)
                                        ->where('business_id', $business_id)
                                        ->with(['contact', 'location'])
                                        ->first();

            $payment_types = $this->transactionUtil->payment_types($transaction->location);

            //Accounts
            $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);
            $cheque_type  =  ($transaction->type == 'purchase' || $transaction->type == 'sell_return')?1:0;
            $cheque =  \App\Models\Check::where('transaction_payment_id',$id)->first();
            return view('transaction_payment.edit_payment_row')
                        ->with(compact('transaction','cheque_type', 'payment_types','cheque','payment_line', 'accounts'));
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit_recieve($id)
    {
        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $payment_line = TransactionRecieved::select()->findOrFail($id);

            $transaction = Transaction::where('id', $payment_line->transaction_id)
                                        ->where('business_id', $business_id)
                                        ->with(['contact', 'location'])
                                        ->first();

            $payment_types = $this->transactionUtil->payment_types($transaction->location);

            //Accounts
            $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);

            return view('transaction_payment.edit_recieve_row')
                        ->with(compact('transaction', 'payment_types', 'payment_line', 'accounts'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $inputs = $request->only(['amount', 'method', 'note', 'card_number', 'card_holder_name',
            'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
            'cheque_number', 'bank_account_number']);
            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            if ($inputs['method'] == 'custom_pay_1') {
                $inputs['transaction_no'] = $request->input('transaction_no_1');
            } elseif ($inputs['method'] == 'custom_pay_2') {
                $inputs['transaction_no'] = $request->input('transaction_no_2');
            } elseif ($inputs['method'] == 'custom_pay_3') {
                $inputs['transaction_no'] = $request->input('transaction_no_3');
            }

            if (!empty($request->input('account_id'))) {
                $inputs['account_id'] = $request->input('account_id');
            }

            $payment = TransactionPayment::where('method', '!=', 'advance')->findOrFail($id);
             //Update parent payment if exists
            if (!empty($payment->parent_id)) {
                $parent_payment = TransactionPayment::find($payment->parent_id);
                $parent_payment->amount = $parent_payment->amount - ($payment->amount - $inputs['amount']);
                $parent_payment->save();
            }
            
            
        $business_id = $request->session()->get('user.business_id');

        $transaction = Transaction::where('business_id', $business_id)
                            ->find($payment->transaction_id);

            if($payment->payment_voucher_id != null){
                $voucher_id = \App\Models\PaymentVoucher::find($payment->payment_voucher_id);
                $voucher_id->amount =   $inputs['amount'] ;
                $voucher_id->save();
                $transactionPay = \App\AccountTransaction::where('payment_voucher_id', $payment->payment_voucher_id)->first();
                if($transactionPay){
                    \App\AccountTransaction::where('payment_voucher_id', $payment->payment_voucher_id)->update([
                                                    'amount'=>$request->amount
                                                ]);
                } 
                $state="Update Voucher";
                \App\Models\StatusLive::insert_data_v($business_id,$transaction,$voucher_id,$state); 
            }

            $transactionPay = \App\AccountTransaction::where('transaction_payment_id', $id)->first();
            if($transactionPay){
                \App\AccountTransaction::where('transaction_payment_id', $id)->update([
                                                'amount'=>$request->amount
                                            ]);
            } 
            $inputs['source']  = $inputs['amount'];
            $transaction_before = $transaction->replicate();
            $document_name      = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $inputs['document'] = $document_name;
            }
                               
            DB::beginTransaction();

            $payment->update($inputs);


            //update payment status
            $payment_status = $this->transactionUtil->updatePaymentStatus($payment->transaction_id);
            $transaction->payment_status = $payment_status;

            $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);
            // uodate cheque
            $check =  \App\Models\Check::where('transaction_payment_id',$id)->first();
            if ($request->method == 'cheque') {
                $rq_payment =  [];
                $rq_payment['amount']          = $request->amount;
                $rq_payment['write_date']      = $request->write_date;
                $rq_payment['due_date']        = $request->due_date;
                
                $rq_payment['transaction_payment_id'] = $id;
                if ($check) {
                    $rq_payment['account_id']  = $request->account_id;
                    $rq_payment['cheque_no'] = $request->cheque_number;
                    $rq_payment['contact_bank_id']     = $request->bank_id;
                    \App\Models\Check::where('id',$check->id)->update($rq_payment);
                    \App\AccountTransaction::where('check_id',$check->id)->update([
                        'amount'=>$request->amount
                    ]);
                    $state="Update Cheque";
                    \App\Models\StatusLive::insert_data_c($business_id,$transaction,$check,$state);
                }else {
                    $rq_payment['cheque_account']  = $request->account_id;
                    $rq_payment['cheque_number']   = $request->cheque_number;
                    $rq_payment['cheque_bank']     = $request->bank_id;
                    $rq_payment['note']            = $request->note;
                    \App\Models\Check::add_cheque($transaction,$rq_payment);
                   
                   
                }
            }else{
                
                if ($check) {
                    \App\AccountTransaction::where('check_id',$check->id)->delete();
                    $check->delete();
                }
                
            }
            //end edit 
            DB::commit();

            //event
            event(new TransactionPaymentUpdated($payment, $transaction->type));

            $output = ['success' => true,
                            'msg' => __('purchase.payment_updated_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                          'msg' => __('messages.something_went_wrong')
                      ];
        }

        return redirect()->back()->with(['status' => $output]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_recieve(Request $request, $id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('warehouse.views')  ) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $inputs = $request->only(['amount', 'method', 'note', 'card_number', 'card_holder_name',
            'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
            'cheque_number', 'bank_account_number']);
            $inputs['paid_on'] = $this->transactionUtil->uf_date($request->input('paid_on'), true);
            $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

            if ($inputs['method'] == 'custom_pay_1') {
                $inputs['transaction_no'] = $request->input('transaction_no_1');
            } elseif ($inputs['method'] == 'custom_pay_2') {
                $inputs['transaction_no'] = $request->input('transaction_no_2');
            } elseif ($inputs['method'] == 'custom_pay_3') {
                $inputs['transaction_no'] = $request->input('transaction_no_3');
            }

            if (!empty($request->input('account_id'))) {
                $inputs['account_id'] = $request->input('account_id');
            }

            $payment = TransactionPayment::where('method', '!=', 'advance')->findOrFail($id);

            //Update parent payment if exists
            if (!empty($payment->parent_id)) {
                $parent_payment = TransactionPayment::find($payment->parent_id);
                $parent_payment->amount = $parent_payment->amount - ($payment->amount - $inputs['amount']);

                $parent_payment->save();
            }

            $business_id = $request->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)->find($payment->transaction_id);
            
            $transaction_before = $transaction->replicate();
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $inputs['document'] = $document_name;
            }
                               
            DB::beginTransaction();

            $payment->update($inputs);


            //update payment status
            $payment_status = $this->transactionUtil->updatePaymentStatus($payment->transaction_id);
            $transaction->payment_status = $payment_status;

            $this->transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);

            DB::commit();

            //event
            event(new TransactionPaymentUpdated($payment, $transaction->type));

            $output = ['success' => true,
                            'msg' => __('purchase.payment_updated_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                          'msg' => __('messages.something_went_wrong')
                      ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                             
                // check the payment
                $payment = TransactionPayment::findOrFail($id);
                DB::beginTransaction();
                $entries = [];
                if($payment->is_invoice == 1){
                    
                    // delete voucher 
                    if($payment->payment_voucher_id != null){
                        $payment_voucher = \App\Models\PaymentVoucher::find($payment->payment_voucher_id);
                        if(!empty($payment_voucher)){
                            $act_trans = \App\AccountTransaction::where("payment_voucher_id",$payment->payment_voucher_id)->orderBy('id','desc')->get();
                            foreach($act_trans as $it){
                                if(!in_array($it->entry_id,$entries) && $it->entry_id != null){
                                    $entries[] = $it->entry_id;
                                }
                                $action_date          = $it->operation_date;
                                $accounts_transaction = \App\Account::find($it->account_id);
                                $it_id = $it->id;   
                                $it->delete();
                                if($accounts_transaction->cost_center!=1){
                                    \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$action_date);
                                } 
                            }
                            $payment_voucher->delete();
                        }
                        $voucher  = \App\Models\StatusLive::where("voucher_id",$payment->payment_voucher_id)->get();
                        foreach($voucher as $it){
                            $it->delete();
                        }
                    }

                    // delete cheque
                    if($payment->check_id != null){
                        $check_id = \App\Models\Check::find($payment->check_id);
                        if(!empty($check_id)){
                            $act_trans = \App\AccountTransaction::where("check_id",$payment->check_id)->get();########3
                            foreach($act_trans as $it){
                                if(!in_array($it->entry_id,$entries) && $it->entry_id != null){
                                    $entries[] = $it->entry_id;
                                }
                                $action_date          = $it->operation_date;
                                $accounts_transaction = \App\Account::find($it->account_id);
                                $it->delete();
                                if($accounts_transaction->cost_center!=1){
                                    \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$action_date);
                                }
                            }
                            $check_id->delete();

                        }
                        $check  = \App\Models\StatusLive::where("check_id",$payment->check_id)->get();
                        foreach($check as $it){
                            $it->delete();
                        }
                    }
                    
                    if (!empty($payment->transaction_id)) {
                        TransactionPayment::deletePayment($payment);
                    } else { //advance payment
                        $adjusted_payments = TransactionPayment::where('parent_id', 
                                                    $payment->id)
                                                    ->get();

                        $total_adjusted_amount = $adjusted_payments->sum('amount');

                        //Get customer advance share from payment and deduct from advance balance
                        $total_customer_advance = $payment->amount - $total_adjusted_amount;
                        if ($total_customer_advance > 0) {
                            $this->transactionUtil->updateContactBalance($payment->payment_for, $total_customer_advance , 'deduct');
                        }

                        //Delete all child payments
                        foreach ($adjusted_payments as $adjusted_payment) {
                            //Make parent payment null as it will get deleted
                            $adjusted_payment->parent_id = null;
                            TransactionPayment::deletePayment($adjusted_payment);
                        }

                        //Delete advance payment
                        TransactionPayment::deletePayment($payment);
                    }

                    $checks =  \App\Models\Check::where('transaction_payment_id',$id)->get();
                    
                    foreach ($checks as $check) {
                        $all_Check_tr = \App\AccountTransaction::where('check_id',$check->id)->get();########3
                        foreach($all_Check_tr as $it){
                            if(!in_array($it->entry_id,$entries) && $it->entry_id != null){
                                $entries[] = $it->entry_id;
                            }
                            $action_date          = $it->operation_date;
                            $accounts_transaction = \App\Account::find($it->account_id);
                            $it->delete();
                            if($accounts_transaction->cost_center!=1){
                                \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$action_date);
                            }
                        }
                        $check->delete();
                    }

                    $allTransactionPayment = \App\AccountTransaction::where('transaction_payment_id',$id)->get();########3
                    foreach($allTransactionPayment as $o){
                        $action_date          = $o->operation_date;
                        $accounts_transaction = \App\Account::find($o->account_id);
                        $o->delete();
                        if($accounts_transaction->cost_center!=1){
                            \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$action_date);
                        }
                    }
                    $TR = \App\Transaction::where("id",$payment->transaction_id)->first();
                    $sum= 0;
                    $bill_move =  \App\AccountTransaction::where('transaction_id',$payment->transaction_id)->get();########3
                    foreach($bill_move as $o){
                        if(!in_array($o->entry_id,$entries)){
                            $entries[] = $o->entry_id;
                        } 
                        $action_date          = $o->operation_date;
                        $accounts_transaction = \App\Account::find($o->account_id);
                        $o->delete();
                        if($accounts_transaction->cost_center!=1){
                            \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$action_date);
                        }
                    }
                    $parent          = \App\Transaction::find($TR->separate_parent);
                    $total_parent    = $parent->final_total;
                    $allTransaction  = \App\Transaction::where("id","!=",$TR->id)->whereNull("sub_type")->where("separate_parent",$TR->separate_parent)->get();
                    foreach($allTransaction as $one_pay){
                        $sum    +=   $one_pay->final_total; 
                    }
                    if(!empty($total_parent)){
                        $parent_balance = $parent->final_total;
                        $status  = 'due';
                        if ($parent_balance <= $sum) {
                            $status = 'paid';
                        } elseif ($sum > 0 && $parent_balance > $sum) {
                            $status = 'partial';
                        }
                        $parent->payment_status = $status;
                        $parent->update();
                    }
                    $TR->delete();
                    foreach ($entries as $key => $value) {
                        # code...
                        $ent = \App\Models\Entry::find($value);
                        if(!empty($ent)){
                            $ent->delete();
                        }
                    }
                    
                }else{
                    
                    // delete voucher 
                    if($payment->payment_voucher_id != null){
                        $payment_voucher = \App\Models\PaymentVoucher::find($payment->payment_voucher_id);
                        if(!empty($payment_voucher)){
                        $act_trans = \App\AccountTransaction::where("payment_voucher_id",$payment->payment_voucher_id)->get();########3
                        foreach($act_trans as $it){
                            if(!in_array($it->entry_id,$entries) && $it->entry_id != null){
                                $entries[] = $it->entry_id;
                            }
                            $action_date          = $it->operation_date;
                            $accounts_transaction = \App\Account::find($it->account_id);
                            $it->delete();
                            if($accounts_transaction->cost_center!=1){
                                \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$action_date);
                            }
                        }
                        $payment_voucher->delete();
                        }
                        $voucher  = \App\Models\StatusLive::where("voucher_id",$payment->payment_voucher_id)->get();
                        foreach($voucher as $it){
                            $it->delete();
                        }
                    }

                    // delete cheque
                    if($payment->check_id != null){
                        $check_id = \App\Models\Check::find($payment->check_id);
                        if(!empty($check_id)){
                        $act_trans = \App\AccountTransaction::where("check_id",$payment->check_id)->get();########3
                        foreach($act_trans as $it){
                            if(!in_array($it->entry_id,$entries) && $it->entry_id != null){
                                $entries[] = $it->entry_id;
                            }
                            $action_date          = $it->operation_date;
                            $accounts_transaction = \App\Account::find($it->account_id);
                            $it->delete();
                            if($accounts_transaction->cost_center!=1){
                                \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$action_date);
                            }
                        }
                        $check_id->delete();

                        }
                        $check  = \App\Models\StatusLive::where("check_id",$payment->check_id)->get();
                        foreach($check as $it){
                            $it->delete();
                        }
                    }
                    
                    if (!empty($payment->transaction_id)) {
                        TransactionPayment::deletePayment($payment);
                    } else { //advance payment
                        $adjusted_payments = TransactionPayment::where('parent_id', 
                                                    $payment->id)
                                                    ->get();

                        $total_adjusted_amount = $adjusted_payments->sum('amount');

                        //Get customer advance share from payment and deduct from advance balance
                        $total_customer_advance = $payment->amount - $total_adjusted_amount;
                        if ($total_customer_advance > 0) {
                            $this->transactionUtil->updateContactBalance($payment->payment_for, $total_customer_advance , 'deduct');
                        }

                        //Delete all child payments
                        foreach ($adjusted_payments as $adjusted_payment) {
                            //Make parent payment null as it will get deleted
                            $adjusted_payment->parent_id = null;
                            TransactionPayment::deletePayment($adjusted_payment);
                        }

                        //Delete advance payment
                        TransactionPayment::deletePayment($payment);
                    }

                    $checks =  \App\Models\Check::where('transaction_payment_id',$id)->get();
                    
                    foreach ($checks as $check) {
                        $all_Check_tr = \App\AccountTransaction::where('check_id',$check->id)->get();########3
                        foreach($all_Check_tr as $it){
                            if(!in_array($it->entry_id,$entries) && $it->entry_id != null){
                                $entries[] = $it->entry_id;
                            }
                            $it->delete();
                        }
                        $check->delete();
                    }

                    $allTransactionPayment = \App\AccountTransaction::where('transaction_payment_id',$id)->get();########3
                    foreach($allTransactionPayment as $o){
                        $action_date          = $o->operation_date;
                        $accounts_transaction = \App\Account::find($o->account_id);
                        $o->delete();
                        if($accounts_transaction->cost_center!=1){
                            \App\AccountTransaction::nextRecords($accounts_transaction->id,$accounts_transaction->business_id,$action_date);
                        }
                    }
                    $TR = \App\Transaction::where("id",$payment->transaction_id)->first();
                    
                    $transaction_pay = \App\TransactionPayment::where("transaction_id",$payment->transaction_id)->where("return_payment",1)->where("id","!=",$id)->first();
                    if(empty($transaction_pay)){
                        $TR->sub_type = null;
                        $TR->update();
                    }
                    $payment_status = $this->transactionUtil->updatePaymentStatus($payment->transaction_id, $payment->transaction->final_total);
                    $payment->transaction->payment_status = $payment_status;
                    $payment->transaction->save();
                    
                    $this->transactionUtil->activityLog($payment->transaction, 'payment_deleted', $TR);
                }
                DB::commit();

                $output = ['success' => true,
                                'msg' => __('purchase.payment_deleted_success')
                            ];
            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = ['success' => false,
                                'msg' => __('messages.something_went_wrong')
                            ];
            }

            return $output;
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_recieve($id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')  ) {
            abort(403, 'Unauthorized action.');
        }

         if (request()->ajax()) {
            try {
                $business_id =  request()->session()->get("user.business_id");
                //.. receipt reference
                $payment     = TransactionRecieved::findOrFail($id);
                //.. receipt purchase bill number
                $transaction = $payment->transaction_id;
                //.. rows of receipt  for purchase bill 
                $ids         = TransactionRecieved::childs($id);
                //.. rows of wrong receipt  for purchase bill 
                $ids_wrong   = TransactionRecieved::childs_wrong($id);
                //.. expense reference for receipt 
                $additional_shipping = \App\Models\AdditionalShipping::where("t_recieved",$id)->first();
                //.. select all  children of receipt 
                $RecievedPrevious = RecievedPrevious::whereIn("id",$ids)->get();
                //.. select all wrong  children of receipt 
                $RecievedWrong    = RecievedWrong::whereIn("id",$ids_wrong)->get();
                DB::beginTransaction();
                //.1.// ........ 
                if(!empty($additional_shipping)){
                    $ids_shipping = $additional_shipping->items->pluck("id");
                    foreach($ids_shipping as $i){
                            $ship = \App\Models\AdditionalShippingItem::find($i);
                            $ship->delete();
                    }
                    $additional_shipping->delete();
                }
                //.2.// ..... 
                $receive           = \App\Models\RecievedPrevious::where("transaction_id",$transaction)->where("transaction_deliveries_id",$id)->get();
                if(count($receive)>0){
                        $tr        = \App\Transaction::find($transaction); 
                        $sum       = \App\Models\RecievedPrevious::where("transaction_id",$transaction)->where("transaction_deliveries_id",$id)->sum("current_qty");
                        $previous  = \App\Models\RecievedPrevious::where("transaction_id",$transaction)->where("transaction_deliveries_id",$id)->get();
                        $move_id        = [];
                        foreach($previous as $it){
                            $ite_m = \App\Models\ItemMove::where("transaction_id",$transaction)->where("recieve_id",$it->id)->first();
                            if(!empty($ite_m)){
                                $move_id[] = $ite_m->id; 
                             }
                            if(!empty($ite_m)){
                                $date = ($ite_m->date != null)?$ite_m->date:$ite_m->created_at;
                                $_id  =  $ite_m->id;
                                $_product_id  =  $ite_m->product_id;
                                $ite_m->delete();$move_id  = [];
                                $itemMoves = \App\Models\ItemMove::orderBy("date","asc")->orderBy("order_id","desc")->orderBy("id","asc")->where("product_id",$_product_id)->first();
                                if(!empty($itemMoves)){
                                    $move_id [] = $itemMoves->id;
                                    $date       = $itemMoves->date;
                                    \App\Models\ItemMove::updateRefresh($itemMoves,$itemMoves,$move_id,$date);
                                }
                            }
                        }
                        foreach($previous as $it){
                            if($it->additional_shipping_id == $id ){
                                $it->delete();
                            }
                        }
                }else{
                    $items = \App\Models\ItemMove::where("transaction_id",$transaction)->get();
                    foreach($items as $it){ $it->delete(); }
                }
                //.2-1.// ..... 
                $wrong = \App\Models\RecievedWrong::where("transaction_id",$transaction)->where("transaction_deliveries_id",$id)->get();
                if(count($wrong)>0){
                        $tr        = \App\Transaction::find($transaction); 
                        $sum       = \App\Models\RecievedWrong::where("transaction_id",$transaction)->sum("current_qty");
                        $wrong     = \App\Models\RecievedWrong::where("transaction_id",$transaction)->get();
                        $move_id        = [];
                        foreach($wrong as $it){
                            $ite_m = \App\Models\ItemMove::where("transaction_id",$transaction)->where("recieve_id",$it->id)->first();
                            if(!empty($ite_m)){ $move_id[] = $ite_m->id; }
                            if(!empty($ite_m)){
                                $date = ($ite_m->date != null)?$ite_m->date:$ite_m->created_at;
                                $_id  =  $ite_m->id;
                                $_product_id  =  $ite_m->product_id;
                                $ite_m->delete();$move_id  = [];
                                $itemMoves = \App\Models\ItemMove::orderBy("date","asc")->orderBy("order_id","desc")->orderBy("id","asc")->where("product_id",$_product_id)->first();
                                if(!empty($itemMoves)){
                                    $move_id [] = $itemMoves->id;
                                    $date       = $itemMoves->date;
                                    \App\Models\ItemMove::updateRefresh($itemMoves,$itemMoves,$move_id,$date);
                                }
                            }
                        }
                        foreach($wrong as $it){ if($it->additional_shipping_id == $id ){ $it->delete(); } }
                }else{
                    $items = \App\Models\ItemMove::where("transaction_id",$transaction)->get();
                    foreach($items as $it){ $it->delete(); }
                }

                //.3.// ............. 
                if(count($RecievedPrevious)>0){
                    foreach($RecievedPrevious as $rp){
                         $this->productUtil->decreaseProductQuantity(
                                $rp->product->id  ,
                                $rp->product->variations[0]->id ,
                                $rp->product->product_locations[0]->id,
                                $rp->current_qty
                            );
                 
                    if(app("request")->input("return_type") == "return_type"){
                        \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                       ->where("store_id",$rp->store->id)
                                                       ->increment('product_qty',$rp->current_qty);
                    }else{
                        \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                       ->where("store_id",$rp->store->id)
                                                       ->decrement('product_qty',$rp->current_qty);
                    }
                        \App\MovementWarehouse::where("product_id",$rp->product->id)
                                                       ->where("transaction_id",$rp->transaction_id)
                                                       ->where("recived_previous_id",$rp->id)
                                                    //    ->where("store_id",$rp->store->id)
                                                       ->delete();
                        $rp->delete();
                    }

                }

                //.4.// .........
                if(count($RecievedWrong)>0){
                    foreach($RecievedWrong as $rp){
                            $this->productUtil->decreaseProductQuantity(
                                $rp->product->id  ,
                                $rp->product->variations[0]->id ,
                                $rp->product->product_locations[0]->id,
                                $rp->current_qty
                            );
                            if(app("request")->input("return_type") == "return_type"){
                                \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                                        ->where("store_id",$rp->store->id)
                                                                        ->increment('product_qty',$rp->current_qty);
                            }else{
                                \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                                ->where("store_id",$rp->store->id)
                                                                ->decrement('product_qty',$rp->current_qty);
                            }
                            \App\MovementWarehouse::where("product_id",$rp->product->id)
                                                        ->where("transaction_id",$rp->transaction_id)
                                                        ->where("recieved_wrong_id",$rp->id)
                                                        // ->where("store_id",$rp->store->id)
                                                        ->delete();
                            $rp->delete();
                    }
                }

                //.5.// ... 
                $StatusLive = \App\Models\StatusLive::where("t_received",$id)->get();
                foreach($StatusLive as $slive){
                    if($slive->shipping_item_id != null){
                        $slive->delete();
                    }else{
                        $tr  = \App\Transaction::find($transaction); 
                        $slive->state   = "Purchase ".$tr->status; 
                        $slive->update();
                    }
                }

                //.6.// .... 
                $payment->delete();

                //.7.//  ... 
                $info = TransactionRecieved::where("transaction_id",$transaction)->get();

                if(!(count($info)>0)){
                    $trans_change_status = Transaction::find($transaction);
                    $trans_change_status->status = "final";
                    $trans_change_status->update();
                }     
                

                DB::commit();
                $output = ['success' => true,
                                'msg' => __('purchase.recieve_deleted_success')
                            ];
            } catch (Exception $e) {
                DB::rollBack();

                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = ['success' => false,
                                'msg' => __('messages.something_went_wrong')
                            ];
            }

            return $output;
        }
    }
    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_delivery($id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments') ) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id =  request()->session()->get("user.business_id");
 
                //. reciept references 
                $payment     = TransactionDelivery::findOrFail($id);
                
                //.. receipt purchase bill number
                $transaction = $payment->transaction_id;

                // .. child of reciept
                $ids         = TransactionDelivery::childs($id);
                 
                // .. child of wrong reciept
                $ids_wrong   = TransactionDelivery::childs_wrong($id);

                //... select all  child
                $DeliveredPreviouse = DeliveredPrevious::whereIn("id",$ids)->get();
                
                //... select all wrong  child
                $DeliveredWrong     = DeliveredWrong::whereIn("id",$ids_wrong)->get();

                DB::beginTransaction();

                //.0.//
                if($payment->is_invoice != null){

                    $separate_bill    = \App\Transaction::find($payment->is_invoice);
                    $separate_lines   = \App\TransactionSellLine::where("transaction_id",$payment->is_invoice)->get();
                    $have_payment     = \App\TransactionPayment::where('transaction_id',$payment->is_invoice)->get();
                    
                    if (count($have_payment) > 0) {
                        $output['success'] = false;
                        $output['msg']     = trans("lang_v1.sorry_there_is_payment");
                        return $output;
                    }
                     
                    /** start entries */
                        $entry_id       = [];
                        $separate_entry = \App\AccountTransaction::where("transaction_id",$payment->is_invoice)->get();
                        foreach($separate_entry as $en){
                            if(!in_array($en->entry_id,$entry_id)){
                                $entry_id[] = $en->entry_id; 
                            }
                        $en->delete(); 
                        }
                        if(count($entry_id)>0){
                            foreach($entry_id as $entry){
                                if($entry!=null){
                                    $en = \App\Models\Entry::find($entry);
                                    $en->delete(); 
                                }
                            }
                        }
                    /** end entries */

                    /** start lines*/
                        foreach($separate_lines as $li){
                            $li->delete(); 
                        }
                    /** end lines*/

                    /** start transaction */
                        $separate_bill->delete(); 
                    /** end transaction */
                }
                

                //.1.// ..... 
                $receive = \App\Models\DeliveredPrevious::where("transaction_id",$transaction)->where("transaction_recieveds_id",$id)->get();
                if(count($receive)>0){
                        $tr        = \App\Transaction::find($transaction); 
                        $sum       = \App\Models\DeliveredPrevious::where("transaction_id",$transaction)->sum("current_qty");
                        $previous  = \App\Models\DeliveredPrevious::where("transaction_id",$transaction)->get();
                        $line_id        = [];
                        $product_id_     = [];
                        $move_id        = []; 
                        foreach($previous as $it){
                            if(!in_array($it->product_id,$line_id)){
                                $line_id[]    = $it->product_id;
                                $product_id_[] = $it->product_id;
                            }
                            $ite_m = \App\Models\ItemMove::where("transaction_id",$transaction)->where("recieve_id",$it->id)->whereNull("is_returned")->first();
                            if(!empty($ite_m)){
                                $ite_m->delete();
                                $move_id  = [];
                                $itemMoves = \App\Models\ItemMove::orderBy("date","asc")->orderBy("order_id","desc")->orderBy("id","asc")->where("product_id",$it->product_id)->first();
                                if(!empty($itemMoves)){
                                    $move_id [] = $itemMoves->id;
                                    $date       = $itemMoves->date;
                                    \App\Models\ItemMove::updateRefresh($itemMoves,$itemMoves,$move_id,$date);
                                }
                               
                            }
                        }
                        foreach($previous as $it){
                            if($it->additional_shipping_id == $id ){
                                $it->delete();
                            }
                        }
                }else{
                    $items = \App\Models\ItemMove::where("transaction_id",$transaction)->get();
                    foreach($items as $it){
                        $it->delete();
                    }
                }

                //.2-1.// ..... 
                $wrong = \App\Models\DeliveredWrong::where("transaction_id",$transaction)->where("transaction_recieveds_id",$id)->get();
                if(count($wrong)>0){
                        $tr        = \App\Transaction::find($transaction); 
                        $sum       = \App\Models\DeliveredWrong::where("transaction_id",$transaction)->sum("current_qty");
                        $wrong     = \App\Models\DeliveredWrong::where("transaction_id",$transaction)->get();
                        foreach($wrong as $it){
                            $ite_m = \App\Models\ItemMove::where("transaction_id",$transaction)->where("recieve_id",$it->id)->first();
                            if(!empty($ite_m)){
                                $ite_m->delete();
                                $move_id  = [];
                                $itemMoves = \App\Models\ItemMove::orderBy("date","asc")->orderBy("order_id","desc")->orderBy("id","asc")->where("product_id",$it->product_id)->first();
                                if(!empty($itemMoves)){
                                    $move_id [] = $itemMoves->id;
                                    $date       = $itemMoves->date;
                                    \App\Models\ItemMove::updateRefresh($itemMoves,$itemMoves,$move_id,$date);
                                }
                            }
                        }
                        
                }else{
                    $items = \App\Models\ItemMove::where("transaction_id",$transaction)->get();
                    foreach($items as $it){
                        $it->delete();
                    }
                }

                //.2.// ...
                if(count($DeliveredPreviouse)>0){
                    foreach($DeliveredPreviouse as $rp){
                         $this->productUtil->updateProductQuantity(
                                $rp->product->product_locations[0]->id,
                                $rp->product->id  ,
                                $rp->product->variations[0]->id ,
                                $rp->current_qty
                            );
                    if(app("request")->input("return_type") == "return_type"){
                        \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                        ->where("store_id",$rp->store->id)
                                                        ->decrement('product_qty',$rp->current_qty);
                    }else{
                        \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                       ->where("store_id",$rp->store->id)
                                                       ->increment('product_qty',$rp->current_qty);
                    }
                        \App\MovementWarehouse::where("product_id",$rp->product->id)
                                                       ->where("transaction_id",$rp->transaction_id)
                                                       ->where("delivered_previouse_id",$rp->id)
                                                       ->where("store_id",$rp->store->id)
                                                       ->delete();
                        $rp->delete();
                    }

                }
                
                //.3.// ... 
                if(count($DeliveredWrong)>0){
                    foreach($DeliveredWrong as $rp){
                         $this->productUtil->decreaseProductQuantity(
                                $rp->product->id  ,
                                $rp->product->variations[0]->id ,
                                $rp->product->product_locations[0]->id,
                                $rp->current_qty
                            );
                        if(app("request")->input("return_type") == "return_type"){
                            \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                            ->where("store_id",$rp->store->id)
                                                            ->decrement('product_qty',$rp->current_qty);
                        }else{
                            \App\Models\WarehouseInfo::where("product_id",$rp->product->id)
                                                            ->where("store_id",$rp->store->id)
                                                            ->increment('product_qty',$rp->current_qty);
                        }
                            \App\MovementWarehouse::where("product_id",$rp->product->id)
                                                        ->where("transaction_id",$rp->transaction_id)
                                                        ->where("delivered_wrong_id",$rp->id)
                                                        ->where("store_id",$rp->store->id)
                                                        ->delete();
                         $rp->delete();
                    }
                }

                //.4.// ... 
                $StatusLive = \App\Models\StatusLive::where("t_received",$id)->get();
                foreach($StatusLive as $slive){
                    if($slive->shipping_item_id != null){
                        $slive->delete();
                    }else{
                        $tr  = \App\Transaction::find($transaction); 
                        $slive->state   = "Sale ".$tr->status; 
                        $slive->update();
                    }
                }
                //.5.// ... 
                $payment->delete();  
                
                // //.7.// ...
                // $info = TransactionDelivery::where("transaction_id",$transaction)->get();

                // if(!(count($info)>0)){
                //     $trans_change_status = Transaction::find($transaction);
                //     $trans_change_status->status = "final";
                //     $trans_change_status->update();
                // }  


                DB::commit();
                $output = ['success' => true,
                                'msg' => __('purchase.recieve_deleted_success')
                            ];
            } catch (Exception $e) {
                DB::rollBack();

                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = ['success' => false,
                                'msg' => __('messages.something_went_wrong')
                            ];
            }

            return $output;
        }
    }

    /**
     * Adds new payment to the given transaction.
     *
     * @param  int  $transaction_id
     * @return \Illuminate\Http\Response
     */
    public function addPayment($transaction_id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views')&& !auth()->user()->can('admin_without.views')&& !auth()->user()->can('admin_supervisor.views')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $business = \App\Business::where("id",$business_id)->first();
            $transaction = Transaction::where('business_id', $business_id)
                                        ->with(['contact', 'location'])
                                        ->findOrFail($transaction_id);
            if ($transaction->payment_status != 'paid') {
                $show_advance = in_array($transaction->type, ['sell', 'purchase']) ? true : false;
                $payment_types = $this->transactionUtil->payment_types($transaction->location, $show_advance);



                $paid_amount = $this->transactionUtil->getTotalPaid($transaction_id);

                $amount = $transaction->final_total - $paid_amount;
                if ($amount < 0) {
                    $amount = 0;
                } 

                $amount_formated       = $this->transactionUtil->num_f($amount);
                $payment_line          = new TransactionPayment();
                $payment_line->amount  = $amount;
                $payment_line->method  = 'cash';
                $payment_line->paid_on = \Carbon::now()->toDateTimeString();
                $cheques               = \App\Models\check::where("transaction_id",$transaction->id)->whereIn("status",[0,2])->get();
                $paymentVoucher        = \App\TransactionPayment::where("transaction_id",$transaction->id)->whereNull("check_id")->get();
                $transaction_child     = \App\Transaction::where("separate_parent",$transaction->id)->where("separate_type","payment")->get();

                //Accounts
                // $accounts     = $this->moduleUtil->accountsDropdown($business_id, true, false, true);
                $account      = \App\Account::where("account_type_id",$business->cash)->get();
                $accounts = [];
                foreach($account as  $it){
                    $accounts[""] = __('messages.please_select');
                    $accounts[$it->id] = $it->account_number . " || " . $it->name; 
                }
                $cheque_type  =  ($transaction->type == 'purchase' || $transaction->type == 'sell_return')?1:0;
                $view         = view('transaction_payment.payment_row')
                                ->with(compact('transaction','transaction_child' ,'paymentVoucher','payment_types', 'payment_line',  'amount_formated', 'accounts','cheque_type','cheques'))->render();

                $output = [ 'status' => 'due',
                                    'view' => $view];
            } else {
                $output = [ 'status' => 'paid',
                                'view' => '',
                                'msg' => __('purchase.amount_already_paid')  ];
            }

            return json_encode($output);
        }
    }


    public function pay_account($id)
    {
        if(request()->ajax()){
            dd("ho=ss");
        }
    }
    /**
     * Adds new payment to the given transaction.
     *
     * @param  int  $transaction_id
     * @return \Illuminate\Http\Response
     */
    public function addRecieve($transaction_id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');
            
            $transaction = Transaction::where('business_id', $business_id)
                                    ->with(['contact', 'location'])
                                    ->findOrFail($transaction_id);

            $purchcaseline = PurchaseLine::where('transaction_id', $transaction_id)->get();
            $RecievedPrevious = RecievedPrevious::where('transaction_id', $transaction_id)->get();
            $product = Product::where('business_id', $business_id)->get();
            $unit = Unit::where('business_id', $business_id)->get();
            $business_locations = BusinessLocation::forDropdown($business_id);
            $Warehouse = Warehouse::where('business_id', $business_id)->get();

            
            $product_list = [];
            foreach($product as $prd){
                $product_list[$prd->id] = $prd->name;
                
            }
            
         
            $Warehouse_list = [];
            foreach($Warehouse as $Ware){
                $Warehouse_list[$Ware->id] = $Ware->name;
                
            }
            // dd($Warehouse_list);
            
     
            $count = 0;
            foreach($purchcaseline as $line){
                $count = $count + 1;
                
            }
            // dd($purchcaseline);
            if ($transaction->payment_status != 'paid') {
                $show_advance  = in_array($transaction->type, ['sell', 'purchase']) ? true : false;
                $payment_types = $this->transactionUtil->payment_types($transaction->location, $show_advance);
                $paid_amount   = $this->transactionUtil->getTotalPaid($transaction_id);

                $amount = $transaction->final_total - $paid_amount;
                if ($amount < 0) {
                    $amount = 0;
                } 
                
                $amount_formated = $this->transactionUtil->num_f($amount);
                
                $payment_line          = new TransactionPayment();
                $payment_line->amount  = $amount;
                $payment_line->method  = 'cash';
                $payment_line->paid_on = \Carbon::now()->toDateTimeString();
                
                //Accounts
                $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);
                
                $view = view('transaction_payment.recieved_row')
                ->with(compact('transaction','Warehouse_list','unit' ,'product','RecievedPrevious' ,'business_locations','payment_types','product_list','purchcaseline', 'payment_line','count','purchcaseline', 'amount_formated', 'accounts'))->render();
                
                $output = [ 'status' => 'due',
                'view' => $view];
            } else {
                $output = [ 'status' => 'paid',
                'view' => '',
                'msg' => __('purchase.amount_already_paid')  ];
            }
            
            return json_encode($output);
        }
    }
    
    /**
     * Shows contact's payment due modal
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function getPayContactDue($contact_id)
    {
        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $due_payment_type = request()->input('type');
            $query = Contact::where('contacts.id', $contact_id)
                            ->leftJoin('transactions AS t', 'contacts.id', '=', 't.contact_id');
            if ($due_payment_type == 'purchase') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                    );
            } elseif ($due_payment_type == 'purchase_return') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                    );
            } elseif ($due_payment_type == 'sale') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'sale' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'sale' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                );
            } elseif ($due_payment_type == 'sell_return') {
                $query->select(
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_return_paid"),
                    'contacts.name',
                    'contacts.supplier_business_name',
                    'contacts.id as contact_id'
                    );
            }

            //Query for opening balance details
            $query->addSelect(
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
            );
            $contact_details = $query->first();
            
            $payment_line = new TransactionPayment();
            if ($due_payment_type == 'purchase') {
                $contact_details->total_purchase = empty($contact_details->total_purchase) ? 0 : $contact_details->total_purchase;
                $payment_line->amount = $contact_details->total_purchase -
                                    $contact_details->total_paid;
            } elseif ($due_payment_type == 'purchase_return') {
                $payment_line->amount = $contact_details->total_purchase_return -
                                    $contact_details->total_return_paid;
            } elseif ($due_payment_type == 'sell') {
                $contact_details->total_invoice = empty($contact_details->total_invoice) ? 0 : $contact_details->total_invoice;

                $payment_line->amount = $contact_details->total_invoice -
                                    $contact_details->total_paid;
            } elseif ($due_payment_type == 'sell_return') {
                $payment_line->amount = $contact_details->total_sell_return -
                                    $contact_details->total_return_paid;
            }

            //If opening balance due exists add to payment amount
            $contact_details->opening_balance = !empty($contact_details->opening_balance) ? $contact_details->opening_balance : 0;
            $contact_details->opening_balance_paid = !empty($contact_details->opening_balance_paid) ? $contact_details->opening_balance_paid : 0;
            $ob_due = $contact_details->opening_balance - $contact_details->opening_balance_paid;
            if ($ob_due > 0) {
                $payment_line->amount += $ob_due;
            }

            $amount_formated = $this->transactionUtil->num_f($payment_line->amount);

            $contact_details->total_paid = empty($contact_details->total_paid) ? 0 : $contact_details->total_paid;
            
            $payment_line->method = 'cash';
            $payment_line->paid_on = \Carbon::now()->toDateTimeString();
                   
            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);

            //Accounts
            $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

            return view('transaction_payment.pay_supplier_due_modal')
                        ->with(compact('contact_details', 'payment_types', 'payment_line', 'due_payment_type', 'ob_due', 'amount_formated', 'accounts'));
        }
    }

    /**
     * Adds Payments for Contact due
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPayContactDue(Request  $request)
    {
        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            
            $this->transactionUtil->payContact($request);

            DB::commit();
            $output = ['success' => true,
                            'msg' => __('purchase.payment_added_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                          'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                      ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * view details of single..,
     * payment.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewPayment($payment_id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')  ) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');
            $single_payment_line = TransactionPayment::findOrFail($payment_id);

            $transaction = null;
            if (!empty($single_payment_line->transaction_id)) {
                $transaction = Transaction::where('id', $single_payment_line->transaction_id)
                                ->with(['contact', 'location', 'transaction_for'])
                                ->first();
            } else {
                $child_payment = TransactionPayment::where('business_id', $business_id)
                        ->where('parent_id', $payment_id)
                        ->with(['transaction', 'transaction.contact', 'transaction.location', 'transaction.transaction_for'])
                        ->first();
                $transaction = $child_payment->transaction;
            }

            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);
            
            return view('transaction_payment.single_payment_view')
                    ->with(compact('single_payment_line', 'transaction', 'payment_types'));
        }
    }
    /**
     * view details of single..,
     * payment.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ //.... correct \//
    public function viewRecieve($payment_id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')  ) {
            abort(403, 'Unauthorized action.');
            }
 
            $business_id = request()->session()->get('business.id');
            $single_payment_line = TransactionRecieved::findOrFail($payment_id);
       
            $RecievedPrevious    = RecievedPrevious::where("transaction_deliveries_id" , $payment_id)->get();
            $RecievedWrong       = RecievedWrong::where("transaction_deliveries_id" ,$payment_id)->get();
            
            $RecPrevious         = RecievedPrevious::where("transaction_deliveries_id" , $payment_id)->sum("current_qty");
            $RecWrong            = RecievedWrong::where("transaction_deliveries_id" , $payment_id)->sum("current_qty");
             
            $purline             = PurchaseLine::where('transaction_id',$single_payment_line->transaction->id)->sum("quantity"); 
            
            $Warehouse_list      = \App\Models\Warehouse::childs($business_id);
  
            $quantity_all        = $RecPrevious;
            $quantity_all1       = $purline;
            $quantity_wrg        = $RecWrong;
            

            return view('transaction_payment.single_recieved_view')
                    ->with(compact('single_payment_line' ,  
                                   'Warehouse_list',   
                                   'quantity_all1' , 
                                   'quantity_all' , 
                                   'quantity_wrg' , 
                                   'RecievedPrevious', 
                                   'RecievedWrong', )
                                );
        
    }

    /**
     * view details of single..,
     * payment.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewDelivered(Request $request ,$payment_id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments') ) {
            abort(403, 'Unauthorized action.');
        }
         if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');
            $single_payment_line    = TransactionDelivery::findOrFail($payment_id);
            $trans_id               = $single_payment_line->transaction_id;
           
            $DeliveredPrevious      = DeliveredPrevious::where("transaction_recieveds_id" , $payment_id)->get();
            $DeliveredWrong         = DeliveredWrong::where("transaction_recieveds_id" ,$payment_id)->get();

            $TransactionSellLine    = TransactionSellLine::where('transaction_id', $trans_id)->get();
            $TraSeLine              = TransactionSellLine::where('transaction_id', $trans_id)->sum("quantity");

            $transaction            = Transaction::find($trans_id);
            
            $DelPrevious_total      = DeliveredPrevious::where("transaction_id" , $trans_id)->sum("current_qty");
            $DelPrevious            = DeliveredPrevious::where("transaction_recieveds_id" , $payment_id)->where("transaction_id" , $trans_id)->sum("current_qty");
            $DelWrong               = DeliveredWrong::where("transaction_recieveds_id" , $payment_id)->where("transaction_id" ,$trans_id)->sum("current_qty");
            
            $totals                 =  $DelPrevious + $DelWrong;

            $notes                  = TransactionSellLine::where("transaction_id",$trans_id)->select(["product_id","sell_line_note"])->get();
            $notes_array            = [];
            foreach($notes as $nte){
                $notes_array[$nte->product_id] = $nte->sell_line_note;
            }
             return view('transaction_payment.single_recieved_view_reciept')
                    ->with(compact('single_payment_line',  
                                    "TransactionSellLine",
                                    'DeliveredWrong',
                                    'DelPrevious_total',
                                    'TraSeLine',
                                    'DelPrevious',
                                    'DelWrong',
                                    'totals',
                                    'notes_array',
                                    'DeliveredPrevious', 
                                    'transaction',
                                 ));
        }
    }
    /**
     * view details of single..,
     * payment.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewRecieve_ref($ref)
    {
         
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');
            $single_payment_line = TransactionRecieved::where("id",$ref)->first();
            $RecievedPrevious = RecievedPrevious::where( "transaction_deliveries_id" , $single_payment_line->id)->get();
            $RecievedWrong = RecievedWrong::where( "transaction_deliveries_id" , $single_payment_line->id)->get();

            $unit = Unit::where('business_id', $business_id)
            ->get();

            $RecievedPrevious_first = RecievedPrevious::where( "transaction_deliveries_id" , $payment_id)->first();

            $trans_id = $RecievedPrevious_first->transaction_id;
             

            $purchcaseline = PurchaseLine::where('transaction_id', $trans_id)
                            ->get();

            $product = Product::where('business_id', $business_id)
                            ->get();
            $products = Product::where('business_id', $business_id)
                            ->get();

            $Warehouse = Warehouse::where('business_id', $business_id)
                            ->get();

            $recieve_previous = [];
            $recieve_item_name = [];
            $quantity_all1 = 0;
            
            foreach($RecievedPrevious as $receive){
                $recieve_previous[$receive->transaction_deliveries_id] = $receive->current_qty;
                $recieve_item_name[$receive->transaction_deliveries_id] = $receive->product_name ;
                $quantity_all1 =  $quantity_all1 + $receive->current_qty; 
            }
            
            $recieve_previous_Wrong = [];
            $recieve_item_name_Wrong = [];
            $product_list = [];
            $Warehouse_list = [];

            foreach($Warehouse as $Ware){
                $Warehouse_list[$Ware->id] = $Ware->name;
                
            }
            foreach($product as $prd){
                foreach($purchcaseline as $pruche){
                    if($pruche->product_id == $prd->id ){
                        $product_list[$prd->id] = $prd->name;
                    }
                }
                
            }
            foreach($RecievedWrong as $receive){
                $recieve_previous_Wrong[$receive->transaction_deliveries_id] = $receive->current_qty;
                $recieve_item_name_Wrong[$receive->transaction_deliveries_id] = $receive->product_name ;
            }
 
             
            $transaction = null;
            if (!empty($single_payment_line->transaction_id)) {
                $transaction = Transaction::where('id', $single_payment_line->transaction_id)
                ->with(['contact', 'location', 'transaction_for'])
                ->first();
            } else {
        
                $child_payment = TransactionDelivery::where('business_id', $business_id)
                ->where('parent_id', $payment_id)
                ->with(['transaction', 'transaction.contact', 'transaction.location', 'transaction.transaction_for'])
                        ->first();
                        $transaction = $child_payment->transaction;
                }
                
                $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);
                
           
            return view('transaction_payment.single_recieved_view_reciept')
                    ->with(compact('single_payment_line' , 'unit' ,'Warehouse_list', "products" , "purchcaseline",  'quantity_all1' , 'product_list' ,'recieve_item_name','RecievedPrevious', 'recieve_previous','recieve_previous_Wrong', 'recieve_item_name_Wrong', 'transaction', 'payment_types'));
        }
    }

    /**
     * Retrieves all the child payments of a parent payments
     * payment.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showChildPayments($payment_id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');

            $child_payments = TransactionPayment::where('business_id', $business_id)
                                                    ->where('parent_id', $payment_id)
                                                    ->with(['transaction', 'transaction.contact'])
                                                    ->get();

            $payment_types = $this->transactionUtil->payment_types(null, false, $business_id);
            
            return view('transaction_payment.show_child_payments')
                    ->with(compact('child_payments', 'payment_types'));
        }
    }

    /**
    * Retrieves list of all opening balance payments.
    *
    * @param  int  $contact_id
    * @return \Illuminate\Http\Response
    */

    public function getOpeningBalancePayments($contact_id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $query = TransactionPayment::leftjoin('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'opening_balance')
                ->where('t.contact_id', $contact_id)
                ->where('transaction_payments.business_id', $business_id)
                ->select(
                    'transaction_payments.amount',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    'transaction_payments.id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number'
                )
                ->groupBy('transaction_payments.id');


            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            
            return Datatables::of($query)
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) {
                    $method = __('lang_v1.' . $row->method);
                    if ($row->method == 'cheque') {
                        $method .= '<br>(' . __('lang_v1.cheque_no') . ': ' . $row->cheque_number . ')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>(' . __('lang_v1.card_transaction_no') . ': ' . $row->card_transaction_number . ')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>(' . __('lang_v1.bank_account_no') . ': ' . $row->bank_account_number . ')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method = __('lang_v1.custom_payment_1') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method = __('lang_v1.custom_payment_2') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method = __('lang_v1.custom_payment_3') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    }
                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="display_currency paid-amount" data-orig-value="' . $row->amount . '" data-currency_symbol = true>' . $row->amount . '</span>';
                })
                ->addColumn('action', '<button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action("TransactionPaymentController@viewPayment", [$id]) }}"><i class="fas fa-eye"></i> @lang("messages.view")
                    </button> <button type="button" class="btn btn-info btn-xs edit_payment" 
                    data-href="{{action("TransactionPaymentController@edit", [$id]) }}"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp; <button type="button" class="btn btn-danger btn-xs delete_payment" 
                    data-href="{{ action("TransactionPaymentController@destroy", [$id]) }}"
                    ><i class="fa fa-trash" aria-hidden="true"></i> @lang("messages.delete")</button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="btn btn-success btn-xs" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                    ->rawColumns(['amount', 'method', 'action'])
                ->make(true);
            }
        }
    /**
     * Return the payment .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function return_payment($id)
    {
        if (!auth()->user()->can('purchase.payments') && !auth()->user()->can('sell.payments')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {

                \DB::beginTransaction();
                // check the payment
                $sum= 0;    
                $payment = TransactionPayment::findOrFail($id);
                if($payment->payment_voucher_id != null){
                    $data                =  \App\Models\PaymentVoucher::find($payment->payment_voucher_id);
                    $transaction_payment =  \App\TransactionPayment::where("payment_voucher_id",$payment->payment_voucher_id)->first();
                    $allDataDebit        =  \App\AccountTransaction::where('payment_voucher_id',$payment->payment_voucher_id)->where("type","debit")->where('amount','>',0)->get();
                    $allDataCredit       =  \App\AccountTransaction::where('payment_voucher_id',$payment->payment_voucher_id)->where("type","credit")->where('amount','>',0)->get();
                    $type                =  "Return Voucher";
                    \App\Models\Entry::create_entries($data,$type);
                    $entry = \App\Models\Entry::where("state","Return Voucher")->where("voucher_id",$payment->payment_voucher_id)->first();
                    foreach($allDataDebit as $debit){
                        $beta =  $debit->replicate();
                        $beta->type = "credit";
                        $beta->entry_id = $entry->id;
                        $beta->save();
                    }
                    foreach($allDataCredit as $credit){
                        $beta =  $credit->replicate();
                        $beta->type = "debit";
                        $beta->entry_id = $entry->id;
                        $beta->save();
                        
                    }
                    $data->return_voucher = 1;
                    $transaction_payment->return_payment = 1;
                    $data->update();
                    $transaction_payment->update();
                    $TR              = \App\Transaction::find($transaction_payment->transaction_id);
                    $TR->sub_type    = "return_payment";
                    $TR->update();
                  
                    if($transaction_payment->is_invoice == 1){
                        $parent          = \App\Transaction::find($TR->separate_parent);
                        $total_parent    = $parent->final_total;
                        $allTransaction  = \App\Transaction::where("id","!=",$TR->id)->whereNull("sub_type")->where("separate_parent",$TR->separate_parent)->get();
                        foreach($allTransaction as $one_pay){
                            $sum    +=   $one_pay->final_total; 
                        }
                        if(!empty($total_parent)){
                            $parent_balance = $parent->final_total;
                            $status  = 'due';
                            if ($parent_balance <= $sum) {
                                $status = 'paid';
                            } elseif ($sum > 0 && $parent_balance > $sum) {
                                $status = 'partial';
                            }
                            $parent->payment_status = $status;
                            $parent->update();
                        }
                        $TR->payment_status = 'due';
                        $TR->update();
                        
                    }else{
                        $allPayment = \App\TransactionPayment::where("transaction_id",$TR->id)->where("return_payment",0)->where("id","!=",$transaction_payment->id)->sum("amount");
                        $total_parent    = $TR->final_total;
                        $status  = 'due';
                        if ($total_parent <= $allPayment) {
                            $status = 'paid';
                        } elseif ($allPayment > 0 && $total_parent > $allPayment) {
                            $status = 'partial';
                        }
                        
                        $TR->payment_status = $status;
                        $TR->update();
                    }
                }
                \DB::commit();
                $output = [
                    "success" => 1,
                    "msg"     => __("Returned Successfully"),
                ];
                
                    
            } catch (\Exception $e) {
                DB::rollBack();

                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = ['success' => 0,
                                // 'msg' => __('messages.something_went_wrong')
                                'msg' => __('messages.something_went_wrong')
                            ];
            }

            return $output;
        }
    }
}
