<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Recieved Note</title>




    <style type="text/css">



        body{

            background-color: #ffffff;

        }

        .bill{

            min-height: 200px;

            background-color: #ffffff;

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
                <td>
                    <img src="http://order-uae.com/assets/dana.jpeg"   style="max-width: 300px">
                </td>
                
                <td style="text-align: right; font-size:12px
                        "> @if($layout) {!! $layout->header_text !!} @endif</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <tbody>
            
                <tr>
                <td style="width:40%;line-height:20px;font-size:16px;">
                    <h3 style="margin-bottom: 15px">{!! trans('home.Customer Information') !!} : </h3>
                    <p>
                        
                        
                    
                    {!! trans('home.Attention') !!}:  {!! $transaction->contact?$transaction->contact->name:' ' !!}
                        
                    
                    </p>
                    <p >
                        {!! trans('home.Date').' : '.date('d-M-Y',strtotime($transaction->created_at)) !!} 
                    </p>
                    <p >
                        {!! trans('home.Company Name')  !!} :  {!! $transaction->business?$transaction->business->name:' ' !!} 
                    </p>
                    <p >
                            {!! trans('home.Quote Ref') !!}.: {!!$Delivery->reciept_no !!}
                    </p>
                    <p >
                            {!! trans('home.Address') !!}: {!! ( $transaction->contact)? $transaction->contact->address_line_1:' ' !!} 
                    </p>
                    <p >
                            {!! trans('home.Contact Info') !!}:   {!! ( $transaction->contact)? $transaction->contact->mobile:' ' !!}
                    </p>
                  
                    
                </td>
                <td style="width: 60%; text-align:center;color:#fff;text-align:center;padding-right:30px">
                    {!! QrCode::format('svg')->size(150,150)->generate(url('reports/receive/'.$Delivery->id )) !!}
                    </td>
                
                    <td style=" width:40%;line-height:20px;font-size:16px;">
                            <h2> {!!  trans("home.received_note")  !!}</h2>
                            
                            <h3>&nbsp;</h3>
                             
                            <h3>Particualrs :</h3>
                            <p>
                                Date {!! date('M-d-Y',strtotime($transaction->created_at)) !!}
                            </p>
                            <p>
                                
                                Status.:  {!! ($transaction->status == "received" )? ($transaction->status) : (($Delivery->id != null)? (trans("home.Partial_received")) : $transaction->status) !!}
                                    
                                        
                                        
                            </p>
                            <p>
                                Project No.:  {!! $transaction->project_no !!}
                            </p>
                            <p>
                                
                            </p>      
                            
     

        <table class="table" style="width:100%;margin-top: 30px;text-align:left;border: 1px solid;"  dir="ltr" >
            <thead >

                <tr>
                    
                    
                    <th style="font-size:12px;font-weight: bold;background-color:#d31084 !important;color:#fff;width:10%">{!! trans('home.NO') !!}</th>
                    <th style="font-size:12px;font-weight: bold;background-color:#d31084 !important;color:#fff;width:70%">{!! trans('home.DESCRIPTION') !!}</th>
                    <th style="font-size:12px;font-weight: bold;background-color:#d31084 !important;color:#fff;width:10%">{!! trans('purchase.qty_total') !!}</th> 
                    
                        
                    
                </tr>

            </thead>
            <tbody>
                @php $total = 0;$total_qty=0; @endphp
                @foreach ($allData as $data)
                        @php
                                $sum = \App\PurchaseLine::where("transaction_id",$data->transaction_id)->where("product_id",$data->product->id)->sum("quantity");
                                $discount =  $data->unit_price_before_discount - $data->unit_price;
                                $total += ($data->unit_price_inc_tax*$data->quantity);
                                $total_qty += $data->current_qty;
                        @endphp
                        <tr>
                   
                            <td style="font-size:16px;;width:10%"> {!!$data->product->sku!!}</td>
                            <td style="font-size:16px;;width:70%">
                                {!!$data->product->product_description !!}
                            </td>
                           
                            <td style="font-size:16px;;width:10%">{!!$data->current_qty!!} </td>
                        
                        </tr>
                @endforeach
            </tbody>
        </table>
        <table class="table" style="width:100%;margin-top: 12px;font-size:10px; "  dir="ltr"    dir="ltr" >
            <tbody >
                <tr>
                    <td style="text-align:right;font-size:12px;  ">
                        Total Quantity:	 {!!$total_qty!!}
                    </td>
                </tr>
            
            </tbody>
        </table>
        <div class="container" style="width:100%;">
            
                <div class="col-6" style="float:left;font-size:12px;width:50%">
                    @if($layout) {!! $layout->delivery_text !!} @endif
                </div>
                <div class="col-6" style="margin:10px;margin-top:20px;font-size:12px; float:right;width:40%">
                    <div>{!!"Prepared By : "!!}</div><br>
                    <div>{!!"Accounts : "!!}</div><br>
                    <div>{!!"Received By : "!!}</div><br>
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