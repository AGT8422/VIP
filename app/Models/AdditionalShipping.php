<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Account;
use App\AccountTransaction;
use Carbon\Carbon;

class AdditionalShipping extends Model
{
    use HasFactory;
    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }
    public static function add_purchase($id,$inputs,$document_expense=NULL,$type=null,$sub_total=null,$purchase_total=null,$shipping_total=null,$id_tr=null,$user=null)
    {
        $data_id = $id;
        if (isset($inputs['shipping_amount'])) { 
            //....................................................... add main table
            //....................................................... ... .. .. .. ...
            $data                 = new AdditionalShipping;
            $data->transaction_id = $id;
            $data->document       = json_encode($document_expense) ;
            $data->t_recieved     = ($id_tr==null)?null:$id_tr ;
            $data->type           = ($type==null)?0:1 ;
            if($type==1){
                $data->sub_total       = $sub_total;
                $data->total_purchase  = $purchase_total;
                $data->total_ship      = $shipping_total;
            }
            if(isset($inputs['currency_id']) && $inputs['currency_id'] != ""){
                $currency_id  =  $inputs['currency_id'] ;
                $currency     = ($inputs['currency_id_amount']>=0)?$inputs['currency_id_amount']:0;
                if(isset($inputs['add_currency_id']) && $inputs['add_currency_id'] != ""){
                    $currency_id  =  $inputs['add_currency_id'] ;
                    $currency     = ($inputs['add_currency_id_amount']>=0)?$inputs['add_currency_id_amount']:0;
                }
                $data->currency_id     = $currency_id;
                $data->exchange_rate   = $currency;
            }
            $data->save();
            $new_ship = \App\Models\ParentArchive::ship_save_parent($data->id,"create");
            
            //........................................................... add children 
            //........................................................... ... .. .. .. ...
            foreach ($inputs['shipping_amount'] as $key=>$amount) {
                $cost_center  = !isset($inputs['cost_center_id'])?null:$inputs['cost_center_id'];
                if ($amount > 0) {
                    $item                 =  new AdditionalShippingItem;
                    $item->contact_id     =  (isset($inputs['shipping_contact_id'][$key]))? $inputs['shipping_contact_id'][$key]:$inputs['contact_id'];
                    $item->cost_center_id =  (isset($inputs['shipping_cost_center_id'][$key]))? $inputs['shipping_cost_center_id'][$key]:$cost_center;
                    $item->account_id     =  $inputs['shipping_account_id'][$key];
                    $item->amount         =  $inputs['shipping_amount'][$key];
                    $item->vat            =  $inputs['shipping_vat'][$key];
                    $item->total          =  $inputs['shipping_total'][$key];
                    $item->text           =  $inputs['shiping_text'][$key];
                    $date_i               =  Carbon::parse($inputs['shiping_date'][$key]);
                    $item->date           =  $date_i->format("Y-m-d h:i:s a");
                    // ..............     currency
                    if(isset($inputs['currency_id']) && $inputs['currency_id'] != ""){
                        $currency_id  =  $inputs['currency_id'] ;
                        $currency     = ($inputs['currency_id_amount'] >= 0)?$inputs['currency_id_amount']:0;
                        if(isset($inputs['add_currency_id']) && $inputs['add_currency_id'] != ""){
                            $currency_id  =  $inputs['add_currency_id'] ;
                            $currency     = ($inputs['add_currency_id_amount']>=0)?$inputs['add_currency_id_amount']:0;
                        }
                        if(isset($inputs['line_currency_id'][$key]) && $inputs['line_currency_id'][$key] != ""){
                            $currency_id  =  $inputs['line_currency_id'][$key] ;
                            $currency     = ($inputs['line_currency_id_amount'][$key]>=0)?$inputs['line_currency_id_amount'][$key]:0;
                        }
                        $item->currency_id     = $currency_id;
                        $item->exchange_rate   = $currency;
                    }
                    //............................................................. .. .. .. . .. . .
                    //........................................................ connect with parent
                    $item->additional_shipping_id =  $data->id;
                    //............................................................. .. .. .. . .. . .
                    $item->save();
                    
                    $new_ship_item = \App\Models\ChildArchive::ship_save_child($item->id,"create");
                    
                    if($item->cost_center_id != null){
                        
                        $cost_center  = null;
                        $cost         = (isset($inputs['shipping_cost_center_id'][$key]))?
                        $inputs['shipping_cost_center_id'][$key]:$inputs['cost_center_id'];
                        
                        $id_delete = \App\Models\AdditionalShippingItem::find($item->id);
                        
                        $trans = \App\Transaction::find($data_id);
                        if($user != null){
                            AccountTransaction::add_shipp_id($cost,$inputs['shipping_amount'][$key],'debit',$trans,'Add Expense',$item->date,$id_delete->id,null,$user);
                        }else{
                            AccountTransaction::add_shipp_id($cost,$inputs['shipping_amount'][$key],'debit',$trans,'Add Expense',$item->date,$id_delete->id);
                        }
                       
                    }
                        
                   
                }
            }
        }
    }
    //.. 1  ..........  update expenses 
    public static function update_purchase($id,$inputs,$document_expense,$type=null,$id_tr=null,$user=null)
    {
        $data_id = $id;
        $check   = ($type == null)?0:1;
        $tr      = \App\Transaction::find($id);
        $data    = AdditionalShipping::where('transaction_id',$id)->where("type",$check)->where("t_recieved",$id_tr)->first();
        # Data info for be ready
        if (empty($data)) {
            
            $data                 = new AdditionalShipping;
            $data->transaction_id = $id;
            $data->document       = json_encode($document_expense) ;
            $data->type           = ($type==null)?0:1 ;
            if(isset($inputs['currency_id']) && $inputs['currency_id'] != ""){
                $currency_id  =  $inputs['currency_id'] ;
                $currency     =  ($inputs['currency_id_amount']>=0)?$inputs['currency_id_amount']:0;
                if(isset($inputs['add_currency_id']) && $inputs['add_currency_id'] != ""){
                    $currency_id  =  $inputs['add_currency_id'] ;
                    $currency     =  ($inputs['add_currency_id_amount']>=0)?$inputs['add_currency_id_amount']:0;
                }
                $data->currency_id     = $currency_id;
                $data->exchange_rate   = $currency;
            }
            $data->t_recieved     = ($id_tr==null)?null:$id_tr ;
            $data->save();
            $new_ship             = \App\Models\ParentArchive::ship_save_parent($data->id,"create");

        }else{
            $old_data             = $data->replicate();
            $new_ship             = \App\Models\ParentArchive::ship_save_parent($data->id,"Edit");
            $data->document       = json_encode($document_expense) ;
            $data->t_recieved     = ($id_tr==null)?null:$id_tr ;
            $data->save();

        }
        # Delete all item closed ( exist balance refresh )
        $ids      =  $inputs['additional_shipping_item_id']??[];
        $re_items =  AdditionalShippingItem::whereHas('additional_shipping', function($query) use($data,$id_tr){ $query->where("id",$data->id); $query->where("t_recieved",$id_tr); })->whereNotIn('id',$ids)->get();
        foreach($re_items as $re_item){
            $lLine = \App\AccountTransaction::where('additional_shipping_item_id',$re_item->id)->get();
            foreach($lLine as $i){
                \App\AccountTransaction::nextRecords($i->account_id,$tr->business_id,$tr->transaction_date);
                $i->delete();
            }
            $re_item->delete(); 
        }
        # Update All Old Items ( exist balance refresh )
        foreach ($ids as $key => $id) {

            $item             =  AdditionalShippingItem::find($id);
            $old_items        =  $item->replicate();
            $new_ship_item    =  \App\Models\ChildArchive::ship_save_child($item->id,"edit",$new_ship);
            $old_cost_center  =  $item->cost_center_id;
            $old_account_id   =  $item->account_id;
 
            if(isset($inputs['cost_center_id']) || isset($inputs['old_shipping_cost_center_id'])){
                
                $co   = (isset($inputs['cost_center_id']))?$inputs['cost_center_id']:null;
                //... cost center ...............................
                $cost = (isset($inputs['old_shipping_cost_center_id'][$key]))?$inputs['old_shipping_cost_center_id'][$key]:$co;
                
                if ( $cost == null ) {

                    if(isset($inputs['cost_center_id'])){
                        AccountTransaction::where('transaction_id',$data_id)
                                            ->whereHas('account',function($query){
                                                $query->where('cost_center',1);
                                            })
                                            ->whereHas("additional_shipping_item",function($query)  use ($check,$id_tr){
                                                $query->whereHas("additional_shipping",function($qy) use ($check,$id_tr){
                                                    $qy->where("type",$check);
                                                    $qy->where("t_recieved",$id_tr);
                                                });
                                            })->where("account_id",$old_cost_center)
                                            ->where("id_delete",$item->id)
                                            ->delete();
                    }
                }else if ( $old_cost_center == $cost ) {
                   
                    
                    $cost_center  = AccountTransaction::where('transaction_id',$data_id)
                                                            ->whereHas('account',function($query){
                                                                    $query->where('cost_center',1);
                                                            })->whereHas("additional_shipping_item",function($query)  use ($check,$id_tr){
                                                                $query->whereHas("additional_shipping",function($qy) use ($check,$id_tr){
                                                                        $qy->where("type",$check);
                                                                        $qy->where("t_recieved",$id_tr);
                                                                });
                                                            })->where("additional_shipping_item_id",$item->id)
                                                            ->where("account_id",$cost)
                                                            ->first();
                    if(empty($cost_center)){
                      
                        $id_delete = \App\Models\AdditionalShippingItem::find($item->id);
                        $trans     = \App\Transaction::find($data_id);
                        if($user != null){
                            AccountTransaction::add_shipp_id($cost,$inputs['old_shipping_amount'][$key],'debit',$trans,'Add Expense',$item->date,$id_delete->id,null,$user);
                        }else{
                            AccountTransaction::add_shipp_id($cost,$inputs['old_shipping_amount'][$key],'debit',$trans,'Add Expense',$item->date,$id_delete->id);
                        }
                    }else{
                         
                        $date_i           = Carbon::parse($cost_center->operation_date);
                        $cost_center->update([
                            'account_id'     => $cost,
                            'amount'         => $inputs['old_shipping_amount'][$key],
                            'operation_date' => $cost_center->operation_date
                        ]); 
                    
                    }
                        
                     
                            
                }else if ( $old_cost_center != $cost ) {
                   

                    if( $old_cost_center != null){
                        AccountTransaction::where('transaction_id',$data_id)
                                                ->whereHas('account',function($query){
                                                    $query->where('cost_center',1);
                                                })->whereHas("additional_shipping_item",function($query)  use ($check,$id_tr){
                                                    $query->whereHas("additional_shipping",function($qy) use ($check,$id_tr){
                                                            $qy->where("type",$check);
                                                            $qy->where("t_recieved",$id_tr);
                                                    });
                                                })->where("account_id",$old_cost_center)
                                                ->where("additional_shipping_item_id",$item->id)
                                                ->delete();
                        
                    }

                    $id_delete = \App\Models\AdditionalShippingItem::find($item->id);
                    $trans     = \App\Transaction::find($data_id);
                    if($user != null){
                        AccountTransaction::add_shipp_id($cost,$inputs['old_shipping_amount'][$key],'debit',$trans,'Add Expense',$item->date,$id_delete->id,null,$user);
                    }else{
                        AccountTransaction::add_shipp_id($cost,$inputs['old_shipping_amount'][$key],'debit',$trans,'Add Expense',$item->date,$id_delete->id);
                    }       
                } 
            }

            //... account ....................................
            if (!$inputs['old_shipping_account_id'][$key]) {
                if( $old_account_id != null ){
                    $account = AccountTransaction::where('transaction_id',$data_id)
                                                        ->where('additional_shipping_item_id',$id)
                                                        ->whereHas("additional_shipping_item",function($query)  use ($check,$id_tr){
                                                            $query->whereHas("additional_shipping",function($qy) use ($check,$id_tr){
                                                                $qy->where("type",$check);
                                                                $qy->where("t_recieved",$id_tr);
                                                            });
                                                        })->where("account_id",$old_account_id)
                                                       ->first();
                    $date_i           = Carbon::parse($account->operation_date);
                    $account->delete();
                    \App\AccountTransaction::nextRecords($old_account_id,$data_id,$date_i->format("Y-m-d"));
                }
            }else if ( $old_account_id == $inputs['old_shipping_account_id'][$key]) {   
                $account = AccountTransaction::where('transaction_id',$data_id)
                                                    ->where('additional_shipping_item_id',$id)
                                                    ->whereHas("additional_shipping_item",function($query)  use ($check,$id_tr){
                                                        $query->whereHas("additional_shipping",function($qy) use ($check,$id_tr){
                                                                $qy->where("type",$check);
                                                                $qy->where("t_recieved",$id_tr);
                                                        });
                                                    })->where("account_id",$old_account_id)
                                                   ->first();
                 
                if($account){
                    $date_i           = Carbon::parse($account->operation_date);
                    $account->update([
                        'amount'         => $inputs['old_shipping_amount'][$key],
                        'operation_date' => $date_i->format("Y-m-d")
                    ]);
                    if($account->account->cost_center!=1){
                        // \App\AccountTransaction::oldBalance($account->id,$account->account_id,$data_id,$date_i->format("Y-m-d"));
                        \App\AccountTransaction::nextRecords($account->account_id,$data_id,$date_i->format("Y-m-d"));
                    }
                }
            }else if ( $old_account_id != $inputs['old_shipping_account_id'][$key]) {
                if($old_account_id != null){
                    $account = AccountTransaction::where('transaction_id',$data_id)
                                                    ->where('additional_shipping_item_id',$id)
                                                    ->whereHas("additional_shipping_item",function($query)  use ($check,$id_tr){
                                                            $query->whereHas("additional_shipping",function($qy) use ($check,$id_tr){
                                                                $qy->where("type",$check);
                                                                $qy->where("t_recieved",$id_tr);
                                                        });
                                                    })->where("account_id",$old_account_id)
                                                    ->first();
                    $date_i           = Carbon::parse($account->operation_date);
                    $account->delete();
                    \App\AccountTransaction::nextRecords($account->account_id,$data_id,$date_i->format("Y-m-d"));
                } 
            }

            //... contact_id ....................................
            $old_contact_id = Account::where('contact_id',$item->contact_id)->first();
            $old_account    = Account::where('contact_id',$inputs['old_shipping_contact_id'][$key])->first();
            if( !empty( $old_account ) ) {
                if (!empty($old_contact_id)) {
                    if ( $old_contact_id->id == $old_account->id) {
                        $account = AccountTransaction::where('transaction_id',$data_id)
                                                        ->whereHas("additional_shipping_item",function($query)  use ($check,$id_tr){
                                                            $query->whereHas("additional_shipping",function($qy) use ($check,$id_tr){
                                                                    $qy->where("type",$check);
                                                                    $qy->where("t_recieved",$id_tr);
                                                            });
                                                        })->where("account_id",$old_contact_id->id)
                                                        ->first();
                        if(!empty($account)){ 
                            $date_i              = Carbon::parse($account->operation_date);
                            $account->update([
                                'amount'         => $inputs['old_shipping_amount'][$key],
                                'operation_date' => $date_i->format("Y-m-d")
                            ]);
                            if($account->account->cost_center!=1){
                                // \App\AccountTransaction::oldBalance($account->id,$account->account->id,$data_id,$date_i->format("Y-m-d"));
                                \App\AccountTransaction::nextRecords($account->account_id,$data_id,$date_i->format("Y-m-d"));
                            }        
                        }                            
                    }else if ($old_contact_id->id != $old_account->id) {
                                $account = AccountTransaction::where('transaction_id',$data_id)
                                                                ->whereHas("additional_shipping_item",function($query)  use ($check,$id_tr){
                                                                    $query->whereHas("additional_shipping",function($qy) use ($check,$id_tr){
                                                                            $qy->where("type",$check);
                                                                            $qy->where("t_recieved",$id_tr);
                                                                    });
                                                                })->where("account_id",$old_contact_id->id)
                                                                ->first();
                                $date_i  = Carbon::parse($account->operation_date);
                                $account->delete();
                                \App\AccountTransaction::nextRecords($account->account_id,$data_id,$date_i->format("Y-m-d"));
                    }
                } 
            }else {
                if(!empty($old_contact_id)){
                    $account = AccountTransaction::where('transaction_id',$data_id)
                                                        ->whereHas("additional_shipping_item",function($query)  use ($check,$id_tr){
                                                            $query->whereHas("additional_shipping",function($qy) use ($check,$id_tr){
                                                                    $qy->where("type",$check);
                                                                    $qy->where("t_recieved",$id_tr);
                                                            });
                                                        })->where("account_id",$old_contact_id->id)
                                                        ->first();
                    $date_i  = Carbon::parse($account->operation_date);
                    $account->delete();
                    \App\AccountTransaction::nextRecords($account->account_id,$data_id,$date_i->format("Y-m-d"));
                }
            }
           
            $cost_center          =  !isset($inputs['cost_center_id'])?null:$inputs['cost_center_id'];
            $item->contact_id     =  (isset($inputs['old_shipping_contact_id'][$key]))? $inputs['old_shipping_contact_id'][$key]:$inputs['contact_id'];
            $item->cost_center_id =  (isset($inputs['old_shipping_cost_center_id'][$key]))? $inputs['old_shipping_cost_center_id'][$key]:$cost_center;
            $item->account_id     =  $inputs['old_shipping_account_id'][$key];
            $item->amount         =  $inputs['old_shipping_amount'][$key];
            $item->vat            =  $inputs['old_shipping_vat'][$key];
            $item->total          =  $inputs['old_shipping_total'][$key];
            $item->text           =  $inputs['old_shiping_text'][$key];
            // ..............     currency
            if(isset($inputs['currency_id']) && $inputs['currency_id'] != ""){
                $currency_id  =  $inputs['currency_id'] ;
                $currency     = ($inputs['currency_id_amount'] >= 0)?$inputs['currency_id_amount']:0;
                if(isset($inputs['add_currency_id']) && $inputs['add_currency_id'] != ""){
                    $currency_id  =  $inputs['add_currency_id'] ;
                    $currency     = ($inputs['add_currency_id_amount']>=0)?$inputs['add_currency_id_amount']:0;
                }
                if(isset($inputs['old_line_currency_id'][$key]) && $inputs['old_line_currency_id'][$key] != ""){
                    $currency_id  =  $inputs['old_line_currency_id'][$key] ;
                    $currency     = ($inputs['old_line_currency_id_amount'][$key]>=0)?$inputs['old_line_currency_id_amount'][$key]:0;
                }
                $item->currency_id     = $currency_id;
                $item->exchange_rate   = $currency;
            }
            //............................................................. .. .. .. . .. . .
            $date_i               =  Carbon::parse($inputs['old_shiping_date'][$key]);
            $item->date           =  $date_i->format("Y-m-d h:i:s a");
            $item->save();
        }


        if (isset($inputs['shipping_amount'])){
             foreach ($inputs['shipping_amount'] as $key=>$amount) {
                 
                $cost_center = (!isset($inputs['cost_center_id']))?null:$inputs['cost_center_id'];
                
                if ($amount > 0) {
                    $item                         =  new AdditionalShippingItem;
                    $item->additional_shipping_id =  $data->id;
                    $item->contact_id             =  (isset($inputs['shipping_contact_id'][$key]))?$inputs['shipping_contact_id'][$key]:$inputs['contact_id']??$inputs['supplier_id'];
                    $item->cost_center_id         =  (isset($inputs['shipping_cost_center_id'][$key]))?$inputs['shipping_cost_center_id'][$key]:$cost_center;
                    $item->account_id             =  $inputs['shipping_account_id'][$key];
                    $item->amount                 =  $inputs['shipping_amount'][$key];
                    $item->vat                    =  $inputs['shipping_vat'][$key];
                    $item->total                  =  $inputs['shipping_total'][$key];
                    $item->text                   =  $inputs['shiping_text'][$key];
                    // ..............     currency
                    if(isset($inputs['currency_id']) && $inputs['currency_id'] != ""){
                        $currency_id  =  $inputs['currency_id'] ;
                        $currency     = ($inputs['currency_id_amount'] >= 0)?$inputs['currency_id_amount']:0;
                        if(isset($inputs['add_currency_id']) && $inputs['add_currency_id'] != ""){
                            $currency_id  =  $inputs['add_currency_id'] ;
                            $currency     = ($inputs['add_currency_id_amount']>=0)?$inputs['add_currency_id_amount']:0;
                        }
                        if(isset($inputs['line_currency_id'][$key]) && $inputs['line_currency_id'][$key] != ""){
                            $currency_id  =  $inputs['line_currency_id'][$key] ;
                            $currency     = ($inputs['line_currency_id_amount'][$key]>=0)?$inputs['line_currency_id_amount'][$key]:0;
                        }
                        $item->currency_id     = $currency_id;
                        $item->exchange_rate   = $currency;
                    }
                    //............................................................. .. .. .. . .. . .
                    $date_i               = Carbon::parse($inputs['shiping_date'][$key]);
                    $item->date           = $date_i->format("Y-m-d h:i:s a");
                    $item->save();

                    if(isset($inputs['cost_center_id'])){
                        $cost = (isset($inputs['shipping_cost_center_id'][$key]))?
                        $inputs['shipping_cost_center_id'][$key]:$inputs['cost_center_id'];
                        
                        $trans = \App\Transaction::find($data_id);
                        if($user != null){
                            AccountTransaction::add_shipp_id($cost,$inputs['shipping_amount'][$key],'debit',$trans,'Add Expense',$item->date,$item->id,null,$user);
                        }else{
                            AccountTransaction::add_shipp_id($cost,$inputs['shipping_amount'][$key],'debit',$trans,'Add Expense',$item->date,$item->id);
                        }
                    } 
                    $new_ship_item = \App\Models\ChildArchive::ship_save_child($item->id,"create");

                }
            }
        } 
        
    }

    public function items()
    {
        return $this->hasMany('App\Models\AdditionalShippingItem','additional_shipping_id');
    }

    ///./.............................
    public static function add_purchase_payment($id,$type=null,$tr_r=null,$return=null,$user=null)
    {
         
        if($type != null){ $check = 1; }else{  $check = 0; }
          $data    =  AdditionalShipping::where('transaction_id',$id)->where("type",$check)->where("t_recieved",$tr_r)->first();
            
         if ($data) {
            //.................................. get children * 1
            $ids  = $data->items->pluck('id');  
            //...................................................... end * 1
            $business_id = \App\Transaction::find($id)->business_id??null;

            //.................................... delete all not in child  * 2
            $ti =  \App\AccountTransaction::where('transaction_id',$id)
                                                ->whereHas("additional_shipping_item",function($query)  use ($check,$tr_r){
                                                        $query->whereHas("additional_shipping",function($qy) use ($check,$tr_r){
                                                            $qy->where("type",$check);
                                                            $qy->where("t_recieved",$tr_r);
                                                        });
                                                })->whereNotNull('additional_shipping_item_id')
                                                ->whereNotIn('additional_shipping_item_id',$ids)
                                                ->get();
            foreach($ti as $o){
              $account_transactions = \App\Account::find($o->account_id);
              $actions_date         = $o->operation_date;
              $o->delete();
              if($account_transactions->cost_center!=1){
                \App\AccountTransaction::nextRecords($account_transactions->id,$business_id,$actions_date);
              }
            }
            //............................................................................ end  * 2
            //................................................... search in children * 3                                 
            foreach ($data->items as $item) { 
                // supp................
                $account     =  Account::where('contact_id',$item->contact_id)->first();
                
                if ($account) {
                    $check_type = ($return != null)?'debit':'credit';
                    $pay = \App\AccountTransaction::where('account_id', $account->id)
                                                        ->where('additional_shipping_item_id',$item->id)
                                                        ->whereHas("additional_shipping_item",function($query)  use ($check,$tr_r){
                                                            $query->whereHas("additional_shipping",function($qy) use ($check,$tr_r){
                                                                $qy->where("type",$check);
                                                                $qy->where("t_recieved",$tr_r);
                                                            });
                                                        })
                                                        ->where("type",$check_type)
                                                        ->first();   
                    ;
                    if (empty($pay)) {
                        $date_i      = Carbon::parse($item->date);
                        $credit_data = [
                            'amount'                      => $item->total,
                            'account_id'                  => $account->id,
                            'type'                        => ($return != null)?'debit':'credit',
                            'sub_type'                    => 'deposit',
                            'operation_date'              => $date_i->format("Y-m-d"),
                            'created_by'                  => ($user != null)?$user->id:session()->get('user.id'),
                            'note'                        => 'Additional Shipping Expenses',
                            'transaction_id'              => $id,
                            'additional_shipping_item_id' => $item->id,
                            'return_transaction_id'       => ($return != null)?$id:null,
                            'cost_center_id'              => $item->cost_center_id
                        ];
                        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                       
                        if($account->cost_center!=1){ 
                            \App\AccountTransaction::nextRecords($account->id,$business_id,$date_i->format("Y-m-d"));
                        }
                        $entry  = \App\Models\Entry::orderBy("id","desc")->where('account_transaction',$id)->first();
                        ($entry) ? $entry->id :null; 
                        $trans_account = \App\AccountTransaction::where("transaction_id",$id)
                                                        ->where("additional_shipping_item_id",$item->id)
                                                        ->whereHas("additional_shipping_item",function($query)  use ($check,$tr_r){
                                                            $query->whereHas("additional_shipping",function($qy) use($check,$tr_r){
                                                                $qy->where("type",$check);
                                                                $qy->where("t_recieved",$tr_r);
                                                            });
                                                        })
                                                        ->whereHas("account",function($query){
                                                                $query->where("cost_center",0);
                                                        })->whereIn("note",["Add Expense","Additional Shipping Expenses","Additional Shipping Expenses Tax" ])
                                                        ->get();
                        
                        foreach($trans_account as $it){
                            $it->entry_id = ($entry)? $entry->id:null;
                            $it->update();
                        }
                    }else{
                        $date_i = Carbon::parse($item->date);
                        $pay->update([
                            'amount'         => $item->total,
                            'operation_date' => $date_i->format("Y-m-d"),
                            'transaction_id' => $id
                        ]);
                        if($account->cost_center!=1){ 
                            \App\AccountTransaction::nextRecords($account->id,$business_id,$date_i->format("Y-m-d"));
                        }
                    }
                }
                
                // tax................
                if ($item->vat) {
                    $for_check = ($check == 0)?null:1;
                    if($user != null){$U_user = $user;}else{$U_user = null;}
                    $setting  = \App\Models\SystemAccount::where("business_id",$data->transaction->business_id)->first();
                    $account  = \App\Account::where("id",$setting->journal_expense_tax)->first();
                    AdditionalShipping::add_main($account->name,$item->vat,$id,$item->id,$item->date,$for_check,$return,$U_user);
                }

                $check_type_2 = ($return != null)?'credit':'debit';
                // expense...................
                $pays = \App\AccountTransaction::where('account_id', $item->account_id)
                                                    ->where('additional_shipping_item_id',$item->id)
                                                    ->whereHas("additional_shipping_item",function($query)  use ($check,$tr_r){
                                                        $query->whereHas("additional_shipping",function($qy) use ($check,$tr_r){
                                                            $qy->where("type",$check);
                                                            $qy->where("t_recieved",$tr_r);
                                                        });
                                                    })
                                                    ->where("type",$check_type_2)
                                                    ->first();   
                if (empty($pays)) {
                    $date_i           = Carbon::parse($item->date);
                    $credit_data = [
                        'amount'                      => $item->amount,
                        'account_id'                  => $item->account_id,
                        'type'                        => ($return != null)?'credit':'debit',
                        'sub_type'                    => 'deposit',
                        'operation_date'              => $date_i->format("Y-m-d"),
                        'created_by'                  => ($user != null)?$user->id:session()->get('user.id'),
                        'note'                        => 'Additional Shipping Expenses',
                        'transaction_id'              => $id,
                        'return_transaction_id'       => ($return != null)?$id:null,
                        'additional_shipping_item_id' => $item->id
                    ];
                    $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
                    // $type="Shipping";
                    // \App\Models\Entry::create_entries($item,$type);
                    if($item->account->cost_center!=1){ 
                        \App\AccountTransaction::nextRecords($item->account_id,$business_id,$date_i->format("Y-m-d"));
                    }
                    $entry  = \App\Models\Entry::orderBy("id","desc")->where('account_transaction',$id)->first();
                    ($entry) ? $entry->id :null; 
                    $trans_account = \App\AccountTransaction::where("transaction_id",$id)
                                                    ->where("additional_shipping_item_id",$item->id)
                                                    ->whereHas("additional_shipping_item",function($query)  use ($check,$tr_r){
                                                        $query->whereHas("additional_shipping",function($qy) use($check,$tr_r){
                                                            $qy->where("type",$check);
                                                            $qy->where("t_recieved",$tr_r);
                                                        });
                                                    })
                                                    ->whereHas("account",function($query){
                                                            $query->where("cost_center",0);
                                                    })->whereIn("note",["Add Expense","Additional Shipping Expenses","Additional Shipping Expenses Tax" ])
                                                    ->get();
                     
                    foreach($trans_account as $it){
                        $it->entry_id = ($entry)? $entry->id:null;
                        $it->update();
                    }
                }else{
                    $date_i           = Carbon::parse($item->date);
                    $pays->update([
                        'amount'         => $item->amount,
                        'operation_date' => $date_i->format("Y-m-d"),
                    ]);
                    if($item->account->cost_center!=1){ 
                        \App\AccountTransaction::nextRecords($item->account_id,$business_id,$date_i->format("Y-m-d"));
                    }
                }

                
            }
        }
    }
    ///./.........................................
    
    
    
    public static function add_main($type,$amount,$id,$item_id,$date=null,$check=null,$return=null,$user=null)
    {
        
        if($check == null){  $check = 0; }else{ $check = 1;  }

        //purchase account 
        $business_id = ($user != null)?$user->business_id:request()->session()->get('user.business_id');
        $account     = \App\Account::where('name',$type)->where('business_id',$business_id)->first();
        if (empty($account)) {
            $account                  =  new \App\Account;
            $account->name            =  $type;
            $account->business_id     =  $business_id;
            $account->name            =  $type;
            $account->account_number  =  '00000'.$type;
            $account->save();
        }
        
        $pay = \App\AccountTransaction::where('account_id',$account->id)->where('transaction_id',$id)->where('return_transaction_id',$return)
                                                ->where('additional_shipping_item_id',$item_id)
                                                ->whereHas("additional_shipping_item",function($query)  use ($check){
                                                    $query->whereHas("additional_shipping",function($qy) use($check){
                                                        $qy->where("type",$check);
                                                    });
                                                })
                                                ->first();
        $entry  = \App\Models\Entry::orderBy("id","desc")->where('account_transaction',$id)->first();
        if (empty($pay)) {
            $credit_data = [
                'amount'                      => $amount,
                'account_id'                  => $account->id,
                'type'                        => ($return!=null)?'credit':'debit',
                'sub_type'                    => 'deposit',
                'operation_date'              => ($date == null)?date('Y-m-d'):$date,
                'created_by'                  => ($user != null)?$user->id:session()->get('user.id'),
                'note'                        => 'Additional Shipping Expenses',
                'transaction_id'              => $id,
                'return_transaction_id'       => ($return != null)?$id:null,
                'additional_shipping_item_id' => $item_id,
                'entry_id'=>$entry_id,
            ];
            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            if($account->cost_center!=1){
                // \App\AccountTransaction::oldBalance($credit->id,$account->id,$business_id,($date==null)?date('Y-m-d'):$date);
                // \App\AccountTransaction::nextRecords($account->id,$business_id,($date==null)?date('Y-m-d'):$date);
            }
            return $credit;
        }else{
            $pay->update([
                'amount'         => $amount,
                'operation_date' => ($date==null)?date('Y-m-d'):$date
            ]);
        }
        
    }
    public function transaction()
    {
        return $this->belongsTo('App\Transaction','transaction_id');

    }

   

}
