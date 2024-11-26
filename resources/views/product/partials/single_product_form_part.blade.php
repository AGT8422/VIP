@if(!session('business.enable_price_tax')) 
  @php
    $default = 0;
    $class = 'hide';
  @endphp
@else
  @php
    $default = null;
    $class = '';
  @endphp
@endif
@php $count =0 ;$ks =0; $ind=0;  @endphp
{{-- Whole PriceRetail PriceLast PriceMinimum Price --}}
<div class="table-responsive">
  @if(isset($other_unit))
      @foreach($other_unit as $k => $i)
        @php  $ks = $ks+1; $count++ ; $list_new=[];  @endphp
         @foreach($units_main as $n => $row_line)
            @if($i == $n)
              @php 
                $ind = $n;
                
                $list_new[$n] = $row_line; 
              @endphp
            @endif
        @endforeach 
        {!! Form::select('unit_D[]', $list_new,  (isset($list_unit[$ind]))?$ind:null  , ['class' => 'form-control input-sm width-10 dpp un_select input_number select2',"data-id"=>$k,'required',"style"=>"width:10%" ,'id'=>'unit_D'     ]); !!}
        <table class="table table-bordered add-product-price-table table-condensed {{$class}}">
          <tr>
            <th>@lang("home.Type Price")</th>
            <th>@lang('product.default_purchase_price')</th>
            <th>@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
            <th>@lang('product.default_selling_price')</th>
          </tr>
          <tr   >
            <td style="vertical-align: bottom">
              {{__('home.Default Price')}}
            </td>
            <td>
              <div class="col-sm-6">
                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}

                {!! Form::text('single_dpp'.$ks.'[]', $default, ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp".$ks, 'placeholder' => __('product.exc_of_tax')  ]); !!}
              </div>

              <div class="col-sm-6">
                {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
              
                {!! Form::text('single_dpp_inc_tax'.$ks.'[]', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax".$ks, 'placeholder' => __('product.inc_of_tax') ]); !!}
              </div>
            </td>
            <td>
              <br/>
              {!! Form::text('profit_percent'.$ks.'[]', @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$ks   ]); !!}
            </td>
            <td>
                <div class="col-sm-6">
                    {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                    {!! Form::text('single_dsp'.$ks.'[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$ks   ]); !!}
                </div>
                <div class="col-sm-6">
                {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                {!! Form::text('single_dsp_inc_tax'.$ks.'[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$ks ]); !!}
                </div>
            </td>
          </tr>
          @foreach($product_price as $i)
            @if($i->product_id == null)
              
                <tr @if($i->name  == "ECM After Price" && $i->name == "ECM Before Price") class= "hide " @endif >
                  <td style="vertical-align: bottom">
                    {{$i->name}}
                  </td>
                  <td>
                    <div class="col-sm-6">
                      {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}

                      {!! Form::text('single_dpp'.$ks.'[]', $default, ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp".$ks, 'placeholder' => __('product.exc_of_tax')  ]); !!}
                    </div>

                    <div class="col-sm-6">
                      {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                    
                      {!! Form::text('single_dpp_inc_tax'.$ks.'[]', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax".$ks, 'placeholder' => __('product.inc_of_tax') ]); !!}
                    </div>
                  </td>
                  <td>
                    <br/>
                    {!! Form::text('profit_percent'.$ks.'[]', @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$ks   ]); !!}
                  </td>
                  <td>
                      <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                          {!! Form::text('single_dsp'.$ks.'[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$ks   ]); !!}
                      </div>
                      <div class="col-sm-6">
                      {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                      {!! Form::text('single_dsp_inc_tax'.$ks.'[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$ks ]); !!}
                      </div>
                  </td>
                </tr>
              @endif
          
          @endforeach
            
        </table>
     
      @endforeach
  @else
      @if(isset($unit_id)) 
          @php 
            $allList     = [] ; 
            $productUtil = new \App\Utils\ProductUtil() ;
            $sub_units   = $productUtil->getSubUnits(session('business.id'), $unit_id, true);
          @endphp 
          @foreach ($units_main as $keies => $item)
              @if(isset($sub_units[$keies]))
              @php  $allList[$keies] = $item; @endphp 
              @endif
          @endforeach
      @endif 
      @foreach($allList as $k => $i)
          @if($ks == 0)
            @php 
              $ks        = $ks+1; $ind = 0;
              $count++ ; $list_new = [];
            @endphp
            @foreach($allList as $n => $row_line)
                @if($k == $n)
                  @php 
                    $ind = $n;
                    $list_new[$n] = $row_line; 
                  @endphp
                @endif
            @endforeach
            
            {!! Form::select('unit_D[]', $list_new,  $ind  , ['class' => 'form-control input-sm width-10 dpp un_select input_number select2',"data-id"=>$k,"style"=>"width:10%" ,'id'=>'unit_D'    ]); !!}
            <table class="table table-bordered add-product-price-table table-condensed {{$class}}">
              <tr>
                <th>@lang("home.Type Price")</th>
                <th>@lang('product.default_purchase_price')</th>
                <th>@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
                <th>@lang('product.default_selling_price')</th>
              </tr>
              <tr   >
                <td style="vertical-align: bottom">
                  {{__('home.Default Price')}}
                </td>
                <td>
                  <div class="col-sm-6">
                    {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}

                    {!! Form::text('single_dpp'.$ks.'[]', $default, ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp".$ks, 'placeholder' => __('product.exc_of_tax')  ]); !!}
                  </div>

                  <div class="col-sm-6">
                    {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                  
                    {!! Form::text('single_dpp_inc_tax'.$ks.'[]', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax".$ks, 'placeholder' => __('product.inc_of_tax') ]); !!}
                  </div>
                </td>
                <td>
                  <br/>
                  {!! Form::text('profit_percent'.$ks.'[]', @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$ks   ]); !!}
                </td>
                <td>
                    <div class="col-sm-6">
                        {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                        {!! Form::text('single_dsp'.$ks.'[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$ks   ]); !!}
                    </div>
                    <div class="col-sm-6">
                    {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                    {!! Form::text('single_dsp_inc_tax'.$ks.'[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$ks ]); !!}
                    </div>
                </td>
              </tr>
              @foreach($product_price as $i)
                @if($i->product_id == null)
                  
                    <tr @if($i->name  == "ECM After Price" && $i->name == "ECM Before Price") class= "hide " @endif >
                      <td style="vertical-align: bottom">
                        {{$i->name}}
                      </td>
                      <td>
                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}

                          {!! Form::text('single_dpp'.$ks.'[]', $default, ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp".$ks, 'placeholder' => __('product.exc_of_tax')  ]); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                        
                          {!! Form::text('single_dpp_inc_tax'.$ks.'[]', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax".$ks, 'placeholder' => __('product.inc_of_tax') ]); !!}
                        </div>
                      </td>
                      <td>
                        <br/>
                        {!! Form::text('profit_percent'.$ks.'[]', @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent'.$ks   ]); !!}
                      </td>
                      <td>
                          <div class="col-sm-6">
                              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                              {!! Form::text('single_dsp'.$ks.'[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp'.$ks   ]); !!}
                          </div>
                          <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                          {!! Form::text('single_dsp_inc_tax'.$ks.'[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax'.$ks ]); !!}
                          </div>
                      </td>
                    </tr>
                  @endif
              
              @endforeach
                
            </table>
          @endif
    @endforeach
  @endif
</div>

{{-- <div class="btn-more-prices btn-primary" onClick="addMore();" style="width:100px;padding:10px">
    <b class="pls "><i class="fa fas-plus fa-lg"></i> @lang("home.more_price")</b>
    <b class="mis hide"><i class="fa fas-minus fa-lg"></i> @lang("home.less_price")</b>
</div>
<h3>&nbsp;</h3>
<div class="more-price hide">
  
    <div class="box-group">
      {!! Form::select('unit_D[]', $unitsm,null, ['class' => 'form-control input-sm dpp un_select input_number select2',"style"=>"width:" ,  'placeholder' => __('messages.please_select')  ]); !!}
    </div>
   
    <div class="table-responsive">
      <table class="table table-bordered add-product-price-table table-condensed {{$class}}">
          <tr>
            <th>@lang("home.Type Price")</th>
            <th>@lang('product.default_purchase_price')</th>
            <th>@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
            <th>@lang('product.default_selling_price')</th>
          </tr>
          <tr   >
            <td style="vertical-align: bottom">
              {{__('home.Default Price')}}
            </td>
            <td>
              <div class="col-sm-6">
                {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
  
                {!! Form::text('single_dpp2[]', $default, ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp2", 'placeholder' => __('product.exc_of_tax')  ]); !!}
              </div>
  
              <div class="col-sm-6">
                {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
              
                {!! Form::text('single_dpp_inc_tax2[]', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax2", 'placeholder' => __('product.inc_of_tax') ]); !!}
              </div>
            </td>
            <td>
              <br/>
              {!! Form::text('profit_percent2[]', @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent2'   ]); !!}
            </td>
            <td>
                <div class="col-sm-6">
                    {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                    {!! Form::text('single_dsp2[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp2'   ]); !!}
                </div>
                <div class="col-sm-6">
                {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                {!! Form::text('single_dsp_inc_tax2[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax2' ]); !!}
                </div>
            </td>
          </tr>
          
          @foreach($product_price as $i)
              @if($i->product_id == null)
                <tr  @if($i->name == "ECM After Price" && $i->name == "ECM Before Price") class= "hide " @endif >
                  <td style="vertical-align: bottom">
                    {{$i->name}}
                  </td>
                  <td>
                    <div class="col-sm-6">
                      {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}

                      {!! Form::text('single_dpp2[]', $default, ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp2", 'placeholder' => __('product.exc_of_tax')  ]); !!}
                    </div>

                    <div class="col-sm-6">
                      {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                    
                      {!! Form::text('single_dpp_inc_tax2[]', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax2", 'placeholder' => __('product.inc_of_tax') ]); !!}
                    </div>
                  </td>
                  <td>
                    <br/>
                    {!! Form::text('profit_percent2[]', @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent2'   ]); !!}
                  </td>
                  <td>
                      <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                          {!! Form::text('single_dsp2[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp2'   ]); !!}
                      </div>
                      <div class="col-sm-6">
                      {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                      {!! Form::text('single_dsp_inc_tax2[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax2' ]); !!}
                      </div>
                  </td>
                </tr>
              @endif
          @endforeach
            
            
      </table>
    </div>
    
    <div class="box-group">
      {!! Form::select('unit_D[]', $unitsm,null, ['class' => 'form-control input-sm un_select dpp input_number select2',"style"=>"width:" ,  'placeholder' => __('messages.please_select')  ]); !!}
    </div>
 
  <div class="table-responsive">
    <table class="table table-bordered add-product-price-table table-condensed {{$class}}">
        <tr>
          <th>@lang("home.Type Price")</th>
          <th>@lang('product.default_purchase_price')</th>
          <th>@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
          <th>@lang('product.default_selling_price')</th>
        </tr>
        <tr   >
          <td style="vertical-align: bottom">
            {{__('home.Default Price')}}
          </td>
          <td>
            <div class="col-sm-6">
              {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}

              {!! Form::text('single_dpp3[]', $default, ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp3", 'placeholder' => __('product.exc_of_tax')  ]); !!}
            </div>

            <div class="col-sm-6">
              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
            
              {!! Form::text('single_dpp_inc_tax3[]', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax3", 'placeholder' => __('product.inc_of_tax') ]); !!}
            </div>
          </td>
          <td>
            <br/>
            {!! Form::text('profit_percent3[]', @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent3'   ]); !!}
          </td>
          <td>
              <div class="col-sm-6">
                  {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                  {!! Form::text('single_dsp3[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp3'   ]); !!}
              </div>
              <div class="col-sm-6">
              {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
              {!! Form::text('single_dsp_inc_tax3[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax3' ]); !!}
              </div>
          </td>
        </tr>
        @foreach($product_price as $i)
          @if($i->product_id == null)
             <tr @if($i->name == "ECM After Price" && $i->name == "ECM Before Price") class= "hide " @endif  >
              <td style="vertical-align: bottom">
                {{$i->name}}
              </td>
              <td>
                <div class="col-sm-6">
                  {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}

                  {!! Form::text('single_dpp3[]', $default, ['class' => 'form-control input-sm dpp input_number',"id"=>"single_dpp3", 'placeholder' => __('product.exc_of_tax')  ]); !!}
                </div>

                <div class="col-sm-6">
                  {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                
                  {!! Form::text('single_dpp_inc_tax3[]', $default, ['class' => 'form-control input-sm dpp_inc_tax input_number',"id"=>"single_dpp_inc_tax3", 'placeholder' => __('product.inc_of_tax') ]); !!}
                </div>
              </td>
              <td>
                <br/>
                {!! Form::text('profit_percent3[]', @num_format($profit_percent), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent3'   ]); !!}
              </td>
              <td>
                  <div class="col-sm-6">
                      {!! Form::label('single_dpp', trans('product.exc_of_tax') . ': ') !!}
                      {!! Form::text('single_dsp3[]', $default, ['class' => 'form-control input-sm  input_number',  'placeholder' => __('product.exc_of_tax'), 'id' => 'single_dsp3'   ]); !!}
                  </div>
                  <div class="col-sm-6">
                  {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ': ') !!}
                  {!! Form::text('single_dsp_inc_tax3[]', $default, ['class' => 'form-control input-sm  input_number', 'placeholder' => __('product.inc_of_tax'), 'id' => 'single_dsp_inc_tax3' ]); !!}
                  </div>
              </td>
            </tr>
     
            @endif
          @endforeach
          
    </table>
  </div>
</div> --}}
