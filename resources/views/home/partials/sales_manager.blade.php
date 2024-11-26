@component("components.widget",["title" => __("home.abbreviations") ,"class" => "abb_"])
<section class="content">
    @include('home.language')
    @if(auth()->user()->can('SalesMan.views') )
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
                <a href="/reports/product-sell-return-report" class=" cont1" >
                    <h2><i class="fa fa-undo-alt"></i></h2>
                    <h3>@lang('lang_v1.sell_return')  </h3>
                </a>
            </div>
            <div class="col-lg-2 backItem">
                <a href="/sells" class=" cont1" >
                    <h2><i class="fa fa-registered"></i></h2>
                    <h3>@lang('lang_v1.sells')  </h3>
                </a>
            </div>
            <div class="col-lg-2 backItem">
                <a href="/sells/create" class=" cont1" >
                    <h2><i class="fa fa-plus"></i></h2>
                    <h3>@lang('lang_v1.Create_sells')  </h3>
                </a>
            </div>

            <div class="col-lg-2 backItem">
                <a href="/contacts?type=customer" class=" cont1" >
                    <h2><i class="fa fas fa-address-book"></i></h2>
                    <h3>  @lang('lang_v1.customers')</h3>
                </a>
            </div>
          
            
             
        </div>
    @endif
</section>

<hr>
 
@endcomponent