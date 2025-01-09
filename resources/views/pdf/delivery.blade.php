<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Delivery Note</title>




    <style type="text/css">

        body{

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
        @php 
            $date_format    = request()->session()->get('business.date_format');  
            $company_name   = request()->session()->get("user_main.domain"); 
            // $currency       = \App\Currency::find($invoice->currency_id); 
            $mainCurrency   = \App\Models\ExchangeRate::where('source',1)->first();  
            // $symbol         = ($mainCurrency)?(\App\Currency::find($mainCurrency->currency_id)?\App\Currency::find($mainCurrency->currency_id):null):null;
            $business_color = '#b0906c';
        @endphp

    <div class="bill"  >

        <table style="width: 100%;margin-bottom:5px;border-bottom:2px solid {{$business_color}}">
            <tbody>
                <tr>
                   <td>
                    <img src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . Session::get('business.logo') ) }}"   style="max-width: 300px ">
                   </td>
                  
                   <td style="text-align: right;
                            line-height:15px;font-size:12px;
                        "> @if($layout) {!! $layout->header_text !!} @endif</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <tbody>
               
                <tr>
                   <td style="width:60%;line-height:20px;font-size:16px;">
                     <h3 style="margin-bottom: 15px">{{ trans('home.Customer Information') }} : </h3>
                    <p>
                        
                        
                    
                    {{ trans('home.Company Name') }}:  {{ $transaction->contact?$transaction->contact->name:' ' }}
                        
                    
                    </p>
                    @if($transaction->contact->supplier_business_name)
                    <p>
                        {{ trans('home.Attention') }}: {{ $transaction->contact?($transaction->contact->supplier_business_name ." ". $transaction->contact->middle_name ." ". $transaction->contact->last_name  ):' ' }}
                    </p>
                    @endif
                    <p >
                        {{ trans('home.Date').' : '.date('d-M-Y',strtotime($Delivery->date)) }} 
                    </p>
                    <p >
                            {{ trans('home.Quote Ref') }}.: {{$Delivery->reciept_no }}
                    </p>
                    <p >
                            {{ trans('home.Address') }}: {{ ( $transaction->contact)? $transaction->contact->address_line_1:' ' }} 
                    </p>
                    <p >
                            {{ trans('home.Contact Info') }}:   {{ ( $transaction->contact)? $transaction->contact->mobile:' ' }}
                    </p>
                    {{-- <p >
                            {{ trans('home.Project Type') }}: Kitchen Restaurant FOB: Dubai
                    </p>
                    <p >
                        {{ trans('home.Loctaion') }} : Silicon Oasis, Duba
                    </p> --}}
                    
                   </td>
                   <td style="width: 40%;text-align:center;color:#fff;text-align:center;padding-right:30px">
                    {{ QrCode::format('svg')->size(150,150)->generate(url('reports/delivery/'.$Delivery->id )) }}
                    </td>
                   
                    <td style=" width:40%;line-height:20px;font-size:16px;">
                            <h2> {{  trans("home.delivery_note")  }}</h2>
                            <h2>&nbsp;</h2>
                            <h3>Particualrs :</h3>
                            <p>
                                Date {{ date('M-d-Y',strtotime($Delivery->date)) }}
                            </p>
                            <p>
                                 
                                Status.:  {{  ($transaction->status == "delivered" )? $transaction->status : (($Delivery->id != null)? trans("home.Partial_delivery") : $transaction->status) }}
                                      
                                        
                                        
                            </p>
                            <p>
                                Project No.:  {{  $transaction->project_no }}
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

        <table class="table" style="width:100%;margin-top: 30px;text-align:left;border: 0px solid;"  dir="ltr" >
            <thead >

                <tr>
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.PHOTO') }}</th>-->
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.Product') }}</th>-->
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">Unit Price Exc.vat</th> -->
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">Unit Price Inc.vat</th> -->
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">Discount</th> -->
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">Unit Cost Exc.vat</th> -->
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">Unit Cost Inc.vat	</th> -->
                    <!--<th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">Subtotal	</th> -->
                    
                    <th style="font-size:12px;font-weight: bold;background-color:{{$business_color}} !important;color:#fff;width:10%">{{ trans('home.NO') }}</th>
                    <th style="font-size:12px;font-weight: bold;background-color:{{$business_color}} !important;color:#fff;width:70%">{{ trans('home.DESCRIPTION') }}</th>
                    {{-- <th style="font-size:12px;font-weight: bold;background-color:{{$business_color}} !important;color:#fff;width:10%">{{ trans('purchase.qty_total') }}</th>  --}}
                    <th style="font-size:12px;font-weight: bold;background-color:{{$business_color}} !important;color:#fff;width:10%">{{ trans('purchase.qty_total') }}</th> 
                    	
                    
                </tr>

            </thead>
            <tbody>
                <?php $total = 0;$total_qty=0; ?>
                @foreach ($allData as $data)
                        <?php 
                                // $sum = \App\TransactionSellLine::where("transaction_id",$data->transaction_id)->where("product_id",$data->product->id)->sum("quantity");
                                $discount =  $data->unit_price_before_discount - $data->unit_price;
                                $total += ($data->unit_price_inc_tax*$data->quantity);
                                $total_qty += $data->current_qty;
                        ?>
                        <tr>
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <td style="font-size:12px; width:10%"> {{$data->product->sku}}</td>
                            <td style="font-size:12px; width:70%">
                                @if($data->line)
                                    @if($data->line->sell_line_note)
                                        <b>{!! $data->product->name !!}</b>
                                        <pre>{!! $data->line->sell_line_note !!}</pre>
                                    @else
                                        {!! $data->product->name !!}
                                    @endif
                                @else
                                    {!! $data->product->name !!}
                                @endif
                            </td>
                            {{-- <td style="font-size:16px; width:10%">{{intval($sum)}} </td> --}}
                            <td style="font-size:12px; width:10%">{{$data->current_qty}} </td>
                        
                        </tr>
                @endforeach
                @foreach ($wrong as $it)
                        <?php 
                                // $sum = \App\TransactionSellLine::where("transaction_id",$data->transaction_id)->where("product_id",$data->product->id)->sum("quantity");
                                $discount =  $it->unit_price_before_discount - $it->unit_price;
                                $total += ($it->unit_price_inc_tax*$it->quantity);
                                $total_qty += $it->current_qty;
                        ?>
                        <tr>
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <!--<td style="font-size:12px;font-weight: bold;"> </td>-->
                            <td style="font-size:12px; width:10%"> {{$it->product->sku}}</td>
                            <td style="font-size:12px; width:70%">
                                @if($it->line->sell_line_note)
                                
                                {!! $it->line->sell_line_note!!}
                                @else
                                {!! $it->product->name !!}
                                @endif
                            </td>
                            {{-- <td style="font-size:16px; width:10%">{{intval($sum)}} </td> --}}
                            <td style="font-size:12px; width:10%">{{$it->current_qty}} </td>
                        
                        </tr>
                @endforeach
            </tbody>
        </table>
        <table class="table" style="width:100%;margin-top: 10px;font-size:11px; font-weight:bold"  dir="ltr"    dir="ltr" >
            <tbody >
                <tr>
                    
                    <td style="text-align:right;width:10%">
                        Total Quantity:	  {{$total_qty}}
                    </td>
                </tr>
              
            </tbody>
        </table>
        <div class="container" style="width:100%;">
             
                 <div class="col-6" style="float:left;width:50%;font-size:12px;">
                     @if($layout) {!! $layout->delivery_text !!} @endif
                 </div>
                 <div class="col-6" style="margin:10px;margin-top:20px;float:right;width:40%;font-size:12px;">
                     <div>{{"Prepared By : "}}</div><br>
                     <div>{{"Accounts : "}}</div><br>
                     <div>{{"Delivered By : "}}</div><br>
                 </div>
            
        </div>
        <table class="table" style="width:100%;margin-top: 30px;text-align:left;border: 1px solid;"  dir="ltr" >
            <tbody>
                <tr>
                   
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>