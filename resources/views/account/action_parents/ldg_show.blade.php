 
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('account.account_book')
    </h1>
</section>
 
<!-- Main content -->
<section class="content">
      <!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">
    <div class="row">
        <div class="col-sm-4 col-xs-6">
            <div class="box box-solid">
                <div class="box-body">
                    <table class="table">
                        
                        <tr>
                            <th>@lang('account.account_name'): </th>
                            <td>{{$account->name}}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang_v1.account_type'):</th>
                            <td>@if(!empty($account->account_type->parent_account)) {{$account->account_type->parent_account->name}} - @endif {{$account->account_type->name ?? ''}}</td>
                        </tr>
                        <tr>
                            <th>@lang('account.account_number'):</th>
                            <td>{{$account->account_number}}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang_v1.balance'):</th>
                            <td><span id="account_balance"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-8  ">
            <div class="box box-solid">
                <div class="box-header text-left">
                    <h3 class="box-title"> <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters'):</h3>
                </div>
                <div class="box-body">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('transaction_type', __('account.transaction_type') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fas fa-exchange-alt"></i></span>
                                {!! Form::select('transaction_type', ['' => __('messages.all'),'debit' => __('account.debit'), 'credit' => __('account.credit')], '', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
        	<div class="box " style="border-top:3px solid #ee6800 ">
                <div class="box-body">
                    @can('ReadOnly.views')
                        <div class="table-responsive">
                    	<table class="table table-bordered table-striped dataTable" id="account_book">
                    		<thead>
                    			<tr>
                                    <th>@lang('home.Ref No')</th>
                                    <th>@lang( 'messages.date' )</th>
                                    <th>@lang( 'lang_v1.description' )</th>
                                    <th>@lang( 'brand.note' )</th>
                                    <th>@lang( 'lang_v1.added_by' )</th>
                                    <th>@lang('account.debit')</th>
                    				<th>@lang('account.credit')</th>
                    				<th>@lang( 'lang_v1.balance' )</th>
                    			</tr>
                    		</thead>
                			 <tfoot>
                                <td colspan="5"></td>
                                <td class="debit_footer"> </td>
                                <td class="credit_footer"> </td>
                                <td class="balance_footer" style="font-weight:bolder"> </td>
                            </tfoot>
                    	</table>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    

    

</section>
<!-- /.content -->

 

 <script>
    $(document).ready(function(){
        update_account_balance();

        dateRangeSettings.startDate = moment().startOf('year');
        dateRangeSettings.endDate = moment();
        $('#transaction_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                
                account_book.ajax.reload();
            }
        );
        
         // Account Book
        account_book = $('#account_book').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: '{{action("AccountController@show",[$account->id])}}',
                                data: function(d) {
                                    var start = '';
                                    var end = '';
                                    
                                    if($('#transaction_date_range').val()){
                                        start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                        end   = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                    }
                                    var transaction_type = $('select#transaction_type').val();
                                    d.start_date = start;
                                    d.end_date = end;
                                    d.type = transaction_type;
                                }
                            },
                            
                            "ordering": false,
                            "searching": false,
                            columns: [
                                {data: 'ref_no', name: 'transaction_id'},
                                {data: 'operation_date', name: 'operation_date'},
                                {data: 'sub_type', name: 'sub_type'},
                                {data: 'note', name: 'note'},
                                {data: 'added_by', name: 'added_by'},
                                {data: 'debit', name: 'amount'},
                                {data: 'credit', name: 'amount_'},
                                {data: 'balance', name: 'balance'}
                            ],
                            fnDrawCallback: function (oSettings) {
                            
                                $('#account_book')  ;
                            },   
                            "footerCallback": function ( row, data ) {
                                var debit = 0;
                                var credit = 0;
                                var balance = 0;
                                var type_ = "";
                                var minus = 0;
                                for (var r in data){
                                    if(data[r].type == "debit"){
                                        debit += parseFloat(data[r].amount);
                                        
                                    }else{
                                        credit += parseFloat(data[r].amount);
                                        
                                    }
                                }   
                                minus =  credit - debit;
                                if(minus<0){
                                    type_ = " / Debit";
                                    minus = minus *-1;
                                }else if(minus == 0){
                                     
                                     type_  = " ";
                
                                }else{
                                    type_ = " / Credit";
                                    
                                }
                                debit_ = " / Debit";
                                credit_ = " / Credit";
                                $('.debit_footer').text(parseFloat(debit).toFixed(2) + debit_ );
                                $('.credit_footer').text(parseFloat(credit).toFixed(2) + credit_);
                                $('.balance_footer').text( parseFloat(minus).toFixed(2) + type_   );
                            },
                        });

                    $('#transaction_type').change( function(){
                        account_book.ajax.reload();
                    });
                    $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
                        $('#transaction_date_range').val('');
                        account_book.ajax.reload();
                    });

                });

    $(document).on('click', '.delete_account_transaction', function(e){
        e.preventDefault();
        swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                $.ajax({
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success === true){
                            toastr.success(result.msg);
                            account_book.ajax.reload();
                            update_account_balance();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });

    function update_account_balance(argument) {
        $('span#account_balance').html('<i class="fas fa-sync fa-spin"></i>');
        $.ajax({
            url: '{{action("AccountController@getAccountBalance", [$account->id])}}',
            dataType: "json",
            success: function(data){
           var dat = 0;
                var crd = " ";
                if(data.balance < 0){
                      dat = data.balance*-1 ;
                      crd = " / Debit";
                }else{
                      dat = data.balance ;
                      crd = " / Credit";
                }
                if(dat == null){
                    var crd = " ";
                }
                $('span#account_balance').text(dat + crd);
            }
        });
    }
</script>
 