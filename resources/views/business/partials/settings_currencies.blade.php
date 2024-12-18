<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-11">
            <div class="form-group">
                

            </div>
        </div>
        {{-- <div class="col-sm-1">
            <div class="form-group">
                <div class="add_row btn-primary pull-right" style="border-radius:.2rem;padding:5px 15px;text-align:center" onClick="formatRows()"><i class="fa fa-plus">&nbsp;@lang('messages.add')</i></div>
            </div>
        </div> --}}
        <table class="table table-bordered table-responsive " id="box_currency">
            <thead>
              <tr>
                <th>@lang("lang_v1.right_amount")</th>
                <th>@lang("lang_v1.date")</th>
                <th>@lang("business.name")</th>
                <th>@lang("business.symbol")</th>
                <th>@lang("business.unit_other")</th>
                <th>@lang("business.unit_basic")</th>
                <th>@lang("business.default")</th>
                <th class="btn-primary" onClick="addRow()"><i class="fa fa-plus"></i></th>
              </tr>
            </thead>
            <tbody>
                @foreach($excange_rates as $it)
                    <tr class="old_row">
                        @if($it->source==1)
                            <td class="col-xs-1 text-center"> </td>
                        @else
                            <td class="col-xs-2"><input type="checkbox" class="curr_right" name="curr_right"  @if($it->right_amount != 0) checked @endif   /></td>
                        @endif
                        <td class="col-xs-1">
                            {{  Form::date('currency_date_old[]',$it->date, ['class'=>'form-control ','readOnly','required','max'=>date('Y-m-d')]) }}
                            {!! Form::hidden('cur_line[]', $it->id,["class"=>"cur_line"] ); !!}

                        </td>
                        <td class="col-xs-1">{{ Form::select('currency_name_old[]',$currencies,$it->currency_id,['class'=>'form-control select2 cur_name cur_select2','style'=>"width:100% !important" , 'placeholder'=> __('messages.please_select')  ]) }}</td>
                        <td class="col-xs-2">{{ Form::text('currency_symbol_old[]',$it->currency->symbol,['class'=>'form-control cur_symbol','readOnly' ]) }}</td>
                        <td class="col-xs-2">{{ Form::number('currency_amount_old[]',round($it->amount,7),['class'=>'form-control','required','step'=>'any','min'=>0]) }}</td>
                        <td class="col-xs-2">{{ Form::number('currency_opposit_amount_old[]',round($it->opposit_amount,7),['class'=>'form-control', 'readOnly','required','step'=>'any','min'=>0]) }}</td>
                        @if($it->source==1)
                            <td class="col-xs-1 text-center"> </td>
                            <td class="col-xs-1 text-center"> </td>
                        @else
                            @if($it->default)
                                <td class="col-xs-2">
                                    {{ Form::select('cur_default_old['.$it->currency_id.']',["1"=>"Default"],$it->default,['class'=>'form-control select2  cur_default cur_select2  ','required' ,'placeholder'=> __("messages.please_select")  ])}}
                                    {!! Form::hidden('cur_defult_check', 1,["class"=>"cur_defult_check"] ); !!}
                                    {!! Form::hidden('cur_defult_check_name',$it->currency_id,["class"=>"cur_defult_check_name"] ); !!}
                                </td>  
                            @else
                                <td class="col-xs-2">{{ Form::select('cur_default_old['.$it->currency_id.']',["1"=>"Default"],$it->default,['class'=>'form-control select2  cur_default cur_select2  ','required' ,'disabled' ,'placeholder'=> __("messages.please_select")  ])}}</td>  
                            @endif
                            <td class="col-xs-1 text-center"><a href="#" onClick="deleteRow(this)"><i class="fas fa-trash" aria-hidden="true"></a></td></tr> 
                         @endif

                    </tr>
                @endforeach
                <tr id="addRow"></tr>

            </tbody>                 
          </table>
        
   </div>
</div>
<script type="text/javascript">
        setTimeout(() => {
            $('.cur_select2').select2();
            update_symbol();
        }, 2000);
        function start(){
            var def     = $(".cur_defult_check").val();
            var id      = $(".cur_defult_check_name").val();
            if(def  != null){
            check_box(id,def ); 
            }
        }
        function formatRows() {
           
            return '<tr class="ros">' +
                    '<td class="col-xs-1"><input hidden type="checkbox" class="" name=""  />&nbsp;</td>' +
                    '<td class="col-xs-1">{{ Form::date('currency_date[]',date('Y-m-d'), ['class'=>'form-control ','readOnly','required','max'=>date('Y-m-d')]) }}</td>' +
                    '<td class="col-xs-1">{{ Form::select('currency_name[]',$currencies,null,['class'=>'form-control  cur_name cur_select2  ' ,'placeholder'=> __("messages.please_select")  ]) }}</td>' +
                    '<td class="col-xs-2">{{ Form::text('currency_symbol[]',null,['class'=>'form-control cur_symbol','readOnly' ]) }}</td>' +
                    '<td class="col-xs-2">{{ Form::number('currency_amount[]',0,['class'=>'form-control currency_amount','required','step'=>'any','min'=>0]) }}</td>' +
                    '<td class="col-xs-2">{{ Form::number('currency_opposit_amount[]',0,['class'=>'form-control currency_opposit_amount','readOnly','required','step'=>'any','min'=>0]) }}</td>' +
                    '<td class="col-xs-2">{{ Form::select('cur_default[ ]',["1"=>"Default"],null,['class'=>'form-control  cur_default cur_select2  ','required' ,'placeholder'=> __("messages.please_select")  ])}}</td>' +
                    '<td class="col-xs-1 text-center"><a href="#" onClick="deleteRow(this)"><i class="fas fa-trash" aria-hidden="true"></a></td></tr>';
                
            };
        function formatRows2() {
         
            return '<tr class="ros">' +
                '<td class="col-xs-1"><input hidden type="checkbox" class="" name=""  />&nbsp;</td>' +
                '<td class="col-xs-1">{{ Form::date('currency_date[]',date('Y-m-d'), ['class'=>'form-control ','readOnly','required','max'=>date('Y-m-d')]) }}</td>' +
                '<td class="col-xs-1">{{ Form::select('currency_name[]',$currencies,null,['class'=>'form-control  cur_name cur_select2  ' ,'placeholder'=> __("messages.please_select")  ]) }}</td>' +
                '<td class="col-xs-2">{{ Form::text('currency_symbol[]',null,['class'=>'form-control cur_symbol','readOnly' ]) }}</td>' +
                '<td class="col-xs-2">{{ Form::number('currency_amount[]',0,['class'=>'form-control currency_amount','required','step'=>'any','min'=>0]) }}</td>' +
                '<td class="col-xs-2">{{ Form::number('currency_opposit_amount[]',0,['class'=>'form-control currency_opposit_amount','readOnly','required','step'=>'any','min'=>0]) }}</td>' +
                '<td class="col-xs-2">{{ Form::select('cur_default[]',["1"=>"Default"],null,['class'=>'form-control  cur_default cur_select2  ','disabled' ,'placeholder'=> __("messages.please_select")  ])}}</td>' +
                '<td class="col-xs-1 text-center"><a href="#" onClick="deleteRow(this)"><i class="fas fa-trash" aria-hidden="true"></a></td></tr>';
            
        };
        
        function addRow() {
            var check =  $(".cur_defult_check").val();
                if(check==null){
                    $(formatRows()).insertBefore('#addRow');
                }else{
                    $(formatRows2()).insertBefore('#addRow');

                }
            $('.cur_select2').select2();
            update_symbol();
        }

        function update_symbol(){
            $(".cur_name").each(function(){
                var e = $(this).parent().parent() ;
                var ee = e.children().find(".cur_symbol");  
                var el = e.children().find(".cur_name");  
                var check  = e.children().find(".add_default");  
                var amount = e.children().find(".currency_amount");  
                var opposit_amount = e.children().find(".currency_opposit_amount");  
                var cur_default = e.children().find(".cur_default");  
                
                el.on("change",function(){
                    id = $(this).val();
                    name = "cur_default["+id+"]";
                    cur_default.attr("name",name); 
                    if(id!=""){
                        $.ajax({
                            url: '/symbol/' + id ,
                            dataType: 'html',
                            success: function ( da ) {
                                ee.val(da);  
                            
                            }
                        });
                    }else{
                        ee.val("");
                        amount.val(0);
                        opposit_amount.val(0);
                    }
                
                });
                amount.on("change",function(){
                    id = $(this).val();
                    opposit_amount.val((1/id).toFixed(4));
                    
                });
                cur_default.on("change",function(){
                    x_default = $(this).val();
                    check_box(el.val(),x_default);
                });
                // opposit_amount.on("change",function(){
                //     id = $(this).val();
                //     amount.val();
                // });
            });      
            $(".cur_name").each(function(){
                var e = $(this).parent().parent() ;
                var check_box = e.children().find(".curr_right");  
                var el = e.children().find(".cur_name"); 
                
                check_box.on("change",function(){
                        main  = $(this) ;
                        id    = el.val();
                    if(!main.attr("checked")){
                                main.attr("checked",true)
                            }else{
                                main.attr("checked",false);
                                $.ajax({
                                    url: "/symbol-left-amount/"+id,
                                    method: 'get',
                                    dataType: 'html',
                                    success: function(result) {
                                        if (JSON.parse(result).success == true) {
                                            toastr.success(JSON.parse(result).msg);
                                        } else {
                                            toastr.error(JSON.parse(result).msg);
                                        }
                                    },
                                });
                                
                            }
                            if(main.attr("checked")){
                                $.ajax({
                                    url: "/symbol-right-amount/"+id,
                                    method: 'get',
                                    dataType: 'html',
                                    success: function(result) {
                                        console.log(JSON.parse(result))
                                        if (JSON.parse(result).success == true) {
                                            toastr.success(JSON.parse(result).msg);
                                        } else {
                                            toastr.error(JSON.parse(result).msg);
                                        }
                                    },
                                });
                            }
                
                
                });
            });      
        }
        function check_box(id,x_default){
            $(".cur_name").each(function(){
                var e = $(this).parent().parent() ;
                var ee = e.children().find(".cur_symbol");  
                var el = e.children().find(".cur_name");  
                var check  = e.children().find(".add_default");  
                var amount = e.children().find(".currency_amount");  
                var opposit_amount = e.children().find(".currency_opposit_amount");
                var ex_default = e.children().find(".cur_default");
                console.log( id + "__ " + el.val());
                if(x_default == ""){
                    ex_default.removeAttr("disabled");
                }else{
                    if(el.val() != id  ){
                        ex_default.attr("disabled",true);
                    } 
                }
            });

        }
        $(document).on('ifChecked', 'input#enable_product_prices', function() {
                $(".prices").removeClass("hide");
        });
        
        $(document).on('ifUnchecked', 'input#enable_product_prices', function() {
                $(".prices").addClass("hide");
        });
        
        function deleteRow(trash) {
            $(trash).closest('tr').remove();
        };
    </script>
