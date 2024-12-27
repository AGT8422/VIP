<?php

namespace App\Http\Controllers;

use App\SellingPriceGroup;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         
        #....... check if admin    
        $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
        if (!$is_admin && !auth()->user()->can('roles.view')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $user_id     = request()->session()->get('user.id');
            $roles       = Role::Orderby("id","asc")->where('business_id', $business_id)->select(['name', 'id', 'is_default', 'business_id']);

            return DataTables::of($roles)
                ->addColumn('action', function ($row) use($user_id,$is_admin) {
                    if (!$row->is_default || $row->name == "Cashier#" . $row->business_id) {
                        $action = '';
                        if (auth()->user()->can('roles.update')) {
                            $action .= '<a href="' . action('RoleController@edit', [$row->id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>&nbsp;&nbsp;' . __("messages.edit") . '</a>';
                        }
                        if ($is_admin || auth()->user()->can('roles.delete')) {
                            $action .= '&nbsp
                                <button data-href="' . action('RoleController@destroy', [$row->id]) . '" class="btn btn-xs btn-danger delete_role_button"><i class="glyphicon glyphicon-trash"></i>&nbsp;&nbsp;' . __("messages.delete") . '</button>';
                        }
                        
                        return $action;
                    } else {
                        return '';
                    }
                })
                ->editColumn('name', function ($row) use ($business_id) {
                    $role_name = str_replace('#'. $business_id, '', $row->name);
                    if (in_array($role_name, ['Admin', 'Cashier'])) {
                        $role_name = __('lang_v1.' . $role_name);
                    }
                    return $role_name;
                })
                ->removeColumn('id')
                ->removeColumn('is_default')
                ->removeColumn('business_id')
                ->rawColumns([1])
                ->make(false);
        }

        return view('role.index')->with("user_id",$user_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        #....... check if admin    
        $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
        if (!$is_admin && !auth()->user()->can('roles.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $user_id     = request()->session()->get('user.id');

        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
                                    ->active()
                                    ->get();

        $module_permissions = $this->moduleUtil->getModuleData('user_permissions');
        $role               = new Role();
        $role_permissions   = [];

        return view('role.edit')
            ->with(compact('role','selling_price_groups', 'module_permissions','role_permissions'));
    }


    /**  COMPARED 11-10-2023
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // (1) Permission check
        #....... check if admin    
        $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
        if (!$is_admin  && !auth()->user()->can('roles.create')) {
            abort(403, 'Unauthorized action.');
        }
        // (2) Function check
        try {
            // ** 1 ** role information
            $role_name   = $request->input('name');
            $permissions = $request->input('permissions');
            $business_id = $request->session()->get('user.business_id');
            $user_id     = $request->session()->get('user.id');
            
            // ** collect the count of role with this name
            $count       = Role::where('name', $role_name . '#' . $business_id)->where('business_id', $business_id)->count();

            if ($count == 0) {
                // *1* services staff ( exam : hairdressers salon )
                $is_service_staff = 0;
                if ($request->input('is_service_staff') == 1) {
                    $is_service_staff = 1;
                }

                // *2* Include selling price group permissions
                $spg_permissions = $request->input('spg_permissions');
                if (!empty($spg_permissions)) {
                    foreach ($spg_permissions as $spg_permission) {
                        $permissions[] = $spg_permission;
                    }
                }
                // *3* create the role
                $role = Role::create([
                            'name'             => $role_name . '#' . $business_id ,
                            'business_id'      => $business_id,
                            'is_service_staff' => $is_service_staff
                        ]);

                // *4* create the permission 
                $this->__createPermissionIfNotExists($permissions);

                // *5* Sync the Permission
                if (!empty($permissions)) {
                    $role->syncPermissions($permissions);
                }
                // *6* messages response 
                $output = ['success' => 1,
                            'msg' => __("user.role_added")
                        ];
            } else {
                // *6* messages response
                $output = ['success' => 0,
                            'msg' => __("user.role_already_exists")
                        ];
            }
        } catch (\Exception $e) {
                // *6* messages Error
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }
        // (3) Final
        return redirect('roles')->with('status', $output);
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
        #....... check if admin    
        $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
        if (!$is_admin && !auth()->user()->can('roles.update')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $user_id     = request()->session()->get('user.id');
        $role        = Role::where('business_id', $business_id)->with(['permissions'])->find($id);
        
        $role_permissions = [];
        foreach ($role->permissions as $role_perm) {
            $role_permissions[] = $role_perm->name;
        }

        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->get();
        $module_permissions   = $this->moduleUtil->getModuleData('user_permissions');

        return view('role.edit')
            ->with(compact('role', 'role_permissions', 'selling_price_groups', 'module_permissions'));
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
        #....... check if admin    
        $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
        if (!$is_admin && !auth()->user()->can('roles.update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $role_name   = $request->input('name');
            $permissions = $request->input('permissions');
            $business_id = $request->session()->get('user.business_id');
            $user_id     = $request->session()->get('user.id');
            $count       = Role::where('name', $role_name . '#' . $business_id)
                            ->where('id', '!=', $id)
                            ->where('business_id', $business_id)
                            ->count();
            dd($role_name,$permissions,$business_id);
            if ($count == 0) {
                $role = Role::findOrFail($id);

                if (!$role->is_default || $role->name == 'Cashier#' . $business_id) {
                    if ($role->name == 'Cashier#' . $business_id) {
                        $role->is_default = 0;
                    }

                    $is_service_staff = 0;
                    if ($request->input('is_service_staff') == 1) {
                        $is_service_staff = 1;
                    }
                    $role->is_service_staff = $is_service_staff;
                    $role->name = $role_name . '#' . $business_id;
                    $role->save();

                    //Include selling price group permissions
                    $spg_permissions = $request->input('spg_permissions');
                    if (!empty($spg_permissions)) {
                        foreach ($spg_permissions as $spg_permission) {
                            $permissions[] = $spg_permission;
                        }
                    }

                    $this->__createPermissionIfNotExists($permissions);

                    if (!empty($permissions)) {
                        $role->syncPermissions($permissions);
                    }

                    $output = ['success' => 1,
                            'msg' => __("user.role_updated")
                        ];
                } else {
                    $output = ['success' => 0,
                            'msg' => __("user.role_is_default")
                        ];
                }
            } else {
                $output = ['success' => 0,
                            'msg' => __("user.role_already_exists")
                        ];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            // 'msg' => __("messages.something_went_wrong")
                            'msg' =>"File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }

        return redirect('roles')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { 
        #....... check if admin    
        $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
        if (!$is_admin && !auth()->user()->can('roles.delete')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;
                $user_id     = request()->session()->get('user.id');

                $role = Role::where('business_id', $business_id)->find($id);

                if (!$role->is_default || $role->name == 'Cashier#' . $business_id) {
                    $role->delete();
                    $output = ['success' => true,
                            'msg' => __("user.role_deleted")
                            ];
                } else {
                    $output = ['success' => 0,
                            'msg' => __("user.role_is_default")
                        ];
                }
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
     * Creates new permission if doesn't exist
     *
     * @param  array  $permissions
     * @return void
     */
    private function __createPermissionIfNotExists($permissions)
    {
        $existing_permissions      = Permission::whereIn('name', $permissions)->pluck('name')->toArray();
        $non_existing_permissions  = array_diff($permissions, $existing_permissions);

        if (!empty($non_existing_permissions)) {
            foreach ($non_existing_permissions as $new_permission) {
                $time_stamp = \Carbon::now()->toDateTimeString();
                Permission::create([
                    'name'       => $new_permission,
                    'guard_name' => 'web'
                ]);
            }
        }
    }
}
