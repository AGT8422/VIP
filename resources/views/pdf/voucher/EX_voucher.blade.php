<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>@lang("home.Gournal Voucher")</title>
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
 
        .bord{
             border: .5px solid #b0906c;
            padding: 10px;
            
         }
        .bords{
            text-align: left
            
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
            font-size: larger;
            padding: 10px;
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
            color:#fff;
            border: 1px solid #b0906c;
        }
        
        td{
            font-size: 10px;
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
        <table   style="width: 100%;margin-bottom:5px;  padding-bottom:25px">
            <tbody>
                <tr >
                    <td   style="border:0px solid grey;  border-radius: 5px 10px 15px 20px;">
                        {{-- <img  src="{{asset("../../../uploads/img/danal.png")}}"     style=" padding:10px;max-width: 300px;height:350px;border-bottom:2px solid #b0906c;border-radius:10px"> --}}
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
            <b> @lang("home.Gournal Voucher")</b>   
        </div>
        <div>&nbsp;</div>

        <div style="position:relative;border:0px solid black;width:100%">
            <b>JV/No. &nbsp; : </b><span > {{$invoice->ref_no}}</span> . {{" / "}} . {{($entry)?$entry->refe_no_e:""}}  <br>
            <b>JV/Date. &nbsp; : </b><span >{{$invoice->date }}</span> <br>       
            {{-- <b>DHS//FC Rate &nbsp; : </b><span > </span>         --}}
         </div>
        <div style="position:relative;border:0px solid black;margin-top:-40px;width:100%">
            <div style="border:0px solid black; width:40%;margin-left:80%;">
                <b>User  &nbsp;: </b><span > {{$user}}</span>  <br>
                <b>Date. &nbsp; : </b><span >{{$invoice->created_at->format("Y-m-d")}}</span> <br>
                <b>Page No. &nbsp; : </b><span >{{1}} . OF . {{$pages}}</span>        
            </div>
        </div>
                  
                    
                
         
       
         
        <div>
            &nbsp;
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
                        $credit_ac     = \App\Account::where("id",$it->credit_account_id)->first();
                        $debit_ac_tax  = \App\Account::where("id",$it->tax_account_id)->first();
                        $debit_ac      = \App\Account::where("id",$it->debit_account_id)->first();
                        $total_debit  += (!empty($debit_ac))?($it->amount-$it->tax_amount):0;
                        $total_debit  += (!empty($debit_ac_tax))?($it->tax_amount):0;
                        $total_credit += (!empty($credit_ac))?($it->amount):0;
                    @endphp
                    @if(!empty($credit_ac))
                        <tr>
                            <td class="border_style">{{$credit_ac->account_number}}</td>
                            <td class="border_style">{{$credit_ac->name}}</td>
                            <td class="border_style">{{$it->text}}</td>
                            <td class="border_style"></td>
                            <td class="border_style">{{number_format($it->amount,2)}}</td>
                        </tr>
                         
                    @endif
                    @if(!empty($debit_ac))
                        @if($it->tax_amount != 0)
                        <tr>
                            <td class="border_style">{{$debit_ac_tax->account_number}}</td>
                            <td class="border_style">{{$debit_ac_tax->name}}</td>
                            <td class="border_style"><pre>{!! $it->text !!}</pre></td>
                            <td class="border_style">{{number_format($it->tax_amount,2)}}</td>
                            <td class="border_style"> </td>
                        </tr>
                        @endif
                    @endif
                    
                    @if(!empty($debit_ac))
                        <tr>
                            <td class="border_style">{{$debit_ac->account_number}}</td>
                            <td class="border_style">{{$debit_ac->name}}</td>
                            <td class="border_style"><pre>{!! $it->text !!}</pre></td>
                            <td class="border_style">{{number_format(($it->amount - $it->tax_amount),2)}}</td>
                            <td class="border_style"> </td>
                        </tr>
                         
                    @endif
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="background-color:rgb(199, 199, 199); border:1px solid rgb(199, 199, 199)"  class="border_style"> </td>
                        <td style="background-color:rgb(199, 199, 199); border:1px solid rgb(199, 199, 199)"  class="border_style"> </td>
                        <td style="background-color:rgb(199, 199, 199); border:1px solid rgb(199, 199, 199);font-weight:bold"  class="border_style"> Total   </td>
                        <td style="background-color:rgb(199, 199, 199); border:1px solid rgb(113, 113, 113);font-weight:bold"  class="border_style"> {{number_format($total_debit,2)}}</td>
                        <td style="background-color:rgb(199, 199, 199); border:1px solid rgb(113, 113, 113);font-weight:bold"  class="border_style"> {{number_format($total_credit,2)}}</td>
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