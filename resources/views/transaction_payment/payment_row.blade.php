<div class="modal-dialog" role="document" style="width:90%;fon-size:medium">
  <div class="modal-content">

    {!! Form::open(['url' => action('TransactionPaymentController@store'), 'method' => 'post', 'id' => 'transaction_payment_add_form', 'files' => true ]) !!}
    {!! Form::hidden('transaction_id', $transaction->id); !!}
    @if(!empty($transaction->location))
      {!! Form::hidden('default_payment_accounts', $transaction->location->default_payment_accounts, ['id' => 'default_payment_accounts']); !!}
    @endif
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'purchase.add_payment' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
      @if(!empty($transaction->contact))
        <div class="col-md-4">
          <div class="well">
            <strong>
            @if(in_array($transaction->type, ['purchase', 'purchase_return']))
              @lang('purchase.supplier') 
            @elseif(in_array($transaction->type, ['sale', 'sell_return']))
              @lang('contact.customer') 
            @endif
            </strong>:{{ $transaction->contact->name }}<br>
            @if($transaction->type == 'purchase')
            <strong>@lang('business.business'): </strong>{{ $transaction->contact->supplier_business_name }}
            @endif
          </div>
        </div>
        @endif
        <div class="col-md-4">
          <div class="well">
            @if(in_array($transaction->type, ['sale', 'sell_return']))
              <strong>@lang('sale.invoice_no'): </strong>{{ $transaction->invoice_no }}
            @else
              <strong>@lang('purchase.ref_no'): </strong>{{ $transaction->ref_no }}
            @endif
            @if(!empty($transaction->location))
              <br>
              <strong>@lang('purchase.location'): </strong>{{ $transaction->location->name }}
            @endif

          </div>
        </div>
        
        <div class="col-md-4">
          <div class="well">
            @php
            $id = $transaction->id;
            $tr = \App\Transaction::find($id) ;
            $final_total_OLD = $tr->final_total;
            $cost_shipp  =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($id,$tr) {
                                                                    $query->where("type",1);
                                                                    $query->where("transaction_id",$id);
                                                                    $query->where("contact_id",$tr->contact_id);
                                                                })->sum("total");
            @endphp
            <strong>@lang('sale.total_amount'): </strong><span class="display_currency" data-currency_symbol="true">{{ $transaction->final_total + $cost_shipp }}</span><br>
            <strong>@lang('purchase.payment_note'): </strong>
            @if(!empty($transaction->additional_notes))
            {{ $transaction->additional_notes }}
            @else
              --
            @endif
          </div>
        </div>
      </div>
      <div class="row">
          <div class="col-md-12">
          <div class="well">
            @php
                $parent  = "";
                $total   = 0 ;
                $refund  = "";
            @endphp
            @if(count($cheques)>0) 
                @php
                    $parent  .= "<h3 style='text-align:center;color :red'>".__('messages.there_is_cheques')."</h3>";
                    
                    // $parent .=  '<div class="btn-group">
                    //   <button  type="button" class="btn btn-note dropdown-toggle btn-xs" 
                    //   style="border:1px solid black;margin-top:10px" data-toggle="dropdown" aria-expanded="false">' .
                    //   __("messages.related_check") .
                    //   '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                    //     </span>
                    //     </button>';
                    $parent .=  '<div class="btn-group">';
                      //' <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                @endphp
                @foreach($cheques as $k => $check)
                    @php
                        ($check->status == 2)? $refund = "Refund":$refund = "";  
                        $parent  .= "\n<a href='#' class='btn btn-info btn-modal'    data-href='/cheque/show/".$check->id."'data-container='.view_modal'> ".$check->ref_no." //  <b>".$check->amount."</b> ".$refund."</a><br>";
                        if($check->status != 2){
                          $total   +=  $check->amount;
                        }
                    @endphp
                @endforeach
                @php
                    // $parent .='</ul>
                    $parent .='
                    </div><br>';
                @endphp
            @endif
            @if(count($paymentVoucher)>0)
                @php
                    $parent  .= "<h3 style='text-align:center;color :red'>".__('There is payments')."</h3>";
                    
                    // $parent .=  '<div class="btn-group">
                    //   <button  type="button" class="btn btn-note dropdown-toggle btn-xs" 
                    //   style="border:1px solid black;margin-top:10px" data-toggle="dropdown" aria-expanded="false">' .
                    //   __("messages.related_check") .
                    //   '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                    //     </span>
                    //     </button>';
                    $parent .=  '<div class="btn-group">';
                      //' <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                 @endphp
              @foreach($paymentVoucher as $k => $li)
                    @php
                         
                        $total   +=  $li->amount;
                        
                    @endphp
                @endforeach
                @php
                    // $parent .='</ul>
                    $parent .='
                    </div><br>';
                @endphp
            @endif
            
            @if(count($transaction_child)>0)
                  @php
                    $parent  .= "<h3 style='text-align:center;color :red'>".__('messages.there_is_invoices')."</h3>";
                    
                    // $parent .=  '<div class="btn-group">
                    //   <button  type="button" class="btn btn-note dropdown-toggle btn-xs" 
                    //   style="border:1px solid black;margin-top:10px" data-toggle="dropdown" aria-expanded="false">' .
                    //   __("messages.related_check") .
                    //   '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                    //     </span>
                    //     </button>';
                    $parent .=  '<div class="btn-group">';
                      //' <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                @endphp
                @foreach($transaction_child as $k => $child)
                  @if($child->sub_type == null)
                    @php
                          
                        $parent  .= "\n<a href='#' class='btn btn-info btn-modal'    data-href='/sells/".$child->id."'data-container='.view_modal'> ".$child->invoice_no." //  <b>".$child->final_total."</b></a><br>";
                        
                        $total   +=  $child->final_total;
                        
                    @endphp
                  @endif
                @endforeach 
                @php
                    // $parent .='</ul>
                    $parent .='
                    </div><br>';
                @endphp
            @endif
            @php
                // $parent .='</ul>
                $parent .='
                <hr><br><b>'.
                $total.' 
                </b><b style="font-size:18px;float:right">Remaining :'. abs(round($transaction->final_total+$cost_shipp,config("constants.currency_precision")) - $total) . '</b>'; 
            @endphp
            {!! $parent !!}
          </div>
        </div>
        <div class="col-md-12">
           
          @if(!empty($transaction->contact))
            <strong>@lang('lang_v1.advance_balance'):</strong> <span class="display_currency" data-currency_symbol="true">{{$transaction->contact->balance}}</span>

            {!! Form::hidden('advance_balance', $transaction->contact->balance, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
          @endif
         
      </div>
      </div>
      <div class="row payment_row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("amount" , __('sale.amount') . ':*') !!} 
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              {!! Form::text("amount",  abs(round($transaction->final_total+$cost_shipp,config("constants.currency_precision")) - $total) , ['id'=>'amount','class' => 'form-control input_number','data-rule-min-value' => 1, 'required', 'placeholder' => 'Amount', 'data-rule-max-value' => @num_format($payment_line->amount), 'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated])]); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("paid_on" , __('lang_v1.paid_on') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('paid_on', @format_datetime($payment_line->paid_on), ['class' => 'form-control','id'=>'paid_on',   'required']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4" >
          <div class="form-group">
            {!! Form::label("method" , __('purchase.payment_method') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              {!! Form::select("method", $payment_types, $payment_line->method, ['class' => 'form-control select2 payment_types_dropdown' , 'id'=>'method', 'required', 'style' => 'width:100%;']); !!}
            </div>
          </div>
        </div>
        @if(!empty($accounts))
          <div class="col-md-6" id="pay_os_account">
            <div class="form-group">
              {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fas fa-money-bill-alt"></i>
                </span>
                {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' , ['class' => 'form-control select2','required', 'id' => "account_id", 'style' => 'width:100%;']); !!}
              </div>
            </div>
          </div>
        @endif
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document', ['accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
            <p class="help-block">
            @includeIf('components.document_help_text')</p>
          </div>
        </div>
        <div class="clearfix"></div> 
        
          @include('transaction_payment.payment_type_details',['cheque_type'=>$cheque_type])
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
            {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'required' ,'rows' => 3]); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
        @php $business = \App\Business::find(auth()->user()->business_id);  $separate_delivery = \App\Transaction::where("separate_parent",$transaction->id)->where("separate_type","partial")->get();   @endphp
        @if($business->separate_pay_sell == 1)
          @if(count($separate_delivery) == 0)
            @if(($transaction->status == "ApprovedQuotation" && $transaction->sub_status == "proforma")  || ($transaction->status == "draft" && $transaction->sub_status == "proforma")  || ($transaction->status == "delivered" && $transaction->sub_status == "proforma"))
              <div class="col-md-6">
              </div>
              @php 
                $have_payment = 0;
                $allRelated = \App\Transaction::where("separate_parent",$transaction->id)->where("separate_type","payment")->get();
                foreach ($allRelated as $key => $value) {
                  $payment  = \App\TransactionPayment::where("transaction_id",$value->id)->first(); 
                  if(!empty($payment)){
                    $have_payment = 1;	
                    break;
                  }
                }
                @endphp
                {{-- @php dd($have_payment); @endphp --}}
              @if($have_payment == 1)
                <div class="col-md-4 hide">
                  <label>
                    {!! Form::checkbox('separate_pay_sell', 1,  
                        1, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_payment_invoice' ) }} 
                  </label>
                
                </div>
                <div class="col-md-4 ">
                  <label style="color:red;background-color:#ffdfdf;padding:10px;border-radius:5px;">
                      @lang("This Payment Will Create Separate Invoice")
                  </label>
                
                </div>

              @else
                <div class="col-md-4">
                  <label>
                    {!! Form::checkbox('separate_pay_sell', 1,  
                        null , 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_payment_invoice' ) }} 
                  </label>
                </div>
              @endif
            @endif
          @endif
        @endif
      <button type="submit" id="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default "  data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
  $('.select2').select2()

  $('#amount').change(function(){
     if( $(this).val()  == 0){
      $("#submit").attr("disabled",true);
    }else{
      $("#submit").attr("disabled",false);
    }
  });
  $('#cheque_number').change(function(){
     if( $(this).val()  != "" ){
      $("#submit").attr("disabled",false);
    }else{
      $("#submit").attr("disabled",true);
    }
  });
  $('#method').change(function(){
    var id = $(this).val();
     $.ajax({
        type: "GET",
        url: "/account/account-ty/"+id,
        success: function(response) {
           console.log(JSON.stringify(response.array));
           $('#account_id').html("");
           ie =  0;
           html = '';
          //  html = '<option selected="selected">None</option>';
           for( var r in response.array){
            if(ie == 0){
              html += "<option selected='selected' value='"+r+"' >"+response.array[r]+"</option>";
            }else{
              html += "<option value='"+r+"' >"+response.array[r]+"</option>";
            }
            ie++;
          }
          $('#account_id').append(html);
        }
    });
  });

  $('#paid_on').datetimepicker({
            format: moment_date_format + ' ' + moment_time_format,
            ignoreReadonly: true,
        });

  $('#method').change(function(){
			var  method  = $('.payment_types_dropdown option:selected').val();
			if (method == 'cheque') {
				$('#pay_os_account').hide();
        if($("#cheque_number").val() == "" || $("#cheque_number").val() == null){
          $("#submit").attr("disabled",true);
        }  
        console.log("here !...."); 
      }else{
				$('#pay_os_account').show();
			}
			
		})
</script>