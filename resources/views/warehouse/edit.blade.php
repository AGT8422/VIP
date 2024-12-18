@extends('layouts.app')
@section('title', __('warehouse.edit'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('warehouse.edit') }}</h1>
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
            {{-- <form action="{{action('WarehouseController@update_id',[$id])}}" method="POST"> --}}
            @php $path = "/warehouse/update/".$id @endphp 
            <form action="{{\URL::to($path)}}" method="POST">
                @csrf
                <div class="form-group col-lg-12">
                    <label for="formGroupExampleInput">@lang('purchase.name') :*</label>
                    <input type="text" class="form-control" data-typ="{{$sourceType}}" name="store_name" value="{{$source}}"  required id="formGroupExampleInput" placeholder="">
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
                    {{  Form::select('parent_id',$parents,$data->parent_id,['class'=>'form-control','placeholder'=>trans('home.please choose the main store if it exists')]) }}
                </div>
                <input type="hidden"  value="{{ $data->id }}" name="store_id" >
                <div class="form-group col-lg-12">
                    <label for="formGroupExampleInput">@lang('purchase.additional_notes')</label>
                    <textarea type="text" class="form-control" name="descript"  id="formGroupExampleInputText"  placeholder="">{{$data->description}}</textarea>  
                </div>
                
               
                <div class="form-group col-lg-12">
                    <input type="submit" class="btn btn-primary" value="@lang('messages.update')">
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
    
    $(document).ready( function(){
        $('#uncheck').on('click', function(event) {
            var target = event.target;
            $('#uncheck1').prop('checked', false);
            $('#parent_cat_div').addClass("hide");
            $('#parent_cat_div').attr('hidden', null);
            });
        $('#uncheck1').on('click', function(event) {
            var target = event.target;
            $('#uncheck').prop('checked', false);
            $('#parent_cat_div').attr('hidden', "hidden");
            
            });

        if($('#formGroupExampleInput').data('typ') != 0){
            $('#uncheck1').prop('checked', false);
            $('#uncheck').prop('checked', true);
            $('#parent_cat_div').removeClass("hide");
            // $('#parent_cat_div').attr('hidden', null);
        }
            
            





    });
    
</script>
@endsection

