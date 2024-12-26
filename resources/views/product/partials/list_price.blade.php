<div class="row " >
    <div class="col-sm-8"></div>
    <div class="col-sm-4 text-right">
        <div class="btn btn-secondry"  onClick="add_column();" style="background-color:#717171;color:#ffffff;margin:10px;">@lang("product.add_column")</div>
        <div class="btn btn-secondry"  onClick="add_row();" style="background-color:#717171;color:#ffffff;margin:10px;">@lang("product.add_row")</div>
    </div>
</div>

<table class="table table-bordered table-striped ajax_view hide-footer" id="product_price">
    <thead>
        <tr>
            <th>@lang('lang_v1.name')</th>
            <th>@lang('lang_v1.price')</th>
            {{-- <th class="column_1">@lang('lang_v1.price_in_currency')</th> --}}
            <th class="last_column"></th>
             
        </tr>
    </thead>
    <tbody>
        <tr>
            <td ><input  type="hidden" name="name_add[]"   class="form-control name_price" value="{{__("izo.Whole Price")}}" placeholder="{{__('messages.please_enter_name')}}"><input  type="text" name="" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Whole Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="" placeholder="{{__('messages.please_enter_price')}}"></td>
            {{-- <td class=" multi-input ceil_1 column_1  currency[1] "  >
                <input  class="ceil" hidden value="1"> 
             <input  type="text" name="currency_price[1][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}"> 
             {{ Form::select('currency_amount_price[1][]',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
            </td> --}}
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden" name="name_add[]"   class="form-control name_price" value="{{__("izo.Retail Price")}}" placeholder="{{__('messages.please_enter_name')}}"><input  type="text" name="" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Retail Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="" placeholder="{{__('messages.please_enter_price')}}"></td>
            {{-- <td class=" multi-input  ceil_1 column_1  currency[1] "  > 
                <input  class="ceil" hidden value="1">
             <input  type="text" name="currency_price[1][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}"> 
             {{ Form::select('currency_amount_price[1][]',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
            </td> --}}
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden" name="name_add[]"   class="form-control name_price" value="{{__("izo.Minimum Price")}}" placeholder="{{__('messages.please_enter_name')}}"><input  type="text" name=""  ReadOnly class="form-control name_price" value="{{__("lang_v1.Minimum Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="" placeholder="{{__('messages.please_enter_price')}}"></td>
            {{-- <td class=" multi-input ceil_1 column_1  currency[1] "  > 
                <input  class="ceil" hidden value="1">
             <input  type="text" name="currency_price[1][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}"> 
             {{ Form::select('currency_amount_price[1][]',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
            </td> --}}
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden" name="name_add[]"   class="form-control name_price" value="{{__("izo.Last Price")}}" placeholder="{{__('messages.please_enter_name')}}"><input  type="text" name="" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Last Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="" placeholder="{{__('messages.please_enter_price')}}"></td>
            {{-- <td class=" multi-input ceil_1 column_1  currency[1] "  > 
             <input  class="ceil" hidden value="1"> 
             <input  type="text" name="currency_price[1][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}"> 
             {{ Form::select('currency_amount_price[1][]',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
            </td> --}}
            <td class="last_column"></td> 
        </tr>
        <tr>
            <td ><input  type="hidden" name="name_add[]"   class="form-control name_price" value="{{__("izo.Last Price")}}" placeholder="{{__('messages.please_enter_name')}}"><input  type="text" name="" ReadOnly  class="form-control name_price" value="{{__("lang_v1.Last Price")}}" placeholder="{{__('messages.please_enter_name')}}"></td>
            <td ><input  type="text" name="price[]" class="form-control amount_price" value="" placeholder="{{__('messages.please_enter_price')}}"></td>
            {{-- <td class=" multi-input ceil_1 column_1  currency[1] "  > 
             <input  class="ceil" hidden value="1"> 
             <input  type="text" name="currency_price[1][]" class="form-control amount_price width-70 pull-left" min="0" style="width:60% ;display:inline-block"  value="0" placeholder="{{__('messages.please_enter_price')}}"> 
             {{ Form::select('currency_amount_price[1][]',$currencies,null,['class'=>'form-control amount_price_s width-30 pull-left select2 ' ,'style'=>'width:40%' ,'placeholder'=>__('messages.please_select') ]) }}
            </td> --}}
            <td class="last_column"></td> 
        </tr>
    </tbody>
    <tfoot>
        <td></td>
        <td></td>
       
        <td class="last_column"></td>
    </tfoot>
</table>
<input type="text" hidden value="1" class="number-of-column">
