<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class PatternController extends Controller
{
    //**
    //.. display patterns
    //.....................
    public function index()
    {
        if(!auth()->user()->can("pattern.index")){
            abort(403, 'Unauthorized action.');
        }
        
        $business_id                = request()->session()->get("user.business_id");
        $business_locations         = \App\BusinessLocation::allLocation($business_id);
        $invoice_layout             = \App\InvoiceLayout::allLocation($business_id);
        $invoice_schemes            = \App\InvoiceScheme::allLocation($business_id);
        $pattern_name               = \App\Models\Pattern::allname($business_id);
        
        if(request()->ajax()){
                $patterns = \App\Models\Pattern::where("business_id",$business_id);
                if(!empty(request()->location_id)){
                    $location_id = request()->location_id;
                    $patterns->where("location_id" ,$location_id);
                }
                if(!empty(request()->pattern_name)){
                    $pattern_name = request()->pattern_name;
                    $patterns->where("name" ,$pattern_name);
                }
                if(!empty(request()->pattern_type)){
                    $pattern_type = request()->pattern_type;
                    $patterns->where("type" ,$pattern_type);
                }
                if(!empty(request()->invoice_scheme)){
                    $invoice_scheme = request()->invoice_scheme;
                    $patterns->where("invoice_scheme" ,$invoice_scheme);
                }
                if(!empty(request()->invoice_layout)){
                    $invoice_layout = request()->invoice_layout;
                    $patterns->where("invoice_layout" ,$invoice_layout);
                }
                if(!empty(request()->pos)){
                    $pos = request()->pos;
                    $patterns->where("pos" ,$pos);
                }
                $patterns->select()->get();

                return Datatables::of($patterns)
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
                                $html .= '<li><a href="#" data-href="' . action('PatternController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                            }
                            if (auth()->user()->can("purchase.update")) {
                                $html .= '<li><a href="' . action('PatternController@edit', [$row->id]) . '"><i class="fas fa-edit"></i>' . __("messages.edit") . '</a></li>';
                            }
                            if (auth()->user()->can("purchase.update")) {
                                $html .= '<li><a href="' . action('PatternController@DefaultPattern', [$row->id]) . '"><i class="fas fa-star"></i>' . __("home.Default") . '</a></li>';
                            }
                            // if (auth()->user()->can("purchase.delete")) {
                            //     $html .= '<li><a href="#" data-href="' . action('PatternController@destroy', [$row->id]) . '" class="delete-patterns"><i class="fas fa-trash"></i>' . __("messages.delete") . '</a></li>';
                            // }
                            $html .=  '</ul></div>';
                            return $html;
                        })
                        ->editColumn("location_id",function($row){
                            return $row->location->name;
                        })
                        ->editColumn("name",function($row){
                            $html      = $row->name;
                            if($row->default_p != 0){
                                $html .= '<br><i class="fas fa-star"></i>' ;
                            }
                            return $html;
                        })
                        ->editColumn("user_id",function($row){
                            return $row->user->username;
                        })
                        ->editColumn("type",function($row){
                            return $row->type;
                        })
                        ->editColumn("invoice_scheme",function($row){
                            return $row->scheme->name;
                        })
                        ->editColumn("pos",function($row){
                            return $row->pos;
                        })
                        ->editColumn("created_at",function($row){
                            $date = date_format( $row->created_at, "d/m/Y h:i:s");
                            return $date;
                        })
                        ->editColumn("invoice_layout",function($row){
                            return $row->layout->name;
                        })
                        ->setRowAttr([
                            "data-href"=> function ($row) { return action('PatternController@show', [$row->id]) ; }  
                        ])
                        ->rawColumns(['actions','name','location_id','user_id','invoice_scheme','invoice_layout','pos'])
                        ->make(true);
        }


        return view("patterns.index")->with(compact([
                                "business_locations",
                                "invoice_layout",
                                "invoice_schemes",
                                "pattern_name",
                            ]));
    }
    //**
    //.. create patterns
    //..
    public function create()
    {

        if(!auth()->user()->can("pattern.create")){
            abort(403, 'Unauthorized action.');
        }

        $business_id                = request()->session()->get("user.business_id");

        $business_locations         = \App\BusinessLocation::allLocation($business_id);
        $default_business_locations = \App\BusinessLocation::FirstLocation($business_id);
        
        $invoice_layout             = \App\InvoiceLayout::allLocation($business_id);
        $default_invoice_layout     = \App\InvoiceLayout::FirstLocation($business_id);
        
        $invoice_schemes            = \App\InvoiceScheme::allLocation($business_id);
        $default_invoice_schemes    = \App\InvoiceScheme::FirstLocation($business_id);
        $accounts                   = \App\Account::items();
        $printer_layout             = \App\Models\PrinterTemplate::allLocation($business_id);
        $default_printer_layout     = \App\Models\PrinterTemplate::FirstLocation($business_id);
        
        return view("patterns.create")->with(compact([
                                    "business_locations",
                                    "default_business_locations",
                                    "invoice_layout",
                                    "default_invoice_layout",
                                    "accounts",
                                    "invoice_schemes",
                                    "default_invoice_schemes",
                                    "printer_layout",
                                    "default_printer_layout",
                                ]));
                                
                            }
    //**
    //.. edit patterns
    //...................
    public function edit($id)
    {
        if(!auth()->user()->can("pattern.edit")){
            abort(403, 'Unauthorized action.');
        }
        $business_id   = request()->session()->get("user.business_id");
        $pattern       = \App\Models\Pattern::find($id);

        $business_locations         = \App\BusinessLocation::allLocation($business_id);
        $invoice_layout             = \App\InvoiceLayout::allLocation($business_id);
        $invoice_schemes            = \App\InvoiceScheme::allLocation($business_id);
        $printer_layout             = \App\Models\PrinterTemplate::allLocation($business_id);
       
        return view("patterns.edit")->with(compact([
                                        "invoice_layout",
                                        "invoice_schemes",
                                        "business_locations",
                                        "pattern",
                                        "printer_layout",
                                    ]));
        
    }
    //**
    //.. save pattern
    //.....................
    public function store(Request $request)
    {    
        if(!auth()->user()->can("pattern.save")){
            abort(403, 'Unauthorized action.');
        }
        
        try{
            $business_id   = request()->session()->get("user.business_id");
            $user_id       = request()->session()->get("user.id");
            $data          = $request->only(["name","pos","invoice_scheme","location_id","invoice_layout","code","pattern_type","printer_type"]);
            
            DB::beginTransaction();
             
            \App\Models\Pattern::create($data,$business_id,$user_id);

            DB::commit();
            
            $output = [
                "success" => 1 ,
                "msg"     => "add Successfull"
            ];

        }catch(Exception $e){

            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            $output = ["success"=> 0 ,
                    "msg"=> "Something Wrong Try Again"
            ];
        }

        return  redirect("/patterns-list")->with("status",$output);
    
    }
    //**
    //.. show pattern
    //.....................
    public function show($id)
    {   
        if(!auth()->user()->can("pattern.show")){
            abort(403, 'Unauthorized action.');
        }
        $business_id   = request()->session()->get("user.business_id");
        $pattern       = \App\Models\Pattern::find($id);
  
        
        return view("patterns.show")->with(compact([
                                    "pattern",
                                ]));

    }
    //**
    //.. update pattern
    //.....................
    public function update(Request $request,$id)
    {
        if(!auth()->user()->can("pattern.update")){
            abort(403, 'Unauthorized action.');
        }
        $business_id   = request()->session()->get("user.business_id");
        $user_id       = request()->session()->get("user.id");
        $data          = $request->only(["code","name","pos","invoice_scheme","location_id","invoice_layout","pattern_type","printer_type"]);
        
        
        try{
           
            DB::beginTransaction();
            \App\Models\Pattern::edit($id,$data,$business_id,$user_id);
            DB::commit();
            $output = ["success"=> 1 ,
                    "msg"=> "Updated Successfully"
                ];
           
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            $output = ["success"=> 0 ,
                    "msg"=> "Something Wrong Try Again"
            ];
        }
        return  redirect("/patterns-list")->with("status",$output);
    
    }
    //**
    //.. delete pattern
    //.....................
    public function destroy($id)
    {
         
        if(!auth()->user()->can("pattern.destroy")){
            abort(403, 'Unauthorized action.');
        }
        try{
            if(request()->ajax()){
                DB::beginTransaction();
                \App\Models\Pattern::remove($id);
                DB::commit();
                $output = ["success"=> 1 ,
                        "msg"=> "Deleted Successfully"
                    ];
            }
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
            $output = ["success"=> 0 ,
                    "msg"=> "Something Wrong Try Again"
            ];
        }
   
        return  $output;
    }
    // **
    //.. default pattern
    //......................
    public function DefaultPattern($id)
    {
        try{
            \App\Models\Pattern::DefaultPattern($id);
            $output = [
                          "success" => 1,
                          "msg"     => __("messages.updated_successfull"),
            ];
            return redirect()->back()->with("status",$output);
        }catch(Exception $e){
            $output = [
                "success" => 0,
                "msg"     => __("messages.something_wrong"),
            ];
            return redirect()->back()->with("status",$output);
        }   
    }

}
