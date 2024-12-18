<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
class PosBranchController extends Controller
{
    
    public function index()
    {
        
        // if(auth()->user()->can("pos.create")){
        //     abort(403,"Utherization actions");
        // }
       
            
            $business_id   = request()->session()->get("user.business_id");
            $business_locations         = \App\BusinessLocation::allLocation($business_id);
            $invoice_layout             = \App\InvoiceLayout::allLocation($business_id);
            $invoice_schemes            = \App\InvoiceScheme::allLocation($business_id);
            $pattern_name               = \App\Models\Pattern::allname($business_id);
            
            if(request()->ajax()){
                $pos = \App\Models\PosBranch::select();
                // if(!empty(request()->location_id)){
                //     $location_id = request()->location_id;
                //     $patterns->where("location_id" ,$location_id);
                // }
                // if(!empty(request()->pattern_name)){
                //     $pattern_name = request()->pattern_name;
                //     $patterns->where("name" ,$pattern_name);
                // }
                // if(!empty(request()->invoice_scheme)){
                //     $invoice_scheme = request()->invoice_scheme;
                //     $patterns->where("invoice_scheme" ,$invoice_scheme);
                // }
                // if(!empty(request()->invoice_layout)){
                //     $invoice_layout = request()->invoice_layout;
                //     $patterns->where("invoice_layout" ,$invoice_layout);
                // }
                // if(!empty(request()->pos)){
                //     $pos = request()->pos;
                //     $patterns->where("pos" ,$pos);
                // }
                $pos->get();

                return Datatables::of($pos)
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
                            if (auth()->user()->can("purchase.view")) {
                                $html .= '<li><a href="#" data-href="' . action('PosBranchController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                            }
                            if (auth()->user()->can("purchase.update")) {
                                $html .= '<li><a href="' . action('PosBranchController@edit', [$row->id]) . '"><i class="fas fa-edit"></i>' . __("messages.edit") . '</a></li>';
                            }
                            // if (auth()->user()->can("purchase.delete")) {
                            //     $html .= '<li><a href="#" data-href="' . action('PatternController@destroy', [$row->id]) . '" class="delete-patterns"><i class="fas fa-trash"></i>' . __("messages.delete") . '</a></li>';
                            // }
                            $html .=  '</ul></div>';
                            return $html;
                        })
                        ->addColumn("name",function($row){
                            return $row->name;
                        })
                        ->addColumn("pattern",function($row){
                            return $row->pattern;
                        })
                        ->addColumn("store_id",function($row){
                            return $row->store_id;
                        })
                        ->addColumn("invoice_scheme",function($row){
                            return $row->invoice_scheme_id;
                        })
                        ->addColumn("main_cash_id",function($row){
                            return $row->main_cash_id;
                        })
                        ->addColumn("cash_id",function($row){
                            return $row->cash_id;
                        })
                        ->addColumn("main_visa_id",function($row){
                            return $row->main_visa_id;
                        })
                        ->addColumn("visa_id",function($row){
                            return $row->visa_id;
                        })
                        ->addColumn("created_at",function($row){
                            $date = date_format( $row->created_at, "d/m/Y h:i:s");
                            return $date;
                        }) 
                        ->setRowAttr([
                            "data-href"=> function ($row) { return action('PosBranchController@show', [$row->id]) ; }  
                        ])
                        ->rawColumns(['actions','name','pattern','store_id','invoice_scheme',"main_cash_id",'cash_id','main_visa_id','visa_id','created_at'])
                        ->make(true);
        }

        
        return view("pos.index")->with(compact(["business_locations","invoice_layout","invoice_schemes","pattern_name"]));
    }
    public function create()
    {
        
        $business_id   = request()->session()->get("user.business_id");
        $business_locations         = \App\BusinessLocation::allLocation($business_id);
        $default_business_locations = \App\BusinessLocation::FirstLocation($business_id);
        $invoice_layout             = \App\InvoiceLayout::allLocation($business_id);
        $default_invoice_layout     = \App\InvoiceLayout::FirstLocation($business_id);
        
        $invoice_schemes            = \App\InvoiceScheme::allLocation($business_id);
        $default_invoice_schemes    = \App\InvoiceScheme::FirstLocation($business_id);
        

        $business = \App\Business::find($business_id);

        if(!empty($business)){
            $bank = $business->bank;
            $cash = $business->cash;
        }else{
            $bank = 1;
            $cash = 1;
        }
        
        $a_cash  =  \App\Account::where("business_id",$business_id)->where("account_type_id",$cash)->get(); 
        $a_visa  =  \App\Account::where("business_id",$business_id)->where("account_type_id",$bank)->get();
        $store   =  \App\Models\Warehouse::where("business_id",$business_id)->where("status",1)->get();
        $pattern =  \App\Models\Pattern::where("business_id",$business_id)->get();
     
        $accounts_cash = [];
        $accounts_visa = [] ;
        $stores        = [] ;
        foreach($a_cash as $it){
            $accounts_cash[$it->id] = $it->name . " || " . $it->account_number ;
        }   

        foreach($a_visa as $it){
            $accounts_visa[$it->id] = $it->name . " || " . $it->account_number ;
        }
        foreach($store as $it){
            $stores[$it->id] = $it->name   ;
        }
        foreach($pattern as $it){
            $patterns[$it->id] = $it->name   ;
        }
   
        return view("pos.create")
                   ->with(compact(["business_locations",
                   "invoice_schemes",
                   "default_invoice_layout",
                   "default_business_locations",
                   "invoice_layout",
                   "default_invoice_schemes",
                   "accounts_cash",
                   "patterns",
                   "stores",
                   "accounts_visa"]));
    }
    public function Pos()
    {
        $pos = \App\Models\PosBranch::select()->get();
        return view("pos.go_to_pos")
                   ->with(compact([
                    "pos",
                    ]));
    }
    public function update($id)
    {
        try{
            $output = [
                "success"=>1,
                "msg"=>  __("messages.updated_successfully")
                ];
        }catch(Exception $e){
            $output = [
                "success"=>0,
                "msg"=>  __("messages.some_thing_wrong")
                ];
        }
        
        return $output;
    }
   
    public function store(Request $request)
    { 
        // if(auth()->user()->can("pos.create")){
        //     abort(403,"Utherization actions");
        // }


        try{
            $business_id   = request()->session()->get("user.business_id");
            $data          = $request->only(["pos","pattern","store","invoice_scheme","cash_main","cash","visa_main","visa"]);
            
            \App\Models\PosBranch::create_pos($business_id,$data);

            $output = [
                "success"=>1,
                "msg"=>  __("messages.updated_successfully")
                ];
        }catch(Exception $e){
            $output = [
                "success"=>0,
                "msg"=>  __("messages.some_thing_wrong")
                ];
        }
        
        return redirect()->back()->with("status",$output);
     }
    public function edit($id)
    {
      return view("pos.edit");
    }
    public function show($id)
    {
        return view("pos.show");
    }


}
