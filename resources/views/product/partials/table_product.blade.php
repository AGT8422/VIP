<div class="row">
    <div class="col-md-12">

       <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#product_list_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cubes" aria-hidden="true"></i> @lang('lang_v1.all_products')</a>
                </li>
                @can('stock_report.view')
                <li>
                    <a href="#product_stock_report" data-toggle="tab" aria-expanded="true"><i class="fa fa-hourglass-half" aria-hidden="true"></i> @lang('report.stock_report')</a>
                </li>
                @endcan
            </ul>



            <div class="tab-content">
                <div class="tab-pane active" id="product_list_tab">

                    {{--@can('product.update_all_prices')

                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-warning pull-right" data-toggle="modal" data-target="#update_all_prices">
                        تعديل كل اسعار البيع
                        </button>

                    @endcan--}}
                    <br><br>
                    @include('product.partials.product_list')
                    {{--@include('product.update_all_prices')--}}
                </div>

                <div class="tab-pane " id="product_stock_report">

                    @can('stock_report.view')

                            @include('report.partials.stock_report_table')

                    @endcan
                </div>

            </div>
        </div>
    </div>
</div>