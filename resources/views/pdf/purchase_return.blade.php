<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Return Purchase</title>




    <style type="text/css">

        body{

            background-color: #f7f7f7;

        }

        .bill{

            min-height: 200px;

            background-color: #f7f7f7;

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

        <table style="width: 100%;margin-bottom:5px;border-bottom:2px solid #8e0f82">
            <tbody>
                <tr>
                   <td style="width: 40%;">
                    <img src="{{asset("../../../uploads/img/danal.png")}}"    style="max-width: 300px">
                   </td>
                    <td style="width: 0%;"></td>
                   <td style="width: 70%;padding-left:10px;"> @if($layout) {!! $layout->header_text !!} @endif</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <tbody>
               
                <tr>
                   <td style="width:50%;line-height:20px;font-size:16px;">
                     <h3 style="margin-bottom: 15px">{{ trans('home.Supplier Information') }} : </h3>
                    <p >
                        {{ trans('home.Company Name')  }} :  {{ $invoice->business?$invoice->contact->name:' ' }} 
                    </p>
                    @if($invoice->contact->supplier_business_name)
                    <p>
                        {{ trans('home.Attention') }}: {{ $invoice->contact?($invoice->contact->supplier_business_name ." ". $invoice->contact->middle_name ." ". $invoice->contact->last_name  ):' ' }}
                    </p>
                    @endif
                    <p >
                        {{ trans('home.Date').' : '.date('d-M-Y',strtotime($invoice->created_at)) }} 
                    </p>
                    <p >
                            {{ trans('home.Ref No') }}.: {{$invoice->ref_no }}
                    </p>
                    @if($invoice->contact->address_line_1)
                    <p >
                            {{ trans('home.Address') }}: {{ ( $invoice->contact)? $invoice->contact->address_line_1:' ' }}
                    </p>
                    @endif
                    <p >
                            {{ trans('home.Contact Info') }}: {{ ( $invoice->contact)? $invoice->contact->mobile:' ' }}
                    </p>
                    @if($invoice->contact->email)
                    <p >
                            {{ trans('lang_v1.email_address') }}: {{ ( $invoice->contact)? $invoice->contact->email:' ' }}
                    </p>
                    @endif
                    {{-- <p >
                            {{ trans('home.Project Type') }}: Kitchen Restaurant FOB: Dubai
                    </p>
                    <p >
                        {{ trans('home.Loctaion') }} : Silicon Oasis, Duba
                    </p> --}}
                    
                   </td>
                   <td style="width: 50%;text-align:center;color:#fff;text-align:center;padding-right:30px">
                    {{ QrCode::format('svg')->size(150,150)->generate(url('reports/purchase/'.$invoice->id.'?ref_no='.$invoice->ref_no)) }}
                    </td>
                    <td style=" width:40%;line-height:20px;font-size:16px;">
                             <h2 style="text-transform: capitalize;margin-bottom:15px;line-height:30px">
                                @if($invoice->status == 'received' || $invoice->status == 'final'  )
                                 Return  Purchase   

                                @elseif($invoice->status == 'ordered' || $invoice->status == 'pending'  )
                                   Return Purchase  Order
                                @endif    
                                
                                 
                             </h2>
                             <h3>&nbsp; &nbsp;&nbsp;&nbsp;</h3>
                             <h3>Particualrs :</h3>
                            <p>
                                Date {{ date('M-d-Y',strtotime($invoice->created_at)) }}
                            </p>
                            <p>
                               
                                   Status.: {{  $invoice->status  }}
                            </p>
                            <p>
                               
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

        <table class="table" style="width:100%;margin-top: 30px;text-align:left;  border-radius:.3rem" dir="ltr" >
            <thead>

                <tr>
                    <th style="font-size:12px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:10%;text-align:left;">{{ trans('home.NO') }}</th>
                    <th style="font-size:12px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:10%;text-align:left;">{{ trans('home.PHOTO') }}</th>
                    <th style="font-size:12px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:10%;text-align:left;">{{ trans('home.Product') }}</th>
                    <th style="font-size:12px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:10%;text-align:left;">{{ trans('home.DESCRIPTION') }}</th>
                    <th style="font-size:12px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:10%;text-align:left;">{{ trans('home.QTY') }}</th> 
                    <th style="font-size:12px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:10%;text-align:left;">Unit Price</th> 
                    <th style="font-size:12px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black;width:10%;text-align:left;">Total	</th> 
                    
                </tr>

            </thead>
            <tbody>
                <?php 
                    $total = 0;
                    $final_total = 0;
                ?>
                @foreach ($invoice->purchase_lines as $data)
                        <?php 
                                if($invoice->dis_type == 1){
                                    $discount =  $data->pp_without_discount - $data->discount_percent; 
                                }else{
                                    $discount =  ($data->discount_percent/100)*$data->pp_without_discount;  
                                }
                                if(!empty($invoice->tax)){
                                    $tax = $invoice->tax->amount;
                                }else{
                                    $tax = 0;
                                }
                                
                                $total       += $data->pp_without_discount + $data->pp_without_discount*($tax/100) ;
                                $final_total += $discount;
                        ?>
                        <tr>
                            <td style="font-size:12px;width:5%;border-bottom: 1px solid grey;text-align:center;">{{ $data->product->sku }}</td>
                            <td style="font-size:12px;width:5%;border-bottom: 1px solid grey;text-align:center;">
                                @if($data->product->image_url)
                                 <img src="{{ URL::to($data->product->image_url) }}" style="max-width: 120px"> 
                                @endif
                            </td>
                            <td style="font-size:12px;width:5%;border-bottom: 1px solid grey;text-align:center;">{{ $data->product->name }}</td>
                            <td style="font-size:12px;width:5%;border-bottom: 1px solid grey;text-align:center;">{!! $data->purchase_note !!}</td>
                            <td style="font-size:12px;width:5%;border-bottom: 1px solid grey;text-align:center;">{{ $data->quantity }}</td>
                            <td style="font-size:12px;width:5%;border-bottom: 1px solid grey;text-align:center;">{{  $data->purchase_price}} </td>
                            <td style="font-size:12px;width:5%;border-bottom: 1px solid grey;text-align:center;"> {{ number_format($data->purchase_price*$data->quantity,4) }}</td>
                        
                        </tr>
                @endforeach
            </tbody>
        </table>
        <table class="table" style="width:100%;margin-top: 10px;font-size:11px; font-weight:bold"    dir="rtl" >
            <tbody>
                @php
                    if(!empty($invoice->tax)){
                            $tax_  = $invoice->tax_amount;
                            $tax_n = $invoice->tax->name;
                    }else{
                            $tax_  = 0;
                            $tax_n = "";
                    }
                @endphp

                <tr>
                    <td style="float:right">
                        Total:   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  @format_currency($invoice->total_before_tax)
                    </td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td>
                    <td>
                        Total:  AED   {{ number_format($parent->total_before_tax , 4)}} 
                    </td>
                </tr>
                @if($parent->discount_amount != 0)
                <tr>
                    @php
                    if ($parent->discount_type == "fixed_before_vat"){
                        $dis = $parent->discount_amount;
                    }else if ($parent->discount_type == "fixed_after_vat"){
                        $tax = \App\TaxRate::find($parent->tax_id);
                        if(!empty($tax)){
                            $dis = ($parent->discount_amount*100)/(100+$tax->amount) ;
                        }else{
                            $dis = ($parent->discount_amount*100)/(100) ;
                        }
                    }else if ($parent->discount_type == "percentage"){
                        $dis = ($parent->total_before_tax *  $parent->discount_amount)/100;
                    }else{
                        $dis = 0;
                    }
                  
                    @endphp 
                    <td>
                        Discount:	(-)  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  @format_currency($dis)  
                    </td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td>
                </tr>

                <tr>
                    <td>
                        Total After Dis  :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	  @format_currency( $invoice->total_before_tax - $dis  )
                    </td>
                    <td style="border:0px solid black;color:transparent">{{""}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                </tr>
                @endif
                
                {{-- @if($tax_ != 0) --}}
                <tr>
                    <td>
                        Tax ({{ $tax_n  }} ):	 (+) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @format_currency($invoice->tax_amount)
                    </td>
                     <td style="border:0px solid black;color:transparent">{{""}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                   
                </tr>
                {{-- @endif --}}
                <tr>
                    @if(isset($dis)) @php $disc = $dis ; @endphp @else @php $disc = 0 ; @endphp @endif
                    <td>
                        Total:	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @format_currency($invoice->total_before_tax  + $invoice->tax_amount - $disc)   
                    </td>
                     <td style="border:0px solid black;color:transparent">{{""}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    <td style="border:0px solid black;color:transparent">{{"&nbsp;&nbsp;&nbsp;&nbsp;"}}</td>
                    
                    
                </tr>
            </tbody>
        </table>
        @if(isset($parent->additional_notes))
        <table class="table" style="width:100%;margin-top: 30px;text-align:left;border: 1px solid;font-size:12px;"  dir="ltr" >
            <tbody>
                <tr>
                    <td>
                        {{ $parent->additional_notes }}
                    </td>
                </tr>
            </tbody>
        </table>
        @endif
    </div>
</body>

</html>