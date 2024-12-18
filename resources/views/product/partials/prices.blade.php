
@extends('layouts.app')

@section("content")
    <section class="content">
        @component('components.widget' , ["class"=>"box-primary","title"=>__('Prices Settings')])
            <h1>@lang('Type Of Product')</h1>
            <h1>&nbsp;</h1>
            <div class="row">
                <div class="col-md-8">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label("category_id",__("Category Name")) !!}
                            {!! Form::select("category_id",[],null,["class"=>"form-control select2","id"=>"category_id","placeholder"=>__("messages.please_select")]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label("sub_category_id",__("Sub Category Name")) !!}
                            {!! Form::select("sub_category_id",[],null,["class"=>"form-control select2","id"=>"sub_category_id","placeholder"=>__("messages.please_select")]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label("brand_id",__("Brand Name")) !!}
                            {!! Form::select("brand_id",[],null,["class"=>"form-control select2","id"=>"brand_id","placeholder"=>__("messages.please_select")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                        {!! Form::label("conditions",__("With Conditions")) !!}
                        {!! Form::checkbox("conditions",1,null,["class"=>"input-icheck","id"=>"conditions"]) !!}
                </div>
                <div class="cond col-md-11 hide">
                    <div class="col-md-4">
                        {!! Form::label("max_price",__("More Than Price")) !!}
                        {!! Form::number("max_price",0,["class"=>"form-control","id"=>"max_price"]) !!}
                    </div>
                    <div class="col-md-4">
                        {!! Form::label("min_price",__("Less Than Price")) !!}
                        {!! Form::number("min_price",0,["class"=>"form-control","id"=>"min_price"]) !!}
                    </div>
                    <div class="col-md-4">
                        {!! Form::label("word",__("Include Word")) !!}
                        {!! Form::text("word",null,["class"=>"form-control","id"=>"word"]) !!}
                    </div>
                </div>
                
            </div>
            <h1>&nbsp;</h1>
    
            
        @endcomponent
        @component("components.widget",["title"=>__("Advanced Setting"),"class"=>"box-secondry"])
            <h1>@lang('Choose the price')</h1>
            <h1>&nbsp;</h1>     
            <div class="row">
                <div class="col-md-5">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label("main_price",__("Main Price")) !!}
                            {!! Form::select("main_price",[],null,["class"=>"form-control select2","id"=>"main_price","placeholder"=>__("messages.please_select")]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label("unit_price",__("Unit Of Product")) !!}
                            {!! Form::select("unit_price",[],null,["class"=>"form-control select2","id"=>"unit_price","placeholder"=>__("messages.please_select")]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="col-md-12 text-center">
                        <div class="form-group">
                             <br>
                             <i class="fa fas fa-arrow-right" style="font-size:30px"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label("new_price",__("New Price")) !!}
                            {!! Form::select("new_price",[],null,["class"=>"form-control select2","id"=>"new_price","placeholder"=>__("messages.please_select")]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label("new_unit_price",__("Unit Of Product")) !!}
                            {!! Form::select("new_unit_price",[],null,["class"=>"form-control select2","id"=>"new_unit_price","placeholder"=>__("messages.please_select")]) !!}
                        </div>
                    </div>
                </div>
             </div>
             <h1>&nbsp;</h1>
             @component("components.widget",["title"=>__("Pricing Policy"),"class"=>"box-secondry"])
                <div class="row">
                    {{--  change to price --}}
                    <div class="col-md-4">
                        <div class="col-md-6">
                            <div class="form-group" style="font-size:15px">
                                {!! Form::radio("price",null, true,["class"=>"price-radio input-check","id"=>"change_to_price"]) !!}
                                {!! Form::label("change_to_price",__("Change To Price")) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="font-size:15px">
                                {!! Form::number("ch_final_price",0,["class"=>"change_to_price price-type form-control","id"=>"ch_final_price"]) !!}  
                            </div>
                            <br>
                        </div>
                       
                        <div class="col-md-6">
                            <div class="form-group" style="font-size:15px">
                                {!! Form::radio("price",null, false,["class"=>" price-radio input-check ","id"=>"fixed_price"]) !!}
                                {!! Form::label("fixed_price",__("Add Fixed Price")) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="font-size:15px">
                                {!! Form::number("fx_final_price",0,["class"=>"fixed_price price-type form-control","disabled","id"=>"fx_final_price"]) !!}  
                            </div>
                        </div>
                          
                    </div>
                    {{--  Statment Math   --}}
                    <div class="col-md-4">
                        <div class="col-md-6">
                            <div class="form-group" style="font-size:15px">
                                {!! Form::radio("price",null, false,["class"=>"price-radio input-check","data-name"=>"multiple","id"=>"mult_number_price"]) !!}
                                {!! Form::label("mult_number_price",__("Multiplication by a number")) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="font-size:15px">
                                {!! Form::number("mult_final_price",0,["class"=>"mult_number_price price-type form-control","disabled","id"=>"mult_final_price"]) !!}  
                            </div>
                            <br>
                        </div>
                      
                        <div class="col-md-6">
                            <div class="form-group" style="font-size:15px">
                                {!! Form::radio("price",null, false,["class"=>"price-radio input-check ",'data-name' => 'percent',"id"=>"perc_number_price"]) !!}
                                {!! Form::label("perc_number_price",__("Multiplication by percentage")) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="font-size:15px">
                                {!! Form::number("perc_final_price",0,["class"=>"perc_number_price price-type form-control","disabled","id"=>"perc_final_price"]) !!}  
                            </div>
                        </div>
                    </div>
                  
                    <div class="col-md-4">
                        <div class="col-md-6">
                            <div class="form-group" style="font-size:15px">
                                {!! Form::radio("price",null, false,["class"=>"price-radio input-check","id"=>"change_to_price"]) !!}
                                {!! Form::label("change_to_price",__("Multiply the price by the unit conversion factor")) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="font-size:15px">
                                {!! Form::label("type_of_unit",__("Type Of Unit")) !!}
                                {!! Form::select("unit_final",[],null,["class"=>"change_to_price price-type form-control select2","disabled","id"=>"unit_final","placeholder"=>__('messages.please_select')]) !!}  
                            </div>
                        </div>
                         
                        
                        
                    </div>
                    
                      {{--  Round to a number in Math   --}}
                      <div class="row">
                          <div class="col-md-4">
                             <div class="col-md-4">
                                <div class="form-group" style="font-size:15px">
                                    {!! Form::radio("price",null, false,["class"=>"price-radio input-check ","id"=>"fixed_price_rd"]) !!}
                                    {!! Form::label("fixed_price_rd",__("Change every price")) !!}
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="col-md-5">
                                    <div class="form-group" style="font-size:15px">
                                        {!! Form::number("from_price",0,["class"=>"fixed_price_rd  price-type form-control","disabled","id"=>"from_price"]) !!}  
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    {!! Form::label("fixed_price",__("To")) !!}
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group" style="font-size:15px">
                                        {!! Form::number("to_price",0,["class"=>"fixed_price_rd price-type form-control","disabled","id"=>"to_price"]) !!}  
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                            <div class="col-md-6 ">
                                <div class="col-md-6">
                                    <div class="form-group" style="font-size:15px">
                                        {!! Form::label("round_to_price",__("Round price to")) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" style="font-size:15px">
                                        {!! Form::select("round_final",[],null,["class"=>"round_to_price round-price-type form-control select2","disabled","id"=>"round_final","placeholder"=>__("messages.please_select")]) !!}  
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" style="font-size:15px">
                                        {!! Form::label("type_of_round",__("Type Of Round")) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" style="font-size:15px">
                                        {!! Form::select("type_round",[],null,["class"=>"type_of_round round-price-type form-control select2","disabled","id"=>"type_round","placeholder"=>__("messages.please_select")]) !!}  
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    </div>
             @endcomponent
        @endcomponent
        <div class="row">
            <div class="col-md-2 text-center pull-right">
                <button type="submit" value="submit" class="btn btn-primary ">@lang('messages.save')</button>
            </div>
        </div>
    </section>
@endsection

@section("javascript")
    <script type="text/javascript" >
    
    // 1......... For more than condition
    $("#conditions").on('ifChecked',  function() {
            $(".cond").removeClass("hide");
            $(".word").attr("disabled",false);
            $(".min_price").attr("disabled",false);
            $(".max_price").attr("disabled",false);
    });
    $("#conditions").on('ifUnchecked', function() {
            $(".cond").addClass("hide");
            $(".word").attr("disabled",true);
            $(".min_price").attr("disabled",true);
            $(".max_price").attr("disabled",true);
           
    });
    
    
    //  2..............  For Particular Condition
    $(".price-radio").each(function() {
        $(this).on("change",function(){
            var e          = $(this);
            var name       = $(this).attr("id");
            var class_name = "."+$(this).attr("id");
            if($(this).data("name") == "percent" || $(this).data("name") ==  "multiple"){
                $(".round-price-type").attr("disabled",false);
            }else{
                $(".round-price-type").attr("disabled",true);
            }
            $(this).parent().parent().parent().find(class_name).attr("disabled",false) ;
                $(".price-type").each(function(){
                    if(!$(this).hasClass(name)){
                        $(this).attr("disabled",true);
                    }
                });
        })
    });
   
    </script>
@endsection