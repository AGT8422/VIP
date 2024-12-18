<?php

namespace App\Http\Controllers;

use App\User;
use App\AccountType;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id      = session()->get('user.business_id');
        $user_id          = request()->session()->get('user.id');
        $user             = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages        = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }

        $account_types = AccountType::where('business_id', $business_id)->get();

        return view('account_types.create')
                ->with(compact('account_types','languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input                = $request->only(['name', 'parent_account_type_id',"code"]);
            $input['business_id'] = $request->session()->get('user.business_id');

            if(!AccountType::create_new($input)){
                $output = [
                            'success' => false,
                            'msg'     => __("lang_v1.account_number_used")
                        ];
            }else{
                $output = [
                                'success' => true,
                                'msg'     => __("lang_v1.added_success")
                            ];
                
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                            'success' => false,
                            'msg'     => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AccountType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function show(AccountType $accountType)
    {
        
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AccountType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id      = session()->get('user.business_id');
        $user_id          = request()->session()->get('user.id');
        $user             = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages        = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }

        $account_type     = AccountType::where('business_id', $business_id)->findOrFail($id);

        $account_types    = AccountType::where('business_id', $business_id)->whereNull('parent_account_type_id')->get();
    

        return view('account_types.edit')
                ->with(compact('account_types', 'languages','account_type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AccountType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    { 
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input                = $request->only(['name', 'parent_account_type_id',"code"]);
            $business_id          = $request->session()->get('user.business_id');
            $input["business_id"] = $business_id;
            $account_type         = AccountType::where('business_id', $business_id)->find($id);
            
            // AccountType::update_new($input,$account_type);
            if(!AccountType::update_new($input,$account_type)){
                $output = [
                            'success' => false,
                            'msg'     => __("lang_v1.account_number_used")
                ];
            }else{
                $output = [
                            'success' => true,
                            'msg'     => __("lang_v1.added_success")
                ];
                
            }
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                            'success' => false,
                            'msg'     => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AccountType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id  = session()->get('user.business_id');

        AccountType::where('business_id', $business_id)->where('id', $id)->delete();

        //Upadete parent account if set
        AccountType::where('business_id', $business_id)->where('parent_account_type_id', $id)->update(['parent_account_type_id' => null]);

        $output = [         
                            'success' => true,
                            'msg'     => __("lang_v1.deleted_success")
                        ];

        return redirect()->back()->with('status', $output);
    }
}
