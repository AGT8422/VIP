<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AccountController;
use Illuminate\Http\Request;
use App\Utils\Util;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Models\GournalVoucher;
use App\Models\Entry;
use App\Account;
use App\Models\GournalVoucherItem;
use DB;
use Illuminate\Support\Facades\Config;
class GournalVoucherController extends Controller
{
    public function __construct(ModuleUtil $moduleUtil, Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, AccountController $accountMainData)
    {
        $this->accountMainData = $accountMainData;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->commonUtil  = $commonUtil;
        $this->productUtil = $productUtil;
    }
    public function index(Request $request)
    {
        if (!auth()->user()->can('gournal_voucher.view') && !auth()->user()->can('gournal_voucher.create')) {
            abort(403, 'Unauthorized action.');
        }
        $accounts    =  Account::items();
        $business_id =  request()->session()->get('user.business_id');
        $allData     =  GournalVoucher::OrderBy('id','desc')->where('business_id',$business_id)->where(function($query) use($request){
                            if ($request->name) {
                                $query->where('ref_no','LIKE','%'.$request->name.'%');
                            }
                            if ($request->date_from) {
                                $query->whereDate('date','>=',$request->date_from);
                            }
                            if ($request->date_to) {
                                $query->whereDate('date','<=',$request->date_to);
                            }
                           })->paginate(30);
        $items = GournalVoucherItem::select()->get();
        return view('gournal_voucher.index')
                 ->with('accounts',$accounts)
                 ->with('allData',$allData)
                 ->with('items',$items)
                 ->with('title',trans('home.expenseJurnalist'));
    }
    public function add()
    {
        if (!auth()->user()->can('gournal_voucher.create')) {
            abort(403, 'Unauthorized action.');
        }
        $currency     =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies   = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $accounts     =  Account::main('cash',null,'bank');
        $reportSetting = \App\Models\ReportSetting::first();
        $expenses = [];
        $databaseName =  "izo26102024_esai" ; $dab =  Config::get('database.connections.mysql.database'); 
        
        if($reportSetting){
            if($reportSetting->expense != null){
               $all_expenses                          =  $this->accountMainData->childOfType($reportSetting->expense);
               $all_expenses[$reportSetting->expense] = \App\AccountType::find($reportSetting->expense)->name;
            }
            foreach($all_expenses as $ie => $exp_id){
                $get_expenses = \App\Account::where('account_type_id',$ie)
                                        ->orWhereHas('account_type',function($query) use($ie){
                                                $query->where('parent_account_type_id',$ie);
                                                $query->orWhere('id',$ie);
                                        })->select(['id','name','account_number'])->get();
                foreach($get_expenses as $e => $exp)
                {
                    $expenses[$exp->id] = $exp->name . " || " . $exp->account_number;
                }
            }
            // $expenses     =  Account::main('Expenses');
        //     if($databaseName == $dab){
        //         dd( $reportSetting,$expenses,$all_expenses);
        //    }
        } 
        $taxes        =  Account::main('Tax Vat 100355364900003');
        $cost_centers =  Account::cost_centers();
        return view('gournal_voucher.add')
                ->with('accounts',$accounts)
                ->with('cost_centers',$cost_centers)
                ->with('currencies',$currencies)
                ->with('expenses',$expenses)
                ->with('taxes',$taxes)
                ->with('title',trans('home.expenseJurnal'));
    }
    public function post_add(Request $request)
    {
        if (!auth()->user()->can('gournal_voucher.create')) {
            abort(403, 'Unauthorized action.');
        }
      
        $request->validate(['image.mimes'=>'png,jpeg,png,jpeg,pdf']);
        $business_id   =  request()->session()->get('user.business_id');
        $edit_days = request()->session()->get('business.transaction_edit_days');
        $edit_date = request()->session()->get('business.transaction_edit_date');  
        if (!$this->transactionUtil->canBeEdited(0, $edit_date,$request->gournal_date)) {
            $output =  [
                        'success' => 0,
                        'msg'     => __('messages.transaction_add_not_allowed', ['days' => $edit_date])];
            return redirect('gournal-voucher')->with('status', $output);
        }
        DB::beginTransaction();
        $company_name      = request()->session()->get("user_main.domain");
        
        $ref_count             =  $this->productUtil->setAndGetReferenceCount("gouranl_voucher");
        $ref_no                =  $this->productUtil->generateReferenceNumber("gouranl_voucher" , $ref_count);
        $document_expense = [];
        $referencesNewStyle = str_replace('/', '-', $ref_no);
        if ($request->hasFile('document_expense')) { $count_doc1 = 1;
            foreach ($request->file('document_expense') as $file) {
                
                #................
                if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                    if ($file->getSize() <= config('constants.document_size_limit')){ 
                        $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                        $file->move('uploads/companies/'.$company_name.'/documents/expense-voucher',$file_name_m);
                        $file_name =  'uploads/companies/'.$company_name.'/documents/expense-voucher/'. $file_name_m;
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
                        $file_name =  'uploads/companies/'.$company_name.'/documents/expense-voucher/'. $new_file_name;
                        // if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\expense-voucher\\$new_file_name"),20)) {
                        //     $uploaded_file_name = $new_file_name;
                        // }
                        $public_path = public_path('uploads/companies/'.$company_name.'/documents/expense-voucher');
                        if (!file_exists($public_path)) {
                            mkdir($public_path, 0755, true);
                        }
                        // if ($imgs->save($public_path ."/". $new_file_name)) {
                        //     $uploaded_file_name = $new_file_name;
                        // }
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
                    }
                }
                #................

                array_push($document_expense,$file_name);
            }
        }
       $setting               =  \App\Models\SystemAccount::where('business_id',$business_id)->first();
       $data                  =  new  GournalVoucher ;
       $data->date            =  $request->gournal_date;
       $data->main_account_id =  $request->main_account_id;
       $data->cost_center_id  =  $request->cost_center_id;
       $data->currency_id     =  $request->currency_id;
       $data->exchange_price  =  $request->currency_id_amount;       
       $data->ref_no          =  $ref_no;
       $data->business_id     =  $business_id;
       
       $data->total_credit    =  $request->total_credit;
       $data->main_credit     =  ($request->main_credit != null)?1:0;

       $data->document        = json_encode($document_expense) ;
       $data->save();

       $net = 0;
       foreach ($request->amount as $key=>$amount) { 
          # ....................................
          $item                       =  new GournalVoucherItem;
          $item->credit_account_id    =  $request->credit_account_id[$key]??$request->main_account_id;
          $item->debit_account_id     =  $request->debit_account_id[$key];
          $item->tax_account_id       =  $setting->journal_expense_tax;
          $item->amount               =  $request->amount[$key];
          $item->text                 =  $request->text[$key];
          $item->tax_percentage       =  $request->tax_percentage[$key];
          $item->tax_amount           =  $request->tax_amount[$key];
          $item->text                 =  $request->text[$key];
          $item->date                 =  ($request->gournal_date)?$request->gournal_date:$request->date[$key];
          $item->cost_center_id       =  ($request->center_id[$key])??$request->cost_center_id;
          $item->gournal_voucher_id   =  $data->id;
          $item->save();
          
          # ....................................
          if($request->main_credit != null  && $request->main_credit != 0){
            $this->effect_debit_total($item->id);
          }else{
            $this->effect_account($item->id);
          }
          $this->effect_cost_center($item);
          $net  += ($item->amount - $item->tax_amount);
         
       } 
       if($request->main_credit != null && $request->main_credit != 0){ 
            $this->effect_account_total($request->total_credit,$request->main_account_id,$item->id,$request->note_main);
       }
       $type="journalEx";
       \App\Models\Entry::create_entries($item,$type);
       $output = [
        'success' => true,
        'msg'     => trans('home.Done Successfully')
        ]; 
       DB::commit();
       return redirect('gournal-voucher')->with('status',$output);
    }
    public function edit($id)
    {
        if (!auth()->user()->can('gournal_voucher.update')) {
            abort(403, 'Unauthorized action.');
        }
        $currency     =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies   = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        $accounts     =  Account::main('cash',null,'bank');
        $reportSetting = \App\Models\ReportSetting::first();
        $expenses = [];
        if($reportSetting){
            if($reportSetting->expense != null){
               $all_expenses =  $this->accountMainData->childOfType($reportSetting->expense);
            }
            foreach($all_expenses as $ie => $exp_id){
                $get_expenses = \App\Account::where('account_type_id',$ie)
                                        ->orWhereHas('account_type',function($query) use($ie){
                                                $query->where('parent_account_type_id',$ie);
                                                $query->orWhere('id',$ie);
                                        })->select(['id','name','account_number'])->get();
                foreach($get_expenses as $e => $exp)
                {
                    $expenses[$exp->id] = $exp->name . " || " . $exp->account_number;
                }
            }
            // $expenses     =  Account::main('Expenses');
        } 
        $taxes        =  Account::main('Tax Vat 100355364900003');
        $data         =  GournalVoucher::find($id);
        $cost_centers =  Account::cost_centers();
        return view('gournal_voucher.edit')
                 ->with('accounts',$accounts)
                 ->with('cost_centers',$cost_centers)
                 ->with('currencies',$currencies)
                 ->with('expenses',$expenses)
                 ->with('taxes',$taxes)
                 ->with('data',$data)
                 ->with('title',trans('home.Gournal Voucher'))
                            ;
    }
    public function post_edit(Request $request,$id)
    {
        if (!auth()->user()->can('gournal_voucher.update')) {
            abort(403, 'Unauthorized action.');
        }
        $edit_days = request()->session()->get('business.transaction_edit_days');
        $edit_date = request()->session()->get('business.transaction_edit_date');
        $tr               = GournalVoucher::find($id);
        $dateFilter       = (\Carbon::parse($request->gournal_date)<\Carbon::parse($tr->date))?$request->gournal_date:$tr->date;
        if (!$this->transactionUtil->canBeEdited($id, $edit_date,$dateFilter)) {
            $output =  [
                        'success' => 0,
                        'msg'     => __('messages.transaction_edit_not_allowed', ['days' => $edit_date])];
            return redirect('gournal-voucher')->with('status', $output);
        }
        DB::beginTransaction();
        $request->validate([  'image.mimes'=>'png,jpeg,png,jpeg,pdf'  ]);
        $access                = 0;
        $business_id           =  request()->session()->get('user.business_id');
        $setting               =  \App\Models\SystemAccount::where('business_id',$business_id)->first();
        $data                  =  GournalVoucher::find($id) ;

        $old_status            =  $data->main_credit;
        $old_account_main      =  $data->main_account_id;
        $note_main             =  $request->note_main;
        $old_date              =  $data->date;

        $data->date            =  $request->gournal_date;
        $data->main_account_id =  $request->main_account_id;
        $data->cost_center_id  =  $request->cost_center_id;
        $data->currency_id     =  $request->currency_id;
        $data->exchange_price  =  $request->currency_id_amount;
        $data->total_credit    =  $request->total_credit;
        $data->main_credit     =  ($request->main_credit != null)?1:0;

        $old_document          =  $data->document;
        if($old_document == null){  $old_document = [];  }
        $company_name      = request()->session()->get("user_main.domain");
        $referencesNewStyle = str_replace('/', '-', $data->ref_no);
        if($request->hasFile('document_expense')) { $count_doc2 = 1;
            foreach ($request->file('document_expense') as $file) {
                
                #................
                if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                    if ($file->getSize() <= config('constants.document_size_limit')){ 
                        $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$count_doc2++.'_'.$file->getClientOriginalName();
                        $file->move('uploads/companies/'.$company_name.'/documents/expense-voucher',$file_name_m);
                        $file_name =  'uploads/companies/'.$company_name.'/documents/expense-voucher/'. $file_name_m;
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
                        $file_name =  'uploads/companies/'.$company_name.'/documents/expense-voucher/'. $new_file_name;
                        // if ($imgs->save(public_path("uploads\companies\\$company_name\documents\\expense-voucher\\$new_file_name"),20)) {
                        //     $uploaded_file_name = $new_file_name;
                        // }
                        $public_path = public_path('uploads/companies/'.$company_name.'/documents/expense-voucher');
                        if (!file_exists($public_path)) {
                            mkdir($public_path, 0755, true);
                        }
                        // if ($imgs->save($public_path . "/" . $new_file_name)) {
                        //     $uploaded_file_name = $new_file_name;
                        // }
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
                    }
                }
                #................

                 $old_document[] = $file_name ;
            }
        }

        if(json_encode($old_document)!="[]"){
            $data->document   = json_encode($old_document) ;
        }

        $data->update();
        
        $ids  =  $request->item_id??[];
        $revs =  GournalVoucherItem::where('gournal_voucher_id',$id)->whereNotIn('id',$ids)->get();
        foreach ($revs as $key => $rev) {
            $ac_transaction = \App\AccountTransaction::where('gournal_voucher_item_id',$rev->id)->get();
            foreach($ac_transaction as $itemOne){
                $account_id_transaction = $itemOne->account_id;
                $action_date            = $itemOne->operation_date;
                $itemOne->delete();
                $act                    = \App\Account::find($account_id_transaction);
                if($act->cost_center!=1){ \App\AccountTransaction::nextRecords($act->id,$act->business_id,$action_date); }
            }
            $rev->delete();
        }
        $net = 0;
        if ($request->item_id) {
            foreach ($request->item_id as $key => $old_id) {
                $item                       =  GournalVoucherItem::find($old_id);
                $old_credit                 =  $item->credit_account_id;
                $old_debit                  =  $item->debit_account_id;
                $old_tax                    =  $item->tax_account_id ;
                # old
                $item->credit_account_id    =  $request->old_credit_account_id[$key]??$request->main_account_id;
                $item->debit_account_id     =  $request->old_debit_account_id[$key];
                $item->amount               =  $request->old_amount[$key];
                $item->text                 =  $request->old_text[$key];
                $item->tax_percentage       =  $request->old_tax_percentage[$key];
                $item->tax_amount           =  $request->old_tax_amount[$key];
                $item->text                 =  $request->old_text[$key];
                $item->date                 =  ($request->gournal_date)?$request->gournal_date:$request->old_date[$key];
                $item->cost_center_id       =  $request->old_center_id[$key]??$request->cost_center_id;
                $item->save();
                $net                       += ($item->amount - $item->tax_amount);
                if($request->main_credit != null && $request->main_credit != 0){ 
                    $this->edit_effect_accounts($item,$request->total_credit,$old_debit,$old_tax,$old_status);
                }else{
                    $this->edit_effect($item,$old_credit,$old_debit,$old_tax,$old_account_main,$old_status);
                }
                $this->effect_cost_center($item);
            }
            if($request->main_credit != null && $request->main_credit != 0){ 
                $access = 1;
                $this->edit_effect_main($item,$request->total_credit,$request->main_account_id,$request->note_main,$old_account_main,$old_status,$note_main,$old_date);
            }else{
                $trans    =  \App\AccountTransaction::where('gournal_voucher_id',$id)->where('account_id',$old_account_main)->first();
                if($trans){
                    $account_transaction = $trans->account_id ;
                    $action_date         = $trans->operation_date ;
                    $trans->delete();
                    $account             = \App\Account::find($account_transaction);
                    if($account->cost_center!=1){ \App\AccountTransaction::nextRecords($account->id,$business_id,$action_date); }
                }    
            }
           
        }
        if ($request->amount) {
            foreach ($request->amount as $key=>$account_id) { 
                $item                       =  new GournalVoucherItem;
                $item->credit_account_id    =  $request->credit_account_id[$key]??$request->main_account_id;
                $item->debit_account_id     =  $request->debit_account_id[$key];
                $item->tax_account_id       =  $setting->journal_expense_tax;
                $item->amount               =  $request->amount[$key];
                $item->text                 =  $request->text[$key];
                $item->tax_percentage       =  $request->tax_percentage[$key];
                $item->tax_amount           =  $request->tax_amount[$key];
                $item->text                 =  $request->text[$key];
                $item->date                 =  ($request->gournal_date)?$request->gournal_date:$request->date[$key];
                $item->cost_center_id       =  $request->center_id[$key]??$request->cost_center_id;
                $item->gournal_voucher_id   =  $data->id;
                $item->save();
                $net                       += ($item->amount - $item->tax_amount);
                if($request->main_credit != null  && $request->main_credit != 0){
                    $this->effect_debit_total($item->id);
                }else{
                    $this->effect_account($item->id);
                }
                $this->effect_cost_center($item);
            }
            if($old_status == 0){
                if($access==0){
                    if($request->main_credit != null  && $request->main_credit != 0){
                        $this->effect_account_total($request->total_credit,$request->main_account_id,$item->id,$request->note_main);
                    }
                }
            }else{ 
                if($request->main_credit != null  && $request->main_credit != 0){
                    if($access==0){
                        $this->edit_effect_main($item,$request->total_credit,$request->main_account_id,$request->note_main,$old_account_main,$old_status,$note_main,$old_date);
                    } 
                }else{
                    $trans    =  \App\AccountTransaction::where('gournal_voucher_id',$id)->where('account_id',$old_account_main)->first();
                    if($trans){
                        $account_transaction = $trans->account_id ;
                        $action_date         = $trans->operation_date ;
                        $trans->delete();
                        $account             = \App\Account::find($account_transaction);
                        if($account->cost_center!=1){ \App\AccountTransaction::nextRecords($account->id,$business_id,$action_date); }
                    }    
                }
            }
        }
       $output = [
            'success' => true,
            'msg'     => trans('home.Done Successfully')
        ];
        DB::commit();
        return redirect('gournal-voucher')->with('status',$output);
    }
    public function effect_cost_center($data)
    {
        $credit =  \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)
                        ->whereHas('account',function($query){
                            $query->where('cost_center','>',0);
                        })->first();
        if ($data->cost_center_id) {
            if (!empty($credit)) {
                $credit->update([
                    'amount'      => $data->amount - $data->tax_amount,
                    'account_id'  => $data->cost_center_id,
                ]);
            }else {
                $credit_data =  [
                    'amount'                  => $data->amount - $data->tax_amount,
                    'account_id'              => $data->cost_center_id,
                    'type'                    => 'credit',
                    'sub_type'                => 'deposit',
                    'operation_date'          => $data->date,
                    'created_by'              => session()->get('user.id'),
                    'note'                    => $data->text??'add journal expense', 
                    'gournal_voucher_item_id' => $data->id
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            }
        }else {
            // \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)->delete();
        }
    }
    public function effect_account($id,$type=null)
    {
        $data  =  GournalVoucherItem::find($id);
        # credit account  
        $credit_data = [
            'amount'                  => $data->amount,
            'account_id'              => $data->credit_account_id,
            'type'                    => 'credit',
            'sub_type'                => 'deposit',
            'operation_date'          => $data->date,
            'created_by'              => session()->get('user.id'),
            'note'                    => $data->text, 
            'gournal_voucher_item_id' => $data->id,
            // 'gournal_voucher_id'      => $data->gournal_voucher->id
        ];
        $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
        $account = \App\Account::find($data->credit_account_id);
        if($account->cost_center!=1){
            \App\AccountTransaction::nextRecords($account->id,$data->gournal_voucher->business_id,$data->date);
        }
        if($type==null){
            if($data->tax_amount > 0){
                # tax account  
                $credit_data = [
                    'amount'                  => $data->tax_amount,
                    'account_id'              => $data->tax_account_id,
                    'type'                    => 'debit',
                    'sub_type'                => 'deposit',
                    'operation_date'          => $data->date,
                    'created_by'              => session()->get('user.id'),
                    'note'                    => $data->text,
                    'gournal_voucher_item_id' => $data->id
                ];
                $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                $account = \App\Account::find($data->tax_account_id);
                if($account->cost_center!=1){
                    \App\AccountTransaction::nextRecords($account->id,$data->gournal_voucher->business_id,$data->date);
                }
            }
            # tax account  
            $credit_data = [
                'amount'                  => ($data->amount - $data->tax_amount),
                'account_id'              => $data->debit_account_id,
                'type'                    => 'debit',
                'sub_type'                => 'deposit',
                'operation_date'          => $data->date,
                'created_by'              => session()->get('user.id'),
                'note'                    => $data->text,
                'gournal_voucher_item_id' => $data->id,
                'cs_related_id'           => $data->cost_center_id

            ];
            $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
            $account = \App\Account::find($data->debit_account_id);
            if($account->cost_center!=1){
                \App\AccountTransaction::nextRecords($account->id,$data->gournal_voucher->business_id,$data->date);
            }
        }
    }
    public function effect_account_total($total,$account_id,$id,$note)
    {
        $data  =  GournalVoucherItem::find($id);
        // credit account  
        $credit_data = [
            'amount'                  => $total,
            'account_id'              => $account_id,
            'type'                    => 'credit',
            'sub_type'                => 'deposit',
            'operation_date'          => $data->date,
            'created_by'              => session()->get('user.id'),
            'note'                    => $note, 
            'gournal_voucher_id'      => $data->gournal_voucher->id,
            'gournal_voucher_item_id' => $data->id,
        ];
        $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
        $account = \App\Account::find($account_id);
        if($account->cost_center!=1){
            \App\AccountTransaction::nextRecords($account->id,$data->gournal_voucher->business_id,$data->date);
        }
    }
    public function effect_debit_total($id)
    {
        $data  =  GournalVoucherItem::find($id);
        if($data->tax_amount > 0){
            # tax account  
            $credit_data = [
                'amount'                  => $data->tax_amount,
                'account_id'              => $data->tax_account_id,
                'type'                    => 'debit',
                'sub_type'                => 'deposit',
                'operation_date'          => $data->date,
                'created_by'              => session()->get('user.id'),
                'note'                    => $data->text,
                'gournal_voucher_item_id' => $data->id
            ];
            $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
            $account = \App\Account::find($data->tax_account_id);
            if($account->cost_center!=1){
                \App\AccountTransaction::nextRecords($account->id,$data->gournal_voucher->business_id,$data->date);
            }
        }
        # tax account  
        $credit_data = [
            'amount'                  => ($data->amount - $data->tax_amount),
            'account_id'              => $data->debit_account_id,
            'type'                    => 'debit',
            'sub_type'                => 'deposit',
            'operation_date'          => $data->date,
            'created_by'              => session()->get('user.id'),
            'note'                    => $data->text,
            'gournal_voucher_item_id' => $data->id,
            'cs_related_id'           => $data->cost_center_id
        ];
        $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
        $account = \App\Account::find($data->debit_account_id);
        if($account->cost_center!=1){
            \App\AccountTransaction::nextRecords($account->id,$data->gournal_voucher->business_id,$data->date);
        }
    }
    public function edit_effect($data,$old_credit,$old_debit,$old_tax,$old_account_main,$old_status) 
    {

        if($old_status == 1){
            $gournal_id            =  ($data->gournal_voucher)?$data->gournal_voucher->id:null;
            if($gournal_id != null){
                $trans             =  \App\AccountTransaction::where('gournal_voucher_id',$gournal_id)->where('account_id',$old_account_main)->first();
           
                if($trans){
                    $account_transaction  = $trans->account_id;
                    $action_date          = $trans->operation_date;
                    $trans->delete();
                    $account = \App\Account::find($account_transaction);
                    if($account->cost_center!=1){
                        \App\AccountTransaction::nextRecords($account->id,$data->gournal_voucher->business_id,$action_date);
                    }
                }    
            }
            $this->effect_account($data->id,1);
        }else{
            $business_id = request()->session()->get("user.business_id");
            $setting     = \App\Models\SystemAccount::where("business_id",$business_id)->first();
            $tax         = ($setting)?$setting->journal_expense_tax:\App\Account::add_main('tax expense');
            # credit account  
            $creditAccount = \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)->where('account_id',$old_credit)->get();
            foreach($creditAccount as $itemCredit){
                # ..................................................
                $action_date_old            = $itemCredit->operation_date ;
                $action_date_new            = $data->date ;
                $accountOld                 = \App\Account::find($old_credit);
                $accountNew                 = \App\Account::find($data->credit_account_id);
                # ..................................................
                $itemCredit->amount         = $data->amount ;
                $itemCredit->operation_date = $data->date   ;
                $itemCredit->account_id     = $data->credit_account_id ;
                $itemCredit->update();
                # ..................................................
                $dateFinal              = ($action_date_old<$action_date_new)?$action_date_old:$action_date_new;
                if($old_credit != $data->credit_account_id){ 
                    if($accountOld->cost_center!=1){ \App\AccountTransaction::nextRecords($accountOld->id,$data->gournal_voucher->business_id,$dateFinal); }
                }
                if($accountNew->cost_center!=1){ \App\AccountTransaction::nextRecords($accountNew->id,$data->gournal_voucher->business_id,$dateFinal); }  
            }
            # tax account  
            $taxAccount = \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)->where('account_id',$old_tax)->get();
            foreach($taxAccount  as $itemTax){
                # ..................................................
                $action_date_old            = $itemTax->operation_date ;
                $action_date_new            = $data->date ;
                $accountOld                 = \App\Account::find($old_tax);
                $accountNew                 = \App\Account::find($tax);
                # ..................................................
                $itemTax->amount            = $data->tax_amount ;
                $itemTax->operation_date    = $data->date   ;
                $itemTax->account_id        = $tax ;
                $itemTax->update();
                # ..................................................
                $dateFinal              = ($action_date_old<$action_date_new)?$action_date_old:$action_date_new;
                if($old_tax != $tax){ 
                    if($accountOld->cost_center!=1){ \App\AccountTransaction::nextRecords($accountOld->id,$data->gournal_voucher->business_id,$dateFinal); }
                }
                if($accountNew->cost_center!=1){ \App\AccountTransaction::nextRecords($accountNew->id,$data->gournal_voucher->business_id,$dateFinal); }
            } 
            # debit account  
            $debitAccount = \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)->where('account_id',$old_debit)->get();
            foreach($debitAccount   as $itemDebit){
                # ..................................................
                $action_date_old           = $itemDebit->operation_date ;
                $action_date_new           = $data->date ;
                $accountOld                = \App\Account::find($old_debit);
                $accountNew                = \App\Account::find($data->debit_account_id);
                # ..................................................
                $itemDebit->amount         = ($data->amount - $data->tax_amount);
                $itemDebit->operation_date = $data->date   ;
                $itemDebit->account_id     = $data->debit_account_id;
                $itemDebit->cs_related_id  = $data->cost_center_id ;
                $itemDebit->update();
                # ..................................................
                $dateFinal              = ($action_date_old<$action_date_new)?$action_date_old:$action_date_new;
                if($old_debit != $data->debit_account_id){ 
                    if($accountOld->cost_center!=1){ \App\AccountTransaction::nextRecords($accountOld->id,$data->gournal_voucher->business_id,$dateFinal); }
                }
                if($accountNew->cost_center!=1){ \App\AccountTransaction::nextRecords($accountNew->id,$data->gournal_voucher->business_id,$dateFinal); }
            } 
        }
    }

    public function edit_effect_accounts($data,$old_credit,$old_debit,$old_tax,$old_status) 
    {
      
        $business_id  = request()->session()->get("user.business_id");
        $setting      = \App\Models\SystemAccount::where("business_id",$business_id)->first();
        $tax          = ($setting)?$setting->journal_expense_tax:\App\Account::add_main('tax expense');
        $taxAccount   = \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)->where('account_id',$old_tax)->get();
        foreach($taxAccount as $itemTax){
            # ..................................................
            $action_date_old            = $itemTax->operation_date ;
            $action_date_new            = $data->date ;
            $accountOld                 = \App\Account::find($old_tax);
            $accountNew                 = \App\Account::find($tax);
            # ..................................................
            $itemTax->amount            = $data->tax_amount ;
            $itemTax->operation_date    = $data->date   ;
            $itemTax->account_id        = $tax ;
            $itemTax->update();
            # ..................................................
            $dateFinal              = ($action_date_old<$action_date_new)?$action_date_old:$action_date_new;
            if($old_tax != $tax){ 
                if($accountOld->cost_center!=1){ \App\AccountTransaction::nextRecords($accountOld->id,$data->gournal_voucher->business_id,$dateFinal); }
            }
            if($accountNew->cost_center!=1){ \App\AccountTransaction::nextRecords($accountNew->id,$data->gournal_voucher->business_id,$dateFinal); } 
        }
        $debitAccount = \App\AccountTransaction::where('gournal_voucher_item_id',$data->id)->where('account_id',$old_debit)->get();
        foreach($debitAccount as $itemDebit){
            # ..................................................
            $action_date_old            = $itemDebit->operation_date ;
            $action_date_new            = $data->date ;
            $accountOld                 = \App\Account::find($old_tax);
            $accountNew                 = \App\Account::find($tax);
            # ..................................................
            $itemDebit->amount          = ($data->amount - $data->tax_amount);
            $itemDebit->operation_date  = $data->date   ;
            $itemDebit->account_id      = $data->debit_account_id ;
            $itemDebit->cs_related_id   = $data->cost_center_id ;
            $itemDebit->update();
            # ..................................................
            $dateFinal              = ($action_date_old<$action_date_new)?$action_date_old:$action_date_new;
            if($old_tax != $tax){ 
                if($accountOld->cost_center!=1){ \App\AccountTransaction::nextRecords($accountOld->id,$data->gournal_voucher->business_id,$dateFinal); }
            }
            if($accountNew->cost_center!=1){ \App\AccountTransaction::nextRecords($accountNew->id,$data->gournal_voucher->business_id,$dateFinal); }
        } 
 
    }
    public function edit_effect_main($data,$total,$account_id,$note,$old_account_main,$old_status,$note_main,$old_date) 
    {
      
        if($data->gournal_voucher->main_account_id != $old_account_main){
            if($old_status == 0){
                $list_ids    = GournalVoucherItem::where('gournal_voucher_id',$data->gournal_voucher->id)->pluck('id');
                $list_main   = [$data->gournal_voucher->main_account_id,$old_account_main];
                $this->delete_credit_items($list_ids,$list_main);
                $credit_data = [
                    'amount'                  => $data->gournal_voucher->total_credit,
                    'account_id'              => $data->gournal_voucher->main_account_id,
                    'type'                    => 'credit',
                    'sub_type'                => 'deposit',
                    'operation_date'          => $data->date,
                    'created_by'              => session()->get('user.id'),
                    'note'                    => $note_main, 
                    'gournal_voucher_id'      => $data->gournal_voucher->id
                    // 'gournal_voucher_item_id' => $data->id,
                ];
                $credit     = \App\AccountTransaction::createAccountTransaction($credit_data);
                $accountOld = \App\Account::find($old_account_main);
                $account    = \App\Account::find($data->gournal_voucher->main_account_id);
                $dateFinal  = ($old_date<$data->date)?$old_date:$data->date;
                if($accountOld->cost_center!=1){ \App\AccountTransaction::nextRecords($accountOld->id,$accountOld->business_id,$dateFinal); }
                if($account->cost_center!=1){ \App\AccountTransaction::nextRecords($account->id,$account->business_id,$dateFinal); }

            }else{
                # ..........................................
                $gournal_id            =  ($data->gournal_voucher)?$data->gournal_voucher->id:null;
                if($gournal_id != null){
                    $trans             =  \App\AccountTransaction::where('gournal_voucher_id',$gournal_id)->where('account_id',$old_account_main)->first();
                    if(!empty($trans)){
                        $account_transaction  = $trans->account_id;
                        $action_date          = $trans->operation_date;
                        $trans->delete();
                        $account = \App\Account::find($account_transaction);
                        if($account->cost_center!=1){
                            \App\AccountTransaction::nextRecords($account->id,$data->gournal_voucher->business_id,$action_date);
                        }
                    }
                }
                $credit_data = [
                    'amount'                  => $data->gournal_voucher->total_credit,
                    'account_id'              => $data->gournal_voucher->main_account_id,
                    'type'                    => 'credit',
                    'sub_type'                => 'deposit',
                    'operation_date'          => $data->date,
                    'created_by'              => session()->get('user.id'),
                    'note'                    => $note_main, 
                    'gournal_voucher_id'      => $gournal_id
                ];
                $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
                $account = \App\Account::find($data->gournal_voucher->main_account_id);
                if($account->cost_center!=1){ \App\AccountTransaction::nextRecords($account->id,$data->gournal_voucher->business_id,$data->date); }
                
            }
        }else if($data->gournal_voucher->main_account_id == $old_account_main){
            if($old_status == 0){
                # ..............................................
                $list_ids  = GournalVoucherItem::where('gournal_voucher_id',$data->gournal_voucher->id)->pluck('id');
                $list_main = [$data->gournal_voucher->main_account_id,$old_account_main];
                $this->delete_credit_items($list_ids,$list_main);
                $credit_data = [
                    'amount'                  => $data->gournal_voucher->total_credit,
                    'account_id'              => $data->gournal_voucher->main_account_id,
                    'type'                    => 'credit',
                    'sub_type'                => 'deposit',
                    'operation_date'          => $data->date,
                    'created_by'              => session()->get('user.id'),
                    'note'                    => $note_main, 
                    'gournal_voucher_id'      => $data->gournal_voucher->id
                    // 'gournal_voucher_item_id' => $data->id
                ];
                $credit   = \App\AccountTransaction::createAccountTransaction($credit_data);
                $account  = \App\Account::find($data->gournal_voucher->main_account_id);
                if($account->cost_center!=1){ \App\AccountTransaction::nextRecords($account->id,$data->gournal_voucher->business_id,$data->date); }
                
            }else{
                # ..............................................
                $gournal_id            =  ($data->gournal_voucher)?$data->gournal_voucher->id:null;
                if($gournal_id != null){
                    $trans             =  \App\AccountTransaction::where('gournal_voucher_id',$gournal_id)->where('account_id',$data->gournal_voucher->main_account_id)->first();
                    if(!empty($trans)){
                        # ...........................................
                        $accountOld            = \App\Account::find($data->gournal_voucher->main_account_id);
                        $account               = \App\Account::find($account_id);
                        $oldT_date             = $trans->operation_date;
                        # ...........................................
                        $trans->amount         = $total;
                        $trans->operation_date = $data->date;
                        $trans->account_id     = $account_id;
                        $trans->note           = $note;
                        $trans->update();
                        # ...........................................
                        $dateFinal              = ($oldT_date<$data->date)?$oldT_date:$data->date;
                        if($data->gournal_voucher->main_account_id != $account_id){ 
                            if($accountOld->cost_center!=1){ \App\AccountTransaction::nextRecords($accountOld->id,$accountOld->business_id,$dateFinal); }
                        }
                        if($account->cost_center!=1){ \App\AccountTransaction::nextRecords($account->id,$account->business_id,$dateFinal); } 
                    }
                }
            }
        }
        
    }

    public function delete_credit_items($list_ids,$account_id) {
        $credit =  \App\AccountTransaction::whereIn('gournal_voucher_item_id',$list_ids)
                                        ->where('type','credit')
                                        ->whereHas('account',function($query) use($account_id){
                                            $query->where('cost_center',"=",0);
                                        })->get();
        
        foreach($credit as $i){ 
            $account_transaction  = $i->account_id;
            $action_date          = $i->operation_date;
            $i->delete();
            $account = \App\Account::find($account_transaction);
            if($account->cost_center!=1){ \App\AccountTransaction::nextRecords($account->id,$account->business_id,$action_date);  }
        }
        $main   = \App\AccountTransaction::whereIn('gournal_voucher_id',$account_id)->get();
        foreach($main as $i){
            $account_transaction  = $i->account_id;
            $action_date          = $i->operation_date;
            $i->delete();
            $account = \App\Account::find($account_transaction);
            if($account->cost_center!=1){ \App\AccountTransaction::nextRecords($account->id,$account->business_id,$action_date); }
        }
    } 
    public function create_credit_items($list_ids) {
        foreach($list_ids as $id){
            $data  =  GournalVoucherItem::find($id);
            # credit account  
            $credit_data = [
                'amount'                  => $data->amount,
                'account_id'              => $data->credit_account_id,
                'type'                    => 'credit',
                'sub_type'                => 'deposit',
                'operation_date'          => $data->date,
                'created_by'              => session()->get('user.id'),
                'note'                    => $data->text, 
                'gournal_voucher_item_id' => $data->id
            ];
            $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
            $account = \App\Account::find($data->credit_account_id);
            if($account->cost_center!=1){ \App\AccountTransaction::nextRecords($account->id,$account->business_id,$data->date);  }
        }
    } 
    public function delete($id)
    {
        if (!auth()->user()->can('gournal_voucher.delete')) {
            abort(403, 'Unauthorized action.');
        }
        $edit_days = request()->session()->get('business.transaction_edit_days');
        $edit_date = request()->session()->get('business.transaction_edit_date');
        $tr               = GournalVoucher::find($id);
        $dateFilter       = \Carbon::parse($tr->date) ;
        if (!$this->transactionUtil->canBeEdited($id, $edit_date,$dateFilter)) {
            $output =  [
                        'success' => 0,
                        'msg'     => __('messages.transaction_edit_not_allowed', ['days' => $edit_date])];
            return redirect('gournal-voucher')->with('status', $output);
        }
        foreach (GournalVoucherItem::where('gournal_voucher_id',$id)->get() as $item) {
                $allAccounts =  \App\AccountTransaction::where('gournal_voucher_item_id',$item->id)->get();
                foreach($allAccounts as $account){
                    $account_id  = $account->account_id;
                    $action_date = $account->operation_date;
                    $account->delete();
                    $act         = \App\Account::find($account_id);
                    if($act->cost_center!=1){
                        \App\AccountTransaction::nextRecords($act->id,$act->business_id,$action_date);
                    }
                }
                $item->delete();
        }
        $data  =  GournalVoucher::find($id);
        if ($data) {
            $data->delete();
        }
        $output = [
            'success' => true,
            'msg'     => trans('home.Done Successfully')
        ];
        return back()->with('status',$output);
    }
    public function view($id)
    {
        if (!auth()->user()->can('gournal_voucher.view')) {
            abort(403, 'Unauthorized action.');
        }
        $vcher  =  GournalVoucher::find($id);
        $items  =  GournalVoucherItem::where('gournal_voucher_id',$id)->get();
        $accts_ =  \App\Account::select("id","name")->get();
        $accts  =  [];
        foreach($accts_ as $ac){
            $accts[$ac->id]= $ac->name;
        }
        return view("gournal_voucher.viewVoucher")->with(compact("vcher","items","accts"));
    }
    public function attach($id)
    {
        $data        = GournalVoucher::find($id);
        return view("gournal_voucher.attach")->with(compact("data"));
    }
    public function entry($id)
    {
        $ids     =  [];
        $data    =  GournalVoucher::find($id);
        $dp      =  GournalVoucherItem::where("gournal_voucher_id",$data->id)->get();
        foreach($dp as $i){$ids[]=$i->id;}
        $entry   =  Entry::get(); 
        $allData =  \App\AccountTransaction::whereIn('gournal_voucher_item_id',$ids)->whereHas("account",function($query){
                                                            $query->where("cost_center",0);
                                                        })->where('amount','>',0)->orWhere('gournal_voucher_id',$id)->get();
         return view('gournal_voucher.entry')
               ->with('allData',$allData)
               ->with('entry',$entry)
               ->with('data',$data); 
    }
}
