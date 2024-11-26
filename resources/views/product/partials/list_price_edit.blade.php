<div class="row " >
    <div class="col-sm-8"></div>
    <div class="col-sm-4 text-right">
        {{-- <div class="btn btn-secondry"  onClick="add_column();" style="background-color:#717171;color:#ffffff;margin:10px;">@lang("product.add_column")</div>
        <div class="btn btn-secondry"  onClick="add_row();" style="background-color:#717171;color:#ffffff;margin:10px;">@lang("product.add_row")</div> --}}
    </div>
</div>
        @php
        $first_price   = null;
        $second_price  = null;
        $third_price   = null;
        $fourth_price  = null;
        $count_of_column         = 1;
        $prices = [];
        $others = [];
        $names  = [];
        @endphp
        {{-- @foreach($product_price as $i) --}}
            {{-- @if($i->default_name == 1 && $i->number_of_default == 1)
                @php  $first_price = $i; $first_line_id = $i->id; $list_first = json_decode($i->list_of_price) ;   $count_of_column = count($list_first);     @endphp
            @elseif($i->default_name == 1 && $i->number_of_default == 2) 
                @php  $second_price = $i;$second_line_id = $i->id; $list_second = json_decode($i->list_of_price) ;   @endphp
            @elseif($i->default_name == 1 && $i->number_of_default == 3)
                @php  $third_price = $i;$third_line_id = $i->id; $list_third = json_decode($i->list_of_price) ;    @endphp
            @elseif($i->default_name == 1 && $i->number_of_default == 4)
                @php  $fourth_price = $i;$fourth_line_id = $i->id; $list_fourth = json_decode($i->list_of_price) ;   @endphp
            @elseif($i->default_name == 1 && $i->number_of_default == 5)
                @php  $fifth_price = $i;$fifth_line_id = $i->id; $list_fifth = json_decode($i->list_of_price) ;   @endphp
            @elseif($i->default_name == 1 && $i->number_of_default == 6)
                @php  $sixth_price = $i;$sixth_line_id = $i->id; $list_sixth = json_decode($i->list_of_price) ;   @endphp
            @elseif($i->default_name == 1 && $i->number_of_default == 7)
                @php  $seventh_price = $i;$seventh_line_id = $i->id; $list_seventh = json_decode($i->list_of_price) ;   @endphp
            @elseif($i->default_name == 1 && $i->number_of_default == 8)
                @php  $eight_price = $i;$eight_line_id = $i->id; $list_eight = json_decode($i->list_of_price) ;   @endphp
            @elseif($i->default_name == 1 && $i->number_of_default == 9)
                @php  $nineth_price = $i;$nineth_line_id = $i->id; $list_nineth = json_decode($i->list_of_price) ;   @endphp
            @else --}}
                {{-- @php    $names[$i->id] = $i->name;  $others[] = $i->id; $prices[$i->id] = $i->price; $list_id[$i->id] =  ;     @endphp --}}
            {{-- @endif --}}
        {{-- @endforeach --}}

<table class="table table-bordered table-striped ajax_view hide-footer" id="product_price">
    <thead>
        <tr>
            <th>@lang('lang_v1.name')</th>
             {{-- @if($count_of_column >= 1)
             @php $counter = 1 @endphp
            @while($counter <= $count_of_column)
                <th class="column_{{$counter}}">@lang('lang_v1.price_in_currency')@if( $counter != 1)  &nbsp;&nbsp;<i class="fa fas fa-trash" onClick="delete_column(this);"></i>@endif</th>
                @php $counter++ @endphp
            @endwhile
            @endif --}}
                @php   $counter = 0 @endphp
                @foreach($unitsP as $k => $i)
                    <th class="column_{{$counter}}">  &nbsp;&nbsp;{{$i}} <input name="unit_idd[]" hidden class="unit_idd" value="{{$k}}" /> </th>
                @php $counter++ @endphp
                @endforeach
            {{-- <th class="last_column"></th> --}}
             
        </tr>
    </thead>
    <tbody>
        {{-- <tr>
            <td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="{{(isset($first_line_id))?$first_line_id:null}}"  ><input  type="text" name="name_add[]" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Whole Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="{{(isset($first_line_id))?$first_price->price:null}}" placeholder="{{__('messages.please_enter_price')}}"></td>
            @if($count_of_column >= 1)
                @php $counter_one = 1 @endphp
                @while($counter_one <= $count_of_column)
                   
                    <td class=" multi-input  ceil_{{$counter_one}} column_{{$counter_one}}  currency[{{$counter_one}}] "  > 
                        <input  class="ceil" hidden value="{{$counter_one}}">
                        @php   @endphp   
                    <input  type="text" name="currency_price[{{$counter_one}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="{{(isset($first_line_id))?get_object_vars($list_first[$counter_one-1])[array_keys(get_object_vars($list_first[$counter_one-1]))[0]]:null}}" placeholder="{{__('messages.please_enter_price')}}"> 
                    {{ Form::select('currency_amount_price['.$counter_one.'][]',$currencies,(isset($first_line_id))?array_keys(get_object_vars($list_first[$counter_one-1]))[0]:null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
                    </td>
                    @php $counter_one++ @endphp
                @endwhile
             @endif
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="{{(isset($second_line_id))?$second_line_id:null}}"  ><input  type="text" name="name_add[]"  ReadOnly class="form-control name_price" value="{{__("lang_v1.Retail Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="{{(isset($second_line_id))?$second_price->price:null}}" placeholder="{{__('messages.please_enter_price')}}"></td>
            @if($count_of_column >= 1)
                @php $counter_two = 1 @endphp
                @while($counter_two <= $count_of_column)
                    <td class=" multi-input  ceil_{{$counter_two}} column_{{$counter_two}}  currency[{{$counter_two}}] "  > 
                        <input  class="ceil" hidden value="{{$counter_two}}">
                    <input  type="text" name="currency_price[{{$counter_two}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="{{(isset($second_line_id))?get_object_vars($list_second[$counter_two-1])[array_keys(get_object_vars($list_second[$counter_two-1]))[0]]:null}}" placeholder="{{__('messages.please_enter_price')}}"> 
                    {{ Form::select('currency_amount_price['.$counter_two.'][]',$currencies,(isset($second_line_id))?array_keys(get_object_vars($list_second[$counter_two-1]))[0]:null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
                    </td>
                    @php $counter_two++ @endphp
                @endwhile
            @endif
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="{{(isset($third_line_id))?$third_line_id:null}}"  ><input  type="text" name="name_add[]"  ReadOnly class="form-control name_price" value="{{__("lang_v1.Minimum Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="{{(isset($third_line_id))?$third_price->price:null}}" placeholder="{{__('messages.please_enter_price')}}"></td>
            @if($count_of_column >= 1)
                @php $counter_three = 1 @endphp
                @while($counter_three <= $count_of_column)
                    <td class=" multi-input  ceil_{{$counter_three}} column_{{$counter_three}}  currency[{{$counter_three}}] "  > 
                        <input  class="ceil" hidden value="{{$counter_three}}">
                    <input  type="text" name="currency_price[{{$counter_three}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="{{(isset($third_line_id))?get_object_vars($list_third[$counter_three-1])[array_keys(get_object_vars($list_third[$counter_three-1]))[0]]:null}}" placeholder="{{__('messages.please_enter_price')}}"> 
                    {{ Form::select('currency_amount_price['.$counter_three.'][]',$currencies,(isset($third_line_id))?array_keys(get_object_vars($list_third[$counter_three-1]))[0]:null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
                    </td>
                    @php $counter_three++ @endphp
                @endwhile
            @endif
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="{{(isset($fourth_line_id))?$fourth_line_id:null}}"  ><input  type="text" name="name_add[]" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Last Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="{{(isset($fourth_line_id))?$fourth_price->price:null}}" placeholder="{{__('messages.please_enter_price')}}"></td>
            @if($count_of_column >= 1)
            @php $counter_four = 1 @endphp
                @while($counter_four <= $count_of_column)
                    <td class=" multi-input  ceil_{{$counter_four}} column_{{$counter_four}}  currency[{{$counter_four}}] "  > 
                        <input  class="ceil" hidden value="{{$counter_four}}">
                    <input  type="text" name="currency_price[{{$counter_four}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="{{(isset($fourth_line_id))?get_object_vars($list_fourth[$counter_four-1])[array_keys(get_object_vars($list_fourth[$counter_four-1]))[0]]:null}}" placeholder="{{__('messages.please_enter_price')}}"> 
                    {{ Form::select('currency_amount_price['.$counter_four.'][]',$currencies,(isset($fourth_line_id))?array_keys(get_object_vars($list_fourth[$counter_four-1]))[0]:null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
                    </td>
                    @php $counter_four++ @endphp
                @endwhile
            @endif
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="{{(isset($fifth_line_id))?$fifth_line_id:null}}"  ><input  type="text" name="name_add[]" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Last Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="{{(isset($fifth_line_id))?$fifth_price->price:null}}" placeholder="{{__('messages.please_enter_price')}}"></td>
            @if($count_of_column >= 1)
            @php $counter_five = 1 @endphp
                @while($counter_five <= $count_of_column)
                    <td class=" multi-input  ceil_{{$counter_five}} column_{{$counter_five}}  currency[{{$counter_five}}] "  > 
                        <input  class="ceil" hidden value="{{$counter_five}}">
                    <input  type="text" name="currency_price[{{$counter_five}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="{{(isset($fourth_line_id))?get_object_vars($list_fifth[$counter_five-1])[array_keys(get_object_vars($list_fifth[$counter_five-1]))[0]]:null}}" placeholder="{{__('messages.please_enter_price')}}"> 
                    {{ Form::select('currency_amount_price['.$counter_five.'][]',$currencies,(isset($fifth_line_id))?array_keys(get_object_vars($list_fifth[$counter_five-1]))[0]:null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
                    </td>
                    @php $counter_five++ @endphp
                @endwhile
            @endif
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="{{(isset($sixth_line_id))?$sixth_line_id:null}}"  ><input  type="text" name="name_add[]" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Last Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="{{(isset($sixth_line_id))?$sixth_price->price:null}}" placeholder="{{__('messages.please_enter_price')}}"></td>
            @if($count_of_column >= 1)
            @php $counter_six = 1 @endphp
                @while($counter_six <= $count_of_column)
                    <td class=" multi-input  ceil_{{$counter_six}} column_{{$counter_six}}  currency[{{$counter_six}}] "  > 
                        <input  class="ceil" hidden value="{{$counter_six}}">
                    <input  type="text" name="currency_price[{{$counter_six}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="{{(isset($sixth_line_id))?get_object_vars($list_sixth[$counter_six-1])[array_keys(get_object_vars($list_sixth[$counter_six-1]))[0]]:null}}" placeholder="{{__('messages.please_enter_price')}}"> 
                    {{ Form::select('currency_amount_price['.$counter_six.'][]',$currencies,(isset($sixth_line_id))?array_keys(get_object_vars($list_sixth[$counter_six-1]))[0]:null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
                    </td>
                    @php $counter_six++ @endphp
                @endwhile
            @endif
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="{{(isset($seventh_line_id))?$seventh_line_id:null}}"  ><input  type="text" name="name_add[]" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Last Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="{{(isset($seventh_line_id))?$seventh_price->price:null}}" placeholder="{{__('messages.please_enter_price')}}"></td>
            @if($count_of_column >= 1)
            @php $counter_seven = 1 @endphp
                @while($counter_seven <= $count_of_column)
                    <td class=" multi-input  ceil_{{$counter_seven}} column_{{$counter_seven}}  currency[{{$counter_seven}}] "  > 
                        <input  class="ceil" hidden value="{{$counter_seven}}">
                    <input  type="text" name="currency_price[{{$counter_seven}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="{{(isset($seventh_line_id))?get_object_vars($list_seventh[$counter_seven-1])[array_keys(get_object_vars($list_seventh[$counter_seven-1]))[0]]:null}}" placeholder="{{__('messages.please_enter_price')}}"> 
                    {{ Form::select('currency_amount_price['.$counter_seven.'][]',$currencies,(isset($seventh_line_id))?array_keys(get_object_vars($list_seventh[$counter_seven-1]))[0]:null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
                    </td>
                    @php $counter_seven++ @endphp
                @endwhile
            @endif
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="{{(isset($eight_line_id))?$eight_line_id:null}}"  ><input  type="text" name="name_add[]" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Last Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="{{(isset($eight_line_id))?$eight_price->price:null}}" placeholder="{{__('messages.please_enter_price')}}"></td>
            @if($count_of_column >= 1)
            @php $counter_eight = 1 @endphp
                @while($counter_eight <= $count_of_column)
                    <td class=" multi-input  ceil_{{$counter_eight}} column_{{$counter_eight}}  currency[{{$counter_eight}}] "  > 
                        <input  class="ceil" hidden value="{{$counter_eight}}">
                    <input  type="text" name="currency_price[{{$counter_eight}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="{{(isset($eight_line_id))?get_object_vars($list_eight[$counter_eight-1])[array_keys(get_object_vars($list_eight[$counter_eight-1]))[0]]:null}}" placeholder="{{__('messages.please_enter_price')}}"> 
                    {{ Form::select('currency_amount_price['.$counter_eight.'][]',$currencies,(isset($eight_line_id))?array_keys(get_object_vars($list_eight[$counter_eight-1]))[0]:null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
                    </td>
                    @php $counter_eight++ @endphp
                @endwhile
            @endif
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="{{(isset($nineth_line_id))?$nineth_line_id:null}}"  ><input  type="text" name="name_add[]" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Last Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="{{(isset($nineth_line_id))?$nineth_price->price:null}}" placeholder="{{__('messages.please_enter_price')}}"></td>
            @if($count_of_column >= 1)
            @php $counter_nineth = 1 @endphp
                @while($counter_nineth <= $count_of_column)
                    <td class=" multi-input  ceil_{{$counter_nineth}} column_{{$counter_nineth}}  currency[{{$counter_nineth}}] "  > 
                        <input  class="ceil" hidden value="{{$counter_nineth}}">
                    <input  type="text" name="currency_price[{{$counter_nineth}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="{{(isset($nineth_line_id))?get_object_vars($list_nineth[$counter_nineth-1])[array_keys(get_object_vars($list_nineth[$counter_nineth-1]))[0]]:null}}" placeholder="{{__('messages.please_enter_price')}}"> 
                    {{ Form::select('currency_amount_price['.$counter_nineth.'][]',$currencies,(isset($nineth_line_id))?array_keys(get_object_vars($list_nineth[$counter_nineth-1]))[0]:null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
                    </td>
                    @php $counter_nineth++ @endphp
                @endwhile
            @endif
            <td class="last_column"></td> 
        </tr> --}}
        {{-- @if(count($others)>0) --}}
        
         @foreach($product_price as $key => $value)
         <tr>
                <td ><input  type="hidden"    name="line_id[]" class="form-control line_id" value="{{$value->id}}"><input @if($key<6) readOnly @endif   type="text" name="name_add[]"   class="form-control name_price" value="{{$value->name}}" placeholder="{{__('messages.please_enter_name')}}"></td>
                 {{-- @if($count_of_column >= 1)
                    @php $counter_x = 1 @endphp
                @while($counter_x <= $count_of_column)
                <td class=" multi-input  ceil_{{$counter_x}} column_{{$counter_x}}  currency[{{$counter_x}}] "  > 
                    <input  class="ceil" hidden value="{{$counter_x}}">
                    <input  type="text" name="currency_price[{{$counter_x}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="{{get_object_vars($value[$counter_x-1])[array_keys(get_object_vars($value[$counter_x-1]))[0]]}}" placeholder="{{__('messages.please_enter_price')}}"> 
                    {{ Form::select('currency_amount_price['.$counter_x.'][]',$currencies,array_keys(get_object_vars($value[$counter_x-1]))[0],['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
                </td>
                @php $counter_x++ @endphp
                @endwhile
                @endif --}}
                {{-- <td class="last_column delete_row" onClick="delete_row(this);"><i class="fa fas fa-trash"></i></td>  --}}
                    @php   $counter_x = 0 @endphp
                    @foreach($unitsP as $i)
                        <td class=" multi-input  ceil_{{$counter_x}} column_{{$counter_x}}  currency[{{$counter_x}}] "  > 
                            <input  class="ceil" hidden value="{{$counter_x}}">
                            <input  type="text" name="currency_price[{{$counter_x}}][]" class="form-control amount_price width-70 pull-left" min="0" style="width:100% ;display:inline-block"  value="" placeholder="{{__('messages.please_enter_price')}}"> 
                            {{-- {{ Form::select('currency_amount_price['.$counter_x.'][]',$units,array_keys(get_object_vars($value[$counter_x-1]))[0],['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }} --}}
                        </td>
                        @php $counter_x++ @endphp
                    @endforeach
            </tr>
            @endforeach
            {{-- @endif --}}
    
    </tbody>
    <tfoot>
        <td></td>
 
        @php $counter_s = 0; @endphp
        @foreach($unitsP as $i)
            <td></td>
            @php $counter_s++ @endphp
        @endforeach
        {{-- @if($count_of_column >= 1)
        @php $counter_w = 1 @endphp
        @while($counter_w <= $count_of_column)
        <td></td>
        @php $counter_w++ @endphp
        @endwhile
        @endif  --}}
        {{-- <td class="last_column"></td> --}}
    </tfoot>
</table>
<input type="text" hidden value="{{$count_of_column}}" class="number-of-column">
