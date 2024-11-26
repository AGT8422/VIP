<?php

namespace App\Models\FrontEnd\Vouchers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\ProductUtil;
use App\Models\FrontEnd\Utils\GlobalUtil;
class ExpenseVoucher extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END BRAND *** // 
    // **1** ALL BRAND
    public static function getExpenseVoucher($user,$filter) {
        try{
            $list            = [];
            $business_id     = $user->business_id;
            if($filter != null){ 
                $data            = ExpenseVoucher::allData("all",null,$business_id,$filter);
            }else{
                $data            = ExpenseVoucher::allData("all",null,$business_id);
            } 
            if($data == false){ return false;}
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE BRAND
    public static function createExpenseVoucher($user,$data) {
        try{
            $business_id             = $user->business_id;
            $create                  = ExpenseVoucher::requirement($user);
            return $create ;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT BRAND
    public static function editExpenseVoucher($user,$data,$id) {
        try{
            $business_id              = $user->business_id;
            $data                     = ExpenseVoucher::allData(null,$id,$business_id);
            if($data == false){ return false;}
            $require                  = ExpenseVoucher::requirement($user);
            $list["info"]             = $data;
            $list["require"]          = $require;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE BRAND
    public static function storeExpenseVoucher($user,$data,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = ExpenseVoucher::createNewExpenseVoucher($user,$data,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE BRAND
    public static function updateExpenseVoucher($user,$data,$id,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = ExpenseVoucher::updateOldExpenseVoucher($user,$data,$id,$request);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE BRAND
    public static function deleteExpenseVoucher($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id      = $user->business_id;
            $expenseVoucher   = \App\Models\GournalVoucher::find($id);
            if(!$expenseVoucher){ return false; }
            foreach (\App\Models\GournalVoucherItem::where('gournal_voucher_id',$id)->get() as $item) {
                \App\AccountTransaction::where('gournal_voucher_item_id',$item->id)->delete();
                $item->delete();
            }
            $expenseVoucher->delete();
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **7** CURRENCY EXPENSE VOUCHER
    public static function currencyExpenseVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $currency    = \App\Models\ExchangeRate::where("id",$id)->first();
            if(!empty($currency)){
                if($currency->right_amount == 0){
                    $amount = $currency->amount;
                }else{
                    $amount = $currency->opposit_amount;
                }
                $symbol = $currency->currency->symbol;
              
            }else{
                $symbol = "";
            }
            $array = [];
            $array["amount"] = $amount;
            $array["symbol"] = $symbol;
            \DB::commit();
            return $array;
        }catch(Exception $e){
            return false;
        }
    }
    // **8** VIEW EXPENSE VOUCHER
    public static function viewExpenseVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $JournalVoucher     = ExpenseVoucher::allData(null,$id,$business_id);
            if($JournalVoucher  == false){ return false; } 
            \DB::commit();
            return $JournalVoucher;
        }catch(Exception $e){
            return false;
        }
    }
    // **9** PRINT EXPENSE VOUCHER
    public static function printExpenseVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $expenseVoucher     = \App\Models\GournalVoucher::find($id);
            if(empty($expenseVoucher)){ return false; }
            $expenseVoucher     =  \URL::to('reports/ex-vh/'.$expenseVoucher->id)   ; 
            \DB::commit();
            return $expenseVoucher;
        }catch(Exception $e){
            return false;
        }
    }
    // **10** ATTACH EXPENSE VOUCHER
    public static function attachExpenseVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $list_of_attach       =  []; 
            $business_id          =  $user->business_id;
            // ..................................................................1........
            $expenseVoucher              =  \App\Models\GournalVoucher::find($id);
            // ..................................................................2........
            $attach     =  isset($expenseVoucher->document)?$expenseVoucher->document:null ;
            if($attach != null){
                foreach($attach as $doc){
                    $list_of_attach[]  =  \URL::to($doc);
                } 
            }
            \DB::commit();
            return $list_of_attach;
        }catch(Exception $e){
            return false;
        }
    }
    // **11** ENTRY EXPENSE  VOUCHER
    public static function entryExpenseVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $list_of_entry        =  [];$list_of_entry2        =  [];
            $line                 =  [];$line2                 =  [];
            $dataJ                 =  \App\Models\GournalVoucherItem::where("gournal_voucher_id",$id)->pluck("id");
        
            $business_id          =  $user->business_id;
            // ..................................................................1........
            $entry_id             =  \App\AccountTransaction::whereIn('gournal_voucher_item_id',$dataJ)->whereNull("id_delete")->where('amount','>',0)->groupBy("entry_id")->pluck("entry_id");
           
            $entry                =  \App\Models\Entry::where("id",$entry_id)->get();
            foreach($entry as $items){
                $list_of_entry["id"]               = $items->id;
                $list_of_entry["entry_reference"]  = $items->refe_no_e;
                $list_of_entry["source_reference"] = $items->ref_no_e;
                $line[]             = $list_of_entry;
            } 
            // ..................................................................2........
            $allData              =  \App\AccountTransaction::whereIn('gournal_voucher_item_id',$dataJ)->whereHas("account",function($query){
                                                                            $query->where("cost_center",0);
                                                    })->where('amount','>',0)->orWhere('gournal_voucher_id',$id)->get();
            $debit  = 0 ; $credit = 0 ;
            foreach($allData as $items){
                $list_of_entry2["id"]                     = $items->id;
                $list_of_entry2["account_id"]             = ($items->account != null)?$items->account->name . " | " . $items->account->account_number:"" ;
                $list_of_entry2["type"]                   = $items->type ;
                $debit                                   += ($items->type == "debit")?$items->amount:0;
                $credit                                  += ($items->type == "credit")?$items->amount:0;
                $list_of_entry2["amount"]                 = $items->amount ;
                $list_of_entry2["operation_date"]         = $items->operation_date->format("Y-m-d") ;
                // $list_of_entry2["transaction_id"]         = $items->transaction_id ;
                // $list_of_entry2["transaction_payment_id"] = $items->transaction_payment_id ;
                $list_of_entry2["gournal_voucher_item_id"]= $items->gournal_voucher_item_id ;
                $list_of_entry2["note"]                   = $items->note ;
                $list_of_entry2["entry_id"]               = $items->entry_id ;
                if($items->cs_related_id != null){
                    $cost_center = \App\Account::find($items->cs_related_id);
                    $list_of_entry2["cost_center"]        = $cost_center->name;
                }else{
                    $list_of_entry2["cost_center"]        = "";
                    
                }
                // $list_of_entry2["created_by"]     = $items-> ;
                $line2[]              = $list_of_entry2;
                } 
            
            $array["entry"]       =  $line;
            $array["allData"]     =  $line2;
            // $array["data"]        =  $data;
            $array["balance"]     =  [
                "total_credit" => $credit ,
                "total_debit"  => $debit,
                "balance"      => (($debit - $credit) != 0)?false:true,

            ];
            \DB::commit();
            return $array;
        }catch(Exception $e){
            return false;
        }
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE BRAND
    public static function createNewExpenseVoucher($user,$data,$request) {
       try{
            $productUtil      = new ProductUtil();
            $business_id      = $user->business_id;
            $document_expense = [];
            // ................................................
            if ($request->hasFile('document_expense')) {
                $count_doc1 = 1;
                foreach ($request->file('document_expense') as $file) {
                    $file_name   =  'public/uploads/documents/'.time().'.'.$count_doc1++.'.'.$file->getClientOriginalExtension();
                        // ...........................
                        $data_sized  = getimagesize($file);
                        $width       = $data_sized[0];
                        $height      = $data_sized[1];
                        $half_width  = $width/2;
                        $half_height = $height/2;
                        $img_sized   = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        if ($img_sized->save(base_path($file_name),20)) {
                            $uploaded_file_name        = $file_name;
                            array_push($document_expense,$uploaded_file_name);
                        }
                }
            }
            // ................................................
            $data                     =  new \App\Models\GournalVoucher();
            $ref_count                =  $productUtil->setAndGetReferenceCount("gouranl_voucher",$business_id);
            $ref_no                   =  $productUtil->generateReferenceNumber("gouranl_voucher" , $ref_count,$business_id);
            $setting                  =  \App\Models\SystemAccount::where('business_id',$business_id)->first();
            $data->date               =  $request->gournal_date;
            $data->main_account_id    =  $request->main_account_id;
            $data->cost_center_id     =  $request->cost_center_id;
            $data->currency_id        =  $request->currency_id;
            $data->exchange_price     =  $request->currency_id_amount;
            $data->ref_no             =  $ref_no;
            if($request->main_credit != null){
                $data->main_credit    =  1;
                $data->total_credit   =  $request->total_credit ;
            }
            $data->business_id        =  $business_id;
            $data->document           = json_encode($document_expense) ;
            $data->save();

            $net = 0;
            foreach ($request->amount as $key=>$amount) { 
            
                $item                       =  new \App\Models\GournalVoucherItem;
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
                if($request->main_credit != null){
                    GlobalUtil::effect_debit_total($item->id,$user,null);
                }else{
                    GlobalUtil::effect_account($item->id,$user,null,null);
                }
                GlobalUtil::effect_cost_center($item,$user,null);
                $net  += ($item->amount - $item->tax_amount);
            
            } 

            if($request->main_credit != null){
                GlobalUtil::effect_account_total($request->total_credit,$request->main_account_id,$item->id,$request->note_main,$user,null);
            }
            $type="journalEx";
            \App\Models\Entry::create_entries($item,$type,null,null,$data->id); 
              
             
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE BRAND
    public static function updateOldExpenseVoucher($user,$data,$id,$request) {
       try{
            $access                = 0;
            $business_id           =  $user->business_id;
            $setting               =  \App\Models\SystemAccount::where('business_id',$business_id)->first();
            $data                  =  \App\Models\GournalVoucher::find($id);
            $entry                 =  \App\Models\Entry::where("expense_voucher_id",$id)->select()->first(); 
            $old_status            =  $data->main_credit;
            $old_account_main      =  $data->main_account_id;
            $note_main             =  $request->note_main;
            $data->date            =  $request->gournal_date;            
            $data->main_account_id =  $request->main_account_id;
            $data->cost_center_id  =  $request->cost_center_id;
            $data->currency_id     =  $request->currency_id;
            $data->exchange_price  =  $request->currency_id_amount;
            $old_document          =  $data->document;
            if($old_document == null){
                $old_document = [];
            }
            if ($request->hasFile('document_expense')) {
                $count_doc2 = 1;
                foreach ($request->file('document_expense') as $file) {
                    $file_name   =  'public/uploads/documents/'.time().'.'.$count_doc2++.'.'.$file->getClientOriginalExtension();
                    // ...........................
                    $data_sized  = getimagesize($file);
                    $width       = $data_sized[0];
                    $height      = $data_sized[1];
                    $half_width  = $width/2;
                    $half_height = $height/2;
                    $img_sized   = \Image::make($file)->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                    if ($img_sized->save(base_path($file_name),20)) {
                        $uploaded_file_name        = $file_name;
                        array_push($old_document,$uploaded_file_name);
                    }
                    
                }
            }
            if($request->main_credit != null &&  $request->main_credit != 0){
                $data->total_credit   =  $request->total_credit ;
                $data->main_credit    =  $request->main_credit;
            }else{
                $data->main_credit    =  0;
                $data->total_credit   =  0;
            }
            if(json_encode($old_document)!="[]"){
                $data->document           = json_encode($old_document) ;
            }
            $data->update();
            // ..0........ delete rows
            $ids    =  $request->item_id??[];
            $revs   =  \App\Models\GournalVoucherItem::where('gournal_voucher_id',$id)->whereNotIn('id',$ids)->get();
            foreach ($revs as $key => $rev) {
                foreach(\App\AccountTransaction::where('gournal_voucher_item_id',$rev->id)->get() as $ii){
                    $ii->delete();
                }
                $rev->delete();
            }
            // .1....... old item rows
            $net    = 0;
            if ($request->item_id) {
                foreach ($request->item_id as $key => $old_id) {
                    $item                       =  \App\Models\GournalVoucherItem::find($old_id);
                    $old_credit                 =  $item->credit_account_id;
                    $old_debit                  =  $item->debit_account_id;
                    $old_tax                    =  $item->tax_account_id ;
                    //old
                    if($request->main_credit != null){
                        $item->credit_account_id    =  $request->main_account_id;
                    }else{
                        $item->credit_account_id    =  $request->old_credit_account_id[$key]??$request->main_account_id;
                    }
                    $item->debit_account_id     =  $request->old_debit_account_id[$key];
                    $item->amount               =  $request->old_amount[$key];
                    $item->text                 =  $request->old_text[$key];
                    $item->tax_percentage       =  $request->old_tax_percentage[$key];
                    $item->tax_amount           =  $request->old_tax_amount[$key];
                    $item->text                 =  $request->old_text[$key];
                    $item->date                 =  ($request->old_date[$key]  != null)?$request->old_date[$key]:$request->gournal_date ;
                    $item->cost_center_id       =  $request->old_center_id[$key]??$request->cost_center_id;
                    $item->update();
                     
                    $net                       += ($item->amount - $item->tax_amount);
                    if($request->main_credit != null && $request->main_credit != 0){
                        GlobalUtil::edit_effect_accounts($item,$request->total_credit,$old_debit,$old_tax,$user,$old_status,$entry);
                    }else{
                        GlobalUtil::edit_effect($item,$old_credit,$old_debit,$old_tax,$user,$old_account_main,$old_status,$entry);
                    }
                    GlobalUtil::effect_cost_center($item,$user,$entry);
                }
                if($request->main_credit != null && $request->main_credit != 0){
                    $access = 1;
                    GlobalUtil::edit_effect_main($item,$request->total_credit,$request->main_account_id,$request->note_main,$user,$old_account_main,$old_status,$note_main,$entry);
                }
            }
            // .2....... Add New Rows
            if ($request->amount) {
                foreach ($request->amount as $key=>$account_id) { 
                    $item                       =  new \App\Models\GournalVoucherItem;
                    if($request->main_credit != null){
                        $item->credit_account_id    =  $request->main_account_id;
                    }else{
                        $item->credit_account_id    =  $request->credit_account_id[$key]??$request->main_account_id;
                    }
                    $item->debit_account_id     =  $request->debit_account_id[$key];
                    $item->tax_account_id       =  $setting->journal_expense_tax;
                    $item->amount               =  $request->amount[$key];
                    $item->text                 =  $request->text[$key];
                    $item->tax_percentage       =  $request->tax_percentage[$key];
                    $item->tax_amount           =  $request->tax_amount[$key];
                    $item->text                 =  $request->text[$key];
                    $item->date                 =  $request->gournal_date ;
                    $item->cost_center_id       =  $request->center_id[$key]??$request->cost_center_id;
                    $item->gournal_voucher_id   =  $data->id;
                    $item->save();
                    $net                       += ($item->amount - $item->tax_amount);
                    if($request->main_credit != null  && $request->main_credit != 0){
                        GlobalUtil::effect_debit_total($item->id,$user,$entry);
                    }else{
                        GlobalUtil::effect_account($item->id,$user,null,$entry);
                    }
                    GlobalUtil::effect_cost_center($item,$user,$entry);
                } 
                if($old_status == 0){
                    if($access==0){
                        if($request->main_credit != null  && $request->main_credit != 0){
                            GlobalUtil::effect_account_total($request->total_credit,$request->main_account_id,$item->id,$request->note_main,$user,$entry);
                        }
                    }
                }else{
                    if($access==0){
                        if($request->main_credit != null  && $request->main_credit != 0){
                            GlobalUtil::effect_account_total($request->total_credit,$request->main_account_id,$item->id,$request->note_main,$user,$entry);
                        }
                    }else{
                        if($request->main_credit != null  && $request->main_credit != 0){
                            GlobalUtil::edit_effect_main($item,$request->total_credit,$request->main_account_id,$request->note_main,$user,$old_account_main,$old_status,$note_main,$entry);
                        }else{
                            $trans      =  \App\AccountTransaction::where('gournal_voucher_id',$id)
                            ->where('account_id',$old_account_main)
                            ->first();
                            
                            if($trans){
                                $trans->delete();
                            }    
                        }
                    }
                }
            }
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **3** GET EXPENSE VOUCHER
    public static function allData($type=null,$id=null,$business_id,$filter=null) {
        try{
            $list   = [];
            if($type != null){
                if($filter!=null){
                    $jourVoucher     = \App\Models\GournalVoucher::where("business_id",$business_id)->orderBy('id','desc');
                    if($filter["startDate"] != null){
                        $jourVoucher->whereDate("date",">=",$filter["startDate"]);
                    }
                    if($filter["endDate"] != null){
                        $jourVoucher->whereDate("date","<=",$filter["endDate"]);
                    }
                     if($filter["month"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["month"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["month"])->format('Y');
                        $startD  = $y."-".$m."-01";
                       
                        $jourVoucher->whereDate("date","<=",$filter["month"]); 
                        $jourVoucher->whereDate("date",">=",$startD); 
                    }
                    if($filter["day"] != null){
                        $jourVoucher->whereDate("date","=",$filter["day"]); 
                    }
                    if($filter["year"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["year"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["year"])->format('Y');
                        $startD  = $y."-01-01";
                       
                        $jourVoucher->whereDate("date","<=",$filter["year"]); 
                        $jourVoucher->whereDate("date",">=",$startD); 
                    }
                    if($filter["week"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["week"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["week"])->format('Y');
                        $d = \Carbon::createFromFormat('Y-m-d',$filter["week"])->format('d');
                        // list of date with 31 or 30
                        $dayOf31 = [1,3,5,7,9,10,12] ;
                        $dayOf30 = [2,4,6,8,11] ;
                        // for day of week
                        $d = $d - 7;
                        if($d < 0){
                             if((intVal($m) - 1)<0){
                                $y = (intVal($y) - 1);
                                $m = abs((intVal($m) - 1)%12);
                                 
                             }elseif((intVal($m) - 1)==0){
                                $y = intVal($y)-1;
                                $m = (((intVal($m) - 1) % 12)==0)?12:abs((intVal($m) - 1) % 12);
                                 
                             }else{
                                $m =  (intVal($m) - 1);
                                 
                             }
                            if(in_array(intVal($m),$dayOf31)){
                                $d = 31 - abs($d);
                            }else{
                                if(intVal($m) == 2){
                                    // Leap Years 1800 - 2400
                                    $mod = substr($y,3)%4;
                                    $numberOfDay = ($mod == 0)?29:28;
                                    $d = $numberOfDay - abs($d);
                                }else{
                                    $d = 30 - abs($d);
                                }
                            }
                        }elseif($d == 0){
                            if((intVal($m) - 1)<0){
                                $y = (intVal($y) - 1);
                                $m = abs((intVal($m) - 1)%12);
                             }elseif((intVal($m) - 1)==0){
                                $y = intVal($y)-1;
                                $m = (((intVal($m) - 1) % 12)==0)?12:abs((intVal($m) - 1) % 12);
                             }else{
                                $m =  (intVal($m) - 1);
                             }
                            if(in_array(intVal($m),$dayOf31)){
                                $d = 31;
                            }else{ 
                                 if(intVal($m) == 2){
                                     // Leap Years 1800 - 2400
                                    $mod = substr($y,3)%4;
                                    $numberOfDay = ($mod == 0)?29:28;
                                    $d = $numberOfDay - abs($d);
                                }else{
                                    $d = 30 - abs($d);
                                }
                            }
                        } 
                        $startD  = $y."-".$m."-".$d;
                        
                        $jourVoucher->whereDate("date","<=",$filter["week"]); 
                        $jourVoucher->whereDate("date",">=",$startD); 
                    }
                    $jourVoucher     = $jourVoucher->get();
                }else{
                    $jourVoucher     = \App\Models\GournalVoucher::where("business_id",$business_id)->orderBy('id','desc')->get();
                }
                if(count($jourVoucher) == 0 ){ return false; }
                foreach($jourVoucher as $ie){
                    $items                    = \App\Models\GournalVoucherItem::where("gournal_voucher_id",$ie->id)->get();
                    $lines                    = [];$total_amount=0;
                    foreach($items as $li) {
                        $lines[] = [
                            "id"                    =>  $li->id,
                            "debit_account_id"      =>  $li->debit_account_id,
                            "debitAccountName"      =>  ($li->debit_account)?$li->debit_account->name:'',
                            "credit_account_id"     =>  $li->credit_account_id,
                            "creditAccountName"     =>  ($li->credit_account)?$li->credit_account->name:'',
                            "amount"                =>  $li->amount,
                            "tax_percentage"        =>  $li->tax_percentage,
                            "tax_amount"            =>  $li->tax_amount,
                            "text"                  =>  $li->text,
                            "date"                  =>  $li->date,
                            "cost_center_id"        =>  $li->cost_center_id,
                            "costCenterName"        =>  ($li->cost_center)?$li->cost_center->name:"",
                        ];
                        $total_amount += $li->amount; 
                    }
                    $list_attach              = [];
                    foreach($ie->document as $doc){
                       $list_attach[]  = \URL::to($doc);
                    }
                    $list[] = [
                        "id"                  => $ie->id,
                        "ref_no"              => $ie->ref_no,
                        "document"            => $list_attach,
                        "date"                => $ie->date,
                        "total"               => $total_amount,
                        "main_account_id"     => $ie->main_account_id,
                        "mainAccountName"     => ($ie->account)?$ie->account->name:'',
                        "cost_center_id"      => $ie->cost_center_id,
                        "costCenterName"      => ($ie->cost_center)?$ie->cost_center->name:'',
                        "currency_id"         => $ie->currency_id,
                        "amount_in_currency"  => $ie->amount_in_currency,
                        "exchange_price"      => $ie->exchange_price,
                        "main_credit"         => $ie->main_credit,
                        "total_credit"        => $ie->total_credit,
                        "items"               => $lines,
                    ];
                }
            }else{
                $journalVoucher           = \App\Models\GournalVoucher::find($id);
                if(empty($journalVoucher)){ return false; }
                $items                    = \App\Models\GournalVoucherItem::where("gournal_voucher_id",$journalVoucher->id)->get();
                $lines                    = [];$total_amount=0;
                foreach($items as $li) {
                    $lines[] = [
                        "id"                    =>  $li->id,
                        "debit_account_id"      =>  $li->debit_account_id,
                        "debitAccountName"      =>  ($li->debit_account)?$li->debit_account->name:'',
                        "credit_account_id"     =>  $li->credit_account_id,
                        "creditAccountName"     =>  ($li->credit_account)?$li->credit_account->name:'',
                        "amount"                =>  $li->amount,
                        "tax_percentage"        =>  $li->tax_percentage,
                        "tax_amount"            =>  $li->tax_amount,
                        "text"                  =>  $li->text,
                        "date"                  =>  $li->date,
                        "cost_center_id"        =>  $li->cost_center_id,
                        "costCenterName"        =>  ($li->cost_center)?$li->cost_center->name:"",
                        
                    ];
                    $total_amount += $li->amount; 
                }
                $list_attach              = [];
                foreach($journalVoucher->document as $doc){
                   $list_attach[]  = \URL::to($doc);
                }
                $list[] = [
                    "id"                  => $journalVoucher->id,
                    "ref_no"              => $journalVoucher->ref_no,
                    "total"               => $total_amount,
                    "date"                => $journalVoucher->date,
                    "main_account_id"     => $journalVoucher->main_account_id,
                    "mainAccountName"     => ($journalVoucher->account)?$journalVoucher->account->name:'',
                    "cost_center_id"      => $journalVoucher->cost_center_id,
                    "costCenterName"      => ($journalVoucher->cost_center)?$journalVoucher->cost_center->name:'',
                    "document"            => $list_attach,
                    "currency_id"         => $journalVoucher->currency_id,
                    "amount_in_currency"  => $journalVoucher->amount_in_currency,
                    "exchange_price"      => $journalVoucher->exchange_price,
                    "main_credit"         => $journalVoucher->main_credit,
                    "total_credit"        => $journalVoucher->total_credit,
                    "items"               => $lines,
                ];
            }
            return $list; 
        }catch(Exception $e){
            return false;
        }
    }
    // **4** REQUIREMENT
    public static function requirement($user) {
        try{
            $list                 = [];   
            $allData              = [];  $accounts    = [];
            $currency             = [];  $contacts    = [];$cost_center    = [];
            $allContact           = \App\Account::where("business_id",$user->business_id)->get();
            foreach( $allContact as $item){
                $contacts[$item->id] = $item->account_number . " | " . $item->name; 
            }   
            $allCostCenter           = \App\Account::where("business_id",$user->business_id)->where("cost_center",1)->get();
            foreach( $allCostCenter as $item){
                $cost_center[$item->id] = $item->name; 
            }   
            $amount = 1;
            
            $allCurrency           = \App\Models\ExchangeRate::where("business_id",$user->business_id)->get();
            foreach( $allCurrency as $item){
                $currency_set      = \App\Models\ExchangeRate::where("id",$item->id)->first();
                if(!empty($currency_set)){
                    if($currency_set->right_amount == 0){
                        $amount = $currency_set->amount;
                    }else{
                        $amount = $currency_set->opposit_amount;
                    }
                    $symbol = $currency_set->currency->symbol;
                  
                }else{
                    $symbol = "";
                }
                $currency[] = [
                    "id"        => $item->id,
                    "value"     => $item->currency->currency . " | " . $item->currency->symbol,
                    "amount"    => $amount,
                    ] ; 
            }   
            foreach( $allCurrency as $item){ 
                $currency[] = [
                    "id"        => $item->id,
                    "value"     => $item->currency->currency . " | " . $item->currency->symbol,
                    "amount"    => $amount,
                    ] ; 
            }   
            $allAccount                  = \App\Account::accounts($user->business_id);
            $allData["accounts_credit"]  = GlobalUtil::arrayToObject(\App\Account::main('cash',$user->business_id,'bank'));
            $allData["accounts_debit"]   = GlobalUtil::arrayToObject(\App\Account::main('Expenses',$user->business_id));
            $allData["cost_center"]      = GlobalUtil::arrayToObject($cost_center);
            $allData["currency"]         = $currency;
            
            return $allData; 
        }catch(Exception $e){
            return false; 
        }
    }
}
