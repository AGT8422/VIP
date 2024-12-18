<?php

namespace Modules\ChartOfAccounts\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ChartOfAccounts\Entities\chartofaccount;

class ChartOfAccountsController extends Controller
{

    public function chart_view (){
        $business_id = request()->session()->get('user.business_id');
        $accounts=chartofaccount::where('business_id',$business_id)
            ->where('parent_id','=',0)->get();
       return view('chartofaccounts::index',['accounts'=>$accounts]);
    }

    public function addacount(){
        $business_id = request()->session()->get('user.business_id');
        $accounts=chartofaccount::where('business_id',$business_id)
                                  ->where('type','=',0)->get();

        return view('chartofaccounts::accounts.create',['accounts'=>$accounts]);
    }

    public function saveacount(Request $request){

        $chartofaccount=new chartofaccount();
        $chartofaccount->code=$request->account_code;
        $chartofaccount->parent_id=$request->parent_id ? $request->parent_id:0;
        $chartofaccount->haschild=0;
        $chartofaccount->cost_center_id=0;
        $chartofaccount->is_admin=0;
        $chartofaccount->type=$request->type;
        $chartofaccount->name=$request->name;
        $chartofaccount->save();
        // update main account
        if($chartofaccount->parent_id > 0){
            $mainaccount=chartofaccount::findorfail($chartofaccount->parent_id);
            $mainaccount->haschild=1;
            $mainaccount->save();
        }



    }

    public function getaccount(Request $request){
        $business_id = request()->session()->get('user.business_id');
        $accounts=chartofaccount::where('business_id',$business_id)
            ->where('parent_id','=',$request->id)->get();
        $output='';
        foreach ($accounts as $account){

            $output .='<div class="parent" onclick="getaccount('.$account->id.')"  id="'.$account->id.'">
                        <span>#'.$account->code.'</span>
                        '.$account->name.'
                           </div>';
        }
        return $output;
    }
}
