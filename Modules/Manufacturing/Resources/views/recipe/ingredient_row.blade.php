<tr>
	<td>
		<i class="fas fa-sort pull-left handle cursor-pointer" title="@lang('lang_v1.sort_order')">
		</i>&nbsp;
		<a  target="_blank" href="/item-move/{{$ingredient->id}}">
		{{$ingredient->full_name}}
		</a>
		
		@if(app("request")->input("item"))  @php   $cost_ = $product_cost @endphp @else @php  $cost_ = $ingredient->dpp_inc_tax @endphp @endif
		<input type="hidden" class="ingredient_price" value="{{$cost_}}"> 
		<input type="hidden" name="ingredients[{{$row_index}}][ingredient_id]" class="ingredient_id" value="{{$ingredient->id}}">

		<input type="hidden" name="ingredients[{{$row_index}}][sort_order]" class="sort_order" value="
			@if(!empty($ingredient->sort_order))
				{{$ingredient->sort_order}}
			@elseif(!empty($sort_order))
				{{$sort_order}}
			@endif">

		@if(!empty($ingredient->ingredient_line_id))
			<input type="hidden" name="ingredients[{{$row_index}}][ingredient_line_id]" value="{{$ingredient->ingredient_line_id}}">
		@endif

		@if(!empty($ingredient->mfg_ingredient_group_id))
			<input type="hidden" name="ingredients[{{$row_index}}][mfg_ingredient_group_id]" value="{{$ingredient->mfg_ingredient_group_id}}">
		@endif

		@if(isset($ig_index))
			<input type="hidden" name="ingredients[{{$row_index}}][ig_index]" value="{{$ig_index}}">
		@endif
	</td>
	<td>
		<div class="input-group">
			{!! Form::text('ingredients[' . $row_index . '][waste_percent]', !empty($ingredient->waste_percent) ? @num_format($ingredient->waste_percent) : 0, ['class' => 'form-control input_number waste_percent input-sm', 'placeholder' => __('lang_v1.waste_percent')]); !!}
			<span class="input-group-addon"><i class="fa fa-percent"></i></span>
		</div>
	</td>
	<td>
		<div class="@if(empty($ingredient->sub_units)) input-group @else input_inline @endif">
			{!! Form::text('ingredients[' . $row_index . '][quantity]', !empty($ingredient->quantity) ? @num_format($ingredient->quantity) : 1, ['class' => 'form-control input_number quantity input-sm', 'placeholder' => __('lang_v1.quantity'), 'required']); !!}
			<span class="@if(empty($ingredient->sub_units)) input-group-addon @endif">
				@if(!empty($ingredient->sub_units))
					<select name="ingredients[{{$row_index}}][sub_unit_id]" class="form-control input-sm row_sub_unit_id">
						@foreach($ingredient->sub_units as $key => $value)
							<option 
								value="{{$key}}"
								data-multiplier="{{$value['multiplier']}}"
								@if(!empty($ingredient->sub_unit_id) && $key == $ingredient->sub_unit_id)
									selected
								@endif
								>{{$value['name']}}
							</option>
						@endforeach
					</select>
				@else
					{!! $ingredient->unit !!}
				@endif
			</span>
		</div>
	</td>
	@if(app("request")->input("item"))  @php $cost = $product_cost;   @endphp  @else  @php   $cost = $ingredient->dpp_inc_tax @endphp @endif
	@php
		$price = !empty($ingredient->quantity) ? $ingredient->quantity *  $cost  :  $cost ;
		$price = $price * $ingredient->multiplier;
	@endphp
	{{-- <td><span class="ingredient_price">{{@num_format($price)}}</span></td> --}}
	<td><button type="button" class="btn btn-danger btn-xs remove_ingredient"><i class="fas fa-times"></i></button></td>
</tr>