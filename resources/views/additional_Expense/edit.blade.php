<style>
  .imgs_ul{padding: 0;}
  .imgs_ul li{display: inline;position: relative;}
  .imgs_ul .close_item{position: absolute;z-index: 100;font-size: 22px;color: red;cursor: pointer;}
  .show_eye{
    position: absolute;
    bottom: 0;
    z-index: 10;
    font-size: 24px;
  }
</style>
<div class="modal-dialog" role="document" style="width:90%">
  <div class="modal-content">
   <?php 
          $contacts                 = \App\Contact::suppliers();
          $cost_center              = \App\Account::cost_centers();
          $expenses                 = \App\Account::main('Expenses');
          $transactions             = $purchase;
          $currency_global_id       = ($purchase->currency_id != null)?$purchase->currency_id:null;
          $currency_global_amount   = ($purchase->currency_id != null)?$purchase->exchange_price:null; 
          
     ?>    
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <div class="row">
        <div class="col-md-4">
           <h4 class="modal-title">@lang( 'home.edit_Expense' )</h4>
        </div>
        <div @if( $currency_global_id == null ) class="add_section hide  col-md-4" @else class="add_section    col-md-4" @endif>
          <div class="form-group">
            @php
            $main_curr_id     = null;
                $main_curr_amount = null;
             if($purchase->additional_shipings->first() != null){
              if($purchase->additional_shipings->first()->currency_id != null){
                $main_curr_id     = $purchase->additional_shipings->first()->currency_id;
                $main_curr_amount = $purchase->additional_shipings->first()->exchange_rate;
              }}
            @endphp
            <div class="multi-input text-center">
                    {!! Form::label('add_currency_id', __('business.currency') . ':') !!} 
                    <br> 
                    {!! Form::select('add_currency_id', $currencies, ($main_curr_id != null)?$main_curr_id:$currency_global_id, ['class' => 'form-control   add_currency_id  select2', "style" => 'width:49%','placeholder' => __('messages.please_select') ]); !!}
                    {!! Form::text('add_currency_id_amount', ($main_curr_amount != null)?$main_curr_amount:$currency_global_amount, ['class' => 'form-control  pull-right add_currency_id_amount' , "style" => 'width:51%',  ]); !!}
            
            </div>
          </div>
        </div>
      </div>
    </div>
    
 
    <div class="container" style=" width: 100%;">
      <div class="modal-body">
        <div class="row">
          <table class="table table-bordered table-responsive " id="additional_expense">
            <thead>
              <tr>
                <th>@lang("home.Supplier")</th>
                <th>@lang("home.Amount")</th>
                <th>@lang("home.Vat")</th>
                <th>@lang("home.Total")</th>
                <th @if($currency_global_id == null) class="ship_curr hide header_texts" @else class="ship_curr  header_texts" @endif >@lang("Amount")</th>
                <th @if($currency_global_id == null) class="ship_curr hide header_vats" @else class="ship_curr  header_vats" @endif >@lang("Vat")</th>
                <th @if($currency_global_id == null) class="ship_curr hide header_totals" @else class="ship_curr  header_totals" @endif >@lang("Total")</th>
                <th>@lang("home.Debit")</th>
                <th>@lang("home.Cost Center")</th>
                <th>@lang("home.Note")</th>       
                <th  @if($currency_global_id == null) class="ship_curr hide" @else class="ship_curr " @endif  style="width:300px;">@lang("business.currency")</th>
                <th>@lang("home.Date")</th>
                <th class="btn-primary" onClick="addRow()"><i class="fa fa-plus"></i></th>
              </tr>
            </thead>
            <tbody>
              <?php
                 $total_shiping_s = 0;
                 $total_shiping_vat = 0;
                 $total_shiping_amount = 0;
                  
              ?>
               @foreach ($purchase->additional_shipings as $ships)
                @if($ships->type == $ship_from)
                  @if($transaction_recieved != null)
                    @if($ships->t_recieved == $transaction_recieved)
                      @foreach ($ships->items as $item) 
                        @php 
                                $main_currency_id         = ($ships->currency_id != null)?$ships->currency_id:$currency_global_id; 
                                $main_currency_amount     = ($ships->currency_id != null)?$ships->exchange_rate:$currency_global_amount;
                        @endphp 
                      <tr>
                        <input type="hidden" name="additional_shipping_item_id[]" value="{{ $item->id }}" >
                        <div class="row">
                          <td class="col-xs-1">{{ Form::select('old_shipping_contact_id[]',$contacts,$item->contact_id,['class'=>'form-control select2  shipping-select2 supplier','placeholder'=>trans('home.please account')]) }}</td>
                          <td class="col-xs-1">{{ Form::number('old_shipping_amount[]',$item->amount,['class'=>'form-control shipping_amount_s','required','step'=>'any','min'=>0]) }}</td>
                          <td class="col-xs-1">{{ Form::number('old_shipping_vat[]',$item->vat,['class'=>'form-control shipping_tax','required','step'=>'any','min'=>0]) }}</td>
                          <td class="col-xs-1">{{ Form::number('old_shipping_total[]',$item->total,['class'=>'form-control shipping_total','required','step'=>'any','min'=>0,'readOnly']) }}</td>
                          <td @if($currency_global_id == null) class="col-xs-1  ship_curr hide" @else class="col-xs-1  ship_curr " @endif>{{ Form::number('old_shipping_amount_curr[]',0,['class'=>'form-control  shipping_amount_curr','required','step'=>'any','min'=>0]) }}</td> 
                          <td @if($currency_global_id == null) class="col-xs-1  ship_curr hide" @else class="col-xs-1  ship_curr " @endif>{{ Form::number('old_shipping_vat_curr[]',0,['class'=>'form-control shipping_tax_curr','required','step'=>'any','min'=>0]) }}</td> 
                          <td @if($currency_global_id == null) class="col-xs-1  ship_curr hide" @else class="col-xs-1  ship_curr " @endif>{{ Form::number('old_shipping_total_curr[]',0,['class'=>'form-control shipping_total_curr','required','step'=>'any','min'=>0,'readOnly']) }}</td> 
                          <td class="col-xs-1">{{ Form::select('old_shipping_account_id[]',$expenses,$item->account_id,['class'=>'form-control select2 ','required']) }}</td>
                          <td class="col-xs-1">{{ Form::select('old_shipping_cost_center_id[]',$cost_center,$item->cost_center_id,['class'=>'form-control select2  shipping-select2 cost_center_id','placeholder'=>trans('home.please account')]) }}</td>
                          <td class="col-xs-1">{{ Form::text('old_shiping_text[]',$item->text,['class'=>'form-control ' ]) }}</td>
                          <td @if($currency_global_id == null) class="col-xs-1 ship_curr currency_check hide" @else class="col-xs-1 ship_curr currency_check" @endif  style="width:300px;">
                            <div class="col-md-12">
                              <div class="form-group">
                                <div class="multi-input text-center">    
                                  {!! Form::select('old_line_currency_id[]', $currencies, ($item->currency_id != null)?$item->currency_id:$main_currency_id, ['class' => 'form-control  width-100 line_currency_id  select2' , 'placeholder' => __('messages.please_select') ]); !!} 
                                  {!! Form::text('old_line_currency_id_amount[]', ($item->exchange_rate != null)?$item->exchange_rate:$main_currency_amount, ['class' => 'form-control  pull-right width-100 line_currency_id_amount' ,  ]); !!} 
                                </div>
                              </div>
                            </div>
                          </td>
                          <td class="col-xs-1">{{ Form::date('old_shiping_date[]',$item->date,['class'=>'form-control ','required']) }}</td>
                          <td class="col-xs-1 text-center"><a href="#" onClick="deleteRow(this)"><i class="fas fa-trash" aria-hidden="true"></a></td>
                        </div>
                        </tr>
                      <?php
                          $total_shiping_s += $item->total;
                          $total_shiping_vat += $item->vat;
                          $total_shiping_amount += $item->amount;
                      ?>
                    @endforeach
                    @endif
                  @else
                    @foreach ($ships->items as $item) 
                          @php 
                                  $main_currency_id         = ($ships->currency_id != null)?$ships->currency_id:$currency_global_id; 
                                  $main_currency_amount     = ($ships->currency_id != null)?$ships->exchange_rate:$currency_global_amount;
                          @endphp 
                      <tr>
                        <input type="hidden" name="additional_shipping_item_id[]" value="{{ $item->id }}" >
                        <div class="row">
                          <td class="col-xs-1">{{ Form::select('old_shipping_contact_id[]',$contacts,$item->contact_id,['class'=>'form-control select2  shipping-select2 supplier','placeholder'=>trans('home.please account')]) }}</td>
                          <td class="col-xs-1">{{ Form::number('old_shipping_amount[]',$item->amount,['class'=>'form-control shipping_amount_s','required','step'=>'any','min'=>0]) }}</td>
                          <td class="col-xs-1">{{ Form::number('old_shipping_vat[]',$item->vat,['class'=>'form-control shipping_tax','required','step'=>'any','min'=>0]) }}</td>
                          <td class="col-xs-1">{{ Form::number('old_shipping_total[]',$item->total,['class'=>'form-control shipping_total','required','step'=>'any','min'=>0,'readOnly']) }}</td>
                          <td @if($currency_global_id == null) class="col-xs-1  ship_curr hide" @else class="col-xs-1  ship_curr " @endif>{{ Form::number('old_shipping_amount_curr[]',0,['class'=>'form-control  shipping_amount_curr','required','step'=>'any','min'=>0]) }}</td> 
                          <td @if($currency_global_id == null) class="col-xs-1  ship_curr hide" @else class="col-xs-1  ship_curr " @endif>{{ Form::number('old_shipping_vat_curr[]',0,['class'=>'form-control shipping_tax_curr','required','step'=>'any','min'=>0]) }}</td> 
                          <td @if($currency_global_id == null) class="col-xs-1  ship_curr hide" @else class="col-xs-1  ship_curr " @endif>{{ Form::number('old_shipping_total_curr[]',0,['class'=>'form-control shipping_total_curr','required','step'=>'any','min'=>0,'readOnly']) }}</td> 
                          <td class="col-xs-1">{{ Form::select('old_shipping_account_id[]',$expenses,$item->account_id,['class'=>'form-control select2 ','required']) }}</td>
                          <td class="col-xs-1">{{ Form::select('old_shipping_cost_center_id[]',$cost_center,$item->cost_center_id,['class'=>'form-control select2  shipping-select2 cost_center_id','placeholder'=>trans('home.please account')]) }}</td>
                          <td class="col-xs-1">{{ Form::text('old_shiping_text[]',$item->text,['class'=>'form-control ' ]) }}</td>
                          <td @if($currency_global_id == null) class="col-xs-1 ship_curr currency_check hide" @else class="col-xs-1 ship_curr currency_check" @endif  style="width:300px;">
                            <div class="col-md-12">
                              <div class="form-group">
                                <div class="multi-input text-center">    
                                  {!! Form::select('old_line_currency_id[]', $currencies, ($item->currency_id != null)?$item->currency_id:$main_currency_id, ['class' => 'form-control  width-100 line_currency_id  select2' , 'placeholder' => __('messages.please_select') ]); !!} 
                                  {!! Form::text('old_line_currency_id_amount[]', ($item->exchange_rate != null)?$item->exchange_rate:$main_currency_amount, ['class' => 'form-control  pull-right width-100 line_currency_id_amount' ,  ]); !!} 
                                </div>
                              </div>
                            </div>
                          </td>
                          <td class="col-xs-1">{{ Form::date('old_shiping_date[]',$item->date,['class'=>'form-control ','required']) }}</td>
                          <td class="col-xs-1 text-center"><a href="#" onClick="deleteRow(this)"><i class="fas fa-trash" aria-hidden="true"></a></td>
                        </div>
                        </tr>
                      <?php
                          $total_shiping_s += $item->total;
                          $total_shiping_vat += $item->vat;
                          $total_shiping_amount += $item->amount;
                      ?>
                    @endforeach
                  @endif
                @endif
              @endforeach
              <tr id="addRow">
                <td class="col-xs-1"> @lang('home.Total Amount') : <span id="shipping_total_amount">{{ $total_shiping_amount  }} </span> </td>
                <td class="col-xs-1"> @lang('home.Total Vat') : <span id="shipping_total_vat_s">{{ $total_shiping_vat }}</span>   </td>
                <td class="col-xs-1"> @lang('home.Total') : <span id="shipping_total_s">{{ $total_shiping_s }}</span>  </td>
                <td @if($currency_global_id == null) class="col-xs-1 ship_curr hide" @else class="col-xs-1 ship_curr  " @endif > @lang('Total Amount Currency') : <span id="shipping_amount_curr"></span> </td>
                <td @if($currency_global_id == null) class="col-xs-1 ship_curr hide" @else class="col-xs-1 ship_curr  " @endif > @lang('Total Vat Currency') : <span id="shipping_vat_curr"></span></td>
                <td @if($currency_global_id == null) class="col-xs-1 ship_curr hide" @else class="col-xs-1 ship_curr  " @endif > @lang('Total - Currency') : <span id="shipping_total_curr"></span></td>
                <td class="col-xs-1"> </td>
                <td class="col-xs-1"> </td>
                <td @if($currency_global_id == null) class="col-xs-3 ship_curr hide" @else class="col-xs-3 ship_curr" @endif  style="width:300px;" > </td>
                <td class="col-xs-1"> </td>
                <td class="col-xs-1"> </td>
                </tr>
            </tbody>                 
          </table>
        </div>
    </div>
  </div>
    
    <div class="container">
      
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              {!! Form::label('document_expense', __('purchase.attach_document') . ':') !!}
              {!! Form::file('document_expense[]', ['multiple','id' => 'upload_document[]', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
              <p class="help-block">
                @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                @includeIf('components.document_help_text')
              </p>
            </div>
          </div>
          <div class="col-sm-6">
         
            @foreach ($purchase->additional_shipings as $ship)
                 <ul class="imgs_ul">
                  
                  @foreach ($ship->document as $doc)
                     <li>
                          <?php $ar =  explode('.',$doc)  ?>
                          <a onclick="$(this).parent().remove()" class="close_item">X</a>
                          @if ($ar[1]  == 'pdf')
                          <a href="{{ URL::to($doc) }}" target="_blank">
                            <i class="fa fa-eye show_eye"></i>
                          </a>
                            <iframe  src="{{ URL::to($doc) }}" frameborder="0" width="100" height="100"></iframe>
                          @else
                            <a href="{{ URL::to($doc) }}" target="_blank">
                              <img src="{{ URL::to($doc) }}" class="img-thumbnail"> 
                            </a>
                            @endif
                         <input type="hidden" name="old_document[]" value="{{ $doc }}">
                     </li>
                     
                 @endforeach
                 </ul>
                 
                 
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @if(isset($check))
    <input type="text" hidden id="check_type" value="{{$check}}">
    @endif
      
      
      <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.save' )</button>
  </div>
</div>

</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
@section('child_script')
<script type="text/javascript">
    update_between_currency();
    $('.add_currency_id_amount').change(function(){
      update_shipping_row();
    });
    $('.add_currency_id').change(function(){
			var id = $(this).val();
			if(id == ""){
        $(".add_currency_id_amount").val("");
				// $(".check_dep_curr").addClass("hide" );
				// $('input[name="dis_currency"]').prop('checked', false);
				// $('#depending_curr').prop('checked', false);
				// discount_cal_amount2() ;
        //         os_total_sub();
        //         os_grand();
        update_shipping_row();
			}else{
				// $(".ship_curr").removeClass("hide" );
				// $(".check_dep_curr").removeClass("hide" );
				// $('input[name="dis_currency"]').prop('checked', true);
				// $('#depending_curr').prop('checked', true);
				$.ajax({
					url:"/symbol/amount/"+id,
					dataType: 'html',
					success:function(data){
						var object  = JSON.parse(data);
						$(".add_currency_id_amount").val(object.amount);
   						// $(".cur_symbol").html( @json(__('purchase.sub_total_amount')) + " " + object.symbol + " : "  );
               update_shipping_row();
            },
				});	 
			}
		})
    update_shipping_row_first();
    function formatRows(main, prefer, common) {
   
        if($('.currency_id').val() == ""){
          return '<tr>' +
              '<td class="col-xs-1">{{ Form::select('shipping_contact_id[]',$contacts,null,['class'=>'form-control select_edit shipping-select2 supplier','placeholder'=>trans('home.please account')]) }}</td>' +
              '<td class="col-xs-1">{{ Form::number('shipping_amount[]',0,['class'=>'form-control  shipping_amount_s','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1">{{ Form::number('shipping_vat[]',0,['class'=>'form-control shipping_tax','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1">{{ Form::number('shipping_total[]',0,['class'=>'form-control shipping_total','required','step'=>'any','min'=>0,'readOnly']) }}</td>' +
              '<td class="col-xs-1 ship_curr hide">{{ Form::number('shipping_amount_curr[]',0,['class'=>'form-control  shipping_amount_curr','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1 ship_curr hide">{{ Form::number('shipping_vat_curr[]',0,['class'=>'form-control shipping_tax_curr','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1 ship_curr hide">{{ Form::number('shipping_total_curr[]',0,['class'=>'form-control shipping_total_curr','required','step'=>'any','min'=>0,'readOnly']) }}</td>' +
              '<td class="col-xs-1">{{ Form::select('shipping_account_id[]',$expenses,null,['class'=>'form-control select_edit shipping-select2 ','required']) }}</td>'+
              '<td class="col-xs-1">{{ Form::select('shipping_cost_center_id[]',$cost_center,null,['class'=>'form-control select_edit shipping-select2 cost-center','placeholder'=>trans('home.please account')]) }}</td>' +
              '<td class="col-xs-1">{{ Form::text('shiping_text[]',null,['class'=>'form-control ' ]) }}</td>' +
              '<td class="col-xs-1 ship_curr currency_check hide" style="width:300px;"><div class="col-md-12"><div class="form-group"><div class="multi-input text-center"> '+ 
               '{!! Form::select('line_currency_id[]', $currencies, null, ['class' => 'form-control width-100  line_currency_id  select2' ,'placeholder' => __('messages.please_select') ]); !!}'+
               '{!! Form::text('line_currency_id_amount[]', null, ['class' => 'form-control  pull-right width-100 line_currency_id_amount'  ,  ]); !!}'+
              '</div>'+
              '</div>'+
              '</div></td>' +
              '<td class="col-xs-1">{{ Form::date('shiping_date[]',date('Y-m-d'),['class'=>'form-control ','id'=>'shipping_date','required','max'=>date('Y-m-d')]) }}</td>' +
              '<td class="col-xs-1 text-center"><a href="#" onClick="deleteRow(this)"><i class="fas fa-trash" aria-hidden="true"></a></td></tr>';
        }else{
          return '<tr>' +
             '<td class="col-xs-1">{{ Form::select('shipping_contact_id[]',$contacts,null,['class'=>'form-control select_edit shipping-select2 supplier','placeholder'=>trans('home.please account')]) }}</td>' +
              '<td class="col-xs-1">{{ Form::number('shipping_amount[]',0,['class'=>'form-control  shipping_amount_s','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1">{{ Form::number('shipping_vat[]',0,['class'=>'form-control shipping_tax','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1">{{ Form::number('shipping_total[]',0,['class'=>'form-control shipping_total','required','step'=>'any','min'=>0,'readOnly']) }}</td>' +
              '<td class="col-xs-1 ship_curr ">{{ Form::number('shipping_amount_curr[]',0,['class'=>'form-control  shipping_amount_curr','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1 ship_curr ">{{ Form::number('shipping_vat_curr[]',0,['class'=>'form-control shipping_tax_curr','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1 ship_curr ">{{ Form::number('shipping_total_curr[]',0,['class'=>'form-control shipping_total_curr','required','step'=>'any','min'=>0,'readOnly']) }}</td>' +
              '<td class="col-xs-1">{{ Form::select('shipping_account_id[]',$expenses,null,['class'=>'form-control select_edit shipping-select2 ','required']) }}</td>'+
              '<td class="col-xs-1">{{ Form::select('shipping_cost_center_id[]',$cost_center,null,['class'=>'form-control select_edit shipping-select2 cost-center','placeholder'=>trans('home.please account')]) }}</td>' +
              '<td class="col-xs-1">{{ Form::text('shiping_text[]',null,['class'=>'form-control ' ]) }}</td>' +
              '<td class="col-xs-1 ship_curr currency_check" style="width:300px;"><div class="col-md-12"><div class="form-group"><div class="multi-input text-center">  '+ 
               '{!! Form::select('line_currency_id[]', $currencies, null, ['class' => 'form-control  width-100 line_currency_id  select2' , 'placeholder' => __('messages.please_select') ]); !!}'+
               '{!! Form::text('line_currency_id_amount[]', null, ['class' => 'form-control  pull-right width-100 line_currency_id_amount' ,  ]); !!}'+
              '</div>'+
              '</div>'+
              '</div></td>' +
              '<td class="col-xs-1">{{ Form::date('shiping_date[]',date('Y-m-d'),['class'=>'form-control ','id'=>'shipping_date','required','max'=>date('Y-m-d')]) }}</td>' +
              '<td class="col-xs-1 text-center"><a href="#" onClick="deleteRow(this)"><i class="fas fa-trash" aria-hidden="true"></a></td></tr>';
        }       
      };
    function deleteRow(trash) {
        var supplier_id =  $('#supplier_id option:selected').val();
        var sup_id      =  $(trash).closest('tr').find('.supplier option:selected').val();
        if(sup_id == supplier_id || sup_id == "" ){
          var minus = $('input[name="ADD_SHIP"]').val() - $(trash).closest('tr').find(".shipping_total").val();
          $('input[name="ADD_SHIP"]').val(minus);
        }else{
          var minus = $('input[name="ADD_SHIP_"]').val() - $(trash).closest('tr').find(".shipping_total").val();
          $('input[name="ADD_SHIP_"]').val(minus);
        }
        $(trash).closest('tr').remove();
        update_shipping();
        total_bill();
    };

    function addRow() {
      var main = $('.addMain').val();
      var preferred = $('.addPrefer').val();
      var common = $('.addCommon').val();
      $(formatRows(main,preferred,common)).insertBefore('#addRow');
      $('.select_edit').select2();
        update_shipping();
        update_between_currency();
    }

    function update_shipping () {
      $('.shipping_tax, .shipping_amount_s, #supplier_id, .supplier, .shipping_amount_curr').change(function(){

        total_bill();
      })
      
    }
    function total_bill(){
        var total_amount_shiping       = 0;
        var total_vat_shiping          = 0;
        var total_shiping              = 0;
        var supplier_pay               = 0;
        var cost_pay                   = 0;
        var total_amount_shiping_curr  = 0;
        var total_tax_shiping_curr     = 0;
        var total_tot_shiping_curr     = 0;
        var supplier_pay_curr          = 0;
        var cost_pay_curr              = 0;
            var supplier_id =  $('#supplier_id option:selected').val();
            $('.shipping_amount_s').each(function(){
                var el                = $(this).parent().parent();
                var sup_id            = el.children().find('.supplier option:selected').val();
                var contact_id        = $('#contact_id').val();
                var amount_curr       =  parseFloat(el.children().find('.shipping_amount_curr').val());

                var amount            = parseFloat($(this).val()) ;
                var tax               = parseFloat(el.children().find('.shipping_tax').val()) ;
                var tax_curr          = parseFloat(el.children().find('.shipping_tax_curr').val()) ;

                total_vat_shiping    += tax;
                var total_s           = amount+tax; 
                var total_curr_s      = amount_curr+tax_curr; 

                total_amount_shiping      += amount;
                total_amount_shiping_curr += amount_curr ;

                total_shiping             += total_s;
                total_tot_shiping_curr    += total_curr_s  ;

                if ((sup_id == supplier_id || sup_id == "" ) && supplier_id != ""   ) {
                    supplier_pay            += total_s;
                    supplier_pay_curr       += total_curr_s;

                }else{
                    cost_pay                += total_s;
                    cost_pay_curr           += total_curr_s;

                }
               
              el.children().find('.shipping_total').val(total_s.toFixed(3));
              el.children().find('.shipping_total_curr').val(total_curr_s.toFixed(2));

            })
            $('#shipping_amount_curr').html(total_amount_shiping_curr.toFixed(2));
            $('#shipping_vat_curr').html(total_tax_shiping_curr.toFixed(2));
            $('#shipping_total_curr').html(total_tot_shiping_curr.toFixed(2));
            $('#shipping_total_amount').text(total_amount_shiping.toFixed(3));
            $('input[name="ADD_SHIP"]').val(supplier_pay);
            $('input[name="ADD_SHIP_"]').val(cost_pay);
            $('#shipping_total_vat_s').text(total_vat_shiping.toFixed(3));
            $('#shipping_total_s').text(total_shiping.toFixed(3));
        
          
            var total_items =  $("#total_subtotal_input_id").val();       
            var total       =  $("#grand_total_hidden").val();
            
            if($("#total_finals_").val() != null){
              var total       =  $("#total_finals_").val()   ;       
                  
            }  
            var total_curr     =  $("#grand_total_cur_hidden").val(); 
            var sub_total_curr = ($("#total_subtotal_cur_edit").html() != null)?$("#total_subtotal_cur_edit").html():0;
            var discount_curr  = parseFloat($("#discount_calculated_amount_cur").text()).toFixed(2); 
            var tax_calculated_amount_curr  = parseFloat($("#tax_calculated_amount_curr").text()).toFixed(2); 

            var ship        =  $("#total_ship_").val();     
            var ship_       =  $("#total_ship_c").val();

            // console.log(ship + "  ::: ship");  

            currancy = $(".currency_id_amount").val();
            if(currancy != "" && currancy != 0){
                  $("#total_final_i_curr").html((parseFloat(sub_total_curr) - parseFloat(discount_curr) + parseFloat(tax_calculated_amount_curr)).toFixed(2));       
                  $("#grand_total_cur").html((parseFloat(sub_total_curr) - parseFloat(discount_curr) + parseFloat(tax_calculated_amount_curr) + parseFloat(supplier_pay_curr)).toFixed(2));       
                  $("#total_final_curr").html((parseFloat(sub_total_curr) - parseFloat(discount_curr) + parseFloat(tax_calculated_amount_curr) + parseFloat(supplier_pay_curr) + parseFloat(cost_pay_curr)).toFixed(2));        
            }        
            $("#total_final_i").html((parseFloat(total)).toFixed(3));      
            $("#total_final_hidden_").val((parseFloat(total) + parseFloat(supplier_pay)).toFixed(3));       
            $("#grand_total").html((parseFloat(total) + parseFloat(supplier_pay)).toFixed(3));       
            $("#total_final_").html((parseFloat(total) + parseFloat(supplier_pay) ).toFixed(3)); 
            if($("#total_finals_").val() == null){
                $("#total_final_").html((parseFloat(total) + parseFloat(supplier_pay)   + parseFloat(cost_pay) ).toFixed(3)); 
            }
            $("#total_final_x").html((parseFloat(total) + parseFloat(supplier_pay) + parseFloat(cost_pay)).toFixed(3));       
            $("#grand_total2").html((parseFloat(total) + parseFloat(supplier_pay) + parseFloat(cost_pay)).toFixed(3)); 
            $("#grand_total_items").html((parseFloat(total_items) + parseFloat(supplier_pay)).toFixed(3));       
            $("#final_total_hidden_items").val((parseFloat(total_items) + parseFloat(supplier_pay)).toFixed(3));       
            $("#total_final_items").html((parseFloat(total_items) + parseFloat(supplier_pay) + parseFloat(cost_pay)).toFixed(3));
            $("#total_final_items_").val((parseFloat(total_items) + parseFloat(supplier_pay) + parseFloat(cost_pay)).toFixed(3));
            $("#payment_due_").html(parseFloat($("#grand_total2").html()) - parseFloat($(".payment-amount").val()));       
            
            if(ship ==""){
              $("#grand_total").html(parseFloat(total));  
            }
        
    }

    function update_between_currency(){
         
        currancy        = $(".currency_id_amount").val();
        $('.shipping_amount_s').on("change",function(){
          el = $(this);
          e  = $(this).val();
          if(el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val() != "" &&  el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val() >= 0){
            currancy        = el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val();
          }else if($(".add_currency_id_amount").val() != "" && $(".add_currency_id_amount").val() >= 0){
            currancy        = $(".add_currency_id_amount").val();
          } 
          if(currancy != "" && currancy != 0){
              var orginal_amount   = parseFloat(e) ;
              var currency_amount  = parseFloat(orginal_amount / currancy);
              var second_amount    = $(this).parent().parent().find(".shipping_amount_curr");
              /** set it value*/second_amount.val(currency_amount.toFixed(2));
              total_bill();
          }  
        });
        $('.shipping_amount_curr').on("change",function(){
          el = $(this);
          e  = $(this).val();
          if(el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val() != "" &&  el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val() >= 0){
            currancy        = el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val();
          }else if($(".add_currency_id_amount").val() != "" && $(".add_currency_id_amount").val() >= 0){
            currancy        = $(".add_currency_id_amount").val();
          } 
          if(currancy != "" && currancy != 0){
              var orginal_amount   = parseFloat(e) ;
              var currency_amount  = parseFloat(orginal_amount * currancy);
              var second_amount    = $(this).parent().parent().find(".shipping_amount_s");
              /** set it value*/second_amount.val(currency_amount.toFixed(2));
              total_bill();
          }  
        });
        $('.shipping_tax_curr').on("change",function(){
          el = $(this);
          e  = $(this).val();
          if(el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val() != "" &&  el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val() >= 0){
            currancy        = el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val();
          }else if($(".add_currency_id_amount").val() != "" && $(".add_currency_id_amount").val() >= 0){
            currancy        = $(".add_currency_id_amount").val();
          } 
          if(currancy != "" && currancy != 0){
              var orginal_amount   = parseFloat(e) ;
              var currency_amount  = parseFloat(orginal_amount * currancy);
              var second_amount    = $(this).parent().parent().find(".shipping_tax");
              /** set it value*/second_amount.val(currency_amount.toFixed(2));
              total_bill();
          }  
        });
        $('.shipping_tax').on("change",function(){
          e = $(this).val();
          if(currancy != "" && currancy != 0){
              var orginal_amount   = parseFloat(e) ;
              var currency_amount  = parseFloat(orginal_amount / currancy);
              var second_amount    = $(this).parent().parent().find(".shipping_tax_curr");
              /** set it value*/second_amount.val(currency_amount.toFixed(2));
              total_bill();
          }  
        });
        $('.line_currency_id_amount').each(function(){
          var e  = $(this);
          e.on("change",function(){
            update_shipping_row();
          });
        });
        $('.line_currency_id').each(function(){
          var e  = $(this);
          
          e.on("change",function(){
            var ele    = $(this);
            var parent = $(this).parent().parent();           
            var id     = ele.val();
            if(id == ""){
              parent.children().find(".line_currency_id_amount").val("");
              update_shipping_row();
            }else{
              $.ajax({
                url:"/symbol/amount/"+id,
                dataType: 'html',
                success:function(data){
                  var object  = JSON.parse(data);
                  parent.children().find(".line_currency_id_amount").val(object.amount);
                  update_shipping_row();  
                },
              });	 
            }
          });
        })
    }

    function update_shipping_row(){
      currancy = $(".currency_id_amount").val();
      $('.shipping_amount_curr').each(function(){
          el = $(this);
            e  = $(this).val();
            if(el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val() != "" &&  el.closest("tbody").find(".currency_check .form-group .line_currency_id_amount").val() >= 0){
              currancy        = el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val();
            }else if($(".add_currency_id_amount").val() != "" && $(".add_currency_id_amount").val() >= 0){
              currancy        = $(".add_currency_id_amount").val();
            } 
            if(currancy != "" && currancy != 0){
                var orginal_amount   = parseFloat(e) ;
                var currency_amount  = parseFloat(orginal_amount * currancy);
                var second_amount    = $(this).parent().parent().find(".shipping_amount_s");
                /** set it value*/second_amount.val(currency_amount.toFixed(3));
                total_bill();
            }  
        });
        $('.shipping_tax_curr').each(function(){
            e = $(this).val();
            if(currancy != "" && currancy != 0){
                var orginal_amount   = parseFloat(e) ;
                var currency_amount  = parseFloat(orginal_amount * currancy);
                var second_amount    = $(this).parent().parent().find(".shipping_tax");
                /** set it value*/second_amount.val(currency_amount.toFixed(3));
                total_bill();
            }  
        });
    }
    function update_shipping_row_first(){
      currancy = $(".currency_id_amount").val();
      $('.shipping_amount_s').each(function(){
          el = $(this);
            e  = $(this).val();
            if(el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val() != "" &&  el.closest("tbody").find(".currency_check .form-group .line_currency_id_amount").val() >= 0){
              currancy        = el.closest("tr").find(".currency_check .form-group .line_currency_id_amount").val();
            }else if($(".add_currency_id_amount").val() != "" && $(".add_currency_id_amount").val() >= 0){
              currancy        = $(".add_currency_id_amount").val();
            } 
            if(currancy != "" && currancy != 0){
                var orginal_amount   = parseFloat(e) ;
                var currency_amount  = parseFloat(orginal_amount / currancy);
                var second_amount    = $(this).parent().parent().find(".shipping_amount_curr");
                /** set it value*/second_amount.val(currency_amount.toFixed(2));
                total_bill();
            }  
        });
        $('.shipping_taxr').each(function(){
            e = $(this).val();
            if(currancy != "" && currancy != 0){
                var orginal_amount   = parseFloat(e) ;
                var currency_amount  = parseFloat(orginal_amount / currancy);
                var second_amount    = $(this).parent().parent().find(".shipping_tax_cur");
                /** set it value*/second_amount.val(currency_amount.toFixed(2));
                total_bill();
            }  
        });
    }

    function correct(){
          date   =  new Date($("#shipping_date").val());
        
          if(date.getFullYear() <= "2000" || date.getFullYear() > "2099"){
            return false ;
          }
        
        return true;
    }
     

   @if(!isset($check))
   update_shipping();total_bill();
		$(document).on("change","#shipping_date",function(){
        date   =  new Date($(this).val());
        date2  =  new Date();
			  result =  correct();
       
        if(result == false){
  
            $(this).css({
                "-webkit-box-shadow":"0px 0px 10px  red",
                "-moz-box-shadow":"0px 0px 10px  red",
                "-o-box-shadow":"0px 0px 10px  red",
                "box-shadow":"0px 0px 10px  red",
                "border":"1px solid red",
                "transaction":".3s ease-in", 
            })
        }else  {
      
                $("input[type='date']").css({
                    "-webkit-box-shadow":"0px 0px 10px  transparent",
                    "-moz-box-shadow":"0px 0px 10px  transparent",
                    "-o-box-shadow":"0px 0px 10px  transparent",
                    "box-shadow":"0px 0px 10px  transparent",
                    "border":"1px solid #f1f1f1",
                    "transaction":".3s ease-in", 
                })
        }
        // console.log(date.getFullYear() + " -----  " + date2.getFullYear());
    });
  @endif

</script>
@endsection
