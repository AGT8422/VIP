<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use App\Business;
use App\InvoiceLayout;
use App\Transaction;
use App\TransactionSellLine;

class PurchaseController extends Controller
{
    public function index($id,Request $request)
    {
        
        \App::setlocale('en');
       
        $invoice =  Transaction::where('ref_no',$request->ref_no)->find($id);
        $parent  =  Transaction::find($invoice->return_parent_id);
        if ($invoice) {
            $busines_id =  request()->session()->get('user.business_id');
            $layout    =  InvoiceLayout::where('business_id',$invoice->business_id)
                                ->where('is_default',1)->first();
               
            $arr = [
                'layout'=>$layout,
                'invoice'=>$invoice,
                'parent'=>$parent
            ];
            if(app("request")->input("return")){
                $pdf = PDF::loadView('pdf.purchase_return', $arr);
            }else{
                $pdf = PDF::loadView('pdf.purchase', $arr);
            }
            return $pdf->stream('report.pdf');
        }else {
           return '<h1> sorry this quotaion not found </h1>'; 
        }
    }
}
