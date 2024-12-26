<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
class StatusLiveController extends Controller
{
    public function index()
    {
        if(!auth()->user()->can("status_view.index")){
            abort(403,"Unauthorized action.");
        }
        $business_id = request()->session()->get("user.business_id");
        $array = [];
        $array_item["items"] = [];
        $allData  = \App\Models\StatusLive::where("business_id",$business_id)->groupBy('transaction_id')->get();
        $array_refe = [] ;
        foreach($allData as $data){
                $array_refe[$data->reference_no] =  $data->reference_no;
        }
        if(request()->ajax()){
         
            if(!empty(request()->reference_no)){
                $reference_no = request()->reference_no;
                $allData->where("transaction_id",$reference_no);
            }
            return DataTables::of($allData)
                ->addColumn('action', function ($row) {
                        $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                        </button> 
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        if (auth()->user()->can("purchase.view")) {
                            $html .= '<li><a href="#" data-href="' . action('StatusLiveController@show', [$row->transaction_id]) . '" class="btn-modal" data-container=".view_modals"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                        }
                        $html .="</ul></div>";
                    return $html;
                
                })
                ->addColumn('reference_no', function ($row) {
                    

                    return $row->reference_no ;
                })
                ->addColumn('created_at', function ($row) {
                    
                    return date_format($row->created_at,"Y-m-d") ;
                }) 
                ->rawColumns(['action','reference_no','created_at'])
                ->make(true)
                ;
        }
        
        return view("status_live.index")->with(compact(["business_id","array_refe"]));
    }
    public function show($id)
    {
        if(!auth()->user()->can("status_view.index")){
            abort(403,"Unauthorized action.");
        }
        $business_id = request()->session()->get("user.business_id");
        $allData     = \App\Models\StatusLive::where("business_id",$business_id)->where("transaction_id",$id)->get();
       return view("status_live.show")->with(compact("business_id","allData"));
    }
}
