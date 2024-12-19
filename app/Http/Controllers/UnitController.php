<?php

namespace App\Http\Controllers;

use App\Unit;
use App\Product;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use App\Utils\Util;

class UnitController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // *1* section permissions
        if (!auth()->user()->can('unit.view') && !auth()->user()->can('unit.create') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }
        // *2* request by ajax 
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $unit = Unit::where('business_id', $business_id)->whereNull('product_id')->orderBy('id','asc')->with(['base_unit'])
                         ->select(['actual_name','default','in_product', 'short_name', 'allow_decimal', 'id','base_unit_id', 'base_unit_multiplier']);

            return Datatables::of($unit)
                // ... action button
                ->addColumn(
                    'action',
                    '@can("unit.update")
                        <button data-href="{{action(\'UnitController@edit\', [$id])}}" class="btn btn-xs btn-info edit_unit_button">
                            <i class="glyphicon glyphicon-edit"></i>
                            @lang("messages.edit")
                        </button>
                        &nbsp;
                    @endcan
                    @if(request()->session()->get("user.id") == 1)
                        <button data-href="{{action(\'UnitController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_unit_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endif 
                    &nbsp;
                    <button @if($default != 1) data-href="{{action(\'UnitController@Default\', ["id" => $id])}}" @endif class="btn btn-xs btn-primary default_button" @if($default == 1) style="opacity:0.5" @endif>
                        @if($default == 1)  
                            @lang("Default")  
                        @else
                            @lang("Not Default")  
                        @endif
                    </button> &nbsp;
                    <button @if($in_product != 1) data-href="{{action(\'UnitController@InPrice\', ["id"=>$id])}}" @endif class="btn btn-xs btn-second In_Price_button" @if($in_product != 0) style="opacity:0.5" @endif>
                        @if($in_product != 0)  
                            @lang("In Price")
                        @else
                            @lang("Not In Price")
                        @endif
                    </button> &nbsp;
                    <button @if($in_product != 0) data-href="{{action(\'UnitController@InPrice\', ["id"=>$id,"unCheck"=>1])}}" @endif class="btn btn-xs btn-danger In_Price_button" @if($in_product != 1) style="opacity:0.5" @endif>
                            @lang("Out")
                    </button> &nbsp;
                    '
                )
                ->editColumn('allow_decimal', function ($row) {
                    if ($row->allow_decimal) {
                        return __('messages.yes');
                    } else {
                        return __('messages.no');
                    }
                })
                ->editColumn('actual_name', function ($row) {
                    if (!empty($row->base_unit_id)) {
                        return  $row->actual_name . ' (' . (float)$row->base_unit_multiplier . $row->short_name . ')';
                    }
                    return  $row->actual_name;
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('unit.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('unit.create') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $units = Unit::forDropdown($business_id);

        return view('unit.create')
                ->with(compact('quick_add', 'units'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Add()
    {
        if (!auth()->user()->can('unit.create') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $units = Unit::forDropdown($business_id);

        return view('unit.create_add')
                ->with(compact('quick_add', 'units'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('unit.create') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['actual_name', 'short_name', 'allow_decimal']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            if ($request->has('define_base_unit')) {
                if (!empty($request->input('base_unit_id')) && !empty($request->input('base_unit_multiplier'))) {
                    $base_unit_multiplier = $this->commonUtil->num_uf($request->input('base_unit_multiplier'));
                    if ($base_unit_multiplier != 0) {
                        $input['base_unit_id'] = $request->input('base_unit_id');
                        $input['base_unit_multiplier'] = $base_unit_multiplier;
                    }
                }
            }

            $unit = Unit::create($input);
            $output = ['success' => true,
                        'data' => $unit,
                        'msg' => __("unit.added_success")
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];
        }

        return $output;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('unit.update') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $unit = Unit::where('business_id', $business_id)->find($id);

            $units = Unit::forDropdown($business_id);

            return view('unit.edit')
                ->with(compact('unit', 'units'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('unit.update') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['actual_name', 'short_name', 'allow_decimal']);
                $business_id = $request->session()->get('user.business_id');

                $unit = Unit::where('business_id', $business_id)->findOrFail($id);
                $unit->actual_name = $input['actual_name'];
                $unit->short_name = $input['short_name'];
                $unit->allow_decimal = $input['allow_decimal'];
                if ($request->has('define_base_unit')) {
                    if (!empty($request->input('base_unit_id')) && !empty($request->input('base_unit_multiplier'))) {
                        $base_unit_multiplier = $this->commonUtil->num_uf($request->input('base_unit_multiplier'));
                        if ($base_unit_multiplier != 0) {
                            $unit->base_unit_id = $request->input('base_unit_id');
                            $unit->base_unit_multiplier = $base_unit_multiplier;
                        }
                    }
                } else {
                    $unit->base_unit_id = null;
                    $unit->base_unit_multiplier = null;
                }

                $unit->save();

                $output = ['success' => true,
                            'msg' => __("unit.updated_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('unit.delete') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $unit = Unit::where('business_id', $business_id)->findOrFail($id);

                //check if any product associated with the unit
                $exists = Product::where('unit_id', $unit->id)
                                ->exists();
                if (!$exists) {
                    $unit->delete();
                    $output = ['success' => true,
                            'msg' => __("unit.deleted_success")
                            ];
                } else {
                    $output = ['success' => false,
                            'msg' => __("lang_v1.unit_cannot_be_deleted")
                            ];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => '__("messages.something_went_wrong")'
                        ];
            }

            return $output;
        }
    }

    //  update all units if was multiple but not used now !?
    public function updateUnit($id)
    {
        if (!auth()->user()->can('unit.create') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['actual_name', 'short_name', 'allow_decimal']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            if ($request->has('define_base_unit')) {
                if (!empty($request->input('base_unit_id')) && !empty($request->input('base_unit_multiplier'))) {
                    $base_unit_multiplier = $this->commonUtil->num_uf($request->input('base_unit_multiplier'));
                    if ($base_unit_multiplier != 0) {
                        $input['base_unit_id'] = $request->input('base_unit_id');
                        $input['base_unit_multiplier'] = $base_unit_multiplier;
                    }
                }
            }

            $unit = Unit::create($input);
            $output = ['success' => true,
                        'data' => $unit,
                        'msg' => __("unit.added_success")
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];
        }

        return $output;
    }

    // *10* section choose default unit 
    public function Default(){
        
        if(request()->ajax()){
            $id          = request()->input("id");
            $unall       = \App\Unit::where("id","!=",$id)->get();  
            foreach( $unall as $uns){
                $uns->default = 0;
                $uns->update();
            }
            $un          = \App\Unit::find($id); 
            $un->default = 1;
            $un->update();
            $output = [
                "success"=>true,
                "msg"=>"Updated Successfully",
            ];
            return $output;
        }
    }
    
    
    // *11* section choose in price unit 
    public function InPrice(){
        
        if(request()->ajax()){
            if(request()->input("unCheck") == null){
                $id          = request()->input("id");
                $count       = \App\Unit::where("in_product",1)->where("id","!=",$id);  
                if($count->get()->count() <= 2 ){
                    $un              = \App\Unit::find($id); 
                    $un->in_product  = 1;
                    $un->update();
                }else{
                    $id_old          = $count->orderby("id","desc")->first();
                    $un              = \App\Unit::find($id); 
                    $un->in_product  = 1;
                    $un->update();       
                    $old             = \App\Unit::find($id_old->id); 
                    $old->in_product = 0;
                    $old->update();       
                } 
                $output = [
                    "success"=>true,
                    "msg"=>"Updated Successfully",
                ];
                return $output;
            }else{
                $id                 = request()->input("id");
                $count              = \App\Unit::find($id);  
                $count->in_product  = 0;
                $count->update();       
                $output = [
                    "success" => true,
                    "msg"     => "Updated Successfully",
                ];
                return $output;
            }
        }
    }

    // *12* section change unit 
    public function Change(){
        
        if(request()->ajax()){
            $id          = request()->input("id");
            $unall       = \App\Unit::where("id","!=",$id)->whereNull("product_id")->get();  
            $unit        = [];
            foreach( $unall as $uns){
               $unit[$uns->id] =   $uns->actual_name . " ( " . $uns->short_name . " ) "; 
            }
            $output = [
                "success" => true,
                "list"    => $unit,
                "msg"     => "Updated Successfully",
            ];
            return $output;
        }
    }
}
