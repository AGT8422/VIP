@extends("layouts.app")

@section("title",__("home.main bill"))

@section("content")

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('home.main bill') </h1>
    {{-- <strong> :::  {{ $user->name }} :::   </strong> --}}
</section>

<section class="content">
    <div class="table" style="border:1px solid rgba(0, 0, 0, 0.577)" >
            <table style="width:100%">
                <thead>
                <tr>
                
                <td style="padding:5px">@lang("lang_v1.id")</td> 
                <td style="padding:5px">@lang("lang_v1.user")</td> 
                <td style="padding:5px">@lang("lang_v1.type")</td> 
                <td style="padding:5px">@lang("purchase.sup_refe")</td> 
                <td style="padding:5px">@lang("home.state")</td> 
                <td style="padding:5px">@lang("lang_v1.grand_total")</td> 
                <td style="padding:5px">@lang("lang_v1.date")</td> 
                <td style="padding:5px">@lang("lang_v1.action_date")</td> 
                </tr>
            </thead>
            <tbody>
                <tr style=" font-weight:600;color:black">
                    <td style="padding:5px">{{$main->id}}</td>
                    <td style="padding:5px">{{$users[$main->created_by]}}</td>
                    <td style="padding:5px">{{$main->type}}</td>
                    <td style="padding:5px">{{($main->type == "sale")?$main->invoice_no:$main->ref_no}}</td>
                    <td style="padding:5px">@lang("home.last Update")</td>
                    <td style="padding:5px">{{$main->final_total}}</td>
                    <td style="padding:5px">{{$main->transaction_date}}</td>
                    <td style="padding:5px">{{$main->created_at}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <table style="width:100% ;border:1px solid rgba(0, 0, 0, 0.577)">
        <thead>
            <tr >
                <td style="padding:5px ;">#</td>
                <td style="padding:5px">@lang("lang_v1.id")</td> 
                <td style="padding:5px">@lang("lang_v1.user")</td> 
                <td style="padding:5px">@lang("lang_v1.type")</td> 
                <td style="padding:5px">@lang("purchase.sup_refe")</td> 
                <td style="padding:5px">@lang("home.state")</td> 
                <td style="padding:5px">@lang("lang_v1.grand_total")</td> 
                <td style="padding:5px">@lang("lang_v1.date")</td> 
                <td style="padding:5px">@lang("lang_v1.action_date")</td> 
                <td style="padding:5px">@lang("purchase.ref_no")</td> 
                <td style="padding:5px">@lang("home.main bill")</td> 
                <td style="padding:5px">@lang("home.parent")</td>  
            </tr>
        </thead>
        <tbody>
            @foreach($child as $it)
                <tr style="padding:5px ;color:rgb(0, 0, 0)">
                    <td style="padding:5px">@if($it->id == $bill_id) <i style="color:#ee680e" class="fa fas fa-star"></i> @endif</td>
                    <td style="padding:5px">{{$it->id}}</td>
                    <td style="padding:5px; font-weight:600;color:#ee680e ">{{$users[$it->created_by]}}</td>
                    <td style="padding:5px">{{$it->type}}</td>
                    <td style="padding:5px">{{($it->type == "sale")?$it->invoice_no:$it->ref_no}}</td>
                    <td style="padding:5px">{{($it->state_action!=null)?$it->state_action:__("home.last Update")}}</td>
                    <td style="padding:5px; font-weight:600;color:#ee680e ">{{$it->final_total}}</td>
                    <td style="padding:5px">{{$it->transaction_date}}</td>
                    <td style="padding:5px">{{$it->created_at}}</td>
                    <td style="padding:5px">{{$it->ref_number}}</td>
                    <td style="padding:5px">{{$it->new_id}}</td>
                    <td style="padding:5px; color:#ee680e ;font-weight:600;" >{{$it->parent_id}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    

</section>

<h3>&nbsp;&nbsp;</h3>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('home.Payment movement') </h1>
    {{-- <strong> :::  {{ $user->name }} :::   </strong> --}}
</section>


<section class="content">
    <div class="table" style="border:1px solid rgba(0, 0, 0, 0.577)" >
            <table style="width:100%">
                <thead>
                <tr>
                <td style="padding:5px">@lang("lang_v1.id")</td> 
                <td style="padding:5px">@lang("lang_v1.user")</td> 
                <td style="padding:5px">@lang("lang_v1.type")</td> 
                <td style="padding:5px">@lang("purchase.sup_refe")</td> 
                <td style="padding:5px">@lang("home.state")</td> 
                <td style="padding:5px">@lang("lang_v1.grand_total")</td> 
                <td style="padding:5px">@lang("lang_v1.date")</td> 
                <td style="padding:5px">@lang("lang_v1.action_date")</td>
                <td style="padding:5px">@lang("purchase.ref_no")</td> 
                <td style="padding:5px">@lang("home.main bill")</td>  
                <td style="padding:5px">@lang("home.parent")</td>  
                </tr>
            </thead>
            <tbody>
                @foreach($payment_main as $it)
                    <tr style="font-weight:600;color:rgb(0, 0, 0)">
                        <td style="padding:5px">{{$it->id}}</td>
                        <td style="padding:5px">{{$users[$it->created_by]}}</td>
                        <td style="padding:5px">{{$it->method}}</td>
                        <td style="padding:5px">{{$it->payment_ref_no}}</td>
                        <td style="padding:5px">{{__("home.last Update")}}</td>
                        <td style="padding:5px">{{$it->amount}}</td>
                        <td style="padding:5px">{{$main->paid_on}}</td>
                        <td style="padding:5px">{{$main->created_at}}</td>
                        <td style="padding:5px"> </td>
                        <td style="padding:5px"> </td>
                        <td style="padding:5px"> </td>
                    </tr>
                    @foreach($payment_child as $i)
                        @if($i->line_id == $it->id)
                            <tr style="border:1px solid #ee680e;color:#ee680e">
                                <td style="padding:5px">{{$i->id}}</td>
                                <td style="padding:5px">{{$users[$i->created_by]}}</td>
                                <td style="padding:5px">{{$i->method}}</td>
                                <td style="padding:5px">{{$i->payment_ref_no}}</td>
                                <td style="padding:5px">{{($i->state_action!=null)?$i->state_action:__("home.last Update")}}</td>
                                <td style="padding:5px;font-weight:600;"  >{{$i->amount}}</td>
                                <td style="padding:5px">{{$i->paid_on}}</td>
                                <td style="padding:5px">{{$i->created_at}}</td>
                                <td style="padding:5px">{{$i->ref_number}}</td>
                                <td style="padding:5px">{{$i->line_id}}</td>
                                <td style="padding:5px">{{$i->log_parent_id}}</td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
     
    

</section>


@endsection

@section("javascript")

@endsection