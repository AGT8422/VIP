<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>@lang("home.Cheque_Out")</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

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
             border: .5px solid #8e0f82;
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
        .final{
            border: 0px solid black;
            text-align: right;
            border-radius: 10px;
            font-size: small;
            margin-bottom: -10px;
            margin-top: 20px;
        }
        b{
            font-weight:bold;
        }
    </style>

</head>

<body>

    <div class="bill"  >
        <table style="width: 100%;margin-bottom:5px;  padding-bottom:25px">
            <tbody>
                <tr >
                    <td class=" " style="border:0px solid grey;  border-radius: 5px 10px 15px 20px;">
                        <img src="{{asset("../../../uploads/img/danal.png")}}"   style=" padding:10px;max-width: 300px;height:350px;border-bottom:2px solid #8e0f82;border-radius:10px">
                    </td>
                    <td style="text-align: right; width:400px;padding:10px;
                            line-height:13px;font-size:12px;border-right:1px solid grey;
                        "> @if($layout) {!! $layout->header_text !!} @endif</td>
                </tr>
            </tbody>
        </table>
     
         
        <div class="title_voucher">
            <b> @lang("home.Cheque_Out")</b><br>
            <span class="references"> ( {{$invoice->ref_no}} . {{"/"}} .  {{($entry)?$entry->refe_no_e:""}} ) </span>
        </div>
        <div class="date_voucher">
            <b> @lang("lang_v1.date") : </b>{{$invoice->created_at->format("Y-m-d")}} 
        </div>
     
        @php $payment = \App\TransactionPayment::where("check_id",$invoice->id)->first(); @endphp
        @php  if(!empty($payment))  { $trans  = \App\Transaction::where("id",$payment->transaction_id)->first(); }   @endphp
            <div style="font-size: 12px;width:70%">
                 <b>- {{($invoice->account_type != 0)? "Account":"Supplier"}} Name :</b> <span>{{($invoice->account_type == 0)?$invoice->contact->name:$invoice->contacts->name}}</span> <br>
                 <b>- Payment Type :</b> <span>{{__("home.Cheque")}}</span> <br>
                 <b>- Amount :</b> <span>@format_currency($invoice->amount)</span> <br>
                <b>- Description :</b> <span>{{$invoice->note}}</span> <br>
                @if(!empty($payment))
                    <b>- For Invoice :</b> <span>{{(!empty($payment))?$trans->ref_no:"0"}}</span> <br>
                    <b>- invoice balance :</b> <span>@format_currency($trans->final_total - $payment->amount )</span> <br>
                @endif
            </div>
            <div style="font-size: 12px; ">
                <div style="width:40%;margin-left:70%;text-align:left;margin-top:-70px;">
                     <b>- Cheque Number :</b> <span>{{$invoice->cheque_no}}</span> <br>
                     @php
                            if( $invoice->status == 0){
                                $status ="Write";
                            }elseif( $invoice->status == 1){
                                $status ="Collected";
                            }elseif( $invoice->status == 2){
                                $status ="Refund";
                            }elseif( $invoice->status == 4){
                                $status = "Un Collected";
                            }elseif( $invoice->status == 3){
                                $status = "Delete Collected";
                            }else{
                                $status = " ";
                                
                            }
                     @endphp
                     <b>- Cheque Status :</b> <span>{{$status}}</span> <br>
                     <b>- Write Date :</b> <span>{{$invoice->write_date }}</span> <br>
                     <b>- Due Date :</b> <span>{{$invoice->due_date }}</span> <br>
                     @if($invoice->bank) 
                        <b>- Bank :</b> <span>{{$invoice->bank->name}}</span> <br>
                    @endif
                    @if(!empty($payment))
                         <b>- For Invoice :</b> <span>{{(!empty($payment))?$trans->ref_no:"0"}}</span> <br>
                         <b>- invoice balance :</b> <span>@format_currency($trans->final_total - $payment->amount )</span> <br>
                    @endif
                </div>
            </div>
                     
   
                    
             

            <div class="row    final " style="text-align: right">
                <b>Final Balance : </b>
                @php
                if($invoice->account_type == 0){
                    $account = \App\Account::where("contact_id",$invoice->contact->id)->first();
                }else{
                    $account = \App\Account::where("id",$invoice->contact_id)->first();
                }
                     $Atr     = \App\AccountTransaction::where("account_id",$account->id)->select("amount");
                if($invoice->account_type == 0){
                     $pr_amount  =  \App\AccountTransaction::whereHas('transaction',function($query) use( $invoice){
                                                $query->where('contact_id',$invoice->contact->id);
                                                $query->whereIn('type',['purchase','purchase_return']);
                                        })->whereHas('account',function($query) use( $invoice){
                                                $query->where('contact_id',$invoice->contact->id);
                                        })
                                        ->whereNull("for_repeat")
                                        ->whereNull("id_delete")
                                        ->where('type','credit')->where("note","!=","refund Collect")
                                        ->sum("amount");
                     $pr_payed   =  \App\AccountTransaction::whereHas('account',function($query) use( $invoice){
                                                    $query->where('contact_id',$invoice->contact->id);
                                                })
                                                ->where('type','debit')
                                                ->whereNull("for_repeat")
                                                ->whereNull("id_delete")
                                                ->sum('amount');
                }else{
                    $pr_amount  =  \App\AccountTransaction::whereHas('account',function($query) use( $invoice){
                                                $query->where('id',$invoice->contact_id);
                                        })
                                        ->whereNull("for_repeat")
                                        ->whereNull("id_delete")
                                        ->where('type','credit')->where("note","!=","refund Collect")
                                        ->sum("amount");
                     $pr_payed   =  \App\AccountTransaction::whereHas('account',function($query) use( $invoice){
                                                    $query->where('id',$invoice->contact_id);
                                                })
                                                ->where('type','debit')
                                                ->whereNull("for_repeat")
                                                ->whereNull("id_delete")
                                                ->sum('amount');
                }                      
                
                
                
                $diff       =   $pr_payed - $pr_amount;
                if($diff < 0 ){
                    $price= ($diff*-1)  ;
                    $type= " / Credit"  ;
                }else{
                    $price= ($diff)  ;
                    $type= " / Debit"  ;
                }
                @endphp
                @php 
                    $business_id = session()->get('user.business_id');
                    $account_BALANCE = \App\Account::leftjoin(
                                    'account_transactions as AT',
                                    'AT.account_id',
                                    '=',
                                    'accounts.id'
                                )
                            ->whereNull('AT.deleted_at')
                            ->where('accounts.business_id', $business_id)
                            ->where('accounts.id', $account->id)
                            ->where('AT.for_repeat',"=", null)
                            ->select('accounts.*', DB::raw("SUM( IF(AT.type='credit', amount, -1 * amount) ) as balance"))
                            ->first();
                    $BALC = ($account_BALANCE)?$account_BALANCE->balance : 0;
                    if($BALC < 0 ){
                        $price= ($BALC*-1)  ;
                        $type= " / Debit"  ;
                    }else{
                        $price= ($BALC)  ;
                        $type= " / Credit"  ;
                    }
                @endphp
                
                <span >@format_currency($price)  {{$type}}</span>
            </div>
            
            <div style="border-bottom:2px solid #82c341">&nbsp;</div>
            <div>&nbsp;</div>
        
            <div style="font-size: 12px; text-align:left">
                <b>  Prepared By :</b>   <br>
            </div>
            <div style="font-size: 12px; margin-top:-15px;">
                <div style="width:50%;margin-left:70%;text-align:left">
                    <b>  Approved By :</b>   <br>
                </div>
            </div>

       

    <!--  -->
 
        
      
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>