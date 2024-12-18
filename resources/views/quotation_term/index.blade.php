@extends('layouts.app')
@section('title', __('lang_v1.terms'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.terms') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
</section>



<!-- Main content -->
 
    <section class="content  no-print"> 
        
    @component("components.filters" , ["title"=>__('report.filters')])
        
        
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('q_name',  __('business.name') . ':') !!}
            {!! Form::select('q_name', $q_name, null, ['class' => 'form-control select2', 'id'=>'q_name', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>


    @endcomponent
        @component("components.widget" , ["title"=>__("lang_v1.terms")])
        {{-- @can('purchase.create') --}}
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('QuotationController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        {{-- @endcan --}}
        <table class="table table-bordered  table-striped ajax_view" id="quatation_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang( 'lang_v1.name' )</th>
                    <th>@lang( 'lang_v1.description' )</th>
                    <th>@lang('messages.date')</th> 
                </tr>
            </thead>
            <tfoot> 
                <tr class="bg-gray font-17 text-center footer-total">
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>
        @endcomponent
 </section>
 
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
 
<!-- /.content -->
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        //Status table
        var quatation_table = $("#quatation_table").DataTable({
        processing:     true,
        serverSide:     true,
        scrollY:      "75vh",
        scrollX:        true,
        scrollCollapse: true,
        "ajax": {
            "url": "/sells/terms",
            "data":  function ( d ) {
                d.name    = $("#q_name").val();
                d = __datatable_ajax_callback(d);
            }
        },
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'actions', searchable: false, orderable: false },
            { data: 'name', name: 'name' ,orderable: true },
            { data: 'description', name: 'description',orderable: true  },
            { data: 'created_at', name: 'created_at', orderable: true },
            
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#quatation_table'));
        },
        // "footerCallback": function ( row, data, start, end, display ) {}
    });

    $('table#quatation_table tbody').on('click', 'a.delete-term', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                $.ajax({
                    method: 'get',
                    url: href,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == 1) {
                            toastr.success(result.msg);
                            quatation_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
    $(document).on("change","#q_name",function(){
                quatation_table.ajax.reload();
    });
});    
    

// alert($('table#patterns_table tbody a.delete-term').html());



</script>
@endsection
