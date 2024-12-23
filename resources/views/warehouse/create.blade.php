@extends('layouts.app')
@section('title', __('warehouse.add'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('warehouse.add') }}</h1>
    <br>
    {{-- <h2> {{ __('warehouse.add_info') }} </h2> --}}

    {{-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> --}}
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-lg-12 col-md-12 ">
            <form action="{{action('WarehouseController@store')}}" method="POST">
                @csrf
                <div class="form-group col-lg-12">
                    <label for="formGroupExampleInput">@lang('purchase.name') :*</label>
                    <input type="text" class="form-control" name="store_name"  required id="formGroupExampleInput" placeholder="">
                </div>
                
                <div class="form-group col-lg-6 hide">
                    <label for="formGroupExampleInput2"> @lang("purchase.business_location") </label>
                    <select class="form-control" name="location_id">
                        @foreach($business_locations as $key => $value)
                        <option value="{{$business_id}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-12">
                    <label for="formGroupExampleInput2">  {{  trans('home.Main Store') }} </label>
                    {{  Form::select('parent_id',$parents,null,['class'=>'form-control','placeholder'=>trans('home.please choose the main store if it exists')]) }}
                </div>
                <div class="form-group col-lg-12">
                    <label for="formGroupExampleInputText">@lang('purchase.additional_notes')  {{" *"}}</label>
                    <textarea type="text" class="form-control" name="descript" required   id="formGroupExampleInputText"  placeholder=""></textarea>  
                </div>
                @if(!empty( $mainstore_categories))
                {{-- <div class="form-group col-lg-6">
                    <div class="checkbox">
                            <label>
                            {!! Form::checkbox('add_as_sub_cat', 1, false,[ 'class' => 'toggler' , "name" => "check", "id" => "uncheck", 'data-toggle_id' => 'parent_cat_div' ]); !!} @lang( 'lang_v1.add_as_sub_store' )
                        </label>
                    </div>
                </div> --}}
                {{-- <div class="form-group col-lg-6">
                    <div class="checkbox">
                            <label>
                            {!! Form::checkbox('add_as_sub_cat1', 1, false,[ 'class' => 'toggler1' , "name" => "check1", "id" => "uncheck1", 'data-toggle_id' => 'parent_cat_div',"checked" => "checked" ]); !!} @lang( 'lang_v1.add_as_main_store' )
                        </label>
                    </div>
                </div>  --}}
                
                @endif
                <div class="form-group col-lg-12">
                    <input type="submit" class="btn btn-primary" value="@lang('lang_v1.save_button')">
                </div>
            </form>
        </div>

    </div>


    <div class="modal fade user_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
@stop
@section('javascript')
<script type="text/javascript">


    //Roles table
    
    // $(document).ready( function(){
    //     $('#uncheck').on('click', function(event) {
    //         var target = event.target;
    //         $('#uncheck1').prop('checked', false);
    //         $('#parent_cat_div').addClass("hide");
    //         $('#parent_cat_div').attr('hidden', null);
    //         });
    //     $('#uncheck1').on('click', function(event) {
    //         var target = event.target;
    //         $('#uncheck').prop('checked', false);
    //         $('#parent_cat_div').attr('hidden', "hidden");
            
    //         });
    // });
    
</script>
@endsection

