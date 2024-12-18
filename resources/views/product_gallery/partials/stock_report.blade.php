
@php
    $var = 1;
@endphp
<table class="table table-bordered table-striped table-bordered dataTable" id="inventory">
    <thead>
    <tr>
        <th></th>
        <th>@lang('product.product_name')</th>
        <th>@lang('purchase.qty_current')</th>
        <th>@lang('lang_v1.price')</th>
        <th>@lang('sale.total')</th>
        <th>@lang('recieved.should_recieved')</th>
        <th>@lang('recieved.should_delivery')</th>

    </tr>
    </thead>
    <tbody class="ingredient-row-sortable">
        @php
             
            $total_sum   = 0 ;
            $total_price = 0 ;
            $total_final = 0 ;
           
        @endphp
    @foreach($products as $product)
        @php
            $tot = 0;
            if($store_id == null){
                if($main_store == null){
                    $tot = \App\Models\WarehouseInfo::where("product_id",$product->id)->sum("product_qty");
                }else{
                    $st = \App\Models\Warehouse::find($main_store);
                    $ids = \App\Models\Warehouse::where("mainStore",$st->name)->get();
                    if(count($ids)>0){
                        foreach ($ids as  $it) {
                            $tot += \App\Models\WarehouseInfo::where("store_id", $it->id)->where("product_id",$product->id)->sum("product_qty");
                        }
                     }else{
                        $tot = \App\Models\WarehouseInfo::where("product_id",$product->id)->sum("product_qty");
                    }
                }
             }else{
                 if($main_store == null){
                    $tot = \App\Models\WarehouseInfo::where("store_id",$store_id)->where("product_id",$product->id)->sum("product_qty");
                }else{
                    $st = \App\Models\Warehouse::find($main_store);
                    $ids = \App\Models\Warehouse::where("mainStore",$st->name)->get();
                    if(count($ids)>0){
                        $tot = \App\Models\WarehouseInfo::whereIn("store_id", $ids->pluck("id"))->where("product_id",$product->id)->sum("product_qty");
                    }else{
                        $tot = \App\Models\WarehouseInfo::where("store_id", $store_id)->where("product_id",$product->id)->sum("product_qty");
                    }
                    
                }
            }
        @endphp
        @if($product_available == null)
                @include("product_gallery.partials.block_stock",["product"=>$product,"tot"=>$tot,"price"=>$price,"until_date"=>$until_date,"total_final"=>$total_final,"total_price"=>$total_price,"total_sum"=>$total_sum])
                @php $total_sum  +=   $tot; @endphp
        @elseif($product_available == 1)
            @if($tot>0 || $tot<0)
                @include("product_gallery.partials.block_stock",["product"=>$product,"tot"=>$tot,"price"=>$price,"until_date"=>$until_date,"total_final"=>$total_final,"total_price"=>$total_price,"total_sum"=>$total_sum])
                @php $total_sum  +=   $tot; @endphp
            @endif
        @elseif($product_available == 0)
            @if($tot<=0)
                @include("product_gallery.partials.block_stock",["product"=>$product,"tot"=>$tot,"price"=>$price,"until_date"=>$until_date,"total_final"=>$total_final,"total_price"=>$total_price,"total_sum"=>$total_sum])
                @php $total_sum  +=   $tot; @endphp
            @endif
        @elseif($product_available == 2)
            @php
               $rec_v =  \App\Product::between_purchase_recieve($product->id);
               $del_v =  \App\Product::between_sell_deliver($product->id);
               $total_sum += $tot;
            @endphp
            @if($del_v>0)
                @include("product_gallery.partials.block_stock",["del_v"=>$del_v,"rec_v"=>$rec_v,"product"=>$product,"tot"=>$tot,"price"=>$price,"until_date"=>$until_date,"total_final"=>$total_final,"total_price"=>$total_price,"total_sum"=>$total_sum])
            @endif
        @elseif($product_available == 3)
            @php
              $rec_v      =  \App\Product::between_purchase_recieve($product->id);
              $del_v      =  \App\Product::between_sell_deliver($product->id);
              $total_sum += $tot;
            @endphp
            @if($rec_v>0)
                @include("product_gallery.partials.block_stock",["del_v"=>$del_v,"rec_v"=>$rec_v,"product"=>$product,"tot"=>$tot,"price"=>$price,"until_date"=>$until_date,"total_final"=>$total_final,"total_price"=>$total_price,"total_sum"=>$total_sum])
            @endif
        @endif
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"></td>
            <td colspan="1">@lang("purchase.qty") : {{$total_sum}}</td>
            <td colspan="1"></td>
            <td colspan="1">@format_currency($total_final)</td>
            <td colspan="1"></td>
            <td colspan="1"></td>
            
        </tr>
    </tfoot>
</table>
<script type="text/javascript">
    $(document).ready(function(){
           
            
     });
</script>
 





