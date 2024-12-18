<tr>
    <td>
        @if($product->image_url)
        <a href="{{ URL::to($product->image_url) }}" target="_blank">
        <img src="{{$product->image_url}}" alt="Product image" style="width: 100px;height: 100px">
        </a>
        @else 
            @lang("home.without_image")   
        @endif    
    </td>
    <td><button class="btn btn-modal btn-link" data-container=".view_modal" data-href="{{action("ProductController@view",[$product->id])}}"> {{$product->product}}
        @if($product->variationname !=='DUMMY')
            -{{$product->variationname}} </button></td>
        @endif

        @if($price == 1)
            @php
                if($until_date != null){
                    $pro   = \App\Models\ItemMove::orderBy("id","desc")->where("created_at","<=",$until_date)->where("product_id",$product->id)->first();
                }else{
                    $pro   = \App\Models\ItemMove::orderBy("id","desc")->where("product_id",$product->id)->first();
                }
                if(!empty($pro)){
                    
                        $po = $pro->unit_cost;
                }else{
                        $po =  0;
                        // $po =  \App\Product::product_cost($product->id);
                } 
                $total = \App\Models\WarehouseInfo::where("product_id",$product->id)->sum("product_qty");
            @endphp
        @else
            @php 
                $rod = \App\Variation::where("product_variation_id",$product->id)->first();
                if(!empty($rod)){
                    $po = $rod->default_sell_price;
                }else{
                    $po =  0;
                } 
                $total = \App\Models\WarehouseInfo::where("product_id",$product->id)->sum("product_qty");
            @endphp
        @endif
                @php
                    $total_price += $po;
                    $total_sum   += $total;
                    $total_final += $po * $total;
                @endphp
        <td> {{round($total,2)}} </td>
        <td>   @format_currency(round($po,2))  </td>
        <td class="display_currency" data-currency_symbol="true">@format_currency(round($po * $total),2)) </td>
            @php
                $rec_v =  \App\Product::between_purchase_recieve($product->id);
                $del_v =  \App\Product::between_sell_deliver($product->id);
            @endphp
            <td>  {{ number_format($rec_v,2) }}  </td>
            <td>  {{ number_format($del_v,2) }} </td>
            <input class="total_price" id ="total_price"  hidden type="text" value="{{$total_final}}">
</tr>


 
