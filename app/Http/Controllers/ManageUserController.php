<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\System;
use App\User;
use App\Utils\ModuleUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;
use App\Models\IzoUser;


class ManageUserController extends Controller
{
    /**
     * Constructor
     *
     * @param Util $commonUtil
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
        if (!auth()->user()->can('user.view') && !auth()->user()->can('user.create') && !auth()->user()->can("ReadOnly.views")) {
            abort(403, 'Unauthorized action.');
        }
        $user_id     = request()->session()->get('user.id');
        if ($user_id != 1 && $user_id != 7) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $user_id     = request()->session()->get('user.id');
            
       
            $users = User::where('business_id', $business_id)->where('username',"!=","IZO")
                        ->user()
                        ->where('is_cmmsn_agnt', 0)
                        ->select(['id', 'username',
                            DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"), 'email', 'allow_login']);

            if($user_id != 1){
                    $users->where("id","!=",1);
            }             

            return Datatables::of($users)
                ->editColumn('username', '{{$username}} @if(empty($allow_login)) <span class="label bg-gray">@lang("lang_v1.login_not_allowed")</span>@endif')
                ->addColumn(
                    'role',
                    function ($row) {
                        $role_name = $this->moduleUtil->getUserRoleName($row->id);
                        return $role_name;
                    }
                )
                ->addColumn(
                    'action',function ($row) use($user_id) {
                        $html = '<a href="'. action('ManageUserController@edit', [$row->id]) .'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>'. __("messages.edit").'</a>
                                &nbsp;';

                        $html .=' <a href="'.action('ManageUserController@show', [$row->id]).'" class="btn btn-xs btn-info"><i class="fa fa-eye"></i>'. __("messages.view").'</a>&nbsp; ';
                        
                        if($user_id == 1 && $row->id != 1){
                          $html.=  '<button data-href="'.action('ManageUserController@destroy', [$row->id]).'" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i>'.__("messages.delete").'</button>';

                        }
                        return $html;
                    } )
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'username'])
                ->make(true);
                
        }

        return view('manage_user.index')->with("user_id",$user_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         
        if (!auth()->user()->can('user.create')     ) {
            abort(403, 'Unauthorized action.');
        }
        $user_id     = request()->session()->get('user.id');
        if ($user_id != 1   && $user_id != 7) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse();
            }elseif (!$this->moduleUtil->isQuotaAvailable('users', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('users', $business_id, action('ManageUserController@index'));
            }
        } elseif (!$this->moduleUtil->isQuotaAvailable('users', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('users', $business_id, action('ManageUserController@index'));
        }
        $patterns  = [];
        $patterns_ = \App\Models\Pattern::select()->get();
        foreach($patterns_ as $it){
                $patterns[$it->id] = $it->name;
        }
        $roles        = $this->getRolesArray($business_id);
        $username_ext = $this->getUsernameExtension();
        $contacts     = Contact::contactDropdown($business_id, true, false);
        $locations    = BusinessLocation::where('business_id', $business_id)
                                        ->Active()
                                        ->get();
        //  agents
        $agents  = [] ;
        $us               = \App\User::where('business_id', $business_id)
                                        ->where('is_cmmsn_agnt', 1)->get();
        foreach($us as $it){
            $agents[$it->id] = $it->first_name;
        }
        $allLocations = [];
        foreach($locations as $e){
            $allLocations[$e->id] = $e->name;
        }
        $ta                  = \App\TaxRate::where("business_id",$business_id)->get();
        $taxes               = [];
        foreach($ta as $i){
            $taxes[$i->id] = $i->name;
        }
        //   cost center
        $account_cost = \App\Account::where("cost_center",1)->get();
        $cost_center = [];
        foreach($account_cost as $i){
            $cost_center[$i->id]= $i->name . " || " . $i->account_number;
        }
        // stores
        $stores = [];
        $mainstore =\App\Models\Warehouse::where('business_id', $business_id)->select(['name','status','id'])->get();
        if (!empty($mainstore)) {
            foreach ($mainstore as $mainstor) {
                if($mainstor->status == 1){
                    $stores[$mainstor->id] = $mainstor->name;
                
                }
            }
                   
        }
        
        $accounts = \App\Account::items();
        //Get user form part from modules
        $form_partials = $this->moduleUtil->getModuleData('moduleViewPartials', ['view' => 'manage_user.create']);

        return view('manage_user.create')
                ->with(compact('roles','accounts','taxes','stores', 'agents','cost_center', 'username_ext', 'patterns' ,'allLocations','contacts', 'locations', 'form_partials'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
            if (!auth()->user()->can('user.create')) {
                abort(403, 'Unauthorized action.');
            }
            $user_id     = request()->session()->get('user.id');
            if ($user_id != 1   && $user_id != 7) {
                abort(403, 'Unauthorized action.');
            }
            try {

                \DB::beginTransaction();

                \Config::set('database.connections.mysql.database', "izocloud");
                \DB::purge('mysql');
                \DB::reconnect('mysql');
                $izoCustomer        = IzoUser::where("email",request()->session()->get('user_main.email'))->first(); 
                $TotalizoCustomer   = IzoUser::where("parent_admin",$izoCustomer->id)->get()->count(); 
                
                $listOfUsers        = IzoUser::pluck("email")->toArray();
                 
                if(in_array($request->email,$listOfUsers) || in_array($request->username,$listOfUsers) ){
                    $output = [
                        'success' => 0,
                         'msg' => __("izo.sorry_this_email_exist")
                    ];
                    return redirect('users')->with('status', $output) ;
                }
                 
                if($TotalizoCustomer>=$izoCustomer->seats){
                    $output = [
                        'success' => 0,
                         'msg' => __("izo.sorry_more_user")
                    ];
                    return redirect('users')->with('status', $output) ;
                }
                $newIzoCustomer                = $izoCustomer->replicate();
                // $newIzoCustomer->surname       = $request->surname;
                $newIzoCustomer->mobile        = $request->contact_number;
                $newIzoCustomer->email         = $request->email;
                $newIzoCustomer->password      = Hash::make($request->password);;
                $newIzoCustomer->device_id     = $request->header('User-Agent');
                $newIzoCustomer->ip            = $request->ip();
                $newIzoCustomer->status        = "company_user" ;
                $newIzoCustomer->admin_user    = 0 ;
                $newIzoCustomer->parent_admin  = $izoCustomer->id ;
                $newIzoCustomer->save();
                $idIzoCustomer = $newIzoCustomer->id; 
                $databaseName  = request()->session()->get('user_main.database') ;  
                \Config::set('database.connections.mysql.database', $databaseName);
                \DB::purge('mysql');
                \DB::reconnect('mysql');

                $user_details = $request->only(['surname', 'first_name', 'last_name',
                    'username', 'email', 'password', 'selected_contacts', 'marital_status',
                    'blood_group', 'contact_number', 'fb_link', 'twitter_link', 'social_media_1',
                    'social_media_2', 'permanent_address', 'current_address',
                    'guardian_name', 'custom_field_1', 'custom_field_2','user_account_id','user_visa_account_id','user_store_id','user_cost_center_id','user_agent_id','user_pattern_id',
                    'custom_field_3', 'custom_field_4', 'id_proof_name','pattern_id',
                    'id_proof_number', 'cmmsn_percent', 'gender',
                    'max_sales_discount_percent', 'family_number', 'alt_number'
                ]);
                $pattern = [];
                if($user_details['pattern_id'] != null){ 
                     foreach($user_details['pattern_id'] as $it){
                        array_push($pattern,$it);
                    }
                }

                $user_details['pattern_id'] = json_encode($pattern)   ;
                $user_details['status']     = !empty($request->input('is_active')) ? 'active' : 'inactive';
                
                $user_details['user_type']  = 'user';

                if (empty($request->input('allow_login'))) {
                    unset($user_details['username']);
                    unset($user_details['password']);
                    $user_details['allow_login'] = 0;
                } else {
                    $user_details['allow_login'] = 1;
                }
                
                if (!isset($user_details['selected_contacts'])) {
                    $user_details['selected_contacts'] = false;
                }
                if (!empty($request->input('dob'))) {
                    $user_details['dob'] = $this->moduleUtil->uf_date($request->input('dob'));
                }

                if (!empty($request->input('bank_details'))) {
                    $user_details['bank_details'] = json_encode($request->input('bank_details'));
                }

                $business_id = $request->session()->get('user.business_id');
                

                $user_details['business_id'] = $business_id;
                $user_details['password'] = $user_details['allow_login'] ? Hash::make($user_details['password']) : null;

                if ($user_details['allow_login']) {
                    $ref_count = $this->moduleUtil->setAndGetReferenceCount('username');
                    if (blank($user_details['username'])) {
                        $user_details['username'] = $this->moduleUtil->generateReferenceNumber('username', $ref_count);
                    }

                    $username_ext = $this->getUsernameExtension();
                    if (!empty($username_ext)) {
                        $user_details['username'] .= $username_ext;
                    }
                }
                
                //Check if subscribed or not, then check for users quota
                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                        return $this->moduleUtil->expiredResponse();
                    }elseif (!$this->moduleUtil->isQuotaAvailable('users', $business_id)) {
                        return $this->moduleUtil->quotaExpiredResponse('users', $business_id, action('ManageUserController@index'));
                    }
                } elseif (!$this->moduleUtil->isQuotaAvailable('users', $business_id)) {
                    return $this->moduleUtil->quotaExpiredResponse('users', $business_id, action('ManageUserController@index'));
                }

                //Sales commission percentage
                $user_details['cmmsn_percent'] = !empty($user_details['cmmsn_percent']) ? $this->moduleUtil->num_uf($user_details['cmmsn_percent']) : 0;

                $user_details['max_sales_discount_percent'] = !is_null($user_details['max_sales_discount_percent']) ? $this->moduleUtil->num_uf($user_details['max_sales_discount_percent']) : null;

                // set user language as its business by eng mohamed ali
                $user_details['language']     = "en";
                $user_details['izo_user_id']  = $idIzoCustomer;

                //Create the user

                
                $user = User::create($user_details);

              
         
                 

                $role_id = $request->input('role');
                $role = Role::findOrFail($role_id);
                $user->assignRole($role->name);

                //Grant Location permissions
                $this->giveLocationPermissions($user, $request);

                //Assign selected contacts
                if ($user_details['selected_contacts'] == 1) {
                    $contact_ids = $request->get('selected_contact_ids');
                    $user->contactAccess()->sync($contact_ids);
                }

                //Save module fields for user
                $this->moduleUtil->getModuleData('afterModelSaved', ['event' => 'user_saved', 'model_instance' => $user]);

                $this->moduleUtil->activityLog($user, 'added');
                \DB::commit();
                $output = ['success' => 1,
                            'msg' => __("user.user_added")
                        ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = ['success' => 0,
                            'msg' =>"File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                            // 'msg' => __("messages.something_went_wrong")
                        ];
            }
    

        return redirect('users')->with('status', $output) ;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }
        $user_id     = request()->session()->get('user.id');
        if ($user_id != 1 && $user_id != 7) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $user = User::where('business_id', $business_id)
                    ->with(['contactAccess'])
                    ->find($id);

        //Get user view part from modules
        $view_partials = $this->moduleUtil->getModuleData('moduleViewPartials', ['view' => 'manage_user.show', 'user' => $user]);

        $users = User::forDropdown($business_id, false);

        $activities = Activity::forSubject($user)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        return view('manage_user.show')->with(compact('user', 'view_partials', 'users', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }
        $user_id     = request()->session()->get('user.id');
        if ($user_id != 1) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $user        = User::where('business_id', $business_id)
                             ->with(['contactAccess'])
                             ->findOrFail($id);
        
        $roles       = $this->getRolesArray($business_id);
        $patterns    = [];
        $patterns_   = \App\Models\Pattern::select()->get();
        foreach($patterns_ as $it){
                $patterns[$it->id] = $it->name;
        }
        $contact_access   = $user->contactAccess->pluck('id')->toArray();
        $contacts         = Contact::contactDropdown($business_id, true, false);
    //  agents
        $agents           = [] ;
        $us               = \App\User::where('business_id', $business_id)
                                        ->where('is_cmmsn_agnt', 1)->get();
        foreach($us as $it){
            $agents[$it->id] = $it->first_name;
        }
    //   cost center
        $account_cost = \App\Account::where("cost_center",1)->get();
        $cost_center  = [];
        foreach($account_cost as $i){
            $cost_center[$i->id]= $i->name . " || " . $i->account_number;
        }
    // stores
        $stores    = [];
        $mainstore =\App\Models\Warehouse::where('business_id', $business_id)->select(['name','status','id'])->get();
        if (!empty($mainstore)) {
            foreach ($mainstore as $mainstor) {
                if($mainstor->status == 1){
                    $stores[$mainstor->id] = $mainstor->name;
                
                }
            }
                   
        }
        $accounts = \App\Account::items();
        if ($user->status == 'active') {
            $is_checked_checkbox = true;
        } else {
            $is_checked_checkbox = false;
        }

        $us                  = \App\User::find($id);
        $list_patterns       = json_decode($us->pattern_id);
        $locations           = BusinessLocation::where('business_id', $business_id)->get();
        $permitted_locations = $user->permitted_locations();
        // dd($permitted_locations);
        $username_ext        = $this->getUsernameExtension();
        $ta                  = \App\TaxRate::where("business_id",$business_id)->get();
        $taxes               = [];
        foreach($ta as $i){
            $taxes[$i->id] = $i->name;
        }
        $allLocations = [];
        foreach($locations as $e){
            $allLocations[$e->id] = $e->name;
        }
        //Get user form part from modules
        $form_partials = $this->moduleUtil->getModuleData('moduleViewPartials', ['view' => 'manage_user.edit', 'user' => $user]);
         
        
        return view('manage_user.edit')
                ->with(compact('roles','taxes', 'user','stores', 'agents','cost_center','list_patterns','accounts','allLocations','patterns','contact_access', 'contacts', 'is_checked_checkbox', 'locations', 'permitted_locations', 'form_partials', 'username_ext'));
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
        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }
        $user_id     = request()->session()->get('user.id');
        $business_id     = request()->session()->get('user.business_id');
        if ($user_id != 1 && $user_id != 7) {
            abort(403, 'Unauthorized action.');
        }
        try {
             
            $user      = User::where('business_id', $business_id)->findOrFail($id);
            $izoUserId = $user->izo_user_id;
            #.......................................
            \DB::beginTransaction();
            \Config::set('database.connections.mysql.database', "izocloud");
            \DB::purge('mysql');
            \DB::reconnect('mysql');
            #.......................................
            $izoCustomer        = IzoUser::where("email",request()->session()->get('user_main.email'))->first(); 
            $TotalizoCustomer   = IzoUser::where("parent_admin",$izoCustomer->id)->get()->count(); 
            $izoChildCustomer   = IzoUser::find($izoUserId); 
            #.......................................
            // $newIzoCustomer->surname       = $request->surname;
            $listOfUsers        = IzoUser::where("id","!=",$izoUserId)->pluck("email")->toArray();
                 
            if(in_array($request->email,$listOfUsers) || in_array($request->username,$listOfUsers) ){
                $output = [
                    'success' => 0,
                        'msg' => __("izo.sorry_this_email_exist")
                ];
                return redirect('users')->with('status', $output) ;
            }
            $password_has = null;
            $izoChildCustomer->mobile        = $request->contact_number;
            $izoChildCustomer->email         = $request->email;
            if($request->password != null && $request->password != ""){
                $izoChildCustomer->password      = Hash::make($request->password);
                $password_has = $izoChildCustomer->password;  
            }
            $izoChildCustomer->update();
            
            $databaseName  = request()->session()->get('user_main.database') ;  
            \Config::set('database.connections.mysql.database', $databaseName);
            \DB::purge('mysql');
            \DB::reconnect('mysql');

            $user_data = $request->only(['surname', 'first_name', 'last_name', 'email', 'selected_contacts', 'marital_status',
                'blood_group', 'contact_number', 'fb_link', 'twitter_link', 'social_media_1',
                'social_media_2', 'permanent_address', 'current_address','user_account_id','user_visa_account_id','user_store_id','user_cost_center_id','user_agent_id',
                'guardian_name', 'custom_field_1', 'custom_field_2','pattern_id','user_pattern_id',
                'custom_field_3', 'custom_field_4', 'id_proof_name', 'id_proof_number', 'cmmsn_percent', 'gender', 'max_sales_discount_percent', 'family_number', 'alt_number']);

            $user_data['status'] = !empty($request->input('is_active')) ? 'active' : 'inactive';
            $business_id = request()->session()->get('user.business_id');

            if (!isset($user_data['selected_contacts'])) {
                $user_data['selected_contacts'] = 0;
            }

            $pattern = [];
            if($user_data['pattern_id'] != null){
                 foreach($user_data['pattern_id'] as $it){
                    array_push($pattern,$it);
                }
            }

            $user_data['pattern_id'] = json_encode($pattern)   ;
            if (empty($request->input('allow_login'))) {
                $user_data['username'] = null;
                $user_data['password'] = null;
                $user_data['allow_login'] = 0;
            } else {
                $user_data['allow_login'] = 1;
            }

            if (!empty($request->input('password'))) {
                if($password_has != null){
                    $user_data['password'] = $password_has;
                }else{
                    $user_data['password'] = $user_data['allow_login'] == 1 ? Hash::make($request->input('password')) : null;
                }
            }

            //Sales commission percentage
            $user_data['cmmsn_percent'] = !empty($user_data['cmmsn_percent']) ? $this->moduleUtil->num_uf($user_data['cmmsn_percent']) : 0;

            $user_data['max_sales_discount_percent'] = !is_null($user_data['max_sales_discount_percent']) ? $this->moduleUtil->num_uf($user_data['max_sales_discount_percent']) : null;

            if (!empty($request->input('dob'))) {
                $user_data['dob'] = $this->moduleUtil->uf_date($request->input('dob'));
            }

            if (!empty($request->input('bank_details'))) {
                $user_data['bank_details'] = json_encode($request->input('bank_details'));
            }

            if ($user_data['allow_login'] && $request->has('username')) {
                $user_data['username'] = $request->input('username');
                $ref_count = $this->moduleUtil->setAndGetReferenceCount('username');
                if (blank($user_data['username'])) {
                    $user_data['username'] = $this->moduleUtil->generateReferenceNumber('username', $ref_count);
                }

                $username_ext = $this->getUsernameExtension();
                if (!empty($username_ext)) {
                    $user_data['username'] .= $username_ext;
                }
            }
            $user_data['username'] = $request->email;
            $user = User::where('business_id', $business_id)
                          ->findOrFail($id);
            $user->update($user_data);
            
            $role_id = $request->input('role');
            $user_role = $user->roles->first();
            $previous_role = !empty($user_role->id) ? $user_role->id : 0;
            if ($previous_role != $role_id) {
                if (!empty($previous_role)) {
                    $user->removeRole($user_role->name);
                }
                
                $role = Role::findOrFail($role_id);
                $user->assignRole($role->name);
            }
            //Grant Location permissions
            $this->giveLocationPermissions($user, $request);

            //Assign selected contacts
            if ($user_data['selected_contacts'] == 1) {
                $contact_ids = $request->get('selected_contact_ids');
            } else {
                $contact_ids = [];
            }
            $user->contactAccess()->sync($contact_ids);

            //Update module fields for user
            $this->moduleUtil->getModuleData('afterModelSaved', ['event' => 'user_saved', 'model_instance' => $user]);
            
            $this->moduleUtil->activityLog($user, 'edited');
            
            $output = ['success' => 1,
                    'msg' => __("user.user_update_success")
                ];
            \DB::commit();
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }

        return redirect('users')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }
        $user_id     = request()->session()->get('user.id');
        if ($user_id != 1 && $user_id != 7) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                \DB::beginTransaction();
                $business_id = request()->session()->get('user.business_id');
                $user        = User::where('business_id', $business_id)->where('id', $id)->first();
                $izoUserId   = $user->izo_user_id;
                
                \Config::set('database.connections.mysql.database', "izocloud");
                \DB::purge('mysql');
                \DB::reconnect('mysql');
                $izoChildCustomer   = IzoUser::find($izoUserId);  
                if($izoChildCustomer->admin_user == 1){
                    $output = [
                        'success' => 0,
                         'msg'    => __("izo.sorry_cant_delete_admin_user")
                    ];
                    return redirect('users')->with('status', $output) ;
                } 
                $izoChildCustomer->delete(); 
                $databaseName  = request()->session()->get('user_main.database') ;  
                \Config::set('database.connections.mysql.database', $databaseName);
                \DB::purge('mysql');
                \DB::reconnect('mysql');


                User::where('business_id', $business_id)->where('id', $id)->delete();
                \DB::commit();
                $output = ['success' => true,
                                'msg' => __("user.user_delete_success")
                                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            // 'msg' => __("messages.something_went_wrong")
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
            }

            return $output;
        }
    }

    private function getUsernameExtension()
    {
        $extension = !empty(System::getProperty('enable_business_based_username')) ? '-' .str_pad(session()->get('business.id'), 2, 0, STR_PAD_LEFT) : null;
        return $extension;
    }

    /**
     * Retrives roles array (Hides admin role from non admin users)
     *
     * @param  int  $business_id
     * @return array $roles
     */
    private function getRolesArray($business_id)
    {
        $roles_array = Role::where('business_id', $business_id)->get()->pluck('name', 'id');
        $roles = [];

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        foreach ($roles_array as $key => $value) {
            if (!$is_admin && $value == 'Admin#' . $business_id) {
                continue;
            }
            $roles[$key] = str_replace('#' . $business_id, '', $value);
        }
        return $roles;
    }

    /**
     * Adds or updates location permissions of a user
     */
    private function giveLocationPermissions($user, $request)
    {
        $permitted_locations = $user->permitted_locations();
        $permissions = $request->input('access_all_locations');
        $revoked_permissions = [];
        //If not access all location then revoke permission
        if ($permitted_locations == 'all' && $permissions != 'access_all_locations') {
            $user->revokePermissionTo('access_all_locations');
        }

        //Include location permissions
        $location_permissions = $request->input('location_permissions');
        if (empty($permissions) &&
            !empty($location_permissions)) {
            $permissions = [];
            foreach ($location_permissions as $location_permission) {
                $permissions[] = $location_permission;
            }

            if (is_array($permitted_locations)) {
                foreach ($permitted_locations as $key => $value) {
                    if (!in_array('location.' . $value, $permissions)) {
                        $revoked_permissions[] = 'location.' . $value;
                    }
                }
            }
        }

        if (!empty($revoked_permissions)) {
            $user->revokePermissionTo($revoked_permissions);
        }

        if (!empty($permissions)) {
            $user->givePermissionTo($permissions);
        } else {
            //if no location permission given revoke previous permissions
            if (!empty($permitted_locations)) {
                $revoked_permissions = [];
                foreach ($permitted_locations as $key => $value) {
                    $revoke_permissions[] = 'location.' . $value;
                }

                $user->revokePermissionTo($revoke_permissions);
            }
        }
    }

    public function log_out()
    {
         $user_id = request()->session()->get("user.id");
         $user    = \App\User::find($user_id); 
         $this->moduleUtil->activityLog($user, 'logout');
    }
}
