<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>@lang("home.gouranl_voucher")</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

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

            font-size: 12px;

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

        .bord{
             border: .5px solid #b0906c;
            padding: 10px;
            
         }
        .bords{
  
            
         }
        .title_voucher{
            border: 0px solid black;
            text-align: center;
            border-radius: 10px;
            font-size: x-large;
            padding: 10px;
            

        }
        .date_voucher{
            border: 0px solid black;
            text-align: right;
            border-radius: 10px;
            font-size: small;
            padding: 10px;
            font-weight: 400;
            margin-top: -70px;
            margin-bottom: 50px;
        }
        .references {
            font-size: medium;
              
        }
        .bodies{
            border: 0px solid black;
            width:100%;
            border-radius:.2rem;
        }
       .tab{
            width:100%;
         }
        tr {
            border-bottom: 1px solid #b0906c !important;
        }
       .head{
            background-color: #b0906c;
            color:#f7f7f7;
            border: 1px solid #b0906c;
        }
        
        td{
            border: 0px solid #b0906c;
            text-align: left;
            box-shadow: 2px 2px 1px black;
            padding:0px;
            line-height: 30px;
        }
        tbody th{
            color:  #000000;
            font-weight: 700;
            border-top:1px solid #727272;
            border-bottom:0px solid #727272;
            
        }
        tfoot th{
      
            color:  #000000;
            font-weight: 700;
            border-top:1px solid #727272;
            border-bottom:0px solid #727272;
            
        }
        th{
            font-size: 12px;
            color:  #ffffff;
            border-bottom:1px solid #b0906c;
            text-align: left;
            padding-left:3px;
            
        }
        .border_style{
            font-size: 10px;
            border-bottom: 1px solid black;
            padding: 5px;
        }
        b{
            font-weight:bold;
        }
    
    </style>

</head>

<body>
    @php  $company_name = request()->session()->get("user_main.domain");  @endphp

    <div class="bill"  >
        <table style="width: 100%;margin-bottom:5px;  padding-bottom:25px">
            <tbody>
                <tr >
                    <td class=" " style="border:0px solid grey;  border-radius: 5px 10px 15px 20px;">
                        {{-- <img src="{{asset("../../../uploads/img/danal.png")}}"    style=" padding:10px;max-width: 300px;height:350px;border-bottom:2px solid #b0906c;border-radius:10px"> --}}
                        @if(!empty(Session::get('business.logo')))
                            <img style=" padding:10px;max-width: 300px;height:350px;border-bottom:2px solid #b0906c;border-radius:10px" src="{{ asset( 'uploads/companies/'.$company_name.'/business_logo/' . Session::get('business.logo') ) }}" alt="Logo">
                        @endif
                    </td>
                    <td style="text-align: right; width:400px;padding:10px;
                            line-height:13px;font-size:12px;border-right:1px solid grey;
                        "> @if($layout) {!! $layout->header_text !!} @endif</td>
                </tr>
            </tbody>
        </table>
     
         
        <div class="title_voucher">
            <b> @lang("home.gouranl_voucher")</b>  <br>
            <span class="references"  > ( {{$invoice->ref_no}}  . {{"/"}} .  {{($entry)?$entry->refe_no_e:""}}  )</span>
        </div>
        <div class="date_voucher">
          
            <b> @lang("lang_v1.date") : </b>{{$invoice->date}} 
        </div>
        @php   $payment = \App\TransactionPayment::where("payment_voucher_id",$invoice->id)->first(); @endphp
        @php  if(!empty($payment))  { $trans  = \App\Transaction::where("id",$payment->transaction_id)->first(); }   @endphp
      

        <div class="bodies">
            <table class="tab">
                <thead >
                    <tr class="head">
                        <th style="border:1px solid white">  Code  </th>

                        <th style="border:1px solid white">  Account Name  </th>
                   
                        <th style="border:1px solid white">  Note  </th>

                        <th style="border:1px solid white">  Debit  </th>
                     
                        <th style="border:1px solid white">  Credit  </th>
                    </tr>
                </thead>
                <tbody>
                    
                    @php
                        $total_debit   =  0;
                        $total_credit  =  0;
                     @endphp
                    @foreach($items as $it)
                        @php
                            $ac     = \App\Account::where("id",$it->account_id)->first();
                            $total_debit  += (!empty($ac))?($it->credit):0;
                            $total_credit += (!empty($ac))?($it->debit):0;
                        @endphp
                    @if($it->credit != 0)
                        <tr>
                            <td class="border_style">{{$ac->account_number}}</td>
                            <td class="border_style">{{$ac->name}}</td>
                            <td class="border_style">{{$it->text}}</td>
                            <td class="border_style"></td>
                            <td class="border_style">{{number_format($it->credit,2)}} </td>
                        </tr>
                     @endif
                    
                    @if($it->debit != 0)
                        <tr>
                            <td class="border_style">{{$ac->account_number}}</td>
                            <td class="border_style">{{$ac->name}}</td>
                            <td class="border_style">{{$it->text}}</td>
                            <td class="border_style" >{{number_format($it->debit,2)}}</td>
                            <td class="border_style"></td>
                        </tr>
                         
                    @endif
                  
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="background-color:rgb(199, 199, 199); border:1px solid rgb(199, 199, 199)" class="border_style"> </td>
                        <td style="background-color:rgb(199, 199, 199); border:1px solid rgb(199, 199, 199)" class="border_style"> </td>
                        <td style="background-color:rgb(199, 199, 199); border:1px solid rgb(199, 199, 199);font-weight:bold" class="border_style"> Total   </td>
                        <td style="background-color:rgb(199, 199, 199); border:1px solid rgb(113, 113, 113);font-weight:bold" class="border_style"> {{number_format($total_debit,2)}}</td>
                        <td style="background-color:rgb(199, 199, 199); border:1px solid rgb(113, 113, 113);font-weight:bold" class="border_style"> {{number_format($total_credit,2)}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div>&nbsp;</div>
        <div>&nbsp;</div>
    
        <div style="font-size: 12px; text-align:left">
            <b>  Prepared By :</b>   <br>
        </div>
        <div style="font-size: 12px; margin-top:-15px;">
            <div style="width:50%;margin-left:70%;text-align:left">
                <b>  Approved By :</b>   <br>
            </div>
        </div>
                     
   
                    
             
            {{-- <div class="row   date_voucher " style="text-align: right">
                <b>Final Balance : </b>
                @php
                $account = \App\Account::where("contact_id",$invoice->contact->id)->first();
                $Atr     = \App\AccountTransaction::where("account_id",$account->id)->select("amount");
                $pr_amount  =  \App\AccountTransaction::whereHas('transaction',function($query) use( $invoice){
                                                $query->where('contact_id',$invoice->contact->id);
                                                $query->whereIn('type',['purchase','purchase_return']);
                                        })->whereHas('account',function($query) use( $invoice){
                                                $query->where('contact_id',$invoice->contact->id);
                                        })->where('type','credit')->where("note","!=","refund Collect")
                                        ->sum("amount");
                $pr_payed   =  \App\AccountTransaction::whereHas('account',function($query) use( $invoice){
                                                    $query->where('contact_id',$invoice->contact->id);
                                                })
                                                ->where('type','debit')
                                                ->whereNull("for_repeat")
                                                ->whereNull("id_delete")
                                                ->sum('amount');
                
                
                $diff       =   $pr_payed - $pr_amount;
                if($diff < 0 ){
                    $price= ($diff*-1)  ;
                    $type= " / Credit"  ;
                }else{
                    $price= ($diff)  ;
                    $type= " / Debit"  ;
                }
                @endphp
                <span >@format_currency($price)  {{$type}}</span>
            </div> --}}
            

       

    <!--  -->
 
        
      
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>