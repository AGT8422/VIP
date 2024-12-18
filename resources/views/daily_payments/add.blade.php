@extends('layouts.app')
@section('title',$title)

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ $title }} </h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => 'daily-payment/add', 'method' => 'post', 'id' => 'daily_payment_form', 'files' => true ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Date').':*') !!}
						{!! Form::text('date',@format_datetime('now'), ['class' => 'form-control ', 'readonly','id'=>'date' ,'required']); !!}
					</div>
				</div>
				{{-- <div class="col-md-6">
					<div class="form-group">
						{!! Form::label('location_id', __('home.Document').':') !!}
						{!! Form::file('image', ['class' => 'form-control ' ,'accept'=>"image/x-png,image/gif,image/jpeg,application/pdf"]); !!}
					</div>
				</div> --}}
				<div class="col-md-4">
					<div class="form-group">
						<div class="multi-input">
							{!! Form::label('currency_id', __('business.currency') . ':') !!}  
							<br/>
							{!! Form::select('currency_id',  $currencies, null, ['class' => 'form-control width-60 currency_id  select2', 'placeholder' => __('messages.please_select')]); !!}
							{!! Form::text('currency_id_amount', null, ['class' => 'form-control width-40 pull-right currency_id_amount'   ]); !!}
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
				<div class="col-md-12">
					
					<table class="table" id="entry_table">
					  <thead>
						<tr>
						  <th>{{ trans('home.Account') }}</th>
						  <th>{{ trans('home.Credit') }}</th>
						  <th>{{ trans('home.Debit') }}</th>
						  <th>@lang("home.Cost Center")</th>
						  <th>{{ trans('home.Note') }}</th>
						  <th></th>
						</tr>
					  </thead>
					  <tbody>
						<tr >
						  <td class="col-xs-1">
							{{ Form::select('account_id[]',$accounts,null,['class'=>'form-control select2','placeholder'=>trans('home.please account'),'required']) }}
						  </td>
                            <td class="col-xs-1 crd-amount">
								{{ Form::number('credit[]',0,['class'=>'form-control credit','data_able'=>0,'style'=>'width:90%','required','step'=>'any','min'=>0]) }}
								<span class="rows_balances btn btn-primary" data-disabled="false" ><i class="fas fa-arrows-alt-h"></i></span>
							</td>
							<td class="col-xs-1 deb-amount">
								{{ Form::number('debit[]',0,['class'=>'form-control debit','required','data_able'=>0,'style'=>'width:100%','step'=>'any','min'=>0]) }}
								<!--<span class="rows_balances btn btn-primary" data-disabled="false"><i class="fas fa-arrows-alt-h"></i></span>-->
							</td>
						  <td class="col-xs-1">
							{{ Form::select('cost_center_id[]',$costs,null,['class'=>'form-control select2','placeholder'=>trans('home.please account') ]) }}
						   </td>
						  <td class="col-xs-3">
							{{ Form::text('text[]',null,['class'=>'form-control ']) }}
						  </td>
						  <td class="col-xs-1 text-center">
							<span class="addBtn">
								<i class="fa fa-plus"></i>
							  </span>
						  </td>
						</tr>
						
						<tr >
							<td class="col-xs-1">
							  {{ Form::select('account_id[]',$accounts,null,['class'=>'form-control select2','placeholder'=>trans('home.please account'),'required']) }}
							</td>
							
							
	                        <td class="col-xs-1 crd-amount">
								{{ Form::number('credit[]',0,['class'=>'form-control credit','data_able'=>'##','style'=>'width:90%','required','step'=>'any','min'=>0]) }}
								<span class="rows_balances btn btn-primary" data-disabled="false" ><i class="fas fa-arrows-alt-h"></i></span>
							</td>
							<td class="col-xs-1 deb-amount">
								{{ Form::number('debit[]',0,['class'=>'form-control debit','required','data_able'=>'#','style'=>'width:100%','step'=>'any','min'=>0]) }}
								<!--<span class="rows_balances btn btn-primary" data-disabled="false"><i class="fas fa-arrows-alt-h"></i></span>-->
							</td>
							<td class="col-xs-1">
								{{ Form::select('cost_center_id[]',$costs,null,['class'=>'form-control select2','placeholder'=>trans('home.please account') ]) }}
							</td>
							<td class="col-xs-3">
								{{ Form::text('text[]',null,['class'=>'form-control ']) }}
							</td>
						  </tr>
						  <tr id="addRow">
							<td class="col-xs-3"></td>
							<td class="col-xs-3">
						    	 <label class="label-control">  {{ trans('home.Total Credit') }} : <span id="total-credit">0</span> </label>
								<input id="total-credit-input" name="total_credit" type="hidden" value="0">
								</td>
							<td class="col-xs-3">
								<label class="label-control">  {{ trans('home.Total Debit') }} : <span id="total-debit">0</span> </label>
								<input id="total-debit-input" name="total_debit" type="hidden" value="0">
							</td>
						  </tr>
					  </tbody>
					</table>
					  <div class="col-md-1 pull-right">
						<button class="btn btn-primary pull-right save-daily">{{ trans('home.Save') }}</button>
					  </div>
					</div>
					<input type="text" id="index" hidden value="3">
					<input type="text" id="index_rows" hidden value="1">
					
			</div>
		</div>
	</div> <!--box end-->
{!! Form::close() !!}
</section>

@endsection
@section('javascript')
  
  <script>
      $('#date').datetimepicker({
            format: moment_date_format + ' ' + moment_time_format,
            ignoreReadonly: true,
        });

  
    $('.debit, .credit').on("click" , function(){
         $(this).select();
     });
	function addByClick(isn){
	    set = $('#entry_table .debit');
       
        set.each(function(index, element) {
            thisVl  = $(this);
            ro      = thisVl.parent().find(".debit")
            ros     = thisVl.parent().parent().find(".credit")
            if( ro.attr("data_able") == "#"){
                ro.on("click",function(e){
                    if( $(this).attr("data_able") == "#"){
                        addRow();
                        e.preventDefault();
                        ros.attr("data_able","0");
                        $(this).attr("data_able","0");
                        $(this).select();
                        changeRowsLineHash();
                        changeRowsLine();
                        changeRowsLineCredit()

                    }
                })
            }
        });
	     
	}
	function addByClickCredit(isn){
	    set = $('#entry_table .credit');
       
        set.each(function(index, element) {
            thisVl  = $(this);
            ro      = thisVl.parent().find(".credit")
            ros     = thisVl.parent().parent().find(".debit")
            if( ro.attr("data_able") == "##"){
                ro.on("click",function(e){
                    if( $(this).attr("data_able") == "##"){
                        addRow();
                        e.preventDefault();
                        ros.attr("data_able","0");
                        $(this).attr("data_able","0");
                        $(this).select();
                        changeRowsLineHash();
                        changeRowsLine();
                        changeRowsLineCredit()

                    }
                })
            }
        });
	     
	}
    function changeRowsLine(){
         set = $('#entry_table .debit');
         length = set.length ;
         set.each(function(index, element) {
                thisVl  = $(this);
                 
                if( index >= length-1 ){
                    thisVl.on("click",function(e){
                        if( $(this).attr("data_able") == "#"){
                            addRow();
                            e.preventDefault();
                            $(this).attr("data_able","0");
                            $(this).parent().parent().find(".credit").attr("data_able","0");
                            changeRowsLineHash();
                            changeRowsLine();
                            changeRowsLineCredit();
                             $(this).select();
                        } 
                    })
                }
            });
          
    }
    function changeRowsLineHash(){
         set = $('#entry_table .debit');
         length = set.length ;
         set.each(function(index, element) {
                thisVl  = $(this);
                if( index >= length-1 ){
                     $(this).parent().parent().find('.credit').attr("data_able","##");
                     $(this).attr("data_able","#");
                     
                }else{
                     $(this).attr("data_able","0");
                     $(this).parent().parent().find('.credit').attr("data_able","0");
                }
            });
          
    }
    function changeRowsLineCredit(){
         set = $('#entry_table .credit');
         length = set.length ;
         set.each(function(index, element) {
                thisVl  = $(this);
                 
                if( index >= length-1 ){
                    thisVl.on("click",function(e){
                        if( $(this).attr("data_able") == "##"){
                            addRow();
                            e.preventDefault();
                            $(this).attr("data_able","0");
                            $(this).parent().parent().find(".debit").attr("data_able","0");
                            changeRowsLineHash();
                            changeRowsLine();
                            changeRowsLineCredit();
                             $(this).select();

                        } 
                    })
                }
            });
          
    }
    addByClick($("#index_rows").val());
   addByClickCredit($("#index_rows").val());
	function formatRows(main, prefer, common,index) {
	 
		return '<tr><td class="col-xs-1">{{ Form::select('account_id[]',$accounts,null,['class'=>'form-control select2 ','placeholder'=>trans('home.please account'),'required']) }}</td>' +
				'<td class="col-xs-1 crd-amount">{{ Form::number('credit[]',0,['class'=>'form-control credit','required','data_able'=>'#','style'=>'width:90%','step'=>'any','min'=>0]) }}<span class="rows_balances btn btn-primary" data-disabled="false" ><i class="fas fa-arrows-alt-h"></i></span></td>' +
				'<td class="col-xs-1 deb-amount">{{ Form::number('debit[]',0,['class'=>'form-control debit','required','data_able'=>'#','style'=>'width:100%','step'=>'any','min'=>0]) }} </td>' +
				'<td class="col-xs-1">{{ Form::select('cost_center_id[]',$costs,null,['class'=>'form-control select2 ','placeholder'=>trans('home.please account')]) }}</td>' +
				'<td class="col-xs-1">{{ Form::text('text[]',null,['class'=>'form-control ']) }}</td>' +
				'<td class="col-xs-1 text-center"><a  onClick="deleteRow(this)">' +
				'<i class="fas fa-trash" aria-hidden="true"></a></td></tr>';
	 
		 
	};
	function deleteRow(trash) {
		setTimeout(() => {
			$(trash).closest('tr').remove();
            allChanged();
            changeRowsLineHash();
            changeRowsLine();
            changeRowsLineCredit();
		}, 1000);
	};
	$('.currency_id_amount').change(function(){
			update_currency();
		})
		$('.currency_id').change(function(){
				var id = $(this).val();
				if(id == ""){
					$(".currency_id_amount").val("");
					$(".curr_column").addClass("hide");
				}else{
					$.ajax({
							url:"/symbol/amount/"+id,
						dataType: 'html',
						success:function(data){
							var object  = JSON.parse(data);
							$(".currency_id_amount").val(object.amount);
							// $(".curr_column").removeClass("hide");
							update_currency();
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
	
	 
	function addRow() {
		var main = $('.addMain').val();
		var index = $('#index').val();
		var preferred = $('.addPrefer').val();
		var common = $('.addCommon').val();
		index = parseInt(index) + 1;
		$('#index').val(index);
		$(formatRows(main,preferred,common,index)).insertBefore('#addRow');
		$('.select2').select2();
		update_deptit();
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
					debit:0,
					credit:0
				};
				
	function allChanged() {
		$('.debit, .credit').each(function(){
			   var  debit =  0;
		       var  credit =  0;
			   $('.debit').each(function(){
					if ($(this).val()) {
						debit +=  parseFloat($(this).val()) ;
					}
				})
				$('.credit').each(function(){
					if ($(this).val()) {
						credit += parseFloat($(this).val());
					}
				})
				$('#total-debit').text(debit.toFixed(2));
				$('#total-credit').text(credit.toFixed(2));

				$('#total-debit-input').val(debit.toFixed(2));
				$('#total-credit-input').val(credit.toFixed(2));
				terms  =    {
					debit:debit.toFixed(2),
					credit:credit.toFixed(2)
				};
	    })
	}			
	function update_deptit () {
		$('.debit, .credit').change(function(){
			   var  debit =  0;
		       var  credit =  0;
			   $('.debit').each(function(){
					if ($(this).val()) {
						debit +=  parseFloat($(this).val()) ;
					}
				})
				$('.credit').each(function(){
					if ($(this).val()) {
						credit += parseFloat($(this).val());
					}
				})
				$('#total-debit').text(debit.toFixed(2));
				$('#total-credit').text(credit.toFixed(2));

				$('#total-debit-input').val(debit.toFixed(2));
				$('#total-credit-input').val(credit.toFixed(2));
				terms  =    {
					debit:debit.toFixed(2),
					credit:credit.toFixed(2)
				};
	    })
    	$('#entry_table .debit').each(function(){
        		var el = $(this).on("change",function(){
        			if($(this).val() == 0 || $(this).val() == ""){
        				el.parent().parent().find('.credit').attr("readOnly",false) ;
        				 
        			}else{
        				el.parent().parent().find('.credit').val(0) ;
        				el.parent().parent().find('.credit').attr("readOnly",true) ;
        				 
        			}
        		})
        		
        		
        	})
    	$('#entry_table .credit').each(function(){
    		var el = $(this).on("change",function(){
    			if($(this).val() == 0 || $(this).val() == ""){
    				el.parent().parent().find('.debit').attr("readOnly",false) ;
    				el.parent().parent().find('.deb-amount .rows_balances').attr("data-disabled","false") ;
    				 
    			}else{
    				el.parent().parent().find('.debit').val(0) ;
    				el.parent().parent().find('.debit').attr("readOnly",true) ;
    				 
    			}
    		})
    		 
    	})
    	$('#entry_table  .crd-amount .rows_balances').each(function(){
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
        						e.parent().parent().find('.debit').val(0) ;
        						e.parent().parent().find('.credit').val(0) ;
        					    e.parent().parent().find('.credit').attr("readOnly",false) ;
        						allChanged();
    				        }else{
    				            if(Math.abs(parseFloat(old_val_credit)) > Math.abs(parseFloat( total_balance))){
            					    e.parent().parent().find('.credit').attr("readOnly",false) ;
            						e.parent().parent().find('.credit').val( (Math.abs(parseFloat(old_val_credit) - Math.abs(parseFloat( total_balance)))).toFixed(3) ) ;
            						e.parent().parent().find('.debit').val(0) ;
            					    e.parent().parent().find('.debit').attr("readOnly",true) ;
    				            }else{
            					    e.parent().parent().find('.debit').attr("readOnly",false) ;
            						e.parent().parent().find('.debit').val( (Math.abs(parseFloat(old_val_debit) + Math.abs(parseFloat( total_balance) ) - Math.abs(parseFloat(old_val_credit)))).toFixed(3) ) ;
            						e.parent().parent().find('.credit').val(0) ;
            					    e.parent().parent().find('.credit').attr("readOnly",true) ;
    				            }
        						allChanged();
    				            
    				        }
    					}else if(total_balance > 0){
    					    if(Math.abs(total_balance) == e.parent().parent().find('.debit').val()){
        					    e.parent().parent().find('.credit').attr("readOnly",false) ;
        						e.parent().parent().find('.credit').val(  0  ) ;
        						e.parent().parent().find('.debit').val(0) ;
        					    e.parent().parent().find('.debit').attr("readOnly",false) ;
        				 
    					    }else{
    					        if(Math.abs(parseFloat(old_val_debit)) > Math.abs(parseFloat( total_balance))){
            					    e.parent().parent().find('.debit').attr("readOnly",false) ;
            						e.parent().parent().find('.debit').val( (Math.abs(parseFloat(old_val_debit) - parseFloat(total_balance))).toFixed(3) ) ;
            						e.parent().parent().find('.credit').val(0) ;
            					    e.parent().parent().find('.credit').attr("readOnly",true) ;
    					        }else{
    					            e.parent().parent().find('.credit').attr("readOnly",false) ;
            						e.parent().parent().find('.credit').val( (Math.abs(parseFloat(total_balance) + parseFloat(old_val_credit)) - parseFloat(old_val_debit) ).toFixed(3)) ;
            						e.parent().parent().find('.debit').val(0) ;
            					    e.parent().parent().find('.debit').attr("readOnly",true) ;   
    					            
    					        }
            						allChanged();
    					        
    					    }
    					}else{
    				// 		e.parent().parent().find('.credit').val(  parseFloat(old_val) ) ;
    						allChanged();
    					}
    					
    				// 	if(e.parent().parent().find('.credit').val() == 0 || e.parent().parent().find('.credit').val() == ""){
    				// 		// alert("balance : " + total_balance + " _______ " + "old : " + old_val);
    				// 		if(total_balance < 0){
    				// 			e.parent().parent().find('.credit').val( Math.abs(parseFloat(old_val) + parseFloat( total_balance)) ) ;
    				// 			allChanged();
    				// 		}else if(total_balance > 0){
    				// 			e.parent().parent().find('.credit').val( Math.abs(parseFloat(total_balance) + parseFloat(old_val)) ) ;
    				// 			allChanged();
    				// 		}else{
    				// 			e.parent().parent().find('.credit').val(  parseFloat(old_val) ) ;
    				// 			allChanged();
    				// 		}
    				// 	}else{
    						 
    				// 		if(total_balance < 0){
    				// 			e.parent().parent().find('.credit').val(  Math.abs(parseFloat(old_val) + parseFloat((total_balance  ))) ) ;
    				// 			allChanged();
    				// 		}else if(total_balance > 0){
    				// 			e.parent().parent().find('.credit').val(  Math.abs(parseFloat(total_balance) + parseFloat(old_val)) ) ;
    				// 			allChanged();
    				// 		}else{
    				// 			e.parent().parent().find('.credit').val(  parseFloat(old_val) ) ;
    				// 			allChanged();
    							
    				// 		}
    				// 	}
    				// 	e.parent().parent().find('.debit').val(0) ;
    				// 	e.parent().parent().find('.debit').attr("readOnly",true) ;
    				// 	e.parent().parent().find(".deb-amount .rows_balances").attr("data-disabled","true");
    				// 	e.parent().parent().find(".deb-amount .rows_balances").attr("disabled",true);
    				} 
    			})
    			
    		})
		
		
	}
    $('#save-daily').on("click",function(e){
			$(this).preventDefault();
		 
	})
    $('#daily_payment_form').submit(function(e){
		if (terms.debit != terms.credit  ||  terms.debit == 0) {
			e.preventDefault();
			alert("{{ trans('home.Total Debit  and total credit must be equaled') }}");
		}
	})
  </script>
@endsection