<?php

namespace App\Models\FrontEnd\Vouchers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\ProductUtil;
use App\Models\FrontEnd\Utils\GlobalUtil;
class JournalVoucher extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END JOURNAL VOUCHER *** // 
    // **1** ALL JOURNAL VOUCHER
    public static function getJournalVoucher($user,$filter) {
        try{
            $business_id       = $user->business_id;     
            $data              = JournalVoucher::allData("all",null,$business_id,$filter);
            if($data == false){ return false;}
            return $data;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE JOURNAL VOUCHER
    public static function createJournalVoucher($user,$data) {
        try{
            $business_id             = $user->business_id;
            $create                  = JournalVoucher::requirement($user);
            return $create;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** EDIT JOURNAL VOUCHER
    public static function editJournalVoucher($user,$data,$id) {
        try{
            $business_id              = $user->business_id;
            $data                     = JournalVoucher::allData(null,$id,$business_id);
            $require                  = JournalVoucher::requirement($user);
            if($data  == false){ return false; }
            $list["info"]             = $data;
            $list["require"]          = $require;
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **4** STORE JOURNAL VOUCHER
    public static function storeJournalVoucher($user,$data,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = JournalVoucher::createNewJournalVoucher($user,$data,$request);
            if($output == false){ return false; } 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **5** UPDATE JOURNAL VOUCHER
    public static function updateJournalVoucher($user,$data,$id,$request) {
        try{
            \DB::beginTransaction();
            $business_id         = $user->business_id;
            $output              = JournalVoucher::updateOldJournalVoucher($user,$data,$id,$request);
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** DELETE JOURNAL VOUCHER
    public static function deleteJournalVoucher($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id    = $user->business_id;
            $dailyPayment   = \App\Models\DailyPayment::find($id);
            if(!$dailyPayment){ return false; }
            foreach (\App\Models\DailyPaymentItem::where('daily_payment_id',$id)->get() as $item) {
                foreach(\App\AccountTransaction::where('daily_payment_item_id',$item->id)->get() as $ii){
                    $ii->delete();
                }
                $item->delete();
            }
            if($dailyPayment) {
                $dailyPayment->delete();
            }
            \DB::commit();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **7** CURRENCY JOURNAL VOUCHER
    public static function currencyJournalVoucher($user,$data,$id) {
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
    // **8** VIEW JOURNAL VOUCHER
    public static function viewJournalVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $JournalVoucher     = JournalVoucher::allData(null,$id,$business_id);
            if($JournalVoucher  == false){ return false; } 
            \DB::commit();
            return $JournalVoucher;
        }catch(Exception $e){
            return false;
        }
    }
    // **9** PRINT JOURNAL VOUCHER
    public static function printJournalVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $business_id = $user->business_id;
            $journalVoucher     = \App\Models\DailyPayment::find($id);
            if(empty($journalVoucher)){ return false; }
            $journalVoucher     =  \URL::to('reports/jv-vh/'.$journalVoucher->id)   ; 
            \DB::commit();
            return $journalVoucher;
        }catch(Exception $e){
            return false;
        }
    }
    // **10** ENTRY JOURNAL  VOUCHER
    public static function entryJournalVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $list_of_entry        =  [];$list_of_entry2        =  [];
            $line                 =  [];$line2                 =  [];
            $dataJ                 =  \App\Models\DailyPaymentItem::where("daily_payment_id",$id)->pluck("id");
        
            $business_id          =  $user->business_id;
            // ..................................................................1........
            $entry_id             =  \App\AccountTransaction::whereIn('daily_payment_item_id',$dataJ)->whereNull("id_delete")->where('amount','>',0)->groupBy("entry_id")->pluck("entry_id");
            $entry                =  \App\Models\Entry::where("id",$entry_id)->get();
            foreach($entry as $items){
                $list_of_entry["id"]               = $items->id;
                $list_of_entry["entry_reference"]  = $items->refe_no_e;
                $list_of_entry["source_reference"] = $items->ref_no_e;
                $line[]             = $list_of_entry;
            } 
            // ..................................................................2........
            $allData              =  \App\AccountTransaction::whereIn('daily_payment_item_id',$dataJ)->whereNull("id_delete")->where('amount','>',0)->get();
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
                $list_of_entry2["daily_payment_item_id"]  = $items->daily_payment_item_id ;
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
    // **11** ATTACH JOURNAL VOUCHER
    public static function attachJournalVoucher($user,$data,$id) {
        try{
            \DB::beginTransaction();
            $list_of_attach       =  []; 
            $business_id          =  $user->business_id;
            // ..................................................................1........
            $journalVoucher              =  \App\Models\DailyPayment::find($id);
            // ..................................................................2........
            $attach     =  isset($journalVoucher->document)?$journalVoucher->document:null ;
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

    // ****** MAIN FUNCTIONS 
    // **1** CREATE JOURNAL VOUCHER
    public static function createNewJournalVoucher($user,$data,$request) {
        try{
                $productUtil          = new ProductUtil();
                $business_id          = $user->business_id;
                $ref_count            = $productUtil->setAndGetReferenceCount("daily_payment",$business_id);
                $ref_no               = $productUtil->generateReferenceNumber("daily_payment" , $ref_count,$business_id);
            
                $document_expense     = [];
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
                $data                  =  new \App\Models\DailyPayment();
                $data->amount          =  round($request->total_credit,2);
                $data->date            =  $request->date;
                $data->ref_no          =  $ref_no;
                $data->business_id     =  $business_id;
                $data->currency_id     =  $request->currency_id;
                $data->exchange_price  =  $request->currency_id_amount;
                $data->document        =  json_encode($document_expense) ;
                $data->save();
                foreach ($request->account_id as $key => $account_id) {
                    $item                   =  new \App\Models\DailyPaymentItem;
                    $item->account_id       =  $account_id;
                    $item->credit           =  round($request->credit[$key],2);
                    $item->debit            =  round($request->debit[$key],2);
                    $item->text             =  $request->text[$key];
                    $item->cost_center_id   =  $request->cost_center_id[$key];
                    $item->daily_payment_id =  $data->id;
                    $item->save();
                    
                    $amount  = ($request->credit[$key] -  $request->debit[$key]);
                    $state   = ($amount > 0) ?'credit':'debit';
                    //effect account
                    $credit_data = [
                        'amount'                => round(abs($amount),2),
                        'account_id'            => $item->account_id,
                        'type'                  => $state,
                        'sub_type'              => 'deposit',
                        'operation_date'        => $request->date,
                        'created_by'            => $user->id,
                        'note'                  => $item->text??trans('home.Daily Payment'),
                        'daily_payment_item_id' => $item->id,
                        'cs_related_id'         => $item->cost_center_id
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    if($item->cost_center_id != null){

                        //  cost_center_id
                        $credit_data_ = [
                            'amount'                => round(abs($amount),2),
                            'account_id'            => $item->cost_center_id,
                            'type'                  => $state,
                            'sub_type'              => 'deposit',
                            'operation_date'        => $request->date,
                            'created_by'            => $user->id,
                            'note'                  => $item->text??trans('home.Daily Payment'),
                            'daily_payment_item_id' => $item->id,
                            'id_delete'             => $item->id
                        ];
                        $credit_ = \App\AccountTransaction::createAccountTransaction($credit_data_);
                    }
                } 
                $type = "journalV";
                \App\Models\Entry::create_entries($data,$type);
                $data->save();
        
                return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE JOURNAL VOUCHER
    public static function updateOldJournalVoucher($user,$data,$id,$request) {
       try{
            $business_id      =  $user->business_id;
            $data             =  \App\Models\DailyPayment::find($id);
            $entry_id         = \App\AccountTransaction::whereHas("daily_payment_item",function($query) use($id){
                                        $query->where("daily_payment_id",$id);
                                })->groupBy("entry_id")->pluck("entry_id");
           
            $entry_id              = (!empty($entry_id))?$entry_id[0]:null;                  
            $data->amount          =  round($request->total_credit,2);
            $data->currency_id     =  $request->currency_id;
            $data->exchange_price  =  $request->currency_id_amount;
            $data->date            =  $request->date;
            $old_document          =  $data->document;
            if($old_document == null){
                $old_document = [];
            }

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
                        array_push($old_document,$file_name);
                    }
                }
            }  

            if(json_encode($old_document)!="[]"){
                    $data->document        = json_encode($old_document) ;
            }

            $data->update();
             
            $ids   = ($request->old_item)??[];
            $Daily = \App\Models\DailyPaymentItem::where('daily_payment_id',$id)->whereNotIn('id',$ids)->get();
            foreach($Daily as $it){
                $i = \App\AccountTransaction::where("daily_payment_item_id",$it->id)->first();
                if($i){
                    $i->delete();
                }
                $it->delete();
            }  

            foreach ($ids as $key => $old_id) {
                $item                   =  \App\Models\DailyPaymentItem::find($old_id);
                $item->account_id       =  $request->old_account_id[$key];
                $item->credit           =  round( $request->old_credit[$key] ,2);
                $item->debit            =  round( $request->old_debit[$key] ,2);
                $item->text             =  $request->old_text[$key];
                $item->cost_center_id   =  $request->old_cost_center_id[$key];
                $item->update();
                $amount  =  ($request->old_credit[$key] -  $request->old_debit[$key]);
                $state   =  ($amount > 0) ?'credit':'debit';
                //effect account
                \App\AccountTransaction::where('daily_payment_item_id',$old_id)->whereHas("account",function($query){
                        $query->where("cost_center",0);
                })->update([
                    'amount'         => round(abs($amount),2),
                    'account_id'     => $request->old_account_id[$key],
                    'operation_date' => $request->date,
                    'type'           => $state,
                    'note'           => $item->text??trans('home.Daily Payment'),
                    'cs_related_id'  => $item->cost_center_id

                ]);

                if($request->old_cost_center_id[$key] == null){
                    $old_trans = \App\AccountTransaction::where('daily_payment_item_id',$old_id)->whereHas("account",function($query){
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
                        'operation_date' => $request->date,
                        'note'           => $item->text??trans('home.Daily Payment'),
                    ]);
                }
            }
            
            if ($request->account_id) {
                foreach ($request->account_id as $key=>$account_id) {
                    $item                   =  new \App\Models\DailyPaymentItem;
                    $item->account_id       =  $account_id;
                    $item->credit           =  round($request->credit[$key],2);
                    $item->debit            =  round($request->debit[$key],2);
                    $item->text             =  $request->text[$key];
                    $item->cost_center_id   =  $request->cost_center_id[$key];
                    $item->daily_payment_id =  $data->id;
                    $item->save();
                    $amount  = ($request->credit[$key] -  $request->debit[$key]);
                    $state   =  ($amount > 0) ?'credit':'debit';
                    //effect account
                    $credit_data = [
                        'amount'                => round(abs($amount),2),
                        'account_id'            => $item->account_id,
                        'type'                  => $state,
                        'sub_type'              => 'deposit',
                        'operation_date'        => $request->date,
                        'created_by'            => $user->id,
                        'note'                  => $item->text??trans('home.Daily Payment'),
                        'daily_payment_item_id' => $item->id,
                        'cs_related_id'         => $item->cost_center_id

                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    \App\AccountTransaction::where('daily_payment_item_id',$item->id)->whereHas("account",function($query){
                            $query->where("cost_center",0);
                    })->update([
                        'entry_id'         =>  $entry_id
                         

                    ]);
                } 
            }

            
            return true; 
        }catch(Exception $e){
            return false;
        }
    }

    // **3** GET JOURNAL VOUCHER
    public static function allData($type=null,$id=null,$business_id,$filter=null) {
        try{
            $list   = [];
            if($type != null){
                if($filter!=null){
                    $journalVoucher     = \App\Models\DailyPayment::where("business_id",$business_id)->orderBy("id","desc");
                    if($filter["startDate"] != null){
                        $journalVoucher->whereDate("date",">=",$filter["startDate"]);
                    }
                    if($filter["endDate"] != null){
                        $journalVoucher->whereDate("date","<=",$filter["endDate"]);
                    }
                     if($filter["month"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["month"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["month"])->format('Y');
                        $startD  = $y."-".$m."-01";
                       
                        $journalVoucher->whereDate("date","<=",$filter["month"]); 
                        $journalVoucher->whereDate("date",">=",$startD); 
                    }
                    if($filter["day"] != null){
                        $journalVoucher->whereDate("date","=",$filter["day"]); 
                    }
                    if($filter["year"] != null){
                        $m = \Carbon::createFromFormat('Y-m-d',$filter["year"])->format('m');
                        $y = \Carbon::createFromFormat('Y-m-d',$filter["year"])->format('Y');
                        $startD  = $y."-01-01";
                       
                        $journalVoucher->whereDate("date","<=",$filter["year"]); 
                        $journalVoucher->whereDate("date",">=",$startD); 
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
                        
                        $journalVoucher->whereDate("date","<=",$filter["week"]); 
                        $journalVoucher->whereDate("date",">=",$startD); 
                    }
                    $journalVoucher     = $journalVoucher->get();
                }else{
                    $journalVoucher     = \App\Models\DailyPayment::where("business_id",$business_id)->orderBy("id","desc")->get();
                }
                if(count($journalVoucher) == 0 ){ return false; }
                foreach($journalVoucher as $ie){
                    $items                    = \App\Models\DailyPaymentItem::where("daily_payment_id",$ie->id)->get();
                    $lines                    = [];
                    foreach($items as $li) {
                        $lines[] = [
                            "id"              =>  $li->id,
                            "credit"          =>  $li->credit,
                            "debit"           =>  $li->debit,
                            "account_id"      =>  $li->account_id,
                            "accountName"     =>  ($li->account)? $li->account->account_number . " | " . $li->account->name:"",
                            "text"            =>  ($li->text != NULL)?$li->text:"",
                            "cost_center_id"  =>  $li->cost_center_id,
                            "costCenterName"  =>  ($li->cost_center)?$li->cost_center->name:"",
                        ];
                    }
                    $list_attach              = [];
                    foreach($ie->document as $doc){
                       $list_attach[]  = \URL::to($doc);
                    }
                    $list[] = [
                        "id"                  => $ie->id,
                        "ref_no"              => $ie->ref_no,
                        "amount"              => $ie->amount,
                        "document"            => $list_attach,
                        "date"                => $ie->date,
                        "currency_id"         => $ie->currency_id,
                        "amount_in_currency"  => $ie->amount_in_currency,
                        "exchange_price"      => $ie->exchange_price,
                        "items"               => $lines,
                    ];
                }
            }else{
                $journalVoucher           = \App\Models\DailyPayment::find($id);
                if(empty($journalVoucher)){ return false; }
                $items                    = \App\Models\DailyPaymentItem::where("daily_payment_id",$journalVoucher->id)->get();
                $lines                    = [];
                foreach($items as $li) {
                    $lines[] = [
                        "id"              =>  $li->id,
                        "credit"          =>  $li->credit,
                        "debit"           =>  $li->debit,
                        "account_id"      =>  $li->account_id,
                        "accountName"     =>  ($li->account)? $li->account->account_number . " | " . $li->account->name:"",
                        "text"            =>  ($li->text != NULL)?$li->text:"",
                        "cost_center_id"  =>  $li->cost_center_id,
                        "costCenterName"  =>  ($li->cost_center)?$li->cost_center->name:"",

                    ];
                }
                $list_attach              = [];
                foreach($journalVoucher->document as $doc){
                   $list_attach[]  = \URL::to($doc);
                }
                $list[] = [
                    "id"                  => $journalVoucher->id,
                    "ref_no"              => $journalVoucher->ref_no,
                    "amount"              => $journalVoucher->amount,
                    "document"            => $list_attach,
                    "date"                => $journalVoucher->date,
                    "currency_id"         => $journalVoucher->currency_id,
                    "amount_in_currency"  => $journalVoucher->amount_in_currency,
                    "exchange_price"      => $journalVoucher->exchange_price,
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
            $allCurrency           = \App\Models\ExchangeRate::where("business_id",$user->business_id)->get();
            foreach( $allCurrency as $item){
                $currency[] = [ 
                    "id"     => $item->id,
                    "value"  => $item->currency->currency . " | " . $item->currency->symbol,
                    "amount" => $item->amount,
                ]; 
            }
            $allAccount              = \App\Account::accounts($user->business_id);
            // $allData["accounts"]     = GlobalUtil::arrayToObject($allAccount);
            // $allData["contact"]      = GlobalUtil::arrayToObject($contacts);
            $allData["accounts"]     = GlobalUtil::arrayToObject($contacts);
            $allData["currency"]     = $currency;
            $allData["cost_center"]  = GlobalUtil::arrayToObject($cost_center);
            
            return $allData; 
        }catch(Exception $e){
            return false; 
        }
    }

}
