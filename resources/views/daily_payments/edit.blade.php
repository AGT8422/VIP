
@extends('layouts.app')
@section('title',$title)

@section('content')
<style>
	.loader-holder{
		height: 300px;
		overflow: hidden;
		padding-top: 30px;
	}
	.loader-holder table{
		opacity: 0;
	}
	.loader-holder.loaded{
		height: auto;
	}
	.loader-holder.loaded table{
		opacity: 1;
	}
	.loader-holder.loaded  .loader{
		display: none
	}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ $title }} </h1>
</section>
@php
	$databaseName = 'izo26102024_esai' ; $dob =  Illuminate\Support\Facades\Config::get('database.connections.mysql.database');
@endphp
<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => 'daily-payment/edit/'.$data->id, 'method' => 'post', 'id' => 'daily_payment_form', 'files' => true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Date').':*') !!}
						{!! Form::text('date',@format_datetime($data->date), ['class' => 'form-control ','readOnly','id'=>'date','required']); !!}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<div class="multi-input">
							{!! Form::label('currency_id', __('business.currency') . ':') !!}  
							<br/>
							{!! Form::select('currency_id',  $currencies, $data->currency_id, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select')]); !!}
							{!! Form::text('currency_id_amount', $data->exchange_price, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
					{!! Form::label('document_expense[]', __('purchase.attach_document') . ':') !!}
					{!! Form::file('document_expense[]', ['multiple','id' => 'upload_document', 'accept' =>
					implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
					<p class="help-block">
						@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
						@includeIf('components.document_help_text')
					</p>
					</div>
				</div>
				{{-- <div class="col-md-4">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Document').':*') !!}
						{!! Form::file('image', ['class' => 'form-control ' ,'accept'=>"image/x-png,image/gif,image/jpeg,application/pdf"]); !!}
					</div>
				</div>
				@if($data->image)
				<div class="col-md-2">
					<a href="{{ URL::to($data->image) }}" target="_blank">
						<i class="fas fa-eye"></i>
						<iframe src="{{ URL::to($data->image) }}" height="150" width="150" frameborder="0"></iframe>
					</a>
					
				</div>
				@endif --}}
				@php
					$hide = ( $data->currency_id != null)?"":"hide";
				@endphp
				
				<div class="col-md-12 loader-holder">
					<div class="loader"></div>
					<table class="table" id="entry_table">
					  <thead>
						<tr>
						  <th class="width_max_th">{{ trans('home.Account') }}</th>
						  @if($databaseName == $dob)
							<th class="width_max_th curr_column {{$hide}}">{{ trans('home.Credit') . " "  }}  <span class="symbol_currency ">{{($data->currency)?"( ".$data->currency->symbol." )":""}}</span>  </th>
						  @endif
						  <th class="width_max_th">{{ trans('home.Credit') }}</th>
						  <th class="width_max_th_btn">&nbsp;</th>
						  @if($databaseName == $dob)
							<th class="width_max_th curr_column {{$hide}}"> {{ trans('home.Debit')  . " " }}  <span class="symbol_currency ">{{($data->currency)? "( ".$data->currency->symbol." )":""}}</span></th> 
						  @endif
						  <th class="width_max_th">{{ trans('home.Debit') }}</th>
						  <th class="cost_width">{{ trans('home.Cost Center') }}</th>
						  <th class="width_max_th">{{ trans('home.Note') }}</th>
						  <th class=""></th>
						</tr>
					  </thead>  
					  <tbody>
						<tr>
							<td class="width_max_th">&nbsp;</td>
							@if($databaseName == $dob)
								<td class="width_max_th curr_column {{$hide}}">&nbsp;</td>
							@endif
							<td class="width_max_th">&nbsp;</td>
							<td class="width_max_th_btn">&nbsp;</td>
							@if($databaseName == $dob)
								<td class="width_max_th curr_column {{$hide}}">&nbsp;</td>
							@endif
							<td class="width_max_th">&nbsp;</td>
							<td class="width_max_th text-center">
								<span class="addBtn pull-right">
								<i class="fa fa-plus"></i>
							    </span>
							</td>
						</tr>
					    @php $counts = count($data->items); @endphp
						@foreach ($data->items as $key=>$item)

							<tr >
								<td class="">
									<input type="hidden" name="old_item[]" value="{{ $item->id }}">
									{{ Form::select('old_account_id[]',$accounts,$item->account_id,['class'=>'form-control select2','placeholder'=>trans('home.please account'),'required']) }}
								</td>
								@if($databaseName == $dob)
									<td class="  curr_column {{$hide}}">
										{{ Form::number('old_credit_curr[]',$item->credit_curr,['class'=>'form-control credit_curr',($item->credit_curr == 0)?"readOnly":"",'required','data_able'=>($key  == ($counts-1))?"##":"0",'style'=>'width:100%','step'=>'any','min'=>0]) }}
											<!--<span class="rows_balances btn btn-primary" @if($item->debit == 0) disabled  data-disabled="true"  @else    data-disabled="false" @endif><i class="fas fa-arrows-alt-h"></i></span>	-->
									</td>
								@endif

								 
								<td class="  crd-amount">
									{{ Form::number('old_credit[]',$item->credit,['class'=>'form-control credit',($item->credit == 0)?"readOnly":"",'required','data_able'=>($key  == ($counts-1))?"##":"0",'style'=>'width:100%','step'=>'any','min'=>0]) }}
									
								</td>
							 
								<td class="">
									<span class="rows_balances btn btn-primary"@if($item->credit == 0)    data-disabled="false"  @else    data-disabled="false" @endif><i class="fas fa-arrows-alt-h"></i></span>	
								</td>
								@if($databaseName == $dob)
									<td class="  curr_column {{$hide}}">
										{{ Form::number('old_debit_curr[]',$item->debit_curr,['class'=>'form-control debit_cur',($item->debit_curr == 0)?"readOnly":"",'required','data_able'=>($key  == ($counts-1))?"#":"0",'style'=>'width:100%','step'=>'any','min'=>0]) }}
											<!--<span class="rows_balances btn btn-primary" @if($item->debit == 0) disabled  data-disabled="true"  @else    data-disabled="false" @endif><i class="fas fa-arrows-alt-h"></i></span>	-->
									</td>
								@endif
								 
								<td class="  deb-amount">
								{{ Form::number('old_debit[]',$item->debit,['class'=>'form-control debit',($item->debit == 0)?"readOnly":"",'required','data_able'=>($key  == ($counts-1))?"#":"0",'style'=>'width:100%','step'=>'any','min'=>0]) }}
									<!--<span class="rows_balances btn btn-primary" @if($item->debit == 0) disabled  data-disabled="true"  @else    data-disabled="false" @endif><i class="fas fa-arrows-alt-h"></i></span>	-->
								</td>
								 
								<td class="">
									{{ Form::select('old_cost_center_id[]',$costs,$item->cost_center_id,['class'=>'form-control select2','placeholder'=>trans('home.please account') ]) }}
								</td>
								<td class="">
									{{ Form::text('old_text[]',$item->text,['class'=>'form-control']) }}
									</td>
								<td class=" text-center">@can("daily_payment.delete_row")<a href="#" onClick="deleteRow(this);"><i class="fas fa-trash" aria-hidden="true"></a>@endcan</td>
							</tr>
						@endforeach
						
						<tr id="addRow">
							<td class=""></td>
							@if($databaseName == $dob)
								<td class=" curr_column {{$hide}}">
									 <label class="label-control">  {{ trans('home.Total Credit')  . " "}}   <span class="symbol_currency "></span>  : <span id="total-credit-currency">0</span> </label>
									<input id="total-credit-input-currency" name="total_credit_currency" type="hidden" value="0">
								</td>
							@endif
							<td class="">
						    	 <label class="label-control">  {{ trans('home.Total Credit') }} : 
								 <span id="total-credit">{{ $amount }}</span> </label>
								 <input id="total-credit-input" name="total_credit" type="hidden" value="{{ $amount }}">
						    </td>
							<td class="width_max_th_btn">
								<p style="position: relative;"><span class="ball_check">&nbsp;&nbsp;</span></p>
							</td>
							@if($databaseName == $dob)
								<td class=" curr_column {{$hide}}">
									<label class="label-control">  {{ trans('home.Total Debit')  . " "}}   <span class="symbol_currency "></span>  : <span id="total-debit-currency">0</span> </label>
									<input id="total-debit-input-currency" name="total_debit_currency" type="hidden" value="0">
								</td>
							@endif
							<td class="">
								<label class="label-control">  {{ trans('home.Total Debit') }} : 
								 <span id="total-debit">{{ $debit }}</span> </label>
								<input id="total-debit-input" name="total_debit" type="hidden" value="{{ $debit }}">
							</td>
							<td class="cost_width">&nbsp;</td>
							<td class="width_max_th">&nbsp;</td>
						</tr>
					  </tbody>
					</table>
 					  <div class="col-md-1 pull-right">
						<button class="btn btn-primary pull-right">{{ trans('messages.update') }}</button>
					  </div>
					 
					</div>
					<input type="text" id="symbol_currencies" hidden value="">
					<input type="text" id="index" hidden value="{{$key+2}}">
					<input type="text" id="index_rows" hidden value="1">
			</div>
		</div>
	</div> <!--box end-->
{!! Form::close() !!}
</section>

@endsection
@section('javascript')
  <script type="text/javascript">
        $('#date').datetimepicker({
            format: moment_date_format + ' ' + moment_time_format,
            ignoreReadonly: true,
        });
		if($('.currency_id').val() != '' && $('.currency_id').val() != null){
			@if($databaseName == $dob)
				$('.debit, .credit,.debit_cur, .credit_curr').on("click" , function(){
					$(this).select();
				});
			@else
				$('.debit, .credit').on("click" , function(){
					$(this).select();
				});
			@endif
		}else{
			$('.debit, .credit').on("click" , function(){
				$(this).select();
			});
		}

    	function addByClick(isn){
			if($('.currency_id').val() != '' && $('.currency_id').val() != null){
				@if($databaseName == $dob)
					set  = $('#entry_table .debit');
					set2 = $('#entry_table .debit_cur');
				@else
					set = $('#entry_table .debit');
				@endif
			}else{
				set = $('#entry_table .debit');
			}
		
            set.each(function(index, element) {
                thisVl  = $(this);
                ro      = thisVl.parent().find(".debit")
                ros     = thisVl.parent().parent().find(".credit")

                if( ro.attr("data_able") == "#"){
                    ro.on("click",function(e){
                        if( $(this).attr("data_able") == "#"){
                            addRow();
                            changeRowsLine();
                            changeRowsLineCredit()
                            e.preventDefault();
                            ros.attr("data_able","0");
                            $(this).attr("data_able","0");
                            $(this).select();
							changeRowsLineHash();

                        }
                    })
                }
            });
			@if($databaseName == $dob)
				if($('.currency_id').val() != '' && $('.currency_id').val() != null){
					set2.each(function(index, element) {
						thisVl   = $(this);
						ro       = thisVl.parent().find(".debit_cur")
						ros      = thisVl.parent().parent().find(".credit_curr")
						
						if( ro.attr("data_able") == "#"){
							ro.on("click",function(e){
								if( $(this).attr("data_able") == "#"){
									addRow();
									changeRowsLine();
									changeRowsLineCredit()
									e.preventDefault();
									ros.attr("data_able","0");
									$(this).attr("data_able","0");
									$(this).select();
									changeRowsLineHash();

								}
							})
						}
					});
				}
			@endif
			
    	     
    	}
    	function addByClickCredit(isn){
            if($('.currency_id').val() != '' && $('.currency_id').val() != null){
				@if($databaseName == $dob)
					set  = $('#entry_table .credit');
					set2 = $('#entry_table .credit_curr');
				@else
					set = $('#entry_table .credit');
				@endif
			}else{
				set = $('#entry_table .credit');
			}

            set.each(function(index, element) {
                thisVl  = $(this);
                ro      = thisVl.parent().find(".credit")
                ros     = thisVl.parent().parent().find(".debit")

                if( ro.attr("data_able") == "##"){
                    ro.on("click",function(e){
                        if( $(this).attr("data_able") == "##"){
                            addRow();
                            changeRowsLine();
                            changeRowsLineCredit()
                            e.preventDefault();
                            ros.attr("data_able","0");
                            $(this).attr("data_able","0");
                            $(this).select();
							changeRowsLineHash();

                        }
                    })
                }
            });
			 
			@if($databaseName == $dob)
				if($('.currency_id').val() != '' && $('.currency_id').val() != null){
					set2.each(function(index, element) {
						thisVl   = $(this);
						ro       = thisVl.parent().find(".credit_curr")
						ros      = thisVl.parent().parent().find(".debit_cur")
						
						if( ro.attr("data_able") == "##"){
							ro.on("click",function(e){
								if( $(this).attr("data_able") == "##"){
									addRow();
									changeRowsLine();
									changeRowsLineCredit()
									e.preventDefault();
									ros.attr("data_able","0");
									$(this).attr("data_able","0");
									$(this).select();
									changeRowsLineHash();

								}
							})
						}
					});
				}
			@endif
    	     
    	}

        function changeRowsLine(){
			if($('.currency_id').val() != '' && $('.currency_id').val() != null){
				@if($databaseName == $dob)
					set  = $('#entry_table .debit');
					set2 = $('#entry_table .debit_cur');
				@else
					set = $('#entry_table .debit');
				@endif
			}else{
				set = $('#entry_table .debit');
			} 
			length = set.length ;
			set.each(function(index, element) {
				thisVl  = $(this);
				if( index >= length-1 ){
					thisVl.on("click",function(e){
						if( $(this).attr("data_able") == "#"){
							$(this).parent().find(".debit").attr("data_able","0");
							$(this).parent().parent().find(".credit").attr("data_able","0");
							@if($databaseName == $dob)
								if($('.currency_id').val() != '' && $('.currency_id').val() != null){
									$(this).parent().parent().find(".credit_curr").attr("data_able","0");
									$(this).parent().parent().find(".debit_cur").attr("data_able","0");
								}
							@endif
							addRow();
							e.preventDefault();
							changeRowsLine();
							changeRowsLineCredit();
							$(this).select();
							changeRowsLineHash();
						} 
					})
				}
			});
		 
              
        }
		function changeRowsLineCredit(){
            if($('.currency_id').val() != '' && $('.currency_id').val() != null){
				@if($databaseName == $dob)  
					set  = $('#entry_table .credit');
					set2 = $('#entry_table .credit_curr');
				@else 
					set = $('#entry_table .credit');
				@endif
			}else{
				set = $('#entry_table .credit');
			}  
			length = set.length ;
			set.each(function(index, element) {
                    thisVl  = $(this);
                     
                    if( index >= length-1 ){
                        thisVl.on("click",function(e){
                            if( $(this).attr("data_able") == "##"){
                                addRow();
                                e.preventDefault();
                                $(this).parent().find(".credit").attr("data_able","0");
                                $(this).parent().parent().find(".debit").attr("data_able","0");
								@if($databaseName == $dob)
									if($('.currency_id').val() != '' && $('.currency_id').val() != null){
										$(this).parent().parent().find(".debit_cur").attr("data_able","0");
										$(this).parent().parent().find(".credit_curr").attr("data_able","0"); 
									}
								@endif
                                changeRowsLine();
                                changeRowsLineCredit();
								$(this).select();
								changeRowsLineHash();

                            } 
                        })
                    }
                });
              
        }

        function changeRowsLineHash(){
			if($('.currency_id').val() != '' && $('.currency_id').val() != null){
				@if($databaseName == $dob) 
					set  = $('#entry_table .debit');
					set2 = $('#entry_table .debit_cur');
					
				@else
					set = $('#entry_table .debit');
				@endif
			}else{

				set = $('#entry_table .debit');
			} 
             length = set.length ;
             set.each(function(index, element) {
				thisVl  = $(this);
				if( index >= length-1 ){
						$(this).parent().find('.debit').attr("data_able","#");
						$(this).parent().parent().find('.credit').attr("data_able","##");
						@if($databaseName == $dob)
							if($('.currency_id').val() != '' && $('.currency_id').val() != null){
								$(this).parent().parent().find(".debit_cur").attr("data_able","#");
								$(this).parent().parent().find(".credit_curr").attr("data_able","##");
							}
						@endif
				}else{
					$(this).parent().find(".debit").attr("data_able","0");
					$(this).parent().parent().find('.credit').attr("data_able","0");
					@if($databaseName == $dob)
						if($('.currency_id').val() != '' && $('.currency_id').val() != null){
							$(this).parent().parent().find(".debit_cur").attr("data_able","0");
							$(this).parent().parent().find(".credit_curr").attr("data_able","0"); 
						}
					@endif
				}
			});

			 
              
        }
        

        addByClick($("#index_rows").val());
        addByClickCredit($("#index_rows").val());
	 
	 
	    $('.currency_id_amount').change(function(){
			update_currency();
			allChanged();
			
		})
		
		$('.currency_id').change(function(){
				var id = $(this).val();
				if(id == ""){
					$(".currency_id_amount").val("");
					$(".curr_column").addClass("hide");
					$("#symbol_currencies").attr('value','');
					$(".symbol_currency").html('');
					$(".width_max_th").css({"width":"calc(100%/6) !important"});
					$(".cost_width").css({"width":"calc(100%/6) !important"});
				}else{
					$.ajax({
							url:"/symbol/amount/"+id,
						dataType: 'html',
						success:function(data){
							var object  = JSON.parse(data);
							$(".currency_id_amount").val(object.amount);
							$(".symbol_currency").html(" ( " + object.symbol + " ) ");
							$(".curr_column").removeClass("hide");
							$(".width_max_th").css({"width":"calc(100%/8) !important"});
							$(".cost_width").css({"width":"80px !important"});
							update_currency();
							allChanged(); 
						},
					});	 
				}
		})
		
		$('.amount_currency').change(function(){
				var id = $(this).val();
				var currency = $('.currency_id_amount').val(); 
				if(currency != ""){
					$('.amount').val((id*currency).toFixed(4));
				}else{
					$('.amount').val((id).toFixed(4));
				}
				 
		})
	
    	function update_currency(){
    		var currency        = $('.currency_id_amount').val(); 
    		var amount          = $(".amount") ;
    		var amount_currency = $(".amount_currency") ;
    		if(currency != "" && currency != 0){
    			amount_currency.val(amount.val()/currency);				
    		}else{
    			amount_currency.val(0);				
    		}
    	}

	    $('.loader-holder').addClass('loaded');
	    
		function formatRows(main, prefer, common) {
			result  = '';hide_class = ($('.currency_id').val() != '' && $('.currency_id').val() != null)?'':'hide';
			result +=  '<tr><td class="">{{ Form::select('account_id[]',$accounts,null,['class'=>'form-control select2 ','placeholder'=>trans('home.please account'),'required']) }}</td>';
			@if($databaseName == $dob) 
				result += '<td class=" curr_column '+hide_class+'"  >{{ Form::number('credit_curr[]',0,['class'=>'form-control credit_curr','required','data_able'=>'##','style'=>'width:100%','step'=>'any','min'=>0]) }}</td>' ;
			@endif ;
			result +=  '<td class=" crd-amount">{{ Form::number('credit[]',0,['class'=>'form-control credit','required','data_able'=>'##','style'=>'width:100%','step'=>'any','min'=>0]) }}</td>';
			result +=  '<td><span class="rows_balances btn btn-primary" data-disabled="false" ><i class="fas fa-arrows-alt-h"></i></span></td>';
			@if($databaseName == $dob) 
				result += '<td class=" curr_column '+hide_class+'"  >{{ Form::number('debit_cur[]',0,['class'=>'form-control debit_cur','required','data_able'=>'#','style'=>'width:100%','step'=>'any','min'=>0]) }}</td>' ;
			@endif ;
			result +=  '<td class=" deb-amount">{{ Form::number('debit[]',0,['class'=>'form-control debit','required','data_able'=>'#','style'=>'width:100%','step'=>'any','min'=>0]) }} </td>';
			result +=  '<td class="">{{ Form::select('cost_center_id[]',$costs,null,['class'=>'form-control select2 ','placeholder'=>trans('home.please account')]) }}</td>';
			result +=  '<td class="">{{ Form::text('text[]',null,['class'=>'form-control ']) }}</td>';
			result +=  '<td class=" text-center"><a href="#" onClick="deleteRow(this)">';
			result +=  '<i class="fas fa-trash" aria-hidden="true"></a></td></tr>';
			return result;
		}

		function deleteRow(trash) {
			$(trash).closest('tr').remove();
				update_deptit();
			    var  debit        =  0;
				var  credit       =  0;
				var  cur_debit    =  0;
				var  cur_credit   =  0;
				var  currency     =  1;
				$('.debit').each(function(){
					if ($(this).val()) {
						@if($databaseName == $dob)
							if($('.currency_id').val() != '' && $('.currency_id').val() != null){
								e_value     = $(this).val();
								c           = $(this).parent().parent().find('.debit_cur');
								currency    = $('.currency_id_amount').val(); 
								in_currency = (currency!=0)?parseFloat(e_value).toFixed(2)/currency:e_value; 
								c.val(in_currency.toFixed(2));
								cur_debit += parseFloat(in_currency);
							}
						@endif
						debit +=  parseFloat($(this).val()) ;
					}
				})
				$('.credit').each(function(){
					if ($(this).val()) {
						@if($databaseName == $dob)
							if($('.currency_id').val() != '' && $('.currency_id').val() != null){
								e_value     = $(this).val();
								c           = $(this).parent().parent().find('.credit_curr');
								currency    = $('.currency_id_amount').val(); 
								in_currency = (currency!=0)?parseFloat(e_value).toFixed(2)/currency:e_value; 
								c.val(in_currency.toFixed(2));
								cur_credit += parseFloat(in_currency);
							}
						@endif
						credit += parseFloat($(this).val());
					}
				})
				$('#total-debit').text(debit.toFixed(2));
				$('#total-credit').text(credit.toFixed(2));

				$('#total-debit-input').val(debit.toFixed(2));
				$('#total-credit-input').val(credit.toFixed(2));

				$('#total-debit-currency').text(cur_debit.toFixed(2));
				$('#total-credit-currency').text(cur_credit.toFixed(2));

				$('#total-debit-input-currency').val(cur_debit.toFixed(2));
				$('#total-credit-input-currency').val(cur_credit.toFixed(2));
				if((debit.toFixed(2) == credit.toFixed(2)) && (debit!=0)){
					$(".ball_check").css({"background-color":"#65e61a","box-shadow":"0px 0px 10px #3a3a3a33"});
				}else{
					$(".ball_check").css({"background-color":"red","box-shadow":"0px 0px 10px #3a3a3a33"});
				}
				terms  =    {
					debit:debit.toFixed(2),
					credit:credit.toFixed(2)
				}
            changeRowsLineHash();
            changeRowsLine();
            changeRowsLineCredit();
		}

		function addRow() {
			var main = $('.addMain').val();
			var preferred = $('.addPrefer').val();
			var common = $('.addCommon').val();
			$(formatRows(main,preferred,common)).insertBefore('#addRow');
			$('.select2').select2();
			 update_deptit();
			 update_change();
			 addByClick($("#index_rows").val());
        	 addByClickCredit($("#index_rows").val());
		}

		$('.addBtn').click(function()  {
 			setTimeout(() => {
				addRow();
                changeRowsLineHash();
                changeRowsLine();
                changeRowsLineCredit();
		}, 1000);
		});
		update_deptit();
		var terms  =    {
			debit:{{ $debit }},
			credit:{{ $amount }}
		};
		function update_deptit() {

			$('.debit, .credit').change(function(){
				var  debit        =  0;
				var  credit       =  0;
				var  cur_debit    =  0;
		        var  cur_credit   =  0;
				var  currency     =  1;
				    $('.debit').each(function(){
						if ($(this).val()) {
							@if($databaseName == $dob)
								if($('.currency_id').val() != '' && $('.currency_id').val() != null){
									e_value     = $(this).val();
									c           = $(this).parent().parent().find('.debit_cur');
									currency    = $('.currency_id_amount').val(); 
									in_currency = (currency!=0)?e_value/currency:e_value; 
									c.val(in_currency.toFixed(2));
									cur_debit += parseFloat(in_currency);
								}
							@endif
							debit +=  parseFloat($(this).val()) ;
						}
					})
					$('.credit').each(function(){
						if ($(this).val()) {
							@if($databaseName == $dob)
								if($('.currency_id').val() != '' && $('.currency_id').val() != null){
									e_value     = $(this).val();
									c           = $(this).parent().parent().find('.credit_curr');
									currency    = $('.currency_id_amount').val(); 
									in_currency = (currency!=0)?e_value/currency:e_value; 
									c.val(in_currency.toFixed(2));
									cur_credit += parseFloat(in_currency);
								}
							@endif
							credit += parseFloat($(this).val());
						}
					})
					$('#total-debit').text(debit.toFixed(2));
					$('#total-credit').text(credit.toFixed(2));

					$('#total-debit-input').val(debit.toFixed(2));
					$('#total-credit-input').val(credit.toFixed(2));

					$('#total-debit-currency').text(cur_debit.toFixed(2));
					$('#total-credit-currency').text(cur_credit.toFixed(2));

					$('#total-debit-currency').text(cur_debit.toFixed(2));
					$('#total-credit-currency').text(cur_credit.toFixed(2));

					if((debit.toFixed(2) == credit.toFixed(2)) && (debit!=0)){
						$(".ball_check").css({"background-color":"#65e61a","box-shadow":"0px 0px 10px #3a3a3a33"});
					}else{
						$(".ball_check").css({"background-color":"red","box-shadow":"0px 0px 10px #3a3a3a33"});
					}

					terms  =    {
    					debit:debit.toFixed(2),
    					credit:credit.toFixed(2)
    				};
			})
			$('.debit_cur, .credit_curr').change(function(){
			    var  debit        =  0;
		        var  credit       =  0;
			    var  cur_debit    =  0;
		        var  cur_credit   =  0;
				var  currency     =  1;
			    $('.debit_cur').each(function(){
					if ($(this).val()) {
						@if($databaseName == $dob)
							if($('.currency_id').val() != '' && $('.currency_id').val() != null){
								e_value     = $(this).val();
								c           = $(this).parent().parent().find('.debit');
								currency    = $('.currency_id_amount').val(); 
								in_currency = (currency!=0)?e_value*currency:e_value; 
								c.val(in_currency.toFixed(2));
								cur_debit +=  parseFloat($(this).val()) ;
								debit +=  parseFloat(in_currency) ;
							}
						@endif
					}
				})
				$('.credit_curr').each(function(){
					if ($(this).val()) {
						@if($databaseName == $dob)
							if($('.currency_id').val() != '' && $('.currency_id').val() != null){
								e_value     = $(this).val();
								c           = $(this).parent().parent().find('.credit');
								currency    = $('.currency_id_amount').val(); 
								in_currency = (currency!=0)?e_value*currency:e_value; 
								c.val(in_currency.toFixed(2));
								cur_credit +=  parseFloat($(this).val()) ;
								credit +=  parseFloat(in_currency) ;
							}
						@endif
					}
				})
				$('#total-debit-currency').text(cur_debit.toFixed(2));
				$('#total-credit-currency').text(cur_credit.toFixed(2));

				$('#total-debit-currency').text(cur_debit.toFixed(2));
				$('#total-credit-currency').text(cur_credit.toFixed(2));

				$('#total-debit').text(debit.toFixed(2));
				$('#total-credit').text(credit.toFixed(2));

				$('#total-debit-input').val(debit.toFixed(2));
				$('#total-credit-input').val(credit.toFixed(2));

				if((debit.toFixed(2) == credit.toFixed(2)) && (debit!=0)){
					$(".ball_check").css({"background-color":"#65e61a","box-shadow":"0px 0px 10px #3a3a3a33"});
				}else{
					$(".ball_check").css({"background-color":"red","box-shadow":"0px 0px 10px #3a3a3a33"});
				}
				 
	    	})
		}		

		allChanged();
		update_total();
		update_change();

    	function allChanged() {
    		$('.debit, .credit').each(function(){
    			   var  debit      =  0;
    		       var  credit     =  0;
				   var  cur_debit  =  0;
				   var  cur_credit =  0;
				   var  currency   =  1;
    			   $('.debit').each(function(){
    					if ($(this).val()) {
							@if($databaseName == $dob)
								if($('.currency_id').val() != '' && $('.currency_id').val() != null){
									e_value     = $(this).val();
									c           = $(this).parent().parent().find('.debit_cur');
									currency    = $('.currency_id_amount').val(); 
									in_currency = (currency!=0)?parseFloat(e_value).toFixed(2)/currency:e_value; 
									c.val(in_currency.toFixed(2));
									cur_debit += parseFloat(in_currency);
								}
							@endif
    						debit +=  parseFloat($(this).val()) ;
    					}
    				})
    				$('.credit').each(function(){
    					if ($(this).val()) {
							@if($databaseName == $dob)
								if($('.currency_id').val() != '' && $('.currency_id').val() != null){
									e_value     = $(this).val();
									c           = $(this).parent().parent().find('.credit_curr');
									currency    = $('.currency_id_amount').val(); 
									in_currency = (currency!=0)?parseFloat(e_value).toFixed(2)/currency:e_value; 
									c.val(in_currency.toFixed(2));
									cur_credit += parseFloat(in_currency);
								}
							@endif
    						credit += parseFloat($(this).val());
    					}
    				})
    				$('#total-debit').text(debit.toFixed(2));
    				$('#total-credit').text(credit.toFixed(2));
    
    				$('#total-debit-input').val(debit.toFixed(2));
    				$('#total-credit-input').val(credit.toFixed(2));
						
					$('#total-debit-currency').text(cur_debit.toFixed(2));
					$('#total-credit-currency').text(cur_credit.toFixed(2));

					$('#total-debit-input-currency').val(cur_debit.toFixed(2));
					$('#total-credit-input-currency').val(cur_credit.toFixed(2));

					if((debit.toFixed(2) == credit.toFixed(2)) && (debit!=0)){
						$(".ball_check").css({"background-color":"#65e61a","box-shadow":"0px 0px 10px #3a3a3a33"});
					}else{
						$(".ball_check").css({"background-color":"red","box-shadow":"0px 0px 10px #3a3a3a33"});
					}

    				terms  =    {
    					debit:debit.toFixed(2),
    					credit:credit.toFixed(2)
    				};
    	    })
    	}

		function update_total(){
			$('.debit, .credit').change(function(){
				var  debit      =  0;
		       	var  credit     =  0;
				var  cur_debit  =  0;
				var  cur_credit =  0;
				var  currency   =  1;
			   	$('.debit').each(function(){
				    if ($(this).val()) {
						@if($databaseName == $dob)
							if($('.currency_id').val() != '' && $('.currency_id').val() != null){
								e_value     = $(this).val();
								c           = $(this).parent().parent().find('.debit_cur');
								currency    = $('.currency_id_amount').val(); 
								in_currency = (currency!=0)?parseFloat(e_value).toFixed(2)/currency:e_value; 
								c.val(in_currency.toFixed(2));
								cur_debit += parseFloat(in_currency);
							}
						@endif
    					debit +=  parseFloat($(this).val()) ;
    				}
    			})
    			$('.credit').each(function(){
    				if ($(this).val()) {
						@if($databaseName == $dob)
							if($('.currency_id').val() != '' && $('.currency_id').val() != null){
								e_value     = $(this).val();
								c           = $(this).parent().parent().find('.credit_curr');
								currency    = $('.currency_id_amount').val(); 
								in_currency = (currency!=0)?parseFloat(e_value).toFixed(2)/currency:e_value; 
								c.val(in_currency.toFixed(2));
								cur_credit += parseFloat(in_currency);
							}
						@endif
    					credit += parseFloat($(this).val());
    				}
    			})
    			$('#total-debit').text(debit.toFixed(2));
    			$('#total-credit').text(credit.toFixed(2));
    			
    			$('#total-debit-input').val(debit.toFixed(2));
			    $('#total-credit-input').val(credit.toFixed(2));
	
				$('#total-debit-currency').text(cur_debit.toFixed(2));
				$('#total-credit-currency').text(cur_credit.toFixed(2));

				$('#total-debit-input-currency').val(cur_debit.toFixed(2));
				$('#total-credit-input-currency').val(cur_credit.toFixed(2));

				if((debit.toFixed(2) == credit.toFixed(2)) && (debit!=0)){
					$(".ball_check").css({"background-color":"#65e61a","box-shadow":"0px 0px 10px #3a3a3a33"});
				}else{
					$(".ball_check").css({"background-color":"red","box-shadow":"0px 0px 10px #3a3a3a33"});
				}
				terms  =    {
					debit:debit.toFixed(2),
					credit:credit.toFixed(2)
				};
			})
		}
		
		$('#daily_payment_form').submit(function(e){
			if (terms.debit != terms.credit  ||  terms.debit == 0) {
				e.preventDefault();
				alert("{{ trans('home.Total Debit  and total credit must be equaled') }}");
			}
		})
		
	    function update_change(){
			$('#entry_table .credit,#entry_table .credit_curr').each(function(){
				var e  = $(this);
				var el = $(this).on("change",function(){
					value  = $(this).val();
					if($(this).val() == 0 || $(this).val() == ""){
						el.parent().parent().find('.debit').attr("readOnly",false) ;
						el.parent().parent().find('.debit_cur').attr("readOnly",false) ;
						el.parent().parent().find('.rows_balances').attr("data-disabled","false") ;
						
					}else{
						e.val( parseFloat(value).toFixed(2) ) ;
						el.parent().parent().find('.debit').val(0) ;
						el.parent().parent().find('.debit').attr("readOnly",true) ;
						el.parent().parent().find('.debit_cur').attr("readOnly",true) ;
						
				
					}
				})
			
			})

			$('#entry_table .debit,#entry_table .debit_cur').each(function(){
				var e  = $(this);
				var el = $(this).on("change",function(){
				value  = $(this).val();
					if($(this).val() == 0 || $(this).val() == ""){
						el.parent().parent().find('.credit').attr("readOnly",false) ;
						el.parent().parent().find('.credit_curr').attr("readOnly",false) ;
					
						
					}else{
						e.val( parseFloat(value).toFixed(2) ) ;
						el.parent().parent().find('.credit').val(0) ;
						el.parent().parent().find('.credit').attr("readOnly",true) ;
						el.parent().parent().find('.credit_curr').attr("readOnly",true) ;
					
					}
				})
				
			})
			
			$('#entry_table   .rows_balances').each(function(){
				var e  = $(this); 
				var el = $(this).on("click",function(){ 
					if(e.attr("data-disabled") == "false" ){
						if(e.parent().parent().find('.credit').val() == 0 || e.parent().parent().find('.credit').val() == ""){
							old_val_credit = 0; 
						}else{
							old_val_credit = e.parent().parent().find('.credit').val(); 
						}
						if(e.parent().parent().find('.debit').val() == 0 || e.parent().parent().find('.debit').val() == ""){
							old_val_debit = 0; 
						}else{
							old_val_debit = e.parent().parent().find('.debit').val(); 
						}
						total_balance = $('#total-debit-input').val() - $('#total-credit-input').val();
						
						if(total_balance < 0){
							if(Math.abs(total_balance) == e.parent().parent().find('.credit').val()){
								e.parent().parent().find('.debit').attr("readOnly",false) ;
								e.parent().parent().find('.debit_cur').attr("readOnly",false) ;
								e.parent().parent().find('.debit').val(0) ;
								e.parent().parent().find('.credit').val(0) ;
								e.parent().parent().find('.credit').attr("readOnly",false) ;
								e.parent().parent().find('.credit_curr').attr("readOnly",false) ;
								allChanged();
							}else{
								if(Math.abs(parseFloat(old_val_credit)) > Math.abs(parseFloat( total_balance))){
									e.parent().parent().find('.credit_curr').attr("readOnly",false) ;
									e.parent().parent().find('.credit').attr("readOnly",false) ;
									e.parent().parent().find('.credit').val( (Math.abs(parseFloat(old_val_credit) - Math.abs(parseFloat( total_balance)))).toFixed(2) ) ;
									e.parent().parent().find('.debit').val(0) ;
									e.parent().parent().find('.debit').attr("readOnly",true) ;
									e.parent().parent().find('.debit_cur').attr("readOnly",true) ;
								}else{
									e.parent().parent().find('.debit_cur').attr("readOnly",false) ;
									e.parent().parent().find('.debit').attr("readOnly",false) ;
									e.parent().parent().find('.debit').val( (Math.abs(parseFloat(old_val_debit) + Math.abs(parseFloat( total_balance) ) - Math.abs(parseFloat(old_val_credit)))).toFixed(2) ) ;
									e.parent().parent().find('.credit').val(0) ;
									e.parent().parent().find('.credit').attr("readOnly",true) ;
									e.parent().parent().find('.credit_curr').attr("readOnly",true) ;
								}
								allChanged();
								
							}
						}else if(total_balance > 0){
							if(Math.abs(total_balance) ==  parseFloat(e.parent().parent().find('.debit').val())){
								e.parent().parent().find('.credit').attr("readOnly",false) ;
								e.parent().parent().find('.credit_curr').attr("readOnly",false) ;
								e.parent().parent().find('.credit').val(  0  ) ;
								e.parent().parent().find('.debit').val(0) ;
								e.parent().parent().find('.debit_cur').attr("readOnly",false) ;
								e.parent().parent().find('.debit').attr("readOnly",false) ;
								allChanged();
							}else{
								if(Math.abs(parseFloat(old_val_debit)) > Math.abs(parseFloat( total_balance))){
									e.parent().parent().find('.debit').attr("readOnly",false) ;
									e.parent().parent().find('.debit_cur').attr("readOnly",false) ;
									e.parent().parent().find('.debit').val( (Math.abs(parseFloat(old_val_debit) - parseFloat(total_balance))).toFixed(2) ) ;
									e.parent().parent().find('.credit').val(0) ;
									e.parent().parent().find('.credit').attr("readOnly",true) ;
									e.parent().parent().find('.credit_curr').attr("readOnly",true) ;
								}else{
									e.parent().parent().find('.credit_curr').attr("readOnly",false) ;
									e.parent().parent().find('.credit').attr("readOnly",false) ;
									e.parent().parent().find('.credit').val( (Math.abs(parseFloat(total_balance) + parseFloat(old_val_credit)) - parseFloat(old_val_debit)).toFixed(2) ) ;
									e.parent().parent().find('.debit').val(0) ;
									e.parent().parent().find('.debit').attr("readOnly",true) ;   
									e.parent().parent().find('.debit_cur').attr("readOnly",true) ;   
									
								}
								allChanged();
								
							}
						}else{
							allChanged();
						}
						
					
					} 
				})
				
			})
		}
		window.onbeforeunload = function() {
			return LANG.sure;
		}
	 
  </script>
@endsection