<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Sell</title>




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

        <table style="width: 100%">
            <tbody>
                <tr>
                   <td>
                    {{-- <img src="{{ URL::to('uploads/invoice_logos/'.$data->logo) }}"  style="max-width: 450px;max-height:200px" > --}}
                   </td>
                   <td style="text-align: right;
                            line-height: 26px;
                            font-size: 18px;
                        "> {!! $data->header_text !!}</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <tbody>
               
                <tr>
                   <td style="
                                padding: 0 20px;
                                border: 2px solid #8e0f82;
                                border-radius: 20px;
                                position: relative;
                                width:50%
                            ">
                     <span  style="position: absolute;
                     top: -14px;
                     right: 43px;
                     background: #fff;
                     font-size: 19px;
                     font-weight: bolder;"> {{ trans('home.Customer Information') }} </span>
                    <p style="font-size: 16px;
                    line-height: 24px;
                    font-weight: bolder;">
                        {{ trans('home.Attention') }}: {{ $invoice->contact?$invoice->contact->name:' ' }}
                        </n>
                        {{ trans('home.Date').' : '.date('d-M-Y',strtotime($invoice->created_at)) }} 
                        </br>
                         {{ trans('home.Company Name')  }} :  {{ $invoice->business?$invoice->business->name:' ' }} 
                        </br>
                            {{ trans('home.Quote Ref') }}.: {{$invoice->invoice_no }}
                        </br>
                            {{ trans('home.Address') }}: {{ ( $invoice->contact)? $invoice->contact->address_line_1:' ' }}
                        </br>
                            {{ trans('home.Contact Info') }}: {{ ( $invoice->contact)? $invoice->contact->mobile:' ' }}
                        </br>
                            {{ trans('home.Project Type') }}: Kitchen Restaurant FOB: Dubai
                        </br>
                        {{ trans('home.Loctaion') }} : Silicon Oasis, Duba
                    </p>
                    
                   </td>
                   <td style="width: 25%;padding:15px;text-align:center;color:#fff">
                    {{ QrCode::format('svg')->size(170,200)->generate(url('https://www.youtube.com/watch?v=l3xZO_pZdRQ')) }}
                    </td>
                   <td style="padding: 0 20px;
                                border: 2px solid #8e0f82;
                                border-radius: 20px;
                                position: relative;
                                width:25%">
                            <span  style="position: absolute;
                            top: -14px;
                            right: 43px;
                            background: #fff;
                            font-size: 19px;
                            font-weight: bolder;"> Particualrs</span>
                            <p style="font-size: 16px;
                            line-height: 24px;
                            font-weight: bolder;">
                                Date 26-Apr-2022
                            </br>
                                Quote Ref.: DIK/2022/02/2299/R3
                                </br>
                                    Project No.: 22099
                                </br>
                                Sales Rep.: RD
                            </br>
                                FOB: Dubai
                                </br>
                                Prepared By: TR
                            </p>
                   </td>
                </tr>
            </tbody>
        </table>

       <!--  -->

        <table class="table" style="width:100%;margin-top: 30px;text-align:left;border: 1px solid;"  dir="ltr" >

                    <thead>

                        <tr>
                             <th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.NO') }}</th>

                             <th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.PHOTO') }}</th>
                             <th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.Product') }}</th>

                            <th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.ITEM CODE') }}</th>

                            <th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.DESCRIPTION') }}</th>

                            <th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.DIMENSION(mm)') }}</th> 
                            <th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.QTY') }}</th> 
                            <th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.UNIT PRICE') }}</th> 
                            <th style="font-size:12px;font-weight: bold;background-color:#000;color:#fff">{{ trans('home.TOTAL PRICE') }}</th> 
                        </tr>

                    </thead>

                    <tbody>
                        @foreach ($allData as $data)
                                <tr>
                                    <td style="font-size:12px;font-weight: bold;">{{ $data->product->sku }}</td>
                                    <td style="font-size:12px;font-weight: bold;">
                                        {{-- <img src="{{ URL::to($data->product->image_url) }}" style="max-width: 120px"> --}}
                                    </td>
                                    <td style="font-size:12px;font-weight: bold;">{{ $data->product->name }}</td>
                                    <td style="font-size:12px;font-weight: bold;">{{ $data->product->barcode_type }}</td>
                                    <td style="font-size:12px;font-weight: bold;">{{ strip_tags($data->product->product_description) }}</td>
                                    <td style="font-size:12px;font-weight: bold;">{{ $data->product->barcode_type }}</td>
                                    <td style="font-size:12px;font-weight: bold;">{{ $data->product->barcode_type }}</td>
                                    <td style="font-size:12px;font-weight: bold;">{{ $data->product->barcode_type }}</td>
                                    <td style="font-size:12px;font-weight: bold;">{{ $data->product->barcode_type }}</td>

                                
                                </tr>
                        @endforeach
                    </tbody>

                </table>

    </div>

</body>

</html>