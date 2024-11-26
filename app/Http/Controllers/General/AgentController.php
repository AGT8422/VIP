<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $allData =  Agent::OrderBy('id','desc')->where('business_id',$business_id)
                            ->where('name','LIKE','%'.$request->name.'%')
                            ->paginate(30);
        return view('agents.index')
                ->with(compact('allData'));

    }
    public function add()
    {        
        return view('agents.add')
                ;
    }
    public function post_add(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $data        = new Agent;
        $data->name  = $request->name;
        $data->phone = $request->phone;
        $data->business_id = $business_id;
        $data->save();
        return redirect('agents')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function edit($id)
    {
        $data  =  Agent::find($id);
        return view('agents.edit')
                ->with('data',$data)
                ;
    }
    public function post_edit(Request $request,$id)
    {
        $business_id = request()->session()->get('user.business_id');
        $data        = Agent::find($id);
        $data->name  = $request->name;
        $data->phone = $request->phone;
        $data->business_id = $business_id;
        $data->save();
        return redirect('agents')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function delete($id)
    {
        $sdata =  Agent::find($id);
        if ($data) {
            $data->delete();
        }
        return redirect('agents')
                ->with('yes',trans('home.Done Successfully'));
    }
}
