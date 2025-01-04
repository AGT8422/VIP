<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Return Sell</title>




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

            padding: 0.05rem;

            vertical-align: top;

            border-top: 1px solid #dee2e6;

        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {

            border-color: #ededed;

            padding: 3px;

        }
        table{
            max-width: 1100px;
            margin: 0 auto;
        }

    </style>

</head>

<body>

    <div class="bill"  >
        @php    $company_name = request()->session()->get("user_main.domain");  @endphp
        <table style="width: 100%;margin-bottom:5px;border-bottom:2px solid #b0906c; padding-bottom:25px">
            <tbody>
                <tr>
                <td>
                    {{-- <img src="http://order-uae.com/assets/dana.jpeg"   style="max-width: 300px;height:320px"> --}}
                    @if(!empty(Session::get('business.logo')))
                        <img style="max-width: 100%;height:100px" src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . Session::get('business.logo') ) }}" alt="Logo">
                    @endif
                </td>
                <td style="text-align: right; width:400px;
                            line-height:16px;font-size:12px;
                        "> @if($layout) {!! $layout->header_text !!} @endif</td>
                </tr>
            </tbody>
        </table>
        
        <table style="width: 100%">
            <tbody>
            
                <tr>
                <td style="width:50%;line-height:5px;font-size:10px;">
                    <h3 style="margin-bottom: 15px">{{ trans('home.Customer Information') }} : </h3>
                    <p>
                        {{ trans('home.Company Name')  }} : {{ $invoice->contact?$invoice->contact->name:' ' }}
                    </p>
                    @if($invoice->contact->supplier_business_name)
                    <p>
                        {{ trans('home.Attention') }}: {{ $invoice->contact?($invoice->contact->supplier_business_name ." ". $invoice->contact->middle_name ." ". $invoice->contact->last_name  ):' ' }}
                    </p>
                    @endif
                    <p >
                        {{ trans('home.Date').' : '.date('d-M-Y',strtotime($invoice->created_at)) }} 
                    </p>
                    {{-- <p >
                        {{ trans('home.Company Name')  }} :  {{ $invoice->business?$invoice->business->name:' ' }} 
                    </p> --}}
                    <p >
                            {{ trans('home.Ref No') }}.: {{$invoice->invoice_no }}
                    </p>
                    @if($invoice->contact->address_line_1)
                        <p >
                                {{ trans('home.Address') }}: {{ ( $invoice->contact)? ( $invoice->contact->address_line_1 .", ". $invoice->contact->city .", ". $invoice->contact->country ):' ' }}
                        </p>
                    @endif
                    <p >
                            {{ trans('home.Contact Info') }}: {{ ( $invoice->contact)? $invoice->contact->mobile:' ' }}
                    </p>
                    <p >
                            {{ trans('lang_v1.email_address') }}: {{ ( $invoice->contact)? $invoice->contact->email:' ' }}
                    </p>
                    {{-- <p >
                            {{ trans('home.Project Type') }}: Kitchen Restaurant FOB: Dubai
                    </p>
                    <p >
                        {{ trans('home.Loctaion') }} : Silicon Oasis, Duba
                    </p> --}}
                    
                </td>
                <td style="width: 40%;text-align:center;color:#fff;text-align:center;padding-right:30px;">
                    {{ QrCode::format('svg')->size(150,150)->generate(url('reports/sell/'.$invoice->id.'?invoice_no='.$invoice->invoice_no)) }}
                    </td>
                    <td style=" width:40%;line-height:5px;font-size:10px;">
                        <h2 style=" width:100% !important;      text-align:right !important;  text-transform: capitalize; ">
                            @if($invoice->sub_status == 'quotation')
                            Quotation
                            @elseif(($invoice->sub_status == '' && $invoice->status == 'draft'  )  )
                            Draft
                            @elseif(($invoice->sub_status == 'proforma' && $invoice->status == 'draft' )|| $invoice->status == 'ApprovedQuotation')
                            Approved Quotation
                            @elseif($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                            Tax Invoice   
                            @else
                            Return Tax Invoice   

                            @endif
                             
                        </h2>
                            <h3>&nbsp;</h3>
                            <h3>Particualrs :</h3>
                            <p>
                                Date {{ date('M-d-Y',strtotime($invoice->created_at)) }}
                            </p>
                            <p>
                                Status.: @if($invoice->sub_status == 'quotation')
                                            Quotation
                                        @elseif($invoice->sub_status == 'proforma')
                                            Approved Quotation
                                        @else
                                            {{ $invoice->status  }}
                                        @endif
                            </p>
                            <p>
                                Project No.: {{ $invoice->project_no }}
                            </p>
                            <p>
                                @php
                                $it = \App\Transaction::agent($invoice->agent_id,$invoice->transaction_id);
                                @endphp
                            Agent : {{ $it }}
                            </p>
                            <p>
                                <!--Sales Rep.: RD-->
                            </p>      
                            
                            <p>&nbsp;</p>
                </td>
                </tr> 
            </tbody>
        </table>

    <!--  -->

        <table class="table" style="width:100%;margin-top: 30px;text-align:left;border: 0px solid; border-radius:.3rem"  dir="ltr" >
            <thead>

                <tr>
                    <th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000;width:10%">{{ trans('home.NO') }}</th>
                    @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                    @else
                        <th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000;width:15%">{{ trans('home.PHOTO') }}</th>
                    @endif
                    <th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000;width:30%">{{ trans('home.Product') }}</th>
                    <th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000;width:30%">{{ trans('home.DESCRIPTION') }}</th>
                    <th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000;width:5%">{{ trans('home.QTY') }}</th> 
                    <!--<th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000">Unit Price Exc.vat</th> -->
                    <!--<th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000">Unit Price Inc.vat</th> -->
                    @php $dis = 0; @endphp
                    @foreach($allData as $data) @if(($data->unit_price_before_discount - $data->unit_price) != 0) @php $dis = 1; @endphp @endif  @endforeach
                    @if($dis != 0)
                    <th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000;width:5%">Tot Before Dis</th> 
                    <th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000;width:5%">Dis</th> 
                    @endif
                    <th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000;width:5%">Unit Cost Exc.vat</th> 
                    <!--<th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000">Unit Cost Inc.vat	</th> -->
                    <th style="font-size:9px;font-weight: bold;background-color:#00000000;border-top:1px solid black;border-bottom:1px solid black;color:#000;width:5%">Subtotal	</th> 
                    
                </tr>

            </thead>
            <tbody>
                <?php $total = 0; ?>
                @foreach ($allData as $data)
                        <?php 

                                
                                $discount =  $data->line_discount_amount ;
                                $total   += ($data->unit_price_inc_tax*$data->quantity);


                        ?>
                        <tr>
                            <td style="font-size:9px;width:5%">{{ $data->product->sku }}</td>
                            
                            @if($invoice->sub_status == 'final' || $invoice->sub_status == 'delivered' || $invoice->sub_status == 'f' )
                            
                            @else
                                <td style="font-size:9px;width:15%">
                                    @if($data->product->image_url)
                                    <img src="{{ URL::to($data->product->image_url) }}" style="max-width: 100%"> 
                                    @endif
                                </td>
                            @endif
                            <td style="font-size:9px;width:10% ">
                                {{ $data->product->name }}
                            
                            <br>
                            @if(!empty($data->warranties->first()))
                                <small  style=" paffing:20px;font-size:15px;background-color:#f1f1f1 ; padding:10px;margin: 10px 0px !important;">
                                    
                                    {{$data->warranties->first()->display_name ?? ''}} 
                                </small>
                                <br>
                                <small  style="background-color:#f1f1f1 ;border: 1px solid black; border-radius:.2rem;padding:5px;margin: 20px 0px">
                                    
                                    {{-- {{ @format_date($data->warranties->first()->getEndDate($data->transaction_date))}} --}}
                                </small>
                            @endif

                        </td>
                            <td style="font-size:9px;width:40%">{{ strip_tags($data->sell_line_note) }}</td>
                            <td style="font-size:9px;width:5%">{{ $data->quantity }}</td>
                            <!--<td style="font-size:12px;font-weight: bold;">@format_currency($data->unit_price_before_discount)</td>-->
                            <!--<td style="font-size:12px;font-weight: bold;">@format_currency($data->unit_price_inc_tax)</td>-->
                            @if($discount!=0)
                                <td style="font-size:9px;width:5%">@format_currency($data->unit_price_before_discount)</td> 
                                <td style="font-size:9px;width:5%">@format_currency($discount)</td> 
                            @elseif($dis != 0)
                                <td style="font-size:9px;width:5%">{{0}}</td> 
                                <td style="font-size:9px;width:5%">{{0}}</td> 
                            @endif
                            <td style="font-size:9px;width:5%">@format_currency($data->unit_price)</td>
                            <!--<td style="font-size:12px;font-weight: bold;">@format_currency($data->unit_price_inc_tax - $discount)</td>-->
                            <td style="font-size:9px;width:5%">@format_currency($data->unit_price*$data->quantity)</td>
                        
                        </tr>
                        

                @endforeach
        
            </tbody>
        </table>
        <table class="table" style=" border:0px solid black;width:100%;font-size:13px;  margin-top: 2px; "  dir="ltr"  dir="ltr"  >
            <tbody>
                <tr>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td>
                        Total:	<span class="pull-right" style="float:right">@format_currency($invoice->total_before_tax)</span>
                    </td>
                </tr>
                 @if($invoice->discount_amount != 0)
                <tr>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td>
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
                        Discount:	(-)     <span class="pull-right" style="float:right">@format_currency( $dis )</span>
                    </td>
                </tr>
                <tr>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td>
                        Total After Dis  :	  <span class="pull-right" style="float:right">@format_currency( $invoice->total_before_tax - $dis  )</span>
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td>
                        Tax ({{ $invoice->tax->name  }} %):	 (+)  <span class="pull-right" style="float:right">@format_currency( $invoice->tax_amount )</span>
                    </td>
                </tr>
                <tr>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td>
                        Total:	  <span class="pull-right" style="float:right">@format_currency( $invoice->final_total )</span>
                    </td>
                </tr>
            </tbody>
        </table>
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
    </div>
</body>

</html>