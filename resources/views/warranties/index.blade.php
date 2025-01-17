@extends('layouts.app')
@section('title', __('lang_v1.warranties'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><b>@lang('lang_v1.warranties')</b>
    </h1>
    <h5><i><b>{{ "   Products  >  " }} </b>  {{ "   Warranties     " }} <b> {{"   "}} </b></i></h5>  
	<br> 
</section>

<!-- Main content -->
<section class="content"> 
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_warranties' )])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action('WarrantyController@create')}}" 
                        data-container=".view_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
            <table class="table table-bordered table-striped dataTable" id="warranty_table">
                <thead>
                    <tr>
                        @if(isset($type))<th>@lang( 'purchase.ref_no' )</th>@endif
                        @if(isset($type))<th>@lang( 'lang_v1.id' )</th>@endif
                        <th>@lang( 'lang_v1.name' )</th>
                        <th>@lang( 'lang_v1.description' )</th>
                        <th>@lang( 'lang_v1.duration' )</th>
                        @if(isset($type))<th>@lang( 'lang_v1.parent_id' )</th>@endif
                        @if(isset($type))<th>@lang( 'lang_v1.state_action' )</th>@endif
                        @if(isset($type))<th>@lang( 'lang_v1.added_by' )</th>@endif
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
        @endcomponent 


</section>
<!-- /.content -->
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        @if(isset($type)) 
             var url = "{{action('ArchiveTransactionController@warranties')}}" ;
             var col = [
                    { data: 'ref_number', name: 'ref_number' },
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'duration', name: 'duration' },
                    { data: 'parent_id', name: 'parent_id' },
                    { data: 'state_action', name: 'state_action' },
                    { data: 'user_id', name: 'user_id' },
                    { data: 'action', name: 'action' },
                ]
        @else 
                var url =  "{{action('WarrantyController@index')}}" ; 
                var col = [
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'duration', name: 'duration' },
                    { data: 'action', name: 'action' },
                   ]
        @endif
        //Status table
        var warranty_table = $('#warranty_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: url,
                columnDefs: [ {
                    "targets": 3,
                    "orderable": false,
                    "searchable": false
                } ],
                columns: col
            });

        $(document).on('submit', 'form#warranty_form', function(e){
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success == true){
                        $('div.view_modal').modal('hide');
                        toastr.success(result.msg);
                        warranty_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
    });
</script>
@endsection
