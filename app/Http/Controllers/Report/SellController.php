<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \DOMPDF;
use Dompdf\Options;
use App\Business;
use App\InvoiceLayout;
use App\Transaction;
use ArPHP\I18N\Arabic; 
 
use App\TransactionSellLine;
class SellController extends Controller
{
    public function index($id,Request $request)
    {
        \App::setlocale('en');
        // dd(request()->header(),$_SERVER['REMOTE_ADDR']);
        ini_set("pcre.backtrack_limit", "5000000");
        $invoice =  Transaction::find($id);
        if(!empty($invoice)){
            $pattern = \App\Models\Pattern::find($invoice->pattern_id);
        }else{
            $pattern = null;
        }
        if ($invoice) {
            $busines_id =  request()->session()->get('user.business_id');
            if($pattern != null){
                $layout    =  InvoiceLayout::where('business_id',$invoice->business_id)
                                ->where('is_default',1)->first();
            }else{
                $layout    =  InvoiceLayout::where('business_id',$invoice->business_id)
                                ->where('is_default',1)->first();
            }
            $tr = \App\Transaction::where("id",$id)->first();
            $return = \App\Transaction::where("id",$tr->return_parent_id)->first();
            if(app("request")->input("return")){
                $allData =  TransactionSellLine::where('transaction_id',$return->id)->orderBy("order_id","asc")->get();
            }else{
                $allData =  TransactionSellLine::where('transaction_id',$id)->orderBy("order_id","asc")->get();
            }
            $arr = [
                'layout' =>$layout,
                'invoice'=>$invoice,
                'allData'=>$allData
            ];
            
           
            if(app("request")->input("return")){
                $pdf =  DOMPDF::loadView('pdf.sell_return', compact("layout","invoice","allData"));
            }else{
                $html   = view('pdf.sell', compact("layout","invoice","allData"))->render();
                $arabic = new Arabic();
                
                $p = $arabic->arIdentify($html);
                for ($i = count($p)-1; $i >= 0; $i-=2) {
                    $utf8ar = $arabic->utf8Glyphs(substr($html, $p[$i-1], $p[$i] - $p[$i-1]));
                    $html = substr_replace($html, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
                }
                // $pdf = PDF::loadView('pdf.sell', compact("layout","invoice","allData"));
                $pdf =  DOMPDF::loadHTML($html);

                // dd($pdf);
                $pdf->setPaper('letter', 'portrait');
            }
           
            return $pdf->stream('report.pdf');
         }else {
           return '<h1> sorry this quotaion not found </h1>'; 
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
