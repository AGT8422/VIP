
@foreach($products as $product)
    {{-- @php dd($products); @endphp --}}
    <div class="col-md-3 col-sm-12 col-xs-12 produc-div">
        <div style="position: relative">
            <div style="position: relative;list-style-type: none">
                @if($from=='inventory')
                    <button data-href="{{action("ProductController@view",[$product->id])}}" class="a-product btn-modal" data-container=".view_modal">
                        @else
                            <a data-href="{{action("ProductController@view",[$product->id])}}" class="a-product btn-modal" data-container=".view_modal">
                        @endif

                   <div class="product-1">

                       <div class="product-2">
                           <div class="product-3">
                               <div class="product-4">
                                   <img src="{{$product->image_url}}" alt="Product image" class="product-image2">
                               </div>
                           </div>
                       </div>
                   </div>

                   <div class="product-footer">
                       <div class="product-name" >
                           <div class="product-name-1" >
                               <div class="product-name-2">
                                   <span class="product-name-span" dir="auto">{{$product->product}}</span>
                               </div>
                           </div>
                       </div>
                     
                            
                        <div class="product-price" >
                            <div class="product-name-1" >
                                <div class="product-name-2 ">
                                     
                                    <span class="product-price-span" dir="auto">@lang("lang_v1.cost_sells")  :  {{ number_format($product->sell_price_inc_tax,2 )}} </span>
                                </div>
                            </div>
                        </div>
                        <div class="product-qty" >
                            <div class="product-name-1" >
                                <div class="product-name-2 ">
                                    @php $total = \App\Models\WarehouseInfo::where("product_id",$product->id)->sum("product_qty"); @endphp
                                    <span class="product-price-span" dir="auto">@if( $total == 0)@lang("lang_v1.Not Available quantity") @else @lang("lang_v1.Available quantity")  :  {{$total}} @endif </span>
                                </div>
                            </div>
                        </div>
                            
                   </div>



                </a>
            </div>

        </div>
     </div>
    @endforeach