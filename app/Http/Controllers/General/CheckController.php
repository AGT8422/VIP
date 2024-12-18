<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\Check;
use  App\TransactionPayment;
use  App\BusinessLocation;
use App\Contact;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\ProductUtil;
use App\Account;
use App\Models\Entry;
use App\Models\ContactBank;
use App\Utils\Util;
use DB;

class CheckController extends Controller
{
     // protected $moduleUtil;
    public function __construct(ModuleUtil $moduleUtil, ProductUtil $productUtil,TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }
   
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $allData =  Check::OrderBy('id','desc')->where('business_id',$business_id)
                            ->where(function($query) use($request){
                                if ($request->location_id) {
                                    $query->where('location_id',$request->location_id);
                                }
                                
                                if ($request->contact_id) {
                                    $id_account = \App\Account::find($request->contact_id);
                                     $query->whereRaw('IF(account_type = "1", contact_id = "'.$request->contact_id.'", contact_id = "'.$id_account->contact_id.'") ');
                                 }
                                if ($request->type_cheque_co) {
                                    if($request->type_cheque_co == 1){
                                        $number = 0;
                                    }elseif($request->type_cheque_co == 2){
                                        $number = 1;
                                    }elseif($request->type_cheque_co == 3){
                                        $number = 4;
                                    }elseif($request->type_cheque_co == 4){
                                        $number = 2;
                                    }
                                    $query->where('status',$number);
                                }
                                if ($request->cheque_type) {
                                   
                                    $query->where('type',$request->cheque_type);
                                }
                                if ($request->write_date_from) {
                                    $query->whereDate('write_date','>=',$request->write_date_from);
                                }
                                if ($request->write_date_to) {
                                    $query->whereDate('write_date','<=',$request->write_date_to);
                                }
                                if ($request->due_date_from) {
                                    $query->whereDate('due_date','>=',$request->due_date_from);
                                }
                                if ($request->due_date_to) {
                                    $query->whereDate('due_date','<=',$request->due_date_to);
                                }
                            })->where(function($query) use($request){
                                if ($request->name) {
                                    $query->where('ref_no','LIKE','%'.$request->name.'%');
                                    $query->orWhere('cheque_no','LIKE','%'.$request->name.'%');
                                }
                            })->paginate(30);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        // $contacts = Contact::contactDropdown($business_id, false, false);
        $types    = Check::types();
         $contacts = [];
        $allAccount = Account::select("*")->get();
        foreach($allAccount as $i){
            $contacts[$i->id]= $i->name . " || " . $i->account_number;
        }
        $business = \App\Business::find($business_id);
        $bank  = ($business)?$business->bank:null;
        $accounts = Account::items($bank);
        $setting = \App\Models\SystemAccount::where("business_id",$business_id)->first();

       
        return view('cheques.index')
                ->with(compact('allData','setting','business_locations','contacts','types','accounts'));

    }
    public function add(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $contacts = Contact::customers();
        if ($request->type == 1) {
            $contacts = Contact::suppliers();
        }
        $account      = \App\Account::where("contact_id",null)->first();
        $currency     =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies   = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $types    =  Check::types();
        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false, true);
        }
        $id      =  (count($contacts))?array_keys($contacts)[0]:NULL;
        $banks   =  ContactBank::items();
        $title   =  (app('request')->input('type') == 1)?'cheque out':' cheque in';
        $accountss      = Account::where("business_id",$business_id)->get();
        $account_list = [];
        foreach($accountss as $act){
            $account_list[$act->id] = $act->name . " || " . $act->account_number;
        }
        $setting = \App\Models\SystemAccount::where("business_id",$business_id)->first();
        return view('cheques.add')
                ->with(compact('business_locations','currencies','account_list','contacts','types','accounts','banks','title'))
                ;
    }
    public function post_add(Request $request)
    {
        
        $bills_      =  $request->only(['bill_id','bill_amount']);
        $id_trans    = null;
        $transaction = null;
        if(!empty($bills_)){
            foreach($bills_["bill_id"] as $bl){ $id_trans =  $bl;  if( $transaction == null ) { $transaction = $bl;  }else { $transaction .= ",".$bl ; } }
        }
        # ................................................
        # .................................................
        # Generate reference number
        $ref_count                = $this->productUtil->setAndGetReferenceCount("Cheque");
        $ref_no                   = $this->productUtil->generateReferenceNumber("Cheque" , $ref_count);
        # return $this->add_main($request->cheque_type);
        $company_name      = request()->session()->get("user_main.domain");
        $document_expense = [];
        if ($request->hasFile('document_expense')) {
            $count_doc1 = 1;
            $referencesNewStyle = str_replace('/', '-', $ref_no);
            foreach ($request->file('document_expense') as $file) {
                
                #................
                if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                    if ($file->getSize() <= config('constants.document_size_limit')){ 
                        $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                        $file->move('uploads/companies/'.$company_name.'/documents/check',$file_name_m);
                        $file_name =  'uploads/companies/'.$company_name.'/documents/check/'. $file_name_m;
                    }
                }else{
                    if ($file->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                        $Data         = getimagesize($file);
                        $width         = $Data[0];
                        $height        = $Data[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2; 
                        $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        $file_name =  'uploads/companies/'.$company_name.'/documents/check/'. $new_file_name;
                        if ($imgs->save(public_path("uploads\companies\\$company_name\documents\check\\$new_file_name"),20)) {
                            $uploaded_file_name = $new_file_name;
                        }
                            
                    }
                }
                #................
                 array_push($document_expense,$file_name);
            }
        }
        $business_id              =  request()->session()->get('user.business_id');
        $setting                  =  \App\Models\SystemAccount::where('business_id',$business_id)->first();
        $id                       =  ($request->cheque_type == 0)?$setting->cheque_collection:$setting->cheque_debit;
        
        $data                     =  new Check;
        $data->location_id        =  $request->location_id;
        $data->contact_id         =  $request->contact_id;
        $data->business_id        =  $business_id;
        $data->cheque_no          =  $request->cheque_no;
        $data->amount             =  $request->amount;
        $data->contact_bank_id    =  $request->bank_id;
        $data->type               =  $request->cheque_type;
        $data->write_date         =  $request->write_date;
        $data->due_date           =  $request->due_date;
        $data->note               =  $request->note;
        $data->currency_id        =  $request->currency_id;
        $data->exchange_price     =  ($request->currency_id != null)?$request->currency_id_amount:null;
        $data->amount_in_currency =  ($request->currency_id != null && $request->currency_id_amount != 0)?$request->amount / $request->currency_id_amount:null;
        $data->account_type       =  1;
        $data->account_id         =  $id;
        $data->document           = json_encode($document_expense) ;
        $data->ref_no             =  $ref_no;
        $data->save();

        Check::add_action($data->id,'added');
        $type        = ($data->type == 0)?'debit':'credit';
        $credit_data = [
            'amount'            => $request->amount,
            'account_id'        => $id,
            'transaction_id'    => $id_trans,
            'type'              => $type,
            'sub_type'          => 'deposit',
            'operation_date'    => $request->write_date,
            'created_by'        => session()->get('user.id'),
            'note'              => 'added cheque',
            'check_id'          => $data->id,
            'transaction_array' => $transaction,
        ];
        $credit       = \App\AccountTransaction::createAccountTransaction($credit_data);
        $accountCheck = \App\Account::find($id);
        if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$request->write_date); }
    
        Check::contact_effect($data->id,$transaction,$id_trans,"all",$request->contact_id);
        $bills  =  $request->only([
                        'bill_id','bill_amount'
                    ]);
        if ($request->bill_id) {
            \App\Services\Cheque\Bill::pay_transaction($data->id,$bills);
        }
        $str_arr = explode (",", $transaction); 
        $types = "check";
        \App\Models\Entry::create_entries($data,$types);
        


        return redirect('cheque')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function edit($id)
    {
        $business_id        = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $data               = Check::find($id);
        $contacts           = Contact::customers();
        if ($data->type == 1) {
            $contacts = Contact::suppliers();
        }
        $currency           =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies         = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $types              =  Check::types();
        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts   = Account::forDropdown($business_id, true, false, true);
        }
        $accountss      = Account::where("business_id",$business_id)->get();
        $account_list = [];
        foreach($accountss as $act){
            $account_list[$act->id] = $act->name . " || " . $act->account_number;
        }
        $banks          =  ContactBank::items();
        return view('cheques.edit')
                ->with(compact('business_locations','currencies','account_list','contacts','types','accounts','banks','data'))
                ;
    }
    public function post_edit(Request $request,$id)
    {
        
        DB::beginTransaction();
        $business_id              = request()->session()->get('user.business_id');
        $data                     = Check::find($id);
        $old_contact_id           = $data->contact_id;
        $data->contact_id         = $request->contact_id;
        $data->business_id        = $business_id;
        $data->cheque_no          = $request->cheque_no;
        $data->amount             = $request->amount;
        $data->contact_bank_id    = $request->bank_id;
        $data->write_date         = $request->write_date;
        $data->due_date           = $request->due_date;
        $data->note               = $request->note;
        $old_document             = $data->document;
        $data->currency_id        = $request->currency_id;
        $data->exchange_price     = ($request->currency_id != null)?$request->currency_id_amount:null;
        $data->amount_in_currency = ($request->currency_id != null && $request->currency_id_amount != 0)?$request->amount / $request->currency_id_amount:null;
        // $data->account_id      = $request->account_id;
        # ....................................
        $company_name      = request()->session()->get("user_main.domain");
        if($old_document == null){  $old_document = []; }
        $referencesNewStyle = str_replace('/', '-', $data->ref_no);
        if ($request->hasFile('document_expense')) { $count_doc2 = 1;
            foreach ($request->file('document_expense') as $file) {
                
                
                #................
                if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                    if ($file->getSize() <= config('constants.document_size_limit')){ 
                        $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$count_doc2++.'_'.$file->getClientOriginalName();
                        $file->move('uploads/companies/'.$company_name.'/documents/check',$file_name_m);
                        $file_name =  'uploads/companies/'.$company_name.'/documents/check/'. $file_name_m;
                    }
                }else{
                    if ($file->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time().'_'.$referencesNewStyle.'_'.$count_doc2++.'_'.$file->getClientOriginalName();
                        $Data         = getimagesize($file);
                        $width         = $Data[0];
                        $height        = $Data[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2; 
                        $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        $file_name =  'uploads/companies/'.$company_name.'/documents/check/'. $new_file_name;
                        if ($imgs->save(public_path("uploads\companies\\$company_name\documents\check\\$new_file_name"),20)) {
                            $uploaded_file_name = $new_file_name;
                        }
                            
                    }
                }
                #................
                array_push($old_document,$file_name);
            }
        }
        if(json_encode($old_document)!="[]"){ $data->document        =  json_encode($old_document) ; }
        # ....................................
        if($old_contact_id != $request->contact_id ){
            $data->account_type    =  1;
        }
        $data->save();
        $allChecks          = \App\AccountTransaction::where('check_id',$id)->get();
        foreach($allChecks as $iCk){
            $account_transaction = $iCk->account_id; 
            $action_date         = $iCk->operation_date; 
            $iCk->amount         = $data->amount;
            $iCk->operation_date = $request->write_date;
            $iCk->update();
            $accountCheck = \App\Account::find($account_transaction);
            if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$action_date); }
        }
        
        if($old_contact_id != $request->contact_id ){
            $account     =  \App\Account::where('id',$data->contact_id)->first();
            $old_account =  \App\Account::where('id',$old_contact_id)->first();

            $allChecks   = \App\AccountTransaction::where('check_id',$id)->where('account_id',$old_account->id)->get();
            foreach($allChecks as $iCk){
                $account_transaction = $iCk->account_id; 
                $action_date         = $iCk->operation_date; 
                $iCk->account_id     = $account->id;
                $iCk->operation_date = $request->write_date;
                $iCk->update();
                $accountCheck        = \App\Account::find($account_transaction);
                $accountCheckNew     = \App\Account::find($account->id);
                if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$action_date); }
                if($accountCheckNew->cost_center!=1){  \App\AccountTransaction::nextRecords($accountCheckNew->id,$accountCheckNew->business_id,$request->write_date); }
            }
             
        }
        $transactionPay  = \App\TransactionPayment::where('check_id', $id)->first();
        if($transactionPay){
            $old_payment = $transactionPay->replicate();
            $parent      = \App\Models\ParentArchive::save_payment_parent($transactionPay->id,"Edit",$old_payment);
        }
        if($transactionPay){
            $sum           =  \App\TransactionPayment::where('transaction_id',$transactionPay->transaction_id)->sum("amount");
            $final         =  $sum - $transactionPay->amount ;
            $transaction   =  \App\Transaction::find($transactionPay->transaction_id);
            $total_bill    =  $transaction->final_total;
            //...... here no previous payment 
            if($final == 0){
                $margin_total = $total_bill - $request->amount;
                if($margin_total < 0 || $margin_total == 0){
                    $payment_amount_final = $total_bill;
                }elseif($margin_total > 0){
                    $payment_amount_final = $request->amount;
                } 
            }else{
                $margin_total = $total_bill - $final;
                $now          = $margin_total - $request->amount; 
                if($now < 0 || $now == 0){
                    $payment_amount_final = $total_bill;
                }elseif($margin_total > 0){
                    $payment_amount_final = $now;
                } 
            }
            \App\TransactionPayment::where('check_id', $id)->update([
                'amount'=>$payment_amount_final,
                'source'=>$payment_amount_final
            ]);
        } 
        $bills   =  $request->only([
                            'bill_id','bill_amount','old_bill_id','old_bill_amount','payment_id'
                        ]);
        \App\Services\Cheque\Bill::update_pay_transaction($data->id,$bills);
        DB::commit();
      //  Check::add_action($id,'edit');
        return redirect('cheque')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function delete($id)
    {
        $data =  Check::find($id);
        DB::beginTransaction();
        if ($data) {
            $allChecks = \App\AccountTransaction::where('check_id',$id)->get();
           
            foreach($allChecks as $iCk){
                $account_transaction = $iCk->account_id; 
                $action_date         = $iCk->operation_date;  
                $iCk->delete();
                $accountCheck        = \App\Account::find($account_transaction); 
                
                if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$action_date); }
            }
            $payment =  \App\TransactionPayment::where("check_id",$id)->first();
         
            if(!empty($payment)){
                $payment->amount = 0;
                $payment->update();
                $total_paid = $payment->amount;
            }else{
                $total_paid = 0;
            }
            
 
            $final_amount = \App\Transaction::find($data->transaction_id);
            if(!empty($final_amount)){
                $balance = $final_amount->final_total;
            
                $status = 'due';
                if ($balance <= $total_paid) {
                    $status = 'paid';
                } elseif ($total_paid > 0 && $balance > $total_paid) {
                    $status = 'partial';
                }
                $final_amount->payment_status = $status;
                $final_amount->update();

            } 
            if(!empty($payment)){
                $payment->delete();
             }
            $data->delete();
        }
        DB::commit();
        return back()
                ->with('yes',trans('home.Done Successfully'));
    }
    public function collect($id,Request $request)
    {
        
        DB::beginTransaction();
        $payment                  = \App\TransactionPayment::where("check_id",$id)->first();
        Check::add_action($id,'collect');
        $data                     =  Check::find($id);
        $data->status             =  1;
        $data->collecting_date    =  date('Y-m-d');
        $data->collect_account_id =  $request->account_id;
        $data->save();
        $type        = ($data->type == 1) ?'credit':'debit';
        $re_type     = ($data->type == 1) ?'debit' :'credit';
        $credit_data = [
            'amount'         => $data->amount,
            'account_id'     => $request->account_id,
            'type'           => $type,
            'sub_type'       => 'deposit',
            'operation_date' => $request->date,
            'created_by'     => session()->get('user.id'),
            'note'           => 'collecting cheque',
            'check_id'       => $data->id,
            'transaction_id' => $data->transaction_id
        ];
        \App\AccountTransaction::createAccountTransaction($credit_data);
        $accountCheck = \App\Account::find($request->account_id);
        if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$request->date); }
        # update old account
        $setting     = \App\Models\SystemAccount::where('business_id',$data->business_id)->first();
        $main_id     = ($request->cheque_type_ == 0)?$setting->cheque_debit:$setting->cheque_collection;
        $credit_data = [
            'amount'         => $data->amount,
            'account_id'     => $main_id,
            'type'           => $re_type,
            'sub_type'       => 'deposit',
            'operation_date' => $request->date,
            'created_by'     => session()->get('user.id'),
            'note'           => 'collecting cheque',
            'check_id'       => $data->id,
            'transaction_id' => $data->transaction_id
        ];
        \App\AccountTransaction::createAccountTransaction($credit_data);
        $accountCheck = \App\Account::find($main_id);
        if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$request->date); }
        
        $type = "Collect Cheque";
        if(!empty($payment)){

            $allPayment = \App\TransactionPayment::where("transaction_id",$payment->transaction_id)->sum("amount");
            $bill       = \App\Transaction::find($payment->transaction_id);
            $totalPaid  = $this->transactionUtil->getTotalPaid($payment->transaction_id);
            
            if($totalPaid == 0){ //**  when there is no old payment   {due}  */ 
                $status = 'due';
                if(round($bill->final_total,2) <= $data->amount){
                        $status = 'paid';
                }elseif(round($bill->final_total,2) > $data->amount){
                        $status = 'partial';
                }
            }elseif($totalPaid <  round($bill->final_total,2)){  //**  when there is old payment   {partial}  */
                $status = 'partial';
                if(round($bill->final_total,2) <= $data->amount){
                    $status = 'paid';
                }elseif(round($bill->final_total,2) > $data->amount){
                    $status = 'partial';
                }
            }elseif($totalPaid >=  round($bill->final_total,2)){ //**  when there is old payment   {paid}  */
                $status = 'paid';
            }

            \App\Transaction::where('id',$bill->id)->update([
                'payment_status' => $status
            ]); 

        }
        \App\Models\Entry::create_entries($data,$type);
       
        DB::commit();

        return redirect('cheque')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function delete_collect($id)
    {
        if(request()->ajax()){
            # ...........................................
            $data         =  Check::find($id);
            $entry        = \App\AccountTransaction::orderBy("id","desc")->where("note","collecting cheque")->where('check_id',$id)->first();
            $data->status = 3;
            $data->update();
            $accountCheck = \App\Account::find($entry->account_id);
            if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$entry->operation_date); }
            # delete collecting account
            $x1   =  \App\AccountTransaction::where('account_id',$data->collect_account_id)
                                            ->where('check_id',$id)
                                            ->where("entry_id",$entry->entry_id)
                                            ->where('type','credit')
                                            ->first();
            $action_date  = $x1->operation_date;
            $accountCheck = \App\Account::find($x1->account_id);
            $x1->delete();
            if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$action_date); }
            $x2   =  \App\AccountTransaction::where('account_id',$data->account_id)
                                            ->where('check_id',$id)
                                            ->where("entry_id",$entry->entry_id)
                                            ->where('type','debit')
                                            ->first();
            $action_date  = $x2->operation_date;
            $accountCheck = \App\Account::find($x2->account_id);
            $x2->delete();                              
            if($accountCheck->cost_center!=1){ \App\AccountTransaction::nextRecords($accountCheck->id,$accountCheck->business_id,$action_date); }
            # ...........................................
            $entry_id = \App\Models\Entry::find($entry->entry_id);
            $entry_id->delete();
            # ...........................................
            return redirect('cheque')
                ->with('yes',trans('home.Done Successfully'));
        }

    }
    public function un_collect($id)
    {
        DB::beginTransaction();
        $data =  Check::find($id);
        $data->status = 4;
        $data->save();
        # reference collecting account
        Check::un_collect($id);
        $type ="UNCollect Cheque";
        
        \App\Models\Entry::create_entries($data,$type);
        DB::commit();
        return redirect('cheque')
                ->with('yes',trans('home.Done Successfully'));
    }
    ///................................ 
    public function refund($id)
    {
        Check::add_action($id,'refund');
        $data         =  Check::find($id);
        $payment      = \App\TransactionPayment::where("check_id",$id)->first();
        $old_state    =  $data->status;
        $data->status =  2;
        $data->save();
        //Check::update_status($data->status,$old_state);
      
        if(app("request")->input("old")){
            Check::refund($id);
        }else{
            Check::refund($id,1);
        }
        $pay  = \App\TransactionPayment::where("check_id",$id)->first();
        if(!empty($pay)){
            $account_transaction = \App\AccountTransaction::where("note","refund Collect")
                            ->where("check_id",$id)->get();
            foreach($account_transaction as $itemOne){
                $acct_id                 = $itemOne->account_id;
                $action_date             = $itemOne->operation_date;
                $itemOne->transaction_id = $pay->transaction_id;
                $itemOne->update();
                $acc                     = \App\Account::find($acct_id);
                if($acc->cost_center != 1){
                    \App\AccountTransaction::nextRecords($acc->id,$acc->business_id,$action_date);
                }
            }
            
            $pay->amount   = 0;
            $pay->update();
            $tr = \App\Transaction::find($pay->transaction_id);
            // \App\Services\Cheque\Bill::update_status($tr);
            
            $allPayment = \App\TransactionPayment::where("transaction_id",$pay->transaction_id)->sum("amount");
            $bill       = \App\Transaction::find($pay->transaction_id);
            // dd( $payment->amount  . ' ' . round($bill->final_total,2) . ' ' . $data->amount . ' ' . $allPayment);
            $totalPaid = $this->transactionUtil->getTotalPaid($pay->transaction_id);
            // dd( $totalPaid );
            if($totalPaid == 0){ //**  when there is no old payment   {due}  */ 
                $status = 'due';
            }elseif($totalPaid <  round($bill->final_total,2)){  //**  when there is old payment   {partial}  */
                $status = 'partial';
            }elseif($totalPaid >=  round($bill->final_total,2)){ //**  when there is old payment   {paid}  */
                $status = 'paid';
            }
            \App\Transaction::where('id',$bill->id)->update([
                'payment_status'=>$status
            ]); 
            
        }
        $type = "Refund Cheque";
        \App\Models\Entry::create_entries($data,$type);
        return redirect('cheque')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function show($id)
    {   
        $payment = TransactionPayment::where("check_id",$id)->get();
        $data    = Check::find($id) ;
        return view('cheques.partials.view_cheque')
                ->with('data',$data)
                ->with('payment',$payment);
    }
    public function attach($id)
    {
        $data        = Check::find($id);
        return view("cheques.attach")->with(compact("data"));
    }
    public function entry($id)
    {   
        $allData =  \App\AccountTransaction::where('check_id',$id)->whereNull("for_repeat")->where('amount','>',0)->get();
        $data    =  Check::find($id);
        $entry   =  Entry::get(); 
        
        return view('cheques.entry')
               ->with('allData',$allData)
               ->with('entry',$entry)
               ->with('data',$data);
    }
    public static function add_main($type)
    {
        $business_id = request()->session()->get('user.business_id');
        $setting =  \App\Models\SystemAccount::where('business_id',$business_id)->first();    
        if($setting != null){  $id =  ($type == 0)?$setting->cheque_collection:$setting->cheque_debit;  }else{ $id = 0;  }
        return $id;
    }
    public function cheque_bills(Request $request)
    {

        if ($request->type == 1) {
            $allData =  \App\Services\Cheque\Bill::supplier();
        }else{
            $allData =  \App\Services\Cheque\Bill::customer();
        }
        
        return $allData;
    }
}
