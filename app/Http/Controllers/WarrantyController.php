<?php

namespace App\Http\Controllers;

use App\Warranty;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\Util;

class WarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $warranties = Warranty::where('business_id', $business_id)->where("type","=",0) 
                         ->select(['id', 'name', 'description', 'duration', 'duration_type']);

            return Datatables::of($warranties)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'WarrantyController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>'
                 )
                 ->removeColumn('id')
                 ->editColumn('duration', function ($row) {
                     return $row->duration . ' ' . __('lang_v1.' .$row->duration_type);
                 })
                 ->rawColumns(['action'])
                 ->make(true);
        }

        return view('warranties.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('warranties.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id     = request()->session()->get('user.id');

        try {
            $input = $request->only(['name', 'description', 'duration', 'duration_type']);
            $input['business_id'] = $business_id;
            $input['state_action'] = "Add";
            $input['user_id'] = $user_id;

            $type        = 'log_file';
            $ref_count   = Util::setAndGetReferenceCount($type);
            $reciept_no  = Util::generateReferenceNumber($type, $ref_count,$business_id,"LOG_");

            $input['ref_number'] = "WARRANTIY_".$reciept_no;
            $input['type']       = 0;
  
            $status = Warranty::create($input);

            $output = ['success' => true,
                        'msg' => __("lang_v1.added_success")
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
     * @param  \App\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function show(Warranty $warranty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $warranty = Warranty::where('business_id', $business_id)->find($id);

            return view('warranties.edit')
                ->with(compact('warranty'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id     = request()->session()->get('user.id');
    
        if (request()->ajax()) {
            try {

                $input = $request->only(['name', 'description', 'duration', 'duration_type']);
                //*** .. log_file section 
                $old_warranty   = Warranty::find($id);
                $log_copy       = $old_warranty->replicate();
                $log_copy->type = 1;
                $log_copy->save(); 
                //.... ** 
                $warranty = Warranty::where('business_id', $business_id)->findOrFail($id);
                $input["parent_id"]     = $log_copy->id;
                $input["state_action"]  = "Edit";
                $input['user_id']       = $user_id;
                $type                   = 'log_file';
                $ref_count              = Util::setAndGetReferenceCount($type);
                $reciept_no             = Util::generateReferenceNumber($type, $ref_count,$business_id,"LOG_");
                $input['ref_number']    = "WARRANTIY_".$reciept_no;
                $input['type']          = 0;
                $warranty->update($input);

                $output = ['success' => true,
                            'msg' => __("lang_v1.updated_success")
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
     * @param  \App\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warranty $warranty)
    {
        //
    }
}
