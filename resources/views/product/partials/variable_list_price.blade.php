

<tr data-ks="{{$ks}}" data-variation_index="{{$variation_index}}"  data-value_index="{{$value_index}}" class="prices_list hide">
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
                {!! Form::select($name . '[' . $variation_index .'][variations]['.$value_index.'][unit_D][]', $list_new,  (isset($list_unit[$i]))?$i:$index  , ['class' => 'form-control input-sm width-10 dpp un_select input_number select2',"data-id"=>$k,"style"=>"width:10%" ,'id'=>'unit_D','required'     ]); !!}

                <table style="width:100%" >
                    <thead>
                        <th colspan="2">@lang('product.default_purchase_price')</th>
                        <th colspan="3">@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
                        <th colspan="3">@lang('product.default_selling_price')</th>
                    </thead>
                    <tbody>
                        @if($k != 0)
                            <tr>
                                <td style="vertical-align: bottom">
                                    {{"Default Price"}}
                                </td>
                                <td colspan="2">
                                    <div class="col-sm-6">
                                    {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                    
                                    {!! Form::text($name . '[' . $variation_index .'][variations]['.$value_index.'][single_dpp'.$ks.'][]', $default, ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp".$ks, 'placeholder' => __('product.exc_of_tax')  ]); !!}
                                    </div>
                    
                                    <div class="col-sm-6">
                                    {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                                    
                                    {!! Form::text($name . '[' . $variation_index .'][variations]['.$value_index.'][single_dpp_inc_tax'.$ks.'][]', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax".$ks, 'placeholder' => __('product.inc_of_tax') ]); !!}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <br/>
                                    {!! Form::text($name . '[' . $variation_index .'][variations]['.$value_index.'][profit_percent'.$ks.'][]' , @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$ks   ]); !!}
                                </td>
                                <td colspan="2">
                                    <div class="col-sm-6">
                                        {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                                        {!! Form::text($name . '[' . $variation_index .'][variations]['.$value_index.'][single_dsp'.$ks.'][]' , $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$ks   ]); !!}
                                    </div>
                                    <div class="col-sm-6">
                                    {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                                    {!! Form::text($name . '[' . $variation_index .'][variations]['.$value_index.'][single_dsp_inc_tax'.$ks.'][]' , $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$ks ]); !!}
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @foreach($product_price as $i => $value)
                            @if($value->product_id == null)
                            <tr>
                                <td style="vertical-align: bottom">
                                    {{$value->name}}
                                </td>
                                <td colspan="2">
                                    <div class="col-sm-6">
                                    {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                    
                                    {!! Form::text($name . '[' . $variation_index .'][variations]['.$value_index.'][single_dpp'.$ks.'][]', $default, ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp".$ks, 'placeholder' => __('product.exc_of_tax')  ]); !!}
                                    </div>
                    
                                    <div class="col-sm-6">
                                    {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                                    
                                    {!! Form::text($name . '[' . $variation_index .'][variations]['.$value_index.'][single_dpp_inc_tax'.$ks.'][]', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax".$ks, 'placeholder' => __('product.inc_of_tax') ]); !!}
                                    </div>
                                </td>
                                <td colspan="2">
                                    <br/>
                                    {!! Form::text($name . '[' . $variation_index .'][variations]['.$value_index.'][profit_percent'.$ks.'][]' , @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$ks   ]); !!}
                                </td>
                                <td colspan="2">
                                    <div class="col-sm-6">
                                        {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                                        {!! Form::text($name . '[' . $variation_index .'][variations]['.$value_index.'][single_dsp'.$ks.'][]' , $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$ks   ]); !!}
                                    </div>
                                    <div class="col-sm-6">
                                    {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                                    {!! Form::text($name . '[' . $variation_index .'][variations]['.$value_index.'][single_dsp_inc_tax'.$ks.'][]' , $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$ks ]); !!}
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    
                </table>
            
            @endforeach
        @endif
    </td>
</tr>