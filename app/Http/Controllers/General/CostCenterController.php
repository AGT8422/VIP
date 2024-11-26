<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Account;

class CostCenterController extends Controller
{
    public function index()
    {
        $business_id    =  request()->session()->get('user.business_id');
        $allData        =  Account::OrderBy('id','desc')->where('business_id',$business_id)
                                    ->where(function($query){
                                        if (app('request')->input('name')) {
                                            $query->where('name','LIKE','%'.app('request')->input('name').'%');
                                        }
                                    })
                                    ->where('cost_center',1)->paginate(30);
        return view('cost_centers.index')
                ->with('allData',$allData)
                ->with('title',trans('home.Cost Center'))
                ;
    }
    public function add_cost_account()
    {
        return view('cost_centers.add')
                ->with('title',trans('home.Add Cost Center'));
    }
    public function post_add(Request $request)
    {
        $data                 =  new Account;
        $data->name           =  $request->name;
        $data->account_number =  $request->account_number;
        $data->note           =  $request->note;
        $data->business_id    =  request()->session()->get('user.business_id');
        $data->cost_center    =  1;
        $data->save();
        return redirect('account/cost-center')
                    ->with('status', [
                        'success' => 1,
                        'msg' =>trans('home.Done Successfully')
                    ]);
    }
    public function edit($id)
    {
        $business_id =  app('request')->session()->get('user.business_id');
        $data        =  Account::where('business_id',$business_id)
                        ->find($id);
        return view('cost_centers.edit')
                  ->with('data',$data)
                  ->with('title',$data->name);
    }
    public function post_edit(Request $request,$id)
    {
        $data                 =  Account::find($id);
        $data->name           =  $request->name;
        $data->account_number =  $request->account_number;
        $data->note           =  $request->note;
        $data->save();
        return redirect('account/cost-center')
                    ->with('status', [
                        'success' => 1,
                        'msg' =>trans('home.Done Successfully')
                    ]);
    }
}
