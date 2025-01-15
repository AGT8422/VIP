@extends("layouts.app")

@section("title",__("business.Edit_patterns"))

@section('content')
   <section class="content  no-print"> 
    {!! Form::open(['url' => action('PatternController@update', [$pattern->id]), 'method' => 'post', 'id' => 'add_patterns', 'files' => true ]) !!}
       @component("components.widget" , ["title"=>__("business.Edit_patterns")])

        {{-- pattern type  --}}
        <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('pattern_type', __('business.type') . ':*') !!}
            {!! Form::select('pattern_type', [ 'sale' => __('business.sale') , 'purchase' => __('business.purchase'), 'cheque' => __('business.cheque')  ] , $pattern->type, ['class' => 'form-control' ,'placeholder' => __('messages.please_select'),   'required']); !!}
            </div>
        </div>
       {{-- pattern name  --}}
       <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('name', __('business.pattern_name') . ':*') !!}
                {!! Form::text('name', $pattern->name, ['class' => 'form-control' ,"placeholder" => __("business.enter pattern name"),   'required']); !!}
            </div>
        </div>
        {{-- pos    --}}
        <div class="col-md-6 hide">
             <div class="form-group">
                 {!! Form::label('pos', __('business.pos') . ':*') !!}
                 {!! Form::text('pos', $pattern->pos, ['class' => 'form-control' ,"placeholder" => __("business.enter pos name"),   'required']); !!}
                </div>
            </div>
        {{-- code    --}}
        <div class="col-md-6">
             <div class="form-group">
                 {!! Form::label('code', __('business.code') . ':*') !!}
                 {!! Form::text('code', $pattern->code, ['class' => 'form-control' ,"placeholder" => __("business.enter pattern code"),   'required']); !!}
                </div>
        </div>
        {{-- invoice scheme  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('invoice_scheme', __('invoice.invoice_scheme') . ':*') !!}
                {!! Form::select('invoice_scheme',$invoice_schemes, $pattern->invoice_scheme, ['class' => 'form-control' ,"placeholder" => __("messages.please_select"),   'required']); !!}
            </div>
        </div>
        {{-- location  --}}
        <div class="col-md-6 hide">
            <div class="form-group">
                {!! Form::label('location_id', __('purchase.business_location') . ':*') !!}
                {!! Form::select('location_id',$business_locations, $pattern->location_id, ['class' => 'form-control' ,"placeholder" => __("messages.please_select"),   'required']); !!}
            </div>
        </div>
       {{-- invoice layout  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('invoice_layout', __('invoice.invoice_layout') . ':*') !!}
                {!! Form::select('invoice_layout',$invoice_layout,  $pattern->invoice_layout, ['class' => 'form-control' ,"placeholder" => __("messages.please_select"),  'required']); !!}
            </div>
        </div>
        {{-- Printer layout  --}}
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('printer_type', __('business.printer_type') . ':*') !!}
                {!! Form::select('printer_type',$printer_layout, $pattern->printer_type, ['class' => 'form-control' ,"placeholder" => __("messages.please_select"),  'required']); !!}
            </div>
        </div>
        @endcomponent
        @component("components.widget" ,[ 'class' => 'box-primary' , 'title' => __('home.Edit_system_account') ] )
            <div class="row">
                <div class="col-sm-12 body_of_patterns"   >
                    @if( $pattern->type == 'sale' )
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('sale', __('home.Sale').':*') !!} 
                                {!! Form::select('sale', $accounts,($data)?$data->sale:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('sale_return', __('home.Sale Return').':*') !!} 
                                {!! Form::select('sale_return', $accounts,($data)?$data->sale_return:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('sale_tax', __('home.Sale Tax').':*') !!} 
                                {!! Form::select('sale_tax', $accounts,($data)?$data->sale_tax:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('sale_discount', __('home.Sale Discount').':*') !!} 
                                {!! Form::select('sale_discount', $accounts,($data)?$data->sale_discount:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                    @endif

                    @if( $pattern->type == 'purchase' )
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('purchase', __('home.Purchase').':*') !!} 
                                {!! Form::select('purchase', $accounts,($data)?$data->purchase:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('purchase_return', __('home.Purchase Return').':*') !!} 
                                {!! Form::select('purchase_return', $accounts,($data)?$data->purchase_return:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('purchase_tax', __('home.Purchase Tax').':*') !!} 
                                {!! Form::select('purchase_tax', $accounts,($data)?$data->purchase_tax:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('purchase_discount', __('home.Purchase Discount').':*') !!} 
                                {!! Form::select('purchase_discount', $accounts,($data)?$data->purchase_discount:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                    @endif

                    @if( $pattern->type == 'cheque' )
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('cheque_debit', __('home.Cheque Debit').':*') !!} 
                                {!! Form::select('cheque_debit', $accounts, ($data)?$data->cheque_debit:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('cheque_collection', __('home.Cheque Collection').':*') !!} 
                                {!! Form::select('cheque_collection', $accounts,($data)?$data->cheque_collection:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                        {{-- <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('journal_expense_tax', __('home.Journal Expense Tax').':*') !!} 
                                {!! Form::select('journal_expense_tax', $accounts,($data)?$data->journal_expense_tax:null, ["required", 'class' => 'form-control select2 font_number','id'=>'journal_expense_tax', 'placeholder' => __('messages.please_select')]); !!}
                            </div> 
                        </div> --}}
                    @endif
                </div>
            </div>
        @endcomponent
        @component("components.widget" )
           <div class="row">
               <div class="col-sm-12  text-right"  >
                   <button type="button" id="submit-pattern" class="btn btn-primary btn-flat">@lang('messages.update')</button>
               </div>
           </div>
       @endcomponent
      
       
</section>
@stop
@section("javascript")
    <script src="{{ asset('js/patterns.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $("#pattern_type").on('change',function(){
        $('.body_of_patterns').html("");
            if($(this).val() == 'purchase'){
                html = '<div class="col-sm-3">';
                html += '<div class="form-group">';
                html += '{!! Form::label('purchase', __("home.Purchase").":*") !!} ';
                html += '{!! Form::select('purchase', $accounts,($data)?$data->purchase:null, ['required', 'class' => 'form-control select2 font_number', 'placeholder' => __("messages.please_select")]); !!}';
                html += '</div>';
                html += '</div>';
                html += '<div class="col-sm-3">';
                html += '<div class="form-group">';
                html += '{!! Form::label('purchase_return', __("home.Purchase Return").":*") !!} ';
                html += '{!! Form::select('purchase_return', $accounts,($data)?$data->purchase_return:null, ['required', 'class' => 'form-control select2 font_number', 'placeholder' => __("messages.please_select")]); !!}';
                html += '</div>';
                html += '</div>';
                html += '<div class="col-sm-3">';
                html += '<div class="form-group">';
                html += '{!! Form::label('purchase_tax', __("home.Purchase Tax").":*") !!} ';
                html += '{!! Form::select('purchase_tax', $accounts,($data)?$data->purchase_tax:null, ['required', 'class' => 'form-control select2 font_number', 'placeholder' => __("messages.please_select")]); !!}';
                html += '</div>';
                html += '</div>';
                html += '<div class="col-sm-3">';
                html += '<div class="form-group">';
                html += '{!! Form::label('purchase_discount', __("home.Purchase Discount").":*") !!} ';
                html += '{!! Form::select('purchase_discount', $accounts,($data)?$data->purchase_discount:null, ['required', 'class' => 'form-control select2 font_number','placeholder' => __("messages.please_select")]); !!}';
                html += '</div>';
                html += '</div>';
                $('.body_of_patterns').html(html);
            }
            if($(this).val() == 'sale'){
                html = '<div class="col-sm-3">';
                html += '<div class="form-group">';
                html += '{!! Form::label('sale', __("home.Sale").":*") !!} ';
                html += '{!! Form::select('sale', $accounts,($data)?$data->sale:null, ['required', 'class' => 'form-control select2 font_number', 'placeholder' => __("messages.please_select")]); !!}';
                html += '</div>';
                html += '</div>';
                html += '<div class="col-sm-3">';
                html += '<div class="form-group">';
                html += '{!! Form::label('sale_return', __("home.Sale Return").":*") !!} ';
                html += '{!! Form::select('sale_return', $accounts,($data)?$data->sale_return:null, ['required', 'class' => 'form-control select2 font_number', 'placeholder' => __("messages.please_select")]); !!}';
                html += '</div>';
                html += '</div>';
                html += '<div class="col-sm-3">';
                html += '<div class="form-group">';
                html += '{!! Form::label('sale_tax', __("home.Sale Tax").":*") !!} ';
                html += '{!! Form::select('sale_tax', $accounts,($data)?$data->sale_tax:null, ['required', 'class' => 'form-control select2 font_number', 'placeholder' => __("messages.please_select")]); !!}';
                html += '</div>';
                html += '</div>';
                html += '<div class="col-sm-3">';
                html += '<div class="form-group">';
                html += '{!! Form::label('sale_discount', __("home.Sale Discount").":*") !!} ';
                html += '{!! Form::select('sale_discount', $accounts,($data)?$data->sale_discount:null, ['required', 'class' => 'form-control select2 font_number','placeholder' => __("messages.please_select")]); !!}';
                html += '</div>';
                html += '</div>';
                $('.body_of_patterns').html(html);
            }
            if($(this).val() == 'cheque'){
                html  = '<div class="col-sm-4">';
                html += '<div class="form-group">';
                html += '{!! Form::label('cheque_debit', __("home.Cheque Debit").":*") !!}';
                html += '{!! Form::select('cheque_debit', $accounts, ($data)?$data->cheque_debit:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __("messages.please_select")]); !!}';
                html += '</div>';
                html += '</div>';
                html += '<div class="col-sm-4">';
                html += '<div class="form-group">';
                html += '{!! Form::label('cheque_collection', __("home.Cheque Collection").":*") !!}';
                html += '{!! Form::select('cheque_collection', $accounts,($data)?$data->cheque_collection:null, ["required", 'class' => 'form-control select2 font_number', 'placeholder' => __("messages.please_select")]); !!}';
                html += '</div>';
                html += '</div>';
                // html += '<div class="col-sm-4">';
                // html += '<div class="form-group">';
                // html += '{!! Form::label('journal_expense_tax', __("home.Journal Expense Tax").":*") !!}';
                // html += '{!! Form::select('journal_expense_tax', $accounts,null, ["required", 'class' => 'form-control select2 font_number','id'=>'journal_expense_tax', 'placeholder' => __("messages.please_select")]); !!}';
                // html += '</div>';
                // html += '</div>';

                $('.body_of_patterns').html(html);
            }
        })
    </script>
@endsection