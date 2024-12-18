<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
class QuotationController extends Controller
{
    
    public function index(Request $request)
    {
        if (!auth()->user()->can('sell.create')  && !auth()->user()->can('SalesMan.views')&& !auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
       
        $q_name = \App\Models\QuatationTerm::names($business_id); 
       

        if(request()->ajax()){
            $terms = \App\Models\QuatationTerm::where("business_id",$business_id);

            if(!empty(request()->name)){
              
                $name = request()->name;
                $terms->where("id",$name);
            }
            
            return Datatables::of($terms)
             ->addColumn('actions', function ($row) {
                $html = '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                data-toggle="dropdown" aria-expanded="false">' .
                __("messages.actions") .
                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                </span>
                </button> 
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                if (auth()->user()->can("sell.create")) {
                    $html .= '<li><a href="#" data-href="' . action('QuotationController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                }
                if (auth()->user()->can("sell.create")) {

                    $html .= '<li><a href="'.\URL::to('sells/edit-terms/'.$row->id).'"  target="_blank" ><i class="fas fa-edit" aria-hidden="true"></i>'. __("messages.edit") .'</a></li>';
                
                }
                if ( request()->session()->get("user.id") == 1 || request()->session()->get("user.id") == 7 || request()->session()->get("user.id") == 8) {
                    $html .= '<li><a  data-href="' . action('QuotationController@destroy', [$row->id]) . '" class="delete-term"><i class="fas fa-trash"></i>' . __("messages.delete") . '</a></li>';
                }
                $html .=  '</ul></div>';
                return $html;
            })->addColumn("name",function($row){
                return $row->name;
            })
            ->addColumn("description",function($row){
                return  $row->description ;
            })
            ->addColumn("created_at",function($row){
              return $row->created_at;
            })
            ->setRowAttr([
                'data-href' => function ($row) {
                    if (auth()->user()->can("sell.create")) {
                        return  action('QuotationController@show', [$row->id]) ;
                    } else {
                        return '';
                    }
            }])
            ->rawColumns(["actions","created_at","description","name"])
            ->make(true);
        }


        return view("quotation_term.index")->with(compact("business_id","q_name"));

    }
    public function create()
    {
        if (!auth()->user()->can('sell.create')  && !auth()->user()->can('SalesMan.views')&& !auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        return view("quotation_term.create")->with(compact("business_id"));
    }
    public function show($id)
    {
        if (!auth()->user()->can('sell.create')  && !auth()->user()->can('SalesMan.views')&& !auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }
        $term = \App\Models\QuatationTerm::find($id) ;  
        $business_id = request()->session()->get('user.business_id');
        return view("quotation_term.show")->with(compact("business_id","term"));
    }
    public function edit($id)
    {
        $term = \App\Models\QuatationTerm::find($id);

        return view("quotation_term.edit")->with(compact(["term"]));
    }

    public function store(Request $request)
    {

        if (!auth()->user()->can('sell.create') && !auth()->user()->can('SalesMan.views')&& !auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        try{

            \App\Models\QuatationTerm::create($request,$business_id);
            
            $output = ['success' => 1,
                    'msg' => __('lang_v1.add_terms')
                ];
        }catch(Exeption $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
            return back()->with('status', $output);
        }

        return redirect('/sells/terms')->with('status', $output);
        ;
    }


    public function update($id,Request $request)
    {
        if (!auth()->user()->can('sell.create')  && !auth()->user()->can('SalesMan.views')&& !auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        try{

            \App\Models\QuatationTerm::update_term($id,$request);
            
            $output = ['success' => 1,
                    'msg' => __('lang_v1.update_terms')
                ];
        }catch(Exeption $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
            return back()->with('status', $output);
        }

        return redirect('/sells/terms')->with('status', $output);
        ;
    }


    public function destroy($id)
    {
        if (!auth()->user()->can('sell.create')  && !auth()->user()->can('SalesMan.views')&& !auth()->user()->can('admin_supervisor.views') ) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = request()->session()->get('user.business_id');

                DB::beginTransaction();

                \App\Models\QuatationTerm::delete_term($id);
               
                DB::commit();

                $output = ['success' => true,
                            'msg' => __('lang_v1.delete_terms')
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                           'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;
    }
}
