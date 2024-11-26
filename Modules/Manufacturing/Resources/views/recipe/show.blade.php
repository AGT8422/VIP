<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">{{$recipe->variation->full_name}}</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="row">
      			<div class="col-md-6">
      				{!! $recipe->variation->product->product_description !!}
      			</div>
      			<div class="col-md-6">
      				@foreach($recipe->variation->media as $media)
			        	{!! $media->thumbnail([60, 60], 'img-thumbnail') !!}
			        @endforeach
      			</div>
      		</div>
      		<div class="row">
      			<div class="col-md-12">
      				<table class="table">
						<thead>
							<tr>
								<th>@lang('manufacturing::lang.ingredient')</th>
								<th>@lang('lang_v1.quantity')</th>
								<th>@lang('manufacturing::lang.waste_percent')</th>
								<th></th>
								{{-- <th>@lang('lang_v1.price')</th> --}}
							</tr>
						</thead>
						<tbody>
							@php
								$ingredient_groups = [];
								$ingredient_total_price = 0;
							@endphp
							@foreach($ingredients as $ingredient)
								 
								@php
									$ingredient_price = $ingredient['quantity']*$ingredient['dpp_inc_tax']*$ingredient['multiplier'];
									$ingredient_total_price += $ingredient_price;
								@endphp
								@if(empty($ingredient['mfg_ingredient_group_id']))
									<tr>
										<td>
											{{$ingredient['full_name']}}
											<br>
											@php
												$product_id = $ingredient['variation']->product_id;
											@endphp
											<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print" data-toggle="dropdown" aria-expanded="false">@lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">
												<li><a data-href="{{action('ProductController@view', [ $product_id])}}" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye"></i>@lang("messages.view")</a></li>
												<li><a href="{{action('ProductController@edit', [ $product_id])}}"><i class="glyphicon glyphicon-edit"></i>@lang("messages.edit")</a></li>
												<li><a href="{{action('ItemMoveController@index', [$product_id])}}"><i class="fas fa-history"></i>@lang("lang_v1.product_stock_history")</a></li>
												</ul>
												<button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print"   data-container=".view_modal" data-href="{{action('ProductController@viewStock', [$product_id])}}">@lang('lang_v1.view_Stock')</button> 
												<button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".view_modal" data-href="{{action('ProductController@viewUnrecieved', [$product_id])}}">@lang('recieved.should_recieved')</button> 
											</div>
										</td>
										<td><span class="display_currency" data-currency_symbol="false">{{$ingredient['quantity']}}</span> {{$ingredient['unit']}}</td>
										<td colspan="2"><span class="display_currency" data-currency_symbol="false">{{$ingredient['waste_percent']}}</span>%</td>
										
										{{-- <td><span class="display_currency" data-currency_symbol="true">{{$ingredient_price}}</span></td> --}}
									</tr>
								@else
									@php
										$ingredient_groups[$ingredient['mfg_ingredient_group_id']][] = $ingredient;
									@endphp
								@endif	
							@endforeach
							@foreach($ingredient_groups as $ingredient_group)
								<tr>
									<td colspan="4" class="bg-gray"><strong>{{$ingredient_group[0]['ingredient_group_name'] ?? ''}}</strong> @if(!empty($ingredient_group[0]['ig_description']))
									- {{$ingredient_group[0]['ig_description']}}
								@endif</td>
								</tr>
								
								@foreach($ingredient_group as $ingredient)
									<tr>
										<td>
											{{$ingredient['full_name']}}
										</td>
										<td><span class="display_currency" data-currency_symbol="false">{{$ingredient['quantity']}}</span> {{$ingredient['unit']}}</td>
										<td><span class="display_currency" data-currency_symbol="false">{{$ingredient['waste_percent']}}</span>%</td>
										<td><span class="display_currency" data-currency_symbol="true">{{$ingredient['quantity']*$ingredient['dpp_inc_tax']*$ingredient['multiplier']}}</span></td>
									</tr>
								@endforeach
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								{{-- <td colspan="1" class="text-right"><strong>@lang('manufacturing::lang.ingredients_cost')</strong></td> --}}
								{{-- <td><span class="display_currency" data-currency_symbol="true">{{$ingredient_total_price}}</span></td> --}}
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						</tfoot>
					</table>
      			</div>
      		</div>
      		<div class="row">
      			<div class="col-md-6">
      				<strong>@lang('manufacturing::lang.wastage'):</strong>
      				{{$recipe->waste_percent ?? 0}} % <br>
      				<strong>@lang('manufacturing::lang.total_output_quantity'):</strong>
      				@if(!empty($recipe->total_quantity)){{@format_quantity($recipe->total_quantity)}}@else 0 @endif @if(!empty($recipe->sub_unit)) {{$recipe->sub_unit->short_name}} @else {{$recipe->variation->product->unit->short_name}} @endif
      			</div>
      			<div class="col-md-6 ">
      				<strong>@lang('manufacturing::lang.extra_cost'):</strong>
      				<span ></span>{{@num_format($recipe->extra_cost)}}% <br>
      				@php
      					$final_price = $ingredient_total_price;
      					if(!empty($recipe->extra_cost)) {
      						$final_price = $final_price + ($final_price * $recipe->extra_cost / 100);
      					}
      				@endphp
      				<strong class="hide">@lang('sale.total'):</strong>
      				<span class="hide" class="display_currency" data-currency_symbol="true">{{$final_price}}</span>
      			</div>
      		</div>
      		<div class="row">
      			<div class="col-md-12">
      				<strong>@lang('lang_v1.instructions'):</strong><br>
      				{!! $recipe->instructions !!}
      			</div>
      		</div>
      	</div>
      	<div class="modal-footer">
      		<button type="button" class="btn btn-primary no-print" aria-label="Print" 
			      onclick="$(this).closest('div.modal-content').printThis();"><i class="fa fa-print"></i> @lang( 'messages.print' )
			</button>
	      	<button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	</div>
</div>
