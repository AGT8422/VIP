@extends("layouts.app")

@section("title",__("home.bill_active"))

@section("content")

<!-- Content Header (Page header) -->
<section class="content-header font_text">
    <h1 class="font_text">@lang('home.bill_active') </h1>
    {{-- <strong> :::  {{ $user->name }} :::   </strong> --}}
</section>

<section class="content">
  
    @component('components.filters', ['title' => __('report.filters') , 'class' => 'box-primary'])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('status',  __('lang_v1.type') . ':') !!}
                {!! Form::select('status',$status_filter , null, ['id'=>'status','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        {{-- <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('user_id',  __('purchase.supplier') . ':') !!}
                {!! Form::select('user_id', $users, null, ['id'=>'user_id','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div> --}}
    @endcomponent

    @component("components.widget",["title"=>__("home.bill_active") , 'class' => 'box-primary'])
        <div class="row">
            <div class="col-xs-12">
                <div class="table">
                    <table class="table table-stripted table-bordered" id="bill_log">
                            <thead>
                                <tr>
                                <th>@lang("messages.action")</th>
                                <th>@lang("lang_v1.user")</th> 
                                <th>@lang("sale.first_no")</th>
                                <th>@lang("warehouse.nameW")</th>
                                <td>@lang("lang_v1.type")</th>
                                <th>@lang('sale.project_no')</th>
                                <th>@lang("purchase.purchase_status")</th>
                                <th>@lang("purchase.payment_status")</th>
                                <th>@lang('home.contact')</th>
                                <th>@lang("purchase.ref_no")</th>
                                <th>@lang("lang_v1.date")</th>
                                <th>@lang('home.sub_total')</th>
                                <th>@lang('purchase.grand_total')</th>
                                <th>@lang('home.Agent')</th>
                                <th>@lang('purchase.sup_refe')</th>
                                <th>@lang('home.Cost Center')</th>
                                <th>@lang('home.pattern')</th> 
                                <th>@lang('purchase.ref_no')</th> 
                                <th>@lang("lang_v1.created_at")</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="10"></td>
                                    <td colspan="7"></td>
                                </tr>
                            </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endcomponent
        
       

</section>


@stop

@section('javascript')
    <script type="text/javascript">
            
             
            log_users = $("#bill_log").DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX:        true,
                scrollCollapse: true,
                ajax: {
                    url: '/bill-log-file/',
                    data: function(d) {
                        d.status = $("#status").val();
                        // d.state   = $("#state").val(); 
                        d = __datatable_ajax_callback(d);
                    },
                },
                aaSorting: [[1, 'desc']],
                columns: [
                    { data: 'action', name: 'action' },
                    { data: 'user', name: 'user' },
                    { data: 'last_transaction', name: 'last_transaction' },
                    { data: 'store', name: 'store' },
                    { data: 'type' , name: 'type' },
                    { data: 'project_no' , name: 'project_no' },
                    { data: 'status' , name: 'status' },
                    { data: 'payment_status' , name: 'payment_status' },
                    { data: 'contact_id' , name: 'contact_id' },
                    { data: 'invoice_no' , name: 'invoice_no' },
                    { data: 'transaction_date' , name: 'transaction_date' },
                    { data: 'total_before_tax' , name: 'total_before_tax' },
                    { data: 'final_total' , name: 'final_total' },
                    { data: 'agent_id' , name: 'agent_id' },
                    { data: 'sup_refe' , name: 'sup_refe' },
                    { data: 'cost_center_id', name: 'cost_center_id' },
                    { data: 'pattern_id', name: 'pattern_id' },
                    { data: 'ref_number', name: 'ref_number' },
                    { data: 'date', name: 'date' },
                    
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#bill_log'));
                },
                "footerCallback": function ( row, data, start, end, display ) {
                    // var total_purchase = 0;
                    // var total_due = 0;
                   
                    // for (var r in data){
                         
                    //     total_due += payment_due_obj.find('.payment_due').data('orig-value') ? 
                    //     parseFloat(payment_due_obj.find('.payment_due').data('orig-value')) : 0;
 
                    // }

                    // $('.footer_purchase_total').html(__currency_trans_from_en(total_purchase));
                    // $('.footer_total_due').html(__currency_trans_from_en(total_due));
                  
                },
                 
            });
            // $("#user_id").on("change",function(){
            //     log_users.ajax.reload();
            // });
            $("#status").on("change",function(){
                log_users.ajax.reload();
            
            }); 
            $("#bill_log").css({"width":"100%"});
    </script>
@endsection