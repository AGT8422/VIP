@component("components.widget",["title" => __("home.abbreviations") ,"class" => "abb_"])
<section class="content">
    @include('home.language')
    @if(auth()->user()->can('manufuctoring.views') )
        <div class="row">
            <div class="col-lg-2 backItem">
                <a href="/gallery/stock_report" class=" cont1 text-center" >
                    <h2><i class="fa fas fa-chart-bar"></i></h2>
                    <h2>@lang('report.stock_report')   </h2>
                </a>
            </div>
            <div class="col-lg-2 backItem">
                <a href="/item-move/show/1" class=" cont1 text-center" >
                    <h2><i class="fa fas fa-shopping-bag"></i></h2>
                    <h2>@lang('lang_v1.Item Movement')   </h2>
                </a>
            </div>
            <div class="col-lg-2 backItem">
                <a href="/manufacturing/recipe" class=" cont1 text-center" >
                    <h2><i class="fa fas fa-shopping-bag"></i></h2>
                    <h2>@lang('manufacturing::lang.recipe')   </h2>
                </a>
            </div>
            <div class="col-lg-2 backItem">
                <a href="/manufacturing/production" class=" cont1 text-center" >
                    <h2><i class="fa fas fa-shopping-bag"></i></h2>
                    <h2>@lang('manufacturing::lang.production')   </h2>
                </a>
            </div>
            <div class="col-lg-2 backItem">
                <a href="/warehouse/movement" class=" cont1 text-center" >
                    <h2><i class="fa fas fa-shopping-bag"></i></h2>
                    <h2>@lang('warehouse.Warehouse_Movement')   </h2>
                </a>
            </div>
            <div class="col-lg-2 backItem">
                <a href="/warehouse" class=" cont1 text-center" >
                    <h2><i class="fa fas fa-shopping-bag"></i></h2>
                    <h2>@lang('warehouse.show_warehouse')   </h2>
                </a>
            </div>
        </div>
    @endif
</section>

<hr>
 
@endcomponent