<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    
@if($invoice->sub_status == 'quotation' )
<title>Quotation</title>
@elseif( ($invoice->sub_status == '' && $invoice->status == 'draft'  ))
<title>Draft</title>
@else
<title>Sale</title>
@endif



    <style type="text/css">

        body{

            font-family:arial;
            background-color: #fff;

        }

        .bill{

            min-height: 200px;

            background-color: #fff;

            margin: 0 auto;

            font-size: 12px;

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

            font-size: 16px;

        }

        .items_table{

            width: 100%;

            border-top: 1px solid;

            font-size: 16px;

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

            font-weight: 500;

        }

        .table th, .table td {
            

            padding: 0.75rem;

            vertical-align: top;

            border-top: 1px solid #dee2e6;

        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {

            border-color: #ededed;

            padding: 15px;

        }
        table{
            max-width: 1100px;
            margin: 0 auto;
        }

    </style>

</head>

<body>

    <div class="bill"  >
        
        @if($invoice->sub_status == 'quotation' || ($invoice->sub_status == '' && $invoice->status == 'draft'  ))
        <table style="width: 100%;margin-bottom:5px;border-bottom:7px solid #1b71bc; padding-bottom:25px">
            <tbody>
                <tr>
                <td style="width: 40%;">
                    <img src="{{asset("../../../uploads/img/agt.png")}}"   style="max-width: 350px;height:320px">
                    <br>
                    <!--@if($layout) {!! $layout->header_text !!} @endif-->
                </td>
                <td style="width: 40%;"></td>
                <td style="width: 50%;padding-left:10px;">
                       <span style="font-size:17px"> <b>TAX No:</b> 100355364900003 </span><br>
                       <span style="font-size:17px"> <b>P.O. Box:</b> 95659, Dubai, UAE </span><br>
                       <span style="font-size:17px"><b> Email:</b> info@dikitchen.ae </span><br>
                        <span style="font-size:17px"><b>Website:</b> www.dikitchen.ae </span><br>
                    <span style="font-size:13px"><b>Dubai:</b> (04)2520680 &nbsp;<b>Abu Dubai:</b> (02)2460163</span> <br>
                        <span style="font-size:13px"><b>Sharijah:</b> (06)5444595&nbsp;<b> Factory:</b> (06)7444305</span> <br>
                </td>
               
                </tr>
                <tr>
                    <td style="width: 30%;"></td>
                    <td style="text-align: center; width:70%;
                             font-size:17px;"> 
                    <h1 style=" width:100% !important; text-align:center !important;    ">
                            @if($invoice->sub_status == 'quotation')
                            QUOTATION
                            @elseif(($invoice->sub_status == '' && $invoice->status == 'draft'  )  )
                            DRAFT
                            @elseif(($invoice->sub_status == 'proforma' && $invoice->status == 'draft' )|| $invoice->status == 'ApprovedQuotation')
                            Approved <br>Quotation
                            @elseif($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                            TAX INVOICE  
                            @endif
                        </h1>
                     </td>
                   
                </tr>
            </tbody>
        </table>
        <table   style="width: 100%">
            <tbody >
            
                <tr style=" background-color:#fff">
                <td style="width:50%;line-height:20px;font-size:13px;">
                        <!--<h4 style="margin-bottom: 15px;">{{ 'BILLED TO : ' }} </h4>-->
                        <h3 style="font-size:13px;">{{ 'CUSTOMER INFORMATION ' }}</h3>
                        <p>
                             
                            {{ trans('home.Company Name')  }}  : {{ $invoice->contact?$invoice->contact->name:' ' }}
                            
                        </p>
                    
                        <p >
                           
                                {{ trans('home.Address') }} : {{ ( $invoice->contact)? ( $invoice->contact->address_line_1 .  $invoice->contact->city  . $invoice->contact->country ):' ' }} 
                            
                        </p>
                        
                        <p>
                             
                            {{ trans('home.Attention') }} : {{ $invoice->contact?($invoice->contact->supplier_business_name ." ". $invoice->contact->middle_name ." ". $invoice->contact->last_name  ):' ' }}
                        </p>
                        
                         
                        <p >
                            
                            {{ trans('Tel No. ') }} : {{ ( $invoice->contact)? $invoice->contact->mobile:' ' }} 
                            
                        </p>
                        <p >
                              
                            {{ trans('lang_v1.email_address') }} : {{ ( $invoice->contact)? $invoice->contact->email:' ' }} 
                          
                           
                        </p>
                     
                        <p>
                             
                            {{ trans('TAX No. ') }}: </b> {{ ($invoice->contact)?($invoice->contact->tax_number ):"" }}</p></td>
                <td style="width: 10%;text-align:right;color:#fff;text-align:center;padding-right:30px;">
                    {{ QrCode::format('svg')->size(90,90)->generate(url('reports/sell/'.$invoice->id.'?invoice_no='.$invoice->invoice_no)) }}
                </td>
                <td style=" width:40%;line-height:20px;font-size:13px;">
                            <h3 style="font-size:13px;">{{ 'DETAILS' }}</h3>

                          
                            <!--<h3 style="font-family:arial">Particualrs :</h3>-->
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
                               Validity Date  :  {{ date('M-d-Y',strtotime($invoice->transaction_date. ' +2 weeks')) }}
                            </p>
                             
                            <!--<p style="font-family:arial">-->
                                
                            <!--    Print {{ trans('home.Date')  }}   :{{  date('d-M-Y',strtotime($invoice->created_at)) }} -->
                                
                            <!--</p>-->
                            <!--<p>-->
                            <!--    Status. : @if($invoice->sub_status == 'quotation')-->
                            <!--                Quotation-->
                            <!--            @elseif($invoice->sub_status == 'proforma')-->
                            <!--                Approved Quotation-->
                            <!--            @else-->
                            <!--                {{ $invoice->status  }}-->
                            <!--            @endif-->
                            <!--</p>-->
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
                              
                            
                            <p>&nbsp;</p>
                </td>
                </tr> 
            </tbody>
        </table>
        @else
        <table style="width: 100%;margin-bottom:5px;border-top:7px solid #1b71bc; padding-bottom:25px">
            <tbody>
                <tr>
                <td style="width: 70%;">
                    <img src="{{asset("../../../uploads/img/agt.png")}}"   style="max-width: 400px;height:320px">
                    <br>
                    @if($layout) {!! $layout->header_text !!} @endif
                </td>
                
                <td style="width: 50%;text-align:right;color:#fff;text-align:center;padding-right:30px;">
                    {{ QrCode::format('svg')->size(140,140)->generate(url('reports/sell/'.$invoice->id.'?invoice_no='.$invoice->invoice_no)) }}
                </td>
                <td style="text-align: right; width:70%; ">
                    <h3>&nbsp;</h3>
                    <h3>&nbsp;</h3>
                    <h3>&nbsp;</h3>
                    <h1 style="font-size:60px; width:100% ;text-align:right !important;  ">
                            @if($invoice->sub_status == 'quotation')
                            Quotation
                            @elseif(($invoice->sub_status == '' && $invoice->status == 'draft'  )  )
                            Draft
                            @elseif(($invoice->sub_status == 'proforma' && $invoice->status == 'draft' )|| $invoice->status == 'ApprovedQuotation')
                            Approved <br>Quotation
                            @elseif($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                            TAX <br>INVOICE  
                            @endif
                        </h1>
                     </td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%">
            <tbody >
            
                <tr style=" background-color:#dbdee1">
                <td style="width:50%;line-height:20px;font-size:13px;">
                    <h3 style="margin-bottom: 15px;">{{ 'BILLED TO : ' }} </h3>
               
                    <p>
                         
                        {{ trans('home.Company Name')  }}  : {{ $invoice->contact?$invoice->contact->name:' ' }}
                        
                    </p>
                    
                    <p >
                       
                            {{ trans('home.Address') }} : {{ ( $invoice->contact)? ( $invoice->contact->address_line_1 .  $invoice->contact->city  . $invoice->contact->country ):' ' }} 
                        
                    </p>
                    
                    <p>
                         
                        {{ trans('home.Attention') }} : {{ $invoice->contact?($invoice->contact->supplier_business_name ." ". $invoice->contact->middle_name ." ". $invoice->contact->last_name  ):' ' }}
                    </p>
                    
                     
                    <p >
                        
                        {{ trans('Tel No. ') }} : {{ ( $invoice->contact)? $invoice->contact->mobile:' ' }} 
                        
                    </p>
                    <p >
                          
                        {{ trans('lang_v1.email_address') }} : {{ ( $invoice->contact)? $invoice->contact->email:' ' }} 
                      
                       
                    </p>
                     
                    <p>
                         
                        {{ trans('TAX No. ') }}: </b> {{ $invoice->contact?($invoice->contact->tax_number ):"" }}
                        
                    </p>
                   
                    {{-- <p >
                            {{ trans('home.Project Type') }}: Kitchen Restaurant FOB: Dubai
                    </p>
                    <p >
                        <b>
                        {{ trans('home.Loctaion') }} </b> : Silicon Oasis, Duba
                    </p> --}}
                    
                </td>
                
                    <td style=" width:40%;line-height:20px;font-size:13px;">
                       
                          
                            <!--<h3 style="font-family:arial">Particualrs :</h3>-->
                            <p >
                                @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                                    {{ trans('Invoice#') }}. : {{$invoice->invoice_no }}
                                @else
                                    {{ trans('home.Ref No') }}.# : {{$invoice->invoice_no }}
                                @endif
                            </p>
                            <p>
                               Invoice Date  :  {{ date('M-d-Y',strtotime($invoice->transaction_date)) }}
                            </p>
                            <p>
                                Project No.  : {{ $invoice->project_no }}
                            </p>
                            <!--<p style="font-family:arial">-->
                                
                            <!--    Print {{ trans('home.Date')  }}   :{{  date('d-M-Y',strtotime($invoice->created_at)) }} -->
                                
                            <!--</p>-->
                            <!--<p>-->
                            <!--    Status. : @if($invoice->sub_status == 'quotation')-->
                            <!--                Quotation-->
                            <!--            @elseif($invoice->sub_status == 'proforma')-->
                            <!--                Approved Quotation-->
                            <!--            @else-->
                            <!--                {{ $invoice->status  }}-->
                            <!--            @endif-->
                            <!--</p>-->
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
                            <p>
                                 AMOUNT DUE : @format_currency( $invoice->final_total )
                            </p>      
                            
                            <p>&nbsp;</p>
                </td>
                </tr> 
            </tbody>
        </table>
        @endif

    <!--  -->

        <table  class="table"   style="width:100%;margin-top: 30px;text-align:left;  border-radius:.3rem"  dir="ltr" >
            <thead  >

                <tr>
                    
                    <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:5%;text-align:left;">{{ trans('home.NO') }}</th>
                     @php $se = 0; @endphp
                    @foreach($allData as $data) @if( $data->se_note != null && $data->se_note !="")    @php $se = 1; @endphp @endif  @endforeach
                    @if($se != 0)    
                    <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:10%;text-align:left;">{{ trans('home.SE') }}</th>
                    @else
                    <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:10%;text-align:left;">{{ trans('home.SE') }}</th>

                    @endif
                    <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:10%;text-align:left;">{{ trans('product.sku') }}</th>
                    @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                    @else
                        <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:15%;text-align:left;">{{ trans('home.PHOTO') }}</th>
                    @endif
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff;width:30%">{{ trans('home.Product') }}</th>-->
                    <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:30%;text-align:left;">ITEM &  {{   trans('home.DESCRIPTION') }}</th>
                    <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:5%;text-align:left;">{{ trans('home.QTY') }}</th> 
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">Unit Price Exc.vat</th> -->
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">Unit Price Inc.vat</th> -->
                    @php $dis = 0; @endphp
                    @foreach($allData as $data) @if(($data->unit_price_before_discount - $data->unit_price) != 0) @php $dis = 1; @endphp @endif  @endforeach
                    @if($dis != 0)
                    <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:5%;text-align:left;">Tot Before Dis</th> 
                    <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:5%;text-align:left;">Dis</th> 
                    @endif
                    <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:20%;text-align:left;">RATE</th> 
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">Unit Cost Inc.vat	</th> -->
                    <th style="font-size:19px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:23%;text-align:left;font-family:arial">AMOUNT</th> 
                    
                </tr>

            </thead>
            <tbody>
                <?php $total = 0;  $count = 0 ; $count_id = 1 ;?>
                @foreach ($allData as $data)
                        <?php 

                                
                                $discount =  $data->line_discount_amount ;
                                $total   += ($data->unit_price_inc_tax*$data->quantity);


                        ?>
                        <tr style="margin-bottom:1px; !important;">
                            @php $count += $data->quantity; @endphp
                            <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;">{{ $count_id++;  }}</td>
                            @if($data->se_note != null && $data->se_note != "")
                                    <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;">{{ $data->se_note }}</td>
                            @else
                                    <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;"> </td>
                            
                            @endif
                            <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;">{{ $data->product->sku }}</td>
                             
                            @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                            
                            @else
                                <td style="font-size:19px;width:20%;border-bottom: 1px solid grey;">
                                    @if($data->product->image_url)
                                    <img src="{{ URL::to($data->product->image_url) }}" style="max-width: 20%"> 
                                    @endif
                                </td>
                            @endif
                            <!--<td style="font-size:19px;width:10% ">-->
                            <!--    {{ $data->product->name }}-->
                            
                            <!--<br>-->
                            @if(!empty($data->warranties->first()))
                                <small  style=" paffing:20px;font-size:17px;background-color:#f1f1f1 ; padding:10px;margin: 10px 0px !important;">
                                    
                                    {{$data->warranties->first()->display_name ?? ''}} 
                                </small>
                                <br>
                                <small  style="background-color:#f1f1f1 ;border: 1px solid black; border-radius:.2rem;padding:5px;margin: 20px 0px">
                                    
                                    {{-- {{ @format_date($data->warranties->first()->getEndDate($data->transaction_date))}} --}}
                                </small>
                            @endif

                        </td>
                        @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                       
                            <td style="font-size:18px;width:70%;border-bottom: 1px solid grey; ">
                                <p><b>{{ $data->product->name }}</b></p>
                                
                                <pre> {!! $data->sell_line_note  !!} </pre></td>
                        @else 
                        <td style="font-size:15px;width:70%;border-bottom: 1px solid grey; ">
                                <p><b>{{ $data->product->name }}</b></p>
                                 <pre> {!! $data->sell_line_note !!} </pre></td>
                            <!--<td style="font-size:15px;width:70%;border-bottom: 1px solid grey; "><pre> {!! $data->sell_line_note !!} </pre></td>-->
                        
                        @endif
                             <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;">{{ $data->quantity }}</td>
                            <!--<td style="font-size:12px;font-weight: bold;">@format_currency($data->unit_price_before_discount)</td>-->
                            <!--<td style="font-size:12px;font-weight: bold;">@format_currency($data->unit_price_inc_tax)</td>-->
                            @if($discount!=0)
                                <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;">@format_currency($data->unit_price_before_discount)</td> 
                                <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;">@format_currency($discount)</td> 
                            @elseif($dis != 0)
                                <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;">{{0}}</td> 
                                <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;">{{0}}</td> 
                            @endif
                            <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;">@format_currency($data->unit_price)</td>
                            <!--<td style="font-size:12px;font-weight: bold;">@format_currency($data->unit_price_inc_tax - $discount)</td>-->
                            <td style="font-size:19px;width:5%;border-bottom: 1px solid grey;">@format_currency($data->unit_price*$data->quantity)</td>
                        
                        </tr>
                        

                @endforeach
        
            </tbody>
        </table>
         @if($invoice->sub_status == 'quotation'|| (($invoice->sub_status == 'proforma' && $invoice->status == 'draft' )|| $invoice->status == 'ApprovedQuotation'))
         <table class="table"   style="width:100%;font-size:11px;Lline-height:10px; margin-top: 10px;font-weight: bold; ">
            <thead>
                <tr>
                    <td>@lang("purchase.qty") : {{$count}}</td>
                </tr>
            </thead>
        </table>
        @endif
        <table class="table"    style="width:100%;font-size:11px;Lline-height:10px; margin-top: 10px; "  dir="ltr"    dir="ltr" >
            <tbody >
                <tr style="width:100% " >
                    
                    <td style="text-align:left;width:65%">
                       @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                        {!! $layout->invoice_text !!} 
                       @endif
                    </td>
                    <td style="text-align:left;width:35%;font-weight: bold;">
                        <div  style=" width:100%">
                            <span   style="float:left;width:50%;font-family:arial">Subtotal:  </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span   style="float:right;width:50%">@format_currency($invoice->total_before_tax)</span>
                        </div>
                        
                        <br>
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
                             <div  style=" width:100%">
                                <span   style="float:left;width:50%">Discount:	    </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span   style="float:right;width:50%">@format_currency( $dis )</span>
                            </div>
                    
                            
                            <br>
                            <div  style=" width:100%">
                                <span   style="float:left;width:50%">Total After Dis  :  </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span   style="float:right;width:50%">@format_currency( $invoice->total_before_tax - $dis  )</span>
                            </div>
                            	  
                             <br>
                        @endif
                        <div  style=" width:100%">
                            <span   style="float:left;width:50%">VAT 5%  :	   </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span   style="float:right;width:50%">@format_currency( $invoice->tax_amount )</span>
                        </div>
                         
                        <hr style="background-color:black;border:2px solid black">
                       <div  style=" width:100%">
                            <span   style="float:left;width:50%"> Total:  </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span   style="float:right;width:50%">@format_currency( $invoice->final_total )</span>
                        </div>
                       	  
                    </td>
                </tr>
            </tbody>
        </table>
        @if($invoice->sub_status != 'quotation')
            <table  style="width:100%;font-size:12px;margin-top: 30px;text-align:left;border-bottom: 5px solid #1b71bc;"  dir="ltr" >
            <tbody>
                <tr>
                    <td style="width:20%">

                    </td>
                    <td style="width:80%;text-align:right;border:1px solid black;color:#fff;text-align:center;padding-right:30px;">
                        <span>
                            <span>{{ QrCode::format('svg')->size(40,40)->generate(url('https://me-qr.com/OVu7mxgl/')) }}</span>
                            <img src="{{asset("../../../uploads/img/loc.png")}}"   style="max-width: 30px;height:30px">
                        </span>
                        <span>
                            <span>{{ QrCode::format('svg')->size(40,40)->generate(url('https://wa.me/971501770199?text=Hello%2C%20i%20have%20a%20question%20about%20a%20product!')) }}</span>
                            <img src="{{asset("../../../uploads/img/whts.png")}}"   style="max-width: 30px;height:30px">
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
        @endif
        @if($invoice->sub_status == 'quotation')
        <table class="table" style="width:100%;font-size:12px;margin-top: 30px;text-align:left;border: 1px solid #BCBAB9;"  dir="ltr" >
            <tbody>
                <tr>
                    <td>
                            @php
                                $trm = \App\Models\QuatationTerm::where("id",$invoice->additional_notes)->first();
                            @endphp
                             @if(!empty($trm))
                                {!!  $trm->description  !!}
                            @endif
                    </td>
                    
                </tr>
            </tbody>
        </table>
        @endif
    </div>
</body>

</html>