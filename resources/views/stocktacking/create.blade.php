@extends('layouts.app')
@section('title', __("lang_v1.Inventory_of_stores"))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{__("lang_v1.Inventory_of_stores")}}</h1>
    <br>
        <h2>@lang("warehouse.Create_an_inventory")</h2>

    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>
 
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-lg-3 col-md-3">
            <form action="{{action('StocktackingController@store')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="formGroupExampleInput"> @lang("warehouse.Date_started")</label>
                    <input type="date" class="form-control" name="start_date"  required id="formGroupExampleInput" placeholder="">
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput"> @lang("warehouse.Date_end")</label>
                    <input type="date" class="form-control" name="end_date"  required id="formGroupExampleInput" placeholder="">
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput2">@lang("warehouse.State")</label>
                    <select class="form-control" name="status">
                        <option value="on">@lang("warehouse.OPEN")</option>
                        <option value="off">@lang("warehouse.CLOSE")</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput2">@lang("warehouse.nameW")</label>
                    <select class="form-control" name="store_id">
                        @foreach($warehouse_list_ as $key => $value)
                            <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group hide">
                    <label for="formGroupExampleInput2">@lang("location_id")</label>
                    <select class="form-control" name="location_id">
                        @foreach($business_locations as $key => $value)
                            <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="{{__("messages.save")}}">
                </div>
            </form>
        </div>

    </div>


    <div class="modal fade user_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    //Roles table
    $(document).ready( function(){
        var users_table = $('#users_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/users',
                    columnDefs: [ {
                        "targets": [4],
                        "orderable": false,
                        "searchable": false
                    } ],
                    "columns":[
                        {"data":"username"},
                        {"data":"full_name"},
                        {"data":"role"},
                        {"data":"email"},
                        {"data":"action"}
                    ]
                });
        $(document).on('click', 'button.delete_user_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_user,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                users_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
             });
        });
        
    });
    
    
</script>
@endsection
