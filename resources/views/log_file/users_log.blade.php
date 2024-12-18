@extends("layouts.app")

@section("title",__("home.users_active"))

@section("content")

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('home.users_active') </h1>
    {{-- <strong> :::  {{ $user->name }} :::   </strong> --}}
</section>


<section class="content">
  
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('state',  __('home.state') . ':') !!}
                {!! Form::select('state', ["login"=>__("login"),"logout"=>__("logout")], null, ['id'=>'state','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('user_id',  __('lang_v1.user') . ':') !!}
                {!! Form::select('user_id', $users, null, ['id'=>'user_id','class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    @endcomponent

    
    
    @component("components.widget",["title"=>__("home.users_active")])
        <div class="row">
            <div class="col-xs-12">
                <div class="table">
                    <table class="table table-stripted table-bordered" id="user_log">
                            <thead>
                                <tr>
                                <td>@lang("lang_v1.id")</td>
                                <td>@lang("home.name")</td>
                                <td>@lang("home.state")</td>
                                <td>@lang("purchase.ref_no")</td>
                                <td>@lang("lang_v1.date")</tr>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"></td>
                                    <td colspan="2"></td>
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
            
             
            log_users = $("#user_log").DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX:        true,
                scrollCollapse: true,
                ajax: {
                    url: '/user-log-file/',
                    data: function(d) {
                        d.user_id = $("#user_id").val();
                        d.state   = $("#state").val(); 
                        d = __datatable_ajax_callback(d);
                    },
                },
                aaSorting: [[1, 'desc']],
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'state', name: 'state' },
                    { data: 'ref_no', name: 'ref_no' },
                    { data: 'date', name: 'date' },
                    
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#user_log'));
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
            $("#user_id").on("change",function(){
                log_users.ajax.reload();
            });
            $("#state").on("change",function(){
                log_users.ajax.reload();
            
            }); 
            $("#user_log").css({"width":"100%"});
    </script>
@endsection