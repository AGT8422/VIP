<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use App\Business;
use App\InvoiceLayout;
use App\Transaction;
use App\Models\TransactionRecieved;
use App\Models\RecievedPrevious;
use App\PurchaseLine;
class ReceiveController extends Controller
{
    public function index($id,Request $request)
    {
        \App::setlocale('en');
        $Delivery    =  TransactionRecieved::find($id);
        $transaction =  Transaction::find($Delivery->transaction_id);
        if ($Delivery) {
            $busines_id =  request()->session()->get('user.business_id');
            $layout     =  InvoiceLayout::where('name','Default')->where('business_id',$Delivery->business_id)
                                            ->where('is_default',1)->first();
            $allData =  RecievedPrevious::where('transaction_id',$Delivery->transaction_id)->get();
            $arr = [
                'layout'=>$layout,
                'Delivery'=>$Delivery,
                'allData'=>$allData,
                'transaction'=>$transaction
            ];

            $pdf = PDF::loadView('pdf.receive', $arr);
            return $pdf->stream('report.pdf');
        }else {
           return '<h1> sorry this purchase not found </h1>'; 
        }
        
        
         
        
        
    }
    public function test()
    {
        $url =  'http://demo.izocloud.com/uploads/dummy.html';

        // Create a stream
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Host: www.te.com\r\n"
                    . "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:71.0) Gecko/20100101 Firefox/71.0\r\n"
                    . "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n"
                    . "Accept-Language: en-US,en;q=0.5\r\n"
                    . "Accept-Encoding: gzip, deflate, br\r\n"
            ],
        ];

        $context = stream_context_create($opts);
        $data = file_get_contents($url, false, $context);
        \Storage::disk('public')->put('filename.pdf', $data);

    }
}
