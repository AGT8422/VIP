<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GournalVoucherItem;
use App\Models\GournalVoucher;
use  App\Models\Entry;

use PDF;
use App\Business;
use App\InvoiceLayout;
use App\Transaction;

use Dompdf\Dompdf;
use setasign\Fpdi\Fpdi;

use App\TransactionSellLine;
class ExVoucherController extends Controller
{
    public function index($id,Request $request)
    {
        \App::setlocale('en');
        $invoice =  GournalVoucher::find($id);
        // dd($invoice) ;
        $pattern = null;
        $entry   =  Entry::where("expense_voucher_id",$id)->first(); 

        if ($invoice) {
            $busines_id =  request()->session()->get('user.business_id');
            $user_id    =  request()->session()->get('user.id');
            $user       =  \App\User::where("id",$user_id)->first();
            $NAME  = (!empty($user))?$user->username : "";
            if($pattern != null){
                $layout    =  InvoiceLayout::where('id',$pattern->invoice_layout)->first();
            }else{
                $layout    =  InvoiceLayout::where('name','Default')->where('business_id',$invoice->business_id)
                                ->where('is_default',1)->first();
            }

            $allData =  GournalVoucher::where('business_id',$invoice->business_id)->get();
            $items   =  GournalVoucherItem::where('gournal_voucher_id',$invoice->id)->get();
            $arr = [
                'user'=>$NAME,
                'layout'=>$layout,
                'invoice'=>$invoice,
                'allData'=>$allData,
                'entry'=>$entry,
                'items'=>$items,
                'pages'=>null
            ];
            $pdf  = PDF::loadView('pdf.voucher.J_voucher', $arr);
            $path = storage_path('app\pdf\JV_print.pdf');
            $pdf->save($path);
            $pdfc = new Fpdi();
            $pageCount = $pdfc->setSourceFile($path);
            $arry = [
                'user'=>$NAME,
                'layout'=>$layout,
                'invoice'=>$invoice,
                'allData'=>$allData,
                'entry'=>$entry,
                'items'=>$items,
                'pages'=>$pageCount
            ];
            $pdf_final  = PDF::loadView('pdf.voucher.EX_voucher', $arry);

            
            return $pdf_final->stream('EX_voucher.pdf');
        }else {
           return '<h1> Sorry This Expense Voucher Not Found </h1>'; 
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
