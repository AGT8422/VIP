<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AccountTransaction;
use App\Models\AdditionalShipping;
use App\Models\AdditionalShippingItem;
use App\Models\Entry;
use App\Models\Check;
use App\Transaction;
class AccountController extends Controller
{
    public function transaction($id)
    {
        $allData   =  AccountTransaction::where('transaction_id',$id)
                                              ->whereHas('account',function($query){
                                                    $query->where('cost_center',0);
                                            })->where('amount','>',0) 
                                            ->whereNull('for_repeat')
                                            ->orderBy("entry_id")
                                            ->get();
                
        $entry     =  Entry::get();
        $check     =  Check::get();
        $ship_     =  AdditionalShipping::where('transaction_id',$id)->get();
        $shipItem  =  null;
        $ship = [];
        if(count($ship_)>0){
            $ship_id_items = [];
            foreach($ship_ as $item){ 
                foreach($item->items->pluck("id") as $item){
                    $ship_id_items[] = $item;
                }
            }
            $shipItem  =  AdditionalShippingItem::whereIn("id",$ship_id_items)->whereNotNull("cost_center_id")->get();
        }
        $business_id = request()->session()->get("user.business_id");
        $setting     =  \App\Models\SystemAccount::where('business_id',$business_id)->first();
        $data        =  Transaction::find($id); 
        
        $tr          =  Transaction::where("return_parent_id",$id)->first(); 
        //... discount cost center
        $purchase_discount_id  = ($setting)?$setting->purchase_discount:Account::add_main('Purchases Discount');
        $discount  =  \App\AccountTransaction::where("transaction_id",$id)
                                                ->whereHas('account',function($query) use($purchase_discount_id){
                                                        $query->where('id',$purchase_discount_id);
                                                        $query->where('cost_center',0);
                                                })->where('amount','>',0) 
                                                ->where('for_repeat',null)
                                                ->orderBy("entry_id")
                                                ->first();
        if(!empty($tr)){
            $global    = \App\AccountTransaction::where("transaction_id",$id)->Orwhere("transaction_id",$tr->id)->whereHas('account',function($query) {
                                                                                            $query->where('cost_center',0);
                                                                                })->where('amount','>',0)
                                                                                ->where('for_repeat',null) 
                                                                                ->get();
            $ent    = \App\AccountTransaction::where("transaction_id",$id)->Orwhere("transaction_id",$tr->id)->whereHas('account',function($query) {
                                                                                    $query->where('cost_center',0);
                                                                        })->where('amount','>',0)
                                                                        ->where('for_repeat',null) 
                                                                        ->select("entry_id")
                                                                        ->groupBy('entry_id') 
                                                                        ->get();
        }else{
            $global    = \App\AccountTransaction::where("transaction_id",$id)->whereHas('account',function($query) {
                                                                                            $query->where('cost_center',0);
                                                                                })->where('amount','>',0)
                                                                                ->whereNull('for_repeat') 
                                                                                ->get();
            $ent    = \App\AccountTransaction::where("transaction_id",$id)->whereHas('account',function($query) {
                                                                                        $query->where('cost_center',0);
                                                                                    })->where('amount','>',0)
                                                                                    ->whereNull('for_repeat') 
                                                                                    ->select("entry_id")
                                                                                    ->groupBy('entry_id') 
                                                                                    ->get();
        }
        $costs     = \App\AccountTransaction::where("transaction_id",$id)->whereHas('account',function($query) {
                                                                                     $query->where('cost_center',">",0);
                                                                            })->whereNull('for_repeat')->where('amount','>',0) 
                                                                            ->get();
        $entries_id  = [];
        $ids         = [];
        $en_ids      = [];
        $en_ids_check      = [];
        foreach($ent as $i){
            $id_global   = [];
            if($i->entry_id != null){
                if(!in_array($i->entry_id,$en_ids)){
                    $en_ids[]           =  $i->entry_id;
                    foreach($global as $it){
                        if($i->entry_id == $it->entry_id){
                            $en_ids_check[] = $i->entry_id;
                            $id_global[]    = $it->id;
                            $ids[$i->entry_id] =  $id_global;
                        }
                    }
                }
            }
        }
          

        $Transaction_payment = \App\TransactionPayment::where("transaction_id",$id)->get();
        $account_transaction_check   = [];
        $account_transaction_voucher = [];
        foreach($Transaction_payment as $it){
            if($it->check_id != null){
                $account_transaction_check[] = $it->check_id;
            }
            if($it->payment_voucher_id != null){
                $account_transaction_voucher[] = $it->payment_voucher_id;
            }
        }

        foreach($account_transaction_check as $it){
            $line = \App\AccountTransaction::where("check_id",$it)->whereNull('for_repeat')->get();
            foreach($line as $i){
                $id_global   = [];
                if($i->entry_id != null){
                    if(!in_array($i->entry_id,$en_ids)){
                        $en_ids[]           =  $i->entry_id;
                        foreach($line as $it){
                            if($i->entry_id == $it->entry_id){
                                $en_ids_check[] = $i->entry_id;
                                $id_global[]    = $it->id;
                                $ids[$i->entry_id] =  $id_global;
                            }
                        }
                    }
                }
            }
            
         }
        foreach($account_transaction_voucher as $it){
            $line = \App\AccountTransaction::where("payment_voucher_id",$it)->whereNotNull("transaction_id")->get();
           
            foreach($line as $i){
                $id_global   = [];
                if($i->entry_id != null){
                    if(!in_array($i->entry_id,$en_ids)){
                        $en_ids[]           =  $i->entry_id;
                        foreach($line as $it){
                            if($i->entry_id == $it->entry_id){
                                $en_ids_check[] = $i->entry_id;
                                $id_global[]    = $it->id;
                                $ids[$i->entry_id] =  $id_global;
                            }
                        }
                    }
                }
            }
           
        }
        return view('account.action_parents.transactions')
        ->with('allData',$allData)
        ->with('data',$data)
        ->with('shipItem',$shipItem)
          ->with('entry',$entry)
          ->with('check',$check)
          ->with('discount',$discount)
          ->with('all',$en_ids)
          ->with('item',$ids)
          ->with('costs',$costs)
          ->with('setting',$setting)
          ->with('global',$global);
    }
}
