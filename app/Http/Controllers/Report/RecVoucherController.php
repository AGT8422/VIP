<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use  App\Models\PaymentVoucher;
use  App\Models\Entry;

use App\Business;
use App\InvoiceLayout;
use App\Transaction;
use Dompdf\Dompdf;

use App\TransactionSellLine;
class RecVoucherController extends Controller
{
    public function index($id,Request $request)
    {
        \App::setlocale('en');
        $business_id  =  request()->session()->get('user.business_id');
        $business     =  \App\Business::find($business_id);
        $invoice      =  PaymentVoucher::find($id);
        $pattern      =  null;
        $entry        =  Entry::where("voucher_id",$id)->first(); 
        
        $account_id   =  $invoice->account_id;
        $list_of_cash =  [$business->cash,$business->bank];
        $paymentType  =  "";
        foreach($list_of_cash as $i => $aid){
            $typ_account  = \App\Account::where('account_type_id',$aid)->select(['id','name','account_number']) 
                                        ->orWhereHas('account_type',function($query) use($aid){
                                                $query->where('parent_account_type_id',$aid);
                                                $query->orWhere('id',$aid);
                                        })->select(['id','name','account_number'])
                                        ->pluck('id');
            $array_accounts = [];
            foreach($typ_account as $iid){
                $array_accounts[] = $iid;
            } 
            // dd($array_accounts,$account_id);
            if(in_array($account_id,$array_accounts)){
                $paymentType = ($i==0)?"<b>Payment Type : </b> Cash":"<b>Payment Type : </b> Bank Transfer";
                break;
            }else{
                $paymentType = "";
            }                         
        } 
       
        if ($invoice) {
            if($pattern != null){
                $layout    =  InvoiceLayout::where('business_id',$business_id)
                                ->where('is_default',1)->first();
            }else{
                $layout    =  InvoiceLayout::where('business_id',$business_id)
                                ->where('is_default',1)->first();
            }
             
            $allData =  PaymentVoucher::where('business_id',$business_id)->get();
            $arr = [
                'layout'=>$layout,
                'invoice'=>$invoice,
                'pattern'=>$pattern,
                'allData'=>$allData,
                'entry'=>$entry,
                'paymentType'=>$paymentType
            ];
         
            $pdf = PDF::loadView('pdf.voucher.R_voucher', $arr);
             
            return $pdf->stream('R_voucher.pdf');
        }else {
           return '<h1> Sorry This Receipt Voucher Not Found </h1>'; 
        }
        
        
         
        
        
    }
    public function generatePdf($id,Request $request)
    {
        \App::setlocale('en');
        $invoice =  Transaction::where('invoice_no',$request->invoice_no)->find($id);
        if(!empty($invoice)){
            $pattern = \App\Models\Pattern::find($invoice->pattern_id);
        }else{
            $pattern = null;
        }
         
        $busines_id =  request()->session()->get('user.business_id');
        if($pattern != null){
            $layout    =  InvoiceLayout::where('id',$pattern->invoice_layout)->first();
        }else{
            $layout    =  InvoiceLayout::where('id','Default')->where('business_id',1)
                            ->where('is_default',1)->first();
        }

        $allData =  TransactionSellLine::where('transaction_id',$id)->get();
        $arr = [
            'layout'=>$layout,
            'invoice'=>$invoice,
            'allData'=>$allData
        ];
        
        // Instantiate a new Dompdf instance
        $dompdf = new Dompdf();
        
        // Set the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Load your HTML into Dompdf
        $dompdf->loadHtml('pdf.sell');

        // Render the HTML as PDF
        $dompdf->render();

        // Set the PDF/A-1b compliance
        $canvas = $dompdf->get_canvas();
        $canvas->get_cpdf()->setMetadata(array(
            'pdfa' => array('1', 'b')
        ));
        // Output the generated PDF to the browser
        return $dompdf->stream();
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
