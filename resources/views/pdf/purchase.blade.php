<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Purchase</title>




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

    <div class="bill"  >
        @php    $company_name = request()->session()->get("user_main.domain");  @endphp
        <table style="width: 100%;margin-bottom:10px;border-bottom:2px solid #b0906c;padding-bottom:10px;">
            <tbody>
                <tr>
                   <td style="width: 80%;">
                    
                     
                    <span>@if($layout) {!! $layout->purchase_text !!} @endif</span>
                    
                   </td>
                  
                   <td style="width: 20%;text-align:right;color:#fff;text-align:center;padding-right:100px;">
                    <span> 
                        {{-- <img src="{{asset("../../../uploads/img/dana_puchase.png")}}"   style="max-width: 400px;height:120px"> --}}
                        @if(!empty(Session::get('business.logo')))
                            <img src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . Session::get('business.logo') ) }}"  style="width: 100%;max-height:120px" alt="Logo">
                        @endif 
                    </span>
                </td>
                
                <td style="  text-align:left;width:90%; ">
                     <h1 style="  width:100% ;font-size:60px;text-align:right !important; ">
                             &nbsp;&nbsp; @if($invoice->status == 'ordered')
                            PURCHASE ORDER
                            @elseif(($invoice->status == 'received'  )  )
                            PURCHASE
                            @elseif( $invoice->status == 'final'    )
                            PURCHASE
                            @elseif($invoice->status == 'pending'   )
                            PURCHASE ORDER
                            @endif
                        </h1>
                        <h3>&nbsp;</h3>
                     <div class=" text-align:left !important">
                            
                         <div style="font-size:25px; color:#b0906c;padding-left:100px;" > 
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span  >L.P.O. NO. </span> <span class="lpo  " name="lpo"  style="color:#000;width:200px;padding:10px;font-size:27px;border:0px solid grey">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$invoice->ref_no}}</span><br>  
                             @php $date_form = \Carbon::parse($invoice->transaction_date);   @endphp
                             <br>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>DATE </span> <span class="lpo  " name="lpo"  style="color:#000;width:200px;padding:10px;font-size:27px;border:0px solid grey">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $date_form->format('Y-m-d') }}</span><br>  
                             <br>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>PROJECT NO. </span> <span class="lpo  " name="lpo"  style="color:#000;width:200px;padding:10px;font-size:27px;border:0px solid grey">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$invoice->project_no }}</span><br>  
                         </div>
                      
                     </div>
                   
                     </td>
                </tr>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <tbody>
               
                <tr>
                   <td style="width:70%;font-size:14px;">
                      <p>
                        <span style="margin-bottom: 15px;color:#b0906c">{{ trans('SUPPLIER DETAILS') }} : </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@if($invoice->contact->first_name){{ $invoice->contact->first_name  }} @else @if($invoice->contact->name){{$invoice->contact->name . " "}} @endif @endif<br> 
                        @if($invoice->contact->address_line_1)<span style="margin-bottom: 15px;color:#fff">{{ trans('SUPPLIER DETAILS') }} : </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@if($invoice->contact->prefix){{$invoice->contact->prefix . " "}} @endif @if($invoice->contact->supplier_business_name){{$invoice->contact->supplier_business_name . " "  }}@endif @if($invoice->contact->middle_name){{ $invoice->contact->middle_name . " " }}@endif @if($invoice->contact->last_name){{ $invoice->contact->last_name    }}@endif <br> @endif
                        @if($invoice->contact->mobile|| $invoice->contact->alternate_number || $invoice->contact->landline)<span style="margin-bottom: 15px;color:#fff">{{ trans('SUPPLIER DETAILS') }} : </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@if($invoice->contact->mobile ){{   $invoice->contact->mobile  }}@endif  @if($invoice->contact->alternate_number ){{ ($invoice->contact->mobile)?" - ":" "  }}{{   $invoice->contact->alternate_number  }}@endif  @if($invoice->contact->landline ) {{ ($invoice->contact->alternate_number ||$invoice->contact->mobile )?" - ":" "  }}{{   $invoice->contact->landline  }}@endif <br> @endif
                        @if($invoice->contact->email)<span style="margin-bottom: 15px;color:#fff">{{ trans('SUPPLIER DETAILS') }} : </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ ( $invoice->contact)? $invoice->contact->email:' ' }} <br>@endif
                        @if($invoice->contact->tax_number)<span style="margin-bottom: 15px;color:#fff">{{ trans('SUPPLIER DETAILS') }} : </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ ( $invoice->contact)? $invoice->contact->tax_number:' ' }} <br>@endif
                        @if($invoice->contact->address_line_1 || $invoice->contact->address_line_2 )<span style="margin-bottom: 15px;color:#fff">{{ trans('SUPPLIER DETAILS') }} : </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@if($invoice->contact->address_line_1){{$invoice->contact->address_line_1 . " "}}@endif @if($invoice->contact->address_line_2) {{ ($invoice->contact->address_line_1)?" - ":" "  }} {{$invoice->contact->address_line_2 . " "}}@endif <br> @endif
                        @if($invoice->contact->city || $invoice->contact->state || $invoice->contact->country )<span style="margin-bottom: 15px;color:#fff">{{ trans('SUPPLIER DETAILS') }} : </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@if($invoice->contact->city) {{$invoice->contact->city . " "}}@endif @if($invoice->contact->state){{ ($invoice->contact->city)?" - ":" "  }}{{$invoice->contact->state . " "}}@endif @if($invoice->contact->country) {{ ($invoice->contact->state)?" - ":" "  }} {{$invoice->contact->country . " "}}@endif <br> @endif

                    
                        </span>
                      </p>
                    <p style="color:#b0906c" >
                            {{ trans('SUPPLIER QUOTE REF') }}. : <span  style="color:#000" > &nbsp;{{ $invoice->sup_refe }} </span>
                    </p>
                    
                  
                    <!--<p >-->
                    <!--    {{ trans('home.Date').' : '.date('d-M-Y',strtotime($invoice->created_at)) }} -->
                    <!--</p>-->
                    <!--@if($invoice->contact->address_line_1)-->
                    <!--<p >-->
                    <!--        {{ trans('home.Address') }} : {{ ( $invoice->contact)? $invoice->contact->address_line_1:' ' }}-->
                    <!--</p>-->
                    <!--@endif-->
                    <!--<p >-->
                    <!--        {{ trans('home.Contact Info') }} : {{ ( $invoice->contact)? $invoice->contact->mobile:' ' }}-->
                    <!--</p>-->
                    <!--@if($invoice->contact->email)-->
                    <!--<p >-->
                            
                    <!--</p>-->
                    <!--@endif-->
                    <!--{{-- <p >-->
                    <!--        {{ trans('home.Project Type') }} : Kitchen Restaurant FOB: Dubai-->
                    <!--</p>-->
                    <!--<p >-->
                    <!--    {{ trans('home.Loctaion') }} : Silicon Oasis, Duba-->
                    <!--</p> --}}-->
                    
                   </td>
                   <td style=" width:5%;"></td>
                  
                    <td style=" width:60%;font-size:10px;text-align:left;padding-left:100px">
                         <div class="display:flex">
                              <div style="font-size:15px; color:#b0906c" > 
                                 <span>DELIVERY TO </span>  <span class="lpo  " name="lpo"  style="color:#000;width:200px;padding:10px;font-size:15px;border:0px solid grey">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$invoice->store_from->name }}</span><br>
                                 <br>
                                 <span>CONTACT PERSON </span><span class="lpo  " name="lpo"  style="color:#000;width:200px;padding:10px;font-size:15px;border:0px solid grey"> &nbsp;&nbsp; {{  $invoice->sales_person->first_name   }}</span><br> 
                                 <br>
                                 <span>MOBILE </span><span class="lpo  " name="lpo"  style="color:#000;width:200px;padding:10px;font-size:15px;border:0px solid grey">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$invoice->sales_person->contact_number }}</span><br> 
                             </div>
                         </div>
                   </td>
                </tr> 
            </tbody>
        </table>

       <!--  -->

        <table class="table" style="width:100%;margin-top: 30px;text-align:left;"  dir="ltr" >
            <thead>

                <tr>
                    <th style="width:8%;font-size:14px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black; text-align:left;">{{ trans('S/N') }}</th>
                    <!--<th style="width:10%;font-size:14px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black; text-align:left;">{{ trans('home.PHOTO') }}</th>-->
                    <th style="width:15%;font-size:14px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black; text-align:left;">{{ trans('MODEL NO.') }}</th>
                    <th style="width:57%;font-size:14px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black; text-align:left;">ITEM & {{ trans('home.DESCRIPTION') }}</th>
                    <th style="width:8%;font-size:14px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black; text-align:left;">{{ trans('home.QTY') }}</th> 
                    <th style="width:15%;font-size:14px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black; text-align:left;">Unit Price</th> 
                    <th style="width:10%;font-size:14px;font-weight: bold;background-color:transparents;color:#000;border-bottom:1px solid black; text-align:left;">Total	</th> 
                    
                </tr>

            </thead>
            <tbody>
                <?php 
                    $total = 0;
                    $final_total = 0;$count = 0 ; $count_id = 1 ;
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
                            <td style="font-size:12px;">{{ $count_id++;  }}</td>
                            <!--<td style="font-size:12px;">-->
                            <!--    @if($data->product->image_url){{ $data->product->name }}-->
                            <!--     <img src="{{ URL::to($data->product->image_url) }}" style="max-width: 120px"> -->
                            <!--    @endif-->
                            <!--</td>-->
                             @php $count += $data->quantity; @endphp
                            <td style="font-size:12px;">{{ $data->product->sku }}</td>
                            <td style="font-size:12px;"><p><b>{{ $data->product->name }}</b></p><pre>{!! $data->purchase_note !!}</pre></td>
                            <td style="font-size:12px;">{{ $data->quantity }}</td>
                            <td style="font-size:12px;">{{  $data->purchase_price}} </td>
                            <td style="font-size:12px;"> {{ number_format($data->purchase_price*$data->quantity,4) }}</td>
                        
                        </tr>
                @endforeach
            </tbody>
        </table>
         <table class="table"   style="width:100%;font-size:11px;line-height:10px; margin-top: 10px;font-weight: bold; ">
            <thead>
                <tr>
                    <td>@lang("purchase.qty") : {{$count}}</td>
                </tr>
            </thead>
        </table>
        <table class="table"    style="width:100%;font-size:11px; ; margin-top: 10px; "  dir="ltr"    dir="ltr" >
            <tbody >
                <tr style="width:100% " >
                    
                    <td style="text-align:left;width:70%;font-size:3px ">
                         <b>{!! $layout->purchase_footer !!} </b> 
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
                            <!--<span   style="float:right;width:50%">@format_currency( $invoice->final_total )</span>-->
                             @if($invoice->discount_amount != 0)
                                 <span   style="float:right;width:50%">@format_currency( $invoice->total_before_tax - $dis + $invoice->tax_amount )</span>
                                @else
                                 <span   style="float:right;width:50%">@format_currency( $invoice->final_total )</span>
                                @endif
                        </div>
                       	  
                    </td>
                </tr>
            </tbody>
        </table>
                <table  style="width:100%;font-size:12px;margin-top: 30px;text-align:left;border-bottom: 5px solid #b0906c;"  dir="ltr" >
            <tbody>
                <tr>
                    <td>
                         Prepared By: 
                    </td>
                    <td>
                         Checked By: 
                    </td>
                    <td>
                         Approved By:
                    </td>
                    
                </tr>
                <tr>
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                </tr>
                <tr>
                      <td>&nbsp;</td>
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                    
                </tr>
                <tr >
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                 </tr>
                
            </tbody>
        </table>
        
    </div>
</body>

</html>