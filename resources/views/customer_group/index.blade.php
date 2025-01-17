@extends('layouts.app')
@section('title', __( 'lang_v1.customer_groups' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><b>@lang( 'lang_v1.customer_groups' )</h1></b>
    @php $mainUrl = '/contacts?type=customer';  @endphp  
    <h5><i><b><a href="{{\URL::to($mainUrl)}}">{{ "   Contacts  >  " }}</a></b>{{ "Customer Groups"   }} <b> {{"   "}} </b></i></h5>
 
</section>

<!-- Main content -->
<section class="content"> 
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_your_customer_groups' )])
            @can('customer.create')
                @slot('tool')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal" 
                            data-href="{{action('CustomerGroupController@create')}}" 
                            data-container=".customer_groups_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endslot
            @endcan
            @can('customer.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="customer_groups_table">
                        <thead>
                            <tr>
                                <th>@lang( 'lang_v1.customer_group_name' )</th>
                                <th>@lang( 'lang_v1.calculation_percentage' )</th>
                                <th>@lang( 'lang_v1.selling_price_group' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
            @elsecan('ReadOnly.views')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="customer_groups_table">
                    <thead>
                        <tr>
                            <th>@lang( 'lang_v1.customer_group_name' )</th>
                            <th>@lang( 'lang_v1.calculation_percentage' )</th>
                            <th>@lang( 'lang_v1.selling_price_group' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
                </div>
        @elsecan('ReadOnly.views')
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="customer_groups_table">
                <thead>
                    <tr>
                        <th>@lang( 'lang_v1.customer_group_name' )</th>
                        <th>@lang( 'lang_v1.calculation_percentage' )</th>
                        <th>@lang( 'lang_v1.selling_price_group' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
            </div>
        @elsecan('warehouse.views')
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="customer_groups_table">
                <thead>
                    <tr>
                        <th>@lang( 'lang_v1.customer_group_name' )</th>
                        <th>@lang( 'lang_v1.calculation_percentage' )</th>
                        <th>@lang( 'lang_v1.selling_price_group' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
            </div>
        @endcan
        @endcomponent

    <div class="modal fade customer_groups_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
    $(document).on('change', '#price_calculation_type', function () {
        var price_calculation_type = $(this).val();

        if (price_calculation_type == 'percentage') {
            $('.percentage-field').removeClass('hide');
            $('.selling_price_group-field').addClass('hide');
        } else {
            $('.percentage-field').addClass('hide');
            $('.selling_price_group-field').removeClass('hide');
        }   
    })
</script>
@endsection
