<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AccountTransaction;
use App\Models\Entry;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use Spatie\Activitylog\Models\Activity;

class EntriesController extends Controller
{

    public function index()
    {
        if(!auth()->user()->can("purchase.create")){
            abort(403, 'Unauthorized action.');
        }
        
        $business_id   = request()->session()->get("user.business_id");
        $total         = \App\Models\Entry::total($business_id);
        $info          = \App\Models\Entry::info($business_id);
        $state         = $info[0] ; 
        $refe          = $info[1] ; 
        $ref           = $info[2] ; 
        if(request()->ajax()){
            $entries = Entry::where("entries.business_id",$business_id);
             if(!empty(request()->refe_no_e)){
                 $refe_no = request()->refe_no_e;
                $entries->where("refe_no_e",$refe_no);
               

            }
            if(!empty(request()->ref_no_e)){
                $ref_no = request()->ref_no_e;
                $entries->where("ref_no_e",$ref_no);
            }
            if(!empty(request()->state)){
                $state = request()->state;
                $entries->where("state",$state);

            }
            if (!empty(request()->start_date) && !empty(request()->end_date) ) {
                $start = request()->start_date;
                $end   =  request()->end_date;
                $entries->join("transactions as tr","entries.account_transaction","=","tr.id")
                                                  ->whereDate('tr.transaction_date', '>=', $start)
                                                  ->whereDate('tr.transaction_date', '<=', $end);
                if(empty(request()->refe_no_e) && empty(request()->ref_no_e) && empty(request()->state)){
                    $entries = Entry::where("entries.business_id",$business_id)
                                             ->whereDate('entries.created_at', '>=', $start)
                                             ->whereDate('entries.created_at', '<=', $end);
                }
                if(!count($entries->get())>0){
                    $entries = Entry::where("entries.business_id",$business_id);
                    $entries->leftjoin("payment_vouchers as pv","entries.voucher_id","=","pv.id")
                                            ->select("pv.*","entries.*")
                                            ->whereDate('entries.created_at', '>=', $start)
                                            ->whereDate('entries.created_at', '<=', $end);

                    if(!empty(request()->refe_no_e)){
                        $refe_no = request()->refe_no_e;
                       $entries->where("refe_no_e",$refe_no);
  
                   }
                   if(!empty(request()->ref_no_e)){
                       $ref_no = request()->ref_no_e;
                       $entries->where("ref_no_e",$ref_no);
                   }
                   if(!empty(request()->state)){
                       $state = request()->state;
                       $entries->where("state",$state);
       
                   }
                }
                if(!count($entries->get())>0){
                    $entries = Entry::where("entries.business_id",$business_id);
                    $entries->leftjoin("daily_payments as dp","entries.journal_voucher_id","=","dp.id")
                                            ->select("dp.*","entries.*")
                                            ->whereDate('entries.created_at', '>=', $start)
                                            ->whereDate('entries.created_at', '<=', $end);

                    if(!empty(request()->refe_no_e)){
                        $refe_no = request()->refe_no_e;
                       $entries->where("refe_no_e",$refe_no);
  
                   }
                   if(!empty(request()->ref_no_e)){
                       $ref_no = request()->ref_no_e;
                       $entries->where("ref_no_e",$ref_no);
                   }
                   if(!empty(request()->state)){
                       $state = request()->state;
                       $entries->where("state",$state);
       
                   }
                   
                }
                if(!count($entries->get())>0){
                    $entries = Entry::where("entries.business_id",$business_id);
                    $entries->leftjoin("gournal_vouchers as gv","entries.expense_voucher_id","=","gv.id")
                                            ->select("gv.*","entries.*")
                                            ->whereDate('entries.created_at', '>=', $start)
                                            ->whereDate('entries.created_at', '<=', $end);

                    if(!empty(request()->refe_no_e)){
                        $refe_no = request()->refe_no_e;
                       $entries->where("refe_no_e",$refe_no);
  
                   }
                   if(!empty(request()->ref_no_e)){
                       $ref_no = request()->ref_no_e;
                       $entries->where("ref_no_e",$ref_no);
                   }
                   if(!empty(request()->state)){
                       $state = request()->state;
                       $entries->where("state",$state);
       
                   }
                   
                }
                if(!count($entries->get())>0){
                    $entries = Entry::where("entries.business_id",$business_id);
                    $entries->leftjoin("additional_shippings as ads","entries.shipping_id","=","ads.id")
                                            ->select("ads.*","entries.*")
                                            ->whereDate('entries.created_at', '>=', $start)
                                            ->whereDate('entries.created_at', '<=', $end);

                    if(!empty(request()->refe_no_e)){
                        $refe_no = request()->refe_no_e;
                       $entries->where("refe_no_e",$refe_no);
  
                   }
                   if(!empty(request()->ref_no_e)){
                       $ref_no = request()->ref_no_e;
                       $entries->where("ref_no_e",$ref_no);
                   }
                   if(!empty(request()->state)){
                       $state = request()->state;
                       $entries->where("state",$state);
       
                   }
                   
                }
            } 
            return DataTables::of($entries)
                    ->addColumn("action",function($row) use($entries) {
                        $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                        </button> 
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        if (auth()->user()->can("purchase.view")) {
                            if($row->state == "Cheque")  {
                                $html .= '<li><a href="#" data-href="' .\URL::to('cheque/entry/'.$row->check_id)   . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                            } else if ($row->state == "Receipt Voucher" || $row->state == "Payment Voucher")  {
                                $html .= '<li><a href="#" data-href="' .\URL::to('payment-voucher/entry/'.$row->voucher_id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                            } else if ($row->state == "Payment")  {
                                if($row->payment){
                                    if($row->payment->transaction){
                                        $html .= '<li><a href="#" data-href="' .\URL::to('entry/transaction/'.$row->payment->transaction->id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                                    }else{$html .="";}
                                }else{$html .="";}
                            } else if ($row->state == "Shipping")  {
                                if($row->additional_shipping){
                                    if($row->additional_shipping->transaction){
                                        $html .= '<li><a href="#" data-href="' .\URL::to('entry/transaction/'.$row->additional_shipping->transaction->id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                                    }else{$html .="";}
                                }else{$html .="";}
                            } else if ($row->state == "Expense Voucher")  {
                                $html .= '<li><a href="#" data-href="' .\URL::to('gournal-voucher/entry/'.$row->expense_voucher_id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                            } else if ($row->state == "Journal Voucher")  {
                                $html .= '<li><a href="#" data-href="' .\URL::to('daily-payment/entry/'.$row->journal_voucher_id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                            } else if ($row->state == "Un Collect Cheque")  {
                                $html .= '<li><a href="#" data-href="' .\URL::to('cheque/entry/'.$row->check_id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                            } else if ($row->state == "refund Collect")  {
                                $html .= '<li><a href="#" data-href="' .\URL::to('cheque/entry/'.$row->check_id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                            } else if ($row->state == "Collect Cheque")  {
                                $html .= '<li><a href="#" data-href="' .\URL::to('cheque/entry/'.$row->check_id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                            } else if ($row->state == "Production")  {
                                if($row->transaction){
                                    $html .= '<li><a href="#" data-href="' .\URL::to('/manufacturing/entry/'.$row->transaction->id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                                }else{$html .="";}
                            } else if ($row->state == "Return Purchase")  {
                                if($row->transaction){
                                    $html .= '<li><a href="#" data-href="' .\URL::to('/manufacturing/entry/'.$row->transaction->id) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                                }else{$html .="";}
                            } else {
                                if($row->purchase){
                                    $url_i = $row->purchase->id;
                                    $html .= '<li><a href="#" data-href="' .\URL::to('entry/transaction/'.$url_i) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("home.Entry") . '</a></li>';
                                }else{$html .="";}
                            }
                        }
                        $html .=  '</ul></div>';
                        return $html; 
                    })
                    ->addColumn("refe_no_e",function($row){
                         return $row->refe_no_e; 
                    })
                    ->addColumn("ref_no_e",function($row) {
                        if($row->state == "Receipt Voucher" || $row->state == "Payment Voucher") {
                                $html = '<button type="button" class="btn btn-link btn-modal"
                                            data-href="' . action('General\PaymentVoucherController@show', [$row->voucher_id]) . '" data-container=".view_modal"> 
                                                    '.$row->ref_no_e.'
                                        </button>';
                             
                        } else if ($row->state == "Journal Voucher") {
                            $html = '<button type="button" class="btn btn-link btn-modal"
                                        data-href="' . action('General\DailyPaymentController@show', [$row->journal_voucher_id]) . '" data-container=".view_modal"> 
                                                '.$row->ref_no_e.'
                                    </button>';
                        } else if ($row->state == "Payment") {
                           
                            if($row->payment){
                                $html = '<button type="button" class="btn btn-link btn-modal"
                                            data-href="' . \URL::to('payments/view-payment', [$row->payment->id]) . '" data-container=".view_modal"> 
                                                '.$row->payment->payment_ref_no.'
                                            </button>';
                            }else{$html ="";}
                               
                            
                        } else if ($row->state == "Expense Voucher") {
                            $html = '<button type="button" class="btn btn-link btn-modal"
                                        data-href="' . action('General\GournalVoucherController@view', [$row->expense_voucher_id]) . '" data-container=".view_modal"> 
                                                '.$row->ref_no_e.'
                                    </button>';
                         
                        } else if ($row->state == "Cheque") {
                                $html = '<button type="button" class="btn btn-link btn-modal"
                                            data-href="' . action('General\CheckController@show', [$row->check_id]) . '" data-container=".view_modal"> 
                                                    '.$row->ref_no_e.'
                                        </button>';
                            
                        } else if ($row->state == "Collect Cheque") {
                            $html = '<button type="button" class="btn btn-link btn-modal"
                                        data-href="' . action('General\CheckController@show', [$row->check_id]) . '" data-container=".view_modal"> 
                                                '.$row->ref_no_e.'
                                    </button>';
                        
                        } else if ($row->state == "Un Collect Cheque") {
                            $html = '<button type="button" class="btn btn-link btn-modal"
                                        data-href="' . action('General\CheckController@show', [$row->check_id]) . '" data-container=".view_modal"> 
                                                '.$row->ref_no_e.'
                                    </button>';
                        
                        } else if ($row->state == "refund Collect") {
                            $html = '<button type="button" class="btn btn-link btn-modal"
                                        data-href="' . action('General\CheckController@show', [$row->check_id]) . '" data-container=".view_modal"> 
                                                '.$row->ref_no_e.'
                                    </button>';
                        
                        } else if ($row->state == "Purchase" || $row->state == "Return Purchase") {
                            if($row->payment){
                                $html = '<button type="button" class="btn btn-link btn-modal"
                                                data-href="'. action('PurchaseController@show', [$row->purchase->id]) .'" data-container=".view_modal"> 
                                                '.$row->ref_no_e.'
                                                </button>';
                            }else{$html ="";}
                          
                        } else if ($row->state == "Sale" || $row->state == "Return Sale") {
                            
                             if($row->payment){
                                $html = '<button type="button" class="btn btn-link btn-modal"
                                        data-href="' . action('SellController@show', [$row->purchase->id]) . '" data-container=".view_modal"> 
                                                '.$row->ref_no_e.'
                                         </button>';
                            }else{$html ="";}
                        } else if ($row->state == "Shipping") {
                            if($row->additional_shipping){
                                if($row->additional_shipping->transaction){
                                    $html = '<button type="button" class="btn btn-link btn-modal"
                                                    data-href="' . action('PurchaseController@show', [$row->additional_shipping->transaction->id]) . '" data-container=".view_modal"> 
                                                            '.$row->ref_no_e.'
                                                </button>';
                                }else{$html ="";}
                            }else{$html ="";}
                        }else if ($row->state == "Production"){
                             
                            if($row->additional_shipping->transaction){
                                 $id   = $row->transaction->id;
                                 $html = '<button type="button" class="btn btn-link btn-modal"
                                                    data-href="' . \URL::to("/manufacturing/production",[$id]) . '" data-container=".view_modal"> 
                                                            '.$row->ref_no_e.'
                                                </button>';
                            }else{$html ="";}
                        }
                        return $html;
                    })
                    ->addColumn("state",function($row){
                        return $row->state;
                    })
                    ->addColumn("created_at",function($row){
                     
                        return $row->created_at;
                    })
                    ->setRowAttr([
                        "data-href" => function($row){
                                if(auth()->user()->can("purchase.create")){
                                    return "";
                                    // return \URL::to("/entries/show/".$row->id);
                                }else{
                                    return "";
                                }
                            }]
                        )
                    ->rawColumns(['refe_no_e','ref_no_e','state','created_at','action'])
                    ->make(true) ;
        }

        return view("entries.index")->with(compact("total","state","refe","ref"));
    }

    public function log_out(){
        
        
        $array = [20,24,23,22,21,16,15,14,13,12,11,10,9,8,7];
        foreach($array as $it){
        $user = \App\User::where("id",$it)->first();    
        if(!empty($user)){
                $activity = Activity::orderBy("id","desc")->forSubject($user)
                ->Where(function ($query) use ($it) {
                    $query->where('subject_id', $it);
                    $query->whereIn('description', ["login","logout"]);
                })->first();
                if(!empty($activity)){
                    $activity->update([
                        "description"=>"logout",
                    ]);
                }
        }
            
        }
        return redirect()->back();
    }

}
