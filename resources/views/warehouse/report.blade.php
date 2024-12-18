@extends('layouts.app')
@section('title', __('warehouse.report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('warehouse.report') }}</h1>
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
        <div class="col-lg-3 col-md-3">
            <form action="" method="POST">
                @csrf
                <div class="form-group">
                    <label for="formGroupExampleInput"> تاريخ البدء</label>
                    <input type="date" class="form-control" name="start_date"  required id="formGroupExampleInput" placeholder="">
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput"> تاريخ الغلق</label>
                    <input type="date" class="form-control" name="end_date"  required id="formGroupExampleInput" placeholder="">
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput2"> الحالة</label>
                    <select class="form-control" name="status">
                        <option value="on">فتح </option>
                        <option value="off">غلق</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput2"> الفرع </label>
                    <select class="form-control" name="location_id">
                        @foreach($business_locations as $key=>$value)
                            <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="حفظ">
                </div>
            </form>
        </div>

    </div>


    <div class="modal fade user_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
@stop