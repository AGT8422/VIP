@extends('layouts.app')
@section('title', __('printer.Templates'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('printer.Templates')
        <small>@lang('printer.manage_your_Templates')</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('Report\PrinterSettingController@printer_setting')}}">
                <i class="fa fa-plus"></i> @lang('printer.add_template')</a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="printer_template">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('printer.name')</th>
                        <th>@lang('printer.template_type')</th>
                        <th>@lang('printer.status')</th>
                        <th>@lang('lang_v1.date')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        var printer_table = $('#printer_template').DataTable({
            processing: true,
            serverSide: true,
            buttons:[],
            ajax: '/printer/settings/list',
            // bPaginate: false,
            columnDefs: [ {
                "targets": 2,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'name_template', name: 'name_template' },
            { data: 'Form_type', name: 'Form_type' },
            { data: 'type', name: 'type' },
            { data: 'created_at', name: 'created_at' },
        ],fnDrawCallback: function(oSettings) {
      
        },"footerCallback": function ( row, data, start, end, display ) {
            
        } 
        });

        $(document).on('click', 'button.delete_printer_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_printer,
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
                            if(result.success === true){
                                toastr.success(result.msg);
                                printer_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
        $(document).on('click', 'button.set_default', function(){
            var href = $(this).data('href');
            var data = $(this).serialize();

            $.ajax({
                method: "get",
                url: href,
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success === true){
                        toastr.success(result.msg);
                        printer_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
     
    });
    function delete_module(html){
         
         swal({
             title: LANG.sure,
             icon: 'warning',
             buttons: true,
             dangerMode: true,
         }).then(willDelete => {
             if (willDelete) {
                 var href = html.attr("data-href");
                
                 $.ajax({
                     method: 'GET',
                     url: href,
                     dataType: 'json',
                     success: function(result) {
                         if (result.success == true) {
                             toastr.success(result.msg);
                            
                         } else {
                             toastr.error(result.msg);
                         }
                     },
                 });
             }
         });
         
     }
</script>
@endsection