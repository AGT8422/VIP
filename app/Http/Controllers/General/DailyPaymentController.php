<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyPayment;
use App\Models\Entry;
use App\Models\DailyPaymentItem;
use App\Account;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use DB;
class DailyPaymentController extends Controller
{
    public function __construct(ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->moduleUtil  = $moduleUtil;
        $this->productUtil = $productUtil;
    }
    public function index(Request $request)
    {
        if (!auth()->user()->can('daily_payment.view') && !auth()->user()->can('daily_payment.create')) {
            abort(403, 'Unauthorized action.');
        }
        $accounts    =  Account::items();
        $business_id = request()->session()->get('user.business_id');
        $allData     =  DailyPayment::OrderBy('id','desc')->where('business_id',$business_id)
                        ->where(function($query) use($request){
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
        return view('daily_payments.index')
                 ->with('accounts',$accounts)
                 ->with('allData',$allData)
                 ->with('title',trans('home.DailyPayment List'));
    }
    public function add()
    {
        if (!auth()->user()->can('daily_payment.create')) {
            abort(403, 'Unauthorized action.');
        }
        $accounts           =  Account::items();
        $costs              =  Account::cost_centers();
        $currency           =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies         = [];
        foreach($currency as $i){
            $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )";
        }
        return view('daily_payments.add')
                 ->with('accounts',$accounts)
                 ->with('costs',$costs)
                 ->with('currencies',$currencies)
                 ->with('title',trans('home.Daily Payment'))
                            ;
    } 
    public function post_add(Request $request)
    {
        if (!auth()->user()->can('daily_payment.create')) {
            abort(403, 'Unauthorized action.');
        }
       \DB::beginTransaction();
       $request->validate([ 'image.mimes'=>'png,jpeg,png,jpeg,pdf' ]);
       $business_id      = request()->session()->get('user.business_id');
       $ref_count        = $this->productUtil->setAndGetReferenceCount("daily_payment");
       $ref_no           = $this->productUtil->generateReferenceNumber("daily_payment" , $ref_count);
       # .................................................
       $company_name      = request()->session()->get("user_main.domain");
       $document_expense = [];
       if ($request->hasFile('document_expense')) { $count_doc1 = 1;
           $referencesNewStyle = str_replace('/', '-', $ref_no); 
           foreach ($request->file('document_expense') as $file) {
                #................
                if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                    if ($file->getSize() <= config('constants.document_size_limit')){ 
                        $file_name    =   time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                        $file->move('uploads/companies/'.$company_name.'/documents/journal-voucher',$file_name);
                        $source_file_name =  'uploads/companies/'.$company_name.'/documents/journal-voucher/'. $file_name;
                    }
                }else{
                    if ($file->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time().'_'.$referencesNewStyle.'_'.$count_doc1++.'_'.$file->getClientOriginalName();
                        $data          = getimagesize($file);
                        $width         = $data[0];
                        $height        = $data[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2; 
                        $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        $source_file_name =  'uploads/companies/'.$company_name.'/documents/journal-voucher/'. $new_file_name;
                        // if ($imgs->save(public_path("uploads\companies\\$company_name\documents\journal-voucher\\$new_file_name"),20)) {
                        //     $uploaded_file_name = $new_file_name;
                        // }
                        $public_path = public_path('uploads/companies/'.$company_name.'/documents/journal-voucher');
                        if (!file_exists($public_path)) {
                            mkdir($public_path, 0755, true);
                        }
                        if ($imgs->save($public_path ."/" . $new_file_name)) {
                            $uploaded_file_name = $new_file_name;
                        }
                    }
                }
                #................
                array_push($document_expense,$source_file_name);
           }
       }
       # .................................................
       $data                      =  new  DailyPayment ;
       $data->amount              =  round($request->total_credit,2);
       $data->date                =  $this->productUtil->uf_date($request->date, true);
       $data->ref_no              =  $ref_no;
       $data->business_id         =  $business_id;
       $data->currency_id         =  $request->currency_id;
       $data->exchange_price      =  $request->currency_id_amount;
       $data->document            =  json_encode($document_expense) ;
       $data->save();
       # .................................................
       foreach ($request->account_id as $key=>$account_id) {
          $item                   =  new DailyPaymentItem;
          $item->account_id       =  $account_id;
          $item->credit           =  round($request->credit[$key],2);
          $item->debit            =  round($request->debit[$key],2);
          $item->text             =  $request->text[$key];
          $item->cost_center_id   =  $request->cost_center_id[$key];
          $item->daily_payment_id =  $data->id;
          $item->save();
          # .................................................
          $amount                 = ($request->credit[$key] -  $request->debit[$key]);
          $state                  = ($amount > 0) ?'credit':'debit';
          # effect account
          $credit_data = [
                'amount'                => round(abs($amount),2),
                'account_id'            => $item->account_id,
                'type'                  => $state,
                'sub_type'              => 'deposit',
                'operation_date'        => $this->productUtil->uf_date($request->date, true),
                'created_by'            => session()->get('user.id'),
                'note'                  => $item->text??trans('home.Daily Payment'),
                'daily_payment_item_id' => $item->id,
                'cs_related_id'         => $item->cost_center_id
            ];
           $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
           $account = \App\Account::find($item->account_id);
           if($account->cost_center!=1){
                \App\AccountTransaction::nextRecords($account->id,$account->business_id,$credit->operation_date);
           }
           if($item->cost_center_id != null){
                #  cost_center_id
                $credit_data_ = [
                    'amount'                =>  round(abs($amount),2),
                    'account_id'            => $item->cost_center_id,
                    'type'                  => $state,
                    'sub_type'              => 'deposit',
                    'operation_date'        => $this->productUtil->uf_date($request->date, true),
                    'created_by'            => session()->get('user.id'),
                    'note'                  => $item->text??trans('home.Daily Payment'),
                    'daily_payment_item_id' => $item->id,
                    'id_delete'             => $item->id
                ];
                $credit_ = \App\AccountTransaction::createAccountTransaction($credit_data_);
            }
        } 
        $type = "journalV";
        \App\Models\Entry::create_entries($data,$type);
        \DB::commit();
       return redirect('daily-payment')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function edit($id)
    {
        if (!auth()->user()->can('daily_payment.update')) {
            abort(403, 'Unauthorized action.');
        }
        $accounts           =  Account::items();
        $data               =  DailyPayment::find($id);
        $amount             =  $data->items->sum('credit');
        $debit              =  $data->items->sum('debit');
        $costs              =  Account::cost_centers();
        $currency           =  \App\Models\ExchangeRate::where("source","!=",1)->get();
        $currencies         = [];
        foreach($currency as $i){ $currencies[$i->currency->id] = $i->currency->country . " " . $i->currency->currency . " ( " . $i->currency->code . " )"; }
        return view('daily_payments.edit')
                 ->with('accounts',$accounts)
                 ->with('data',$data)
                 ->with('costs',$costs)
                 ->with('amount',$amount)
                 ->with('debit',$debit)
                 ->with('currencies',$currencies)
                 ->with('title',trans('home.Daily Payment'))
                            ;
    } 
    public function post_edit(Request $request,$id)
    {
        if (!auth()->user()->can('daily_payment.update')) {
            abort(403, 'Unauthorized action.');
        }
        \DB::beginTransaction();
        $business_id  =  request()->session()->get('user.business_id');
        $data         =  DailyPayment::find($id) ;
        $entry        =  \App\Models\Entry::where("journal_voucher_id",$id)->first();
        $data->amount =  round($request->total_credit,2);
        $data->date   =  $this->productUtil->uf_date($request->date, true);
        $data->currency_id     =  $request->currency_id;
        $data->exchange_price  =  $request->currency_id_amount;
        # ................................................
        $company_name      = request()->session()->get("user_main.domain");
        $old_document    =  $data->document;
        $referencesNewStyle = str_replace('/', '-', $data->ref_no);
        if($old_document == null){ $old_document = []; }
        if ($request->hasFile('document_expense')) {  $id_s = 1;
            foreach ($request->file('document_expense') as $file) {
                #................
                if(!in_array($file->getClientOriginalExtension(),["jpg","png","jpeg"])){
                    if ($file->getSize() <= config('constants.document_size_limit')){ 
                        $file_name_m    =   time().'_'.$referencesNewStyle.'_'.$id_s++.'_'.$file->getClientOriginalName();
                        $file->move('uploads/companies/'.$company_name.'/documents/journal-voucher',$file_name_m);
                        $file_name =  'uploads/companies/'.$company_name.'/documents/journal-voucher/'. $file_name_m;
                    }
                }else{
                    if ($file->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time().'_'.$referencesNewStyle.'_'.$id_s++.'_'.$file->getClientOriginalName();
                        $Data         = getimagesize($file);
                        $width         = $Data[0];
                        $height        = $Data[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2; 
                        $imgs = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        $file_name =  'uploads/companies/'.$company_name.'/documents/journal-voucher/'. $new_file_name;
                        // if ($imgs->save(public_path("uploads\companies\\$company_name\documents\journal-voucher\\$new_file_name"),20)) {
                        //     $uploaded_file_name = $new_file_name;
                        // }
                        $public_path = public_path('uploads/companies/'.$company_name.'/documents/journal-voucher');
                        if (!file_exists($public_path)) {
                            mkdir($public_path, 0755, true);
                        }
                        if ($imgs->save($public_path ."/" . $new_file_name)) {
                            $uploaded_file_name = $new_file_name;
                        }  
                    }
                }
                #................
                array_push($old_document,$file_name);
            }
        }
        if(json_encode($old_document)!="[]"){ $data->document        = json_encode($old_document) ; }
        # ................................................
        $data->save();

        $ids   = ($request->old_item)??[];
        $Daily = DailyPaymentItem::where('daily_payment_id',$id)->whereNotIn('id',$ids)->get();
        foreach($Daily as $it){
            $i = \App\AccountTransaction::where("daily_payment_item_id",$it->id)->first();
            if($i){
                $account_transaction = $i->account_id;  
                $action_date         = $i->operation_date;  
                $i->delete();
                $account = \App\Account::find($account_transaction); 
                if($account->cost_center!=1){
                    \App\AccountTransaction::nextRecords($account->id,$account->business_id,$action_date);
               }
            }
            $it->delete();
        }  
        foreach ($ids as $key => $old_id) {
            $item                   =  DailyPaymentItem::find($old_id);
            $item->account_id       =  $request->old_account_id[$key];
            $item->credit           =  round( $request->old_credit[$key] ,2);
            $item->debit            =  round( $request->old_debit[$key] ,2);
            $item->text             =  $request->old_text[$key];
            $item->cost_center_id   =  $request->old_cost_center_id[$key];
            $item->update();
            $amount                 =  ($request->old_credit[$key] -  $request->old_debit[$key]);
            $state                  =  ($amount > 0) ?'credit':'debit';
            # effect account
            $accounts_items         = \App\AccountTransaction::where('daily_payment_item_id',$old_id)->whereHas("account",function($query){
                                                                                    $query->where("cost_center",0);
                                                                            })->get();
            foreach($accounts_items as $itemOne){
                # ................................................. 
                $accountOld                = \App\Account::find($itemOne->account_id);
                $accountNew                = \App\Account::find($request->old_account_id[$key]);
                $dateOld                   = $itemOne->operation_date;  
                $dateNew                   = $this->productUtil->uf_date($request->date, true);
                # .................................................
                $itemOne->amount           = round(abs($amount),2)  ; 
                $itemOne->account_id       = $request->old_account_id[$key]  ; 
                $itemOne->operation_date   = $this->productUtil->uf_date($request->date, true)  ; 
                $itemOne->type             = $state  ; 
                $itemOne->note             = $item->text??trans('home.Daily Payment')  ; 
                $itemOne->cs_related_id    = $item->cost_center_id  ; 
                $itemOne->entry_id         = ($entry)?$entry->id:null  ; 
                $itemOne->update() ;
                # ..................................................
                if($accountOld->cost_center!=1){ \App\AccountTransaction::nextRecords($accountOld->id,$data->business_id,$dateOld); }
                if($accountNew->cost_center!=1){ \App\AccountTransaction::nextRecords($accountNew->id,$data->business_id,$dateNew); } 
            }

            if($request->old_cost_center_id[$key] == null){
                $old_trans      = \App\AccountTransaction::where('daily_payment_item_id',$old_id)->whereHas("account",function($query){
                                                                                                $query->where("cost_center",">",0);
                                                                                            })->first();
                if(!empty($old_trans)){ 
                    $old_trans->delete(); 
                }
            }else{
                \App\AccountTransaction::where('daily_payment_item_id',$old_id)->whereHas("account",function($query){
                        $query->where("cost_center",">",0);
                })->update([
                    'amount'         => round(abs($amount),2),
                    'account_id'     => $request->old_cost_center_id[$key],
                    'type'           => $state,
                    'operation_date' => $this->productUtil->uf_date($request->date, true),
                    'note'           => $item->text??trans('home.Daily Payment'),
                    'entry_id'       => ($entry)?$entry->id:null,
                ]);
            }
        }
        
        if ($request->account_id) {
            foreach ($request->account_id as $key=>$account_id) {
                # ...............................................
                $item                   =  new DailyPaymentItem;
                $item->account_id       =  $account_id;
                $item->credit           =  round($request->credit[$key],2);
                $item->debit            =  round($request->debit[$key],2);
                $item->text             =  $request->text[$key];
                $item->cost_center_id   =  $request->cost_center_id[$key];
                $item->daily_payment_id =  $data->id;
                $item->save();
                # ...............................................
                $amount                 =  ($request->credit[$key] -  $request->debit[$key]);
                $state                  =  ($amount > 0) ?'credit':'debit';
                # effect account
                $credit_data = [
                      'amount'                => round(abs($amount),2),
                      'account_id'            => $item->account_id,
                      'type'                  => $state,
                      'sub_type'              => 'deposit',
                      'operation_date'        => $this->productUtil->uf_date($request->date, true),
                      'created_by'            => session()->get('user.id'),
                      'note'                  => $item->text??trans('home.Daily Payment'),
                      'daily_payment_item_id' => $item->id,
                      'cs_related_id'         => $item->cost_center_id,
                      'entry_id'              => ($entry)?$entry->id:null,
                ];
                $credit  = \App\AccountTransaction::createAccountTransaction($credit_data);
                $account = \App\Account::find($item->account_id);
                if($account->cost_center!=1){ \App\AccountTransaction::nextRecords($account->id,$data->business_id,$this->productUtil->uf_date($request->date, true)); }
             } 
        }
        \DB::commit();
        return redirect('daily-payment')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function delete($id)
    {
        if (!auth()->user()->can('daily_payment.delete')) {
            abort(403, 'Unauthorized action.');
        }
        foreach (DailyPaymentItem::where('daily_payment_id',$id)->get() as $item) {
            $accountTransactions = \App\AccountTransaction::where('daily_payment_item_id',$item->id)->get();
            foreach($accountTransactions as $o){
                $account_transaction = $o->account_id;
                $actions_date        = $o->operation_date;
                $o->delete();
                $account             =  \App\Account::find($account_transaction);
                if($account->cost_center!=0){
                    \App\AccountTransaction::nextRecords($account->id,$account->business_id,$actions_date);
                }
            }
            $item->delete();
        }
        $data =  DailyPayment::find($id);
        if ($data) {
            $data->delete();
        }
        return back()->with('yes',trans('home.Done Successfully'));
    }

    public function show($id)
    {           
        if (!auth()->user()->can('daily_payment.view')) {
            abort(403, 'Unauthorized action.');
        }
        $data =  DailyPayment::find($id) ;
        return view('daily_payments.show')
                ->with('data',$data);
    }
 
    public function attach($id)
    {
        $data        = DailyPayment::find($id);
        return view("daily_payments.attach")->with(compact("data"));
    }
    
    public function entry($id)
    {
        $ids     =  [];
        $data    =  DailyPayment::find($id);
        $dp      =  DailyPaymentItem::where("daily_payment_id",$data->id)->get();
        foreach($dp as $i){$ids[]=$i->id;}
        $allData =  \App\AccountTransaction::whereIn('daily_payment_item_id',$ids)->whereHas("account",function($query){
                                                                                    $query->where("cost_center",0);
                                                                            })
                                                                ->where('amount','>',0)->get();
        $entry_id = null;
        foreach($allData as $i){
            $entry_id = $i->entry_id;
            break;
        }
        $entry   =  Entry::where("id",$entry_id)->get(); 
        return view('daily_payments.entry')
                   ->with('allData', $allData)
                   ->with('entry', $entry)
                   ->with('data', $data); 
    }
    
    
}
