<div class="modal-dialog" role="document" style="width:100%">
      <div class="modal-content">
       <?php 
              $contacts    =  \App\Contact::suppliers();
              $cost_center =  \App\Account::cost_center_list();
              $expenses    =  \App\Account::main('Expenses');
              $mBusiness                = \App\Business::find(request()->session()->get('user.business_id'));
              if(!empty($mBusiness)){
                  $accountType              = \App\AccountType::find($mBusiness->additional_expense);
                  $expenseID                = $mBusiness->additional_expense;
                  // $databaseName =  "izo26102024_esai" ; $dab =  Illuminate\Support\Facades\Config::get('database.connections.mysql.database'); 
                  
                  if(!empty($accountType)){
                     $additional_expenses = [];
                      $additional_exp  =\App\Account::whereHas('account_type',function($query) use($expenseID){
                                $query->where('id',$expenseID);
                                $query->orWhere('parent_account_type_id',$expenseID);
                      })->get();
                      foreach ($additional_exp as $key => $value) {
                        # code...
                        $additional_expenses[$value->id] = $value->name . " || " . $value->account_number; 
                      }
                    }else{
                      
                      $additional_expenses      = \App\Account::items();
                    }
                  }else{ 
                    $additional_expenses      = \App\Account::items();
                    
                  }
              if($databaseName == $dab){
                // dd($additional_expenses);
              }
          ?>    
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <div class="row">
            <div class="col-md-4">
              <h4 class="modal-title">@lang( 'home.add_Expense' )</h4>
            </div>
            <div class="add_section hide col-md-4">
              <div class="form-group">
                <div class="multi-input text-center">
                        {!! Form::label('add_currency_id', __('business.currency') . ':') !!} 
                        <br> 
                        {!! Form::select('add_currency_id', $currencies, null, ['class' => 'form-control   add_currency_id  select2', "style" => 'width:49%','placeholder' => __('messages.please_select') ]); !!}
                        {!! Form::text('add_currency_id_amount', null, ['class' => 'form-control  pull-right add_currency_id_amount' , "style" => 'width:51%',  ]); !!}
                
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
                    <th class="ship_curr hide header_texts">@lang("Amount")</th>
                    <th class="ship_curr hide header_vats">@lang("Vat")</th>
                    <th class="ship_curr hide header_totals">@lang("Total")</th>
                    <th>@lang("home.Debit")</th>
                    <th>@lang("home.Cost Center")</th>
                    <th>@lang("home.Note")</th>
                    <th class="ship_curr hide" style="width:300px;">@lang("business.currency")</th>
                    <th>@lang("home.Date")</th>
                    <th class="btn-primary" onClick="addRow()"><i class="fa fa-plus"></i></th>
                  </tr>
                </thead>
                <tbody>
                  <tr id="addRow">
                    <td class="col-xs-1"> @lang('home.Total Amount') : <span id="shipping_total_amount"></span> </td>
                    <td class="col-xs-1"> @lang('home.Total Vat') : <span id="shipping_total_vat_s"></span> </td>
                    <td class="col-xs-1"> @lang('home.Total') : <span id="shipping_total_s"></span> </td>
                    <td class="col-xs-1 ship_curr hide"> @lang('Total Amount Currency') : <span id="shipping_amount_curr"></span> </td>
                    <td class="col-xs-1 ship_curr hide"> @lang('Total Vat Currency') : <span id="shipping_vat_curr"></span></td>
                    <td class="col-xs-1 ship_curr hide"> @lang('Total - Currency') : <span id="shipping_total_curr"></span></td>
                    <td class="col-xs-1"> </td>
                    <td class="col-xs-1"> </td>
                    <td class="col-xs-1"> </td>
                    <td class="col-xs-3 ship_curr hide" style="width:300px;" > </td>
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
              <div class="col-sm-3">
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
            </div>
          </div>
        </div>
          
          
          
          <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.save' )</button>
      </div>
    </div>
    
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
 @section('child_script')
  <script type="text/javascript">

    // $("#add_currency_id").select2({
    //       placeholder: "Please Select",
    //       multiple: false,
    //       minimumInputLength: 1,
    //       ajax: {
    //         url: "/symbol/amount/"+$(this).val(),
    //         dataType: 'json',
    //         quietMillis: 250,
    //         data: function(term, page) {
    //           return {
    //             q: term,
    //           };
    //         },
    //         results: function(data, page) {
    //           return {results: data};
    //         },
    //         cache: true
    //       },
    //       formatResult: function(element){
    //         return element.text + ' (' + element.id + ')';
    //       },
    //       formatSelection: function(element){
    //         return element.text + ' (' + element.id + ')';
    //       },
    //       escapeMarkup: function(m) {
    //         return m;
    //       }
    // });

    setTimeout(() => {
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
    }, 500);
    function formatRows(main, prefer, common,cost) {
         update_between_currency();
         if($('.currency_id').val() == ""){
           return '<tr>' +
              '<td class="col-xs-1">{{ Form::select('shipping_contact_id[]',$contacts,null,['class'=>'form-control select_edit shipping-select2 supplier','placeholder'=>trans('home.please account')]) }}</td>' +
              '<td class="col-xs-1">{{ Form::number('shipping_amount[]',0,['class'=>'form-control  shipping_amount_s','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1">{{ Form::number('shipping_vat[]',0,['class'=>'form-control shipping_tax','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1">{{ Form::number('shipping_total[]',0,['class'=>'form-control shipping_total','required','step'=>'any','min'=>0,'readOnly']) }}</td>' +
              '<td class="col-xs-1 ship_curr hide">{{ Form::number('shipping_amount_curr[]',0,['class'=>'form-control  shipping_amount_curr','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1 ship_curr hide">{{ Form::number('shipping_vat_curr[]',0,['class'=>'form-control shipping_tax_curr','required','step'=>'any','min'=>0]) }}</td>' +
              '<td class="col-xs-1 ship_curr hide">{{ Form::number('shipping_total_curr[]',0,['class'=>'form-control shipping_total_curr','required','step'=>'any','min'=>0,'readOnly']) }}</td>' +
              '<td class="col-xs-1">{{ Form::select('shipping_account_id[]',$additional_expenses,null,['class'=>'form-control select_edit shipping-select2 ','required']) }}</td>'+
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
              '<td class="col-xs-1">{{ Form::select('shipping_account_id[]',$additional_expenses,null,['class'=>'form-control select_edit shipping-select2 ','required']) }}</td>'+
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
    };

    function addRow() {
      var main = $('.addMain').val();
      var preferred = $('.addPrefer').val();
      var common = $('.addCommon').val();
      var cost = $('#cost_center_id option:selected').val();
      $('.shipping-select2').select2();
      $(formatRows(main,preferred,common,cost)).insertBefore('#addRow');
        update_shipping();
        $('.select_edit').select2();
        update_between_currency();

       
    }

    function update_shipping () {
      
      $('.shipping_tax, .shipping_amount_s, #supplier_id, .supplier , .shipping_amount_curr').change(function(){
        
        total_bill();
      });
       
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
                /** set it value*/second_amount.val(currency_amount.toFixed(2));
                total_bill();
            }  
        });
        $('.shipping_tax_curr').each(function(){
            e = $(this).val();
            if(currancy != "" && currancy != 0){
                var orginal_amount   = parseFloat(e) ;
                var currency_amount  = parseFloat(orginal_amount * currancy);
                var second_amount    = $(this).parent().parent().find(".shipping_tax");
                /** set it value*/second_amount.val(currency_amount.toFixed(2));
                total_bill();
            }  
        });
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
        var supplier_id                = $('#supplier_id option:selected').val();
        $('.shipping_amount_s').each(function(){
         
          var el           =  $(this).parent().parent();
          var sup_id       =  el.children().find('.supplier option:selected').val();
          var amount_curr  =  parseFloat(el.children().find('.shipping_amount_curr').val());

          el.children().find('.shipping_tax').val();
          var amount       =  parseFloat($(this).val()) ;
          var tax          =  parseFloat(el.children().find('.shipping_tax').val()) ;
          var tax_curr     =  parseFloat(el.children().find('.shipping_tax_curr').val()) ;

          total_vat_shiping    += tax;
          var total_s           = amount+tax; 
          var total_curr_s      = amount_curr+tax_curr; 

          total_amount_shiping      += amount;
          total_amount_shiping_curr += amount_curr ;
          total_tax_shiping_curr    += tax_curr ;
          total_tot_shiping_curr    += total_curr_s  ;
          total_shiping             += total_s;
         
          if ( (sup_id == supplier_id || sup_id == "" ) && supplier_id != ""  ) {
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
        $('input[name="ADD_SHIP"]').val(supplier_pay.toFixed(3));
        $('input[name="ADD_SHIP_"]').val(cost_pay.toFixed(3));
        
        $('#cost_amount_supplier_curr').text(supplier_pay_curr.toFixed(2));
        $('#cost_amount_curr').text(cost_pay_curr.toFixed(2));

        $('#shipping_total_vat_s').text(total_vat_shiping.toFixed(3));
        $('#shipping_total_s').text(total_shiping.toFixed(3));
        
        var total_items =  $("#total_subtotal_input_id").val();       
        var total       =  $("#grand_total_hidden").val();    
        if($("#total_finals_").val() != null){
          var total       =  $("#total_finals_").val()   ;       
              
        }   
        var total_curr     =  $("#grand_total_cur_hidden").val(); 
        var sub_total_curr = ($("#total_subtotal_input_cur").val() != null)?$("#total_subtotal_input_cur").val():0;
        var discount_curr  = parseFloat($("#discount_calculated_amount_cur").text()).toFixed(2); 
        var tax_calculated_amount_curr  = parseFloat($("#tax_calculated_amount_curr").text()).toFixed(2); 
        var ship        =  $("#total_ship_").val();       
        var ship_       =  $("#total_ship_c").val(); 
        currancy        =  $(".currency_id_amount").val();
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
        $(".hide_div").removeClass("hide");
        if(ship ==""){
            $("#grand_total").html(parseFloat(total));  
          }
        // console.log($(".hide_div").html());
    }

    function correct(){
        date   =  new Date($("#shipping_date").val());
       
        if(date.getFullYear() <= "2000" || date.getFullYear() > "2099"){
          return false ;
        }
      
			return true;
		}
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

  </script>
 @endsection
   