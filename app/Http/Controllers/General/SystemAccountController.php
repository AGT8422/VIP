<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemAccount;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Account;
class SystemAccountController extends Controller
{
    
    
    public function index()
    { 
        
        if(!auth()->user()->can("business.create")){
            abort(403, 'Unauthorized action.');
        }

        $business_id                =  request()->session()->get('user.business_id');
        $data                       =  SystemAccount::where('business_id',$business_id)->first();
        $accounts                   =  Account::items();
        $patterns                   =  \App\Models\Pattern::allname_id($business_id);
        $business_locations         =  \App\BusinessLocation::allLocation($business_id);
        
         
        if(request()->ajax()){
            
            $Account = SystemAccount::join("patterns as pt" , "system_accounts.pattern_id","=","pt.id")
                                      ->where("system_accounts.business_id",$business_id);
             
            if(!empty(request()->location_id)){
                $location_id = request()->location_id;
                $Account->where("pt.location_id" ,$location_id);
            }
            if(!empty(request()->pattern_id)){
      
                $pattern_id = request()->pattern_id;
                $Account->where("system_accounts.pattern_id" ,$pattern_id);
            }
          
            $Account->select()->get();

            return Datatables::of($Account)
                    ->removeColumn("id")
                    ->addColumn("actions",function($row){
                        $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                        </button> 
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        // if (auth()->user()->can("purchase.view")) {
                        //     $html .= '<li><a href="#" data-href="' . action('PatternController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                        // }
                        if (auth()->user()->can("purchase.update")) {
                            $html .= '<li><a href="' . action('General\SystemAccountController@edit', [$row->id]) . '"><i class="fas fa-edit"></i>' . __("messages.edit") . '</a></li>';
                        }
                        // if (auth()->user()->can("purchase.delete")) {
                        //     $html .= '<li><a href="#" data-href="' . action('PatternController@destroy', [$row->id]) . '" class="delete-patterns"><i class="fas fa-trash"></i>' . __("messages.delete") . '</a></li>';
                        // }
                        $html .=  '</ul></div>';
                        return $html;
                    })
                    ->editColumn("location_id",function($row){
                        $pattern = \App\Models\Pattern::find($row->pattern_id);
                        return $pattern->location->name ;
                    }) 
                    ->editColumn("created_at",function($row){
                        $date = date_format( $row->created_at, "d/m/Y h:i:s");
                        return $date;
                    })->editColumn("user_id",function($row){
                        $pattern = \App\Models\Pattern::find($row->pattern_id);
                        return $pattern->user->username ;
                    })->setRowAttr([
                        // "data-href"=> function ($row) { return action('PatternController@show', [$row->id]) ; }  
                    ])
                    ->rawColumns(['actions','location_id','created_at','user_id'])
                    ->make(true);
        }



        return view('account.systemList')
                            ->with('accounts',$accounts)
                            ->with('data',$data)
                            ->with('patterns',$patterns)
                            ->with('business_locations',$business_locations)
                            ;
        
    }
    
    
    
    public function create()
    {
        
        $business_id = request()->session()->get('user.business_id');
        $data        =  SystemAccount::where('business_id',$business_id)->first();
        $accounts    =  Account:: items();
        $patterns    =  \App\Models\Pattern::allname_id_account($business_id);

        return view('account.system')
                    ->with('accounts',$accounts)
                    ->with('data',$data)
                    ->with('patterns',$patterns)
                    ;
    }

    public function edit($id)
    {
        
        $business_id = request()->session()->get('user.business_id');
        $data        =  SystemAccount::where('business_id',$business_id)->where("pattern_id",$id)->first();
        $accounts    =  Account:: items();
        $patterns    =  \App\Models\Pattern::allname_id($business_id);

        return view('account.edit_system_account')
                    ->with('accounts',$accounts)
                    ->with('data',$data)
                    ->with('patterns',$patterns)
                    ;
    }
    public function add(Request $request)
    {
       
        $business_id               =  request()->session()->get('user.business_id');
        $data                      =  SystemAccount::where('business_id',$business_id)->where("pattern_id",$request->pattern_id)->first();
        if (empty($data)) {
            $data                  =  new SystemAccount;
        }
        $data->pattern_id          =  $request->pattern_id;
        $data->business_id         =  $business_id;
        $data->purchase            =  $request->purchase;
        $data->purchase_tax        =  $request->purchase_tax;
        $data->sale                =  $request->sale;
        $data->sale_tax            =  $request->sale_tax;
        $data->cheque_debit        =  $request->cheque_debit;
        $data->cheque_collection   =  $request->cheque_collection;
        $data->journal_expense_tax =  $request->journal_expense_tax;
        $data->sale_return         =  $request->sale_return;
        $data->sale_discount       =  $request->sale_discount;
        $data->purchase_return     =  $request->purchase_return;
        $data->purchase_discount   =  $request->purchase_discount;
        $data->save();
        return redirect("/account/system-account-list")
                ->with('status', [
                        'success' => 1,
                        'msg' =>trans('home.Done Successfully')
                    ]);
    }

    public function update(Request $request,$id)
    {
       
        $business_id               = request()->session()->get('user.business_id');
        $data                      =  SystemAccount::find($id);
        // $data->pattern_id          =  $request->pattern_id;
        $data->business_id         =  $business_id;
        $data->purchase            =  $request->purchase;
        $data->purchase_tax        =  $request->purchase_tax;
        $data->sale                =  $request->sale;
        $data->sale_tax            =  $request->sale_tax;
        $data->cheque_debit        =  $request->cheque_debit;
        $data->cheque_collection   =  $request->cheque_collection;
        $data->journal_expense_tax =  $request->journal_expense_tax;
        $data->sale_return         =  $request->sale_return;
        $data->sale_discount       =  $request->sale_discount;
        $data->purchase_return     =  $request->purchase_return;
        $data->purchase_discount   =  $request->purchase_discount;
        $data->update();
        return redirect("/account/system-account-list")
                ->with('status', [
                        'success' => 1,
                        'msg' =>trans('home.Done Successfully')
                    ]);
    }
}
