<div class="modal-dialog " style="width:80%"  role="document">
  <div class="modal-content" >

    {!! Form::open(['url' => action('TransactionPaymentController@make'), 'method' => 'post', 'id' => 'transaction_payment_add_form', 'files' => true ]) !!}
    {!! Form::hidden('transaction_id', $transaction->id); !!}
    @if(!empty($transaction->location))
      {!! Form::hidden('default_payment_accounts', $transaction->location->default_payment_accounts, ['id' => 'default_payment_accounts']); !!}
    @endif
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'purchase.add_recieved' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
      @if(!empty($transaction->contact))
        <div class="col-md-4">
          <div class="well">
            <strong>
            @if(in_array($transaction->type, ['purchase', 'purchase_return']))
              @lang('purchase.supplier') 
            @elseif(in_array($transaction->type, ['sell', 'sell_return']))
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
          @if(in_array($transaction->type, ['sell', 'sell_return']))
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
            <strong>@lang('sale.total_amount'): </strong><span class="display_currency" data-currency_symbol="true">{{  $count }}</span><br>
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
        <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('location_id', __('lang_v1.loaction_addres').':*') !!}
              {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'location_id']); !!}
            </div>
          </div>
        <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('store_id', __('warehouse.nameW').':*') !!}
              {!! Form::select('store_id', $Warehouse_list, null, ['class' => 'form-control select2', 'name' => "store_id", 'placeholder' => __('messages.please_select'), 'required', 'id' => 'store_id']); !!}
            </div>
          </div>
      </div>
      

        <div class="row">
          <div class="col-md-12">
            @if(!empty($transaction->contact))
              <strong>@lang('lang_v1.need_balance_items'):</strong> <span class="display_currency" data-currency_symbol="true"></span>
            @endif
          </div>
        </div>

        

      <div class="row">
        <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-recieve" >
                    <tr style="background:#f1f1f1;">
                      <th>@lang('messages.date')</th>
                      <th>@lang('product.product_name')</th>
                      <th>@lang('purchase.amount')</th>
                      <th>@lang('purchase.delivery_status')</th>
                      <th>@lang('warehouse.nameW')</th>
                      <th>@lang('purchase.payment_note')</th>
                      {{-- <th class="no-print">@lang('messages.actions')</th> --}}
                    </tr>
                    @php
                      $total = 0;
                    @endphp
                    @forelse ($purchcaseline as $payment)

                      @php
                            $total = $total + $payment->quantity;
                            $product_name = "";
                            $product_id_src = "";
                            $product_id_str = "";
                            $product_id_unit = "";
                            $product_id_qty = $payment->quantity;
                            $product_id_unit_value = "";
                            $counter = 1; 

                            foreach($product_list as $product_l){
                              if($payment->product_id == $counter ){
                                $product_name = $product_l;
                                $product_id_src = $payment->product_id;
                                $product_id_str = $payment->store_id;
                                foreach($product as $rd){
                                  if($rd->name == $product_name){
                                    $product_id_unit = $rd->unit_id;
                                    foreach($unit as $un){
                                        if($un->id == $product_id_unit){
                                          $product_id_unit_value = $un->actual_name;
                                        };
                                      }
                                  };
                                }
                              

                              }
                              $counter = $counter + 1 ;
                              
                            }
                            $Warehouse_name = "";
                            $counter_1 = 1; 
                            foreach($Warehouse_list as $Warehouse_l){
                              if($payment->store_id == $counter_1 ){
                                $Warehouse_name = $Warehouse_l;
                              }
                              $counter_1 = $counter_1 + 1 ;
                              
                              // dd($Warehouse_list);
                            }

                            // dd($transaction);
                      @endphp 
                        <tr style="border:1px solid #f1f1f1;">
                          <td>{{$payment->created_at}}</td>
                          <td>{{$product_name}}</td>
                          <td>{{$payment->quantity}}</td>
                          <td>{{$transaction->status}}</td>
                          <td>{{$Warehouse_name}}</td>
                          <td>{!! Form::text('product_id_src', $product_id_src ,["hidden",'id' => 'product_id_src']); !!}</td>
                          <td hidden>{!! Form::text('product_id_str', $product_id_str ,["hidden",'id' => 'product_id_str']); !!}</td>
                          <td hidden>{!! Form::text('product_id_unit_value', $product_id_unit ,["hidden",'id' => 'product_id_unit_value']); !!}</td>
                          <td hidden>{!! Form::text('product_id_qty', $product_id_qty ,["hidden",'id' => 'product_id_qty']); !!}</td>
                        </tr>
                        
                        
                    @empty
                    
                    @endforelse
                    <tfoot>
                      <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                        <td class="text-center " colspan="2"><strong>@lang('sale.total'):</strong></td>
                        <td>{{$total}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                   
                      </tr>
                   </tfoot>
                    </table>
                </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-12">
          @if(!empty($transaction->contact))
            <strong>@lang('lang_v1.old_recieved'):</strong> <span class="display_currency" data-currency_symbol="true"></span>
          @endif
        </div>
    </div>

      <div class="row">
        <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-recieve" >
                    <tr style="background:#f1f1f1;">
                      <th>@lang('messages.date')</th>
                      <th>@lang('product.product_name')</th>
                      <th>@lang('product.unit')</th>
                      <th>@lang('purchase.amount_total')</th>
                      <th>@lang('purchase.amount_current')</th>
                      <th>@lang('purchase.amount_remain')</th>
                      <th>@lang('warehouse.nameW')</th>
                      <th>@lang('purchase.payment_note')</th>
                      {{-- <th class="no-print">@lang('messages.actions')</th> --}}
                    </tr>
                    @php
                      $total = 0;
                    @endphp
                    @forelse ($RecievedPrevious as $Recieved)

                    @php
                        
                    @endphp 
                        <tr style="border:1px solid #f1f1f1;">
                          <td>{{$payment->created_at}}</td>
                          <td>{{$product_name}}</td>
                          <td>{{$payment->quantity}}</td>
                          <td>{{$transaction->status}}</td>
                          <td>{{$Warehouse_name}}</td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                        
                    @empty
                    
                    @endforelse
                    <tfoot>
                      <tr class="bg-gray  font-17 footer-total" style="border:1px solid #f1f1f1;  ">
                        <td class="text-center " colspan="2"><strong>@lang('sale.total'):</strong></td>
                        <td>{{$total}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                   
                      </tr>
                   </tfoot>
                    </table>
                </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          @if(!empty($transaction->contact))
            <strong>@lang('lang_v1.recieved_item'):</strong> <span class="display_currency" data-currency_symbol="true"></span>
            {!! Form::hidden('advance_balance', $transaction->contact->balance, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
          @endif
        </div>
    </div>

     

      @if(count($business_locations) == 1)
				@php 
					$default_location = current(array_keys($business_locations->toArray()));
					$search_disable = false; 
				@endphp
			@else
				@php $default_location = null;
				$search_disable = true;
				@endphp
			@endif

      @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
          <div class="col-sm-8 col-sm-offset-2">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-search"></i>
                </span>
                {!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'disabled' => $search_disable]); !!}
              </div>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <button tabindex="-1" type="button" class="btn btn-link btn-modal"data-href="{{action('ProductController@quickAdd')}}" 
                  data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
            </div>
          </div>
        </div>
        @php
          $hide_tax = '';
          if( session()->get('business.enable_inline_tax') == 0){
            $hide_tax = 'hide';
          }
        @endphp
        <div class="row">
          <div class="col-sm-12">
            <div class="table-responsive">
              <table class="table table-condensed table-bordered table-th-green text-center table-striped" id="purchase_entry_table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>@lang( 'product.product_name' )</th>
                    <th>@lang( 'purchase.purchase_quantity' )</th>
                    <th>@lang( 'lang_v1.unit_cost_before_discount' )</th>
                    <th>@lang( 'lang_v1.discount_percent' )</th>
                    <th>@lang( 'purchase.unit_cost_before_tax' )</th>
                    <th class="{{$hide_tax}}">@lang( 'purchase.subtotal_before_tax' )</th>
                    <th class="{{$hide_tax}}">@lang( 'purchase.product_tax' )</th>
                    <th class="{{$hide_tax}}">@lang( 'purchase.net_cost' )</th>
                    <th>@lang( 'purchase.line_total' )</th>
                    <th class="@if(!session('business.enable_editing_product_from_purchase')) hide @endif">
                      @lang( 'lang_v1.profit_margin' )
                    </th>
                    <th>
                      @lang( 'purchase.unit_selling_price' )
                      <small>(@lang('product.inc_of_tax'))</small>
                    </th>
                    @if(session('business.enable_lot_number'))
                      <th>
                        @lang('lang_v1.lot_number')
                      </th>
                    @endif
                    @if(session('business.enable_product_expiry'))
                      <th>
                        @lang('product.mfg_date') / @lang('product.exp_date')
                      </th>
                    @endif
                    <th><i class="fa fa-trash" aria-hidden="true"></i></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <hr/>
            <div class="pull-right col-md-5">
              <table class="pull-right col-md-12">
                <tr>
                  <th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
                  <td class="col-md-5 text-left">
                    <span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
                  </td>
                </tr>
                <tr class="hide">
                  <th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
                  <td class="col-md-5 text-left">
                    <span id="total_st_before_tax" class="display_currency"></span>
                    <input type="hidden" id="st_before_tax_input" value=0>
                  </td>
                </tr>
                <tr>
                  <th class="col-md-7 text-right">@lang( 'purchase.net_total_amount' ):</th>
                  <td class="col-md-5 text-left">
                    <span id="total_subtotal" class="display_currency"></span>
                    <!-- This is total before purchase tax-->
                    <input type="hidden" id="total_subtotal_input" value=0  name="total_before_tax">
                  </td>
                </tr>
              </table>
            </div>

            <input type="hidden" id="row_count" value="0">
          </div>
        </div>
      @endcomponent


      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
              {!! Form::label('additional_notes', __('project::lang.notes') . ':') !!}
              {!! Form::textarea('additional_notes', $transaction->additional_notes, ['class' => 'form-control ', 'rows' => '3']); !!}
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


@section('javascript')
	<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/producte.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready( function(){
      		__page_leave_confirmation('#add_purchase_form');
      		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
    	});
    	$(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
		    var default_accounts = $('select#location_id').length ? 
		                $('select#location_id')
		                .find(':selected')
		                .data('default_payment_accounts') : [];
		    var payment_types_dropdown = $('.payment_types_dropdown');
		    var payment_type = payment_types_dropdown.val();
		    var payment_row = payment_types_dropdown.closest('.payment_row');
	        var row_index = payment_row.find('.payment_row_index').val();

	        var account_dropdown = payment_row.find('select#account_' + row_index);
		    if (payment_type && payment_type != 'advance') {
		        var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
		            default_accounts[payment_type]['account'] : '';
		        if (account_dropdown.length && default_accounts) {
		            account_dropdown.val(default_account);
		            account_dropdown.change();
		        }
		    }

		    if (payment_type == 'advance') {
		        if (account_dropdown) {
		            account_dropdown.prop('disabled', true);
		            account_dropdown.closest('.form-group').addClass('hide');
		        }
		    } else {
		        if (account_dropdown) {
		            account_dropdown.prop('disabled', false); 
		            account_dropdown.closest('.form-group').removeClass('hide');
		        }    
		    }
		});
	</script>
	@include('purchase.partials.keyboard_shortcuts')
@endsection
  
{{-- <script src="{{ asset('js/stock_transfers.js?v=' . $asset_v) }}"></script> --}}

  
{{-- <script type="text/javascript">
  $(document).ready(function() {
    //Add products
    if ($('#search_product_for_srock_adjustment').length > 0) {
        //Add Product
        $('#search_product_for_srock_adjustment')
            .autocomplete({
              source: function(request, response) {
                
                $.getJSON(
                '/products/list',
                { location_id: $('#location_id').val(), term: request.term },
                response
                );
              
                },
                minLength: 2,
                response: function(event, ui) {
                  if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                    if (ui.item.qty_available > 0 && ui.item.enable_stock == 1 || ui.item.qty_available == null && ui.item.enable_stock == 1) {
                      // alert(JSON.stringify(ui.content[0].enable_stock));
                            $(this)
                            .data('ui-autocomplete')
                            ._trigger('select', 'autocompleteselect', ui);
                            $(this).autocomplete('close');
                          }
                    } else if (ui.content.length == 0) {
                          swal(LANG.no_products_found);
                    }
                },
                focus: function(event, ui) {
                    if (ui.item.qty_available <= 0) {
                        // return false;
                    }
                },
                select: function(event, ui) {
                  if (ui.item.qty_available >= 0 || ui.item.qty_available == null ) {
                    $(this).val(null);
                    stock_transfer_product_row(ui.item.variation_id);
                    } else {
                        alert(LANG.out_of_stock);
                    }
                },
            })
            .autocomplete('instance')._renderItem = function(ul, item) {
            
                var string = '<div>' + item.name;
                if (item.type == 'variable') {
                    string += '-' + item.variation;
                }
                string += ' (' + item.sub_sku + ') </div>';
                return $('<li>')
                    .append(string)
                    .appendTo(ul);
            
        };
    }

        $('select#location_id').change(function() {
            if ($(this).val()) {
                $('#search_product_for_srock_adjustment').removeAttr('disabled');
            } else {
                $('#search_product_for_srock_adjustment').attr('disabled', 'disabled');
            }
            $('table#stock_adjustment_product_table tbody').html('');
            $('#product_row_index').val(0);
            update_table_total();
        });

        $(document).on('change', 'input.product_quantity', function() {
            update_table_row($(this).closest('tr'));
        });
        $(document).on('change', 'input.product_unit_price', function() {
            update_table_row($(this).closest('tr'));
        });

        $(document).on('click', '.remove_product_row', function() {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    $(this)
                        .closest('tr')
                        .remove();
                    update_table_total();
                }
            });
        });

        //Date picker
        $('#transaction_date').datetimepicker({
            format: moment_date_format + ' ' + moment_time_format,
            ignoreReadonly: true,
        });

        jQuery.validator.addMethod(
            'notEqual',
            function(value, element, param) {
                return this.optional(element) || value != param;
            },
            'Please select different location'
        );

        $('form#stock_transfer_form').validate({
            rules: {
                transfer_location_id: {
                    notEqual: function() {
                        return $('select#location_id').val();
                    },
                },
            },
        });
        $('#save_stock_transfer').click(function(e) {
            e.preventDefault();

            if ($('table#stock_adjustment_product_table tbody').find('.product_row').length <= 0) {
                toastr.warning(LANG.no_products_added);
                return false;
            }
            if ($('form#stock_transfer_form').valid()) {
                $('form#stock_transfer_form').submit();
            } else {
                return false;
            }
        });

        stock_transfer_table = $('#stock_transfer_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: '/stock-transfers',
            columnDefs: [
                {
                    targets: 8,
                    orderable: false,
                    searchable: false,
                },
            ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'location_from', name: 'l1.name' },
                { data: 'location_to', name: 'l2.name' },
                { data: 'status', name: 'status' },
                { data: 'shipping_charges', name: 'shipping_charges' },
                { data: 'final_total', name: 'final_total' },
                { data: 'additional_notes', name: 'additional_notes' },
                { data: 'action', name: 'action' },
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#stock_transfer_table'));
            },
        });
        var detailRows = [];

        $('#stock_transfer_table tbody').on('click', '.view_stock_transfer', function() {
            var tr = $(this).closest('tr');
            var row = stock_transfer_table.row(tr);
            var idx = $.inArray(tr.attr('id'), detailRows);

            if (row.child.isShown()) {
                $(this)
                    .find('i')
                    .removeClass('fa-eye')
                    .addClass('fa-eye-slash');
                row.child.hide();

                // Remove from the 'open' array
                detailRows.splice(idx, 1);
            } else {
                $(this)
                    .find('i')
                    .removeClass('fa-eye-slash')
                    .addClass('fa-eye');

                row.child(get_stock_transfer_details(row.data())).show();

                // Add to the 'open' array
                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
            }
        });

        // On each draw, loop over the `detailRows` array and show any child rows
        stock_transfer_table.on('draw', function() {
            $.each(detailRows, function(i, id) {
                $('#' + id + ' .view_stock_transfer').trigger('click');
            });
        });

        //Delete Stock Transfer
        $(document).on('click', 'button.delete_stock_transfer', function() {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                toastr.success(result.msg);
                                stock_transfer_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
    });

    function stock_transfer_product_row(variation_id) {
        var row_index = parseInt($('#product_row_index').val());
        var location_id = $('select#location_id').val();
        
        $.ajax({
            method: 'POST',
            url: '/stock-adjustments/get_product_row',
            data: { row_index: row_index, variation_id: variation_id, location_id: location_id },
            dataType: 'html',
            success: function(result) {
              // alert(result);
              $('table#stock_adjustment_product_table tbody').append(result);
              update_table_total();
                $('#product_row_index').val(row_index + 1);
            },
        });
    }

    function update_table_total() {
        var table_total = 0;
        $('table#stock_adjustment_product_table tbody tr').each(function() {
            var this_total = parseFloat(__read_number($(this).find('input.product_line_total')));
            if (this_total) {
                table_total += this_total;
            }
        });
        $('input#total_amount').val(table_total);
        $('span#total_adjustment').text(__number_f(table_total));
    }

    function update_table_row(tr) {
        var quantity = parseFloat(__read_number(tr.find('input.product_quantity')));
        var unit_price = parseFloat(__read_number(tr.find('input.product_unit_price')));
        var row_total = 0;
        if (quantity && unit_price) {
            row_total = quantity * unit_price;
        }
        tr.find('input.product_line_total').val(__number_f(row_total));
        update_table_total();
    }

    function get_stock_transfer_details(rowData) {
        alert(rowData);
        var div = $('<div/>')
            .addClass('loading')
            .text('Loading...');
        $.ajax({
            url: '/stock-transfers/' + rowData.DT_RowId,
            dataType: 'html',
            success: function(data) {
                div.html(data).removeClass('loading');
            },
        });

        return div;
    }

    $(document).on('click', 'a.stock_transfer_status', function(e) {
        e.preventDefault();
        var href = $(this).data('href');
        var status = $(this).data('status');
        $('#update_stock_transfer_status_modal').modal('show');
        $('#update_stock_transfer_status_form').attr('action', href);
        $('#update_stock_transfer_status_form #update_status').val(status);
        $('#update_stock_transfer_status_form #update_status').trigger('change');
    });

    $(document).on('submit', '#update_stock_transfer_status_form', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serialize();

        $.ajax({
            method: 'post',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            beforeSend: function(xhr) {
                __disable_submit_button(form.find('button[type="submit"]'));
            },
            success: function(result) {
                if (result.success == true) {
                    $('div#update_stock_transfer_status_modal').modal('hide');
                    toastr.success(result.msg);
                    stock_transfer_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
                $('#update_stock_transfer_status_form')
                .find('button[type="submit"]')
                .attr('disabled', false);
            },
        });
    });
    $(document).on('shown.bs.modal', '.view_modal', function() {
        __currency_convert_recursively($('.view_modal'));
    });

</script> --}}
	
  
{{-- <script type="text/javascript">
    $(document).ready(function (){
      if ($('#search_product').length > 0) {
        //Add Product
        $('#search_product')
            .autocomplete({
                source: function(request, response) {
                  $.getJSON(
                  '/products/list',
                        { location_id: $('#location_id').val(), term: request.term },
                        response
                        );
                      },
                minLength:2,
                response: function(event, ui) {
                  // alert(JSON.stringify(ui));
                  if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                        if (ui.item.qty_available > 0 && ui.item.enable_stock == 1) {
                            $(this)
                                .data('ui-autocomplete')
                                ._trigger('select', 'autocompleteselect', ui);
                                $(this).autocomplete('close');
                              }
                            } else if (ui.content.length == 0) {
                              swal(LANG.no_products_found);
                            }
                },
                focus: function(event, ui) {
                  if (ui.item.qty_available <= 0) {
                    return false;
                    }
                },
                select: function(event, ui) {
                  if (ui.item.qty_available > 0) {
                    $(this).val(null);
                    // alert(JSON.stringify(ui));
                    stock_transfer_product_row(ui.item.variation_id);
                  } else {
                    alert(LANG.out_of_stock);
                  }
                },
                create: function(){
                    $(this)._renderItem = function(ul, item) {
                        if (item.qty_available <= 0) {
                            var string = '<li class="ui-state-disabled">' + item.name;
                            if (item.type == 'variable') {
                                string += '-' + item.variation;
                            }
                            string += ' (' + item.sub_sku + ') (Out of stock) </li>';
                            return $(string).appendTo(ul);
                        } else if (item.enable_stock != 1) {
                            return ul;
                        } else {
                            var string = '<div>' + item.name;
                            if (item.type == 'variable') {
                                string += '-' + item.variation;
                            }
                            string += ' (' + item.sub_sku + ') </div>';
                            alert();
                            return $('<li>')
                                .append(string)
                                .appendTo(ul);
                        }
                    }
                },
            });
          
          }
          $(document).on('change', 'input.product_quantity', function() {
            update_table_row($(this).closest('tr'));
          });
          $(document).on('change', 'input.product_unit_price', function() {
              update_table_row($(this).closest('tr'));
          });

          $(document).on('click', '.remove_product_row', function() {
              swal({
                  title: LANG.sure,
                  icon: 'warning',
                  buttons: true,
                  dangerMode: true,
              }).then(willDelete => {
                  if (willDelete) {
                      $(this)
                          .closest('tr')
                          .remove();
                      update_table_total();
                  }
              });
          });
          $('form#stock_transfer_form').validate({
              rules: {
                  transfer_location_id: {
                      notEqual: function() {
                          return $('select#location_id').val();
                      },
                  },
              },
          });
          $('#save_stock_transfer').click(function(e) {
            e.preventDefault();

            if ($('table#stock_adjustment_product_table tbody').find('.product_row').length <= 0) {
                toastr.warning(LANG.no_products_added);
                return false;
            }
            if ($('form#stock_transfer_form').valid()) {
                $('form#stock_transfer_form').submit();
            } else {
                return false;
            }
    });
    function stock_transfer_product_row(variation_id) {
      
        var row_index = parseInt($('#product_row_index').val());
        var location_id = $('select#location_id').val();
        $.ajax({
            method: 'POST',
            url: '/stock-adjustments/get_product_row',
            data: { row_index: row_index, variation_id: variation_id, location_id: location_id },
            dataType: 'html',
            success: function(result) {
                $('table#stock_adjustment_product_table tbody').append(result);
                update_table_total();
                $('#product_row_index').val(row_index + 1);
            },
        });
    }

    function update_table_row(tr) {
    var quantity = parseFloat(__read_number(tr.find('input.product_quantity')));
    var unit_price = parseFloat(__read_number(tr.find('input.product_unit_price')));
    var row_total = 0;
    if (quantity && unit_price) {
        row_total = quantity * unit_price;
    }
    tr.find('input.product_line_total').val(__number_f(row_total));
    update_table_total();
    }

    function get_stock_transfer_details(rowData) {
        var div = $('<div/>')
            .addClass('loading')
            .text('Loading...');
        $.ajax({
            url: '/stock-transfers/' + rowData.DT_RowId,
            dataType: 'html',
            success: function(data) {
                div.html(data).removeClass('loading');
            },
        });

        return div;
    }
    $(document).on('shown.bs.modal', '.view_modal', function() {
        __currency_convert_recursively($('.view_modal'));
    });
        });
		// __page_leave_confirmation('#stock_transfer_form');
	</script> --}}


  
