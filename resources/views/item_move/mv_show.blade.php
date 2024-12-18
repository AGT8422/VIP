 
{{-- @php dd("ssss"); @endphp --}}
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('home.ItemMove') </h1>
    <h5><i>  <b>  {{ " ( " . $product->name . " ) " }}  </b></i></h5>
    <br> 
</section>

<section class="content">
    
	
    <input type="hidden" id="product_id" value="{{$product->id}}">
    @component("components.widget",["title"=>__("home.movement"),"class"=> "box-primary"])
        <div class="row">
            <div class="col-xs-12">
                <div class="table">
                    <table class="table table-stripted table-bordered dataTable" id="item_move">
                            <thead>
                                <tr>
                                    <td>@lang("lang_v1.date")</td>
                                    <td>@lang("home.account")</td>
                                    <td>@lang("home.state")</td>
                                    <td>@lang("purchase.ref_no")</td>
                                    <td>@lang("purchase.qty") (+)</td>
                                    <td>@lang("purchase.qty") (-)</td>
                                    <td>@lang("warehouse.nameW") </td>
                                    @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                                        <td>@lang("home.row_price")</td>
                                        <td>@lang("home.row_price_inc_exp")</td>
                                        <td>@lang("home.unit_cost")</td>
                                    @endif
                                    <td>@lang("home.current_qty")</td>
                                    <td>@lang("home.name")</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                                        <td colspan="7" id="footer_total"></td>
                                        <td colspan="2"></td>
                                        <td colspan="3"> @lang("home.unit_cost") : &nbsp;@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')) @format_currency($movment_cost)  @else {{ "--" }} @endif</td>
                                    @else
                                        <td colspan="8"></td>
                                        <td colspan="1"> @lang("home.unit_cost") : &nbsp;@if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost')) @format_currency($movment_cost)  @else {{ "--" }} @endif</td>
                                    @endif
                                </tr>
                            </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endcomponent
</section>

 
 
<script src="{{ asset('js/functions.js?v=' . $asset_v) }} "></script>
  {{-- <script src="{{ asset('js/item_move.js?v=' . $asset_v) }} "></script> --}}
  <script  type="text/javascript">
           
    $(document).on('click', '.btn-modal', function(e) {
        e.preventDefault();
        var container = $(this).data('container');
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function(result) {
                $(container)
                    .html(result)
                    .modal('show');
            },
        });
    });
    var product_id = $("#product_id").val();
    item_move  = $('#item_move').DataTable({
    
        processing: true,
        serverSide: true,
        ajax:{
            url:"/item-move/"+product_id ,
            data:function(d){
                d.product_id = $("#product_id").val();
                d = __datatable_ajax_callback(d);
            }
        },
        aaSorting:[0,"desc"],
        columns:[
            {data:"created_at",name:"created_at"},
            {data:"product",name:"product"},
            {data:"account",name:"account"},
            {data:"state",name:"state"},
            {data:"references",name:"references"},
            {data:"qty_plus",name:"qty_plus"},
            {data:"qty_minus",name:"qty_minus"},
            @if(auth()->user()->hasRole('Admin#' . session('business.id')) ||  auth()->user()->can('product.avarage_cost'))
                {data:"row_price",name:"row_price" , class:"hi"},
                {data:"row_price_inc_exp",name:"row_price_inc_exp" , class:"hi"},
                {data:"unit_cost",name:"unit_cost",class:"hi"},
            @endif
            {data:"current_qty",name:"current_qty"},
            {data:"store_id",name:"store_id"},
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#item_move'));
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var total_plus  = 0;
            var total_minus = 0;
            for (var r in data){
                console.log(data[r].qty_minus);
                total_plus  += parseFloat(data[r].qty_plus) ;
                total_minus += parseFloat(data[r].qty_minus) ;
            }
            $('#footer_total').html(LANG.qty + " : " + (total_plus - total_minus));
            add_attribute();
            
        }

    });
        function add_attribute(){

            var table = $('#item_move').DataTable();
        
            // Loop through each row in the table
            table.rows().every(function() {
                // Get the DOM element of the row
                var rowNode = this.node();
                // console.log($(rowNode));
                $(rowNode).find(".hi").addClass('display_currency');
                $(rowNode).find(".hi").attr('data-currency_symbol', true );
            
                
            });
        }
</script>
 