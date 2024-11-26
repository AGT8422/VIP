

<tr data-ks="{{$ks}}"  data-variation_index="{{$variation_index}}"  data-value_index="{{$value_index}}" class="prices_list hide">
    {{-- variable_table_price[{{$variation_index}}][variations][{{$value_index}}][list_price][default_sell_price] --}}
    @php
        $default = 0;
    @endphp
    <td colspan="7" >
        @if(isset($other_unit))
            @php $count =0 ;$ks =0; $index = 0;   @endphp
            @foreach($other_unit as $k => $i)
                @php $count++ ; $list_new=[];  @endphp
                @php  $ks  = $ks+1; @endphp 
                @foreach($units_main as $n => $row_line)
                    @if($i == $n)
                        @php 
                            $index = $n;    
                            $list_new[$n] = $row_line; 
                        @endphp
                    @endif
                @endforeach

                {!! Form::select('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][unit_D][]', $list_new,  (isset($list_unit[$i]))?$i:$index  , ['class' => 'form-control input-sm width-10 dpp un_select input_number select2',"data-id"=>$k,"style"=>"width:10%" ,'id'=>'unit_D','required', 'placeholder' => __('messages.please_select')     ]); !!}

                <table style="width:100%" >
                    <thead>
                        <th colspan="2">@lang('product.default_purchase_price')</th>
                        <th colspan="3">@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
                        <th colspan="3">@lang('product.default_selling_price')</th>
                    </thead>
                    <tbody>
                        @php 
                        $var_id = \App\Variation::find($variation_value_id);
                        $v_id   = 100000000000; 
                        if(!empty($var_id)){$v_id  = $var_id->variation_value_id;} 
                        // dd($variations_template_id . " _ " .  $v_id  . " _ " . $ks  . " _ " . $i  . " _ " . $product_id);  
                       
                        $product_price  = \App\Models\ProductPrice::where("product_id",$product_id)
                                                                    ->whereNull("default_name")

                                                                    // ->where("number_of_default","!=",0)
                                                                    
                                                                    ->where("unit_id",$i)
                                                                    // ->where("ks_line",$ks)
                                                                    ->where("variations_value_id",$v_id)
                                                                    ->where("variations_template_id",$variations_template_id);
                        if($k == 0){
                            $product_price->where("number_of_default","!=",0);
                            
                        }
                        $product_price  =  $product_price->get(); 
                          
                        @endphp
                        @if(count($product_price)>0)
                            @foreach($product_price as $i => $value) 
                            @if($value->product_id != null)
                                <tr>
                                    <td style="vertical-align: bottom">
                                        {{$value->name}}
                                    </td>
                                    <td colspan="2">
                                        <div class="col-sm-6">
                                        {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                        
                                        {!! Form::hidden('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][line_id'.$ks.'][]',  $value->id , ['class' => 'form-control input-sm   input_number' , 'placeholder' => __('product.exc_of_tax')  ]); !!}
                                        {!! Form::text('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][single_dpp'.$ks.'][]', @num_format($value->default_purchase_price), ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp".$ks, 'placeholder' => __('product.exc_of_tax')  ]); !!}
                                        </div>
                        
                                        <div class="col-sm-6">
                                        {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                                        
                                        {!! Form::text('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][single_dpp_inc_tax'.$ks.'][]', @num_format($value->dpp_inc_tax), ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax".$ks, 'placeholder' => __('product.inc_of_tax') ]); !!}
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <br/>
                                        {!! Form::text('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][profit_percent'.$ks.'][]' , @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$ks   ]); !!}
                                    </td>
                                    <td colspan="2">
                                        <div class="col-sm-6">
                                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                                            {!! Form::text('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][single_dsp'.$ks.'][]' , @num_format($value->default_sell_price), ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$ks   ]); !!}
                                        </div>
                                        <div class="col-sm-6">
                                        {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                                        {!! Form::text('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][single_dsp_inc_tax'.$ks.'][]' , @num_format($value->sell_price_inc_tax), ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$ks ]); !!}
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @endforeach
                        @else
                                @php   $start = 1; $end = 9;  @endphp
                             
                                @while($start <= $end)
                                @php $product_prices = \App\Models\ProductPrice::where("product_id",$product_id)
                                                                                // ->where("ks_line",$ks)
                                                                                ->where("variations_value_id",$v_id)
                                                                                ->where("variations_template_id",$variations_template_id)
                                                                                ->where("number_of_default",$start)
                                                                                ->where("unit_id",$i)->first();  
                                    switch ($start) {
                                        case 0:
                                            $name_val = "Default Price";
                                            break;
                                        case 1:
                                            $name_val = "Whole Price";
                                            break;
                                        case 2:
                                            $name_val = "Retail Price";
                                            break;
                                        case 3:
                                            $name_val = "Minimum Price";
                                            break;
                                        case 4:
                                            $name_val = "Last Price";
                                            break;
                                        case 5:
                                            $name_val = "ECM Before Price";
                                            break;
                                        case 6:
                                            $name_val = "ECM After Price";
                                            break;
                                        case 7:
                                            $name_val = "Custom Price 1";
                                            break;
                                        case 8:
                                            $name_val = "Custom Price 2";
                                            break;
                                        case 9:
                                            $name_val = "Custom Price 3";
                                            break;
                                        default:
                                            $name_val = "";
                                    }
                                @endphp
                                
                                <tr>
                                    <td style="vertical-align: bottom"> {{ (!empty($product_prices))?$product_prices->name:$name_val }} </td>
                                    <td colspan="2">
                                        {{-- @php $varid = null; foreach ($product_details->variations as $key => $var) { $varid  =   $var->id; } @endphp
                                        <input type="hidden" name="single_variation_id1" value="{{$varid}}"> --}}
                                        <div class="col-sm-6">
                                        {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                        {!! Form::text('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][single_dpp'.$ks.'][]', @num_format((!empty($product_prices))?$product_prices->default_purchase_price:0), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => __('product.exc_of_tax'),'id' => 'single_dpp'.$count, 'required']); !!}
                                        </div>
                                        <div class="col-sm-6">
                                        {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                                        {!! Form::text('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][single_dpp_inc_tax'.$ks.'][]', @num_format((!empty($product_prices))?$product_prices->dpp_inc_tax:0), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => __('product.inc_of_tax'),'id' => 'single_dpp_inc_tax'.$count, 'required']); !!}
                                        </div>
                                    </td>
                                    <td colspan="2"> <br/> {!! Form::text('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][profit_percent'.$ks.'][]', @num_format((!empty($product_prices))?$product_prices->profit_percent:0), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$count, 'required']); !!}</td>
                                    <td colspan="2">
                                        <div class="col-sm-6">
                                            {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':*') !!}
                                            {!! Form::text('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][single_dsp'.$ks.'][]', @num_format((!empty($product_prices))?$product_prices->default_sell_price:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$count, 'required']); !!}
                                        </div>
            
                                        <div class="col-sm-6">
                                        {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':*') !!}
                                        {!! Form::text('product_variation_edit[' . $value_index .'][variations_edit]['.$variation_index.'][single_dsp_inc_tax'.$ks.'][]', @num_format((!empty($product_prices))?$product_prices->sell_price_inc_tax:0), ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$count, 'required']); !!}
                                        </div>
                                    </td>
                                </tr> 
                                @php $start++; @endphp
                                @endwhile
                                 
                        
                        @endif

                    </tbody>
                    
                </table>
            
                @endforeach
         
                
       @endif
          
    </td>
</tr>