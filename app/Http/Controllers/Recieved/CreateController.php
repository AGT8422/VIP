<?php

namespace App\Http\Controllers\Recieved;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Transaction;
use App\Contact;


class CreateController extends Controller
{
    public function index(Request $request)
    {
        $business_id =  request()->session()->get('user.business_id');
        $warehouses  =  Warehouse::all_stores($business_id);
        $allData     =  Transaction::OrderBy('id','desc')->where('business_id',$business_id)->paginate(30);
        $suppliers   =  Contact::suppliers();
        $allData     =  Transaction::OrderBy('id','desc')->where('business_id',$business_id)
                            ->where('type','purchase')->where('status','!=','received')
                            ->where(function($query) use($request){
                                if ($request->ref_no) {
                                    $query->where('ref_no','LIKE','%'.$request->ref_no.'%');
                                }
                                if ($request->store_id) {
                                    $query->where('store',$request->store_id);
                                }
                                if ($request->supplier_id) {
                                    $query->where('contact_id',$request->supplier_id);
                                }
                            })->get();
        return view('recieved.general')
                ->with('warehouses',$warehouses)
                ->with('allData',$allData)
                ->with('suppliers',$suppliers)
                ;
    }
}
