<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
     
    <title>Sell</title>

    @php 
        $text_align     = "center" ;
        $font_text      = "15px" ;
        $font_text_table      = "13px" ;
        $color          =  "#b0906c";
        $font_bill_text = "18px" ;
        $font_table_bill_text = "15px" ;
        $number_format_digit = 2;
        if($invoice->sub_status == 'quotation'){
            $sizes = "100px 25px 100px 25px";
            $bottom_sizes = "100px";
        }else{
            $sizes = "20px 25px 100px 25px";
            $bottom_sizes = "400px";
        }
    @endphp 


    <style type="text/css">
        @font-face {
            font-family: "Amiri";
            src: url("../fonts/Amiri-Bold.ttf") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        
        @font-face {
            font-family: "Amiri";
            src: url("../fonts/Amiri-Regular.ttf") format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        body{

            font-family: 'Amiri';
            text-align: right;
            background-color: #fff;

        }

        
        .bill{

            min-height: 200px;

            background-color: #fff;

            margin: 0 auto;

            font-size: 14px;

        }

        .info_ul{

            padding: 0;

        }

        

        

        .products_table {

            width: calc(100% - 20px);

            border: 1px solid #929eaa;

            border-radius: 15px;

            margin: 10px;

        }

        .products_table th{

            width: calc(100% - 20px);

            border: 1px solid #929eaa;

            border-radius: 15px;

            margin: 10px;

            padding: 5px;

        }

        .body_cell_table tr{



        }

        .body_cell_table td {

            padding-bottom: 7px;

            border-bottom: 1px dashed;

            padding-top: 7px;

        }

        .sign_table{

            width: 100%;

        }

        .sign_table td{

            /* border-bottom: 1px dashed; */

        }

        .signature_table tr>td{

            padding: 10px 30px;

            font-size: 18px;

        }

        .items_table{

            width: 100%;

            border-top: 1px solid;

            font-size: 18px;

            padding-bottom: 25px;

        }

        .items_table th{

            padding: 20px 10px;

            border-bottom: 1px solid;

        }

        .items_table td{

            padding: 0 15px;

            padding-top: 15px;

        }

        .table>thead>tr>th {

            color: #2a2a2a;

            border-bottom: 1px solid #ededed;

            font-weight: 800;

        }
        .tables  {
            
             width:100%;

        }
        .tables>thead>tr>th {
            
            color: #2a2a2a;

            border-bottom: 1px solid #ededed;

            font-weight: 600;

        }

        .table th, .table td {
            

            padding: 0.75rem;

            vertical-align: top;

            border-top: 1px solid #dee2e6;

        }
        .tables thead, .tables tbody, .tables thead {
            width:100%;
        }
        .tables th  {
            font-size:10px;
            border-bottom:1px solid black;
        }
        .tables th, .tables td {
            
            
            text-align:center !important;
            padding: 0.01rem;
            width:9.9%;
            /*vertical-align: top;*/

            border-top: 1px solid #dee2e6;

        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
            
            border-color: #ededed;
            padding: 1px;
            
        }
        .row_item>tr>td, .row_item>tr>th{
            border-right:0px solid #000;
            border-left:0px solid #000;
        }
        .tables>tbody>tr>td, .tables>tbody>tr>th, .tables>tfoot>tr>td, .tables>tfoot>tr>th, .tables>thead>tr>td, .tables>thead>tr>th {

            border-color: #ededed;

            padding: 1px;

        }
        .desc{
             max-width: 11.1%;
        }
        table{
            max-width: 1100px;
            margin: 0 auto;
        }
        @page {
            margin: {{ $sizes }};
          
        }
        header {
            position: fixed;
            top: -100px;
            left: 0px;
            right: 0px;
            height: 00px;
            background-color: #fff;
            text-align: center;
            line-height: 0px;
        }
        .footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 100px;
            background-color: transparent;
            text-align: center;
            font-size:9px !important;
            line-height: 15px;
        }
        footer {
            position: fixed;
            bottom: -130px;
            left: 0px;
            right: 0px;
            height: 100px;
            background-color: transparent;
            text-align: center;
            font-size:9px !important;
            line-height: 15px;
        }
        .content {
            margin-top: 00px;
            margin-bottom: {{ $bottom_sizes }};
        }

    </style>

</head>

<body >
    @php $company_name = request()->session()->get("user_main.domain"); $brand_check = 0; $business= \App\Business::find(session()->get('business.id')); @endphp

    <!--<header>-->
    <!--    Fixed Header Content-->
    <!--</header>-->
    {{-- @if($invoice->sub_status == 'quotation')         --}}
        <footer>
                @php
                    $trm = \App\Models\QuatationTerm::where("id",$invoice->additional_notes)->first();
                @endphp
                @if($invoice->sub_status == 'quotation')
                    <table  style="width:100%;margin-top: 0px;text-align:left;border-bottom: 0px solid #003496; "  dir="ltr" >
                        <tbody >
                            <tr >
                                <td style="width:40% ;text-align:left" >
                                    
                                @if($invoice->sub_status == 'quotation'  )
                                        <p>
                                            Validity Date  :  {{ date('M-d-Y',strtotime($invoice->transaction_date. ' +2 weeks')) }}
                                        </p>
                                @endif
                                @if(!empty($business))
                                    @if(!empty($business->logo))

                                    <img style="width:100%" src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . $business->logo ) }}" alt="Logo">
                                    @endif 
                                @endif 
                                </td>
                                <td style="width:60% ;text-align:center;line-height:10px;">
                                    <br>
                                    <br>
                                
                                
                                    <b>  </b> <br>
                                      <br>
                                   
                                
                                </td>
                            </tr>
                        
                        </tbody>
                        
                    </table>
                @else
 
                    <hr style="background-color: {{$color}};border:1px solid {{$color}};border-radius:10px;">
                    <p style="line-height: 1px;margin-top:-4px">
                        {!!  $layout->footer_text !!} 
                    </p> 
                @endif 
              
        </footer>
    {{-- @endif --}}
    
    <div class="bill ">
            @php $business = \App\Business::find($invoice->business_id); $currency = \App\Currency::find($business->currency_id); @endphp

            @if($invoice->sub_status == 'quotation' || ($invoice->sub_status == '' && $invoice->status == 'draft'  ))
                {{-- /uploads/companies/elke/business_logo/1735812828_elke.png    --}}
                {{-- <img src="{{asset("/uploads/img/aljazira.png")}}"   style="margin-left:-20%; width: 100%; height:150px;margin-top:-100px;"> --}}
                
                <table style=" position:relative; top:-50px;width: 100%;margin-bottom:0px; border-bottom:7px solid {{$color}} ; padding-bottom:0px">
                    <tbody>
                        <tr>
                            <td colspan="2" style="width: 100%">
                                @if(!empty($business))
                                    @if(!empty($business->logo))
                                    <img style="width:100%" src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . $business->logo ) }}" alt="Logo">
                                    @endif 
                                @endif 
                                
                            </td>
                        </tr> 
                        <tr>
                            <td style="width: 40%;"><h3 style="text-align:left !important;width:100% !important;  ;margin-left:0px;font-size:19px;">&nbsp;</h3></td>
                            <td style="width: 100%;text-align:right;color:#000;text-align:center;padding-right:200px;"> 
                                <h1 style=" width:100% !important; text-align:center !important;  font-size:{{$font_bill_text}};  ">
                                    @if($invoice->sub_status == 'quotation')
                                    QUOTATION
                                    @elseif(($invoice->sub_status == '' && $invoice->status == 'draft'  )  )
                                    DRAFT
                                    @elseif(($invoice->sub_status == 'proforma' && $invoice->status == 'draft' )|| $invoice->status == 'ApprovedQuotation')
                                    Proforma <br>Invoice
                                    @elseif($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                                    TAX INVOICE  
                                    @endif
                                </h1>
                             </td>
                        </tr>
                    </tbody>
                </table>
                <table   style=" position:relative; top:-50px;width: 100%">
                    <tbody >
                    
                        <tr style=" background-color:#fff">
                        <td style="padding-left:30px;width:55%;line-height:1px;font-size:12px;padding:10px;">
                                 
                                <h3 style=" font-size:{{$font_table_bill_text}} !important;">{{ 'CUSTOMER INFORMATION ' }}</h3>
                                <p>
                                    {{ trans('home.Company Name')  }}  : <span style="word-break: break-all:position:relative;line-height:12px;">{{ $invoice->contact?$invoice->contact->name:' ' }} </span>
                                    
                                </p>
                            
                                <p >
                                    {{ trans('home.Address') }} :  
                                    {!! ( $invoice->contact)? ( $invoice->contact->address_line_1. " " .  $invoice->contact->city  . " " . $invoice->contact->country ):' ' !!} 
                                </p>
                                
                                <p>
                                    {{ trans('home.Attention') }} : 
                                    {{ $invoice->contact?($invoice->contact->supplier_business_name ." ". $invoice->contact->middle_name ." ". $invoice->contact->last_name  ):' ' }}
                                </p>
                                 
                                <p >
                                    {{ trans('Tel No. ') }} : 
                                    {{ ( $invoice->contact)? $invoice->contact->mobile:' ' }} 
                                </p>
                                <p >
                                    {{ trans('lang_v1.email_address') }} : 
                                    {{ ( $invoice->contact)? $invoice->contact->email:' ' }} 
                                </p>
                             
                                <p>
                                    {{ trans('TRN. ') }}: 
                                    {{ ($invoice->contact)?($invoice->contact->tax_number ):"" }}</p>
                                </td>
                        <td style="width: 5%;text-align:right;color:#fff;text-align:center;padding-right:30px;">
                            
                        </td>
                        <td style=" width:45%;line-height:1px;font-size:12px;padding:10px;">
                                    <h3 style=" font-size:{{$font_table_bill_text}} !important;">{{ 'DETAILS' }}</h3>
        
                                  
                                   
                                    <p >
                                        @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                                            {{ trans('Invoice#') }}. : {{$invoice->invoice_no }}
                                        @else
                                            {{ trans('Quote No') }}. : {{$invoice->invoice_no }}
                                        @endif
                                    </p>
                                    <p>
                                       Quote Date  :  {{ date('M-d-Y',strtotime($invoice->transaction_date)) }}
                                    </p>
                                    
                                     
                                    
                                     <p>
                                        @php
                                        $it = \App\Transaction::agent($invoice->agent_id,$invoice->transaction_id);
                                        @endphp
                                      
                                        Sales Rep : {{ $it }}
                                     
                                    </p>
                                    <p>
                                        
                                        @php $user =  \App\User::find($invoice->created_by); @endphp
                                        Prepared by : {{  ($user)?$user->first_name:"" }}
                                        
                                    </p>
                                    @php $note =   $invoice->note ; @endphp
                                    @if($note != null || $note != "")
                                    <p >
                                            
                                        {{__('sale.note') }}:  <span style="word-break: break-all:position:relative;line-height:12px;">{!! $note !!} </span>
                                    
                                    </p>
                                    @endif
                                      
                                    
                                    <p>&nbsp;</p>
                        </td>
                        </tr> 
                    </tbody>
                </table>
            @else
                <table style=" width: 100%;border:0px solid black;margin-bottom:0px;border-bottom:3px solid {{$color}}; padding-bottom:0px">
                    <tbody>
                        <tr>  
                        <td style="width: 40%;"></td>
                        <td style="width: 100%;padding-right:20px;">
                            {{-- <img src="{{asset("/uploads/img/aljazira.png")}}"   style="margin-left:-20%;max-width: 100%;max-height:520px"> --}}
                            @if(!empty( $business))
                                @if(!empty( $business->logo))
                                {{-- @php dd(asset( 'uploads/companies/'.$company_name.'/business_logo/' . $business->logo )); @endphp --}}
                                    <img src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . $business->logo ) }}"  style="margin-left:-20%;width: 500px;max-height:420px" alt="Logo">
                                @endif 
                            @endif 
                            
                        </td>
                        </tr>
                        <tr>
                            
                            <td style="width: 40%;"></td>
                             <td style="width: 100%;text-align:right;color:#000;text-align:center;padding-right:200px;"> 
                                
                            <h1 style="font-size:{{$font_bill_text}}; width:100% ;text-align:center !important;margin-left:-25px;  font-size:{{$font_bill_text}};  ">
                                    @if($invoice->sub_status == 'quotation')
                                    Quotation
                                    @elseif(($invoice->sub_status == '' && $invoice->status == 'draft'  )  )
                                    Draft
                                    @elseif(($invoice->sub_status == 'proforma' && $invoice->status == 'draft' )|| $invoice->status == 'ApprovedQuotation')
                                    Proforma <br>Invoice
                                    @elseif($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' || $invoice->sub_status == 'final '   )
                                    TAX INVOICE  
                                    @endif
                                <h3 style="text-align:left !important;width:100% !important;  ;margin-left:80px; font-size:{{$font_bill_text}}; ">TRN  {{ Session::get('business.tax_label_1') }}</h3>
                                </h1>
                        </td>
                       
                        </tr>
                        
                    </tbody>
                </table>
                <table class="table" style="  width: 100%;border:0px solid black;">
                    <tbody style="height: auto">
                    
                        <tr style=" background-color:#dbdee1;height:100px !important;line-height:1px">
                        <td style="width:50%;line-height:initial;font-size:{{$font_text}};padding:15px 0px 5px 15px;height:auto">
                            <h3 style="margin-bottom: 15px; font-size:{{$font_table_bill_text}} !important ;padding:0px;margin:0px">{{ 'BILLED TO : ' }} </h3>
                       
                            <p style="padding:0px;margin:0px">
                                 
                                {{ trans('home.Company Name')  }}  : <span style="word-break: break-all:position:relative;line-height:12px;">{{ $invoice->contact?$invoice->contact->name:' ' }} </span>
                                
                            </p>
                            
                            <p  style="padding:0px;margin:0px">
                               
                                    {{ trans('home.Address') }} : 
                                    {!! ( $invoice->contact)? ( $invoice->contact->address_line_1 . " " .  $invoice->contact->city  . " " . $invoice->contact->country ):' ' !!} 
                                
                            </p>
                            
                            <p style="padding:0px;margin:0px">
                                 
                                {{ trans('home.Attention') }} : 
                                {{ $invoice->contact?($invoice->contact->supplier_business_name ." ". $invoice->contact->middle_name ." ". $invoice->contact->last_name  ):' ' }}
                            </p>
                            
                             
                            <p style="padding:0px;margin:0px">
                                
                                {{ trans('Tel No. ') }} : 
                                {{ ( $invoice->contact)? $invoice->contact->mobile:' ' }} 
                                
                            </p>
                            <p  style="padding:0px;margin:0px">
                                  
                                {{ trans('lang_v1.email_address') }} : 
                                {{ ( $invoice->contact)? $invoice->contact->email:' ' }} 
                              
                               
                            </p>
                             
                            <p style="padding:0px;margin:0px">
                                 
                                {{ trans('TRN. ') }}: 
                                {{ $invoice->contact?($invoice->contact->tax_number ):"" }}
                                
                            </p>
                           
                            {{-- <p >
                                    {{ trans('home.Project Type') }}: Kitchen Restaurant FOB: Dubai
                            </p>
                            <p >
                                <b>
                                {{ trans('home.Loctaion') }} </b> : Silicon Oasis, Duba
                            </p> --}}
                            
                        </td>
                       
                            <td style=" width:40%;line-height:initial;font-size:{{$font_text}};padding:15px 0px 5px 15px;height:auto">
                               
                                  
                                   
                                    <p style="padding:0px;margin:0px">
                                    
                                        @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                                            {{ trans('Invoice#') }}. : {{$invoice->invoice_no }}
                                        @else
                                            {{ trans('home.Ref No') }}.# : {{$invoice->invoice_no }}
                                        @endif
                                    </p>
                                    <p style="padding:0px;margin:0px">
                                   
                                       Invoice Date  :  {{ date('M-d-Y',strtotime($invoice->transaction_date)) }}
                                    </p>
                                    <p style="padding:0px;margin:0px">
                                   
                                        Project No.  : {{ $invoice->project_no }}
                                    </p>
                                    
                                    <p style="padding:0px;margin:0px">
                                    
                                        @php
                                        $it = \App\Transaction::agent($invoice->agent_id,$invoice->transaction_id);
                                        @endphp
                                      
                                        Sales Rep : {{ $it }}
                                     
                                    </p>
                                    @php $note =   $invoice->note ; @endphp
                                    @if($note != null || $note != "")
                                    <p style="padding:0px;margin:0px">
                                        {{__('sale.note') }}:  <span style="word-break: break-all:position:relative;line-height:12px;">{!! $note !!} </span>
                                    </p>
                                    @endif
                                    <p style="display: none">
                                         AMOUNT DUE :{{ number_format($invoice->final_total,$number_format_digit) }} {{  isset($currency)?$currency->symbol:""}}  
                                         
                                    </p>      
                                    <p  style="display: none">
                                         
                                        @php $user =  \App\User::find($invoice->created_by); @endphp
                                        Prepared by : {{  ($user)?$user->first_name:"" }}
                                     
                                    </p>
                                    <p style="display: none">&nbsp;</p>
                                   
                        </td>
                        </tr> 
                       
                    </tbody>
                     
                </table>
                 
            @endif

            <table  class="table" @if($invoice->sub_status == 'quotation') style="position:relative; top:-50px; border:0px solid black; margin-top: 30px;text-align:{{$text_align}};width: 100%;  border-radius:.3rem" @else style="border:0px solid black; margin-top: 30px;text-align:left;width: 100%;  border-radius:.3rem" @endif     dir="ltr" >
                <thead>
    
                    <tr>
                        <th style="font-size:{{$font_text_table}};max-width:20px;font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black; text-align:{{$text_align}};">{{ trans('home.NO') }}</th>
                        @php $se = 0; @endphp
                        @foreach($allData as $data) @if( $data->se_note != null && $data->se_note !="")    @php $se = 1; @endphp @endif @if( $data->product->brand != null &&$data->product->brand !="")    @php $brand_check = 1; @endphp @endif  @endforeach
                        @if($se != 0)    
                            <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:10px;text-align:{{$text_align}};">{{ trans('home.SE') }}</th>
                        @else
                            
                        @endif
                        <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:5%;text-align:{{$text_align}};">ITEM &  {{   trans('home.DESCRIPTION') }}</th>
                        @if($brand_check != 0)    
                            <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:4%;text-align:{{$text_align}};">{{ trans('product.brand') }}</th>
                        @else
                            
                        @endif
                        <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:10px; text-align:{{$text_align}};">{{ trans('product.model_no') }}</th>
                        <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;width:5%;text-align:{{$text_align}};">{{ trans('home.QTY') }}</th> 
                        @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' || $invoice->sub_status == 'final ')
                        {{--  <th style="font-size:10px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:10px;text-align:left;">{{ trans('home.PHOTO') }}</th>--}}
                        @else
                        <th style="font-size:{{$font_text_table}};font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:10px;text-align:{{$text_align}};">{{ trans('home.PHOTO') }}</th>  
                        @endif 
                        
                        @php $dis = 0; @endphp
                        @foreach($allData as $data) @if(($data->unit_price_before_discount - $data->unit_price) != 0) @php $dis = 1; @endphp @endif  @endforeach
                        @if($dis != 0)
                        <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:10px;text-align:{{$text_align}};">{{"Price"}}</th> 
                        <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:10px;text-align:{{$text_align}};">{{"Disc"}}</th> 
                        @endif
                        <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:10px;text-align:{{$text_align}};">{{"Unit Price"}}</th> 
                        
                        <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:10px;text-align:{{$text_align}};">{{  "Tot Price" }}</th>
                        <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:10px; text-align:{{$text_align}};">{{ ($invoice->tax)?$invoice->tax->name . ":" : "";  }}</th>

                        <th style="font-size:{{$font_text_table}};font-weight: bolder;background-color:transparents;color:#000;border-bottom:1px solid black;max-width:10px;text-align:{{$text_align}};font-family:arial">{{"Tot Incld.vat"}}</th> 
                        
                    </tr>
    
                </thead>
                <tbody class="row_item" style="padding:0px !important">
                    <?php $total = 0;  $count = 0 ; $count_id = 1 ;?>
                           
                    @foreach ($allData as $data)
                            <?php 
    
                                    
                            $discount =  $data->line_discount_amount ;
                            $total   += ($data->unit_price_inc_tax*$data->quantity);
                            
    
                            ?>
                            <tr style="margin-bottom:1px; !important;padding:0px !important">
                                @php $count += $data->quantity; @endphp
                                <td style="font-size:{{$font_text_table}};width:1%;border-bottom: 1px solid grey;padding:1px !important">{{ $count_id++;  }}</td>
                                @if($data->se_note != null && $data->se_note != "")
                                        <td style="font-size:{{$font_text_table}};max-width:5px;text-align:{{$text_align}};  border-bottom: 1px solid grey; white-space: normal; word-break: break-word;padding:1px !important">{{ $data->se_note }}</td>
                                        @else
                                        
                                        
                                        @endif
                                    @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                                    
                                        <td style="font-size:{{$font_text_table}};max-width:30px;text-align:{{$text_align}};  border-bottom: 1px solid grey; white-space: normal; word-break: break-word;padding:0px !important ">
                                            <b style="font-size:{{$font_text_table}};">{{ $data->product->name }}</b> 
                                            @if($data->sell_line_note != null && $data->sell_line_note != "")<pre style="font-size:9px; text-align:left; line-height:10px !important;word-break: break-word;word-wrap: break-word;"> {!! $data->sell_line_note !!} </pre>@endif
                                        </td>
                                    @else 
                                        {{--   $data->sell_line_note  --}} 
                                        <td style="font-size:{{$font_text_table}};max-width:40px;text-align:{{$text_align}};  border-bottom: 1px solid grey;padding:0px !important ">
                                            
                                            <b style="font-size:{{$font_text_table}};"style="font-size:{{$font_text_table}};"> {{ $data->product->name }} </b>
                                            @if($data->sell_line_note != null && $data->sell_line_note != "")<pre style="font-size:9px; text-align:left; line-height:10px !important;word-break: break-word;word-wrap: break-word;"> {!! $data->sell_line_note !!} </pre>@endif
                                            {{-- <pre style="width:100px;text-align:left; font-size:9px; line-height:10px !important;word-break: break-word;word-wrap: break-word;"> {!! $data->sell_line_note !!} </pre> --}}
                                        </td>
                                        
                                    
                                    @endif
                                    @if($brand_check != 0)    
                                        <td style="font-size:{{$font_text_table}};max-width:5%;text-align:{{$text_align}};  border-bottom: 1px solid grey; white-space: normal; word-break: break-word;padding:1px !important">{{ ($data->product->brand)?$data->product->brand->name:"" }}</td>
                                    @else
                                        
                                    @endif
                                <td style="font-size:{{$font_text_table}};max-width:5px;text-align:{{$text_align}};  border-bottom: 1px solid grey; white-space: normal; word-break: break-word;padding:1px !important">{{ $data->product->sku }}</td>
                                <td style="font-size:{{$font_text_table}};max-width:10px;text-align:{{$text_align}};border-bottom: 1px solid grey;padding:1px !important">{{ $data->quantity }}</td>
                                    
                                @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' || $invoice->sub_status == 'final ' )
                                   
                                @else
                                    <td style="font-size:15px;max-width:20px;border-bottom: 1px solid grey;">
                                        @if($data->product->image_url)
                                        
                                     <img src="{{ URL::to($data->product->image_url) }}" style="max-width: 100%">   
                                    
                                        @endif
                                    </td>  
                                @endif
                                
                                @if(!empty($data->warranties->first()))
                                    <small  style=" padding:20px;font-size:{{$font_text_table}};background-color:#f1f1f1 ;text-align:{{$text_align}}; padding:10px;margin: 00px 0px !important;padding:1px !important">
                                        
                                        {{$data->warranties->first()->display_name ?? ''}} 
                                    </small>
                                    <br>
                                    <small  style="background-color:#f1f1f1 ;border: 1px solid black; border-radius:.2rem;padding:5px;margin: 00px 0px;padding:1px !important">
                                        
                                        {{-- {{ @format_date($data->warranties->first()->getEndDate($data->transaction_date))}} --}}
                                    </small>
                                @endif
    
                            
                                {{-- <pre style="font-size:9px; line-height:9px !important;word-break: break-word;word-wrap: break-word;"> {!! $data->sell_line_note !!}</pre>  --}}
                            
                                @if($discount!=0)
                                    <td style="font-size:{{$font_text_table}};max-width:10px;text-align:{{$text_align}};border-bottom: 1px solid grey;padding:1px !important">{{ number_format($data->unit_price_before_discount,$number_format_digit) }} {{  isset($currency)?"":""}} </td> 
                                    <td style="font-size:{{$font_text_table}};max-width:10px;text-align:{{$text_align}};border-bottom: 1px solid grey;padding:1px !important">{{ number_format($discount,$number_format_digit) }} {{  isset($currency)?"":""}}  </td> 
                                @elseif($dis != 0)
                                    <td style="font-size:{{$font_text_table}};max-width:10px;text-align:{{$text_align}};border-bottom: 1px solid grey;padding:1px !important">{{0}}</td> 
                                    <td style="font-size:{{$font_text_table}};max-width:10px;text-align:{{$text_align}};border-bottom: 1px solid grey;padding:1px !important">{{0}}</td> 
                                @endif
                                @php
                                    $vatValue =  ($invoice->tax)?$invoice->tax->amount : 0;
                                @endphp
                                <td style="font-size:{{$font_text_table}};max-width:10px;text-align:{{$text_align}};border-bottom: 1px solid grey;padding:1px !important">{{ number_format($data->unit_price,$number_format_digit) }} {{  isset($currency)?"":""}} </td>
                                
                                <td style="font-size:{{$font_text_table}};max-width:10px;text-align:{{$text_align}};border-bottom: 1px solid grey;padding:1px !important">{{ number_format($data->unit_price*$data->quantity,$number_format_digit) }} {{  isset($currency)?"":""}} </td> 
                                <td style="font-size:{{$font_text_table}};max-width:10px;text-align:{{$text_align}};border-bottom: 1px solid grey;padding:1px !important">{{ number_format( ($data->unit_price*$data->quantity * $vatValue/100),$number_format_digit) }} {{  isset($currency)?"":""}}  </td> 
                                 
                                <td style="font-size:{{$font_text_table}};max-width:10px;text-align:{{$text_align}};border-bottom: 1px solid grey;padding:1px !important">{{ number_format((($data->unit_price*$data->quantity * $vatValue/100)+$data->unit_price*$data->quantity),$number_format_digit) }} {{  isset($currency)?"":""}} </td>
                            
                            </tr>
    
                    @endforeach
            
                </tbody>
            </table>
        
            @if($invoice->sub_status == 'quotation'|| (($invoice->sub_status == 'proforma' && $invoice->status == 'draft' )|| $invoice->status == 'ApprovedQuotation'))
                <table class="table" style="width:100%;font-size:11px;Lline-height:10px; margin-top: 10px;font-weight: bold; ">
                    <thead>
                        <tr>
                        
                            <td>@lang("purchase.qty") : {{$count}}</td>
                        </tr>
                    </thead>
                </table>
            @endif
            
            <table class="table" style=" border:0px solid black;width:100%;font-size:13px;  margin-top: 2px; "  dir="ltr"  dir="ltr" >
                <tbody >
                    <tr style="width:100% " >
                        
                        <td style="text-align:left;width:70%">
                        @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' || $invoice->sub_status == 'final ' )
                            {!! $layout->invoice_text !!} 
                        @endif
                        <br>
                        @php 
                            // $convert           = new Kwn\NumberToWords\NumberToWords();  
                            // $numberTransformer = $numberToWords->getNumberTransformer('en');
                            // $words = $numberTransformer->toWords($invoice->final_total);
                            $f     = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
                            $words =  $f->format(round($invoice->final_total,$number_format_digit));
                        @endphp
                        <span style="text-transform: capitalize">
                                <b>{{ __('Total : ')}} </b>   {{$words}} {{  isset($currency)?$currency->symbol:" "}}  
                        </span>
                        </td>
                        
                        <td style="text-align:left;width:30%;font-weight: bold;">
                            <p  style=" width:100%;line-height:1px;padding:0px;">
                                <span   style="float:left;width:50%;font-family:arial">Tot Exlcd.Vat :  </span>  
                                <span   style="float:right;width:50%">{{ number_format($invoice->total_before_tax,$number_format_digit) }} {{  isset($currency)?$currency->symbol:""}} </span>
                            </p>
                            <p style="line-height:3px">&nbsp;</p>
                            
                            @php
                            if ($invoice->discount_type == "fixed_before_vat"){
                                $dis = $invoice->discount_amount;
                            }else if ($invoice->discount_type == "fixed_after_vat"){
                                $tax = \App\TaxRate::find($invoice->tax_id);
                                if(!empty($tax)){
                                    $dis = ($invoice->discount_amount*100)/(100+$tax->amount) ;
                                }else{
                                    $dis = ($invoice->discount_amount*100)/(100) ;
                                }
                            }else if ($invoice->discount_type == "percentage"){
                                $dis = ($invoice->total_before_tax *  $invoice->discount_amount)/100;
                            }else{
                                $dis = 0;
                            }
                            @endphp
                            @if($invoice->discount_amount != 0)
                                <p  style=" width:100%;line-height:5px;">
                                    <span   style="float:left;width:50%;border:0px solid black">Discount :</span>    
                                    <span   style="float:left;width:50%;border:0px solid black">{{ number_format($dis,$number_format_digit) }} {{  isset($currency)?$currency->symbol:""}}   </span>
                                
                                </p>
                            <br>
                            <p style="line-height:3px">&nbsp;</p>
                            <p  style=" width:100%;line-height:3px;">
                                <span   style="float:left; width:50%;border:0px solid black">Total After Dis  :  </span>    
                                <span   style=" float:left;width:50%">{{ number_format($invoice->total_before_tax - $dis,$number_format_digit) }} {{  isset($currency)?$currency->symbol:""}}  </span>
                                
                            </p>
                            <p style="line-height:3px">&nbsp;</p>
                            <br>
                            @endif
                            <p  style="   width:100%;line-height:1px;padding:0px;">
                                <span   style="float:left;width:50%">{{ ($invoice->tax)?$invoice->tax->name . ":" : ""  }} 	   </span>    
                                <span   style="float:left;width:50%">{{ number_format($invoice->tax_amount,$number_format_digit) }} {{  isset($currency)?$currency->symbol:""}}  </span>
                            
                            </p>
                            <p style="line-height:1px">&nbsp;</p>
                    
                            <p  style="  width:100%;line-height:1px; border-top:3px solid #444444;padding-top:20px;">
                                <span   style="float:left;width:50%"> Tot Incld.Vat  :   </span>    
                                <span   style="float:right;width:50%">{{ number_format( $invoice->final_total,$number_format_digit) }} {{  isset($currency)?$currency->symbol:""}}  </span>
                                
                            </p>
                        <br>
                            
                        </td>
                    </tr>
                </tbody>
            </table>
            @if(($invoice->status != 'final' && $invoice->sub_status != 'final')  || ($invoice->sub_status == 'f' && $invoice->status == 'final') )
                         
                @if(!empty($trm))
                    <h3 style="text-align: left">{{"Terms & And Conditions"}}</h3>
                    <table class="table" style="width:100%;font-size:0.6rem; outline: 1px solid #BCBAB9;border-radius:10px;margin-bottom:0px;"  dir="ltr" >
                        <tbody style="border:0px">
                            <tr>
                                <td>
                                    {!!  $trm->description  !!}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @endif
            @endif
            <table class=" " style="position:relative; top:-30px;width:100%;  border: 0px solid #BCBAB9;margin-bottom:0px;margin-top:30px; border:0px solid black; "  dir="ltr" >
                <tbody>
                    <tr>
                        <td style="width:49%;font-size:15px">
                                
                                    {{ " Customer Signature : " }}                                         
                                
                        </td>
                        <td style="width:1%">
                                
                                                                        
                        </td>
                        <td style="width:1%">
                                
                                                                        
                        </td>
                        <td style="width:49%;text-align:center;font-size:15px">
                                
                                    {{ " Authorize Signature : " }}                                         
                                
                        </td>
                    </tr>
                </tbody>
            </table> 
            <table class=" " style="width:100%;  border: 0px solid #BCBAB9;margin-bottom:0px; border-top:1px solid black;margin-top:0px"  dir="ltr" >
                <tbody>
                    <tr>
                        <td style="width:50%;font-size:12px">
                            Bank Details : <br>
                            Account Name: Efficient Line Kitchen & Restaurant Equipment Trading LLC <br>
                            OR: Efficient Line Kit & Rest Eq Tr LLC <br>
                            Bank Name: Abu Dhabi Commercial Bank, Khaled Bin Waleed, Dubai-UAE <br>
                            Currency: AED CID: 10559494 <br>
                            Account Number: 1055 9494 124 001 <br>
                            IBAN: AE 9600 300 1055 9494 124 001 <br>
                            Swift Code: ADCBAEAA <br>

                        </td> 
                        <td style="width:1%">
                                
                                                                        
                        </td>
                        <td style="width:49%;text-align:center;font-size:15px">
                                
                                                                        
                                
                        </td>
                    </tr>
                </tbody>
            </table>
            @php
                $trm = \App\Models\QuatationTerm::where("id",$invoice->additional_notes)->first();
            @endphp
           
            
                 
            @if($invoice->sub_status != 'quotation')
                <div class="footer" style="display:none;">
                    @php
                        $trm = \App\Models\QuatationTerm::where("id",$invoice->additional_notes)->first();
                    @endphp
                    @if($invoice->sub_status == 'quotation')
                        
                        
                        <table  style="width:100%;margin-top: 0px;text-align:left;border-bottom: 0px solid #003496; "  dir="ltr" >
                            <tbody >
                                <tr >
                                    <td style="width:40% ;text-align:left" >
                                    @if($invoice->sub_status == 'quotation'  )
                                            <p>
                                                Validity Date  :  {{ date('M-d-Y',strtotime($invoice->transaction_date. ' +2 weeks')) }}
                                            </p>
                                    @endif
                                    {{-- <img src="{{asset("/uploads/img/Footer-Aljazira.png")}}"   style="margin-bottom:-70px;width: 300px;height:50px;margin-right:00px;"> --}}
                                    {{-- @if(!empty(Session::get('business.logo')))
                                        <img style="width:100%" src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . Session::get('business.logo') ) }}" alt="Logo">
                                    @endif --}}
                                    </td>
                                    <td style="width:60% ;text-align:center;text-height:10px;">
                                    {{--                                         
                                        <b>Tel : +968 24503131   </b> <br>
                                        ست:1479047 , برج الراية , طريق رقم 319 , مبنى رقم : 7/1ب ,ص.ب : 394 , الرمز البريدي : 400 ,غلا , سلطنة عمان <br>
                                        
                                        C.R.No.1479047,Al Raya Tower Way No.319,Building No. 1/7B ,  <br>
                                        P.O.Box: 394 PC: 400,Ghala,Sultanate of Oman ,E-mail:info@aljazirah.com 
                                     --}}
                                    </td>
                                </tr>
                            
                            </tbody>
                            
                        </table>
                    @else
                        <table class=" " style="display:none;width:100%;  border: 0px solid #BCBAB9;margin-bottom:0px; border:0px solid black;margin-top:0px"  dir="ltr" >
                            <tbody>
                                <tr>
                                    <td style="width:49%;font-size:15px">
                                            
                                                {{ " Customer Signature : " }}                                         
                                            
                                    </td>
                                    <td style="width:1%">
                                            
                                                                                    
                                    </td>
                                    <td style="width:1%">
                                            
                                                                                    
                                    </td>
                                    <td style="width:49%;text-align:center;font-size:15px">
                                            
                                                {{ " Authorized Signature : " }}                                         
                                            
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="4">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                        <table class=" " style="display:none;width:100%;  border: 0px solid #BCBAB9;margin-bottom:0px; border-top:1px solid black;margin-top:0px"  dir="ltr" >
                            <tbody>
                                <tr>
                                    <td style="width:50%;font-size:12px">
                                        Bank Details : <br>
                                        Account Name: Efficient Line Kitchen & Restaurant Equipment Trading LLC <br>
                                        OR: Efficient Line Kit & Rest Eq Tr LLC <br>
                                        Bank Name: Abu Dhabi Commercial Bank, Khaled Bin Waleed, Dubai-UAE <br>
                                        Currency: AED CID: 10559494 <br>
                                        Account Number: 1055 9494 124 001 <br>
                                        IBAN: AE 9600 300 1055 9494 124 001 <br>
                                        Swift Code: ADCBAEAA <br>

                                    </td> 
                                    <td style="width:1%">
                                            
                                                                                    
                                    </td>
                                    <td style="width:49%;text-align:center;font-size:15px">
                                            
                                                                                    
                                            
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @endif 
                        <br>
                    <hr style="display:none;background-color: {{$color}};border:1px solid {{$color}};border-radius:10px;">
                    <p style="display:none;line-height: 1px;margin-top:-4px">
                        {!!  $layout->footer_text !!} 
                    </p>
                </div>
            
            @endif
      
        
    </div>

    
</body>
 
</html>